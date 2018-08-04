<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportsteam extends MY_Controller {
	public $data = array();
	public $shiftArr = array();
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->shiftArr = array(1,2,3);
		$this->load->model('fm_model');

	}

	public function index(){
		$this->data = array();
		$this->data['all'] = ''; 
		$this->data['dateRange'] = $this->_getDateDetails();
		$this->data['dateRange']['selectedDate'] =  $this->data['dateRange']['min_date'];
		$this->data['get_meter'] =  $this->fm_model->getMeter();
		$startDate = '';
		if(isset($_POST['startDate']) && isset($_POST['endDate'])){

			$this->startDateTime = date('Y-m-d',strtotime($_POST['startDate']));
			$this->endDateTime = date('Y-m-d',strtotime($_POST['endDate']));//'2017-09-25';		

			$shift = $this->fm_model->getShiftStart(1);
			$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
			$this->endDateTime = $this->_dateAddonEnddate();

			$this->dataTimeInterval = 'PT24H00M00S';
			$endDate = $this->_dateAdd();
			$finalEndDate = $this->endDateTime = $this->_dateAddonEnddate();

			/// Get Date Wise Data Aggregator Data
			$dataArray = array();
			for ( $i = strtotime($this->startDateTime); $i <= strtotime($this->endDateTime); $i = $i + 86400 ) {
			  $thisDate = date( 'Y-m-d H:i:s', $i ); 
			  
			  	$sDate = $thisDate;
				$eDate = $this->_dateAddonDate($sDate,'PT24H00M00S');				

				if(strtotime($eDate)<=strtotime($this->endDateTime)){
			  		
					$this->data['last15DataSet'] = $this->fm_model->getLastDateDataTypeWise($sDate ,$eDate);
					//$this->data['typeWise'] = $this->_getTotalTypeWise();
					//$this->data['typeWise'] = $this->_getTotalTypeWise1();
					
					$dataArray[date('Y-m-d',strtotime($sDate))] = $this->_getTotalTypeWise();
					

			  	}			  

			}
			
			// Iterate Data Structure Depends on Excel Format Data Structure
			$newDataArray = $this->_regenarateArrayForExcel($dataArray);
		
			// Generate Excel Sheet Name And Call The Excel Generator Library
			$fileName = 'fm_'.date('Y-m-d');
			$this->load->library('excel');
			$this->excel->generateAndDownload($fileName,$newDataArray);

			//$this->data['data'] = $resultArray;

			//$this->data['all'] = $this->load->view('reportsteam/all_day',$this->data, true);
		}
		
		$this->load->view('reportsteam/index', $this->data);
	}

	private function _regenarateArrayForExcel($dataArray){

		$newStrucArray = array();
		if(count($dataArray)>0){
			$i=1;

			foreach ($dataArray as $datekey => $datevalue) {
				//var_dump($i);
				if(!isset($newStrucArray[0])) $newStrucArray[0][] = 'Date';
				if(!in_array('Date', $newStrucArray[0])){
					$newStrucArray[0][] = 'Date';
				}
				
				$newStrucArray[$i][] = $datekey;	
				
				$diffGenAcons = 0;
				$totalTypeWiseFlow = array();
				foreach ($datevalue as $typekey => $typevalue) {
					//if($typekey=="main") continue;
					if(!isset($totalTypeWiseFlow[$typekey])) $totalTypeWiseFlow[$typekey] = 0;
					foreach ($typevalue['meter'] as $meterkey => $metervalue) {
						if(isset($metervalue['name'])){
							
							if(!in_array($metervalue['name'], $newStrucArray[0])){
								$newStrucArray[0][] = $metervalue['name'];
							}
							//$newStrucArray[$i][] = isset($metervalue['TTL_flow']) ? number_format(($metervalue['TTL_flow']),3) : '--';
							$newStrucArray[$i][] = isset($metervalue['flow']) ? number_format(($metervalue['flow']),3) : '--';	
							$totalTypeWiseFlow[$typekey]+=	isset($metervalue['flow']) ? ($metervalue['flow'] * 24) : '0';		
						}
													
					}
					$totalGen[$typekey] = 0;
				
					if($typekey=="gen"){

						if(!in_array('Total Generation', $newStrucArray[0])){
							$newStrucArray[0][] = 'Total Generation';
						}
						//$totalGen[$typekey] = isset($datevalue['gen']['total']['TTL_flow']) ? ($datevalue['gen']['total']['TTL_flow']) : 0;
						$totalGen[$typekey] = isset($totalTypeWiseFlow['gen']) ? ($totalTypeWiseFlow['gen']) : 0;
						
						$newStrucArray[$i][] = number_format($totalGen[$typekey],3);
					}
					//var_dump($totalGen);
					$totalConsumption[$typekey] = 0;
					if($typekey=="dist"){

						if(!in_array('Total Consumtion', $newStrucArray[0])){
							$newStrucArray[0][] = 'Total Consumtion';
						}
						//$totalConsumption[$typekey] = isset($datevalue['dist']['total']['TTL_flow']) ? ($datevalue['dist']['total']['TTL_flow']) : 0;
						
						$totalConsumption[$typekey] = isset($totalTypeWiseFlow['dist']) ? ($totalTypeWiseFlow['dist']) : 0;
						$newStrucArray[$i][] = number_format($totalConsumption[$typekey],3);

					}
					//var_dump($totalConsumption);				
					
				}
				
				if(!in_array('Difference on Generation and Consumption', $newStrucArray[0])){
					$newStrucArray[0][] = 'Difference on Generation and Consumption';
				}
				if(count($datevalue)>0){
					//var_dump($totalGen);
					//var_dump($totalConsumption);
					$diffGenAcons = ($totalGen['gen']-$totalConsumption['dist']);
					$newStrucArray[$i][] = number_format($diffGenAcons,3);
				}
				
			$i++;}
		}

		return $newStrucArray;
	}

	

	public function shift(){
		$this->data = array();
		$this->data['all'] = null; 
		$this->data['shiftVal'] = 8;
		$this->data['dateRange'] = $this->_getDateDetails();
		$this->data['dateRange']['selectedDate'] =  $this->data['dateRange']['min_date'];
		$startDate = '';
		if(isset($_POST['startDate']) && isset($_POST['sifthour'])){
			$shiftHours = trim($_POST['sifthour']);
			$this->startDateTime = date('Y-m-d',strtotime($_POST['startDate']));
			if($shiftHours == 8){
				$shift = $this->fm_model->getShiftStart(1);
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
			$this->data['last15DataSet'] = $this->fm_model->getLastDateDataTypeWise($startDate ,$endDate);
			$this->data['typeWise'] = $this->_getTotalTypeWise();

			//$this->data['dateRange'] = $this->_getDateDetails();
			$this->data['dateRange']['selectedDate'] =  date('Y-m-d',strtotime($_POST['startDate']));
			$this->data['shiftVal'] = strtotime($startDate);
			$this->data['all'] = $this->load->view('shift/all',$this->data, true);
		}
		//die();
		
		$this->load->view('shift/index', $this->data);
	}

	private function _getTotalTypeWise1(){
		
		$data = array();
		if(count($this->data['last15DataSet'])){			
			
			foreach ($this->data['last15DataSet'] as $key => $value) {
				
				$data[date('m/d/Y H:i:s',strtotime($value->end_date_time))][$value->type]['meter'][$value->meter_id]['name'] = $value->name;

				if(isset($value->P_pressure) && $value->P_pressure>0){
					$data[date('m/d/Y H:i:s',strtotime($value->end_date_time))][$value->type]['meter'][$value->meter_id]['P_pressure'] = $value->P_pressure;
				}
				if(isset($value->flow) && $value->flow>0){
					$data[date('m/d/Y H:i:s',strtotime($value->end_date_time))][$value->type]['meter'][$value->meter_id]['flow'] = $value->flow;
				}
				if(isset($value->T_temp) && $value->T_temp>0){
					$data[date('m/d/Y H:i:s',strtotime($value->end_date_time))][$value->type]['meter'][$value->meter_id]['T_temp'] = $value->T_temp;
				}
			}
		}
		return $data;
	}

	private function _getTotalTypeWise(){
		$retData = array();
		if(count($this->data['last15DataSet'])){
			$data = array();
			$typeWiseArr = array();
			foreach ($this->data['last15DataSet'] as $key => $value) {
				
				$data[$value->type]['meter'][$value->meter_id]['name'] = $value->name;

				$data[$value->type]['meter'][$value->meter_id]['P_pressure'][] = $value->P_pressure;

				$data[$value->type]['meter'][$value->meter_id]['flow'][] = $value->flow;

				$data[$value->type]['meter'][$value->meter_id]['T_temp'][] = $value->T_temp;

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
			

			if(count($data) > 0){
				foreach ($data as $key1 => $value1) {
					foreach($value1['meter'] as $key2 => $val2){
						$retData[$key1]['meter'][$key2]['name'] = $val2['name'];
						$retData[$key1]['meter'][$key2]['benchmark_delta_pressure'] = $val2['benchmark_delta_pressure'];
						$retData[$key1]['meter'][$key2]['benchmark_delta_temp'] = $val2['benchmark_delta_temp'];

						$retData[$key1]['meter'][$key2]['flow'] = array_sum($val2['flow'])/count($val2['flow']);

						$retData[$key1]['meter'][$key2]['P_pressure'] = array_sum($val2['P_pressure'])/count($val2['P_pressure']);
						
						$retData[$key1]['meter'][$key2]['T_temp'] = array_sum($val2['T_temp'])/count($val2['T_temp']);						

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

				
				/*
				$tempArr = array();
				foreach ($typeWiseArr as $key3 => $value3) {
					foreach ($value3 as $key4 => $value4) {
						
						$tempArr[$key3]['flow'][$key4] = array_sum($value4['flow']);

						$tempArr[$key3]['flow_for_sum'][$key4] = array_sum($value4['flow']);

						$tempArr[$key3]['P_pressure'][$key4] = array_sum($value4['P_pressure'])/count($value4['P_pressure']);
						//$tempArr[$key3]['P_pressure'][$key4] = array_sum($value4['P_pressure'])/$tempArr[$key3]['flow'][$key4];

						
						
						$tempArr[$key3]['T_temp'][$key4] = array_sum($value4['T_temp'])/count($value4['T_temp']);
						//$tempArr[$key3]['T_temp'][$key4] = array_sum($value4['T_temp'])/$tempArr[$key3]['flow'][$key4];

						$tempArr[$key3]['TTL_flow'][$key4] = array_sum($value4['TTL_flow']);
						

						if($tempArr[$key3]['flow_for_sum'][$key4] > 0){
							$tempArr[$key3]['steam_enthalpy'][$key4] = array_sum($value4['steam_enthalpy_pro'])/$tempArr[$key3]['flow_for_sum'][$key4];
						}else{
							$tempArr[$key3]['steam_enthalpy'][$key4] = 0;
						}

						$tempArr[$key3]['steam_enthalpy_prod'][$key4] = $tempArr[$key3]['steam_enthalpy'][$key4] * $tempArr[$key3]['flow_for_sum'][$key4];
						$tempArr[$key3]['steam_heat_content'][$key4] = array_sum($value4['steam_heat_content']);

					}					
				}
				//echo "<pre>";
				//print_r($tempArr);
				if(count($tempArr) > 0){


					foreach ($tempArr as $key5 => $value5) {

						//$retData[$key5]['total']['flow'] = array_sum($value5['flow']);
						$retData[$key5]['total']['flow'] = array_sum($value5['flow']);

						//$retData[$key5]['total']['P_pressure'] = array_sum($value5['P_pressure'])/count($value5['P_pressure']);
						$retData[$key5]['total']['P_pressure'] = array_sum($value5['P_pressure'])/$retData[$key5]['total']['flow'];
						
						//$retData[$key5]['total']['T_temp'] = array_sum($value5['T_temp'])/count($value5['T_temp']);
						$retData[$key5]['total']['T_temp'] = array_sum($value5['T_temp'])/$retData[$key5]['total']['flow'];

						$retData[$key5]['total']['TTL_flow'] = array_sum($value5['TTL_flow']);

						if($retData[$key5]['total']['flow'] > 0){
							$retData[$key5]['total']['steam_enthalpy'] = array_sum($value5['steam_enthalpy_prod'])/$retData[$key5]['total']['flow'];
						}else{
							$retData[$key5]['total']['steam_enthalpy'] = 0;
						}
						$retData[$key5]['total']['steam_heat_content'] = array_sum($value5['steam_heat_content']);
					}
				}*/
			}

			//echo "<pre>";print_r($retData);exit;
		}
		return $retData;
	}

	private function _getDateDetails(){
		
		$meter_id = 0;
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

    private function _dateAddonEnddate(){
        $date = new DateTime($this->endDateTime);
        $date->add(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

    private function _dateAddonDate($date,$interVal){
        $date = new DateTime($date);
        $date->add(new DateInterval($interVal));
        return $date->format('Y-m-d H:i:s');
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

    	$data = $this->fm_model->getSteamMeterDataDay($meter_id, $selected);
    	
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
    	$selected = 'SM.name,SMD.end_date_time';
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

    	$data = $this->fm_model->getSteamMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);
    	
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

    public function getMeterChartAllDay(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = '';
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

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

    	$data = $this->fm_model->getSteamMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	
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
    	$shiftDateStrToTime = trim($this->uri->segment(9));

    	$this->startDateTime = date('Y-m-d', $this->uri->segment(9));
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	
    	$selected = 'SM.name,SMD.end_date_time';
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

    	$data = $this->fm_model->getSteamMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	
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
	
}