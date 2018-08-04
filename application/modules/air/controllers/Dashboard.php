<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	public $data = array();
	public $shiftArr = array();
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->shiftArr = array(1,2,3);
		$this->load->model('air_model');
        $this->load->model('cpp_model');
        addPageDetails();
	}

	public function index(){
		$where = array();
		
		$this->data['search'] = false;
		$this->data['meter_id'] = 0;
		$this->data['startDate'] = '2017-08-22';
		$this->data['working_shift'] = 0;
		$this->data['min_val'] = '0';
		$this->data['max_val'] = '0';
		$this->data['dateRange'] = $this->_getDateDetails();
		#print_r($this->data['dateRange']);
		if($this->input->post('working_shift') > 0){
			$this->data['search'] = true;
			$this->data['meter_id'] = trim($this->input->post('meter_id'));
			$this->data['working_shift'] = trim($this->input->post('working_shift'));
			$this->data['dateRange'] = $this->_getDateDetails();
		}
		


        ///////////// Get Data From Datalog CPP ****************Start
        $this->data['last15DataSet_cpp'] = $this->cpp_model->getLast15DataTypeWise();
        $this->data['result_cpp'] = $this->geCalculationResult_cpp();
        
        ///////////// Get Data From Datalog CPP *****************End

		$this->data['last15DataSet'] = $this->air_model->getLast15DataTypeWise();

		

		$this->data['startDateShow'] = $this->startDateTime = isset($this->data['last15DataSet'][0]->end_date_time) ? $this->data['last15DataSet'][0]->end_date_time : '0000-00-00 00:00:00';
		$this->dataTimeInterval = 'PT00H15M00S';;
		$this->data['endDateShow'] = $this->_dateAdd();

		$this->data['typeWise'] = $this->_getTotalTypeWise();


        


		$this->data['notConnetedMeter'] = array(6 => 'Compressor 2', 7 => 'Comp 7 (ZH7000)', 8 => 'Comp 9 (ZH15000+)', 9 => 'Comp 6 (ZR500VSD)', 10 => 'Comp 10 (ZH1600+)', 11 => 'Comp 11 (Cameron Turbine)', 12 => 'WATER PDB 5', 13 => 'COMPRESSOR WATER PDB 1', 14 => 'COMPRESSOR WATER PDB 2', 15 => 'WATER PDB 4');
		$this->data['auxArr'] = array(12,13,14);

		$this->data['compRelKW'] = $this->_getCompRelKW();

        $this->data['compRelKW_total'] = $this->_getCompRelKW_Total();
        //echo "<pre>";
        //var_dump($this->data['compRelKW_total']);

		$this->data['all'] = $this->load->view('dashboard/all',$this->data, true);
		
		
		$where = array();
		$this->data['tags'] = $this->tbl_generic_model->get('master_tag', '*', $where);
		$this->load->view('dashboard/index', $this->data);
	}

    private function _getCompRelKW_Total(){

        $totalVals = 0;
        if(isset($this->data['compRelKW']) && count($this->data['compRelKW'])>0){

            foreach ($this->data['compRelKW'] as $airKey => $airval) {
                $totalVals+= isset($airval[0]) ? $airval[0] : 0;
            }
        }

        return $totalVals;
    }

    public function mappingAIRtoEMS(){
        $returnDataset['deviceIds'] = '48,46,47,44,43,45,51,52,53,56,57,49,50,55';
        $returnDataset['data'] = array(1 => 48, 2 => 47, 3=>45, 4=>44,5=>43,6=>46, 7=>51, 8=>52, 9=>53,10=> 56,11=>0,12=>57,13=>49,14=>50,15 => 55);


        return $returnDataset;
    }

    private function _getCompRelKW_totaltimestamp(){
        
        $mappingData = $this->mappingAIRtoEMS();
        $deviceIds = isset($mappingData['deviceIds']) ? $mappingData['deviceIds'] : '';
        //$startDate = $this->startDateTime;
        $startDate = '';
        $sqlData = $this->air_model->getEMSkwLastDatasets($startDate, $deviceIds);
        $tempSql = array();
        //echo "<pre>";
        //var_dump($sqlData);

        if(count($sqlData) > 0){
            foreach ($sqlData as $key => $value) {
                $tempSql[$value->end_date_time][$value->device_id] = $value->data_KW;//array('device_id'=>$value->device_id,'kw'=>$value->data_KW);
            }
        }

        
        $retData = array();
        if(isset($tempSql) && count($tempSql)>0){
            foreach ($tempSql as $timekey => $timevalue) {
                $kwSum = 0;
                foreach ($timevalue as $devicekey => $deviceKWvalue) {
                    $kwSum+= $deviceKWvalue;
                }
                $retData[$timekey] = $kwSum;
            }
        }
       
        return $retData;
    }

    private function _getCompRelKW_CFMimestamp($meter_id=NULL){

        $mappingData = $this->mappingAIRtoEMS();
        $deviceIds = isset($mappingData['deviceIds']) ? $mappingData['deviceIds'] : '';

        if(isset($meter_id) && $meter_id!=NULL){
            $deviceIds = isset($mappingData['data'][$meter_id]) ? $mappingData['data'][$meter_id] : $deviceIds;
        }
        //$startDate = $this->startDateTime;
        $startDate = '';
        $sqlData = $this->air_model->getEMSkwLastDatasets($startDate, $deviceIds);
        $tempSql = array();
        /*echo "<pre>";
        var_dump($sqlData);*/
        if(count($sqlData) > 0){
            foreach ($sqlData as $key => $value) {
                $tempSql[$value->end_date_time] = $value->data_KW;//array('device_id'=>$value->device_id,'kw'=>$value->data_KW);
            }
        }

        return $tempSql;

    }

    public function getMeterChart_forKWCFM(){

        $meter_id = $this->uri->segment(4);

        // Get KW value according 15 mint timestamp interval
        $dataFinalArray_KW = $this->_getCompRelKW_CFMimestamp($meter_id);       

        // Get CFM value according 15 mint timestamp interval
        $selected = 'AM.name,AMD.end_date_time';
        $selected .= ',AMD.flow';

        $dataFinalArray_CFM = $this->air_model->getAirMeterData($meter_id, $selected);

        // Generate The KW/CFM Value
        $meterName = "NA";
        $dataFinalArray = array();
        if(isset($dataFinalArray_CFM) && count($dataFinalArray_CFM)>0){
            foreach ($dataFinalArray_CFM as $inkey => $invalue) {
                $meterName = isset($invalue->name) ? $invalue->name : 'NA';

                if(isset($dataFinalArray_KW[$invalue->end_date_time]) && isset($invalue->end_date_time))
                $dataFinalArray[$invalue->end_date_time] = (isset($invalue->flow) && $invalue->flow>0) ? ($dataFinalArray_KW[$invalue->end_date_time] / $invalue->flow) : 0;

            }
        }
        /*echo "<pre>";
        var_dump($meter_id);
        var_dump($dataFinalArray);
        die();*/

        $dataFinalArray = array_reverse($dataFinalArray);
        

        $dataSet = '';
        $consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = $meterName;
        $this->data['meterNameColumn'] = 'KW/CFM';
        if(count($dataFinalArray) > 0){
            //$this->data['meterName'] = $data[0]->name;
            foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
                /*
                $dataSet .= '{
                        "label": "'.$dateTimeKey.'",
                        "value": "'.round($dateTimeValue,3).'"
                    },';
                    */

                $dataSet .= '{
                        "date": "'.$dateTimeKey.'",
                        "paramVal": "'.round($dateTimeValue,3).'"
                    },';
                    $maxMin[] = $dateTimeValue;
                
            }
        }
        //var_dump($dataSet);
        //die();
        /////////////////////////////////////////////////
        $this->data['consisIndex'] = 'no';
        $dataSetConsis = '';
        $maxMinConsis = array();
        $this->data['max_val_con'] = 0;
        $this->data['min_val_con'] = 0;
        $this->data['graph_logic'] = '1';
        /////////////////////////////////////////////////
        $this->data['max_val'] = (intval(max($maxMin))<=0) ? intval(max($maxMin)) : (intval(max($maxMin)) + 1);
        $this->data['min_val'] = (intval(min($maxMin))<=0) ? intval(min($maxMin)) : (intval(min($maxMin)) - ((min($maxMin) == 0)?0:1));
        //echo intval(min($maxMin));    
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSetConsis'] = $dataSetConsis;
        
        //echo max($maxMin);
        //echo min($maxMin);
        $this->data['chartData'] = $dataSet;
        $this->load->view('am_chart/line',$this->data);

        //$this->load->view('dashboard/chart',$this->data);

    }


    public function getMeterChartKWTotal(){

        $dataFinalArray = $this->_getCompRelKW_totaltimestamp();

        

        $dataFinalArray = array_reverse($dataFinalArray);
        

        $dataSet = '';
        $consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = 'Total of Generation of KW';
        $this->data['meterNameColumn'] = 'KW';
        if(count($dataFinalArray) > 0){
            //$this->data['meterName'] = $data[0]->name;
            foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
                /*
                $dataSet .= '{
                        "label": "'.$dateTimeKey.'",
                        "value": "'.round($dateTimeValue,3).'"
                    },';
                */
                $dataSet .= '{
                        "date": "'.$dateTimeKey.'",
                        "paramVal": "'.round($dateTimeValue,3).'"
                    },';
                    $maxMin[] = $dateTimeValue;
                
            }
        }
        //var_dump($dataSet);
        //die();
        /////////////////////////////////////////////////
        $this->data['consisIndex'] = 'no';
        $dataSetConsis = '';
        $maxMinConsis = array();
        $this->data['max_val_con'] = 0;
        $this->data['min_val_con'] = 0;
        $this->data['graph_logic'] = '1';
        /////////////////////////////////////////////////
        $this->data['max_val'] = intval(max($maxMin)) + 1;
        $this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
        //echo intval(min($maxMin));    
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSetConsis'] = $dataSetConsis;
        
        $this->data['chartData'] = $dataSet;
        $this->load->view('am_chart/line',$this->data);

        //$this->load->view('dashboard/chart',$this->data);

    }



    private function geCalculationResult_cpp(){
        $retData = array();
        if(count($this->data['last15DataSet_cpp'])){
            $data = array();
            //echo "<pre>";
            //var_dump($this->data['last15DataSet_cpp']);
            $startDateTime = "";
            foreach ($this->data['last15DataSet_cpp'] as $key => $value) {
                
                //var_dump($value);
                $retData['meter_data'][$value->type][$value->meter_id]['name'] = $value->name;

                $retData['meter_data'][$value->type][$value->meter_id]['pressure'] = $value->pressure; 
                $retData['meter_data'][$value->type][$value->meter_id]['temp'] = $value->temp; 
                $retData['meter_data'][$value->type][$value->meter_id]['flow'] = $value->flow * 10; 
               
               $startDateTime =  $value->end_date_time; 
                
                
            }


            //var_dump($startDateTime);

            $date = new DateTime($startDateTime);
            $date->add(new DateInterval('PT00H15M00S'));
            $endDateTime = $date->format('Y-m-d H:i:s');
            
            
            //var_dump($retData);
            $retData['timestamp']['start_time'] = $startDateTime;
            $retData['timestamp']['end_time'] = $endDateTime;

        }
        return $retData;
    }

	private function _getCompRelKW(){
		//$deviceIds = '48,46,47,44,43,45,51,52,53,56,57,49,50,55';
		//$data = array(1 => 48, 2 => 47, 3=>46, 4=>44,5=>43,6=>45, 7=>51, 8=>52, 9=>53,10=> 56,11=>0,12=>57,13=>49,14=>50,15 => 55);
		
        $mappingData = $this->mappingAIRtoEMS();
        $deviceIds = isset($mappingData['deviceIds']) ? $mappingData['deviceIds'] : '';
        $data = isset($mappingData['data']) ? $mappingData['data'] : '';

        $startDate = $this->startDateTime;
		$sqlData = $this->air_model->getEMSkw($startDate, $deviceIds);
		$tempSql = array();
		if(count($sqlData) > 0){
			foreach ($sqlData as $key => $value) {
				$tempSql[$value->device_id] = $value->data_KW;
			}
		}

		$retData = array();
		foreach ($data as $key => $value) {
			if(isset($tempSql[$value])){
				$retData[$key] = array(round($tempSql[$value],2),$value);
			}else{
				$retData[$key] = array('DNA',$value);
			}
		}

		return $retData;
	}


	private function _getTotalTypeWise(){
		$retData = array();
		if(count($this->data['last15DataSet'])){
			$data = array();
			//echo "<pre>";
			//var_dump($this->data['last15DataSet']);
			//var_dump($this->data['last15DataSet']);


			foreach ($this->data['last15DataSet'] as $key => $value) {
				//var_dump($value);

				$data[$value->type]['P_pressure'][] = $value->P_pressure;
				$data[$value->type]['P_pressure_prod'][] = $value->P_pressure * $value->flow;

				$data[$value->type]['flow'][] = $value->flow;
				$data[$value->type]['T_temp'][] = $value->T_temp;
				$data[$value->type]['T_temp_prod'][] = $value->T_temp * $value->flow;

				$data[$value->type]['TTL_flow'][] = $value->TTL_flow;
				$data[$value->type]['steam_enthalpy'][] = $value->steam_enthalpy;
				$data[$value->type]['steam_enthalpy_prod'][] = $value->flow * $value->steam_enthalpy;

				$data[$value->type]['steam_heat_content'][] = $value->steam_heat_content;
				$retData[$value->type]['meter'][$value->meter_id]['name'] = $value->name;
				$retData[$value->type]['meter'][$value->meter_id]['capacity'] = $value->capacity;
				$retData[$value->type]['meter'][$value->meter_id]['P_pressure'] = $value->P_pressure;
				$retData[$value->type]['meter'][$value->meter_id]['flow'] = $value->flow;
				$retData[$value->type]['meter'][$value->meter_id]['T_temp'] = $value->T_temp;
				$retData[$value->type]['meter'][$value->meter_id]['TTL_flow'] = $value->TTL_flow;
				$retData[$value->type]['meter'][$value->meter_id]['steam_enthalpy'] = $value->steam_enthalpy;
				$retData[$value->type]['meter'][$value->meter_id]['steam_heat_content'] = $value->steam_heat_content;
				$retData[$value->type]['meter'][$value->meter_id]['benchmark_delta_pressure'] = $value->benchmark_delta_pressure;
				$retData[$value->type]['meter'][$value->meter_id]['benchmark_delta_temp'] = $value->benchmark_delta_temp;
				$retData[$value->type]['meter'][$value->meter_id]['alert_counter_pressure'] = $value->alert_counter_pressure;
				$retData[$value->type]['meter'][$value->meter_id]['alert_counter_temp'] = $value->alert_counter_temp;
				$retData[$value->type]['meter'][$value->meter_id]['alert_mail_status'] = $value->alert_mail_status;
			}

            // For CPP
            /*echo '<pre>';
            var_dump($this->data['result_cpp']);*/

            if(isset($this->data['result_cpp']['meter_data']) && count($this->data['result_cpp']['meter_data'])>0){
                
                foreach($this->data['result_cpp']['meter_data'] as $cppkk=>$cppvv){
                    if($cppkk=='gen'){
                        foreach($cppvv as $cppkk1=>$cppvv1){
                            if($cppkk1==1){
                                $data[$cppkk]['P_pressure'][] = $cppvv1['pressure'];
                                $data[$cppkk]['P_pressure_prod'][] = $cppvv1['pressure'] * $cppvv1['flow'];

                                $data[$cppkk]['flow'][] = $cppvv1['flow'];
                            }                            

                        }
                    }
                    
                }
            }
			//echo "<pre>";
			//print_r($retData);
			if(count($data) > 0){
				foreach ($data as $key => $value) {

					$retData[$key]['total']['flow'] = array_sum($value['flow']);

					//$retData[$key]['total']['P_pressure'] = array_sum($value['P_pressure'])/count($value['P_pressure']);
					if(isset($retData[$key]['total']['flow']) && $retData[$key]['total']['flow']!=0){
						$retData[$key]['total']['P_pressure'] = array_sum($value['P_pressure_prod'])/$retData[$key]['total']['flow'];
					}else{
						$retData[$key]['total']['P_pressure'] = 0;
					}
					

					//$retData[$key]['total']['flow'] = array_sum($value['flow'])/count($value['flow']);

					//$retData[$key]['total']['T_temp'] = array_sum($value['T_temp'])/count($value['T_temp']);
					if(isset($retData[$key]['total']['flow']) && $retData[$key]['total']['flow']!=0){
						$retData[$key]['total']['T_temp'] = array_sum($value['T_temp_prod'])/$retData[$key]['total']['flow'];
					}else{
						$retData[$key]['total']['T_temp'] = 0;
					}
					

					$retData[$key]['total']['TTL_flow'] = array_sum($value['TTL_flow']);

					if($retData[$key]['total']['flow'] > 0){
						$retData[$key]['total']['steam_enthalpy'] = array_sum($value['steam_enthalpy_prod'])/$retData[$key]['total']['flow'];
					}else{
						$retData[$key]['total']['steam_enthalpy'] = 0;
					}
					$retData[$key]['total']['steam_heat_content'] = array_sum($value['steam_heat_content']);
				}
				//print_r($retData);
			}
		}
		return $retData;
	}

	private function _getDateDetails(){
		
		$meter_id = $this->data['meter_id'] ;
		$data = $this->air_model->getDateDetails($meter_id);
		$retData['max_date'] = '';
		$retData['min_date'] = '';
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$retData['max_date'] = $value->max_date;
				$retData['min_date'] = $value->min_date;
			}
		}
		return $retData;
	}

    private function _dateAdd(){
        $date = new DateTime($this->startDateTime);
        $date->add(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

    public function getNewDataSet(){
    	$this->data['last15DataSet'] = $this->air_model->getLast15DataTypeWise();
		$this->data['typeWise'] = $this->_getTotalTypeWise();

        $this->data['auxArr'] = array(12,13,14);

        $this->data['compRelKW'] = $this->_getCompRelKW();
        
        $this->data['notConnetedMeter'] = array(6 => 'Comp 1 (ZH15000)', 7 => 'Comp 7 (ZH7000)', 8 => 'Comp 9 (ZH15000+)', 9 => 'Comp 6 (ZR500VSD)', 10 => 'Comp 10 (ZH1600+)', 11 => 'Comp 11 (Cameron Turbine)', 12 => 'WATER PDB 5', 13 => 'COMPRESSOR WATER PDB 1', 14 => 'COMPRESSOR WATER PDB 2', 15 => 'WATER PDB 4');
        
        ///////////// Get Data From Datalog CPP ****************Start
        $this->data['last15DataSet_cpp'] = $this->cpp_model->getLast15DataTypeWise();
        $this->data['result_cpp'] = $this->geCalculationResult_cpp();
        ///////////// Get Data From Datalog CPP *****************End

		$retData['all'] = $this->load->view('dashboard/all',$this->data, true);
		$retData['status'] = 'success'; 
		echo json_encode($retData);
    }

    public function getMeterChartTotalGNvsDIST(){
        
        $type = $this->uri->segment(4);
        //$meterType = $this->uri->segment(5);

        $selected = 'A.name,AD.end_date_time';
        $aa = 'T_temp';
        $column = 'Temperature';
        if($type == 'temp'){
            $selected .= ',AD.T_temp';
        }else if($type == 'pres'){
            $selected .= ',AD.P_pressure';
            $aa = 'P_pressure';
            $column = 'Pressure';
        }else if($type == 'flow'){
            $selected .= ',AD.flow';
            $aa = 'flow';
            $column = 'Flow';
        }else{
            $selected .= ',AD.steam_enthalpy';
            $aa = 'steam_enthalpy';
            $column = 'Enthalpy';
        }

        $meterdata = $this->air_model->getMeter();
        //echo "<pre>";
        

        $data = array();
        if(isset($meterdata) && count($meterdata)>0){
            foreach ($meterdata as $mkey => $mvalue) {
                if(isset($mvalue->type) && $mvalue->type=='main') continue;

                $data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterData($mvalue->meter_id);

            }
        }
        
        // Iterate Array according to End Date Time
        $dataArray = array();
        if(isset($data) && count($data)>0){
            foreach ($data as $dkeyType => $dvalue) {
                $dataArray[$dkeyType] = $this->_setData($dvalue); 
            }
        }           
        
        // Calculation Of Value
        $dataFinalArray = array();
        if(isset($dataArray) && count($dataArray)>0){
            foreach ($dataArray as $mTypekey => $mTypevalue) {
                $dataFinalArray[$mTypekey] = $this->_calculationTimestamp($mTypevalue); 
            }
        }       

        // Re-Structure The Array
        $newDataArray = array();
        if(isset($dataFinalArray) && count($dataFinalArray)>0){
            foreach ($dataFinalArray as $typeKey => $typeValue) {
                foreach ($typeValue as $dateTimekeys => $dateTimeValues) {
                    $newDataArray[$dateTimekeys][$typeKey] = $dateTimeValues[$aa];
                }
            }
        }

        // Calculation For Generation Vs Distribution
        $resultArray = array();
        if(isset($newDataArray) && count($newDataArray)>0){
            foreach ($newDataArray as $dateTimeskey => $dateTimesvalue) {
                if(isset($dateTimesvalue['gen']) && isset($dateTimesvalue['dist'])){
                    $resultArray[$dateTimeskey] = ($dateTimesvalue['gen'] - $dateTimesvalue['dist']);
                }else{
                    if(isset($dateTimesvalue['gen'])){
                        $resultArray[$dateTimeskey] = $dateTimesvalue['gen'];
                    }elseif(isset($dateTimesvalue['dist'])){
                        $resultArray[$dateTimeskey] = $dateTimesvalue['gen'];
                    }else{
                        $resultArray[$dateTimeskey] = '0';
                    }
                }
            }
        }
        
            

        $resultArray = array_reverse($resultArray);
        //var_dump($resultArray);
        //die();
        $dataSet = '';
        $consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = 'Generation Vs Distribution';
        $this->data['meterNameColumn'] = $column;
        if(count($resultArray) > 0){
            //$this->data['meterName'] = $data[0]->name;
            foreach ($resultArray as $dateTimeKey => $dateTimeValue) {
                
                $dataSet .= '{
                        "label": "'.$dateTimeKey.'",
                        "value": "'.round($dateTimeValue,3).'"
                    },';
                    $maxMin[] = $dateTimeValue[$aa];
                
            }
        }
        //var_dump($dataSet);
        //die();
        /////////////////////////////////////////////////
        $this->data['consisIndex'] = 'no';
        $dataSetConsis = '';
        $maxMinConsis = array();
        $this->data['max_val_con'] = 0;
        $this->data['min_val_con'] = 0;
        $this->data['graph_logic'] = '1';
        /////////////////////////////////////////////////
        $this->data['max_val'] = intval(max($maxMin)) + 1;
        $this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
        //echo intval(min($maxMin));    
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSetConsis'] = $dataSetConsis;
        
        $this->load->view('dashboard/chart',$this->data);
    }

    public function getMeterChartTotalGNvsDISTforDoubleGraph(){
    	
    	$type = $this->uri->segment(4);
    	//$meterType = $this->uri->segment(5);

    	$selected = 'A.name,AD.end_date_time';
        $selected2 = 'A.name,AD.end_date_time';

    	$aa = 'P_pressure';
        $aa2 = 'flow';

  		$column = 'Pressure Vs Flow';
        $seriesname = 'Pressure(Bar)';
        $seriesname2 = 'Flow(CFM)';

    	if($type == 'pres'){
    		$aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
    	}else if($type == 'flow'){
    		$selected .= ',AD.flow';
            $selected2 .= ',AD.P_pressure';
    		$aa = 'flow';
            $aa2 = 'P_pressure';
    		$column = 'Pressure Vs Flow';
            $seriesname = 'Flow(CFM)';
            $seriesname2 = 'Pressure(Bar)';
    	}else{
    		$aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
    	}

    	$meterdata = $this->air_model->getMeter();
    	//echo "<pre>";
    	

    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			if(isset($mvalue->type) && $mvalue->type=='main') continue;

    			$data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterData($mvalue->meter_id);

    		}
    	}
    	
    	// Iterate Array according to End Date Time
    	$dataArray = array();
    	if(isset($data) && count($data)>0){
    		foreach ($data as $dkeyType => $dvalue) {
    			$dataArray[$dkeyType] = $this->_setData($dvalue); 
    		}
    	}    	 	
    	
    	// Calculation Of Value
    	$dataFinalArray = array();
    	if(isset($dataArray) && count($dataArray)>0){
    		foreach ($dataArray as $mTypekey => $mTypevalue) {
    			$dataFinalArray[$mTypekey] = $this->_calculationTimestamp($mTypevalue); 
    		}
    	}    	

    	// Re-Structure The Array
    	$newDataArray = array();
        $newDataArray2 = array();
    	if(isset($dataFinalArray) && count($dataFinalArray)>0){
    		foreach ($dataFinalArray as $typeKey => $typeValue) {
    			foreach ($typeValue as $dateTimekeys => $dateTimeValues) {
    				$newDataArray[$dateTimekeys][$typeKey] = $dateTimeValues[$aa];
                    $newDataArray2[$dateTimekeys][$typeKey] = $dateTimeValues[$aa2];
    			}
    		}
    	}
        
    	// Calculation For Generation Vs Distribution for first set
    	$resultArray = array();
    	if(isset($newDataArray) && count($newDataArray)>0){
    		foreach ($newDataArray as $dateTimeskey => $dateTimesvalue) {
    			if(isset($dateTimesvalue['gen']) && isset($dateTimesvalue['dist'])){
    				$resultArray[$dateTimeskey] = ($dateTimesvalue['gen'] - $dateTimesvalue['dist']);
    			}else{
    				if(isset($dateTimesvalue['gen'])){
    					$resultArray[$dateTimeskey] = $dateTimesvalue['gen'];
    				}elseif(isset($dateTimesvalue['dist'])){
    					$resultArray[$dateTimeskey] = $dateTimesvalue['dist'];
    				}else{
    					$resultArray[$dateTimeskey] = '0';
    				}
    			}
    		}
    	}
        // Calculation For Generation Vs Distribution for second set
        $resultArray2 = array();
        if(isset($newDataArray2) && count($newDataArray2)>0){
            foreach ($newDataArray2 as $dateTimeskey2 => $dateTimesvalue2) {
                if(isset($dateTimesvalue2['gen']) && isset($dateTimesvalue2['dist'])){
                    $resultArray2[$dateTimeskey2] = ($dateTimesvalue2['gen'] - $dateTimesvalue2['dist']);
                }else{
                    if(isset($dateTimesvalue2['gen'])){
                        $resultArray2[$dateTimeskey2] = $dateTimesvalue2['gen'];
                    }elseif(isset($dateTimesvalue2['dist'])){
                        $resultArray2[$dateTimeskey2] = $dateTimesvalue2['dist'];
                    }else{
                        $resultArray2[$dateTimeskey2] = '0';
                    }
                }
            }
        }
    	
    	   	

    	$resultArray = array_reverse($resultArray);
        $resultArray2 = array_reverse($resultArray2);
    	
        /*echo "<pre>";
        var_dump($resultArray2);
        die();*/

        $this->data['meterName'] = 'Generation Vs Distribution';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;

    	$this->data['dayCount'] = 1;        

        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';
        if(count($resultArray) > 0){

            foreach ($resultArray as $key => $value) {                
                if(isset($key) && $key!=''){
                    /*$dataSetDate .= '{
                        "label": "'.$key.'"
                    },';

                    $dataSet .= '{
                        "value": "'.number_format((float)round($value,3), 2, '.', '').'"
                    },';

                    if(isset($resultArray2[$key])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($resultArray2[$key],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }*/

                    if($type=="pres"){
                        $p = isset($value) ? number_format((float)round($value,3), 2, '.', '') : '0';
                        $f = isset($resultArray2[$key]) ? number_format((float)round($resultArray2[$key],3), 2, '.', '') : '0';
                    }else{
                        $p = isset($resultArray2[$key]) ? number_format((float)round($resultArray2[$key],3), 2, '.', '') : '0';
                        $f = isset($value) ? number_format((float)round($value,3), 2, '.', '') : '0';
                    }
                    
                    $dataSet .= '{
                        "date": "'.$key.'",
                        "pressure": "'.$p.'",
                        "flow": "'.$f.'"                    
                    },';

                }            
                
            }
        }
        

        $this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;

        $this->data['chartData'] = $dataSet;
        $this->load->view('dashboard/chart_loading_am_line',$this->data);

        //$this->load->view('dashboard/chart_loading',$this->data);
    }

    

    public function getMeterChartTotal(){
    	
    	$type = $this->uri->segment(4);
    	$meterType = $this->uri->segment(5);
    	$selected = 'A.name,AD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	if($type == 'temp'){
    		$selected .= ',AD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',AD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',AD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',AD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	// Get The Meter Details by Type(Generation or distribution)
    	$meterdata = $this->air_model->getMeterTypeWise($meterType);
    	
    	// Fetch the data meter wise
    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			
    			$data[$mvalue->meter_id] = $this->air_model->getAirMeterData($mvalue->meter_id);

    		}
    	}

    	// Iterate Array according to End Date Time
    	$dataArray = $this->_setData($data);    	
    	
    	// Calculation Of Value
    	$dataFinalArray = $this->_calculationTimestamp($dataArray);    	

    	$dataFinalArray = array_reverse($dataFinalArray);

    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Distribution';
    	$this->data['meterNameColumn'] = $column;
    	if(count($dataFinalArray) > 0){
    		//$this->data['meterName'] = $data[0]->name;
    		foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
    			/*
		        $dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue[$aa],3).'"
			        },';
                */
                $dataSet .= '{
                        "date": "'.$dateTimeKey.'",
                        "paramVal": "'.round($dateTimeValue[$aa],3).'"
                    },';
			        $maxMin[] = $dateTimeValue[$aa];
		        
    		}
    	}
    	//var_dump($dataSet);
    	//die();
    	/////////////////////////////////////////////////
    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	/////////////////////////////////////////////////
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	//echo intval(min($maxMin));	
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
        $this->data['chartData'] = $dataSet;
        $this->load->view('am_chart/line',$this->data);
    	//$this->load->view('dashboard/chart',$this->data);
    }

    public function getMeterChartTotalforDoubleGraph(){
        
        $type = $this->uri->segment(4);
        $meterType = $this->uri->segment(5);
        $selected = 'A.name,AD.end_date_time';
        $selected2 = 'A.name,AD.end_date_time';
        $aa = 'P_pressure';
        $aa2 = 'flow';
        $column = 'Pressure Vs Flow';
        $seriesname = 'Pressure(Bar)';
        $seriesname2 = 'Flow(CFM)';

        if($type == 'pres'){
            $selected .= ',AD.P_pressure';
            $selected2 .= ',AD.flow';
            $aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }else if($type == 'flow'){
            $selected .= ',AD.flow';
            $selected2 .= ',AD.P_pressure';
            $aa = 'flow';
            $aa2 = 'P_pressure';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Flow(CFM)';
            $seriesname2 = 'Pressure(Bar)';
        }else{
            $selected .= ',AD.P_pressure';
            $selected2 .= ',AD.flow';
            $aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }

        // Get The Meter Details by Type(Generation or distribution)
        $meterdata = $this->air_model->getMeterTypeWise($meterType);
        
        // Fetch the data meter wise
        $data = array();
        if(isset($meterdata) && count($meterdata)>0){
            foreach ($meterdata as $mkey => $mvalue) {
                
                $data[$mvalue->meter_id] = $this->air_model->getAirMeterData($mvalue->meter_id);

            }
        }

        // Iterate Array according to End Date Time
        $dataArray = $this->_setData($data);        
        
        // Calculation Of Value
        $dataFinalArray = $this->_calculationTimestamp($dataArray);     

        $dataFinalArray = array_reverse($dataFinalArray);

        $this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Distribution';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;

        $this->data['dayCount'] = 1;        

        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';

        if(count($dataFinalArray) > 0){
            //$this->data['meterName'] = $data[0]->name;
            foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
                
                if(isset($dateTimeKey) && $dateTimeKey!=''){
                    /*$dataSetDate .= '{
                        "label": "'.$dateTimeKey.'"
                    },';

                    if(isset($dateTimeValue[$aa])){
                        $dataSet .= '{
                            "value": "'.number_format((float)round($dateTimeValue[$aa],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet .= '{
                            "value": "0"
                        },';
                    }

                    if(isset($dateTimeValue[$aa2])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($dateTimeValue[$aa2],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }*/
                    if($type == 'pres'){
                        $p = isset($dateTimeValue[$aa]) ? number_format((float)round($dateTimeValue[$aa],3), 2, '.', '') : '0';
                        $f = isset($dateTimeValue[$aa2]) ? number_format((float)round($dateTimeValue[$aa2],3), 2, '.', '') : '0';
                    }else{
                        $f = isset($dateTimeValue[$aa]) ? number_format((float)round($dateTimeValue[$aa],3), 2, '.', '') : '0';
                        $p = isset($dateTimeValue[$aa2]) ? number_format((float)round($dateTimeValue[$aa2],3), 2, '.', '') : '0';
                    }
                    
                    $dataSet .= '{
                        "date": "'.$dateTimeKey.'",
                        "pressure": "'.$p.'",
                        "flow": "'.$f.'"                    
                    },';

                }
                
            }
        }
        /////////////////////////////////////////////////
        $this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;
        /*echo "<pre>";
        var_dump($this->data);
        die();*/
        
        $this->data['chartData'] = $dataSet;
        $this->load->view('dashboard/chart_loading_am_line',$this->data);
        

        //$this->load->view('dashboard/chart_loading',$this->data);
    }

    private function _setData($dataSet){

    	$dataArray = array();
    	if(isset($dataSet) && count($dataSet)>0){
    		foreach ($dataSet as $meterId => $meter_value) {
    			foreach ($meter_value as $akey => $aValue) {
    				$dataArray[$aValue->end_date_time][$meterId]['TTL_flow'] = $aValue->TTL_flow;
    				$dataArray[$aValue->end_date_time][$meterId]['flow'] = $aValue->flow;
    				$dataArray[$aValue->end_date_time][$meterId]['steam_heat_content'] = $aValue->steam_heat_content;
    				$dataArray[$aValue->end_date_time][$meterId]['P_pressure'] = $aValue->P_pressure;
    				$dataArray[$aValue->end_date_time][$meterId]['T_temp'] = $aValue->T_temp;
    				$dataArray[$aValue->end_date_time][$meterId]['steam_enthalpy'] = $aValue->steam_enthalpy;
    			}
    			
    		}
    	}

    	return $dataArray;
    }

    private function _calculationTimestamp($dataSet){

    	$finalArray = array();
    	if(isset($dataSet) && count($dataSet)>0){
    		foreach ($dataSet as $dateKey => $dateValue) {
    			
    			foreach ($dateValue as $meterkey => $metersvalue) {
    				$finalArray[$dateKey]['TTL_flow'][] = $metersvalue['TTL_flow'];
    				$finalArray[$dateKey]['flow'][] = $metersvalue['flow'];
    				$finalArray[$dateKey]['steam_heat_content'][] = $metersvalue['steam_heat_content'];

    				$finalArray[$dateKey]['P_pressure'][] = $metersvalue['P_pressure'];
    				$finalArray[$dateKey]['P_pressure_prod'][] = $metersvalue['P_pressure'] * $metersvalue['flow'];

    				$finalArray[$dateKey]['T_temp'][] = $metersvalue['T_temp'];
    				$finalArray[$dateKey]['T_temp_prod'][] = $metersvalue['T_temp'] * $metersvalue['flow'];

    				$finalArray[$dateKey]['steam_enthalpy'][] = $metersvalue['steam_enthalpy'];
    				$finalArray[$dateKey]['steam_enthalpy_prod'][] = $metersvalue['steam_enthalpy'] * $metersvalue['flow'];

    			}

    			//$finalArray[$dateKey] = array_sum($dateValue);
    		}
    	} 

    	$dataFinalArray = array();
    	if(isset($finalArray) && count($finalArray)>0){
    		foreach ($finalArray as $dateMkey => $dateMvalue) {
    			$dataFinalArray[$dateMkey]['TTL_flow'] = array_sum($dateMvalue['TTL_flow']);
    			$dataFinalArray[$dateMkey]['flow'] = array_sum($dateMvalue['flow']);
    			$dataFinalArray[$dateMkey]['steam_heat_content'] = array_sum($dateMvalue['steam_heat_content']);

    			if($dataFinalArray[$dateMkey]['flow']>0){
    				$dataFinalArray[$dateMkey]['P_pressure'] = array_sum($dateMvalue['P_pressure_prod']) / $dataFinalArray[$dateMkey]['flow'];
    			}else{
    				$dataFinalArray[$dateMkey]['P_pressure'] = 0;
    			}

    			if($dataFinalArray[$dateMkey]['flow']>0){
    				$dataFinalArray[$dateMkey]['T_temp'] = array_sum($dateMvalue['T_temp_prod']) / $dataFinalArray[$dateMkey]['flow'];
    			}else{
    				$dataFinalArray[$dateMkey]['T_temp'] = 0;
    			}

    			if($dataFinalArray[$dateMkey]['flow']>0){
    				$dataFinalArray[$dateMkey]['steam_enthalpy'] = array_sum($dateMvalue['steam_enthalpy_prod']) / $dataFinalArray[$dateMkey]['flow'];
    			}else{
    				$dataFinalArray[$dateMkey]['steam_enthalpy'] = 0;
    			}
    		}
    	}


    	return $dataFinalArray;
    }

    public function getMeterChart(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$selected = 'AM.name,AMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	if($type == 'temp'){
    		$selected .= ',AMD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',AMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',AMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else if($type == 'capacity'){
    		$selected .= ',AMD.flow';
    		$aa = 'flow';
    		$column = 'CFM CU';
    	}else{
    		$selected .= ',AMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$data = $this->air_model->getAirMeterData($meter_id, $selected);
    	$MeterCapacity = $this->air_model->getMeterCapacity($meter_id);
    	
    	
    	$capacity = isset($MeterCapacity[0]->capacity) ? $MeterCapacity[0]->capacity : '0';
    	
    	$data = array_reverse($data);
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $column;
    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		        if($curVal != ''){
		        	/*$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';
                    */
                    $dataSet .= '{
                        "date": "'.$value->end_date_time.'",
                        "paramVal": "'.round($deltaGenVal - $value->{$aa},3).'"
                    },';

			        $maxMin[] = $deltaGenVal - $value->{$aa};
		        	$consistencyArr[$value->end_date_time] = $deltaGenVal - $value->{$aa};
		        }else{

		        	$valuess = $value->{$aa};

		        	if($type == 'capacity'){
		        		$valuess = '0';
		        		if($capacity>0){
		        			$valuess = ($value->{$aa} / $capacity) * 100;
		        			$valuess = round($valuess,2);
		        		}
		        		
		        	}
		        	/*$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($valuess,3).'"
			        },';
                    */
                    $dataSet .= '{
                        "date": "'.$value->end_date_time.'",
                        "paramVal": "'.round($valuess,3).'"
                    },';

			        $maxMin[] = $valuess;
		        }
		        
    		}
    	}
    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	if(count($consistencyArr) > 0){
    		$this->data['consisIndex'] = 'yes';
    		$consistencyJson = json_encode($consistencyArr);
    		
    		$cinsistencyResult = $this->consistencyIndex($staticDeltaVal, $curVal, $consistencyJson);
    		if(count($cinsistencyResult['consistencyArray']) > 0){
    			foreach ($cinsistencyResult['consistencyArray'] as $key1 => $value1) {
    				$dataSetConsis .= '{
			            "label": "'.$key1.'",
			            "value": "'.$value1.'"
			        },';
    			}
    			$maxMinConsis[] = $value1;
    		}
    		$this->data['graph_logic'] = $cinsistencyResult['flag'];
    		$this->data['max_val_con'] = intval(max($maxMinConsis)) + 1;
    		$this->data['min_val_con'] = intval(min($maxMinConsis)) - ((min($maxMinConsis) == 0)?0:1);
    	}
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	//echo intval(min($maxMin));	
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
        $this->data['chartData'] = $dataSet;
        $this->load->view('am_chart/line',$this->data);
        

    	//$this->load->view('dashboard/chart',$this->data);
    }

    public function getMeterChartFordoubleGraph(){
        $meter_id = $this->uri->segment(4);
        $type = $this->uri->segment(5);
        //$curVal = $this->uri->segment(6);
        //$deltaGenVal = $this->uri->segment(7);
        //$staticDeltaVal = $this->uri->segment(8);
        $selected = 'AM.name,AMD.end_date_time';
        $selected2 = 'AM.name,AMD.end_date_time';
        $aa = 'P_pressure';
        $aa2 = 'flow';
        $column = 'Pressure Vs Flow';
        $seriesname = 'Pressure(Bar)';
        $seriesname2 = 'Flow(CFM)';

        if($type == 'pres'){
            $selected .= ',AMD.P_pressure';
            $selected2 .= ',AMD.flow';
            $aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }else if($type == 'flow'){
            $selected .= ',AMD.flow';
            $selected2 .= ',AMD.P_pressure';
            $aa = 'flow';
            $aa2 = 'P_pressure';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Flow(CFM)';
            $seriesname2 = 'Pressure(Bar)';
        }else{
            $selected .= ',AMD.P_pressure';
            $selected2 .= ',AMD.flow';
            $aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }

        $data = $this->air_model->getAirMeterData($meter_id, $selected);
        $data2 = $this->air_model->getAirMeterData($meter_id, $selected2);
        
        $data = array_reverse($data);
        $data2 = array_reverse($data2);

        $this->data['meterName'] = isset($data[0]->name) ? $data[0]->name : 'NA';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;

        
        // Iterate First Data Set
        $newData = array();
        if (isset($data) && count($data)>0) {
            foreach ($data as $indKey => $indValue) {
                if(isset($indValue->end_date_time)){
                    $newData[$indValue->end_date_time] = isset($indValue->{$aa}) ? $indValue->{$aa} : 0;
                }
               
            }
        }
        // Iterate Second Data Set
        $newData2 = array();
        if (isset($data2) && count($data2)>0) {
            foreach ($data2 as $indKey2 => $indValue2) {
                if(isset($indValue2->end_date_time)){
                    $newData2[$indValue2->end_date_time] = isset($indValue2->{$aa2}) ? $indValue2->{$aa2} : 0;
                }
               
            }
        }

       

        $this->data['dayCount'] = 1;    

        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';
        if(count($newData) > 0){
            foreach ($newData as $key => $value) {                
                if(isset($key) && $key!=''){
                    /*$dataSetDate .= '{
                        "label": "'.$key.'"
                    },';
                    $dataSet .= '{
                        "value": "'.number_format((float)round($value,3), 2, '.', '').'"
                    },';
                    if(isset($newData2[$key])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($newData2[$key],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }*/
                    if($type == 'pres'){
                        $p = isset($value) ? number_format((float)round($value,3), 2, '.', '') : '0';
                        $f = isset($newData2[$key]) ? number_format((float)round($newData2[$key],3), 2, '.', '') : '0';
                    }else{
                        $f = isset($value) ? number_format((float)round($value,3), 2, '.', '') : '0';
                        $p = isset($newData2[$key]) ? number_format((float)round($newData2[$key],3), 2, '.', '') : '0';
                    }
                    
                    $dataSet .= '{
                        "date": "'.$key.'",
                        "pressure": "'.$p.'",
                        "flow": "'.$f.'"                    
                    },';

                }
            }
        }
        

        $this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;
        
        /*echo "<pre>";
        var_dump($this->data);
        die();*/
        
        $this->data['chartData'] = $dataSet;

        if($this->data['dayCount'] == 1){
            //$this->load->view('dashboard/chart_loading_am_bar',$this->data);
            $this->load->view('dashboard/chart_loading_am_line',$this->data);
        }else{
            $this->load->view('dashboard/chart_loading_am_bar',$this->data);
        }

        //$this->load->view('dashboard/chart_loading',$this->data);
    }

    public function consistencyIndex($avgVal , $currentVal , $dataSet ){
    	$retData['consistencyArray'] = array();
    	$retData['flag'] = 1;
    	if(isset($currentVal) && isset($dataSet)){

			$dataSetArray = json_decode($dataSet,true);
			if(count($dataSetArray)>0){
				//echo "<pre>";
				// First logic
				$firstAlertCounterArray = array();
				foreach ($dataSetArray as $timestamp => $value) {
					if($value > $avgVal){
						$firstAlertCounterArray[$timestamp] = 1;
					}else{
						$firstAlertCounterArray[$timestamp] = 0;
					}
				}

				if(count($firstAlertCounterArray)>0){
					// Second Logic
					$secondCounterArray = array();
					$previousVal = "";
					$previousTimestamp = "";				

					foreach($firstAlertCounterArray as $key1=>$val1){					

						if(count($secondCounterArray)==0){
							$secondCounterArray[$key1] = $val1;
						}else{

							$secondCounterArray[$key1] = $val1 + $previousVal;
						}

						$previousTimestamp = $key1;
						$previousVal = isset($secondCounterArray[$previousTimestamp]) ? $secondCounterArray[$previousTimestamp] : $val1;

					}
				}

				//var_dump($secondCounterArray);
				if(count($secondCounterArray)>0){
					$consistencyArray = array();
					$i=1;
					foreach($secondCounterArray as $key2 => $value2) {
						
						$consistencyArray[$key2] = (1 - $value2/$i) * 100;
					$i++;}

					//var_dump($consistencyArray);
				}
				if (count(array_unique($consistencyArray)) === 1 && end($consistencyArray) == 0) {
					$retData['flag'] = 2;//0 logic

				}elseif (count(array_unique($consistencyArray)) === 1 && end($consistencyArray) == 100) {
					$retData['flag'] = 3;//100 logic
				}

				$retData['consistencyArray'] = $consistencyArray;

			}

		}
		return $retData;
	}


    public function getMeterChart_CPP(){

        $meter_id = $this->uri->segment(4);
        $type = $this->uri->segment(5);
        $curVal = $this->uri->segment(6);
        $deltaGenVal = $this->uri->segment(7);
        $staticDeltaVal = $this->uri->segment(8);
        $selected = 'DM.name,DMD.end_date_time';
        $aa = 'temp';
        $column = 'Temperature';
        if($type == 'temp'){
            $selected .= ',DMD.temp';
        }else if($type == 'pres'){
            $selected .= ',DMD.pressure';
            $aa = 'pressure';
            $column = 'Pressure';
        }else if($type == 'flow'){
            $selected .= ',DMD.flow';
            $aa = 'flow';
            $column = 'Flow';
        }else{
            $selected .= ',DMD.pressure';
            $aa = 'pressure';
            $column = 'Pressure';
        }

        $data = $this->cpp_model->getDataLoggerMeterData($meter_id, $selected);
        /*echo "<pre>";
        var_dump($data);
        die();*/

        $data = array_reverse($data);
        $dataSet = '';
        
        $maxMin = array();
        $this->data['meterName'] = '';
        $this->data['meterNameColumn'] = $column;
        if(count($data) > 0){
            $this->data['meterName'] = $data[0]->name;
            foreach ($data as $key => $value) {                
                
                $dataSet .= '{
                    "label": "'.$value->end_date_time.'",
                    "value": "'.round($value->{$aa},3).'"
                },';
                $maxMin[] = $value->{$aa};                
                
            }
        }
        $this->data['consisIndex'] = 'no';
        
        $this->data['graph_logic'] = '1';
        
        $this->data['max_val'] = intval(max($maxMin)) + 1;
        $this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
        //echo intval(min($maxMin));    
        $this->data['dataSet'] = $dataSet;
        //$this->data['dataSetConsis'] = $dataSetConsis;
        
        $this->load->view('dashboard/chart',$this->data);
    }


    public function getMeterChart_CPPForDoubleGraph(){

        $meter_id = $this->uri->segment(4);
        $type = $this->uri->segment(5);

        $selected = 'DM.name,DMD.end_date_time';
        $selected2 = 'DM.name,DMD.end_date_time';
        $aa = 'pressure';
        $aa2 = 'flow';
        $column = 'Pressure Vs Flow';
        $seriesname = 'Pressure(Bar)';
        $seriesname2 = 'Flow(CFM)';
        
        if($type == 'pres'){
            $selected .= ',DMD.pressure';
            $selected2 .= ',DMD.flow';
            $aa = 'pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }else if($type == 'flow'){
            $selected .= ',DMD.flow';
            $selected2 .= ',DMD.pressure';
            $aa = 'flow';
            $aa2 = 'pressure';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Flow(CFM)';
            $seriesname2 = 'Pressure(Bar)';
        }else{
            $selected .= ',DMD.pressure';
            $selected2 .= ',DMD.flow';
            $aa = 'pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }

        $data = $this->cpp_model->getDataLoggerMeterData($meter_id, $selected);
        $data2 = $this->cpp_model->getDataLoggerMeterData($meter_id, $selected2);
        

        $data = array_reverse($data);
        $data2 = array_reverse($data2);

        

        $this->data['meterName'] = isset($data[0]->name) ? $data[0]->name : 'NA';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;

        // Iterate First Data Set
        $newData = array();
        if (isset($data) && count($data)>0) {
            foreach ($data as $indKey => $indValue) {
                if(isset($indValue->end_date_time)){
                    $newData[$indValue->end_date_time] = isset($indValue->{$aa}) ? $indValue->{$aa} : 0;
                }
               
            }
        }
        // Iterate Second Data Set
        $newData2 = array();
        if (isset($data2) && count($data2)>0) {
            foreach ($data2 as $indKey2 => $indValue2) {
                if(isset($indValue2->end_date_time)){
                    $newData2[$indValue2->end_date_time] = isset($indValue2->{$aa2}) ? $indValue2->{$aa2} : 0;
                }
               
            }
        }

        

        $this->data['dayCount'] = 1;        

        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';

        if(count($newData) > 0){

            foreach ($newData as $key => $value) {                
                if(isset($key) && $key!=''){
                    /*$dataSetDate .= '{
                        "label": "'.$key.'"
                    },';

                    $dataSet .= '{
                        "value": "'.number_format((float)round($value,3), 2, '.', '').'"
                    },';

                    if(isset($newData2[$key])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($newData2[$key],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }*/
                    if($type == 'pres'){
                        $p = isset($value) ? number_format((float)round($value,3), 2, '.', '') : '0';
                        $f = isset($newData2[$key]) ? number_format((float)round($newData2[$key],3), 2, '.', '') : '0';
                    }else{
                        $f = isset($value) ? number_format((float)round($value,3), 2, '.', '') : '0';
                        $p = isset($newData2[$key]) ? number_format((float)round($newData2[$key],3), 2, '.', '') : '0';
                    }
                    
                    $dataSet .= '{
                        "date": "'.$key.'",
                        "pressure": "'.$p.'",
                        "flow": "'.$f.'"                    
                    },';


                }            
                
            }
        }
        

        $this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;

        $this->data['chartData'] = $dataSet;

        if($this->data['dayCount'] == 1){
            //$this->load->view('dashboard/chart_loading_am_bar',$this->data);
            $this->load->view('dashboard/chart_loading_am_line',$this->data);
        }else{
            $this->load->view('dashboard/chart_loading_am_bar',$this->data);
        }

        //$this->load->view('dashboard/chart_loading',$this->data);
    }
	
}

