<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Report extends MY_Controller {
	public $data = array();
	public $currentStartDateTime = '';
	public $currentEndDateTime = '';
	public $dataTimeInterval = '';
	public $shiftViewArr = array(1 => 'Shift 1',2 => 'Shift 2', 3 => 'Shift 3', 4 => 'Day');
	public $shiftStartHour = 8;
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->load->model('ems_model');
		$this->_setCurrentDateTime();
	}

	public function index(){
		$this->data['typeId'] = $this->uri->segment(4);
		$this->data['shiftViewArr'] = $this->shiftViewArr;
		$this->data['minMaxDate'] = $this->_getDateDetails();
		$this->data['selectedDate'] = $this->data['minMaxDate']['max_date'];
		$this->data['selectedShift'] = 1;
		$this->data['all'] = '';
		$this->data['chartDate'] = '';
		$startDate = '';
		$endDate = '';
		if($this->input->post('go')){
			$this->data['selectedDate'] = $this->startDateTime = $selectdate = $this->input->post('selectedDate');
			$this->data['selectedShift'] = $this->input->post('shift');
			$tempDateData = $this->_setDateInterVal();
			$startDate = $tempDateData['startDate'];
			$endDate = $tempDateData['endDate'];
			$this->data['chartDate'] = strtotime($this->data['selectedDate']);
			$this->data['meterWiseData'] = $this->ems_model->getDeviceTypeWiseDataReport($startDate, $endDate, $this->data['typeId']);
			if($this->data['typeId'] > 0 && $this->data['typeId'] < 4){
				$this->data['typeWise'] = $this->_makingTypeData();
				$this->data['all'] = $this->load->view('report/all',$this->data, true);
			}
		}
		$this->load->view('report/index', $this->data);
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
		print_r($masterArr);
		exit;*/
		return $masterArr;
	}

	private function _setCurrentDateTime(){
		$this->dataTimeInterval = 'PT00H15M00S';
		$currentDate = $this->ems_model->getCurrent();
		$this->currentStartDateTime = $this->startDateTime = $currentDate[0]->end_date_time;
		$this->currentEndDateTime = $this->_dateAdd();
	}

	private function _dateAdd(){
        $date = new DateTime($this->startDateTime);
        $date->add(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

    private function _setDateInterVal(){
    	$retData['startDate'] = '';
    	$retData['endDate'] = '';
    	if($this->data['selectedShift'] == 1){
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif($this->data['selectedShift'] == 2){
			$this->dataTimeInterval = 'PT16H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif ($this->data['selectedShift'] == 3) {
			$this->dataTimeInterval = 'PT24H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif ($this->data['selectedShift'] == 4) {
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT24H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}
		return $retData;
    }


    private function _getDateDetails(){		
		$data = $this->ems_model->getDateDetails();
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


	public function showGraph(){
		$this->data['device_id'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$this->data['selectedShift'] = $this->uri->segment(6);
		$this->data['selectedDate'] = $this->startDateTime = date('Y-m-d',$this->uri->segment(7));
		$tempDateData = $this->_setDateInterVal();
		$startDate = $tempDateData['startDate'];
		$endDate = $tempDateData['endDate'];

		$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$data = $this->ems_model->getShiftWiseDataSetChart($this->data['device_id'], $this->data['paramVal'], $startDate, $endDate);
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
		$this->load->view('report/chart', $this->data);
	}
}

?>