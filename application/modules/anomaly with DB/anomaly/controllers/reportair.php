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

	}

	public function index(){
		$this->data = array();
		$this->data['all'] = null; 
		$startDate = '';
		$this->data['dateRange'] = $this->_getDateDetails();
		$this->data['dateRange']['selectedDate'] =  $this->data['dateRange']['min_date'];
		$this->data['get_meter'] =  $this->air_model->getMeter();


		if(isset($_POST['endDate']) && isset($_POST['meter'])){		
			
			
			$this->endDateTime = date('Y-m-d',strtotime($_POST['endDate']));//'2017-09-25';			

			$shift = $this->air_model->getShiftStart(1);
			$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';					
			$this->endDateTime = $this->_dateAddonEnddate();
			$this->dataTimeInterval = 'PT24H00M00S';			
			$this->endDateTime = $this->_dateAddonEnddate();
			
			// Check End Date Value Exists in Database or Not (Otherwise Call The API)
			$finalResultArray = array();
			$getAnomalyHistoryData = $this->air_model->getAnomalyHistoryDataByDate($this->endDateTime,$_POST['meter']);
			if(isset($getAnomalyHistoryData) && count($getAnomalyHistoryData)==0){

				// Get Last End Date
				$getLastHistoryDate = $this->air_model->getLastHistoryDateData($_POST['meter']);
				
				if(isset($getLastHistoryDate) && count($getLastHistoryDate)>0){
					$this->startDateTime = date('Y-m-d H:i:s',strtotime($getLastHistoryDate[0]->end_date_time));
				}else{
					$this->startDateTime = $this->_dateAddonDate('2017-09-19','PT'.$shift[0]->config_val.'H00M00S');
				}

				// Get and generate The 15 minutes data date wise
				$dataArray = array();
				for ( $i = strtotime($this->startDateTime); $i <= strtotime($this->endDateTime); $i = $i + 86400 ) {
				  	$thisDate = date( 'Y-m-d H:i:s', $i ); 
				  
				  	$sDate = $thisDate;
					$eDate = $this->_dateAddonDate($sDate,'PT24H00M00S');

					if(strtotime($eDate)<=strtotime($this->endDateTime)){

						$this->data['last15DataSet'] = $this->air_model->getLastDateDataTypeWise($_POST['meter'],$sDate ,$eDate);				

						//$this->data['typeWise'] = $this->_getTotalTypeWise();
						//$this->data['typeWise'] = $this->_getTotalTypeWise1();
						//$dataArray[date('m/d/Y H:i:s',strtotime($sDate))] = $this->_getTotalTypeWise();
						$dataArray[$eDate] = $this->_getTotalTypeWise1();				
						
				  	}		  

				}				
				// Iterate The Data
				$newArray = $this->_regenerateTheDataSet($dataArray);

				// Ready For Call The DIY1
				$resultArray = $this->_callToAPI($newArray);
				
				$finalResultArray = isset($resultArray[$this->endDateTime]) ? $resultArray[$this->endDateTime] : '';
				
			}else{
				
				$meterDetails = $this->air_model->getMeterDetails($_POST['meter']);
				if(!isset($finalResultArray[$meterDetails[0]->type][$meterDetails[0]->meter_id]['meter']['name'])){
					$finalResultArray[$meterDetails[0]->type][$meterDetails[0]->meter_id]['meter']['name'] = isset($meterDetails[0]->name) ? $meterDetails[0]->name : '';
				}
				
				$finalResultArray[$meterDetails[0]->type][$meterDetails[0]->meter_id]['meter']['P_pressure'] = isset($getAnomalyHistoryData[0]->P_pressure) ? json_decode($getAnomalyHistoryData[0]->P_pressure,true) : '';

				$finalResultArray[$meterDetails[0]->type][$meterDetails[0]->meter_id]['meter']['T_temp'] = isset($getAnomalyHistoryData[0]->T_temp) ? json_decode($getAnomalyHistoryData[0]->T_temp,true) : '';

				$finalResultArray[$meterDetails[0]->type][$meterDetails[0]->meter_id]['meter']['flow'] = isset($getAnomalyHistoryData[0]->flow) ? json_decode($getAnomalyHistoryData[0]->flow,true) : '';

				

			}
			///////////////////////////////////////////			
			$this->data['data'] = $finalResultArray;
			

			$this->data['all'] = $this->load->view('reportair/all_day',$this->data, true);
		}
		
		
		$this->load->view('reportair/index', $this->data);
	}

	private function _regenerateTheDataSet($dataArray){

		$newArray = array();
		if(count($dataArray)>0){
			foreach ($dataArray as $key123 => $value123) {
				foreach ($value123 as $key => $value) {
					foreach ($value as $key1 => $value1) {
						foreach ($value1['meter'] as $key2 => $value2) {
							$newArray[$key123][$key2]['meter'] = $value2['name'];
							$newArray[$key123][$key2]['type'] = $key1;
							if(isset($value2['P_pressure'])){
								$newArray[$key123][$key2]['P_pressure'][$key] = $value2['P_pressure'];
							}
							if(isset($value2['T_temp'])){
								$newArray[$key123][$key2]['T_temp'][$key] = $value2['T_temp'];
							}
							if(isset($value2['flow'])){
								$newArray[$key123][$key2]['flow'][$key] = $value2['flow'];
							}
							
						}
					}
				}
			}
		}

		return $newArray;
	}

	private function _callToAPI($newArray){

		$resultArray = array();
		if(count($newArray)>0){
			foreach ($newArray as $endDate => $dateValue) {

				foreach ($dateValue as $meterId => $metervalue) {

					if(!isset($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['name'])){
						$resultArray[$endDate][$metervalue['type']][$meterId]['meter']['name'] = isset($metervalue['meter']) ? $metervalue['meter'] : '';
					}
					
					// Get Historical Data For Call the Running Info
					$getHistoryData = $this->air_model->getAllHistoryDateData($meterId); 

					$runningInfoP = array();
					$runningInfoT = array();
					$runningInfoF = array();
					if(count($getHistoryData)>0){
						foreach ($getHistoryData as $Hiskey => $Hisvalue) {
							if(isset($Hisvalue->P_pressure) && $Hisvalue->P_pressure!=''){
								$jDecode = json_decode($Hisvalue->P_pressure,true);
								if(isset($jDecode) && count($jDecode)>0){
									$runningInfoP[] = $jDecode;
								}
								
							}
							if(isset($Hisvalue->T_temp) && $Hisvalue->T_temp!=''){
								$jDecode = json_decode($Hisvalue->T_temp,true);
								if(isset($jDecode) && count($jDecode)>0){
									$runningInfoT[] = $jDecode;
								}
								
							}
							if(isset($Hisvalue->flow) && $Hisvalue->flow!=''){
								$jDecode = json_decode($Hisvalue->flow,true);
								if(isset($jDecode) && count($jDecode)>0){
									$runningInfoF[] = $jDecode;
								}
								
							}
							
						}
					}
					// Pressure
					if(isset($metervalue['P_pressure']) && count($metervalue['P_pressure'])>0){
						$pArray = array();
						foreach ($metervalue['P_pressure'] as $pdate => $pvalue) {
							//$pArray[] = ['Date'=>$pdate,'P_pressure'=>$pvalue];
							$pArray[] = ['P_pressure'=>$pvalue];
						}	

						$arrPaddiInfo = array("data_key" => "P_pressure","given_high" => "","given_low" => "", "running_info" => $runningInfoP);
						$arrP = array('data'=>($pArray),"additional_info"=>($arrPaddiInfo));

									
						// Call DIY1 API
						$pApiData = $this->makeAPIcall($arrP);

						$pApiDataJsonDecode = json_decode($pApiData,true);
						
						$resultArray[$endDate][$metervalue['type']][$meterId]['meter']['P_pressure'] = array();
						if(isset($pApiDataJsonDecode['status']) && $pApiDataJsonDecode['status']=='SUCCESS' && isset($pApiDataJsonDecode['data'])){
							$resultArray[$endDate][$metervalue['type']][$meterId]['meter']['P_pressure'] = $pApiDataJsonDecode['data'];
						}
						
					}
					// Temp
					if(isset($metervalue['T_temp']) && count($metervalue['T_temp'])>0){
						$tArray = array();
						foreach ($metervalue['T_temp'] as $tdate => $tvalue) {
							//$tArray[] = ['Date'=>$tdate,'T_temp'=>$tvalue];
							$tArray[] = ['T_temp'=>$tvalue];
						}
						
						$arrTaddiInfo = array("data_key" => "T_temp","given_high" => "","given_low" => "", "running_info" => $runningInfoT);
						$arrT = array('data'=>($tArray),"additional_info"=>($arrTaddiInfo));

						// Call DIY1 API
						$tApiData = $this->makeAPIcall($arrT);
					
						$tApiDataJsonDecode = json_decode($tApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['T_temp'] = array();
						if(isset($tApiDataJsonDecode['status']) && $tApiDataJsonDecode['status']=='SUCCESS' && isset($tApiDataJsonDecode['data'])){
							$resultArray[$endDate][$metervalue['type']][$meterId]['meter']['T_temp'] = $tApiDataJsonDecode['data'];
						}
					}
					// Flow
					if(isset($metervalue['flow']) && count($metervalue['flow'])>0){
						$fArray = array();
						foreach ($metervalue['flow'] as $fdate => $fvalue) {
							//$fArray[] = ['Date'=>$fdate,'flow'=>$fvalue];
							$fArray[] = ['flow'=>$fvalue];
						}
						
						$arrFaddiInfo = array("data_key" => "flow","given_high" => "","given_low" => "", "running_info" => $runningInfoF);
						$arrF = array('data'=>($fArray),"additional_info"=>($arrFaddiInfo));

						// Call DIY1 API
						$fApiData = $this->makeAPIcall($arrF);
						
						$fApiDataJsonDecode = json_decode($fApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['flow'] = array();
						if(isset($fApiDataJsonDecode['status']) && $fApiDataJsonDecode['status']=='SUCCESS' && isset($fApiDataJsonDecode['data'])){
							$resultArray[$endDate][$metervalue['type']][$meterId]['meter']['flow'] = $fApiDataJsonDecode['data'];
						}
					}

					// Insert The API Result
					$pressureValue = isset($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['P_pressure']) ? json_encode($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['P_pressure']) : '';
					$temperatureValue = isset($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['T_temp']) ? json_encode($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['T_temp']) : '';
					$flowValue = isset($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['flow']) ? json_encode($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['flow']) : '';

					if($pressureValue!='' && $temperatureValue!='' && $flowValue!=''){
						$oo = $this->air_model->insertInAnomalyHistory($meterId,$pressureValue,$temperatureValue,$flowValue,$endDate,$_SESSION['user_id']);
					}else{
						break;
					}							

				}
			}
			
		}
		return $resultArray;

	}

	private function makeAPIcall($dataArray){

		//$post['filepath'] = $dataArray;
		//$post['keys'] = $string;

		$posts['dataset'] = json_encode($dataArray);

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL,"http://marketplace-dev.indusnetlabs.com/apicall/index/");
		curl_setopt($ch, CURLOPT_URL,"http://stage.en-view.com/api/AnamolyTendency/");
		//curl_setopt($ch, CURLOPT_URL,"http://localhost/effindexcalculator/api/effindex");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		//'Content-Type: application/json',
		//'Content-Length: ' . strlen($posts),
		//'Accept: multipart/form-data',
		curl_setopt($ch,CURLOPT_HTTPHEADER,array(
												
												'MPLACE-API-KEY: EAj3RNDel85LnhcATxIpsK2agi0bYOjDpCGgpjvLsAwQSJqMh917',
												'MPLACE-SECRETE-KEY: J5wthM04zhlWxTzToSwV1dKSrdvIYnyIs4bKRcOqt9n12XUrQq10'));

		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);

		$result = curl_exec ($ch);
		curl_close ($ch);

		return $result;
	}

	public function shift(){
		$this->data = array();
		$this->data['all'] = null; 
		$this->data['shiftVal'] = 8;
		$this->data['dateRange'] = $this->_getDateDetails();
		$this->data['dateRange']['selectedDate'] = $this->data['dateRange']['min_date'];
		$startDate = '';
		if(isset($_POST['startDate']) && isset($_POST['sifthour'])){
			$shiftHours = trim($_POST['sifthour']);
			$this->startDateTime = date('Y-m-d',strtotime($_POST['startDate']));
			if($shiftHours == 8){
				$shift = $this->air_model->getShiftStart(1);
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

			$this->data['last15DataSet'] = $this->air_model->getLastDateDataTypeWise($startDate ,$endDate);
			$this->data['typeWise'] = $this->_getTotalTypeWise();
			$this->data['dateRange']['selectedDate'] = $_POST['startDate'];
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

	
			}

			//echo "<pre>";print_r($retData);exit;
		}
		return $retData;
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

    	$data = $this->air_model->getAirMeterDataDay($meter_id, $selected);
    	

    	$data = (count($data)>0) ? array_reverse($data) : array();
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
    		$this->data['min_val_con'] = intval(min($maxMinConsis)) - 1;
    	}
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartShift(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$shiftDateStrToTime = trim($this->uri->segment(9));
    	if($shiftDateStrToTime == '' && $curVal > 0){
    		$shiftDateStrToTime = $curVal;
    	}
    	$this->startDateTime = $shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$shiftEndDate = $this->_dateAdd();
    	$selected = 'AM.name,AMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	/*if($type == 'temp'){
    		$selected .= ',SMD.T_temp';
    	}else{
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}*/
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

    	$data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);
    	
    	$data = (count($data)>0) ? array_reverse($data) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $column;
    	//echo "<pre>";
    	//print_r($curVal);

    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		        if($curVal != '' && $deltaGenVal != 0){
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
    		$this->data['min_val_con'] = intval(min($maxMinConsis)) - 1;
    	}
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartAllDay(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = '';
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	$deltaGenVal = '';
    	$staticDeltaVal = '';
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

    	$data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	

    	$data = (count($data)>0) ? array_reverse($data) : array();
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
    		$this->data['min_val_con'] = intval(min($maxMinConsis)) - 1;
    	}
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartAllDayCon(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(9));
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();
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

    	$data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	

    	$data = (count($data)>0) ? array_reverse($data) : array();
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
    		$this->data['min_val_con'] = intval(min($maxMinConsis)) - 1;
    	}
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
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
						
						$consistencyArray[$key2] = number_format(((1 - $value2/$i) * 100),2);

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