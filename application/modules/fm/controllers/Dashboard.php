<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	public $data = array();
	public $shiftArr = array();
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->shiftArr = array(1,2,3);
		$this->load->model('fm_model');
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
		//print_r($this->data['dateRange']);
		if($this->input->post('working_shift') > 0){
			$this->data['search'] = true;
			$this->data['meter_id'] = trim($this->input->post('meter_id'));
			$this->data['working_shift'] = trim($this->input->post('working_shift'));
			$this->data['dateRange'] = $this->_getDateDetails();
		}
		

		$this->data['last15DataSet'] = $this->fm_model->getLast15DataTypeWise();
		$this->data['startDateShow'] = $this->startDateTime = isset($this->data['last15DataSet'][0]->end_date_time) ? $this->data['last15DataSet'][0]->end_date_time : '0000-00-00 00:00:00';
		$this->dataTimeInterval = 'PT00H15M00S';
		$this->data['endDateShow'] = $this->_dateAdd();

		$this->data['typeWise'] = $this->_getTotalTypeWise();

        //echo "<pre>";
        //var_dump($this->data['typeWise']);




		//var_dump($this->data['last15DataSet']);
		$this->data['totalInsertDataset'] = array();

		if(isset($this->data['last15DataSet']) && count($this->data['last15DataSet'])>0){
			foreach ($this->data['last15DataSet'] as $keyMeterD => $Metervalueid) {
				if(isset($Metervalueid->meter_id)){

					$this->data['totalInsertDataset'][$Metervalueid->meter_id] = isset($this->fm_model->getCountMeterData($Metervalueid->meter_id)[0]->C) ? $this->fm_model->getCountMeterData($Metervalueid->meter_id)[0]->C : 0;
				}
				
			}
		}

		$this->data['last_steam_ack_counter'] = $this->fm_model->getSteamACKCounterLastID($this->session->userdata('user_id'));
		

        ///////////// Get Data From Datalog CPP ****************Start
        $this->data['last15DataSet_cpp'] = $this->cpp_model->getLast15DataTypeWise();
        
        //echo "<pre>";
        $this->data['result_cpp'] = $this->geCalculationResult_cpp();
        
        //var_dump($this->data['result_cpp']['meter_data']['head']);
        //die();
        ///////////// Get Data From Datalog CPP *****************End
		$this->data['all'] = $this->load->view('dashboard/all',$this->data, true);
		
		
		$where = array();
		$this->data['tags'] = $this->tbl_generic_model->get('master_tag', '*', $where);
		$this->load->view('dashboard/index', $this->data);
	}

    private function geCalculationResult_cpp(){
        $retData = array();
        if(count($this->data['last15DataSet_cpp'])){
            $data = array();
            //echo "<pre>";
            //var_dump($this->data['last15DataSet_cpp']);
            $startDateTime = "";
            foreach ($this->data['last15DataSet_cpp'] as $key => $value) {
                
                if($value->meter_id!=1){
                    $data['meter_data'][$value->type]['pressure_prod'][] = $value->pressure * $value->flow;

                    $data['meter_data'][$value->type]['flow'][] = $value->flow;

                    if($value->meter_id==4){
                        $data['meter_data'][$value->type]['temp'] = $value->temp;
                    }else{
                        $data['meter_data'][$value->type]['temp'] = 0;
                    }
                    
                }
                

                //var_dump($value);
                $retData['meter_data'][$value->type][$value->meter_id]['name'] = $value->name;

                $retData['meter_data'][$value->type][$value->meter_id]['pressure'] = $value->pressure; 
                $retData['meter_data'][$value->type][$value->meter_id]['temp'] = $value->temp; 
                $retData['meter_data'][$value->type][$value->meter_id]['flow'] = $value->flow; 
               
               $startDateTime =  $value->end_date_time; 
                
                
            }
            if(isset($data['meter_data']) && count($data['meter_data']) > 0){
                foreach ($data['meter_data'] as $key1 => $value1) {

                    $retData['meter_data'][$key1]['total']['flow'] = array_sum($value1['flow']);

                    //$retData[$key]['total']['P_pressure'] = array_sum($value['P_pressure'])/count($value['P_pressure']);
                    if($retData['meter_data'][$key1]['total']['flow'] > 0){
                        $retData['meter_data'][$key1]['total']['pressure'] = array_sum($value1['pressure_prod'])/$retData['meter_data'][$key1]['total']['flow'];
                    }else{
                        $retData['meter_data'][$key1]['total']['pressure'] = 0;
                    }

                    $retData['meter_data'][$key1]['total']['temp'] = $value1['temp'];
                }
            }

            //var_dump($startDateTime);

            $date = new DateTime($startDateTime);
            $date->add(new DateInterval('PT00H15M00S'));
            $endDateTime = $date->format('Y-m-d H:i:s');
            
            
            //var_dump($retData);
            $retData['timestamp']['start_time'] = $startDateTime;
            $retData['timestamp']['end_time'] = $endDateTime;

        }
        /*echo "<pre>";
        var_dump($retData);
        die();*/

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
                //var_dump($value->meter_id);
                if($value->meter_id!=6 && $value->meter_id!=8){
                    //var_dump($value->meter_id);
                    $data[$value->type]['flow'][] = $value->flow;
                }
				
				$data[$value->type]['T_temp'][] = $value->T_temp;
				$data[$value->type]['T_temp_prod'][] = $value->T_temp * $value->flow;

				$data[$value->type]['TTL_flow'][] = $value->TTL_flow;
				$data[$value->type]['steam_enthalpy'][] = $value->steam_enthalpy;
				$data[$value->type]['steam_enthalpy_prod'][] = $value->flow * $value->steam_enthalpy;

				$data[$value->type]['steam_heat_content'][] = $value->steam_heat_content;
				$retData[$value->type]['meter'][$value->meter_id]['name'] = $value->name;
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
			//echo "<pre>";
			//print_r($data);
			if(count($data) > 0){
				foreach ($data as $key => $value) {

					$retData[$key]['total']['flow'] = array_sum($value['flow']);

					//$retData[$key]['total']['P_pressure'] = array_sum($value['P_pressure'])/count($value['P_pressure']);
					if($retData[$key]['total']['flow'] > 0){
						$retData[$key]['total']['P_pressure'] = array_sum($value['P_pressure_prod'])/$retData[$key]['total']['flow'];
					}else{
						$retData[$key]['total']['P_pressure'] = 0;
					}
					

					//$retData[$key]['total']['flow'] = array_sum($value['flow'])/count($value['flow']);

					//$retData[$key]['total']['T_temp'] = array_sum($value['T_temp'])/count($value['T_temp']);
					if($retData[$key]['total']['flow'] > 0){
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
                if(!isset($retData['dist']['total']['flow'])) $retData['dist']['total']['flow'] = 0;
                if(!isset($retData['main']['total']['flow'])) $retData['main']['total']['flow'] = 0;

                $retData['dist']['total']['flow'] = ($retData['dist']['total']['flow'] + $retData['main']['total']['flow']);
			}
		}

		return $retData;
	}

	private function _getDateDetails(){
		
		$meter_id = $this->data['meter_id'] ;
		$data = $this->fm_model->getDateDetails($meter_id);
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
    	$this->data['last15DataSet'] = $this->fm_model->getLast15DataTypeWise();
		$this->data['typeWise'] = $this->_getTotalTypeWise();

        ///////////// Get Data From Datalog CPP ****************Start
        $this->data['last15DataSet_cpp'] = $this->cpp_model->getLast15DataTypeWise();
        $this->data['result_cpp'] = $this->geCalculationResult_cpp();
        ///////////// Get Data From Datalog CPP *****************End
        
		$retData['all'] = $this->load->view('dashboard/all',$this->data, true);
		$retData['status'] = 'success'; 
		echo json_encode($retData);
    }

    public function updateACKStatus(){

    	$retData['status'] = 'error';
    	if($this->session->userdata('user_id')!='' &&$this->uri->segment(4)!=''){
    		$data['user_id'] = $this->session->userdata('user_id');
	    	$data['status_updated_on'] = date('Y-m-d H:i:s');
	    	$data['status'] = $this->uri->segment(4);

	    	$this->fm_model->add('steam_acknowledge_counter',$data);
	    	$retData['status'] = 'success';
    	}
    	echo json_encode($retData);
    	
    }

    public function getMeterChartTotalGNvsDIST(){
    	
    	$type = $this->uri->segment(4);
    	//$meterType = $this->uri->segment(5);

    	$selected = 'SM.name,SMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	if($type == 'temp'){
    		$selected .= ',SMD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',SMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',SMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$meterdata = $this->fm_model->getMeter();
    	//echo "<pre>";
    	

    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			if(isset($mvalue->type) && $mvalue->type=='main') continue;

    			$data[$mvalue->type][$mvalue->meter_id] = $this->fm_model->getSteamMeterData($mvalue->meter_id);

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

    public function getMeterChartTotal(){
    	
    	$type = $this->uri->segment(4);
    	$meterType = $this->uri->segment(5);
    	$selected = 'SM.name,SMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	if($type == 'temp'){
    		$selected .= ',SMD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',SMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',SMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	// Get The Meter Details by Type(Generation or distribution)
    	$meterdata = $this->fm_model->getMeterTypeWise($meterType);
        

    	//echo "<pre>";
        //var_dump($meterdata);
        //var_dump($meterdataForMain);

    	// Fetch the data meter wise
    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			if($mvalue->meter_id!=6 && $mvalue->meter_id!=8){

                    $data[$mvalue->meter_id] = $this->fm_model->getSteamMeterData($mvalue->meter_id);
                }
    			

    		}
    	}
        if(isset($meterType) && $meterType=='dist'){
            $meterdataForMain = $this->fm_model->getMeterTypeWise('main');
            if(isset($meterdataForMain) && count($meterdataForMain)>0){
                foreach ($meterdataForMain as $mkeyMain => $mvalueMain) {

                    $data[$mvalueMain->meter_id] = $this->fm_model->getSteamMeterData($mvalueMain->meter_id);                    

                }
            }
        }
        

        //var_dump($data);

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
    			
		        /*$dataSet .= '{
		            "label": "'.$dateTimeKey.'",
		            "value": "'.round($dateTimeValue[$aa],3).'"
		        },';*/
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
    	$selected = 'SM.name,SMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	if($type == 'temp'){
    		$selected .= ',SMD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',SMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',SMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$data = $this->fm_model->getSteamMeterData($meter_id, $selected);
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
			        },';*/
                    $dataSet .= '{
                        "date": "'.$value->end_date_time.'",
                        "paramVal": "'.round($deltaGenVal - $value->{$aa},3).'"
                    },';
			        $maxMin[] = $deltaGenVal - $value->{$aa};
		        	$consistencyArr[$value->end_date_time] = $deltaGenVal - $value->{$aa};
		        }else{
		        	/*$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($value->{$aa},3).'"
			        },';*/

                    $dataSet .= '{
                        "date": "'.$value->end_date_time.'",
                        "paramVal": "'.round($value->{$aa},3).'"
                    },';

			        $maxMin[] = $value->{$aa};
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
    	
        // It Will Be implemented double chart
        $this->data['chartData'] = $dataSet;
        $this->load->view('am_chart/line',$this->data);


    	//$this->load->view('dashboard/chart',$this->data);
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
                /*
                $dataSet .= '{
                    "label": "'.$value->end_date_time.'",
                    "value": "'.round($value->{$aa},3).'"
                },';*/
                
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->{$aa},3).'"
                },';

                //$maxMin[] = $value->{$aa};                
                
            }
        }

        //$this->data['consisIndex'] = 'no';
        
        //$this->data['graph_logic'] = '1';
        
        //$this->data['max_val'] = intval(max($maxMin)) + 1;
        //$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
        //echo intval(min($maxMin));    
        //$this->data['dataSet'] = $dataSet;
        //$this->data['dataSetConsis'] = $dataSetConsis;
        $this->data['chartData'] = $dataSet;
        $this->load->view('am_chart/line',$this->data);
        //$this->load->view('dashboard/chart',$this->data);
    }


    public function getMeterChartTotal_CPP(){
        
        $type = $this->uri->segment(4);
        $meterType = $this->uri->segment(5);
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

        // Get The Meter Details by Type(Generation or distribution)
        $meterdata = $this->cpp_model->getMeterTypeWise($meterType);
        
        // Fetch the data meter wise
        $data = array();
        if(isset($meterdata) && count($meterdata)>0){
            foreach ($meterdata as $mkey => $mvalue) {
                if($mvalue->id=='1') continue;

                $data[$mvalue->id] = $this->cpp_model->getDataLoggerMeterData($mvalue->id);

            }
        }

        // Iterate Array according to End Date Time
        $dataArray = $this->_setData_CPP($data);        
        
        // Calculation Of Value
        $dataFinalArray = $this->_calculationTimestamp_CPP($dataArray);     
        
        $dataFinalArray = array_reverse($dataFinalArray);

        $dataSet = '';
        //$consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Head';
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
                //$maxMin[] = $dateTimeValue[$aa];
                
            }
        }
        
        /////////////////////////////////////////////////
        //$this->data['consisIndex'] = 'no';
        //$this->data['graph_logic'] = '1';
        /////////////////////////////////////////////////
        //$this->data['max_val'] = intval(max($maxMin)) + 1;
        //$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
        //echo intval(min($maxMin));    
        //$this->data['dataSet'] = $dataSet;
        //$this->data['dataSetConsis'] = $dataSetConsis;
        
        //$this->load->view('dashboard/chart',$this->data);
        $this->data['chartData'] = $dataSet;
        $this->load->view('am_chart/line',$this->data);
    }


    private function _setData_CPP($dataSet){

        $dataArray = array();
        if(isset($dataSet) && count($dataSet)>0){
            foreach ($dataSet as $meterId => $meter_value) {
                foreach ($meter_value as $akey => $aValue) {
                    
                    $dataArray[$aValue->end_date_time][$meterId]['flow'] = $aValue->flow;
                    $dataArray[$aValue->end_date_time][$meterId]['pressure'] = $aValue->pressure;
                    $dataArray[$aValue->end_date_time][$meterId]['temp'] = $aValue->temp;
                }
                
            }
        }

        return $dataArray;
    }


    private function _calculationTimestamp_CPP($dataSet){

        $finalArray = array();
        if(isset($dataSet) && count($dataSet)>0){
            foreach ($dataSet as $dateKey => $dateValue) {
                
                foreach ($dateValue as $meterkey => $metersvalue) {
                    
                    $finalArray[$dateKey]['flow'][] = $metersvalue['flow'];
                    

                    $finalArray[$dateKey]['pressure'][] = $metersvalue['pressure'];
                    $finalArray[$dateKey]['pressure_prod'][] = $metersvalue['pressure'] * $metersvalue['flow'];

                    $finalArray[$dateKey]['temp'] = $metersvalue['temp'];
                                       

                }

                
            }
        } 

        

        $dataFinalArray = array();
        if(isset($finalArray) && count($finalArray)>0){
            foreach ($finalArray as $dateMkey => $dateMvalue) {
                
                $dataFinalArray[$dateMkey]['flow'] = array_sum($dateMvalue['flow']);
                
                $dataFinalArray[$dateMkey]['temp'] = $dateMvalue['temp'];

                if($dataFinalArray[$dateMkey]['flow']>0){
                    $dataFinalArray[$dateMkey]['pressure'] = array_sum($dateMvalue['pressure_prod']) / $dataFinalArray[$dateMkey]['flow'];
                }else{
                    $dataFinalArray[$dateMkey]['pressure'] = 0;
                }

                

            }
        }

        

        return $dataFinalArray;
    }


    public function getMeterChart_CPPgenVSpph(){

        $meter_id = '4';
        $type = $this->uri->segment(4);
        $meterType = 'gen';
        
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

        ///////////// Start Auxiliary Steam Header Get Data
        $data_auxheader = $this->cpp_model->getDataLoggerMeterData($meter_id, $selected);
        

        $data_auxheader = array_reverse($data_auxheader);
        // Re-Structure Aux Header Point Data 
        $auxHeaderArray = array();
        if(count($data_auxheader)>0){
            foreach ($data_auxheader as $indexkey => $datavalue) {
                $auxHeaderArray[$datavalue->end_date_time] = $datavalue->{$aa};
            }
        }
        //////////////// End Auxiliary Steam Header


        ////////////////////////////// Get Total CPP Generation
        // Get The Meter Details by Type(Generation or distribution)
        $meterdata = $this->cpp_model->getMeterTypeWise($meterType);
        
        // Fetch the data meter wise
        $data = array();
        if(isset($meterdata) && count($meterdata)>0){
            foreach ($meterdata as $mkey => $mvalue) {
                if($mvalue->id=='1') continue;

                $data[$mvalue->id] = $this->cpp_model->getDataLoggerMeterData($mvalue->id);

            }
        }

        // Iterate Array according to End Date Time
        $dataArray = $this->_setData_CPP($data);        
        
        // Calculation Of Value
        $dataFinalArray = $this->_calculationTimestamp_CPP($dataArray);     
        
        $dataFinalArray = array_reverse($dataFinalArray);

        ////////////////////////////// End Total CPP
        $dataSet = '';
        
        $maxMin = array();
        $this->data['meterName'] = 'CPP Generation VS Power plant header';
        $this->data['meterNameColumn'] = $column;
        if(count($dataFinalArray) > 0){
            //$this->data['meterName'] = $data[0]->name;
            foreach ($dataFinalArray as $key => $value) {                
                
                $V = $value[$aa] - $auxHeaderArray[$key];
                /*$dataSet .= '{
                    "label": "'.$key.'",
                    "value": "'.round($V,3).'"
                },';*/
                $dataSet .= '{
                    "date": "'.$key.'",
                    "paramVal": "'.round($V,3).'"
                },';
                //$maxMin[] = $V;                
                
            }
        }
        //$this->data['consisIndex'] = 'no';
        
        //$this->data['graph_logic'] = '1';
        
        //$this->data['max_val'] = intval(max($maxMin)) + 1;
        //$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
        //echo intval(min($maxMin));    
        //$this->data['dataSet'] = $dataSet;
        //$this->data['dataSetConsis'] = $dataSetConsis;
        
        //$this->load->view('dashboard/chart',$this->data);
        $this->data['chartData'] = $dataSet;
        $this->load->view('am_chart/line',$this->data);
    }


    public function getMeterChart_CPP_PPHvsWILgen(){

        $meter_id = '4';
        $type = $this->uri->segment(4);
        $meterType = 'gen';
        
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

        ///////////// Start WIL GEN Total
        // Get The Meter Details by Type(Generation or distribution)
        $meterdataWIL = $this->fm_model->getMeterTypeWise($meterType);
        
        // Fetch the data meter wise
        $dataWIL = array();
        if(isset($meterdataWIL) && count($meterdataWIL)>0){
            foreach ($meterdataWIL as $mkey => $mvalue) {
                
                $dataWIL[$mvalue->meter_id] = $this->fm_model->getSteamMeterData($mvalue->meter_id);

            }
        }

        // Iterate Array according to End Date Time
        $dataArrayWIL = $this->_setData($dataWIL);        
        
        // Calculation Of Value
        $dataFinalArrayWIL = $this->_calculationTimestamp($dataArrayWIL);     

        $dataFinalArrayWIL = array_reverse($dataFinalArrayWIL);
        
        //////////// End WIL GEN Total

        ///////////// Start Auxiliary Steam Header Get Data
        $data_auxheader = $this->cpp_model->getDataLoggerMeterData($meter_id, $selected);
        

        $data_auxheader = array_reverse($data_auxheader);
        // Re-Structure Aux Header Point Data 
        $auxHeaderArray = array();
        if(count($data_auxheader)>0){
            foreach ($data_auxheader as $indexkey => $datavalue) {
                $auxHeaderArray[$datavalue->end_date_time] = $datavalue->{$aa};
            }
        }

        //////////////// End Auxiliary Steam Header


        ////////////////////////////// Get Total CPP Generation
        // Get The Meter Details by Type(Generation or distribution)
        $meterdataCPP = $this->cpp_model->getMeterTypeWise($meterType);
        
        // Fetch the data meter wise
        $dataCPP = array();
        if(isset($meterdataCPP) && count($meterdataCPP)>0){
            foreach ($meterdataCPP as $mkey1 => $mvalue1) {
                if($mvalue1->id=='1') continue;

                $dataCPP[$mvalue1->id] = $this->cpp_model->getDataLoggerMeterData($mvalue1->id);

            }
        }

        // Iterate Array according to End Date Time
        $dataArrayCPP = $this->_setData_CPP($dataCPP);        
        
        // Calculation Of Value
        $dataFinalArrayCPP = $this->_calculationTimestamp_CPP($dataArrayCPP);     
        
        $dataFinalArrayCPP = array_reverse($dataFinalArrayCPP);
        /*echo "<pre>";
        var_dump($dataFinalArrayCPP);
        die();*/
        ////////////////////////////// End Total CPP
        $dataSet = '';
        
        $maxMin = array();
        $this->data['meterName'] = 'Power Plant Header VS WIL Generation';
        $this->data['meterNameColumn'] = $column;
        if(count($dataFinalArrayWIL) > 0){
            //$this->data['meterName'] = $data[0]->name;
            foreach ($dataFinalArrayWIL as $key => $value) {    

                $V = 0;
                if($type=='pres'){
                    if(isset($auxHeaderArray[$key])){
                        $V = $auxHeaderArray[$key] - $value['P_'.$aa];
                    }
                    
                }
                if($type=='temp'){
                    if(isset($auxHeaderArray[$key])){
                        $V = $auxHeaderArray[$key] - $value['T_'.$aa];
                    }
                    
                }
                if($type=='flow'){
                    if(isset($dataFinalArrayCPP[$key][$aa])){
                        $V = $dataFinalArrayCPP[$key][$aa] - $value[$aa];
                    }
                    
                }
                /*
                $dataSet .= '{
                    "label": "'.$key.'",
                    "value": "'.round($V,3).'"
                },';
                */
                $dataSet .= '{
                    "date": "'.$key.'",
                    "paramVal": "'.round($V,3).'"
                },';

                $maxMin[] = $V;                
                
            }
        }
        $this->data['consisIndex'] = 'no';
        
        $this->data['graph_logic'] = '1';
        
        $this->data['max_val'] = intval(max($maxMin)) + 1;
        $this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
        //echo intval(min($maxMin));    
        $this->data['dataSet'] = $dataSet;
        //$this->data['dataSetConsis'] = $dataSetConsis;
        
        $this->data['chartData'] = $dataSet;
        $this->load->view('am_chart/line',$this->data);

        //$this->load->view('dashboard/chart',$this->data);
    }







	
}