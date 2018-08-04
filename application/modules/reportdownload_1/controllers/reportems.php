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

		if(isset($_POST['startDate']) && isset($_POST['endDate']) && isset($_POST['meter'])){
			$this->startDateTime = date('Y-m-d',strtotime($_POST['startDate']));
			$this->endDateTime = date('Y-m-d',strtotime($_POST['endDate']));//'2017-09-25';		

			$shift = 8;
			$this->dataTimeInterval = 'PT'.$shift.'H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
			$this->endDateTime = $this->_dateAddonEnddate();

			$this->dataTimeInterval = 'PT24H00M00S';
			$endDate = $this->_dateAdd();
			$finalEndDate = $this->endDateTime = $this->_dateAddonEnddate();

			
			/// Get Date Wise Data Aggregator Data			
			$dataArray = array();
			for ( $i = strtotime($this->startDateTime); $i <= strtotime($this->endDateTime); $i = $i + 86400 ) {
			  $thisDate = date( 'Y-m-d H:i:s', $i ); 
			  
			  	$sDate = $thisDate;
				$eDate = $this->_dateAddonDate($sDate,'PT24H00M00S');				

				if(strtotime($eDate)<=strtotime($this->endDateTime)){
			  		
					$this->data['last15DataSet'] = $this->ems_model->getLastDateDataTypeWise($_POST['meter'],$sDate ,$eDate);
					

					//$this->data['typeWise'] = $this->_getTotalTypeWise();
					//$this->data['typeWise'] = $this->_getTotalTypeWise1();
					

					//$dataArray[date('m/d/Y',strtotime($sDate))] = $this->_getTotalTypeWise();
					$dataArray[] = $this->_getTotalTypeWise1();

			  	}			  

			}
			

			// Iterate The Data
			$newArray = array();
			if(count($dataArray)>0){
				foreach ($dataArray as $key123 => $value123) {
					foreach ($value123 as $key => $value) {
						foreach ($value as $key1 => $value1) {
							foreach ($value1['meter'] as $key2 => $value2) {
								$newArray[$key2]['meter'] = $value2['device_name'];
								$newArray[$key2]['type'] = $key1;
								if(isset($value2['KW'])){
									$newArray[$key2]['KW'][$key] = $value2['KW'];
								}
								if(isset($value2['PF'])){
									$newArray[$key2]['PF'][$key] = $value2['PF'];
								}
								if(isset($value2['Amps'])){
									$newArray[$key2]['Amps'][$key] = $value2['Amps'];
								}
								if(isset($value2['Volt'])){
									$newArray[$key2]['Volt'][$key] = $value2['Volt'];
								}
								if(isset($value2['HZ'])){
									$newArray[$key2]['HZ'][$key] = $value2['HZ'];
								}
							}
						}
					}
				}
			}
			

			// Temp Array
			$newArray1 = array();			
			foreach ($newArray as $keytemp => $valuetemp) {
				if($keytemp==$_POST['meter']){// For Selected Meter
					$newArray1[$keytemp] = $valuetemp;
				}
			}

			// Ready For Call The DIY1
			$resultArray = array();
			if(count($newArray1)>0){
				foreach ($newArray1 as $meterId => $metervalue) {

					if(!isset($resultArray[$metervalue['type']][$meterId]['meter']['name'])){
						$resultArray[$metervalue['type']][$meterId]['meter']['name'] = isset($metervalue['meter']) ? $metervalue['meter'] : '';
					}
					
					// KW
					if(isset($metervalue['KW']) && count($metervalue['KW'])>0){
						$kwArray = array();
						foreach ($metervalue['KW'] as $kwdate => $kwvalue) {
							$kwArray[] = ['Date'=>$kwdate,'KW'=>$kwvalue];
						}						
						// Call DIY1 API
						$kwApiData = $this->makeAPIcall($kwArray,'B');

						$kwApiDataJsonDecode = json_decode($kwApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['KW'] = array();
						if(isset($kwApiDataJsonDecode['status']) && $kwApiDataJsonDecode['status']=='true' && isset($kwApiDataJsonDecode['message']['data']['B'])){
							$resultArray[$metervalue['type']][$meterId]['meter']['KW'] = $kwApiDataJsonDecode['message']['data']['B'];
						}
						
					}

					// PF
					if(isset($metervalue['PF']) && count($metervalue['PF'])>0){
						$pfArray = array();
						foreach ($metervalue['PF'] as $pfdate => $pfvalue) {
							$pfArray[] = ['Date'=>$pfdate,'PF'=>$pfvalue];
						}						
						// Call DIY1 API
						$pfApiData = $this->makeAPIcall($pfArray,'B');

						$pfApiDataJsonDecode = json_decode($pfApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['PF'] = array();
						if(isset($pfApiDataJsonDecode['status']) && $pfApiDataJsonDecode['status']=='true' && isset($pfApiDataJsonDecode['message']['data']['B'])){
							$resultArray[$metervalue['type']][$meterId]['meter']['PF'] = $pfApiDataJsonDecode['message']['data']['B'];
						}
						
					}

					// Volt
					if(isset($metervalue['Volt']) && count($metervalue['Volt'])>0){
						$VoltArray = array();
						foreach ($metervalue['Volt'] as $Voltdate => $Voltvalue) {
							$VoltArray[] = ['Date'=>$Voltdate,'Volt'=>$Voltvalue];
						}						
						// Call DIY1 API
						$VoltApiData = $this->makeAPIcall($VoltArray,'B');

						$VoltApiDataJsonDecode = json_decode($VoltApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['Volt'] = array();
						if(isset($VoltApiDataJsonDecode['status']) && $VoltApiDataJsonDecode['status']=='true' && isset($VoltApiDataJsonDecode['message']['data']['B'])){
							$resultArray[$metervalue['type']][$meterId]['meter']['Volt'] = $VoltApiDataJsonDecode['message']['data']['B'];
						}
						
					}

					// Amps
					if(isset($metervalue['Amps']) && count($metervalue['Amps'])>0){
						$AmpsArray = array();
						foreach ($metervalue['Amps'] as $Ampsdate => $Ampsvalue) {
							$AmpsArray[] = ['Date'=>$Ampsdate,'Amps'=>$Ampsvalue];
						}		

						// Call DIY1 API
						$AmpsApiData = $this->makeAPIcall($AmpsArray,'B');
						
						$AmpsApiDataJsonDecode = json_decode($AmpsApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['Amps'] = array();
						if(isset($AmpsApiDataJsonDecode['status']) && $AmpsApiDataJsonDecode['status']=='true' && isset($AmpsApiDataJsonDecode['message']['data']['B'])){
							$resultArray[$metervalue['type']][$meterId]['meter']['Amps'] = $AmpsApiDataJsonDecode['message']['data']['B'];
						}
						
					}

					// HZ
					if(isset($metervalue['HZ']) && count($metervalue['HZ'])>0){
						$HZArray = array();
						foreach ($metervalue['HZ'] as $HZdate => $HZvalue) {
							$HZArray[] = ['Date'=>$HZdate,'HZ'=>$HZvalue];
						}						
						// Call DIY1 API
						$HZApiData = $this->makeAPIcall($HZArray,'B');

						$HZApiDataJsonDecode = json_decode($HZApiData,true);
						
						$resultArray[$metervalue['type']][$meterId]['meter']['HZ'] = array();
						if(isset($HZApiDataJsonDecode['status']) && $HZApiDataJsonDecode['status']=='true' && isset($HZApiDataJsonDecode['message']['data']['B'])){
							$resultArray[$metervalue['type']][$meterId]['meter']['HZ'] = $HZApiDataJsonDecode['message']['data']['B'];
						}
						
					}
					
					
					
				}
			}

			$this->data['data'] = $resultArray;

			$this->data['all'] = $this->load->view('reportems/all_day',$this->data, true);

		}
		
		$this->load->view('reportems/index', $this->data);
	}

	private function makeAPIcall($dataArray,$string){

		$post['filepath'] = $dataArray;
		$post['keys'] = $string;

		$posts['dataset'] = json_encode($post);

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL,"http://localhost:81/apimarketplace/apicall/index/");
		//curl_setopt($ch, CURLOPT_URL,"http://localhost/DIY1api/index.php");
		curl_setopt($ch, CURLOPT_URL,"http://stage.en-view.com/DIY1api/index.php");
		//curl_setopt($ch, CURLOPT_URL,"http://marketplace-dev.indusnetlabs.com/apicall/index/");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		//'Content-Type: application/json',
		//'Content-Length: ' . strlen($posts),
		//'Accept: multipart/form-data',
		curl_setopt($ch,CURLOPT_HTTPHEADER,array(
												
												'MPLACE-API-KEY: al0RYN8W2sECyJG2NNlBSDThxwZurjSBFTsDHBAJ3flCZ2EMP024',
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