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
		addPageDetails();
	}

	public function index(){
		$this->data = array();
		$this->data['all'] = null; 
		$startDate = '';
		$this->data['titles'] = array('Steam Report');
		$this->data['msg'] = '';
		$this->data['active_date'] = $this->fm_model->getLastDate();
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
				$this->_setHeaderMergeCellRowThree();
				$this->_setHeaderMergeCellRowFour();
				$this->_setHeaderMergeCellRowFive();
				$this->_generateAndDownload();
			}
		}
		
		$this->load->view('reportsteam/index', $this->data);
	}

	private function _setMeterData($startDate = '', $endDate = ''){
		$data = $this->fm_model->getCfmData($startDate, $endDate);
		$this->data['sheetData']['meter']['data'] = array();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$cfm = 0;
				if($value->next_data_cfm > 0 ){
					$cfm = ($value->next_data_cfm - $value->data_cfm);
				}
				$this->data['sheetData']['meter']['data'][$value->end_date][$value->meter_id] = array('meterRed' => $value->data_cfm, 'con' => $cfm, 'type' => $value->type);
			}
		}else{
			$this->data['msg'] = 'No Data Please.';
		}
		/*pr($this->data['sheetData']['meter']['data']);
		exit;*/
	}

	private function _setHeaderMergeCellRowOne($data = array()){
		$cellTitle = array('','Main Meter','114','106 Inlet','103 Inlet','108 inlet','New CRP','');
		$color = array('91def1');
		$merge = array(10,2,2,2,2,2,2,6);
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
		$cellTitle = array('From Power Plant','CVL-1,2 & SM-120','SM-270','EGB','','BS Wvg Sizing','TT process','TT Wvg Sizing','BS Cut & Sew + BSP ph-2','BS Process ph-1,3','Adv. Textile + NSC','Spinning-1 & 2','Spinning-3','Old Canteen','New Canteen','Line Losses');
		$color = array('91def1');
		$merge = array(4,2,2,2,2,2,2,2,2,2,1,1,1,1,1,1);
		$start = "B";
		$m = 1;
		foreach ($cellTitle as $key => $value) {
			$end = $this->_getExcelCellChar($start, $merge[$key] -1);
			$this->data['sheetData']['header'][2][] = array($start.'2:'.$end.'2', $cellTitle[$key], $m, $color[0]);
			$start = $this->_getExcelCellChar($start, $merge[$key]);
			$m = $m + $merge[$key];
		}
	}

	private function _setHeaderMergeCellRowThree($data = array()){
		$cellTitle = array('','43MW','80MW','','','','','','','','','','','','','','','');
		$color = array('91def1');
		$merge = array(1,2,2,2,2,2,2,2,2,2,2,2,1,1,1,1,1,1);
		$start = "A";
		$m = 1;
		foreach ($cellTitle as $key => $value) {
			$end = $this->_getExcelCellChar($start, $merge[$key] - 1);
			$this->data['sheetData']['header'][3][] = array($start.'3:'.$end.'3', $cellTitle[$key], $m , $color[0]);
			$start = $this->_getExcelCellChar($start, $merge[$key]);
			$m = $m + $merge[$key];
		}
	}

	private function _setHeaderMergeCellRowFour($data = array()){
		$color = array('7fbf9e');
		$merge = 1;
		$start = "A";
		$m = 1;
		foreach ($this->data['sheetData']['meter']['details'] as $key => $value) {
			if($key > 0 && $key < 23 && $key%2 == 1){
				$end = $this->_getExcelCellChar($start, $merge);
				$this->data['sheetData']['header'][4][] = array($start.'4:'.$end.'4', $value['name'], $m, $color[0]);
				$start = $this->_getExcelCellChar($start, 2);
				$m = $m + $merge + 1;
			}elseif($key == 0 || $key >= 23){
				$end = $this->_getExcelCellChar($start, $merge - 1);
				$this->data['sheetData']['header'][4][] = array($start.'4:'.$end.'4', $value['name'], $m, $color[0]);
				$start = $this->_getExcelCellChar($start, $merge);
				$m = $m + $merge;
			}
		}
		//pr($this->data['sheetData']['header'][4]);exit;
	}

	private function _setHeaderMergeCellRowFive($data = array()){
		$color = array('aeecf7');
		$merge = 1;
		$start = "A";
		$m = 1;
		$steamArr = array(2,4,6,8,10);
		//echo "string";exit;
		for ($i = 0; $i < 29; $i++) { 
			$name = 'Conspt.';
			if($i == 0){
				$name = 'Date';
			}elseif($i >= 1 && $i<=22){
				if($i%2 != 0){
					$name = 'Meter Rdg';
				}elseif(in_array($i, $steamArr)){
					$name = 'Steam';
				}
			}
			$end = $this->_getExcelCellChar($start, $merge - 1);
			$this->data['sheetData']['header'][5][] = array($start.'5:'.$end.'5', $name, $m, $color[0]);
			$start = $this->_getExcelCellChar($start, $merge);
			$m = $m + $merge;
		}
		/*pr($this->data['sheetData']['header'][4]);
		exit;*/
	}

	private function _getExcelCellChar($column = 'B', $step = 1){
		for($i = 0; $i < $step; $i++) {
		    $column++;
		}
		return $column;
	}

    private function _generateAndDownload(){
		if(count($this->data['sheetData']['meter']['data']) > 1){
			$this->data['fileName'] = 'steam_report_'.date('Y-m-d',strtotime($this->data['startDate'])).'_'.date('Y-m-d',strtotime($this->data['endDate'])).'.xlsx';
			$this->load->library('steam_excel');
			$this->steam_excel->createMultipleSheet($this->data['titles'], $this->data['sheetData']);
			$this->steam_excel->generate();
			$this->steam_excel->download($this->data['fileName']);
		}else{
			$this->data['msg'] = "No Record Found.";
		}
	}

	private function _getMeterDetailsArr(){
		$data = $this->fm_model->getMeterDetails();
		$retData = array();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$retData[$value->report_excel_position] = array(
					'meter_id' => $value->meter_id,
					'name' => $value->name,
					'short_desc' => $value->report_name
				);
			}
		}
		/*pr($retData,0);
		exit;*/
		$retData[0] = array('meter_id' => 0, 'name' => '', 'short_desc' => '');
		$retData[5] = array('meter_id' => 0, 'name' => '', 'short_desc' => '');
		$retData[9] = array('meter_id' => 0, 'name' => '', 'short_desc' => '');

		for ($i=2; $i <23 ; $i = $i+2) { 
			$retData[$i] = array('meter_id' => 0, 'name' => '', 'short_desc' => '');
		}
		for ($i=23; $i < 29; $i++) { 
			$retData[$i] = array('meter_id' => 0, 'name' => '', 'short_desc' => '');
		}
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

	public function indexOld(){
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
			/////////////////// Start Old CFM (Flow) Code 12.01.2018
			/// Get Date Wise Data Aggregator Data
			/*
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
			*/
			//////////////////// End Old CFM (Flow) Code 12.01.2018
			/////////////////// Start New Totaliser (Flow) Code 12.01.2018
			$begin = $this->startDateTime;//new DateTime($newStartDate);
			$end   = $this->endDateTime;//new DateTime($newEndDate);
			$arr = array();
			if(isset($begin) && $begin!='' && isset($end) && $end!=''){
				for($i = $begin; $i <= $end; $i=$this->_dateAddcustom($i,'PT24H00M00S')){

					//echo $i;
					if($i!=''){
						$arr[$i] = $this->fm_model->getTTL_flow_newDateDataTypeWise($i);
					}
				    
				}
			}
			

			$newArr = array();
			if(isset($arr) && count($arr)>0){
				foreach ($arr as $dkey => $dvalue) {
					foreach ($dvalue as $mkey => $mvalue) {	
						//if($mvalue->type=='main') continue;	
						if(!isset($newArr[$mvalue->type]['meter'][$mvalue->meter_id][$dkey]['flow'])) $newArr[$mvalue->type]['meter'][$mvalue->meter_id][$dkey]['flow'] = 0;	
						if(!isset($newArr[$mvalue->type]['meter'][$mvalue->meter_id][$dkey]['name'])) $newArr[$mvalue->type]['meter'][$mvalue->meter_id][$dkey]['name'] = 'NA';	
						$newArr[$mvalue->type]['meter'][$mvalue->meter_id][$dkey]['flow'] = isset($mvalue->data_cfm) ? $mvalue->data_cfm : 0;
						$newArr[$mvalue->type]['meter'][$mvalue->meter_id][$dkey]['name'] = isset($mvalue->name) ? $mvalue->name : 'NA';		
						
					}
					
				}
			}
			/*echo "<pre>";
			var_dump($newArr);*/
			
			$dataArray = array();
			if(isset($newArr) && count($newArr)>0){

				foreach ($newArr as $tkey => $tvalue) {
					if(isset($tvalue['meter']) && count($tvalue['meter'])>0){
						foreach ($tvalue['meter'] as $mmkey => $mmvalue) {
							
							if(isset($mmvalue) && count($mmvalue)>0){
								$descArray = array_reverse($mmvalue);								

								foreach ($descArray as $ddkey => $ddvalue) {

									$cDate = $ddkey;
									$lDate = $this->_dateSub($cDate,'PT24H00M00S');									
									//var_dump($cDate);								

									if(isset($lDate) && $lDate!='' && isset($ddvalue['flow']) && $ddvalue['flow']!='' && isset($descArray[$lDate]['flow']) && $descArray[$lDate]['flow']!=''){

										$ddddate = date('Y-m-d',strtotime($lDate));
										if(!isset($dataArray[$ddddate][$tkey]['meter'][$mmkey]['flow'])) $dataArray[$ddddate][$tkey]['meter'][$mmkey]['flow'] = 0;
										if(!isset($dataArray[$ddddate][$tkey]['meter'][$mmkey]['name'])) $dataArray[$ddddate][$tkey]['meter'][$mmkey]['name'] = 'NA';
										//var_dump($descArray);
										$dataArray[$ddddate][$tkey]['meter'][$mmkey]['name'] = isset($ddvalue['name']) ? $ddvalue['name'] : 'NA';	
										if($ddvalue['flow']>0 && $descArray[$lDate]['flow']>0){
											$dataArray[$ddddate][$tkey]['meter'][$mmkey]['flow'] = (($ddvalue['flow'] - $descArray[$lDate]['flow']));
										}
										
									}
								}
							}
							
						}
					}
				}
			}
			/*echo "<pre>";
			var_dump($dataArray);
			die();*/
			// Iterate Data Structure Depends on Excel Format Data Structure
			$newDataArray = $this->_regenarateArrayForExcel1($dataArray);
			/*echo "<pre>";
			var_dump($newDataArray);
			die();*/
			//////////////////// End New Totaliser (Flow) Code 12.01.2018
			// Generate Excel Sheet Name And Call The Excel Generator Library
			$fileName = 'fm_'.date('Y-m-d');
			$this->load->library('excel');
			$this->excel->generateAndDownload($fileName,$newDataArray);

			//$this->data['data'] = $resultArray;

			//$this->data['all'] = $this->load->view('reportsteam/all_day',$this->data, true);
		}
		
		$this->load->view('reportsteam/index', $this->data);
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
							$newStrucArray[$i][] = isset($metervalue['flow']) ? number_format(($metervalue['flow'] * 24),3) : '--';	
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

	private function _regenarateArrayForExcel1($dataArray){

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
							$totalTypeWiseFlow[$typekey]+=	isset($metervalue['flow']) ? ($metervalue['flow']) : '0';		
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
    
	
}