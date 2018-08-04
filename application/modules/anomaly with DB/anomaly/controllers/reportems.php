<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Reportems extends MY_Controller {
	public $data = array();
	public $currentStartDateTime = '';
	public $currentEndDateTime = '';
	public $dataTimeInterval = '';
	public $shiftViewArr = array(1 => 'Shift 1',2 => 'Shift 2', 3 => 'Shift 3', 4 => 'Day');
	public $shiftStartHour = 8;
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->load->model('ems_model');
		$this->_setCurrentDateTime();
	}

	public function index(){
		
		$this->data['get_meter'] =  $this->ems_model->getDevice();

		$this->data['typeId'] = $this->uri->segment(4);
		$this->data['shiftViewArr'] = $this->shiftViewArr;
		$this->data['minMaxDate'] = $this->_getDateDetails();
		$this->data['selectedDate'] = $this->data['minMaxDate']['max_date'];
		$this->data['selectedShift'] = 1;
		$this->data['all'] = '';
		$this->data['chartDate'] = '';
		$startDate = '';
		$endDate = '';

		if(isset($_POST['endDate']) && isset($_POST['meter'])){
			
			$this->endDateTime = date('Y-m-d',strtotime($_POST['endDate']));//'2017-09-25';		

			$shift = 8;
			$this->dataTimeInterval = 'PT'.$shift.'H00M00S';
			
			$this->endDateTime = $this->_dateAddonEnddate();

			$this->dataTimeInterval = 'PT24H00M00S';
			
			$this->endDateTime = $this->_dateAddonEnddate();

			// Check End Date Value Exists in Database or Not (Otherwise Call The API)
			$finalResultArray = array();
			$getAnomalyHistoryData = $this->ems_model->getAnomalyHistoryDataByDate($this->endDateTime,$_POST['meter']);
			if(isset($getAnomalyHistoryData) && count($getAnomalyHistoryData)==0){

				// Get Last End Date
				$getLastHistoryDate = $this->ems_model->getLastHistoryDateData($_POST['meter']);
				if(isset($getLastHistoryDate) && count($getLastHistoryDate)>0){
					$this->startDateTime = date('Y-m-d H:i:s',strtotime($getLastHistoryDate[0]->end_date_time));
				}else{
					$this->startDateTime = $this->_dateAddonDate('2017-09-19','PT'.$shift.'H00M00S');
				}

				// Get and generate The 15 minutes data date wise
				$dataArray = array();
				for ( $i = strtotime($this->startDateTime); $i <= strtotime($this->endDateTime); $i = $i + 86400 ) {
				  	$thisDate = date( 'Y-m-d H:i:s', $i ); 
				  
				  	$sDate = $thisDate;
					$eDate = $this->_dateAddonDate($sDate,'PT24H00M00S');

					if(strtotime($eDate)<=strtotime($this->endDateTime)){

						$this->data['last15DataSet'] = $this->ems_model->getLastDateDataTypeWise($_POST['meter'],$sDate ,$eDate);				

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

				$meterDetails = $this->ems_model->getMeterDetails($_POST['meter']);
				if(!isset($finalResultArray[$meterDetails[0]->type][$meterDetails[0]->device_id]['meter']['name'])){
					$finalResultArray[$meterDetails[0]->type][$meterDetails[0]->device_id]['meter']['name'] = isset($meterDetails[0]->device_name) ? $meterDetails[0]->device_name : '';
				}
				
				$finalResultArray[$meterDetails[0]->type][$meterDetails[0]->device_id]['meter']['KW'] = isset($getAnomalyHistoryData[0]->KW) ? json_decode($getAnomalyHistoryData[0]->KW,true) : '';
				$finalResultArray[$meterDetails[0]->type][$meterDetails[0]->device_id]['meter']['PF'] = isset($getAnomalyHistoryData[0]->PF) ? json_decode($getAnomalyHistoryData[0]->PF,true) : '';
				$finalResultArray[$meterDetails[0]->type][$meterDetails[0]->device_id]['meter']['Amps'] = isset($getAnomalyHistoryData[0]->Amps) ? json_decode($getAnomalyHistoryData[0]->Amps,true) : '';
				$finalResultArray[$meterDetails[0]->type][$meterDetails[0]->device_id]['meter']['Volt'] = isset($getAnomalyHistoryData[0]->Volt) ? json_decode($getAnomalyHistoryData[0]->Volt,true) : '';
				$finalResultArray[$meterDetails[0]->type][$meterDetails[0]->device_id]['meter']['HZ'] = isset($getAnomalyHistoryData[0]->Amps) ? json_decode($getAnomalyHistoryData[0]->HZ,true) : '';

			}		
			

			
			$this->data['data'] = $finalResultArray;

			$this->data['all'] = $this->load->view('reportems/all_day',$this->data, true);

			//die();

		}
		
		$this->load->view('reportems/index', $this->data);
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
					$getHistoryData = $this->ems_model->getAllHistoryDateData($meterId); 

					$runningInfoKW = array();
					$runningInfoPF = array();
					$runningInfoAmps = array();
					$runningInfoVolt = array();
					$runningInfoHZ = array();
					if(count($getHistoryData)>0){
						foreach ($getHistoryData as $Hiskey => $Hisvalue) {
							if(isset($Hisvalue->KW) && $Hisvalue->KW!=''){
								$jDecode = json_decode($Hisvalue->KW,true);
								if(isset($jDecode) && count($jDecode)>0){
									$runningInfoKW[] = $jDecode;
								}
								
							}
							if(isset($Hisvalue->PF) && $Hisvalue->PF!=''){
								$jDecode = json_decode($Hisvalue->PF,true);
								if(isset($jDecode) && count($jDecode)>0){
									$runningInfoPF[] = $jDecode;
								}
								
							}
							if(isset($Hisvalue->Amps) && $Hisvalue->Amps!=''){
								$jDecode = json_decode($Hisvalue->Amps,true);
								if(isset($jDecode) && count($jDecode)>0){
									$runningInfoAmps[] = $jDecode;
								}
								
							}
							if(isset($Hisvalue->Volt) && $Hisvalue->Volt!=''){
								$jDecode = json_decode($Hisvalue->Volt,true);
								if(isset($jDecode) && count($jDecode)>0){
									$runningInfoVolt[] = $jDecode;
								}
								
							}
							if(isset($Hisvalue->HZ) && $Hisvalue->HZ!=''){
								$jDecode = json_decode($Hisvalue->HZ,true);
								if(isset($jDecode) && count($jDecode)>0){
									$runningInfoHZ[] = $jDecode;
								}
								
							}
							
						}
					}
					
					// KW
					if(isset($metervalue['KW']) && count($metervalue['KW'])>0){
						$kwArray = array();
						foreach ($metervalue['KW'] as $kwdate => $kwvalue) {
							//$kwArray[] = ['Date'=>$kwdate,'KW'=>$kwvalue];
							$kwArray[] = ['KW'=>$kwvalue];
						}	

						$arrKWaddiInfo = array("data_key" => "KW","given_high" => "","given_low" => "", "running_info" => $runningInfoKW);
						$arrKW = array('data'=>($kwArray),"additional_info"=>($arrKWaddiInfo));

						// Call DIY1 API
						$kwApiData = $this->makeAPIcall($arrKW);

						$kwApiDataJsonDecode = json_decode($kwApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['KW'] = array();
						if(isset($kwApiDataJsonDecode['status']) && $kwApiDataJsonDecode['status']=='SUCCESS' && isset($kwApiDataJsonDecode['data'])){
							$resultArray[$endDate][$metervalue['type']][$meterId]['meter']['KW'] = $kwApiDataJsonDecode['data'];
						}
						
					}
					// PF
					if(isset($metervalue['PF']) && count($metervalue['PF'])>0){
						$pfArray = array();
						foreach ($metervalue['PF'] as $pfdate => $pfvalue) {
							//$pfArray[] = ['Date'=>$pfdate,'PF'=>$pfvalue];
							$pfArray[] = ['PF'=>$pfvalue];
						}	

						$arrPFaddiInfo = array("data_key" => "PF","given_high" => "","given_low" => "", "running_info" => $runningInfoPF);
						$arrPF = array('data'=>($pfArray),"additional_info"=>($arrPFaddiInfo));

						// Call DIY1 API
						$pfApiData = $this->makeAPIcall($arrPF);

						$pfApiDataJsonDecode = json_decode($pfApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['PF'] = array();
						if(isset($pfApiDataJsonDecode['status']) && $pfApiDataJsonDecode['status']=='SUCCESS' && isset($pfApiDataJsonDecode['data'])){
							$resultArray[$endDate][$metervalue['type']][$meterId]['meter']['PF'] = $pfApiDataJsonDecode['data'];
						}
						
					}

					// Volt
					if(isset($metervalue['Volt']) && count($metervalue['Volt'])>0){
						$VoltArray = array();
						foreach ($metervalue['Volt'] as $Voltdate => $Voltvalue) {
							//$VoltArray[] = ['Date'=>$Voltdate,'Volt'=>$Voltvalue];
							$VoltArray[] = ['Volt'=>$Voltvalue];
						}		

						$arrVOLTaddiInfo = array("data_key" => "Volt","given_high" => "","given_low" => "", "running_info" => $runningInfoVolt);
						$arrVolt = array('data'=>($VoltArray),"additional_info"=>($arrVOLTaddiInfo));

						// Call DIY1 API
						$VoltApiData = $this->makeAPIcall($arrVolt);

						$VoltApiDataJsonDecode = json_decode($VoltApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['Volt'] = array();
						if(isset($VoltApiDataJsonDecode['status']) && $VoltApiDataJsonDecode['status']=='SUCCESS' && isset($VoltApiDataJsonDecode['data'])){
							$resultArray[$endDate][$metervalue['type']][$meterId]['meter']['Volt'] = $VoltApiDataJsonDecode['data'];
						}
						
					}

					// Amps
					if(isset($metervalue['Amps']) && count($metervalue['Amps'])>0){
						$AmpsArray = array();
						foreach ($metervalue['Amps'] as $Ampsdate => $Ampsvalue) {
							//$AmpsArray[] = ['Date'=>$Ampsdate,'Amps'=>$Ampsvalue];
							$AmpsArray[] = ['Amps'=>$Ampsvalue];
						}

						$arrAMPSaddiInfo = array("data_key" => "Amps","given_high" => "","given_low" => "", "running_info" => $runningInfoAmps);
						$arrAmps = array('data'=>($AmpsArray),"additional_info"=>($arrAMPSaddiInfo));		

						// Call DIY1 API
						$AmpsApiData = $this->makeAPIcall($arrAmps);
						
						$AmpsApiDataJsonDecode = json_decode($AmpsApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['Amps'] = array();
						if(isset($AmpsApiDataJsonDecode['status']) && $AmpsApiDataJsonDecode['status']=='SUCCESS' && isset($AmpsApiDataJsonDecode['data'])){
							$resultArray[$endDate][$metervalue['type']][$meterId]['meter']['Amps'] = $AmpsApiDataJsonDecode['data'];
						}
						
					}

					// HZ
					if(isset($metervalue['HZ']) && count($metervalue['HZ'])>0){
						$HZArray = array();
						foreach ($metervalue['HZ'] as $HZdate => $HZvalue) {
							//$HZArray[] = ['Date'=>$HZdate,'HZ'=>$HZvalue];
							$HZArray[] = ['HZ'=>$HZvalue];
						}	

						$arrHZaddiInfo = array("data_key" => "HZ","given_high" => "","given_low" => "", "running_info" => $runningInfoHZ);
						$arrHZ = array('data'=>($HZArray),"additional_info"=>($arrHZaddiInfo));		

						// Call DIY1 API
						$HZApiData = $this->makeAPIcall($arrHZ);

						$HZApiDataJsonDecode = json_decode($HZApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['HZ'] = array();
						if(isset($HZApiDataJsonDecode['status']) && $HZApiDataJsonDecode['status']=='SUCCESS' && isset($HZApiDataJsonDecode['data'])){
							$resultArray[$endDate][$metervalue['type']][$meterId]['meter']['HZ'] = $HZApiDataJsonDecode['data'];
						}
						
					}
					//echo "<pre>";
					//var_dump($resultArray);

					// Insert The API Result
					$KWValues = isset($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['KW']) ? json_encode($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['KW']) : '';
					$PFValues = isset($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['PF']) ? json_encode($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['PF']) : '';
					$AmpsValues = isset($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['Amps']) ? json_encode($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['Amps']) : '';
					$VoltValues = isset($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['Volt']) ? json_encode($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['Volt']) : '';
					$HZValues = isset($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['HZ']) ? json_encode($resultArray[$endDate][$metervalue['type']][$meterId]['meter']['HZ']) : '';

					if($KWValues!='' && $PFValues!='' && $AmpsValues!='' && $VoltValues!='' && $HZValues!=''){
						$oo = $this->ems_model->insertInAnomalyHistory($meterId,$KWValues,$PFValues,$AmpsValues,$VoltValues,$HZValues,$endDate,$_SESSION['user_id']);
					}else{
						break;
					}							

				}
			}
			
		}
		return $resultArray;

	}


	private function _regenerateTheDataSet($dataArray){

		$newArray = array();
		if(count($dataArray)>0){
			foreach ($dataArray as $key123 => $value123) {
				foreach ($value123 as $key => $value) {
					foreach ($value as $key1 => $value1) {
						foreach ($value1['meter'] as $key2 => $value2) {
							$newArray[$key123][$key2]['meter'] = $value2['device_name'];
							$newArray[$key123][$key2]['type'] = $key1;
							if(isset($value2['KW'])){
								$newArray[$key123][$key2]['KW'][$key] = $value2['KW'];
							}
							if(isset($value2['PF'])){
								$newArray[$key123][$key2]['PF'][$key] = $value2['PF'];
							}
							if(isset($value2['Amps'])){
								$newArray[$key123][$key2]['Amps'][$key] = $value2['Amps'];
							}
							if(isset($value2['Volt'])){
								$newArray[$key123][$key2]['Volt'][$key] = $value2['Volt'];
							}
							if(isset($value2['HZ'])){
								$newArray[$key123][$key2]['HZ'][$key] = $value2['HZ'];
							}							
						}
					}
				}
			}
		}

		return $newArray;
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

	private function _makingTypeData(){
		$masterArr = array();
		if(count($this->data['meterWiseData']) > 0){
			foreach ($this->data['meterWiseData'] as $value) {
				$masterArr[$value->type][$value->device_id]['name'] = $value->device_name;
				$masterArr[$value->type][$value->device_id]['data'][$value->short_name][] = $value->data;
			}
		}
		/*echo "<pre>";
		print_r($masterArr);
		exit;*/
		return $masterArr;
	}

	private function _getTotalTypeWise1(){
		
		$data = array();
		if(count($this->data['last15DataSet'])){			
			
			foreach ($this->data['last15DataSet'] as $key => $value) {

				$data[date('m/d/Y H:i:s',strtotime($value->end_date_time))][$value->type]['meter'][$value->device_id]['device_name'] = $value->device_name;

				if(isset($value->short_name) && $value->short_name=="KW" && isset($value->data) && $value->data>0){
					$data[date('m/d/Y H:i:s',strtotime($value->end_date_time))][$value->type]['meter'][$value->device_id]['KW'] = $value->data;
				}
				if(isset($value->short_name) && $value->short_name=="PF" && isset($value->data) && $value->data>0){
					$data[date('m/d/Y H:i:s',strtotime($value->end_date_time))][$value->type]['meter'][$value->device_id]['PF'] = $value->data;
				}
				if(isset($value->short_name) && $value->short_name=="Volt" && isset($value->data) && $value->data>0){
					$data[date('m/d/Y H:i:s',strtotime($value->end_date_time))][$value->type]['meter'][$value->device_id]['Volt'] = $value->data;
				}
				if(isset($value->short_name) && $value->short_name=="Amps" && isset($value->data) && $value->data>0){
					$data[date('m/d/Y H:i:s',strtotime($value->end_date_time))][$value->type]['meter'][$value->device_id]['Amps'] = $value->data;
				}
				if(isset($value->short_name) && $value->short_name=="HZ" && isset($value->data) && $value->data>0){
					$data[date('m/d/Y H:i:s',strtotime($value->end_date_time))][$value->type]['meter'][$value->device_id]['HZ'] = $value->data;
				}
			}
		}

		return $data;
	}
	private function _getTotalTypeWise(){
		$retData = array();
		if(count($this->data['last15DataSet'])){
			$data = array();
			
			foreach ($this->data['last15DataSet'] as $key => $value) {

				$data[$value->type]['meter'][$value->device_id]['device_name'] = $value->device_name;

				if(isset($value->short_name) && $value->short_name=="KW"){
					$data[$value->type]['meter'][$value->device_id]['KW'][] = $value->data;
				}
				if(isset($value->short_name) && $value->short_name=="PF"){
					$data[$value->type]['meter'][$value->device_id]['PF'][] = $value->data;
				}
				if(isset($value->short_name) && $value->short_name=="Volt"){
					$data[$value->type]['meter'][$value->device_id]['Volt'][] = $value->data;
				}
				if(isset($value->short_name) && $value->short_name=="Amps"){
					$data[$value->type]['meter'][$value->device_id]['Amps'][] = $value->data;
				}
				if(isset($value->short_name) && $value->short_name=="HZ"){
					$data[$value->type]['meter'][$value->device_id]['HZ'][] = $value->data;
				}
				

				//var_dump($value);
			}
			//print_r($data);
			$resultArray = array();
			if(count($data)>0){
				foreach ($data as $key1 => $value1) {
					foreach ($value1['meter'] as $key2 => $value2) {

						$resultArray[$key1]['meter'][$key2]['name'] = $value2['device_name'];

						if(!isset($resultArray[$key1]['meter'][$key2]['KW'])) $resultArray[$key1]['meter'][$key2]['KW'] = 0;
						if(!isset($resultArray[$key1]['meter'][$key2]['PF'])) $resultArray[$key1]['meter'][$key2]['PF'] = 0;
						if(!isset($resultArray[$key1]['meter'][$key2]['Amps'])) $resultArray[$key1]['meter'][$key2]['Amps'] = 0;
						if(!isset($resultArray[$key1]['meter'][$key2]['Volt'])) $resultArray[$key1]['meter'][$key2]['Volt'] = 0;
						if(!isset($resultArray[$key1]['meter'][$key2]['HZ'])) $resultArray[$key1]['meter'][$key2]['HZ'] = 0;

						if(isset($value2['KW'])){
							$resultArray[$key1]['meter'][$key2]['KW'] = array_sum($value2['KW']);							
						}
						if(isset($value2['Amps'])){
							$resultArray[$key1]['meter'][$key2]['Amps'] = array_sum($value2['Amps']);							
						}
						if(isset($value2['Volt'])){
							$resultArray[$key1]['meter'][$key2]['Volt'] = array_sum($value2['Volt']) / count($value2['Volt']);
						}
						if(isset($value2['HZ'])){
							$resultArray[$key1]['meter'][$key2]['HZ'] = array_sum($value2['HZ']) / count($value2['HZ']);
						}
						if(isset($value2['KW']) && isset($value2['PF'])){
							$kwPFsum = 0;
							foreach ($value2['KW'] as $keyIndex => $keyVal) {
							
								if($value2['PF'][$keyIndex]>0){
									$kwPFsum+= $value2['KW'][$keyIndex] / $value2['PF'][$keyIndex];
								}
								
							}
							if(isset($resultArray[$key1]['meter'][$key2]['KW']) && $resultArray[$key1]['meter'][$key2]['KW']>0){
								$resultArray[$key1]['meter'][$key2]['PF'] = ($kwPFsum / $resultArray[$key1]['meter'][$key2]['KW']);
							}
							
						}
						
					}
				}
			}
			//var_dump($resultArray);
		}
		return $resultArray;
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

    private function _setDateInterVal(){
    	$retData['startDate'] = '';
    	$retData['endDate'] = '';
    	if($this->data['selectedShift'] == 1){
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif($this->data['selectedShift'] == 2){
			$this->dataTimeInterval = 'PT16H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif ($this->data['selectedShift'] == 3) {
			$this->dataTimeInterval = 'PT24H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}elseif ($this->data['selectedShift'] == 4) {
			$this->dataTimeInterval = 'PT08H00M00S';
			$retData['startDate'] = $this->startDateTime = $this->_dateAdd();
			$this->dataTimeInterval = 'PT24H00M00S';
			$retData['endDate'] = $this->_dateAdd();
		}
		return $retData;
    }


    private function _getDateDetails(){		
		$data = $this->ems_model->getDateDetails();
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
		            "value": "'.round($value->data,3).'"
		        },';
		        $maxMin[] = $value->data;
    		}
    	}
    	$this->data['dataSet'] = $dataSet;
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	$this->data['dataSetConsis'] = '';
		$this->load->view('report/chart', $this->data);
	}
}

?>