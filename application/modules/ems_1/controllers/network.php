<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Network extends MY_Controller {
	public $data = array();
	public $currentStartDateTime = '';
	public $currentEndDateTime = '';
	public $dataTimeInterval = '';

	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->load->model('network_model');

	}

	public function index(){
		$status = '';
        $msg = '';
		$this->data['pDeviceSelected'] = '';
		$this->data['dateRange'] = $this->_getDateDetails();
		$this->data['dateRange']['selectedDate'] =  $this->data['dateRange']['min_date'];
		$this->data['dateRange']['selectedDateEnd'] =  $this->data['dateRange']['min_date'];
		$this->data['deviceData'] = array();
		$this->data['viewParentData'] = array();
		$this->data['viewTinData'] = array();
		$this->data['viewToutData'] = array();
		$this->data['transDetails']['parent'] = array();
		$this->data['transDetails']['in'] = array();
		$this->data['transDetails']['out'] = array();
		$this->load->library('form_validation');
		if($this->input->post('btnSearch')){
			$this->form_validation->set_rules('p_device_id','Location','trim|required');
            $this->form_validation->set_rules('startDate','Start date','trim|required');
            if($this->form_validation->run() === TRUE){
				$this->data['pDeviceSelected'] = $this->input->post('p_device_id');
				$this->startDateTime = $this->data['dateRange']['selectedDate'] =  date('Y-m-d',strtotime($this->input->post('startDate')));
				$this->data['dateRange']['selectedDateEnd'] =  date('Y-m-d',strtotime($this->input->post('endDate')));
				$this->dataTimeInterval = 'PT00H00M00S';
				$startDate = $this->startDateTime = $this->_dateAdd();
				$this->startDateTime = $this->data['dateRange']['selectedDateEnd'];
				$this->dataTimeInterval = 'PT24H00M00S';
				$endDate = $this->_dateAdd();

				$this->data['tempTransDetails'] = $this->network_model->getTransformerDetails($this->data['pDeviceSelected']);
				$this->data['transDetails'] = $this->_makingTransDetails();
				$this->data['deviceParentData'] = $this->network_model->getDeviceWiseTransferParentData($this->data['pDeviceSelected'], $startDate, $endDate);
				$this->data['viewParentData'] = $this->_makingParentData();
				unset($this->data['deviceParentData']);
				$this->data['deviceTinData'] = $this->network_model->getDeviceWiseTransferInData($this->data['pDeviceSelected'], $startDate, $endDate);
				//$this->p($this->data['deviceTinData']);
				$this->data['viewTinData'] = $this->_makingTinData();
				$this->data['deviceToutData'] = $this->network_model->getDeviceWiseTransferOutData($this->data['pDeviceSelected'], $startDate, $endDate);
				//$this->p($this->data['deviceToutData']);
				$this->data['viewToutData'] = $this->_makingToutData();
			}else{
				$status = 'danger';
                $msg = validation_errors();
			}
		}
		$this->data['msg'] = $msg;
		$this->data['pDevice'] = $this->network_model->getParentDeviceList();
		$this->data['mainPage'] = $this->load->view('network/mainPage', $this->data, true);
		$this->load->view('network/index', $this->data);
	}

	private function _makingParentData(){
		$retData = array();
		if(count($this->data['deviceParentData']) > 0){
			$parentData = array();
			foreach ($this->data['deviceParentData'] as $key => $value) {
				$parentData['name'] = $value->device_name;
				$parentData['device_id'] = $value->device_id;
				//$parentData['capacity'] = $value->t_in_device_capacity;
				if( $value->data > 0){
					$parentData[$value->short_name][] = $value->data;
				}
			}
			if(count($parentData) > 0){
				foreach($parentData as $key => $val){
					if($key == 'name' || $key == 'device_id' ){
						$retData[$key] = $val;
					}else{
						if(count($value) > 0){
							$retData[$key] = array_sum($val)/count($val);
						}else{
							$retData[$key] = 0;
						}
					}
				}
			}
		}

		return $retData;
	}

	private function _makingTinData(){
		$retData = array();
		if(count($this->data['deviceTinData']) > 0){
			$allData = array();
			foreach ($this->data['deviceTinData'] as $key => $value) {
				$allData[$value->device_id]['name'] = $value->device_name;
				$allData[$value->device_id]['capacity'] = $value->t_in_device_capacity;
				if($value->data > 0){
					$allData[$value->device_id][$value->short_name][] = $value->data;
				}
			}

			if(count($allData) > 0){
				foreach ($allData as $key => $value) {
					$retData[$key]['name'] = $value['name'];
					$retData[$key]['capacity'] = $value['capacity'];
					if(isset($value['KW'])){
						if(count($value['KW']) > 0){
							$retData[$key]['KWPF'] = $this->_getKWPF($value['KW'] ,$value['PF']);
							$retData[$key]['KW'] = array_sum($value['KW'])/count($value['KW']);
						}else{
							$retData[$key]['KWPF'] = 0;
							$retData[$key]['KW'] = 0;
						}
					}else{
						$retData[$key]['KWPF'] = 0;
						$retData[$key]['KW'] = 0;
					}
				}
			}
			//$this->p($retData);
		}
		//$this->p($retData);
		return $retData;
	}

	private function _getKWPF($kw = array(), $pf = array()){
		$retData = 0;
		if(count($kw) > 0){
			$sumKw = 0;
			$sumKwPf = 0;
			foreach ($kw as $key => $value) {
				if(isset($pf[$key]) && $pf[$key] > 0){
					$aaa[$key] = $value / $pf[$key];
					$sumKwPf = $sumKwPf + $aaa[$key];
				}		
				$sumKw = $sumKw + $value;	
			}
			if($sumKw > 0){
				$retData = $sumKw/$sumKwPf;
			}
			

		}
		return $retData;
	}

	private function _makingToutData(){
		$retData = array();
		if(count($this->data['deviceToutData']) > 0){
			$allData = array();
			foreach ($this->data['deviceToutData'] as $key => $value) {
				$allData[$value->device_id]['name'] = $value->device_name;
				$allData[$value->device_id]['capacity'] = $value->t_in_device_capacity;
				if($value->data > 0){
					$allData[$value->device_id][$value->short_name][] = $value->data;
				}
			}

			if(count($allData) > 0){
				foreach ($allData as $key => $value) {
					$retData[$key]['name'] = $value['name'];
					$retData[$key]['capacity'] = $value['capacity'];
					if(count($value['KW']) > 0){
						$retData[$key]['KW'] = array_sum($value['KW'])/count($value['KW']);
					}else{
						$retData[$key]['KW'] = 0;
					}
				}
			}
			//$this->p($retData);
		}
		return $retData;
	}

	private function _getDateDetails(){
		$meter_id = 0;
		$data = $this->network_model->getDateDetails($meter_id);
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

    private function _makingTransDetails(){
    	$retData = array();
    	if(count($this->data['tempTransDetails'])){
    		foreach ($this->data['tempTransDetails'] as $key => $value) {

    			if($value->p_device_id != ''){
    				$retData['parent'] = array(
											'id' => $value->p_device_id,
											'name' => $value->p_device_name,
										);
    			}
    			if($value->t_in_device_id != ''){
    				$retData['in'][] = array(
											'id' => $value->t_in_device_id,
											'name' => $value->t_in_device_name,
											'capacity' => $value->t_in_device_capacity,
										);
    			}

    			if($value->t_out_device_id != ''){
    				$retData['out'][] = array(
											'id' => $value->t_out_device_id,
											'name' => $value->t_out_device_name,
										);
    			}

    			if($value->t_in_device_id != '' && $value->t_out_device_id != ''){
    				$retData['inout'][] = array(
											't_in_device_id' => $value->t_in_device_id,
											't_out_device_id' => $value->t_out_device_id,
										);
    			}


    		}
    	}
    	//$this->p($retData);
    	return $retData;
    }



    public function p($data = array()){
    	echo '<pre>';
    	print_r($data);
    	//exit;
    }

}

