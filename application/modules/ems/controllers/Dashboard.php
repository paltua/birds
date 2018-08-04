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
		//echo $this->currentStartDateTime = "2017-09-19 08:00:00";
		$this->data['genDistAllData'] = $this->ems_model->getGenDistAllDataReport($this->currentStartDateTime,$this->currentEndDateTime);
		$this->data['meterWiseDataCpp'] = $this->ems_model->getDeviceWiseDataCpp($this->currentStartDateTime);
				
		$emsCppArr = '5,1,2,3,4,6';
		$emsArr = '2,3,30,31,34,4';

		$this->data['emsCppDataLoss'] = $this->ems_model->getEmsCppLoss($this->currentStartDateTime, $emsCppArr);
		$this->data['emsDataLoss'] = $this->ems_model->getEmsLoss($this->currentStartDateTime, $emsArr);

		$this->data['startDateShow'] = $this->currentStartDateTime;
		$this->data['endDateShow'] = $this->currentEndDateTime;
		
		$this->data['all'] = $this->load->view('dashboard/all', $this->data, true);
		unset($this->data['meterWiseDataCpp']);
		unset($this->data['emsCppDataLoss']);
		unset($this->data['emsDataLoss']);
		$this->load->view('dashboard/index', $this->data);
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

	private function _makingTypeData(){
		$masterArr = array();
		if(count($this->data['meterWiseData']) > 0){
			foreach ($this->data['meterWiseData'] as $value) {
				$masterArr[$value->type][$value->device_id]['name'] = $value->device_name;
				$masterArr[$value->type][$value->device_id]['data']['KW'][] = $value->KW;
				$masterArr[$value->type][$value->device_id]['data']['PF'][] = $value->PF;
				$masterArr[$value->type][$value->device_id]['data']['Amps'][] = $value->Amps;
				$masterArr[$value->type][$value->device_id]['data']['HZ'][] = $value->HZ;
				$masterArr[$value->type][$value->device_id]['data']['Volt'][] = $value->Volt;
				$masterArr[$value->type][$value->device_id]['data']['KWPF'][] = $value->KWPF;
			}
		}
		return $masterArr;
	}

	private function _setCurrentDateTime(){
		$this->dataTimeInterval = 'PT00H15M00S';
		$currentDate = $this->ems_model->getCurrent();
		$this->currentStartDateTime = $this->startDateTime = $currentDate[0]->end_date_time;
		$this->currentEndDateTime = $this->_dateAdd();
		/*$this->currentEndDateTime = $this->startDateTime = $currentDate[0]->end_date_time;
		$this->currentStartDateTime = $this->_dateSub();*/
	}

	public function showGraph(){
		$this->data['device_id'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$type = 'ems';
    	$data = $this->ems_model->getCurrent32DataSet($this->data['device_id'], $this->data['paramVal'], $type);
    	$this->data['chartData'] = $data = array_reverse($data);
    	$this->data['meterName'] = $data[0]->device_name;
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
    	$dataSet = '';
    	if(count($data) > 0){
    		$this->data['airMeterName'] = array(1=>"IR Compressor",2=>"Compressor 3",3=>"Comp 1 (ZH15000)",4=>"Compressor 4",5=>"Compressor 900 VSD",6 => 'Compressor 2', 7 => 'Comp 7 (ZH7000)', 8 => 'Comp 9 (ZH15000+)', 9 => 'Comp 6 (ZR500VSD)', 10 => 'Comp 10 (ZH1600+)', 11 => 'Comp 11 (Cameron Turbine)', 12 => 'WATER PDB 5', 13 => 'COMPRESSOR WATER PDB 1', 14 => 'COMPRESSOR WATER PDB 2', 15 => 'WATER PDB 4');
		
    		$this->data['meterName'] = isset($this->data['airMeterName'][$this->data['meterName']]) ? $this->data['airMeterName'][$this->data['meterName']] : 'NA';//$data[0]->device_name;

    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2).'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('am_chart/line',$this->data);
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
		$this->data['chartData'] = $data = array_reverse($data);
    	
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
		$this->data['meterName'] = 'Distribution loss '.(($this->data['typeId'] == 1)?'A':(($this->data['typeId'] == 2)?'B':'C')) ;
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
    	$this->data['chartData'] = $data;
    	$this->data['perAbs'] = $perAbs;
		$this->load->view('dashboard/chart_other', $this->data);
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
    	
    	$this->data['chartData'] = $data = array_reverse($data);
    	$this->data['meterName'] = $data[0]->device_name;
		$this->load->view('dashboard/chart', $this->data);
	}

	public function showPPtotalChart(){
		$this->data['type'] = $this->uri->segment(4);
		$this->data['paramVal'] = $this->uri->segment(5);
		$endDate = $this->startDateTime = date('Y-m-d H:i:s',$this->uri->segment(6));
		$this->dataTimeInterval = 'PT00H15M00S';
		$endDate = $this->startDateTime = $this->_dateAdd();
		$this->dataTimeInterval = 'PT08H00M00S';
		$startDate = $this->_dateSub();
		
		$this->data['meterName'] = 'Total CPP';
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$this->data['chartData'] = $data = $this->ems_model->showPPtotalChart($this->data['type'], $this->data['paramVal'], $startDate, $endDate);
    	
		$this->load->view('dashboard/chart', $this->data);
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
		$this->data['meterName'] = 'DG House Bus-';
		$this->data['meterName'] .=  ($this->data['type_level'] == 1)?'A':(($this->data['type_level'] == 2)?'B':'C');
		$this->data['meterName'] .= ($this->data['type_text'] == 'G')?' Receiving ':' Distribution ';
		
    	$this->data['meterNameColumn'] = $this->data['paramVal'];
    	$this->data['chartData'] = $data = $this->ems_model->getCurrent32DataSetGenDist($this->data['type_text'], $this->data['type_level'], $this->data['paramVal'], $startDate, $endDate);
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
    	$this->data['chartData'] = $data = $this->ems_model->showGenDistChartTotal($this->data['type_text'], $this->data['paramVal'], $startDate, $endDate);
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
		$this->data['meterName'] = 'Distribution Loss '.(($perAbs == 'per')?'in %':'');
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
    	
    	$this->data['chartData'] = $data;
    	$this->data['perAbs'] = $perAbs;
		$this->load->view('dashboard/chart_other', $this->data);

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
		$this->data['chartData'] = $data = $this->ems_model->showCppWillChartLossTotal($startDate, $endDate, $meterIds, $this->data['type']);
		
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
    			/*$maxMin['per'][] = $data[$valueD->end_date_time]['per'];
    			$maxMin['abs'][] = $data[$valueD->end_date_time]['abs'];*/
    		}
    	}
    	
    	$this->data['chartData'] = $data;
    	$this->data['perAbs'] = $perAbs;
		$this->load->view('dashboard/chart_other', $this->data);
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
    			/*$maxMin['per'][] = $data[$valueD->end_date_time]['per'];
    			$maxMin['abs'][] = $data[$valueD->end_date_time]['abs'];*/
    		}
    	}
    	
    	$this->data['chartData'] = $data;
    	$this->data['perAbs'] = $perAbs;
		$this->load->view('dashboard/chart_other', $this->data);
    }

    

    

	
}