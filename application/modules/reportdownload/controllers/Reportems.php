<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Reportems extends MY_Controller {
	public $data = array();
	public $dataTimeInterval = '';
	public $startDateTime = '';
	public $dateRange = array();
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->load->model('ems_model');
		addPageDetails();
	}

	public function index(){		
		$this->data = array();
		$this->data['msg'] = '';
		$this->data['fileName'] = '';
		$this->data['titles'] = array('KWH Readings', 'KWH Consumption', 'Average KW Report', 'Compressor KWH Reading', 'Compressor KWH Consumption');
		$this->data['sheetData'] = array();
		$this->data['startDate'] = '';
		$this->data['endDate'] = '';
		$this->data['active_date'] = $this->ems_model->getLastDate();
		if($this->input->post()){
			$this->data['startDate'] = $this->input->post('startDate');
			$this->data['endDate'] = $this->input->post('endDate');
			$this->dateRange = $this->_getDatesFromRange($this->data['startDate'], $this->data['endDate']);
			$this->_getKwhReadingData();
			$this->_getKwhConsumptionData();
			$this->_getKwAvgData();
			$this->_getKwhReadingCompressor();
			$this->_generateAndDownload();
		}
		$this->load->view('reportdownload/reportems/index', $this->data);
	}

	private function _getKwhReadingData(){
		$dataTemp = array();
		$dataTempNew = array();
		$this->data['sheetData'][0]['data'] = array();
		$this->data['sheetData'][0]['header'] = array();
		$data = $this->ems_model->getKwhReadingData($this->data['startDate'], $this->data['endDate']);
		if(count($data) > 0){
			$this->data['sheetData'][0]['header'][0] = 'Date';
			foreach ($data as $key => $value) {
				$dataTemp[$value->end_date_time_1][$value->type_text][$value->device_id] = $value->data_kwh;
				$dataTempNew[$value->type_text][$value->device_id][$value->end_date_time_1] = $value->data_kwh;
				$this->data['sheetData'][0]['header'][$value->device_id] = $value->device_name;
			}
		}
		$period = $this->dateRange;
		$period = array_reverse($period);
		$originalData = array();
		if(count($period) > 0){
			foreach ($period as $keyP => $valueP) {
				if(count($dataTempNew) > 0){
					foreach ($dataTempNew as $keyDT => $valueDT) {
						foreach ($valueDT as $keyVDT => $valueVDT) {
							if(isset($dataTempNew[$keyDT][$keyVDT][$valueP])){
								$originalData[$valueP][$keyDT][$keyVDT] = $dataTempNew[$keyDT][$keyVDT][$valueP];
							}else{
								$originalData[$valueP][$keyDT][$keyVDT] = 0;
							}
						}
					}
				}
			}
		}
		$this->data['sheetData'][0]['data'] = $originalData;
		if(isset($dataTemp[$this->data['startDate']]) && count($dataTemp[$this->data['startDate']]) > 0){
			$this->_setKwhReadingMergeCell($dataTemp[$this->data['startDate']]);
		}elseif(isset($dataTemp[$this->data['endDate']]) && count($dataTemp[$this->data['endDate']]) > 0){
			$this->_setKwhReadingMergeCell($dataTemp[$this->data['endDate']]);
		}
		unset($data);unset($dataTemp);unset($dataTempNew);unset($originalData);
	}

	private function _getExcelCellChar($column = 'B', $step = 1){
		for($i = 0; $i < $step; $i++) {
		    $column++;
		}
		return $column;
	}

	private function _setKwhReadingMergeCell($data = array()){
		$cellTitle = array('Power Generation','Power Distribution');
		$color = array('91def1','9decd4');
		$start = "B";
		$k = 0;
		$m = 1;
		foreach ($data as $key => $value) {
			$end = $this->_getExcelCellChar($start, count($value) -1);
			$this->data['sheetData'][0]['merge'][] = array($start.'1:'.$end.'1', $cellTitle[$k], $m, $color[$k]);
			$start = $this->_getExcelCellChar($start, count($value));
			$k++;
			$m = $m + count($value);
		}
	}

	private function _getKwhConsumptionData(){
		$dataTemp = array();
		$dataTempNew = array();
		$this->data['sheetData'][1]['data'] = array();
		$this->dataTimeInterval = 'PT24H00M00S';
		$this->startDateTime = $this->data['endDate'];
		$startD = $this->data['startDate'];
		$endD = $this->_dateAdd();
		$data = $this->ems_model->getKwhReadingData($startD, $endD);
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$dataTempNew[$value->type_text][$value->device_id][$value->end_date_time_1] = $value->data_kwh;
				$dataTemp[$value->type_text][$value->device_id] = $value->device_name;
			}
		}
		$originalHeader = $dataTemp;
		$originalHeader['G']['total'] = 'Total KWH Generation'; 
		$originalHeader['D']['total'] = 'Total KWH Distribituion'; 
		$originalHeader['diff'] = 'Difference in KWH';
		$originalHeader['error'] = 'Error in %'; 
		$period = $this->dateRange;
		$period = array_reverse($period);
		$this->dataTimeInterval = 'P1D';
		$originalData = array();
		$totalData = array();
		if(count($period) > 0){
			foreach ($period as $keyP => $valueP) {
				$start = $this->startDateTime = $valueP;
				$end = date('Y-m-d', strtotime($this->_dateAdd()));
				if(count($dataTemp) > 0){
					foreach ($dataTemp as $keyDT => $valueDT) {
						foreach ($valueDT as $keyVDT => $valueVDT) {
							if(isset($dataTempNew[$keyDT][$keyVDT][$end]) && isset($dataTempNew[$keyDT][$keyVDT][$start])){
								$originalData[$start][$keyDT][$keyVDT] = $dataTempNew[$keyDT][$keyVDT][$end] - $dataTempNew[$keyDT][$keyVDT][$start];
								$totalData[$start][$keyDT][$keyVDT] = $originalData[$start][$keyDT][$keyVDT];
							}else{
								$originalData[$start][$keyDT][$keyVDT] = 0;
								$totalData[$start][$keyDT][$keyVDT] = 0;
							}
						}
						if(isset($totalData[$start][$keyDT])){
							$originalData[$start][$keyDT]['total'] = array_sum($totalData[$start][$keyDT]);
							unset($totalData[$start][$keyDT][$keyVDT]);
						}
					}
					if(isset($originalData[$start])){
						$originalData[$start]['diff'] = $originalData[$start]['G']['total'] - $originalData[$start]['D']['total'];
						if($originalData[$start]['G']['total'] != 0){
							$originalData[$start]['error'] = round(($originalData[$start]['diff']/$originalData[$start]['G']['total']) * 100, 2);
						}else{
							$originalData[$start]['error'] = 'N/A';
						}
					}
				}
			}
		}
		$this->_setKwhConsumptionMergeCell();
		$this->data['sheetData'][1]['header'] = $originalHeader;
		$this->data['sheetData'][1]['data'] = $originalData;
		unset($originalHeader);unset($originalData);
	}

	private function _setKwhConsumptionMergeCell($data = array()){
		$genDistData = $this->ems_model->getCountGenDist();
		$cellTitle = array('Power Generation','','Power Distribution','','','');
		$color = array('91def1','f8f9f9 ','9decd4','f8f9f9','f8f9f9','f8f9f9');
		$start = "B";
		$m = 1;
		foreach ($cellTitle as $key => $value) {
			if($key == 0){
				$end = $this->_getExcelCellChar($start, $genDistData[0]->total -1);
			}elseif($key == 2){
				$end = $this->_getExcelCellChar($start, $genDistData[1]->total -1);
			}else{
				$end = $this->_getExcelCellChar($start, 0);
			}
			$this->data['sheetData'][1]['merge'][] = array($start.'1:'.$end.'1', $value, $m, $color[$key]);
			if($key == 0){
				$m = $m + $genDistData[0]->total;
				$start = $this->_getExcelCellChar($start, $genDistData[0]->total);
			}elseif($key == 2){
				$m = $m + $genDistData[1]->total;
				$start = $this->_getExcelCellChar($start, $genDistData[1]->total);
			}else{
				$m = $m + 1;
				$start = $this->_getExcelCellChar($start, 1);
			}
		}
	}

	private function _getKwAvgData(){
		$dataTemp = array();
		$dataTempNew = array();
		$this->data['sheetData'][2]['data'] = array();
		$startD = $this->data['startDate'];
		$endD = $this->data['endDate'];
		$data = $this->ems_model->getKwAvgData($startD, $endD);
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$dataTempNew[$value->type_text][$value->device_id][$value->end_date_time_1] = $value->data_kw;
				$dataTemp[$value->type_text][$value->device_id] = $value->device_name;
			}
		}
		$originalHeader = $dataTemp;
		$originalHeader['G']['total'] = 'Total KW Generation'; 
		$originalHeader['D']['total'] = 'Total KW Distribituion'; 
		$originalHeader['diff'] = 'Difference in KW';
		$originalHeader['error'] = 'Error in %'; 
		$period = $this->dateRange;
		$period = array_reverse($period);
		$originalData = array();
		$totalData = array();
		if(count($period) > 0){
			foreach ($period as $key => $value) {
				if(count($dataTemp) > 0){
					foreach ($dataTemp as $keyDT => $valueDT) {
						foreach ($valueDT as $keyVDT => $valueVDT) {
							if(isset($dataTempNew[$keyDT][$keyVDT][$value])){
								$originalData[$value][$keyDT][$keyVDT] = $dataTempNew[$keyDT][$keyVDT][$value];
								$totalData[$value][$keyDT][$keyVDT] = $originalData[$value][$keyDT][$keyVDT];
							}else{
								$originalData[$value][$keyDT][$keyVDT] = 0;
								$totalData[$value][$keyDT][$keyVDT] = 0;
							}
						}
						if(isset($totalData[$value][$keyDT])){
							$originalData[$value][$keyDT]['total'] = array_sum($totalData[$value][$keyDT]);
							unset($totalData[$value][$keyDT][$keyVDT]);
						}
					}
					if(isset($originalData[$value])){
						$originalData[$value]['diff'] = $originalData[$value]['G']['total'] - $originalData[$value]['D']['total'];
						if($originalData[$value]['G']['total'] != 0){
							$originalData[$value]['error'] = round(($originalData[$value]['diff']/$originalData[$value]['G']['total']) * 100, 2);
						}else{
							$originalData[$value]['error'] = 'N/A';
						}
					}
				}
			}
		}
		$this->data['sheetData'][2]['merge'] = $this->data['sheetData'][1]['merge'];
		$this->data['sheetData'][2]['header'] = $originalHeader;
		$this->data['sheetData'][2]['data'] = $originalData;
		unset($originalHeader);unset($originalData);
	}

	private function _getKwhReadingCompressor(){
		$originalHeader['date'] = 'Date';
		$this->data['notConnetedMeter'] = array(6 => 'Compressor 2', 7 => 'Comp 7 (ZH7000)', 8 => 'Comp 9 (ZH15000+)', 9 => 'Comp 6 (ZR500VSD)', 10 => 'Comp 10 (ZH1600+)', 11 => 'Comp 11 (Cameron Turbine)', 12 => 'WATER PDB 5', 13 => 'COMPRESSOR WATER PDB 1', 14 => 'COMPRESSOR WATER PDB 2', 15 => 'WATER PDB 4');
		$this->data['auxArr'] = array(12,13,14);
		$deviceRelation =  modules::load('air/dashboard')->mappingAIRtoEMS();
		$airDetails = $this->ems_model->getAirMeter();
		$airTotal = count($airDetails);
		$this->_setKwhCompressorMerge($airTotal);
		if(count($airDetails) > 0){
			foreach ($airDetails as $key => $value) {
				$originalHeader[$value->meter_id] = $value->name;
			}
		}
		$this->data['sheetData'][3]['header'] = $originalHeader;
		$originalHeader['total'] = 'Total KWH Consumed';
		$this->data['sheetData'][4]['header'] = $originalHeader;
		$deviceIds = $deviceRelation['deviceIds'];
		$this->dataTimeInterval = 'PT24H00M00S';
		$this->startDateTime = $this->data['endDate'];
		$startD = $this->data['startDate'];
		$endD = $this->_dateAdd();
		$data = $this->ems_model->getAirEmsKwhData($startD, $endD, $deviceIds);
		$dataTempNew = array();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$dataTempNew[$value->device_id][$value->end_date_time_1] = $value->data_kwh;
			}
		}
		$period = $this->dateRange;
		$period = array_reverse($period);
		$orginalData['reading'] = array();
		$orginalData['consumed'] = array();
		if(count($period) > 0){
			foreach ($period as $keyP => $valueP) {
				if(count($deviceRelation['data']) > 0){
					$start = $this->startDateTime = $valueP;
					$end = date('Y-m-d', strtotime($this->_dateAdd()));
					$tottalArr = array();
					for ($i=1; $i < 6; $i++) { 
						if(isset($dataTempNew[$deviceRelation['data'][$i]][$valueP])){
							$orginalData['reading'][$valueP][$i] = $dataTempNew[$deviceRelation['data'][$i]][$valueP];
						}else{
							$orginalData['reading'][$valueP][$i] = 0;
						}
						if(isset($dataTempNew[$deviceRelation['data'][$i]][$start]) && isset($dataTempNew[$deviceRelation['data'][$i]][$end])){
							$orginalData['consumed'][$valueP][$i] = $dataTempNew[$deviceRelation['data'][$i]][$end] - $dataTempNew[$deviceRelation['data'][$i]][$start];
						}else{
							$orginalData['consumed'][$valueP][$i] = 0;
						}
						$tottalArr[] = $orginalData['consumed'][$valueP][$i];
					}
					$orginalData['consumed'][$valueP]['total'] = array_sum($tottalArr);
				}
			}
		}
		$this->data['sheetData'][3]['data'] = $orginalData['reading'];
		$this->data['sheetData'][4]['data'] = $orginalData['consumed'];
		unset($originalHeader);unset($dataTempNew);unset($orginalData);
	}

	private function _setKwhCompressorMerge($count = 5){
		$start = 'B';
		$m = 1;
		$color = array('9decd4');
		$end = $this->_getExcelCellChar($start, $count  - 1);
		$this->data['sheetData'][3]['merge'][] = array($start.'1:'.$end.'1', 'KWH Reading', $m, $color[0]);
		$end = $this->_getExcelCellChar($start, $count);
		$this->data['sheetData'][4]['merge'][] = array($start.'1:'.$end.'1', 'KWH Consumed', $m, $color[0]);
	}

	private function _getDatesFromRange($start, $end, $format = 'Y-m-d') {
	    $array = array();
	    $interval = new DateInterval('P1D');
	    $realEnd = new DateTime($end);
	    $realEnd->add($interval);
	    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
	    foreach($period as $date) { 
	        $array[] = $date->format($format); 
	    }
	    return $array;
	}

	private function _generateAndDownload(){
		if(count($this->data['sheetData'][0]['header']) > 1){
			$this->data['fileName'] = 'ems_report_'.$this->data['startDate'].'_'.$this->data['endDate'].'.xlsx';
			$this->load->library('ems_excel');
			$this->ems_excel->createMultipleSheet($this->data['titles'], $this->data['sheetData']);
			$this->ems_excel->generate();
			$this->ems_excel->download($this->data['fileName']);
		}else{
			$this->data['msg'] = "No Record Found.";
		}
	}


	private function _dateAdd(){
        $date = new DateTime($this->startDateTime);
        $date->add(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

    private function _dateSub(){
        $date = new DateTime($this->startDateTime);
        $date->sub(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

	


}

?>