<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller {
	public $data = array();
	public $shiftArr = array();
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->shiftArr = array(1,2,3);
		$this->load->model('air_model');

	}

	public function index(){
		$this->data = array();
		$this->data['all'] = null; 
		$startDate = '';
		$this->data['dateRange'] = $this->_getDateDetails();
		$this->data['dateRange']['selectedDate'] =  $this->data['dateRange']['min_date'];
		$this->data['dateRange']['selectedDateEnd'] =  $this->data['dateRange']['min_date'];
		if(isset($_POST['startDate'])){
			$this->startDateTime = date('Y-m-d',strtotime($_POST['startDate']));
			$shift = $this->air_model->getShiftStart(1);
			$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
			$this->startDateTime = date('Y-m-d',strtotime($_POST['endDate']));
			$this->dataTimeInterval = 'PT24H00M00S';
			$endDate = $this->_dateAdd();
			$this->data['last15DataSet'] = $this->air_model->getLastDateDataTypeWise($startDate ,$endDate);
			$this->data['typeWise'] = $this->_getTotalTypeWise();
			$this->data['dateRange']['selectedDate'] = date('Y-m-d', strtotime($_POST['startDate']));
			$this->data['dateRange']['selectedDateEnd'] = date('Y-m-d', strtotime($_POST['endDate']));
			
			$this->data['all'] = $this->load->view('report/all_day',$this->data, true);
		}

		$this->load->view('report/index', $this->data);
	}

	public function shift(){
		$this->data = array();
		$this->data['all'] = null; 
		$this->data['shiftVal'] = 8;
		$this->data['dateRange'] = $this->_getDateDetails();
		$this->data['dateRange']['selectedDate'] = $this->data['dateRange']['min_date'];
		$startDate = '';
		if(isset($_POST['startDate']) && isset($_POST['sifthour'])){
			$shiftHours = trim($_POST['sifthour']);
			$this->startDateTime = date('Y-m-d',strtotime($_POST['startDate']));
			if($shiftHours == 8){
				$shift = $this->air_model->getShiftStart(1);
				$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
				$startDate = $this->startDateTime = $this->_dateAdd();
			}elseif($shiftHours == 16){
				$this->dataTimeInterval = 'PT16H00M00S';
				$startDate = $this->startDateTime = $this->_dateAdd();
			}elseif($shiftHours == 24){
				$this->dataTimeInterval = 'PT24H00M00S';
				$startDate = $this->startDateTime = $this->_dateAdd();
			}
			

			$this->dataTimeInterval = 'PT08H00M00S';
			$endDate = $this->_dateAdd();

			$this->data['last15DataSet'] = $this->air_model->getLastDateDataTypeWise($startDate ,$endDate);
			$this->data['typeWise'] = $this->_getTotalTypeWise();
			$this->data['dateRange']['selectedDate'] = $_POST['startDate'];
			$this->data['shiftVal'] = strtotime($startDate);
			$this->data['all'] = $this->load->view('shift/all',$this->data, true);
		}
		//die();
		
		$this->load->view('shift/index', $this->data);
	}


	private function _getTotalTypeWise(){
		$retData = array();
		if(count($this->data['last15DataSet'])){
			$data = array();
			$typeWiseArr = array();
			foreach ($this->data['last15DataSet'] as $key => $value) {
				
				$data[$value->type]['meter'][$value->meter_id]['name'] = $value->name;
				
				$data[$value->type]['meter'][$value->meter_id]['P_pressure'][] = $value->P_pressure;
				
				if(!isset($data[$value->type]['meter'][$value->meter_id]['P_pressure_count'])) $data[$value->type]['meter'][$value->meter_id]['P_pressure_count'] = 1;

				if($value->P_pressure>0){
					$data[$value->type]['meter'][$value->meter_id]['P_pressure_count'] = $data[$value->type]['meter'][$value->meter_id]['P_pressure_count'] + 1;
				}
					
				

				$data[$value->type]['meter'][$value->meter_id]['flow'][] = $value->flow;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['flow_count'])) $data[$value->type]['meter'][$value->meter_id]['flow_count'] = 1;
				if($value->flow>0){

					$data[$value->type]['meter'][$value->meter_id]['flow_count'] = $data[$value->type]['meter'][$value->meter_id]['flow_count'] + 1;

				}


				$data[$value->type]['meter'][$value->meter_id]['T_temp'][] = $value->T_temp;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['T_temp_count'])) $data[$value->type]['meter'][$value->meter_id]['T_temp_count'] = 1;

				if($value->T_temp>0){
					$data[$value->type]['meter'][$value->meter_id]['T_temp_count'] = $data[$value->type]['meter'][$value->meter_id]['T_temp_count'] + 1;
				}

				$data[$value->type]['meter'][$value->meter_id]['TTL_flow'][] = $value->TTL_flow;
				$data[$value->type]['meter'][$value->meter_id]['steam_enthalpy'][] = $value->steam_enthalpy;
				
				//$data[$value->type]['meter'][$value->meter_id]['steam_enthalpy_pro'][] = $value->steam_enthalpy * $value->TTL_flow;
				$data[$value->type]['meter'][$value->meter_id]['steam_enthalpy_pro'][] = $value->steam_enthalpy * $value->flow;

				$data[$value->type]['meter'][$value->meter_id]['steam_heat_content'][] = $value->steam_heat_content;
				$data[$value->type]['meter'][$value->meter_id]['benchmark_delta_pressure'] = $value->benchmark_delta_pressure;
				$data[$value->type]['meter'][$value->meter_id]['benchmark_delta_temp'] = $value->benchmark_delta_temp;

				
				$typeWiseArr[$value->type][$value->end_date_time]['P_pressure'][] = $value->P_pressure;
				
				
				$typeWiseArr[$value->type][$value->end_date_time]['P_pressure_prod'][] = $value->P_pressure * $value->flow;

				$typeWiseArr[$value->type][$value->end_date_time]['flow'][] = $value->flow;

				$typeWiseArr[$value->type][$value->end_date_time]['T_temp'][] = $value->T_temp;
				$typeWiseArr[$value->type][$value->end_date_time]['T_temp_prod'][] = $value->T_temp * $value->flow;

				$typeWiseArr[$value->type][$value->end_date_time]['TTL_flow'][] = $value->TTL_flow;

				$typeWiseArr[$value->type][$value->end_date_time]['steam_enthalpy'][] = $value->steam_enthalpy;
				//$typeWiseArr[$value->type][$value->end_date_time]['steam_enthalpy_pro'][] = $value->steam_enthalpy * $value->TTL_flow;
				$typeWiseArr[$value->type][$value->end_date_time]['steam_enthalpy_pro'][] = $value->steam_enthalpy * $value->flow;

				$typeWiseArr[$value->type][$value->end_date_time]['steam_heat_content'][] = $value->steam_heat_content;
			}
			
			//echo "<pre>";
			//var_dump($data);

			if(count($data) > 0){
				foreach ($data as $key1 => $value1) {
					foreach($value1['meter'] as $key2 => $val2){
						$retData[$key1]['meter'][$key2]['name'] = $val2['name'];
						$retData[$key1]['meter'][$key2]['benchmark_delta_pressure'] = $val2['benchmark_delta_pressure'];
						$retData[$key1]['meter'][$key2]['benchmark_delta_temp'] = $val2['benchmark_delta_temp'];

						//$retData[$key1]['meter'][$key2]['flow'] = array_sum($val2['flow'])/count($val2['flow']);
						$retData[$key1]['meter'][$key2]['flow'] = array_sum($val2['flow'])/$val2['flow_count'];

						//$retData[$key1]['meter'][$key2]['P_pressure'] = array_sum($val2['P_pressure'])/count($val2['P_pressure']); 

						$retData[$key1]['meter'][$key2]['P_pressure'] = array_sum($val2['P_pressure'])/$val2['P_pressure_count'];
						
						//$retData[$key1]['meter'][$key2]['T_temp'] = array_sum($val2['T_temp'])/count($val2['T_temp']);	
						$retData[$key1]['meter'][$key2]['T_temp'] = array_sum($val2['T_temp'])/$val2['T_temp_count'];						

						$retData[$key1]['meter'][$key2]['TTL_flow'] = array_sum($val2['TTL_flow']);

						$retData[$key1]['meter'][$key2]['flow_sum'] = array_sum($val2['flow']);

						if($retData[$key1]['meter'][$key2]['flow_sum'] > 0){
							$retData[$key1]['meter'][$key2]['steam_enthalpy'] = array_sum($val2['steam_enthalpy_pro'])/$retData[$key1]['meter'][$key2]['flow_sum'];
							
						}else{
							$retData[$key1]['meter'][$key2]['steam_enthalpy'] = 0;
						}
						$retData[$key1]['meter'][$key2]['steam_heat_content'] = array_sum($val2['steam_heat_content']);


					}
				}
			}
			//echo "<pre>";
			//print_r($retData);

			if(count($retData)>0){
				
				$tempArr = array();
				foreach($retData as $kk=>$vv){
					foreach($vv['meter'] as $kk1=>$vv1){

						if(!isset($tempArr[$kk]['total']['flow'])) $tempArr[$kk]['total']['flow'] = 0;
						if(!isset($tempArr[$kk]['total']['P_pressure'])) $tempArr[$kk]['total']['P_pressure'] = 0;
						if(!isset($tempArr[$kk]['total']['T_temp'])) $tempArr[$kk]['total']['T_temp'] = 0;
						if(!isset($tempArr[$kk]['total']['TTL_flow'])) $tempArr[$kk]['total']['TTL_flow'] = 0;
						if(!isset($tempArr[$kk]['total']['steam_enthalpy'])) $tempArr[$kk]['total']['steam_enthalpy'] = 0;
						if(!isset($tempArr[$kk]['total']['steam_heat_content'])) $tempArr[$kk]['total']['steam_heat_content'] = 0;

						$tempArr[$kk]['total']['flow']+=isset($vv1['flow']) ? $vv1['flow'] : 0;

						$tempArr[$kk]['total']['P_pressure']+= ($vv1['P_pressure'] * $vv1['flow']);

						$tempArr[$kk]['total']['T_temp']+=$vv1['T_temp'] * $vv1['flow'];

						$tempArr[$kk]['total']['TTL_flow']+=$vv1['TTL_flow'];

						$tempArr[$kk]['total']['steam_enthalpy']+=$vv1['steam_enthalpy'] * $vv1['flow'];

						$tempArr[$kk]['total']['steam_heat_content']+=$vv1['steam_heat_content'];

					}
				}
				//echo "<pre>";
				//var_dump($tempArr);

			}
			if(count($tempArr) > 0){
				foreach($tempArr as $kk4=>$vv4){

					if(!isset($retData[$kk4]['total']['flow'])) $retData[$kk4]['total']['flow'] = 0;
					if(!isset($retData[$kk4]['total']['P_pressure'])) $retData[$kk4]['total']['P_pressure'] = 0;
					if(!isset($retData[$kk4]['total']['T_temp'])) $retData[$kk4]['total']['T_temp'] = 0;
					if(!isset($retData[$kk4]['total']['TTL_flow'])) $retData[$kk4]['total']['TTL_flow'] = 0;
					if(!isset($retData[$kk4]['total']['steam_enthalpy'])) $retData[$kk4]['total']['steam_enthalpy'] = 0;
					if(!isset($retData[$kk4]['total']['steam_heat_content'])) $retData[$kk4]['total']['steam_heat_content'] = 0;

					$retData[$kk4]['total']['flow'] = isset($vv4['total']['flow']) ? $vv4['total']['flow'] : 0;

					$retData[$kk4]['total']['P_pressure'] = (isset($vv4['total']['flow']) && $vv4['total']['flow']>0) ? ($vv4['total']['P_pressure'] / $vv4['total']['flow']) : 0;

					$retData[$kk4]['total']['T_temp'] = (isset($vv4['total']['flow']) && $vv4['total']['flow']>0) ? ($vv4['total']['T_temp'] / $vv4['total']['flow']) : 0;

					$retData[$kk4]['total']['TTL_flow'] = isset($vv4['total']['TTL_flow']) ? $vv4['total']['TTL_flow'] : 0;

					$retData[$kk4]['total']['steam_enthalpy'] = (isset($vv4['total']['flow']) && $vv4['total']['flow']>0) ? ($vv4['total']['steam_enthalpy'] / $vv4['total']['flow']) : 0;

					$retData[$kk4]['total']['steam_heat_content'] = isset($vv4['total']['steam_heat_content']) ? $vv4['total']['steam_heat_content'] : 0;
				}
			}
			if(count($typeWiseArr) > 0){

	
			}

			//echo "<pre>";print_r($retData);exit;
		}
		return $retData;
	}

	private function _getDateDetails(){
		
		$meter_id = 0;
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
    	}else{
    		$selected .= ',AMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$data = $this->air_model->getAirMeterDataDay($meter_id, $selected);
    	

    	$data = (count($data)>0) ? array_reverse($data) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $column;
    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		        if($curVal != ''){
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';
			        $maxMin[] = $deltaGenVal - $value->{$aa};
		        	$consistencyArr[$value->end_date_time] = $deltaGenVal - $value->{$aa};
		        }else{
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($value->{$aa},3).'"
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
    		$this->data['min_val_con'] = intval(min($maxMinConsis)) - 1;
    	}
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
    }


    public function getMeterChartTotalGNvsDIST(){
    	
    	$type = $this->uri->segment(4);
    	//$meterType = $this->uri->segment(5);

    	$curVal = $this->uri->segment(5);
        $deltaGenVal = $this->uri->segment(6);
        $staticDeltaVal = $this->uri->segment(7);
        $shiftDateStrToTime = trim($this->uri->segment(8));

    	if($shiftDateStrToTime == '' && $curVal > 0){
            $shiftDateStrToTime = $curVal;
        }
        $this->startDateTime = $shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
        $this->dataTimeInterval = 'PT08H00M00S';
        $shiftEndDate = $this->_dateAdd();

        $selected = 'AM.name,AMD.end_date_time';
        $aa = 'T_temp';
        $column = 'Temperature';
        /*if($type == 'temp'){
            $selected .= ',SMD.T_temp';
        }else{
            $selected .= ',SMD.P_pressure';
            $aa = 'P_pressure';
            $column = 'Pressure';
        }*/
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
        }else{
            $selected .= ',AMD.steam_enthalpy';
            $aa = 'steam_enthalpy';
            $column = 'Enthalpy';
        }

        $this->data['dayCount'] = 1;

    	$meterdata = $this->air_model->getMeter();
    	//echo "<pre>";


    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			if(isset($mvalue->type) && $mvalue->type=='main') continue;

    			//$data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterData($mvalue->meter_id);
    			$data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id,'*',$shiftStartDate, $shiftEndDate);

    		}
    	}
    	//var_dump($data);
    	//die();

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

    public function getMeterChartShiftTotal(){

    	$type = $this->uri->segment(4);
    	$meterType = $this->uri->segment(5);

    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$shiftDateStrToTime = trim($this->uri->segment(9));

    	if($shiftDateStrToTime == '' && $curVal > 0){
    		$shiftDateStrToTime = $curVal;
    	}
    	$this->startDateTime = $shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$shiftEndDate = $this->_dateAdd();

    	$selected = 'AM.name,AMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	/*if($type == 'temp'){
    		$selected .= ',SMD.T_temp';
    	}else{
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}*/
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
    	}else{
    		$selected .= ',AMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$this->data['dayCount'] = 1;

    	// Get The Meter Details by Type(Generation or distribution)
    	$meterdata = $this->air_model->getMeterTypeWise($meterType);

    	// Fetch the data meter wise
    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			
    			$data[$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id,'*',$shiftStartDate, $shiftEndDate);

    		}
    	}


    	//$data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);

    	// Iterate Array according to End Date Time
    	$dataArray = $this->_setData($data); 

    	// Calculation Of Value
    	$dataFinalArray = $this->_calculationTimestamp($dataArray);  


    	
    	$dataFinalArray = (count($dataFinalArray)>0) ? array_reverse($dataFinalArray) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Distribution';
    	$this->data['meterNameColumn'] = $column;
    	//echo "<pre>";
    	//print_r($curVal);

    	if(count($dataFinalArray) > 0){
    		//$this->data['meterName'] = $data[0]->name;
    		foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
    			
		        if($curVal != '' && $deltaGenVal != 0){
		        	$dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($deltaGenVal - $dateTimeValue[$aa],3).'"
			        },';
			        $maxMin[] = $deltaGenVal - $dateTimeValue[$aa];
		        	$consistencyArr[$dateTimeKey] = $deltaGenVal - $dateTimeValue[$aa];
		        }else{
		        	$dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue[$aa],3).'"
			        },';
			        $maxMin[] = $dateTimeValue[$aa];
		        }
		        
    		}
    	}
    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartShift(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$shiftDateStrToTime = trim($this->uri->segment(9));
    	if($shiftDateStrToTime == '' && $curVal > 0){
    		$shiftDateStrToTime = $curVal;
    	}
    	$this->startDateTime = $shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$shiftEndDate = $this->_dateAdd();
    	$selected = 'AM.name,AMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	/*if($type == 'temp'){
    		$selected .= ',SMD.T_temp';
    	}else{
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}*/
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
    	}else{
    		$selected .= ',AMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$this->data['dayCount'] = 1;
    	$data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);
    	
    	$data = (count($data)>0) ? array_reverse($data) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $column;
    	//echo "<pre>";
    	//print_r($curVal);

    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		        if($curVal != '' && $deltaGenVal != 0){
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';
			        $maxMin[] = $deltaGenVal - $value->{$aa};
		        	$consistencyArr[$value->end_date_time] = $deltaGenVal - $value->{$aa};
		        }else{
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($value->{$aa},3).'"
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
    		$this->data['min_val_con'] = intval(min($maxMinConsis)) - 1;
    	}
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartAllDayTotalGNvsDIST(){
    	
    	$type = $this->uri->segment(4);
    	//$meterType = $this->uri->segment(5);

    	$curVal = '';
    	$shift = $this->air_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(5));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	//$genconsRow = $this->uri->segment(7);

    	//$meterdata = $this->fm_model->getMeterTypeWise($meterType);
    	$meterdata = $this->air_model->getMeter();
    	//echo "<pre>";
    	
    	//var_dump($startDate);
    	//var_dump($endDate);

    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	$retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);

    	$aa = $retSelectArr['aa'];
    	$column = $retSelectArr['column'];
    	$selected = $retSelectArr['selected'];

    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {

 				if(isset($mvalue->type) && $mvalue->type=='main') continue;

    			if($this->data['dayCount'] == 1){
		    		$data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id, '*', $startDate, $endDate);
		    	}else{

		    		
	    			$selected = 'DATE_FORMAT(AMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(AMD.T_temp) T_temp, AVG(AMD.P_pressure) P_pressure, AVG(AMD.TTL_flow) TTL_flow, AVG(AMD.flow) flow, AVG(AMD.steam_enthalpy) steam_enthalpy,AVG(AMD.steam_heat_content) steam_heat_content';
	    	


		    		$data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterDataDayWiseParam($mvalue->meter_id, $selected, $startDate, $endDate);
		    	}


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
    	//$dataFinalArray = $this->_calculationTimestamp($dataArray,$genconsRow);   

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

    	
    	
    	$resultArray = (count($resultArray)>0) ? array_reverse($resultArray) : array();
    	//var_dump($dataFinalArray);
    	

    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = 'Generation Vs Distribution';

    	$this->data['meterNameColumn'] = $column;
    	if(count($data) > 0){
    			
    		foreach ($resultArray as $dateTimeKey => $dateTimeValue) {
    			
		        $dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue,3).'"
			        },';
			        $maxMin[] = $dateTimeValue;
		        
    		}
    	}
    	//var_dump($dataSet);
    	//die();

    	//////////////
    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	//////////////////////
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartAllDayTotal(){
    	
    	$type = $this->uri->segment(4);
    	$meterType = $this->uri->segment(5);

    	$curVal = '';
    	$shift = $this->air_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	//$genconsRow = $this->uri->segment(8);

    	$meterdata = $this->air_model->getMeterTypeWise($meterType);
    	//echo "<pre>";
    	
    	//var_dump($startDate);
    	//var_dump($endDate);

    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	$retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);

    	$aa = $retSelectArr['aa'];
    	$column = $retSelectArr['column'];
    	$selected = $retSelectArr['selected'];

    	
    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
 
    			if($this->data['dayCount'] == 1){
		    		$data[$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id, '*', $startDate, $endDate);
		    	}else{
		    		
	    			$selected = 'DATE_FORMAT(AMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(AMD.T_temp) T_temp, AVG(AMD.P_pressure) P_pressure, AVG(AMD.TTL_flow) TTL_flow, AVG(AMD.flow) flow, AVG(AMD.steam_enthalpy) steam_enthalpy,AVG(AMD.steam_heat_content) steam_heat_content';

		    		$data[$mvalue->meter_id] = $this->air_model->getAirMeterDataDayWiseParam($mvalue->meter_id, $selected, $startDate, $endDate);
		    	}


    		}
    	}

    	// Iterate Array according to End Date Time
    	$dataArray = $this->_setData($data);  

    	// Calculation Of Value
    	$dataFinalArray = $this->_calculationTimestamp($dataArray);   
    	
    	$dataFinalArray = (count($dataFinalArray)>0) ? array_reverse($dataFinalArray) : array();
    	//var_dump($dataFinalArray);
    	

    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Distribution';

    	$this->data['meterNameColumn'] = $column;
    	if(count($data) > 0){
    			
    		foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
    			
    			
    			
		        $dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue[$aa],3).'"
			        },';
			        $maxMin[] = $dateTimeValue[$aa];
		        
    		}
    	}
    	//var_dump($dataSet);
    	//die();

    	//////////////
    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	//////////////////////
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
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

    public function getMeterChartAllDay(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = '';
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$shift = $this->air_model->getShiftStart(1);
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);
    	$aa = $retSelectArr['aa'];
    	$column = $retSelectArr['column'];
    	$selected = $retSelectArr['selected'];
    	if($this->data['dayCount'] == 1){
    		$data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	}else{
    		$data = $this->air_model->getAirMeterDataDayWiseParam($meter_id, $selected, $startDate, $endDate);
    	}

    	$deltaGenVal = '';
    	$staticDeltaVal = '';

    	$data = (count($data)>0) ? array_reverse($data) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $column;
    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		        if($curVal != ''){
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';
			        $maxMin[] = $deltaGenVal - $value->{$aa};
		        	$consistencyArr[$value->end_date_time] = $deltaGenVal - $value->{$aa};
		        }else{
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($value->{$aa},3).'"
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
    		$this->data['min_val_con'] = intval(min($maxMinConsis)) - 1;
    	}
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartAllDayCon(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(9));
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();
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
    	}else{
    		$selected .= ',AMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	

    	$data = (count($data)>0) ? array_reverse($data) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $column;
    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		        if($curVal != ''){
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';
			        $maxMin[] = $deltaGenVal - $value->{$aa};
		        	$consistencyArr[$value->end_date_time] = $deltaGenVal - $value->{$aa};
		        }else{
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($value->{$aa},3).'"
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
    		$this->data['min_val_con'] = intval(min($maxMinConsis)) - 1;
    	}
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
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
						
						$consistencyArray[$key2] = number_format(((1 - $value2/$i) * 100),2);

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

	private function _createSelectQuery($type = 'press', $day = 1){
    	$retData = array();
    	if($day == 1){
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
	    	}else{
	    		$selected .= ',AMD.steam_enthalpy';
	    		$aa = 'steam_enthalpy';
	    		$column = 'Enthalpy';
	    	}
    	}else{
    		$selected = 'AM.name,DATE_FORMAT(AMD.end_date_time, "%Y-%m-%d") end_date_time';
	    	$aa = 'T_temp';
	  		$column = 'Temperature';
	    	if($type == 'temp'){
	    		$selected .= ',AVG(NULLIF(AMD.T_temp,0)) T_temp';
	    	}else if($type == 'pres'){
	    		$selected .= ',AVG(NULLIF(AMD.P_pressure,0)) P_pressure';
	    		$aa = 'P_pressure';
	    		$column = 'Pressure';
	    	}else if($type == 'flow'){
	    		$selected .= ',AVG(NULLIF(AMD.flow,0)) flow';
	    		$aa = 'flow';
	    		$column = 'Flow';
	    	}else{
	    		$selected .= ',AVG(NULLIF(AMD.steam_enthalpy,0)) steam_enthalpy';
	    		$aa = 'steam_enthalpy';
	    		$column = 'Enthalpy';
	    	}
    	}
    	$retData['selected'] = $selected;
    	$retData['aa'] = $aa;
    	$retData['column'] = $column;
    	return $retData;
    	
    }

    private function _getDateDiff($startDate = '', $endDate = '', $type = '%d'){
		if ($startDate != '' && $endDate != '') {
			$datetime1 = date_create($startDate);
		    $datetime2 = date_create($endDate);
		    $interval = date_diff($datetime1, $datetime2);
		    return $interval->format($type);
		}else{
			return null;
		}
		
	}
	
}