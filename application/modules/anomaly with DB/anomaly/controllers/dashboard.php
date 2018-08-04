<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	public $data = array();
	public $shiftArr = array();
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->shiftArr = array(1,2,3);
		$this->load->model('air_model');

	}

	public function index(){
		$where = array();
		
		$this->data['search'] = false;
		$this->data['meter_id'] = 0;
		$this->data['startDate'] = '2017-08-22';
		$this->data['working_shift'] = 0;
		$this->data['min_val'] = '0';
		$this->data['max_val'] = '0';
		$this->data['dateRange'] = $this->_getDateDetails();
		//print_r($this->data['dateRange']);
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


	private function _getTotalTypeWise(){
		$retData = array();
		if(count($this->data['last15DataSet'])){
			$data = array();
			//echo "<pre>";
			//var_dump($this->data['last15DataSet']);
			//var_dump($this->data['last15DataSet']);


			foreach ($this->data['last15DataSet'] as $key => $value) {
				//var_dump($value);

				$data[$value->type]['P_pressure'][] = $value->P_pressure;
				$data[$value->type]['P_pressure_prod'][] = $value->P_pressure * $value->flow;

				$data[$value->type]['flow'][] = $value->flow;
				$data[$value->type]['T_temp'][] = $value->T_temp;
				$data[$value->type]['T_temp_prod'][] = $value->T_temp * $value->flow;

				$data[$value->type]['TTL_flow'][] = $value->TTL_flow;
				$data[$value->type]['steam_enthalpy'][] = $value->steam_enthalpy;
				$data[$value->type]['steam_enthalpy_prod'][] = $value->flow * $value->steam_enthalpy;

				$data[$value->type]['steam_heat_content'][] = $value->steam_heat_content;
				$retData[$value->type]['meter'][$value->meter_id]['name'] = $value->name;
				$retData[$value->type]['meter'][$value->meter_id]['P_pressure'] = $value->P_pressure;
				$retData[$value->type]['meter'][$value->meter_id]['flow'] = $value->flow;
				$retData[$value->type]['meter'][$value->meter_id]['T_temp'] = $value->T_temp;
				$retData[$value->type]['meter'][$value->meter_id]['TTL_flow'] = $value->TTL_flow;
				$retData[$value->type]['meter'][$value->meter_id]['steam_enthalpy'] = $value->steam_enthalpy;
				$retData[$value->type]['meter'][$value->meter_id]['steam_heat_content'] = $value->steam_heat_content;
				$retData[$value->type]['meter'][$value->meter_id]['benchmark_delta_pressure'] = $value->benchmark_delta_pressure;
				$retData[$value->type]['meter'][$value->meter_id]['benchmark_delta_temp'] = $value->benchmark_delta_temp;
				$retData[$value->type]['meter'][$value->meter_id]['alert_counter_pressure'] = $value->alert_counter_pressure;
				$retData[$value->type]['meter'][$value->meter_id]['alert_counter_temp'] = $value->alert_counter_temp;
				$retData[$value->type]['meter'][$value->meter_id]['alert_mail_status'] = $value->alert_mail_status;
			}
			//echo "<pre>";
			//print_r($retData);
			if(count($data) > 0){
				foreach ($data as $key => $value) {

					$retData[$key]['total']['flow'] = array_sum($value['flow']);

					//$retData[$key]['total']['P_pressure'] = array_sum($value['P_pressure'])/count($value['P_pressure']);
					if(isset($retData[$key]['total']['flow']) && $retData[$key]['total']['flow']!=0){
						$retData[$key]['total']['P_pressure'] = array_sum($value['P_pressure_prod'])/$retData[$key]['total']['flow'];
					}else{
						$retData[$key]['total']['P_pressure'] = 0;
					}
					

					//$retData[$key]['total']['flow'] = array_sum($value['flow'])/count($value['flow']);

					//$retData[$key]['total']['T_temp'] = array_sum($value['T_temp'])/count($value['T_temp']);
					if(isset($retData[$key]['total']['flow']) && $retData[$key]['total']['flow']!=0){
						$retData[$key]['total']['T_temp'] = array_sum($value['T_temp_prod'])/$retData[$key]['total']['flow'];
					}else{
						$retData[$key]['total']['T_temp'] = 0;
					}
					

					$retData[$key]['total']['TTL_flow'] = array_sum($value['TTL_flow']);

					if($retData[$key]['total']['flow'] > 0){
						$retData[$key]['total']['steam_enthalpy'] = array_sum($value['steam_enthalpy_prod'])/$retData[$key]['total']['flow'];
					}else{
						$retData[$key]['total']['steam_enthalpy'] = 0;
					}
					$retData[$key]['total']['steam_heat_content'] = array_sum($value['steam_heat_content']);
				}
				//print_r($retData);
			}
		}
		return $retData;
	}

	private function _getDateDetails(){
		
		$meter_id = $this->data['meter_id'] ;
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

    public function getNewDataSet(){
    	$this->data['last15DataSet'] = $this->air_model->getLast15DataTypeWise();
		$this->data['typeWise'] = $this->_getTotalTypeWise();

		$retData['all'] = $this->load->view('dashboard/all',$this->data, true);
		$retData['status'] = 'success'; 
		echo json_encode($retData);
    }

    public function getMeterChart(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$selected = 'AM.name,AMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	if($type == 'temp'){
    		$selected .= ',AMD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',AMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',AMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',AMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$data = $this->air_model->getAirMeterData($meter_id, $selected);
    	$data = array_reverse($data);
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $column;
    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		        if($curVal != ''){
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';
			        $maxMin[] = $deltaGenVal - $value->{$aa};
		        	$consistencyArr[$value->end_date_time] = $deltaGenVal - $value->{$aa};
		        }else{
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($value->{$aa},3).'"
			        },';
			        $maxMin[] = $value->{$aa};
		        }
		        
    		}
    	}
    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	if(count($consistencyArr) > 0){
    		$this->data['consisIndex'] = 'yes';
    		$consistencyJson = json_encode($consistencyArr);
    		
    		$cinsistencyResult = $this->consistencyIndex($staticDeltaVal, $curVal, $consistencyJson);
    		if(count($cinsistencyResult['consistencyArray']) > 0){
    			foreach ($cinsistencyResult['consistencyArray'] as $key1 => $value1) {
    				$dataSetConsis .= '{
			            "label": "'.$key1.'",
			            "value": "'.$value1.'"
			        },';
    			}
    			$maxMinConsis[] = $value1;
    		}
    		$this->data['graph_logic'] = $cinsistencyResult['flag'];
    		$this->data['max_val_con'] = intval(max($maxMinConsis)) + 1;
    		$this->data['min_val_con'] = intval(min($maxMinConsis)) - ((min($maxMinConsis) == 0)?0:1);
    	}
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	//echo intval(min($maxMin));	
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('dashboard/chart',$this->data);
    }

    public function consistencyIndex($avgVal , $currentVal , $dataSet ){
    	$retData['consistencyArray'] = array();
    	$retData['flag'] = 1;
    	if(isset($currentVal) && isset($dataSet)){

			$dataSetArray = json_decode($dataSet,true);
			if(count($dataSetArray)>0){
				//echo "<pre>";
				// First logic
				$firstAlertCounterArray = array();
				foreach ($dataSetArray as $timestamp => $value) {
					if($value > $avgVal){
						$firstAlertCounterArray[$timestamp] = 1;
					}else{
						$firstAlertCounterArray[$timestamp] = 0;
					}
				}

				if(count($firstAlertCounterArray)>0){
					// Second Logic
					$secondCounterArray = array();
					$previousVal = "";
					$previousTimestamp = "";				

					foreach($firstAlertCounterArray as $key1=>$val1){					

						if(count($secondCounterArray)==0){
							$secondCounterArray[$key1] = $val1;
						}else{

							$secondCounterArray[$key1] = $val1 + $previousVal;
						}

						$previousTimestamp = $key1;
						$previousVal = isset($secondCounterArray[$previousTimestamp]) ? $secondCounterArray[$previousTimestamp] : $val1;

					}
				}

				//var_dump($secondCounterArray);
				if(count($secondCounterArray)>0){
					$consistencyArray = array();
					$i=1;
					foreach($secondCounterArray as $key2 => $value2) {
						
						$consistencyArray[$key2] = (1 - $value2/$i) * 100;
					$i++;}

					//var_dump($consistencyArray);
				}
				if (count(array_unique($consistencyArray)) === 1 && end($consistencyArray) == 0) {
					$retData['flag'] = 2;//0 logic

				}elseif (count(array_unique($consistencyArray)) === 1 && end($consistencyArray) == 100) {
					$retData['flag'] = 3;//100 logic
				}

				$retData['consistencyArray'] = $consistencyArray;

			}

		}
		return $retData;
	}
	
}