<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends MY_Controller {
	public $data = array();
	public $section ;
	public $formHeader ;
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->load->model('data_model');
		$this->data['msg'] = '';
	}

	public function index(){
		$where = array();
		$this->data['search'] = false;
		$this->data['meter_id'] = 0;
		$this->data['working_shift'] = 0;
		$this->data['min_val'] = '0';
		$this->data['max_val'] = '0';
		if($this->input->post('working_shift') > 0){
			$this->data['search'] = true;
			$this->data['meter_id'] = trim($this->input->post('meter_id'));
			$this->data['working_shift'] = trim($this->input->post('working_shift'));
			$this->data['dateRange'] = $this->_getDateDetails();
		}
		$this->data['last15DataSet'] = $this->air_model->getLast15DataTypeWise();
		$this->data['startDateShow'] = $this->startDateTime = isset($this->data['last15DataSet'][0]->end_date_time) ? $this->data['last15DataSet'][0]->end_date_time : '0000-00-00 00:00:00';
		$this->dataTimeInterval = 'PT00H15M00S';;
		$this->data['endDateShow'] = $this->_dateAdd();
		$this->data['typeWise'] = $this->_getTotalTypeWise();
		$this->data['all'] = $this->load->view('dashboard/all',$this->data, true);
		$where = array();
		$this->data['tags'] = $this->tbl_generic_model->get('master_tag', '*', $where);
		$this->load->view('dashboard/index', $this->data);
	}

	public function ems(){
		$this->section = 'ems';
		$this->formHeader = 'EMS';
		$table = '';
		$this->data['column'] = $this->data_model->getEmsColumn($this->section);
		$this->data['pDevice'] = $this->data_model->getEmsDeviceList($this->section);
		$this->data['pDeviceSelected'] = 0;
		$this->data['colSelected'] = array();
		$this->_getEmsColumnArr();
		$this->data['startDateShow'] = '';
		$this->data['endDateShow'] = '';
		$this->data['currentDate'] = date('Y-m-d');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('p_device_id', 'Device Name', 'required|trim');
		$this->form_validation->set_rules('startDate', 'Start Date', 'required');
		$this->form_validation->set_rules('endDate', 'End Date', 'required');
		if($this->form_validation->run() == TRUE){
			$this->_setFormData();
			$this->_setSheetTitle();
			$this->_setEmsHeaderMergeCellRowOne();
			$this->_setEmsDeviceData();
			$this->data['fileName'] = $this->section.'_data_'.date('Y-m-d',strtotime($this->data['startDateShow'])).'_'.date('Y-m-d',strtotime($this->data['endDateShow'])).'.xlsx';
			$this->_generateAndDownload();
		}else{
			$this->_setFormData();
		}
		
		$this->load->view('download/ems', $this->data);
	}

	private function _getEmsColumnArr(){
		foreach ($this->data['column'] as $key => $value) {
			$this->data['colSelected'][] = $value->Field;
		}
	}

	private function _setDeviceIdArr(){
		$ids = array();
		if(is_array($this->data['pDeviceSelected'])){
			$ids = $this->data['pDeviceSelected'];
		}else{
			$ids[] = $this->data['pDeviceSelected'];
		}
		return $ids;
	}

	private function _setSheetTitle(){
		$this->data['ids'] = array();
		$ids = $this->_setDeviceIdArr();
		$data = $this->data_model->getEmsDeviceName($ids,$this->section);
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$this->data['titles'][] = $value->device_name;
				$this->data['ids'][] = $value->device_id;
			}
		}
	}

	private function _setEmsHeaderMergeCellRowOne(){
		$color = array('91def1');
		$merge = array(1);
		$start = "A";
		$m = 1;
		foreach ($this->data['colSelected'] as $key => $value) {
			$cellTitle = getEmsData($value);
			$end = $this->_getExcelCellChar($start, $merge[0] -1);
			$this->data['sheetData']['header'][] = array($start.'1:'.$end.'1', $cellTitle, $m, $color[0], $value);
			$start = $this->_getExcelCellChar($start, $merge[0]);
			$m = $m + $merge[0];
		}
		//pr($this->data['header']);
	}

	private function _setEmsDeviceData(){
		$ids = $this->_setDeviceIdArr();
		$data = $this->data_model->getEmsDeviceData($this->data['startDateShow'], $this->data['endDateShow'], $ids, $this->section);
		$deviceWiseData = array();
		$this->data['sheetData']['data'][0] = array();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$deviceDateData = array();
				foreach ($value as $keyD => $valueD) {
					if($this->section == 'emsCpp' && ($keyD == 'data_KW' || $keyD == 'data_Volt')){
						$deviceDateData[$keyD] = $valueD * 1000;
					}else{
						$deviceDateData[$keyD] = $valueD;
					}
					
				}
				$deviceWiseData[$value->device_id][] = $deviceDateData;
			}
		}
		if(count($deviceWiseData) > 0){
			foreach ($this->data['ids'] as $key => $value) {
				//$this->data['sheetData']['data'][$key][$value] = isset($deviceWiseData[$value])?$deviceWiseData[$value]:array();
				$this->data['sheetData']['data'][$key] = isset($deviceWiseData[$value])?$deviceWiseData[$value]:array();
			}
		}
	}

	public function emsCpp(){
		$this->section = 'emsCpp';
		$this->formHeader = 'EMS CPP';
		$table = '';
		$this->data['column'] = $this->data_model->getEmsColumn($this->section);
		$this->data['pDevice'] = $this->data_model->getEmsDeviceList($this->section);
		$this->data['pDeviceSelected'] = 0;
		$this->data['colSelected'] = array();
		$this->_getEmsColumnArr();
		$this->data['startDateShow'] = '';
		$this->data['endDateShow'] = '';
		$this->data['currentDate'] = date('Y-m-d');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('p_device_id', 'Device Name', 'required|trim');
		$this->form_validation->set_rules('startDate', 'Start Date', 'required');
		$this->form_validation->set_rules('endDate', 'End Date', 'required');
		if($this->form_validation->run() == TRUE){
			$this->_setFormData();
			$this->_setSheetTitle();
			$this->_setEmsHeaderMergeCellRowOne();
			$this->_setEmsDeviceData();
			$this->data['fileName'] = $this->section.'_data_'.date('Y-m-d',strtotime($this->data['startDateShow'])).'_'.date('Y-m-d',strtotime($this->data['endDateShow'])).'.xlsx';
			$this->_generateAndDownload();
		}else{
			$this->_setFormData();
		}
		$this->load->view('download/emsCpp', $this->data);
	}

	public function air(){
		$this->section = 'air';
		$this->formHeader = 'AIR';
		$table = '';
		$this->data['column'] = $this->data_model->getAirColumn($this->section);
		$this->data['pDevice'] = $this->data_model->getAirDeviceList($this->section);
		$this->data['pDeviceSelected'] = 0;
		$this->data['colSelected'] = array();
		$this->_getEmsColumnArr();
		$this->data['startDateShow'] = '';
		$this->data['endDateShow'] = '';
		$this->data['currentDate'] = date('Y-m-d');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('p_device_id', 'Device Name', 'required|trim');
		$this->form_validation->set_rules('startDate', 'Start Date', 'required');
		$this->form_validation->set_rules('endDate', 'End Date', 'required');
		if($this->form_validation->run() == TRUE){
			$this->_setFormData();
			$this->_setAirSheetTitle();
			$this->_setEmsHeaderMergeCellRowOne();
			$this->_setAirDeviceData();
			$this->data['fileName'] = $this->section.'_data_'.date('Y-m-d',strtotime($this->data['startDateShow'])).'_'.date('Y-m-d',strtotime($this->data['endDateShow'])).'.xlsx';
			$this->_generateAndDownload();
		}else{
			$this->_setFormData();
		}
		$this->load->view('download/air', $this->data);
	}

	private function _setAirSheetTitle(){
		$this->data['ids'] = array();
		$ids = $this->_setDeviceIdArr();
		$data = $this->data_model->getAirDeviceName($ids,$this->section);
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$this->data['titles'][] = $value->name;
				$this->data['ids'][] = $value->meter_id;
			}
		}
	}

	private function _setAirDeviceData(){
		$ids = $this->_setDeviceIdArr();
		$data = $this->data_model->getAirDeviceData($this->data['startDateShow'], $this->data['endDateShow'], $ids, $this->section);
		$deviceWiseData = array();
		$this->data['sheetData']['data'][0] = array();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$deviceDateData = array();
				foreach ($value as $keyD => $valueD) {
					$deviceDateData[$keyD] = $valueD;
				}
				$deviceWiseData[$value->meter_id][] = $deviceDateData;
			}
		}
		if(count($deviceWiseData) > 0){
			foreach ($this->data['ids'] as $key => $value) {
				//$this->data['sheetData']['data'][$key][$value] = isset($deviceWiseData[$value])?$deviceWiseData[$value]:array();
				$this->data['sheetData']['data'][$key] = isset($deviceWiseData[$value])?$deviceWiseData[$value]:array();
			}
		}
	}

	public function steam(){
		$this->section = 'steam';
		$this->formHeader = 'STEAM';
		$table = '';
		$this->data['column'] = $this->data_model->getSteamColumn($this->section);
		$this->data['pDevice'] = $this->data_model->getSteamDeviceList($this->section);
		$this->data['pDeviceSelected'] = 0;
		$this->data['colSelected'] = array();
		$this->_getEmsColumnArr();
		$this->data['startDateShow'] = '';
		$this->data['endDateShow'] = '';
		$this->data['currentDate'] = date('Y-m-d');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('p_device_id', 'Device Name', 'required|trim');
		$this->form_validation->set_rules('startDate', 'Start Date', 'required');
		$this->form_validation->set_rules('endDate', 'End Date', 'required');
		if($this->form_validation->run() == TRUE){
			$this->_setFormData();
			$this->_setSteamSheetTitle();
			$this->_setEmsHeaderMergeCellRowOne();
			$this->_setSteamDeviceData();
			$this->data['fileName'] = $this->section.'_data_'.date('Y-m-d',strtotime($this->data['startDateShow'])).'_'.date('Y-m-d',strtotime($this->data['endDateShow'])).'.xlsx';
			$this->_generateAndDownload();
		}else{
			$this->_setFormData();
		}
		$this->load->view('download/steam', $this->data);
	}

	private function _setSteamSheetTitle(){
		$this->data['ids'] = array();
		$ids = $this->_setDeviceIdArr();
		$data = $this->data_model->getSteamDeviceName($ids,$this->section);
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$this->data['titles'][] = $value->name;
				$this->data['ids'][] = $value->meter_id;
			}
		}
	}

	private function _setSteamDeviceData(){
		$ids = $this->_setDeviceIdArr();
		$data = $this->data_model->getSteamDeviceData($this->data['startDateShow'], $this->data['endDateShow'], $ids, $this->section);
		$deviceWiseData = array();
		$this->data['sheetData']['data'][0] = array();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$deviceDateData = array();
				foreach ($value as $keyD => $valueD) {
					$deviceDateData[$keyD] = $valueD;
				}
				$deviceWiseData[$value->meter_id][] = $deviceDateData;
			}
		}
		if(count($deviceWiseData) > 0){
			foreach ($this->data['ids'] as $key => $value) {
				//$this->data['sheetData']['data'][$key][$value] = isset($deviceWiseData[$value])?$deviceWiseData[$value]:array();
				$this->data['sheetData']['data'][$key] = isset($deviceWiseData[$value])?$deviceWiseData[$value]:array();
			}
		}
	}

	public function weaving(){
		$this->section = 'weaving';
		$this->formHeader = 'WEAVING';
		$table = '';
		$this->data['column'] = $this->data_model->getWeavingColumn($this->section);
		$this->data['pDevice'] = $this->data_model->getWeavingDeviceList($this->section);
		$this->data['pDeviceSelected'] = 0;
		$this->data['colSelected'] = array();
		$this->_getEmsColumnArr();
		$this->data['startDateShow'] = '';
		$this->data['endDateShow'] = '';
		$this->data['currentDate'] = date('Y-m-d');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('p_device_id', 'Device Name', 'required|trim');
		$this->form_validation->set_rules('startDate', 'Start Date', 'required');
		$this->form_validation->set_rules('endDate', 'End Date', 'required');
		if($this->form_validation->run() == TRUE){
			$this->_setFormData();
			$this->_setWeavingSheetTitle();
			$this->_setEmsHeaderMergeCellRowOne();
			$this->_setWeavingDeviceData();
			$this->data['fileName'] = $this->section.'_data_'.date('Y-m-d',strtotime($this->data['startDateShow'])).'_'.date('Y-m-d',strtotime($this->data['endDateShow'])).'.xlsx';
			$this->_generateAndDownload();
		}else{
			$this->_setFormData();
		}
		$this->load->view('download/weaving', $this->data);
	}

	private function _setWeavingSheetTitle(){
		$this->data['ids'] = array();
		$ids = $this->_setDeviceIdArr();
		$data = $this->data_model->getWeavingDeviceName($ids,$this->section);
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$this->data['titles'][] = $value->STYLE;
				$this->data['ids'][] = $value->STYLE_ID;
			}
		}
	}

	private function _setWeavingDeviceData(){
		$ids = $this->_setDeviceIdArr();
		$data = $this->data_model->getWeavingDeviceData($this->data['startDateShow'], $this->data['endDateShow'], $ids, $this->section);
		$deviceWiseData = array();
		$this->data['sheetData']['data'][0] = array();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$deviceDateData = array();
				foreach ($value as $keyD => $valueD) {
					$deviceDateData[$keyD] = $valueD;
				}
				$deviceWiseData[$value->STYLE_ID][] = $deviceDateData;
			}
		}
		if(count($deviceWiseData) > 0){
			foreach ($this->data['ids'] as $key => $value) {
				//$this->data['sheetData']['data'][$key][$value] = isset($deviceWiseData[$value])?$deviceWiseData[$value]:array();
				$this->data['sheetData']['data'][$key] = isset($deviceWiseData[$value])?$deviceWiseData[$value]:array();
			}
		}
	}

	private function _setFormData(){
		$this->data['pDeviceSelected'] = $this->input->post('p_device_id');
		$this->data['startDateShow'] = $this->input->post('startDate');
		$this->data['endDateShow'] = $this->input->post('endDate');
		if(!is_null($this->input->post('col'))){
			$this->data['colSelected'] = $this->input->post('col');
		}
	}

	private function _getExcelCellChar($column = 'B', $step = 1){
		for($i = 0; $i < $step; $i++) {
		    $column++;
		}
		return $column;
	}

	private function _generateAndDownload(){
		if(count($this->data['sheetData']['data'][0]) > 0){
			//exit('ok');
			$this->load->library('data_excel');
			$this->data_excel->createMultipleSheet($this->data['titles'], $this->data['sheetData']);
			$this->data_excel->generate();
			$this->data_excel->download($this->data['fileName']);
		}else{
			$this->data['msg'] = "No Records Found.";
		}
	}

    private function _dateAdd(){
        $date = new DateTime($this->startDateTime);
        $date->add(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }
	
}