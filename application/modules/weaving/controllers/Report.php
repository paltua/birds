<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Report extends MX_Controller {
	public $data = array();
	public $currentStartDateTime = '';
	public $currentEndDateTime = '';
	public $dataTimeInterval = '';
	public $shiftViewArr = array(1 => 'Shift 1',2 => 'Shift 2', 3 => 'Shift 3');
	public $shiftStartHour = 8;
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->load->model('weaving_model');
		$this->load->model('report_model');
		//$this->_setCurrentDateTime();
		addPageDetails();
	}

	public function day(){
		$this->startDateTime = date('Y-m-d');
		$this->dataTimeInterval = 'PT24H00M00S';
		$this->data['notToday'] = $this->_dateSub();
		$this->data['selectedDate'] = $this->data['selectedEndDate'] = '';
		$this->data['all'] = '';
		$this->data['showData'] = 0;
		$startDate = $this->data['startDateShow'] = '';
		$endDate = $this->data['endDateShow'] = '';
		$this->data['table'][1] = array();
		$this->data['table'][2]['data'] = array();
		if($this->input->post('go')){
			$this->data['showData'] = 1;
			$this->data['selectedDate'] = $this->startDateTime = $this->input->post('selectedDate');
			$this->dataTimeInterval = 'PT00H30M00S';
			$startDate = $this->data['startDateShow'] = $this->_dateAdd();
			$this->data['selectedEndDate'] = $this->startDateTime = $this->input->post('selectedEndDate');
			$this->dataTimeInterval = 'PT24H30M00S';
			$endDate = $this->data['endDateShow'] = $this->_dateAdd();
			//$this->data['table'][2]['data'] = $this->report_model->getLoomWiseProduction($startDate, $endDate);
			$this->data['table'][1]['cmpx'] = $this->report_model->getTotalCmpxProduceDay($startDate, $endDate);
			$this->data['table'][1]['cfm'] = $this->report_model->getTotalCfmConsumptionDay($startDate, $endDate);
			$this->data['table'][1]['press'] = $this->report_model->getTotalPressDay($startDate, $endDate);
			//$this->data['table'][1]['kw'] = $this->report_model->getTotalKwConsumptionDay($startDate, $endDate, 0);
			$this->data['table'][1]['hplant_kw'] = $this->report_model->getTotalKwConsumptionDay($startDate, $endDate, 1) * 24;
			$this->data['table'][1]['loom_kw'] = $this->report_model->getTotalKwConsumptionDay($startDate, $endDate, 2) * 24;
			$this->data['table'][1]['kw'] = $this->data['table'][1]['hplant_kw'] + $this->data['table'][1]['loom_kw'];

			$this->data['table'][2]['data'] = $this->report_model->getLoomStyleWiseDataDay($startDate, $endDate);

			$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
		}

		$this->data['all'] = $this->load->view('report/day/all', $this->data, true);
		$this->load->view('report/day/index', $this->data);
	}

	public function shift(){
		$this->data['shiftViewArr'] = $this->shiftViewArr;
		$this->data['selectedDate'] = '';
		$this->data['selectedShift'] = 1;
		$this->data['all'] = '';
		$this->data['showData'] = 0;
		$startDate = $this->data['startDateShow'] = '';
		$endDate = $this->data['endDateShow'] = '';
		$this->data['table'][1] = array();
		$this->data['table'][2]['data'] = array();
		if($this->input->post('go')){
			$this->data['showData'] = 1;
			$this->data['selectedDate'] = $this->startDateTime = $selectdate = $this->input->post('selectedDate');
			$this->data['selectedShift'] = $this->input->post('shift');
			$dateInterval = $this->_setDateInterVal();
			$startDate = $this->data['startDateShow'] = $dateInterval['startDate'];
			$endDate = $this->data['endDateShow'] = $dateInterval['endDate'];
			$this->data['table'][2]['data'] = $this->report_model->getLoomWiseProduction($startDate, $endDate);
			$this->data['table'][1]['cmpx'] = $this->report_model->getTotalCmpxProduce($startDate, $endDate);
			$this->data['table'][1]['cfm'] = $this->report_model->getTotalCfmConsumptionShift($startDate, $endDate);
			$this->data['table'][1]['pres'] = $this->report_model->getTotalPressureShift($startDate, $endDate);
			$this->data['table'][1]['kw'] = $this->report_model->getTotalKwConsumptionShift($startDate, $endDate, 0);
			$this->data['table'][1]['hplant_kw'] = $this->report_model->getTotalKwConsumptionShift($startDate, $endDate, 1);
			$this->data['table'][1]['loom_kw'] = $this->report_model->getTotalKwConsumptionShift($startDate, $endDate, 2);
		}

		$this->data['all'] = $this->load->view('report/shift/all', $this->data, true);
		$this->load->view('report/shift/index', $this->data);
	}

	private function _dateSub(){
        $date = new DateTime($this->startDateTime);
        $date->sub(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

	private function _setCurrentDateTime(){
		$this->dateTimeInterval = 'PT00H15M00S';
		$currentDate = $this->weaving_model->getCurrent();
        $this->currentEndDateTime = $this->startDateTime = $currentDate[0]->last_view_date_time;
        $this->currentStartDateTime = $this->_dateSub();
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
			$this->dataTimeInterval = 'PT00H30M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif($this->data['selectedShift'] == 2){
			$this->dataTimeInterval = 'PT08H30M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H000M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif ($this->data['selectedShift'] == 3) {
			$this->dataTimeInterval = 'PT16H30M00S';
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
		//print_r($retData);
		return $retData;
    }


	

	public function getTotalCmpxProduceChartShift(){
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->data['meterNameColumn'] = 'CMPX';
    	$data = $this->report_model->getTotalCmpxProduceDateWiseShift($this->startDateTime, $this->endDate);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Total CMPX Produce';//$data[0]->device_name;
    	if(count($data) > 0){
    		$i = 0;
    		foreach ($data as $key => $value) {
    			$cmpx = ($value->data)/WEAVING_CMPX;
    			if($cmpx > 0){
		        	$dataSet .= '{
			            "date": "'.$value->end_date_time.'",
			            "paramVal": "'.round($cmpx, 2).'"
			        },';
			    }
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

	public function getCfmChartShift(){
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$type = $this->uri->segment(6); 
    	$data = $this->report_model->getCfmDateWiseShift($this->startDateTime, $this->endDate, $type);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	if($type == 'flow'){
    		$this->data['meterNameColumn'] = 'CFM';
    		$this->data['meterName'] = 'Compressed Air Flow(CFM)';//$data[0]->device_name;
    	}else{
    		$this->data['meterNameColumn'] = 'Pressure';
    		$this->data['meterName'] = 'Compressed Air Pressure';//$data[0]->device_name;
    	}
    	
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2).'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getCfmCmpxChartShift(){
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->data['meterNameColumn'] = 'CFM/CMPX';
    	$data = $this->report_model->getCfmCmpxDateWiseShift($this->startDateTime, $this->endDate);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'CFM/CMPX';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
    			$data = $value->flow/(($value->sum_picks - $value->prev_sum_picks)/WEAVING_CMPX);
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($data, 2).'"
		        },';
		        $data = 0;
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

	public function getRunningKwChartShift(){
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$type = $this->uri->segment(6);
		$this->data['meterNameColumn'] = 'KW';
    	$data = $this->report_model->getRunningKwDateWiseShift($this->startDateTime, $this->endDate, $type);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Running KW ';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2) .'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getKwCmpxChartShift(){
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->data['meterNameColumn'] = 'KW/CMPX';
    	$data = $this->report_model->getKwCmpxDateWiseShift($this->startDateTime, $this->endDate);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'KW/CMPX';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
    			$data = $value->sum_kw/(($value->sum_picks - $value->prev_sum_picks)/WEAVING_CMPX);
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($data, 2).'"
		        },';
		        $data = 0;
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    

	public function getLoomProductionEffChartShift(){
    	$this->data['machine_id'] = $machine_id = $this->uri->segment(4);
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(6));
		$this->data['meterNameColumn'] = 'Efficiency';
    	$data = $this->report_model->getSingleLoomProductionShift($machine_id, $this->startDateTime, $this->endDate, 'eff');
    	//$data = array_reverse($data);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Loom # '.$machine_id;//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2).'"
		        },';
    		}
    	}
    	//echo $dataSet;
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

	public function getLoomProductionRpmChartShift(){
    	$this->data['machine_id'] = $machine_id = $this->uri->segment(4);
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(6));
		$this->data['meterNameColumn'] = 'RPM';
    	$data = $this->report_model->getSingleLoomProductionShift($machine_id, $this->startDateTime, $this->endDate, 'rpm');
    	//$data = array_reverse($data);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Loom # '.$machine_id;//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2).'"
		        },';
    		}
    	}
    	//echo $dataSet;
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getLoomProductionPicksChartShift(){
    	$this->data['machine_id'] = $machine_id = $this->uri->segment(4);
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(6));
		$this->data['meterNameColumn'] = 'PICKS';
    	$data = $this->report_model->getSingleLoomProductionShift($machine_id, $this->startDateTime, $this->endDate, 'picks');
    	//$data = array_reverse($data);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Loom # '.$machine_id;//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2).'"
		        },';
    		}
    	}
    	//echo $dataSet;
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getLoomProductionCmpxChartShift(){
    	$this->machine_id = $this->uri->segment(4);
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(6));
		$type = $this->uri->segment(7);
		$this->data['meterNameColumn'] = 'CMPX';
		if($type == 1){
			$this->data['meterNameColumn'] = 'PICKS';
		}
    	$data = $this->report_model->getCmpxDateWiseShift($this->machine_id, $this->endDate);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Loom #'.$this->machine_id;//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
    			if($type == 1){
    				$data = ($value->cmpx);
    				$dataSet .= '{
			            "date": "'.$value->end_date_time.'",
			            "paramVal": "'.number_format((float)round($data, 2), 2, '.', '').'"
			        },';
    			}else{
    				$data = ($value->cmpx)/WEAVING_CMPX;
    				$dataSet .= '{
			            "date": "'.$value->end_date_time.'",
			            "paramVal": "'.number_format((float)round($data, 4), 4, '.', '').'"
			        },';
    			}
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }


	private function _getDateDiff($startDate = '', $endDate = '', $type = '%d'){
		if ($startDate != '' && $endDate != ''){
		    $data = $this->report_model->getDateDiff($startDate, $endDate);
		    return $data[0]->days;
		}else{
			return null;
		}
	}

	public function getTotalCmpxProduceChartDay(){
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->data['meterNameColumn'] = 'CMPX';
		$this->data['dayCount'] = $this->_getDateDiff($this->startDateTime, $this->endDate, '%d');
		if($this->data['dayCount'] == 1){
			$data = $this->report_model->getTotalCmpxProduceSingleDay($this->startDateTime, $this->endDate);
		}else{
			$data = $this->report_model->getTotalCmpxProduceDateWiseDay($this->startDateTime, $this->endDate);
		}
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Total CMPX Produce';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
    			$cmpx = $value->data/WEAVING_CMPX;
    			if($cmpx > 0){
		        	$dataSet .= '{
			            "date": "'.$value->end_date_time.'",
			            "paramVal": "'.round($cmpx, 2).'"
			        },';
			    }
    		}
    	}
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
        	$this->load->view('weaving/chart/line',$this->data);
        }else{
        	$this->load->view('weaving/chart/bar',$this->data);
        }
    }


    public function getRunningKwChartDay(){
    	$startDate =  date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->dataTimeInterval = 'PT00H45M00S';
		$endDate = $this->_dateSub();
		$type = $this->uri->segment(6);
		$this->data['meterNameColumn'] = 'KWH';
		$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
		if($this->data['dayCount'] == 1){
			$data = $this->report_model->getRunningKwChartSingleDay($startDate, $endDate, $type);
			$multiply = 1;
			$this->data['meterNameColumn'] = 'KW';
		}else{
			$data = $this->report_model->getRunningKwChartDay($startDate, $endDate, $type);
			$multiply = 24;
		}
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	if($type == 1){
    		$this->data['meterName'] = 'Total H-Plant '.$this->data['meterNameColumn'];
    	}elseif($type == 2){
    		$this->data['meterName'] = 'Total Loom '.$this->data['meterNameColumn'];
    	}else{
    		$this->data['meterName'] = 'Total '.$this->data['meterNameColumn'];
    	}
    	
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data * $multiply, 2).'"
		        },';
			    
    		}
    	}
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
        	$this->load->view('weaving/chart/line',$this->data);
        }else{
        	$this->load->view('weaving/chart/bar',$this->data);
        }
    }

    public function getCfmChartDay(){
		$startDate =  date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->dataTimeInterval = 'PT00H30M00S';
		$endDate = $this->_dateSub();
		$this->data['meterNameColumn'] = 'CFM';
		$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
		if($this->data['dayCount'] == 1){
			$data = $this->report_model->getCfmDateWiseSingleDay($startDate, $endDate);
			$devide = 1;
		}else{
			$data = $this->report_model->getCfmDateWiseDay($startDate, $endDate);
			$devide = WEAVING_CFM;
		}
    	
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Compressed Air CFM';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data/$devide, 2).'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
        	$this->load->view('weaving/chart/line',$this->data);
        }else{
        	$this->load->view('weaving/chart/bar',$this->data);
        }
    }

    public function getCfmCmpxChartDay(){
    	$startDate =  date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->dataTimeInterval = 'PT00H30M00S';
		$endDate = $this->_dateSub();
		$this->data['meterNameColumn'] = 'CFM/CMPX';
		$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
		if($this->data['dayCount'] == 1){
			$data = $this->report_model->getCfmCmpxChartSingleDay($startDate, $endDate);
			$devide = 1;
		}else{
			$data = $this->report_model->getCfmCmpxChartDay($startDate, $endDate);
			$devide = WEAVING_CFM;
		}
    	
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = ' CFM/CMPX';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data/$devide, 2).'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
        	$this->load->view('weaving/chart/line',$this->data);
        }else{
        	$this->load->view('weaving/chart/bar',$this->data);
        }
    }

    public function getKwCmpxChartDay(){
    	$startDate =  date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->dataTimeInterval = 'PT00H30M00S';
		$endDate = $this->_dateSub();
		$this->data['meterNameColumn'] = 'KWH/CMPX';
		$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
		if($this->data['dayCount'] == 1){
			$data = $this->report_model->getKwCmpxChartSingleDay($startDate, $endDate);
			$multiply = 1;
		}else{
			$data = $this->report_model->getKwCmpxChartDay($startDate, $endDate);
			$multiply = 24;
		}
    	
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'KWH/CMPX';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data * $multiply, 2).'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
        	$this->load->view('weaving/chart/line',$this->data);
        }else{
        	$this->load->view('weaving/chart/bar',$this->data);
        }
    }

    public function getCfmPressChartDay(){
    	$startDate =  date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->dataTimeInterval = 'PT00H30M00S';
		$endDate = $this->_dateSub();
		$this->data['meterNameColumn'] = 'CFM/Pressure';
		$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
		
    	$data = $this->report_model->getCfmPressChartDay($startDate, $endDate);
    	$this->data['title1'] = 'CFM';
        $this->data['title2'] = 'Pressure';
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "v1": "'.round($value->data, 2).'",
                    "v2": "'.round($value->pressure, 2).'"
                },';
            }
        }
        $this->data['meterName'] = 'CFM Consumption Vs Compressed Air Pressure';
    	$this->data['chartData'] = $dataSet;
    	$this->load->view('weaving/chart/dual_bar',$this->data);
    	
    }

    public function getLoomStyleWiseEffRpmChartDay(){
    	$machine_id = $this->uri->segment(4);
    	$style_id = $this->uri->segment(5);
    	$startDate =  date('Y-m-d H:i:s', $this->uri->segment(6));
		$endDate = date('Y-m-d H:i:s', $this->uri->segment(7));
		$this->data['meterNameColumn'] = $this->uri->segment(8);
		$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
		if($this->data['dayCount'] == 1){
			$data = $this->report_model->getLoomStyleWiseEffRpmChartSingleDay($startDate, $endDate, $machine_id, $style_id);
		}else{
			$data = $this->report_model->getLoomStyleWiseEffRpmChartDay($startDate, $endDate, $machine_id, $style_id);
		}
		$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Machine Id # '.$machine_id.' & Style ID # '.$style_id;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->{$this->data['meterNameColumn']} , 2).'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
        	$this->load->view('weaving/chart/line',$this->data);
        }else{
        	$this->load->view('weaving/chart/bar',$this->data);
        }
    }

    public function getLoomStyleCmpxChartDay(){
    	$machine_id = $this->uri->segment(4);
    	$style_id = $this->uri->segment(5);
    	$startDate =  date('Y-m-d H:i:s', $this->uri->segment(6));
		$endDate = date('Y-m-d H:i:s', $this->uri->segment(7));
		$this->data['meterNameColumn'] = 'CMPX';
		$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
		if($this->data['dayCount'] == 1){
			$data = $this->report_model->getLoomStyleCmpxChartSingleDay($startDate, $endDate, $machine_id, $style_id);
		}else{
			$data = $this->report_model->getLoomStyleCmpxChartDay($startDate, $endDate, $machine_id, $style_id);
		}
		$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Machine Id # '.$machine_id.' & Style ID # '.$style_id;
    	if(count($data) > 0){
    		foreach ($data as $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->cmpx/WEAVING_CMPX , 2).'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
        	$this->load->view('weaving/chart/line',$this->data);
        }else{
        	$this->load->view('weaving/chart/bar',$this->data);
        }
    }

}

?>