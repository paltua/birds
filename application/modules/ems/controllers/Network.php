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
		addPageDetails();
	}

	public function index(){
		//die("Coming soon.");
		$status = '';
        $msg = '';
		$this->data['pDeviceSelected'] = '0';
		$this->data['dateRange'] = $this->_getDateDetails();
		$this->data['dateRange']['selectedDate'] =  '';//$this->data['dateRange']['min_date'];
		$this->data['dateRange']['selectedDateEnd'] =  '';//$this->data['dateRange']['max_date'];
		$this->data['deviceData'] = array();
		$this->data['viewParentData'] = array();
		$this->data['viewTinData'] = array();
		$this->data['viewToutData'] = array();
		$this->data['transDetails']['parent'] = array();
		$this->data['transDetails']['in'] = array();
		$this->data['transDetails']['out'] = array();
        $this->data['transDetails']['inout'] = array();
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
				$this->data['viewTinData'] = $this->_makingTinData();
				$this->data['deviceToutData'] = $this->network_model->getDeviceWiseTransferOutData($this->data['pDeviceSelected'], $startDate, $endDate);
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
				if( $value->data > 0){
					$parentData['KW'] = $value->data;
				}
			}
			$retData = $parentData;
		}

		return $retData;
	}

	private function _makingTinData(){
		$retData = array();
		if(count($this->data['deviceTinData']) > 0){
			foreach ($this->data['deviceTinData'] as $key => $value) {
				$retData[$value->device_id]['name'] = $value->device_name;
				$retData[$value->device_id]['capacity'] = $value->t_in_device_capacity;
				$retData[$value->device_id]['KW'] = $value->KW;
				if($value->KWPF > 0){
					$retData[$value->device_id]['KWPF'] = $value->sum_KW/$value->KWPF;
				}else{
					$retData[$value->device_id]['KWPF'] = 0;
				}
			}
		}
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
			foreach ($this->data['deviceToutData'] as $key => $value) {
				$retData[$value->device_id]['name'] = $value->device_name;
				$retData[$value->device_id]['capacity'] = $value->t_in_device_capacity;
				$retData[$value->device_id]['KW'] = $value->KW;
			}
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

    private function _dateSub(){
        $date = new DateTime($this->startDateTime);
        $date->sub(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

    private function _makingTransDetails(){
    	$retData = array();
    	if(count($this->data['tempTransDetails'])){
            //$this->p($this->data['tempTransDetails']);
    		foreach ($this->data['tempTransDetails'] as $key => $value) {
    			if($value->p_device_id != ''){
    				$retData['parent'] = array(
											'id' => $value->p_device_id,
											'name' => $value->p_device_name,
										);
    			}
    			if($value->t_in_device_id != ''){
    				$retData['in'][$value->id] = array(
											'id' => $value->t_in_device_id,
											'name' => $value->t_in_device_name,
											'capacity' => $value->t_in_device_capacity,
										);
    			}
    			if($value->t_out_device_id != ''){
    				$retData['out'][$value->id] = array(
											'id' => $value->t_out_device_id,
											'name' => $value->t_out_device_name,
										);
    			}
    			if($value->t_in_device_id != '' && $value->t_out_device_id != ''){
    				$retData['inout'][$value->id] = array(
											't_in_device_id' => $value->t_in_device_id,
											't_out_device_id' => $value->t_out_device_id,
                                            'name' => $value->trans_name,
										);
    			}
    		}
    	}
    	//$this->p($retData);
    	return $retData;
    }

    public function showNetGraphKw(){
    	$meter_id = $this->uri->segment(4);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(5));
    	$this->dataTimeInterval = 'PT00H00M00S';
    	$startDate = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$selected = 'KW';
    	$meterDetails = $this->network_model->getMeterDetails($meter_id, $selected);
    	if(count($meterDetails) > 0){
    		$this->data['meterName'] = $meterDetails[0]->device_name;
    		$this->data['meterNameColumn'] = $selected;
    	}
    	$data = array();
    	if($this->data['dayCount'] == 1){
    		$data = $this->network_model->getChartMeterData24Hours($meter_id, $startDate, $endDate);
    	}else{
    		$data = $this->network_model->getChartMeterDataDay($meter_id, $startDate, $endDate);
    	}
        $dataSet = '';
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
    			$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.number_format((float)round($value->data,3), 2, '.', '').'"
		        },';
    		}
    	}
        unset($data);	
        $this->data['chartData'] = $dataSet;
        if($this->data['dayCount'] == 1){
    	    $this->load->view('network/chart_am_line',$this->data);
        }else{
            $this->load->view('network/chart_am_bar',$this->data);
        }
    }



    private function _getDateDiff($startDate = '', $endDate = '', $type = '%d'){
		if ($startDate != '' && $endDate != ''){
            $data = $this->network_model->getDateDiff($startDate, $endDate);
            return $data[0]->days;
		}else{
			return null;
		}
	}

	public function showGraphLineLoss(){
		$parenDeviceId = $this->uri->segment(4);
		$type = $perAbs = $this->uri->segment(5);
		$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT00H00M00S';
    	$startDate = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$selected = 'KW';
    	$meterDetails = $this->network_model->getTransformerDetailsWithGroupConcatGraph($parenDeviceId);
    	if(count($meterDetails) > 0){
    		$this->data['meterName'] = 'Line Loss of meter ('.$meterDetails[0]->p_device_name.') VS ('.$meterDetails[0]->t_in_device_name.')';
    		$this->data['meterNameColumn'] = 'Loss'.(($perAbs == 'per')?' in %':'');
    	}
    	$getPDeviceData = $this->network_model->getParentDeviceGraph($parenDeviceId, $startDate, $endDate, $this->data['dayCount']);
    	$getTINDeviceData = $this->network_model->getTransInDeviceGraph($parenDeviceId, $startDate, $endDate, $this->data['dayCount']);

    	$data = array();
    	if(count($getPDeviceData) > 0){
    		$dataG = array();
    		foreach ($getPDeviceData as $keyG => $valueG) {
    			$dataG[$valueG->end_date_time] = $valueG->KW;
    		}
    		foreach ($getTINDeviceData as $keyD => $valueD) {
    			if(isset($dataG[$valueD->end_date_time])){
    				$data[$valueD->end_date_time]['abs'] = $dataG[$valueD->end_date_time] - $valueD->KW;
    				if($dataG[$valueD->end_date_time] == 0){
    					$data[$valueD->end_date_time]['per'] = 0;
    				}else{
    					$data[$valueD->end_date_time]['per'] = ($data[$valueD->end_date_time]['abs']/$dataG[$valueD->end_date_time]) * 100;
    				}
    			}else{
    				$data[$valueD->end_date_time]['abs'] = 0;
    				$data[$valueD->end_date_time]['per'] = 0;
    			}
    		}
    	}
    	$dataSet = '';
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$key.'",
		            "paramVal": "'.number_format((float)round($value[$perAbs],3), 2, '.', '').'"
		        },';
    		}
    	}
        unset($data);   
        $this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
            $this->load->view('network/chart_am_line',$this->data);
        }else{
            $this->load->view('network/chart_am_bar',$this->data);
        }
	}

	public function showGraphTransformerLoss(){
		$parenDeviceId = $this->uri->segment(4);
		$type = $perAbs = $this->uri->segment(5);
		$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT00H00M00S';
    	$startDate = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$selected = 'KW';
    	$meterDetails = $this->network_model->getTransformerDetailsWithGroupConcatGraph($parenDeviceId);
    	if(count($meterDetails) > 0){
    		$this->data['meterName'] = 'Transformer Loss of meter ('.$meterDetails[0]->t_in_device_name.') VS ('.$meterDetails[0]->t_out_device_name.')';
    		$this->data['meterNameColumn'] = 'Loss'.(($perAbs == 'per')?' in %':'');
    	}
    	$getTINDeviceData = $this->network_model->getTransInDeviceGraph($parenDeviceId, $startDate, $endDate, $this->data['dayCount']);
    	$getTOUTDeviceData = $this->network_model->getTransOutDeviceGraph($parenDeviceId, $startDate, $endDate, $this->data['dayCount']);

    	$data = array();
    	if(count($getTINDeviceData) > 0){
    		$dataG = array();
    		foreach ($getTINDeviceData as $keyG => $valueG) {
    			$dataG[$valueG->end_date_time] = $valueG->KW;
    		}
    		foreach ($getTOUTDeviceData as $keyD => $valueD) {
    			if(isset($dataG[$valueD->end_date_time])){
    				$data[$valueD->end_date_time]['abs'] = $dataG[$valueD->end_date_time] - $valueD->KW;
    				if($dataG[$valueD->end_date_time] == 0){
    					$data[$valueD->end_date_time]['per'] = 0;
    				}else{
    					$data[$valueD->end_date_time]['per'] = ($data[$valueD->end_date_time]['abs']/$dataG[$valueD->end_date_time]) * 100;
    				}
    			}else{
    				$data[$valueD->end_date_time]['abs'] = 0;
    				$data[$valueD->end_date_time]['per'] = 0;
    			}
    			
    		}
    	}
    	$dataSet = '';
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$key.'",
		            "paramVal": "'.number_format((float)round($value[$perAbs],3), 2, '.', '').'"
		        },';
    		}
    	}
    	unset($data);   
        $this->data['chartData'] = $dataSet;
        if($this->data['dayCount'] == 1){
            $this->load->view('network/chart_am_line',$this->data);
        }else{
            $this->load->view('network/chart_am_bar',$this->data);
        }
	}

	public function showGraphTotalLoss(){
		$parenDeviceId = $this->uri->segment(4);
		$type = $perAbs = $this->uri->segment(5);
		$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT00H00M00S';
    	$startDate = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$selected = 'KW';
    	$meterDetails = $this->network_model->getTransformerDetailsWithGroupConcatGraph($parenDeviceId);
    	if(count($meterDetails) > 0){
    		$this->data['meterName'] = 'Transformer Loss of meter '.$meterDetails[0]->p_device_name.' VS ('.$meterDetails[0]->t_in_device_name.')';
    		$this->data['meterNameColumn'] = 'Loss'.(($perAbs == 'per')?' in %':'');
    	}

    	$getPDeviceData = $this->network_model->getParentDeviceGraph($parenDeviceId, $startDate, $endDate, $this->data['dayCount']);
    	$getTOUTDeviceData = $this->network_model->getTransOutDeviceGraph($parenDeviceId, $startDate, $endDate, $this->data['dayCount']);

    	$data = array();
    	/*$this->data['dataSet'] = '';
    	$this->data['max_val'] = 10;
    	$this->data['min_val'] = 0;
    	$maxMin = array();*/
    	if(count($getPDeviceData) > 0){
    		$dataG = array();
    		foreach ($getPDeviceData as $keyG => $valueG) {
    			$dataG[$valueG->end_date_time] = $valueG->KW;
    		}
    		foreach ($getTOUTDeviceData as $keyD => $valueD) {
    			if(isset($dataG[$valueD->end_date_time])){
    				$data[$valueD->end_date_time]['abs'] = $dataG[$valueD->end_date_time] - $valueD->KW;
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
    	$dataSet = '';
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$key.'",
		            "paramVal": "'.number_format((float)round($value[$perAbs],3), 2, '.', '').'"
		        },';
    		}
    		/*$this->data['max_val'] = intval(max($maxMin[$perAbs])) ;
    		$this->data['min_val'] = intval(min($maxMin[$perAbs]));*/
    	}
    	unset($data);   
        $this->data['chartData'] = $dataSet;
        if($this->data['dayCount'] == 1){
            $this->load->view('network/chart_am_line',$this->data);
        }else{
            $this->load->view('network/chart_am_bar',$this->data);
        }
	}

	public function showGraphTransformerDetailsLoss(){
		$parenDeviceId = $this->uri->segment(4);
		$transId = $this->uri->segment(5);
		$type = $perAbs = $this->uri->segment(6);
		$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT00H00M00S';
    	$startDate = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(8));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$selected = 'KW';
    	$this->data['tempTransDetails'] = $this->network_model->getTransformerDetailsByTransId($transId);
		$this->data['transDetails'] = $this->_makingTransDetails();
		//$this->p($this->data['transDetails']);

    	if(count($this->data['transDetails']) > 0){
    		$this->data['meterName'] = 'Transformer ('.$this->data['transDetails']['in'][$transId]['name'].') - ('.$this->data['transDetails']['out'][$transId]['name'].')';
    		$this->data['meterNameColumn'] = 'Loss'.(($perAbs == 'per')?' in %':'');
    	}
    	$tInId = $this->data['transDetails']['in'][$transId]['id'];
    	$tOutId = $this->data['transDetails']['out'][$transId]['id'];
		$getTINDeviceData = $this->network_model->getTransInDetailsGraph($tInId, $startDate, $endDate, $this->data['dayCount']);
    	$getTOUTDeviceData = $this->network_model->getTransOutDetailsGraph($tOutId, $startDate, $endDate, $this->data['dayCount']);

    	$data = array();
    	/*$this->data['dataSet'] = '';
    	$this->data['max_val'] = 10;
    	$this->data['min_val'] = 0;
    	$maxMin = array();*/
    	if(count($getTINDeviceData) > 0){
    		$dataG = array();
    		foreach ($getTINDeviceData as $keyG => $valueG) {
    			$dataG[$valueG->end_date_time] = $valueG->KW;
    		}
    		foreach ($getTOUTDeviceData as $keyD => $valueD) {
    			if(isset($dataG[$valueD->end_date_time])){
    				$data[$valueD->end_date_time]['abs'] = $dataG[$valueD->end_date_time] - $valueD->KW;
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
    	$dataSet = '';
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$key.'",
		            "paramVal": "'.number_format((float)round($value[$perAbs],3), 2, '.', '').'"
		        },';
    		}
    		/*$this->data['max_val'] = intval(max($maxMin[$perAbs])) ;
    		$this->data['min_val'] = intval(min($maxMin[$perAbs]));*/
    	}
    	unset($data);   
        $this->data['chartData'] = $dataSet;
        if($this->data['dayCount'] == 1){
            $this->load->view('network/chart_am_line',$this->data);
        }else{
            $this->load->view('network/chart_am_bar',$this->data);
        }
	}

	public function showGraphTransformerDetailsLoading(){
		$parenDeviceId = $this->uri->segment(4);
		$transId = $this->uri->segment(5);
		$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT00H00M00S';
    	$startDate = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$selected = 'KW';
    	$this->data['tempTransDetails'] = $this->network_model->getTransformerDetailsByTransId($transId);
		$this->data['transDetails'] = $this->_makingTransDetails();
       

    	if(count($this->data['transDetails']) > 0){
    		$this->data['meterName'] = 'Transformer ('.$this->data['transDetails']['in'][$transId]['name'].') - ('.$this->data['transDetails']['out'][$transId]['name'].')';
    		$this->data['meterNameColumn'] = 'Loading vs Loss';
    	}
    	$tInId = $this->data['transDetails']['in'][$transId]['id'];
    	$tOutId = $this->data['transDetails']['out'][$transId]['id'];
		$getTINDeviceData = $this->network_model->getTransInDetailsGraph($tInId, $startDate, $endDate, $this->data['dayCount']);
    	$getTOUTDeviceData = $this->network_model->getTransOutDetailsGraph($tOutId, $startDate, $endDate, $this->data['dayCount']);

    	$data = array();
    	$dataLoading = array();
    	if(count($getTINDeviceData) > 0){
    		$dataG = array();
    		foreach ($getTINDeviceData as $keyG => $valueG) {
    			$dataG[$valueG->end_date_time] = $valueG->KW;
    			if($valueG->KWPF != 0 && $valueG->t_in_device_capacity != 0){
    				$tempLoading = $valueG->sum_KW/$valueG->sum_PF;
    				$dataLoading[$valueG->end_date_time] = ($tempLoading /$valueG->t_in_device_capacity) * 100;
    				$tempLoading = '';
    			}else{
    				$dataLoading[$valueG->end_date_time] = 0;
    			}
    		}
    		foreach ($getTOUTDeviceData as $keyD => $valueD) {
    			if(isset($dataG[$valueD->end_date_time])){
    				$data[$valueD->end_date_time] = $dataG[$valueD->end_date_time] - $valueD->KW;
    				if($dataG[$valueD->end_date_time] == 0){
    					$data[$valueD->end_date_time] = 0;
    				}else{
    					$data[$valueD->end_date_time] = ($data[$valueD->end_date_time]/$dataG[$valueD->end_date_time]) * 100;
    				}
    			}else{
    				
    			}
    		}
    	}
        
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$key.'",
                    "loading": "'.number_format((float)round($dataLoading[$key],3), 2, '.', '').'",
                    "loss": "'.number_format((float)round($value,3), 2, '.', '').'"                    
                },';
            }
        }
        unset($data);   
        $this->data['chartData'] = $dataSet;
        if($this->data['dayCount'] == 1){
            $this->load->view('network/chart_loading_am_line',$this->data);
        }else{
            $this->load->view('network/chart_loading_am_bar',$this->data);
        }
	}

    public function dashboard(){
        $status = '';
        $msg = '';
        $maxMinDate = $this->network_model->lastDate();
        $this->data['datePickerStart'] = '';//$maxMinDate[0]->min_date;
        $this->data['datePickerEnd'] = '';//$maxMinDate[0]->max_date;
        $this->data['datePickerDateMax'] = $maxMinDate[0]->max_date;
        $this->data['datePickerDateMin'] = $maxMinDate[0]->min_date;
        //$this->_getTransDashboardDate();
        $this->data['selectedCapacity'] = 0;
        $this->data['transDetails'] = array();
        $this->data['viewStartDate'] = '';
        $this->data['viewEndDate'] = '';
        $this->load->library('form_validation');
        if($this->input->post('btnSearch')){
            $this->data['selectedCapacity'] = $this->input->post('t_in_device_capacity');
            $this->data['datePickerStart'] = $this->input->post('startDate');
            $this->data['datePickerEnd'] = $this->input->post('endDate');
            $this->_getTransDashboardDate();
            $this->data['viewStartDate'] = strtotime($this->data['startDate']);
            $this->data['viewEndDate'] = strtotime($this->data['endDate']);
            $this->data['transDetails'] = $this->network_model->transformerDetailsWithDateRange($this->data['startDate'], $this->data['endDate'], $this->data['selectedCapacity']);
        }
        $this->data['capacity'] = $this->network_model->getTransformerCapacity();
        $this->data['msg'] = $msg;
        $this->data['mainPage'] = $this->load->view('network/dashboardMainPage', $this->data, true);
        $this->load->view('network/dashboardIndex', $this->data);
    }

    private function _getTransDashboardDate(){
        $this->dataTimeInterval = 'PT00H00M00S';
        $this->startDateTime = $this->data['datePickerStart'];
        $this->data['startDate'] = $this->_dateAdd();
        $this->startDateTime = $this->data['datePickerEnd'];
        $this->dataTimeInterval = 'PT23H59M59S';
        $this->data['endDate'] =  $this->_dateAdd();
    }

    public function showGraphHtLtKw(){
        $type = $this->uri->segment(4);
        $transId = $this->uri->segment(5);
        $this->startDateTime = date('Y-m-d', $this->uri->segment(6));
        $this->dataTimeInterval = 'PT00H00M00S';
        $startDate = $this->_dateAdd();
        $this->startDateTime = date('Y-m-d', $this->uri->segment(7));
        $this->dataTimeInterval = 'PT24H00M00S';
        $endDate = $this->_dateAdd();
        $this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
        $selected = 'KW';
        $transDetails = $this->network_model->getTransformerDetailsByTransId($transId);
        if(count($transDetails) > 0){
            $this->data['meterName'] = 'Transformer # '.$transDetails[0]->trans_name;
            $this->data['meterNameColumn'] = 'KW';
        }
        $tInId = $transDetails[0]->t_in_device_id;
        $tOutId = $transDetails[0]->t_out_device_id;
        $data = array();
        if($type == 'in'){
            $data = $this->network_model->getTransInDetailsGraph($tInId, $startDate, $endDate, $this->data['dayCount']);
        }elseif($type == 'out'){
            $data = $this->network_model->getTransOutDetailsGraph($tOutId, $startDate, $endDate, $this->data['dayCount']);
        }
        $this->data['chartData'] = $data;
        $dataSet = '';
        if(count($data) > 0){
            $maxMin = array();
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.number_format((float)round($value->KW,3), 2, '.', '').'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        if($this->data['dayCount'] > 1){
            $this->load->view('network/chart_am_bar',$this->data);
        }else{
            $this->load->view('network/chart_am_line',$this->data);
        }
    }



    public function p($data = array()){
    	echo '<pre>';
    	print_r($data);
    	//exit;
    }

}

