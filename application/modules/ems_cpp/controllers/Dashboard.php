<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MX_Controller {
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
		addPageDetails();
	}

	public function index(){
		$this->data['startDateShow'] = $this->currentStartDateTime;
		$this->data['endDateShow'] = $this->currentEndDateTime;
		$this->data['time'] = strtotime($this->currentStartDateTime);

		$this->_getGapTable();
		$this->_getElectricityTable();
		$this->_getSteamData();

		$this->data['compressAirData'] = $this->ems_model->getCompressAirData(1,$this->currentStartDateTime);
		$this->data['all'] = $this->load->view('dashboard/all', $this->data, true);
		unset($this->data['meterWiseDataCpp']);
		unset($this->data['emsCppDataLoss']);
		unset($this->data['emsDataLoss']);
		$this->load->view('dashboard/index', $this->data);
	}

	private function _getGapTable(){
		$emsCppArr = '5,1,2,3,4,6';
		$emsArr = '2,3,30,31,34,4';

		$emsCppDataLoss = $this->ems_model->getEmsCppLoss($this->currentStartDateTime, $emsCppArr);
		$emsDataLoss = $this->ems_model->getEmsLoss($this->currentStartDateTime, $emsArr);
		$kw_loss = $emsCppDataLoss[0]->data - $emsDataLoss[0]->data;
		$kw_loss_per = 0;
		if($emsCppDataLoss[0]->data != 0){
			$kw_loss_per = (($emsCppDataLoss[0]->data - $emsDataLoss[0]->data)/$emsCppDataLoss[0]->data)*100;
		}

		$steamDeltaData = $this->_getSteamDeltaData();

		$time = $this->data['time'];
		$this->data['gap'][0] = array('name' => 'Electricity (KW)', 'loss_val' => $kw_loss,'loss_per' => $kw_loss_per,'chart_link_abs' => 'ems/dashboard/showCppWillChartLoss/abs/'.$time, 'chart_link_per' => 'ems/dashboard/showCppWillChartLoss/per/'.$time);
		$this->data['gap'][1] = array('name' => 'Delta Flow (Steam)', 'loss_val' => $steamDeltaData['deltaFlowAbs'],'loss_per' => $steamDeltaData['deltaFlowPer'],'chart_link_abs' => 'fm/dashboard/getMeterChart_CPP_PPHvsWILgen/flow', 'chart_link_per' => 'fm/dashboard/getMeterChart_CPP_PPHvsWILgen/flow');
		$this->data['gap'][2] = array('name' => 'Delta Pressure (Steam)', 'loss_val' => $steamDeltaData['deltaPresAbs'],'loss_per' => $steamDeltaData['deltaPresPer'],'chart_link_abs' => 'fm/dashboard/getMeterChart_CPP_PPHvsWILgen/pres', 'chart_link_per' => '');
		$this->data['gap'][3] = array('name' => 'Delta Temperature (Steam)', 'loss_val' => $steamDeltaData['deltaTempAbs'],'loss_per' => $steamDeltaData['deltaTempPer'],'chart_link_abs' => 'fm/dashboard/getMeterChart_CPP_PPHvsWILgen/temp', 'chart_link_per' => '');

	}

	private function _getSteamDeltaData(){
		$this->data['steamTableData'] = array();
		$retData['deltaFlowAbs'] = 0;
		$retData['deltaFlowPer'] = 0;
		$retData['deltaPresAbs'] = 0;
		$retData['deltaPresPer'] = 'NA';
		$retData['deltaTempAbs'] = 0;
		$retData['deltaTempPer'] = 'NA';
		$dataLoggerFlowIds = '2,3';
		$dataLoggePreTempIds = 4;
		$steamIds = '1,2';
		$dataLoggerFlowData = $this->ems_model->getDataLoggerFlowData($this->currentStartDateTime,$dataLoggerFlowIds);
		$dataLoggePreTempData = $this->ems_model->getDataLoggePreTempData($this->currentStartDateTime,$dataLoggePreTempIds);
		$steamData = $this->ems_model->getGapSteamData($this->currentStartDateTime,$steamIds);
		if(isset($dataLoggerFlowData[0]->sum_flow) && isset($steamData[0]->sum_flow)){
			$this->data['steamTableData']['gen_flow_last_15'] = $dataLoggerFlowData[0]->sum_flow;
			$this->data['steamTableData']['dist_flow_last_15'] = $steamData[0]->sum_flow;
			$retData['deltaFlowAbs'] = $dataLoggerFlowData[0]->sum_flow - $steamData[0]->sum_flow;
			if($dataLoggerFlowData[0]->sum_flow != 0){
				$retData['deltaFlowPer'] = ($retData['deltaFlowAbs'] / $dataLoggerFlowData[0]->sum_flow) * 100;
			}
		}
		
		if(isset($dataLoggePreTempData[0]->pressure) && isset($steamData[0]->avg_pre)){
			$retData['deltaPresAbs'] = $dataLoggePreTempData[0]->pressure - $steamData[0]->avg_pre;
			$this->data['steamTableData']['gen_pres_last_15'] = $dataLoggePreTempData[0]->pressure;
			$this->data['steamTableData']['dist_pres_last_15'] = $steamData[0]->avg_pre;
		}
		if(isset($dataLoggePreTempData[0]->temp) && isset($steamData[0]->avg_temp)){
			$retData['deltaTempAbs'] = $dataLoggePreTempData[0]->temp - $steamData[0]->avg_temp;
			$this->data['steamTableData']['gen_temp_last_15'] = $dataLoggePreTempData[0]->temp;
			$this->data['steamTableData']['dist_temp_last_15'] = $steamData[0]->avg_temp;
		}

		$this->data['steamTableData']['gen_flow_avg'] = $this->ems_model->getSteamDataloggerCurrentMonthAvg('dl', $dataLoggerFlowIds);
		$this->data['steamTableData']['dist_flow_avg'] = $this->ems_model->getSteamDataloggerCurrentMonthAvg('steam', $steamIds);
		//echo "<pre>";print_r($this->data['steamTableData']);
		return $retData;
	}

	private function _getElectricityTable(){
		$emsCppArr = '5,1,2,3,4';
		$emsArr = '2,3,30,31,34';

		$emsCppDataLoss = $this->ems_model->getEmsCppLoss($this->currentStartDateTime, $emsCppArr);
		$emsDataLoss = $this->ems_model->getEmsLoss($this->currentStartDateTime, $emsArr);
		$emsCppDataLossMonthly = $this->ems_model->getEmsCppMonthlyData($emsCppArr);
		$emsDataLossMonthly = $this->ems_model->getEmsMonthlyData($emsArr);

		$time = $this->data['time'];
		$this->data['electricity'][0] = array('name' => 'Total Generation Sum of  ( WIL-1,2,3,4,5 )', 'kw_15' => $emsCppDataLoss[0]->data,'kw_avg_month' => $emsCppDataLossMonthly[0]->data,'chart_link_15' => 'ems_cpp/dashboard/showPPtotalChart/wil/'.$time,'chart_link_month' => 'ems_cpp/dashboard/showPPtotalChartMonthly/wil/'.$time);
		$this->data['electricity'][1] = array('name' => 'Total Distribution Sum of  ( 80MWIC-1,2,3,4,5 )', 'kw_15' => $emsDataLoss[0]->data,'kw_avg_month' => $emsDataLossMonthly[0]->data,'chart_link_15' => 'ems_cpp/dashboard/showPPtotalChart/80mw/'.$time, 'chart_link_month' => 'ems_cpp/dashboard/showPPtotalChartMonthly/80mw/'.$time);
	}

	private function _getSteamData(){
		$gen_flow_last_15 = 0;$dist_flow_last_15 = 0;
		$gen_flow_avg = 0;$dist_flow_avg = 0;
		$gen_pres_last_15 = 0;$dist_pres_last_15 = 0;
		$gen_temp_last_15 = 0;$dist_temp_last_15 = 0;
		if(isset($this->data['steamTableData']['gen_flow_last_15'])){
			$gen_flow_last_15 = $this->data['steamTableData']['gen_flow_last_15'];
		}
		if(isset($this->data['steamTableData']['dist_flow_last_15'])){
			$dist_flow_last_15 = $this->data['steamTableData']['dist_flow_last_15'];
		}
		if(isset($this->data['steamTableData']['gen_flow_avg'])){
			$gen_flow_avg = $this->data['steamTableData']['gen_flow_avg'][0]->data;
		}
		if(isset($this->data['steamTableData']['dist_flow_avg'])){
			$dist_flow_avg = $this->data['steamTableData']['dist_flow_avg'][0]->data;
		}
		if(isset($this->data['steamTableData']['gen_pres_last_15'])){
			$gen_pres_last_15 = $this->data['steamTableData']['gen_pres_last_15'];
		}
		
		if(isset($this->data['steamTableData']['dist_pres_last_15'])){
			$dist_pres_last_15 = $this->data['steamTableData']['dist_pres_last_15'];
		}
		if(isset($this->data['steamTableData']['gen_temp_last_15'])){
			$gen_temp_last_15 = $this->data['steamTableData']['gen_temp_last_15'];
		}
		if(isset($this->data['steamTableData']['dist_temp_last_15'])){
			$dist_temp_last_15 = $this->data['steamTableData']['dist_temp_last_15'];
		}



		$this->data['steam'][0] = array(
				'name' => 'Total Generation Sum of (Turbo Outlet Steam & 30 TPH Steam)', 
				'flow_last_15' => $gen_flow_last_15,'flow_last_15_url'=> 'fm/dashboard/getMeterChartTotal_CPP/flow/gen',
				'flow_avg' => $gen_flow_avg,'flow_avg_url'=> 'ems_cpp/dashboard/showSteamDataloggerChartMonthly/dl',
				'pres_last_15' => $gen_pres_last_15,'pres_last_15_url'=> 'fm/dashboard/getMeterChartTotal_CPP/pres/gen',
				'temp_last_15' => $gen_temp_last_15,'temp_last_15_url'=> 'fm/dashboard/getMeterChartTotal_CPP/temp/head');

		$this->data['steam'][1] = array(
			'name' => 'Total Distribution Sum of (DG Header Point 1 & 2)', 
			'flow_last_15' => $dist_flow_last_15,'flow_last_15_url' => 'fm/dashboard/getMeterChartTotal/flow/gen',
			'flow_avg' => $dist_flow_avg,'flow_avg_url'=> 'ems_cpp/dashboard/showSteamDataloggerChartMonthly/steam',
			'pres_last_15' => $dist_pres_last_15,'pres_last_15_url'=>'fm/dashboard/getMeterChartTotal/pres/gen', 
			'temp_last_15' => $dist_temp_last_15,'temp_last_15_url' => 'fm/dashboard/getMeterChartTotal/temp/gen');
	}
	
	public function live(){
		//die("Coming soon.");
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

	

	private function _setCurrentDateTime(){
		$this->dataTimeInterval = 'PT00H15M00S';
		$currentDate = $this->ems_model->getCurrent();
		$this->currentStartDateTime = $this->startDateTime = $currentDate[0]->end_date_time;
		$this->currentEndDateTime = $this->_dateAdd();
		/*$this->currentStartDateTime = $this->startDateTime = '2017-11-25 15:30:00';
		$this->currentEndDateTime = $this->_dateAdd();*/
	}

	public function showGraph(){
		$this->data['device_id'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$type = 'ems';
    	$data = $this->ems_model->getCurrent32DataSet($this->data['device_id'], $this->data['paramVal'], $type);
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		$maxMin = array();
    		$this->data['meterName'] = $data[0]->device_name;
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
		$this->load->view('dashboard/chart', $this->data);
	}

	public function showGraph_Air(){
		$this->data['device_id'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$this->data['meterName'] = $this->uri->segment(6);
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$type = 'ems';
    	$data = $this->ems_model->getCurrent32DataSet($this->data['device_id'], $this->data['paramVal'], $type);
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		$maxMin = array();

    		$this->data['airMeterName'] = array(1=>"IR Compressor",2=>"Compressor 3",3=>"Compressor 2",4=>"Compressor 4",5=>"Compressor 900 VSD",6 => 'Comp 1 (ZH15000)', 7 => 'Comp 7 (ZH7000)', 8 => 'Comp 9 (ZH15000+)', 9 => 'Comp 6 (ZR500VSD)', 10 => 'Comp 10 (ZH1600+)', 11 => 'Comp 11 (Cameron Turbine)', 12 => 'WATER PDB 5', 13 => 'COMPRESSOR WATER PDB 1', 14 => 'COMPRESSOR WATER PDB 2', 15 => 'WATER PDB 4');
		
    		$this->data['meterName'] = isset($this->data['airMeterName'][$this->data['meterName']]) ? $this->data['airMeterName'][$this->data['meterName']] : 'NA';//$data[0]->device_name;

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
		$this->load->view('dashboard/chart', $this->data);
	}

	public function showGraphTotal(){
    	$this->data['type'] = $this->uri->segment(4);
		$this->data['typeId'] = $this->uri->segment(5);
		$this->data['meterNameColumn'] = $column = $this->uri->segment(6);
		$endDate = $this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(7));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();
		if($this->data['type'] == 'G'){
			$this->data['meterName'] = 'Total Data of Generation '.$this->data['typeId'];
		}else{
			$this->data['meterName'] = 'Total Data of Distribution '.$this->data['typeId'];
		}
		$data = $this->ems_model->getShiftWiseDataSetChartTotal($startDate, $endDate, $this->data['type'], $this->data['typeId'], $column);
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
		$this->load->view('dashboard/chart', $this->data);
    }

    public function showGraphTotalLoss(){
		$this->data['typeId'] = $this->uri->segment(4);
		$this->data['meterNameColumn'] = $column = 'KW';
		$endDate = $this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(5));
		$perAbs = $this->uri->segment(6);
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();
		$this->data['meterName'] = 'Total Data of Generation '.$this->data['typeId'].' Distribution '.$this->data['typeId'] ;
		if($perAbs == 'abs'){
			$this->data['meterNameColumn'] = 'Loss in Absolute Value ';
		}else{
			$this->data['meterNameColumn'] = 'Loss in %';
		}
		$dataGen = $this->ems_model->getShiftWiseDataSetChartTotal($startDate, $endDate, 'G', $this->data['typeId'], $column);
    	$dataDist = $this->ems_model->getShiftWiseDataSetChartTotal($startDate, $endDate, 'D', $this->data['typeId'], $column);
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

	public function showPPchart(){
		$this->data['type'] = $this->uri->segment(4);
		$this->data['device_id'] = $this->uri->segment(5);
		$this->data['paramVal'] = $this->uri->segment(6);
		$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$data = $this->ems_model->getCurrent32DataSetPP($this->data['device_id'], $this->data['paramVal'], $this->data['type']);
    	$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->device_name;
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.abs(round($value->data,3)).'"
		        },';
		        $maxMin[] = abs(round($value->data,3));
    		}
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	$this->data['dataSetConsis'] = '';
		$this->load->view('dashboard/chart', $this->data);
	}

	public function showPPtotalChart(){
		$emsCppArr = '1,2,3,4,5';
		$emsArr = '2,3,30,31,34';
		$this->data = array();
		$this->data['type'] = $this->uri->segment(4);
		$this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(5));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();
		if($this->data['type'] == 'wil'){
			$this->data['meterName'] = 'Total Generation Sum of  ( WIL - 1,2,3,4,5)';
			$ids = $emsCppArr; 
		}else{
			$this->data['meterName'] = 'Total Distribution Sum of  ( 80MWIC-1,2,3,4,5 )';
			$ids = $emsArr; 
		}
    	$this->data['meterNameColumn'] = 'Total KW';
    	$this->data['chartData'] = $this->ems_model->showElectricityTableChart($this->data['type'], $ids, $startDate, $endDate);

		/*$this->data['type'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$endDate = $this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(6));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();
		
		$this->data['meterName'] = 'Total CPP';
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$data = $this->ems_model->showPPtotalChart($this->data['type'], $this->data['paramVal'], $startDate, $endDate);
    	//$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$maxMin = array();
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		//$this->data['meterName'] = $data[0]->device_name;
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.abs(round($value->data,3)).'"
		        },';
		        $maxMin[] = abs(round($value->data,3));
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = '';*/
		$this->load->view('dashboard/chart', $this->data);
	}

	public function showPPtotalChartMonthly(){
		$emsCppArr = '1,2,3,4,5';
		$emsArr = '2,3,30,31,34';
		$this->data = array();
		$this->data['type'] = $this->uri->segment(4);
		$this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(5));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();
		if($this->data['type'] == 'wil'){
			$this->data['meterName'] = 'Total Generation Sum of  ( WIL - 1,2,3,4,5)';
			$ids = $emsCppArr; 
		}else{
			$this->data['meterName'] = 'Total Distribution Sum of  ( 80MWIC-1,2,3,4,5 )';
			$ids = $emsArr; 
		}
    	$this->data['meterNameColumn'] = 'Average (KW) Month wise';
    	$this->data['chartData'] = $this->ems_model->getMonthWiseAvg($this->data['type'], $ids);
		$this->load->view('dashboard/chart_bar', $this->data);
	}

	public function showSteamDataloggerChartMonthly(){
		$dataLoggerFlowIds = '2,3';
		$steamIds = '1,2';
		$this->data = array();
		$this->data['type'] = $this->uri->segment(4);
		if($this->data['type'] == 'dl'){
			$this->data['meterName'] = 'Total Generation Sum of (Turbo Outlet Steam & 30 TPH Steam)';
			$ids = $dataLoggerFlowIds; 
		}else{
			$this->data['meterName'] = 'Total Distribution Sum of (DG Header Point 1 & 2)';
			$ids = $steamIds; 
		}
    	$this->data['meterNameColumn'] = 'Average (KW) of this Month';
    	$this->data['chartData'] = $this->ems_model->getSteamDataloggerMonthWiseAvg($this->data['type'], $ids);
		$this->load->view('dashboard/chart_bar', $this->data);
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


    public function showGenDistChart(){
    	$this->data['type_text'] = $this->uri->segment(4);
		$this->data['type_level'] = $this->uri->segment(5);
		$this->data['paramVal'] = $this->uri->segment(6);
		$endDate = $this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(7));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();

		$this->data['meterName'] = ($this->data['type_text'] == 'G')?'Generation ':'Distribution ';
		$this->data['meterName'] = $this->data['meterName']. $this->data['type_level'];
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$data = $this->ems_model->getCurrent32DataSetGenDist($this->data['type_text'], $this->data['type_level'], $this->data['paramVal'], $startDate, $endDate);
    	//$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$maxMin = array();
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		//$this->data['meterName'] = $data[0]->device_name;
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.abs(round($value->data,3)).'"
		        },';
		        $maxMin[] = abs(round($value->data,3));
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = '';
		$this->load->view('dashboard/chart', $this->data);
    }

    public function showGenDistChartTotal(){
    	$this->data['type_text'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$endDate = $this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(6));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();

		$this->data['meterName'] = ($this->data['type_text'] == 'G')?'Generation ':'Distribution ';
		$this->data['meterName'] = 'Total '.$this->data['meterName'];
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$data = $this->ems_model->showGenDistChartTotal($this->data['type_text'], $this->data['paramVal'], $startDate, $endDate);
    	//$data = array_reverse($data);
    	$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$maxMin = array();
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		//$this->data['meterName'] = $data[0]->device_name;
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.abs(round($value->data,3)).'"
		        },';
		        $maxMin[] = abs(round($value->data,3));
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = '';
		$this->load->view('dashboard/chart', $this->data);
    }


    public function showGenDistChartLoss(){
    	$perAbs = $this->uri->segment(4);
		$this->data['paramVal'] = 'KW';
		$endDate = $this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(5));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();
		$this->data['meterName'] = 'Distribution Loss';
    	$this->data['meterNameColumn'] = 'Loss';
    	$dataGen = $this->ems_model->showGenDistChartLoss($startDate, $endDate, 'G');
    	$dataDist = $this->ems_model->showGenDistChartLoss($startDate, $endDate, 'D');
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
    	//$data = array_reverse($data);
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
    	$this->data['consisIndex'] = 'no';
    	$this->data['dataSetConsis'] = '';
		$this->load->view('dashboard/chart', $this->data);

    }

    public function showCppWillChartLossTotal(){
    	$this->data['type'] = $this->uri->segment(4);
		$this->data['paramVal'] = 'KW';
		$endDate = $this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(5));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();
		if($this->data['type'] == 'ems'){
			$emsArr = $meterIds = '2,3,30,31,34,4';
			$this->data['meterName'] = 'WIL Meter';
    		$this->data['meterNameColumn'] = 'KW';
		}else{
			$emsCppArr = $meterIds = '5,1,2,3,4,6';
			$this->data['meterName'] = 'CPP Meter';
    		$this->data['meterNameColumn'] = 'KW';
		}
		$data = $this->ems_model->showCppWillChartLossTotal($startDate, $endDate, $meterIds, $this->data['type']);
		$this->data['consisIndex'] = 'no';
    	$dataSet = '';
    	$maxMin = array();
    	$this->data['min_val'] = 0;
    	$this->data['max_val'] = 10;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.abs(round($value->data,3)).'"
		        },';
		        $maxMin[] = abs(round($value->data,3));
    		}
    		$this->data['max_val'] = intval(max($maxMin)) + 1;
    		$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = '';
		$this->load->view('dashboard/chart', $this->data);
    }

     public function showCppWillChartLoss(){
    	$perAbs = $this->uri->segment(4);
		$this->data['paramVal'] = 'KW';
		$endDate = $this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(5));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();
		$this->data['meterName'] = ($perAbs == 'per')?'Loss in %':'Loss';
    	$this->data['meterNameColumn'] = 'Loss';
    	$emsArr = '2,3,30,31,34,4';
    	$emsCppArr = '5,1,2,3,4,6';
    	$dataGen = $this->ems_model->showCppWillChartLossTotal($startDate, $endDate, $emsCppArr, 'ems_cpp');
    	$dataDist = $this->ems_model->showCppWillChartLossTotal($startDate, $endDate, $emsArr, 'ems');
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
    	$this->data['consisIndex'] = 'no';
    	$this->data['dataSetConsis'] = '';
		$this->load->view('dashboard/chart', $this->data);
    }

    public function showPPWILchartLoss(){
    	$emsCppArr = $this->uri->segment(4);
    	$emsArr = $this->uri->segment(5);
    	$perAbs = $this->uri->segment(7);
		$this->data['paramVal'] = 'KW';
		$endDate = $this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(6));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();
		$this->data['meterName'] = ($perAbs == 'per')?'Loss in %':'Loss';
    	$this->data['meterNameColumn'] = 'Loss';
    	/*$emsArr = '2,3,30,31,34,4';
    	$emsCppArr = '5,1,2,3,4,6';*/
    	$dataGen = $this->ems_model->showCppWillChartLossTotal($startDate, $endDate, $emsCppArr, 'ems_cpp');
    	$dataDist = $this->ems_model->showCppWillChartLossTotal($startDate, $endDate, $emsArr, 'ems');
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
    	$this->data['consisIndex'] = 'no';
    	$this->data['dataSetConsis'] = '';
		$this->load->view('dashboard/chart', $this->data);
    }

    

    

	
}