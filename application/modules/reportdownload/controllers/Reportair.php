<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportair extends MY_Controller {
	public $data = array();
	public $shiftArr = array();
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->shiftArr = array(1,2,3);
		$this->load->model('air_model');
		addPageDetails();
	}

	public function index(){
		$this->data = array();
		$this->data['all'] = null; 
		$startDate = '';
		$this->data['titles'] = array('Air Report');
		$this->data['msg'] = '';
		$this->data['active_date'] = $this->air_model->getLastDate();
		if(isset($_POST['startDate']) && isset($_POST['endDate'])){
			$startDate = $this->input->post('startDate');
			$endDate = $this->input->post('endDate');
			if($startDate != ''){
				$startDate = date('Y-m-d 00:00:00',strtotime($startDate));
			}else{
				$startDate = date('Y-m-d 00:00:00');
			}

			if($endDate != ''){
				$endDate = date('Y-m-d 00:00:00',strtotime($endDate));
			}else{
				$endDate = date('Y-m-d 00:00:00');
			}
			$this->data['startDate'] = $startDate;
			$this->data['endDate'] = $endDate;
			$this->data['sheetData'] = array();
			$this->_setMeterData($startDate, $endDate);
			if($this->data['msg'] == ''){
				$this->_getMeterDetailsArr();
				$this->_setHeaderMergeCellRowOne();
				$this->_setHeaderMergeCellRowTwo();
				$this->_setHeaderMergeCellRowThreeAndFour();
				$this->_generateAndDownload();
			}
		}
		
		
		$this->load->view('reportair/index', $this->data);
	}

	private function _dateSub($date,$timestamp){
        $date = new DateTime($date);
        $date->sub(new DateInterval($timestamp));
        return $date->format('Y-m-d H:i:s');
    }

	private function _dateAddcustom($date,$timestamp){
        $date = new DateTime($date);
        $date->add(new DateInterval($timestamp));
        return $date->format('Y-m-d H:i:s');
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

    private function _setHeaderMergeCellRowOne($data = array()){
		$cellTitle = array('Compressed air generation in SCFM','Compressed air consumption business wise.','','Difference on generation and consumption');
		$color = array('91def1');
		$merge = array(12,44,1,1);
		$start = "B";
		$m = 1;
		foreach ($cellTitle as $key => $value) {
			$end = $this->_getExcelCellChar($start, $merge[$key] -1);
			$this->data['sheetData']['header'][1][] = array($start.'1:'.$end.'1', $cellTitle[$key], $m, $color[0]);
			$start = $this->_getExcelCellChar($start, $merge[$key]);
			$m = $m + $merge[$key];
		}
	}

	private function _setHeaderMergeCellRowTwo($data = array()){
		$cellTitle = array('Zh15000','Zh15000','Zh15000','Zh15000','Zh15000+','IR','ZH 7000','ZR 900','ZR500','','ZH 1600+','','Spinning bussiness','Towel bussiness','Sheet  bussiness','UTILITY','','');
		$color = array('91def1');
		$merge = array(1,1,1,1,1,1,1,1,1,1,1,1,5,18,14,7,1,1);
		$start = "B";
		$m = 1;
		foreach ($cellTitle as $key => $value) {
			$end = $this->_getExcelCellChar($start, $merge[$key] -1);
			$this->data['sheetData']['header'][2][] = array($start.'2:'.$end.'2', $cellTitle[$key], $m, $color[0]);
			$start = $this->_getExcelCellChar($start, $merge[$key]);
			$m = $m + $merge[$key];
		}
	}

	private function _setHeaderMergeCellRowThreeAndFour($data = array()){
		$color = array('91def1');
		$merge = 1;
		$start = "A";
		$m = 1;
		foreach ($this->data['sheetData']['meter']['details'] as $key => $value) {
			$end = $this->_getExcelCellChar($start, $merge -1);
			$this->data['sheetData']['header'][3][] = array($start.'3:'.$end.'3', $value['short_desc'], $m, $color[0]);
			$this->data['sheetData']['header'][4][] = array($start.'4:'.$end.'4', $value['name'], $m, $color[0]);
			//$this->data['sheetData']['header'][3][] = array($start.'3:'.$end.'3', $value['short_desc'].'('.$value['meter_id'].')', $m, $color[0]);
			//$this->data['sheetData']['header'][4][] = array($start.'4:'.$end.'4', $value['name'].'('.$value['meter_id'].')', $m, $color[0]);
			$start = $this->_getExcelCellChar($start, $merge);
			$m = $m + $merge;
		}
	}

	private function _setMeterData($startDate = '', $endDate = ''){
		$data = $this->air_model->getCfmData($startDate, $endDate);
		$this->data['sheetData']['meter']['data'] = array();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$cfm = 0;
				if($value->prev_data_cfm > 0 ){
					$cfm = ($value->prev_data_cfm - $value->data_cfm) / WEAVING_CFM;
				}
				$this->data['sheetData']['meter']['data'][$value->end_date][$value->meter_id] = $cfm;
			}
		}else{
			$this->data['msg'] = 'No Data Please.';
		}
		
	}

	private function _getExcelCellChar($column = 'B', $step = 1){
		for($i = 0; $i < $step; $i++) {
		    $column++;
		}
		return $column;
	}

    private function _generateAndDownload(){
		if(count($this->data['sheetData']['meter']['data']) > 1){
			$this->data['fileName'] = 'air_report_'.date('Y-m-d',strtotime($this->data['startDate'])).'_'.date('Y-m-d',strtotime($this->data['endDate'])).'.xlsx';
			$this->load->library('air_excel');
			$this->air_excel->createMultipleSheet($this->data['titles'], $this->data['sheetData']);
			$this->air_excel->generate();
			$this->air_excel->download($this->data['fileName']);
		}else{
			$this->data['msg'] = "No Record Found.";
		}
	}

	private function _getMeterDetailsArr(){
		$data = $this->air_model->getMeterDetails();
		$retData = array();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$retData[$value->report_excel_position] = array(
					'meter_id' => $value->meter_id,
					'name' => $value->name,
					'short_desc' => $value->short_desc
				);
			}
		}
		$retData[0] = array('meter_id' => 0, 'name' => 'Date', 'short_desc' => '');
		$retData[2] = array('meter_id' => 0, 'name' => 'Compressor 2', 'short_desc' => 'Comp. #2');
		$retData[5] = array('meter_id' => 0, 'name' => 'Comp 9 (ZH15000+)', 'short_desc' => 'Comp. #9');
		$retData[7] = array('meter_id' => 0, 'name' => 'Comp 7 (ZH7000)', 'short_desc' => 'Comp. #7');
		$retData[8] = array('meter_id' => 0, 'name' => 'Compressor 900 VSD', 'short_desc' => 'Comp. #5');
		$retData[9] = array('meter_id' => 0, 'name' => 'Comp 6 (ZR500VSD)', 'short_desc' => 'Comp. #6');
		$retData[10] = array('meter_id' => 0, 'name' => 'Turbo Compressor Air', 'short_desc' => '80 MW Turbine comp');
		$retData[11] = array('meter_id' => 0, 'name' => 'Comp 10 (ZH1600+)', 'short_desc' => 'Comp. #10');
		$retData[12] = array('meter_id' => 0, 'name' => 'Total Generation', 'short_desc' => 'Total Generation');
		$retData[13] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Spinning 1 & 2');
		$retData[14] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Spinning 3');
		$retData[15] = array('meter_id' => 0, 'name' => 'OE Draw Frame Blow Room Shed 6 & 7', 'short_desc' => 'OPEN END');
		$retData[16] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'TFO');
		$retData[17] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Total Spinning Buss.');
		$retData[18] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Warping ');
		$retData[19] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Sizing');
		$retData[24] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Wvg shed  7');
		$retData[25] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Wvg shed  8');
		$retData[26] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Wvg shed  9');
		$retData[28] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Process terry ph-1');
		$retData[29] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Process terry ph-2');
		$retData[30] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'C&S terry');
		$retData[33] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'TT WVG WARPING');
		$retData[34] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'GREIGE TT ');
		$retData[35] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Total Towel Business');
		$retData[40] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Wvg  shed 7');
		$retData[41] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Wvg  shed 8');
		$retData[42] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Wvg  shed 9');
		$retData[43] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Cleanning air w.shed1 to 6');
		$retData[44] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Cleanning air wvg shed 7 to 9');
		$retData[45] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'BS Process ph-1');
		$retData[46] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'BS Process ph- 2');
		$retData[47] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'BS Process ph- 3');
		$retData[48] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'C&S sheeting + g+1 plant');
		$retData[49] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Total Bed Sheet Business');
		$retData[50] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'ADVANCE  TEXTILE + NSC');
		$retData[51] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'BOILER');
		$retData[52] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'ETP');
		$retData[53] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'DG ');
		$retData[54] = array('meter_id' => 0, 'name' => '', 'short_desc' => '80 MW ');
		$retData[55] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'ETP ph-2');
		$retData[56] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Total UTILITY');
		$retData[57] = array('meter_id' => 0, 'name' => '', 'short_desc' => 'Total Consumption');
		$retData[58] = array('meter_id' => 0, 'name' => '', 'short_desc' => '');
		ksort($retData, SORT_NUMERIC);
		$this->data['sheetData']['meter']['details'] = $retData;
	}

	function pr($data = array(), $is_exit = 1){
		echo "<pre>";
		print_r($data);
		if($is_exit == 0){
			exit;
		}
	}
	
}