<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Report extends MY_Controller {
	public $data = array();
	public $currentStartDateTime = '';
	public $currentEndDateTime = '';
	public $dataTimeInterval = '';
	public $shiftViewArr = array(1 => 'Shift 1',2 => 'Shift 2', 3 => 'Shift 3');
	public $shiftStartHour = 8;
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->load->model('ems_model');
		$this->_setCurrentDateTime();
		addPageDetails();
	}

	public function index(){
		//die("Coming soon.");
		$this->data['typeId'] = (($this->uri->segment(4) <= 0)?1:$this->uri->segment(4));
		if($this->uri->segment(5) != '' && $this->uri->segment(6) != ''){
			$startDate = $this->data['selectedDate'] = date('Y-m-d H:i:s', $this->uri->segment(5));
			$endDate = $this->data['selectedDateEnd'] = date('Y-m-d H:i:s', $this->uri->segment(6));
		}else{
			$startDate = '';
			$endDate = '';
		}
		$this->data['shiftViewArr'] = $this->shiftViewArr;
		$this->data['minMaxDate'] = $this->_getDateDetails('ems');
		$this->data['selectedDate'] = $this->data['minMaxDate']['min_date'];
		$this->data['selectedDateEnd'] = $this->data['minMaxDate']['max_date'];
		
		$this->data['all'] = '';
		$this->data['chartDate'] = '';
		
		if($this->input->post('go')){
			$this->dataTimeInterval = 'PT00H00M00S';
			$this->startDateTime = $this->data['selectedDate'] = $this->input->post('selectedDate');	
			$startDate = $this->_dateAdd();	
			$this->dataTimeInterval = 'PT24H00M00S';
			$this->startDateTime = $this->data['selectedDateEnd'] = $this->input->post('endDate');
			$endDate = $this->_dateAdd();	
		}

		if($startDate != '' && $endDate != ''){
			$this->data['selectedDate'] = date('Y-m-d', strtotime($startDate));
			$this->startDateTime = date('Y-m-d', strtotime($endDate));
			$this->dataTimeInterval = 'PT24H00M00S';
			$this->data['selectedDateEnd'] = $this->_dateSub();
			$this->data['chartDate'] = strtotime($startDate);
			$this->data['chartDateEnd'] = strtotime($endDate);
			$this->data['meterWiseData'] = $this->ems_model->getDeviceTypeWiseDataReport($startDate, $endDate, $this->data['typeId']);
			if($this->data['typeId'] > 0 && $this->data['typeId'] < 4){
				$this->data['typeWise'] = $this->_makingTypeData();
				$this->data['all'] = $this->load->view('report/day/all',$this->data, true);
			}
		}

		$this->load->view('report/day/index', $this->data);
	}

	private function _dateSub(){
        $date = new DateTime($this->startDateTime);
        $date->sub(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

	private function _makingTypeData(){
		$masterArr = array();
		if(count($this->data['meterWiseData']) > 0){
			foreach ($this->data['meterWiseData'] as $value) {
				$masterArr[$value->type][$value->device_id]['name'] = $value->device_name;
				$masterArr[$value->type][$value->device_id]['data']['KW'][] = $value->KW;
				$masterArr[$value->type][$value->device_id]['data']['PF'][] = $value->PF;
				$masterArr[$value->type][$value->device_id]['data']['KWPF'][] = $value->KWPF;
				$masterArr[$value->type][$value->device_id]['data']['Volt'][] = $value->Volt;
				$masterArr[$value->type][$value->device_id]['data']['Amps'][] = $value->Amps;
				$masterArr[$value->type][$value->device_id]['data']['HZ'][] = $value->HZ;
			}
		}
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
			$this->dataTimeInterval = 'PT00H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif($this->data['selectedShift'] == 2){
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif ($this->data['selectedShift'] == 3) {
			$this->dataTimeInterval = 'PT16H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif ($this->data['selectedShift'] == 4) {
			$this->dataTimeInterval = 'PT00H00M00S';
			$retData['startDate'] = $this->_dateAdd();
			$this->dataTimeInterval = 'PT24H00M00S';
			if($this->data['selectedDateEnd'] == ''){
				$this->startDateTime = $retData['startDate'];
			}else{
				$this->startDateTime = $this->data['selectedDateEnd'];
			}
			$retData['endDate'] = $this->_dateAdd();
		}
		return $retData;
    }


    private function _getDateDetails($type = 'ems'){		
		$data = $this->ems_model->getDateDetails($type);
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


	public function shift(){
		$this->data['typeId'] = $this->uri->segment(4);
		$this->data['shiftViewArr'] = $this->shiftViewArr;
		$this->data['minMaxDate'] = $this->_getDateDetails('ems');
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
				$this->data['all'] = $this->load->view('report/shift/all',$this->data, true);
			}
		}
		$this->load->view('report/shift/index', $this->data);
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
		            "value": "'.round($value->data,2).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	$this->data['dataSetConsis'] = '';
		$this->load->view('report/shift/chart', $this->data);
	}

	public function showGraph_Air(){
		$this->data['device_id'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$this->data['selectedShift'] = $this->uri->segment(6);
		$this->data['selectedDate'] = $this->startDateTime = date('Y-m-d',$this->uri->segment(7));
		$tempDateData = $this->_setDateInterVal();
		$startDate = $tempDateData['startDate'];
		$endDate = $tempDateData['endDate'];

		$this->data['meterName'] = $this->uri->segment(8);
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$data = $this->ems_model->getShiftWiseDataSetChart($this->data['device_id'], $this->data['paramVal'], $startDate, $endDate);
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	if(count($data) > 0){

    		$this->data['airMeterName'] = array(1=>"IR Compressor",2=>"Compressor 3",3=>"Compressor 2",4=>"Compressor 4",5=>"Compressor 900 VSD",6 => 'Comp 1 (ZH15000)', 7 => 'Comp 7 (ZH7000)', 8 => 'Comp 9 (ZH15000+)', 9 => 'Comp 6 (ZR500VSD)', 10 => 'Comp 10 (ZH1600+)', 11 => 'Comp 11 (Cameron Turbine)', 12 => 'WATER PDB 5', 13 => 'COMPRESSOR WATER PDB 1', 14 => 'COMPRESSOR WATER PDB 2', 15 => 'WATER PDB 4');
		
    		$this->data['meterName'] = isset($this->data['airMeterName'][$this->data['meterName']]) ? $this->data['airMeterName'][$this->data['meterName']] : 'NA';//$data[0]->device_name;

    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($value->data,2).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	$this->data['dataSetConsis'] = '';
		$this->load->view('report/shift/chart', $this->data);
	}

	public function showGraphTotal(){
		$this->data['paramVal'] = $column = $this->uri->segment(4);
		$this->data['type'] = $this->uri->segment(5);
		$this->data['typeId'] = $this->uri->segment(6);
		$this->data['selectedShift'] = $this->uri->segment(7);
		$this->data['selectedDate'] = $this->startDateTime = date('Y-m-d',$this->uri->segment(8));
		$tempDateData = $this->_setDateInterVal();
		$startDate = $tempDateData['startDate'];
		$endDate = $tempDateData['endDate'];

		$this->data['meterName'] = 'Total for Shift '.$this->data['selectedShift'];
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$data = $this->ems_model->getShiftWiseDataSetChartTotal($startDate, $endDate, $this->data['type'],$this->data['typeId'],$column);
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		$maxMin = array();
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($value->data,2).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = '';
		$this->load->view('report/shift/chart', $this->data);
	}

	public function showGraphTotalLoss(){
		$this->data['paramVal'] = $column = 'KW';
		$this->data['typeId'] = $this->uri->segment(4);
		$this->data['selectedShift'] = $this->uri->segment(5);
		$this->data['selectedDate'] = $this->startDateTime = date('Y-m-d',$this->uri->segment(6));
		$this->data['selectedPerAbs'] = $perAbs = $this->uri->segment(7);
		$tempDateData = $this->_setDateInterVal();
		$startDate = $tempDateData['startDate'];
		$endDate = $tempDateData['endDate'];
		if($perAbs == 'abs'){
			$this->data['meterName'] = 'Total for Shift '.$this->data['selectedShift'];
			$this->data['meterNameColumn'] = 'Loss in Absolute Value ';
		}else{
			$this->data['meterName'] = 'Total for Shift '.$this->data['selectedShift'];
			$this->data['meterNameColumn'] = 'Loss in %';
		}

    	$dataGen = $this->ems_model->getShiftWiseDataSetChartTotal($startDate, $endDate, 'G',$this->data['typeId'],$column);
    	$dataDist = $this->ems_model->getShiftWiseDataSetChartTotal($startDate, $endDate, 'D',$this->data['typeId'],$column);
    	$data = array();
    	$maxMin = array();
    	if(count($dataGen) > 0){
    		$dataG = array();
    		foreach ($dataGen as $keyG => $valueG) {
    			$dataG[$valueG->end_date_time] = $valueG->data;
    		}
    		$dataD = array();
    		foreach ($dataDist as $keyD => $valueD) {
    			if(isset($dataG[$valueD->end_date_time])){
    				$data[$valueD->end_date_time]['abs'] = $dataG[$valueD->end_date_time] - $valueD->data;
    				if($dataG[$valueD->end_date_time] == 0){
    					$data[$valueD->end_date_time]['per'] = 0;
    				}else{
    					$data[$valueD->end_date_time]['per'] = ($data[$valueD->end_date_time]['abs']/$dataG[$valueD->end_date_time]) * 100;
    				}
    			}else{
    				$data[$valueD->end_date_time]['abs'] = 0;
    				$data[$valueD->end_date_time]['per'] = 0;
    			}
    			$maxMin['per'][] = $data[$valueD->end_date_time]['per'];
    			$maxMin['abs'][] = $data[$valueD->end_date_time]['abs'];
    		}
    	}
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$key.'",
		            "value": "'.round($value[$perAbs],3).'"
		        },';
    		}

    		$this->data['max_val'] = intval(max($maxMin[$perAbs])) ;
    		$this->data['min_val'] = intval(min($maxMin[$perAbs]));
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = '';
		$this->load->view('report/shift/chart', $this->data);
	}

	public function showGraphDay(){
		$this->data['device_id'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$startDate = date('Y-m-d H:i:s',$this->uri->segment(6));
		$endDate = date('Y-m-d H:i:s',$this->uri->segment(7));
		
		$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	if($this->data['dayCount'] == 1){
    		$data = $this->ems_model->getDayWiseDataSetChartLine($this->data['device_id'], $this->data['paramVal'], $startDate, $endDate);
    	}else{
    		$data = $this->ems_model->getDayWiseDataSetChartBar($this->data['device_id'], $this->data['paramVal'], $startDate, $endDate);
    	}
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
		$this->load->view('report/day/chart', $this->data);
	}

	public function showGraphDay_Air(){
		$this->data['device_id'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$startDate = date('Y-m-d H:i:s',$this->uri->segment(6));
		$endDate = date('Y-m-d H:i:s',$this->uri->segment(7));
		
		$this->data['meterName'] = $this->uri->segment(8);
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	if($this->data['dayCount'] == 1){
    		$data = $this->ems_model->getDayWiseDataSetChartLine($this->data['device_id'], $this->data['paramVal'], $startDate, $endDate);
    	}else{
    		$data = $this->ems_model->getDayWiseDataSetChartBar($this->data['device_id'], $this->data['paramVal'], $startDate, $endDate);
    	}
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	if(count($data) > 0){
    		$this->data['airMeterName'] = array(1=>"IR Compressor",2=>"Compressor 3",3=>"Compressor 2",4=>"Compressor 4",5=>"Compressor 900 VSD",6 => 'Comp 1 (ZH15000)', 7 => 'Comp 7 (ZH7000)', 8 => 'Comp 9 (ZH15000+)', 9 => 'Comp 6 (ZR500VSD)', 10 => 'Comp 10 (ZH1600+)', 11 => 'Comp 11 (Cameron Turbine)', 12 => 'WATER PDB 5', 13 => 'COMPRESSOR WATER PDB 1', 14 => 'COMPRESSOR WATER PDB 2', 15 => 'WATER PDB 4');
		
    		$this->data['meterName'] = isset($this->data['airMeterName'][$this->data['meterName']]) ? $this->data['airMeterName'][$this->data['meterName']] : 'NA';//$data[0]->device_name;

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
		$this->load->view('report/day/chart', $this->data);
	}

	public function showGraphDayTotal(){
		$this->data['meterNameColumn'] = $column = $this->uri->segment(4);
		$this->data['type'] = $this->uri->segment(5);
		$this->data['typeId'] = $this->uri->segment(6);
		$startDate = date('Y-m-d H:i:s',$this->uri->segment(7));
		$endDate = date('Y-m-d H:i:s',$this->uri->segment(8));
		
		$this->data['meterName'] = 'Total by Date Range';
    	
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	if($this->data['dayCount'] == 1){
    		$data = $this->ems_model->getDeviceTypeWiseDayDataReportTotal($startDate, $endDate, $this->data['type'], $this->data['typeId'], $column);
    	}else{
    		$data = $this->ems_model->getDeviceTypeWiseDateRangeDataReportTotal($startDate, $endDate, $this->data['type'], $this->data['typeId'], $column);
    	}
		$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		$maxMin = array();
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($value->data,3).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = '';
		$this->load->view('report/day/chart', $this->data);
	}

	public function showGraphDayTotalLoss(){

		$this->data['paramVal'] = $column = 'KW';
		$this->data['typeId'] = $this->uri->segment(4);
		$startDate = date('Y-m-d',$this->uri->segment(5));
		$endDate = date('Y-m-d',$this->uri->segment(6));
		$this->data['selectedPerAbs'] = $perAbs = $this->uri->segment(7);
		if($perAbs == 'abs'){
			$this->data['meterName'] = 'Total for Date Range ';
			$this->data['meterNameColumn'] = 'Loss in Absolute Value ';
		}else{
			$this->data['meterName'] = 'Total for Date Range ';
			$this->data['meterNameColumn'] = 'Loss in %';
		}
		$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
		if($this->data['dayCount'] == 1){
    		$dataGen = $this->ems_model->getDeviceTypeWiseDayDataReportTotal($startDate, $endDate, 'G', $this->data['typeId'], $column);
    		$dataDist = $this->ems_model->getDeviceTypeWiseDayDataReportTotal($startDate, $endDate, 'D', $this->data['typeId'], $column);
    	}else{
    		$dataGen = $this->ems_model->getDeviceTypeWiseDateRangeDataReportTotal($startDate, $endDate, 'G', $this->data['typeId'], $column);
    		$dataDist = $this->ems_model->getDeviceTypeWiseDateRangeDataReportTotal($startDate, $endDate, 'D', $this->data['typeId'], $column);
    	}
    	
    	$data = array();
    	$maxMin = array();
    	if(count($dataGen) > 0){
    		$dataG = array();
    		foreach ($dataGen as $keyG => $valueG) {
    			$dataG[$valueG->end_date_time] = $valueG->data;
    		}
    		
    		foreach ($dataDist as $keyD => $valueD) {
    			if(isset($dataG[$valueD->end_date_time])){
    				$data[$valueD->end_date_time]['abs'] = $dataG[$valueD->end_date_time] - $valueD->data;
    				if($dataG[$valueD->end_date_time] == 0){
    					$data[$valueD->end_date_time]['per'] = 0;
    				}else{
    					$data[$valueD->end_date_time]['per'] = ($data[$valueD->end_date_time]['abs']/$dataG[$valueD->end_date_time]) * 100;
    				}
    			}else{
    				$data[$valueD->end_date_time]['abs'] = 0;
    				$data[$valueD->end_date_time]['per'] = 0;
    			}
    			$maxMin['per'][] = $data[$valueD->end_date_time]['per'];
    			$maxMin['abs'][] = $data[$valueD->end_date_time]['abs'];
    		}
    	}
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$key.'",
		            "value": "'.round($value[$perAbs],3).'"
		        },';
    		}

    		$this->data['max_val'] = intval(max($maxMin[$perAbs])) ;
    		$this->data['min_val'] = intval(min($maxMin[$perAbs]));
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = '';
    	$this->data['dayCount'] = 1;
		$this->load->view('report/day/chart', $this->data);
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

	
	public function cppDay(){
		$this->data['minMaxDate'] = $this->_getDateDetails('ems_cpp');
		$this->data['selectedDate'] = $this->data['minMaxDate']['min_date'];
		$this->data['selectedDateEnd'] = $this->data['minMaxDate']['max_date'];
		$this->data['all'] = '';
		$this->data['chartDate'] = '';
		$startDate = '';
		$endDate = '';
		if($this->input->post('go')){
			$this->dataTimeInterval = 'PT00H00M00S';
			$this->startDateTime = $this->data['selectedDate'] = $this->input->post('selectedDate');	
			$startDate = $this->_dateAdd();	
			$this->dataTimeInterval = 'PT24H00M00S';
			$this->startDateTime = $this->data['selectedDateEnd'] = $this->input->post('endDate');
			$endDate = $this->_dateAdd();
			$this->data['chartDate'] = strtotime($startDate);
			$this->data['chartDateEnd'] = strtotime($endDate);
			$this->data['meterWiseData'] = $this->ems_model->getCppDataReportDay($startDate, $endDate);
			$this->data['all'] = $this->load->view('report/cpp_day/all',$this->data, true);
		}
		$this->load->view('report/cpp_day/index', $this->data);
	}

	public function showGraphDayCpp(){
		$this->data['device_id'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$startDate = date('Y-m-d H:i:s',$this->uri->segment(6));
		$endDate = date('Y-m-d H:i:s',$this->uri->segment(7));
		
		$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	if($this->data['dayCount'] == 1){
    		$data = $this->ems_model->getDayWiseDataSetChartLineCpp($this->data['device_id'], $this->data['paramVal'], $startDate, $endDate);
    	}else{
    		$data = $this->ems_model->getDayWiseDataSetChartBarCpp($this->data['device_id'], $this->data['paramVal'], $startDate, $endDate);
    	}
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
		$this->load->view('report/cpp_day/chart', $this->data);
	}

	public function showGraphDayCppTotal(){
		$this->data['meterNameColumn'] = $column = $this->uri->segment(4);
		$this->data['startDate'] = $startDate = date('Y-m-d H:i:s',$this->uri->segment(5));
		$this->data['endDate'] = $endDate = date('Y-m-d H:i:s',$this->uri->segment(6));
		
		$this->data['meterName'] = 'Total By Date Range';
    	
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	if($this->data['dayCount'] == 1){
    		$data = $this->ems_model->getCppDayDataReportTotal($column, $startDate, $endDate);
    	}else{
    		$data = $this->ems_model->getCppDateRangeDataReportTotal($column, $startDate, $endDate);
    	}
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		$maxMin = array();
    		
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($value->data,3).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = '';
    	$this->load->view('report/cpp_day/chart', $this->data);
		
	}

	public function cppShift(){
		$this->data['shiftViewArr'] = $this->shiftViewArr;
		$this->data['minMaxDate'] = $this->_getDateDetails('ems_cpp');
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
			$this->data['meterWiseData'] = $this->ems_model->getCppDataReportShift($startDate, $endDate);
			$this->data['all'] = $this->load->view('report/cpp_shift/all',$this->data, true);
		}
		$this->load->view('report/cpp_shift/index', $this->data);
	}

	public function showGraphCpp(){
		$this->data['device_id'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$this->data['selectedShift'] = $this->uri->segment(6);
		$this->data['selectedDate'] = $this->startDateTime = date('Y-m-d',$this->uri->segment(7));
		$tempDateData = $this->_setDateInterVal();
		$startDate = $tempDateData['startDate'];
		$endDate = $tempDateData['endDate'];

		$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$data = $this->ems_model->getShiftWiseDataSetChartCpp($this->data['device_id'], $this->data['paramVal'], $startDate, $endDate);

    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->device_name;
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($value->data,2).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	$this->data['dataSetConsis'] = '';
		$this->load->view('report/cpp_shift/chart', $this->data);
	}

	public function showGraphCppTotal(){
		
		$this->data['paramVal'] = $column = $this->uri->segment(4);
		$this->data['selectedShift'] = $this->uri->segment(5);
		$this->data['selectedDate'] = $this->startDateTime = date('Y-m-d',$this->uri->segment(6));
		$tempDateData = $this->_setDateInterVal();
		$startDate = $tempDateData['startDate'];
		$endDate = $tempDateData['endDate'];

		$this->data['meterName'] = 'Total for Shift '.$this->data['selectedShift'];
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$data = $this->ems_model->getShiftWiseDataSetChartCppReportTotal($column, $startDate, $endDate);
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$maxMin = array();
    	$dataSet = '';
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($value->data,2).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    	}
    	$this->data['dataSet'] = $dataSet;
    	if(count($maxMin) > 0){
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	}else{
    		$this->data['max_val'] = 10;
    		$this->data['min_val'] = 0;
    	}
    	
    	$this->data['dataSetConsis'] = '';
		$this->load->view('report/cpp_shift/chart_total', $this->data);
	}

	public function all(){
		$this->data['minMaxDate'] = $this->_getDateDetails('ems');
		$this->data['selectedDate'] = $this->data['minMaxDate']['min_date'];
		$this->data['selectedDateEnd'] = $this->data['minMaxDate']['max_date'];
		
		$this->data['all'] = '';
		$this->data['chartDate'] = '';
		$this->data['chartDateEnd'] = '';
		$this->data['startDateShow'] = '';
		$this->data['endDateShow'] = '';
		if($this->input->post('go')){
			$this->dataTimeInterval = 'PT00H00M00S';
			$this->startDateTime = $this->data['selectedDate'] = $this->input->post('selectedDate');	
			$this->data['startDateShow'] = $startDate = $this->_dateAdd();	
			$this->dataTimeInterval = 'PT24H00M00S';
			$this->startDateTime = $this->data['selectedDateEnd'] = $this->input->post('endDate');
			$this->data['endDateShow'] = $endDate = $this->_dateAdd();
			$this->data['chartDate'] = strtotime($startDate);
			$this->data['chartDateEnd'] = strtotime($endDate);
		}
		
		$this->_getAllDataHtml();
		$this->load->view('report/all/index', $this->data);
	}

	private function _getAllDataHtml(){
		if($this->data['startDateShow'] != '' && $this->data['endDateShow'] != ''){
			$this->data['genDistAllData'] = $this->ems_model->getGenDistAllDataReport($this->data['startDateShow'], $this->data['endDateShow']);
			$this->data['meterWiseDataCpp'] = $this->ems_model->getDeviceWiseDataCppAllReport($this->data['startDateShow'], $this->data['endDateShow']);
					
			$emsCppArr = '5,1,2,3,4,6';
			$emsArr = '2,3,30,31,34,4';

			$this->data['emsCppDataLoss'] = $this->ems_model->getEmsCppLossAllReport($this->data['startDateShow'], $this->data['endDateShow'], $emsCppArr);
			$this->data['emsDataLoss'] = $this->ems_model->getEmsLossAllReport($this->data['startDateShow'], $this->data['endDateShow'], $emsArr);
			
			$this->data['all'] = $this->load->view('report/all/data', $this->data, true);

			unset($this->data['genData']);
			unset($this->data['distData']);
			for($i = 1; $i <= 3; $i++){
				unset($this->data['genLevelData'][$i]);
			}
			unset($this->data['meterWiseDataCpp']);
		}
	}

	public function showGenDistAllTotal(){
		$this->data['type'] = $type = $this->uri->segment(4);
		$this->data['meterNameColumn'] = $column = $this->uri->segment(5);
		$startDate = date('Y-m-d H:i:s',$this->uri->segment(6));
		$endDate = date('Y-m-d H:i:s',$this->uri->segment(7));
		$this->data['meterName'] = 'Total By Date Range';
    	
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	if($this->data['dayCount'] == 1){
    		$data = $this->ems_model->getGenDistAllTotalDayDataReportTotal($column, $startDate, $endDate, $type);
    	}else{
    		$data = $this->ems_model->getGenDistAllTotalDateRangeDataReportTotal($column, $startDate, $endDate, $type);
    	}
    	
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		$maxMin = array();
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($value->data,3).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		if($column == 'PF'){
    			$this->data['min_val'] = 0;
    		}else{
    			$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    		}
    		
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->load->view('report/all/genDisTotal', $this->data);
	}

	public function showGenDistLossAllTotal(){
		$this->data['selectedPerAbs'] = $perAbs = $this->uri->segment(4);
		$startDate = date('Y-m-d H:i:s',$this->uri->segment(5));
		$endDate = date('Y-m-d H:i:s',$this->uri->segment(6));
		$this->data['meterName'] = 'Total By Date Range';
    	
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$this->data['meterNameColumn'] = $column = 'KW';
    	if($this->data['dayCount'] == 1){
    		$dataG = $this->ems_model->getGenDistAllTotalDayDataReportTotal($column, $startDate, $endDate, 'G');
    		$dataD = $this->ems_model->getGenDistAllTotalDayDataReportTotal($column, $startDate, $endDate, 'D');
    	}else{
    		$dataG = $this->ems_model->getGenDistAllTotalDateRangeDataReportTotal($column, $startDate, $endDate, 'G');
    		$dataD = $this->ems_model->getGenDistAllTotalDateRangeDataReportTotal($column, $startDate, $endDate, 'D');
    	}
    	
    	
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($dataG) > 0){
    		$maxMin = array();
    		foreach ($dataG as $key => $value) {
    			$tempVal = $value->data;
    			if(isset($dataD[$key])){
    				$tempVal = $tempVal - $dataD[$key]->data;
    			}
    			if($perAbs == 'per'){
    				if($value->data > 0){
    					$tempVal = ($tempVal / $value->data) * 100;
    				}else{
    					$tempVal = 0.00;
    				}
    			}
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($tempVal,3).'"
		        },';
		        $maxMin[] = $tempVal;
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		if($column == 'PF'){
    			$this->data['min_val'] = 0;
    		}else{
    			$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    		}
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->load->view('report/all/genDisTotal', $this->data);
	}

	public function showPPWILchartLoss(){
		$emsCppArr = $this->uri->segment(4);
    	$emsArr = $this->uri->segment(5);
		$startDate = date('Y-m-d H:i:s',$this->uri->segment(6));
		$endDate = date('Y-m-d H:i:s',$this->uri->segment(7));
		$this->data['selectedPerAbs'] = $perAbs = $this->uri->segment(8);
		$this->data['meterName'] = 'Total By Date Range';
    	
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$this->data['meterNameColumn'] = $column = 'KW';
    	if($this->data['dayCount'] == 1){
    		$dataCpp = $this->ems_model->showCppWillChartLossTotalAllReport($startDate, $endDate, $emsCppArr, 'ems_cpp', $this->data['dayCount']);
    		$dataWil = $this->ems_model->showCppWillChartLossTotalAllReport($startDate, $endDate, $emsArr, 'ems', $this->data['dayCount']);
    	}else{
    		$dataCpp = $this->ems_model->showCppWillChartLossTotalAllReport($startDate, $endDate, $emsCppArr, 'ems_cpp', $this->data['dayCount']);
    		$dataWil = $this->ems_model->showCppWillChartLossTotalAllReport($startDate, $endDate, $emsArr, 'ems', $this->data['dayCount']);
    	}
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($dataCpp) > 0){
    		$maxMin = array();
    		foreach ($dataCpp as $key => $value) {
    			$tempVal = $value->data;
    			if(isset($dataWil[$key])){
    				$tempVal = $tempVal - $dataWil[$key]->data;
    			}
    			if($perAbs == 'per'){
    				if($value->data > 0){
    					$tempVal = ($tempVal / $value->data) * 100;
    				}else{
    					$tempVal = 0.00;
    				}
    			}
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($tempVal,3).'"
		        },';
		        $maxMin[] = $tempVal;
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	}

		$this->data['dataSet'] = $dataSet;
    	$this->load->view('report/all/genDisTotal', $this->data);
	}

	public function showCppWillChartLossTotalAll(){
		$this->data['type'] = $this->uri->segment(4);
		$startDate = date('Y-m-d H:i:s',$this->uri->segment(5));
		$endDate = date('Y-m-d H:i:s',$this->uri->segment(6));
		$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

		if($this->data['type'] == 'ems'){
			$emsArr = $meterIds = '2,3,30,31,34,4';
			$this->data['meterName'] = 'WIL Meter';
    		$this->data['meterNameColumn'] = $column = 'KW';
		}else{
			$emsCppArr = $meterIds = '5,1,2,3,4,6';
			$this->data['meterName'] = 'CPP Meter';
    		$this->data['meterNameColumn'] = $column = 'KW';
		}
		if($this->data['dayCount'] == 1){
			$data = $this->ems_model->getCppDayDataReportTotalAll($column, $startDate, $endDate, $meterIds, $this->data['type']);
		}else{
			$data = $this->ems_model->getCppDateRangeDataReportTotalAll($column, $startDate, $endDate, $meterIds, $this->data['type']);
		}
		$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		$maxMin = array();
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($value->data,3).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		if($column == 'PF'){
    			$this->data['min_val'] = 0;
    		}else{
    			$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    		}
    		
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->load->view('report/all/genDisTotal', $this->data);
	}

	public function showCppWillChartLossAll(){
		$this->data['selectedPerAbs'] = $perAbs = $this->uri->segment(4);
		$startDate = date('Y-m-d H:i:s',$this->uri->segment(5));
		$endDate = date('Y-m-d H:i:s',$this->uri->segment(6));
		$this->data['meterName'] = 'Total By Date Range';
    	
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$this->data['meterNameColumn'] = $column = 'KW';
    	$emsArr = '2,3,30,31,34,4';
    	$emsCppArr = '5,1,2,3,4,6';
    	if($this->data['dayCount'] == 1){
    		$dataCpp = $this->ems_model->getCppDayDataReportTotalAll($column, $startDate, $endDate, $emsCppArr, 'ems_cpp');
    		$dataWil = $this->ems_model->getCppDayDataReportTotalAll($column, $startDate, $endDate, $emsArr, 'ems');
    	}else{
    		$dataCpp = $this->ems_model->getCppDateRangeDataReportTotalAll($column, $startDate, $endDate, $emsCppArr, 'ems_cpp');
    		$dataWil = $this->ems_model->getCppDateRangeDataReportTotalAll($column, $startDate, $endDate, $emsArr, 'ems');
    	}
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($dataCpp) > 0){
    		$maxMin = array();
    		foreach ($dataCpp as $key => $value) {
    			$tempVal = $value->data;
    			if(isset($dataWil[$key])){
    				$tempVal = $tempVal - $dataWil[$key]->data;
    			}
    			if($perAbs == 'per'){
    				if($value->data > 0){
    					$tempVal = ($tempVal / $value->data) * 100;
    				}else{
    					$tempVal = 0.00;
    				}
    			}
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($tempVal,3).'"
		        },';
		        $maxMin[] = $tempVal;
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	}

		$this->data['dataSet'] = $dataSet;
    	$this->load->view('report/all/genDisTotal', $this->data);
	}


}

?>