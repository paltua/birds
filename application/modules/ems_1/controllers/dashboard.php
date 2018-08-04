<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	public $data = array();
	public $currentStartDateTime = '';
	public $currentEndDateTime = '';
	public $dataTimeInterval = '';

	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->shiftArr = array(1 => '0',2 => '32',3 => '64');
		$this->load->model('ems_model');
		$this->_setCurrentDateTime();
		

	}

	public function index(){
		
		$this->data['meterWiseData'] = $this->ems_model->getDeviceWiseData($this->currentStartDateTime);
		$this->data['typeWise'] = $this->_makingData();
		$this->data['meterWiseDataCpp'] = $this->ems_model->getDeviceWiseDataCpp($this->currentStartDateTime);
		$this->data['typeWiseCpp'] = $this->_makingDataCpp();
		
		$emsCppArr = '5,1,2,3,4,6';
		$emsArr = '2,3,30,31,34,4';

		$this->data['emsCppDataLoss'] = $this->ems_model->getEmsCppLoss($this->currentStartDateTime, $emsCppArr);
		//print_r($this->data['emsCppDataLoss']);
		$this->data['emsDataLoss'] = $this->ems_model->getEmsLoss($this->currentStartDateTime, $emsArr);

		$this->data['startDateShow'] = $this->currentStartDateTime;
		$this->data['endDateShow'] = $this->currentEndDateTime;
		unset($this->data['meterWiseData']);
		unset($this->data['meterWiseDataCpp']);
		$this->data['all'] = $this->load->view('dashboard/all', $this->data, true);
		unset($this->data['emsCppDataLoss']);
		unset($this->data['emsDataLoss']);
		//print_r($this->data['meterWiseData']);
		$this->load->view('dashboard/index', $this->data);
	}

	private function _makingData(){
		$masterArr = array();
		if(count($this->data['meterWiseData']) > 0){
			foreach ($this->data['meterWiseData'] as $value) {
				if($value->data != 0){
					$masterArr[$value->type][$value->short_name][] = $value->data;
				}
			}
		}
		
		$viewArr = array();
		if(count($masterArr) > 0){
			foreach ($masterArr as $key1 => $value1) {
				if(count($value1) > 0){
					foreach ($value1 as $key2 => $value2) {
						$viewArr[$key1][$key2] = $this->_calculationTagWise($key2,$value2);
					}
				}
			}
		}
		
		return $viewArr;
	}

	private function _makingDataCpp(){
		$masterArr = array();
		$nameArr = array();
		if(count($this->data['meterWiseDataCpp']) > 0){
			foreach ($this->data['meterWiseDataCpp'] as $value) {
				$nameArr[$value->device_id] = $value->device_name;
				$masterArr[$value->device_id][$value->short_name] = array();
				if($value->data != 0){
					$masterArr[$value->device_id][$value->short_name][] = $value->data;
				}
			}
		}

		$viewArr = array();
		if(count($masterArr) > 0){
			foreach ($masterArr as $key1 => $value1) {
				if(count($value1) > 0){
					foreach ($value1 as $key2 => $value2) {
						$viewArr[$key1][$key2] = $this->_calculationTagWise($key2,$value2);
						$viewArr[$key1]['name'] = $nameArr[$key1];
					}
				}
			}
		}
		
		return $viewArr;
	}

	private function _calculationTagWise($key2 = '', $value2 = array()){
		$retData = 0;
		if($key2 == 'KW'){
			$retData = array_sum($value2);
		}elseif($key2 == 'Amps'){
			$retData = array_sum($value2);
		}else{
			if(count($value2) > 0){
				$retData = array_sum($value2)/count($value2);
			}else{
				$retData = 0;
			}
			
		}
		
		return $retData;
	}

	public function live(){
		$typeId = $this->data['typeId'] = $this->uri->segment(4);
		$this->data['meterWiseData'] = $this->ems_model->getDeviceTypeWiseData($this->currentStartDateTime, $typeId);
		$this->data['typeWise'] = $this->_makingTypeData();
		$this->data['startDateShow'] = $this->currentStartDateTime;
		$this->data['endDateShow'] = $this->currentEndDateTime;
		unset($this->data['meterWiseData']);
		if($this->data['typeId'] > 0 && $this->data['typeId'] < 4){
			$this->data['all'] = $this->load->view('dashboard/live_all',$this->data, true);
		}else{
			$this->data['all'] = '';
		}
		//print_r($this->data['meterWiseData']);
		$this->load->view('dashboard/live', $this->data);
	}

	private function _makingTypeData(){
		$masterArr = array();
		if(count($this->data['meterWiseData']) > 0){
			foreach ($this->data['meterWiseData'] as $value) {
				$masterArr[$value->type][$value->device_id]['name'] = $value->device_name;
				$masterArr[$value->type][$value->device_id]['data'][$value->short_name][] = $value->data;
			}
		}
		/*echo "<pre>";
		print_r($this->data['meterWiseData']);
		exit;*/
		return $masterArr;
	}

	private function _setCurrentDateTime(){
		$this->dataTimeInterval = 'PT00H15M00S';
		$currentDate = $this->ems_model->getCurrent();
		$this->currentStartDateTime = $this->startDateTime = $currentDate[0]->end_date_time;
		$this->currentEndDateTime = $this->_dateAdd();
	}

	public function showGraph(){
		$this->data['device_id'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$data = $this->ems_model->getCurrent32DataSet($this->data['device_id'], $this->data['paramVal']);
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->device_name;
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($value->data,3).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	$this->data['dataSetConsis'] = '';
		$this->load->view('dashboard/chart', $this->data);
	}

	public function liveRefresh(){
		$typeId = $this->data['typeId'] = $this->uri->segment(4);
		$this->data['meterWiseData'] = $this->ems_model->getDeviceTypeWiseData($this->currentStartDateTime, $typeId);
		$this->data['typeWise'] = $this->_makingTypeData();
		$this->data['startDateShow'] = $this->currentStartDateTime;
		$this->data['endDateShow'] = $this->currentEndDateTime;
		unset($this->data['meterWiseData']);
		$retData['all'] = $this->load->view('dashboard/live_all',$this->data, true);
		$retData['status'] = 'success'; 
		echo json_encode($retData);
	}


    private function _dateAdd(){
        $date = new DateTime($this->startDateTime);
        $date->add(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

	
}