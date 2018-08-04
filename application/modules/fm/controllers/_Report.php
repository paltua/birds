<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller {
	public $data = array();
	public $shiftArr = array();
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->shiftArr = array(1,2,3);
		$this->load->model('fm_model');
		$this->load->model('cpp_model');

	}

	public function index(){
		$this->data = array();
		$this->data['all'] = ''; 
		$this->data['dateRange'] = $this->_getDateDetails();
		$this->data['dateRange']['selectedDate'] =  $this->data['dateRange']['min_date'];
		$this->data['dateRange']['selectedDateEnd'] =  $this->data['dateRange']['min_date'];
		$startDate = '';
		if(isset($_POST['startDate'])){
			$this->startDateTime = date('Y-m-d',strtotime($_POST['startDate']));
			$shift = $this->fm_model->getShiftStart(1);
			$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
			$this->startDateTime = date('Y-m-d',strtotime($_POST['endDate']));
			$this->dataTimeInterval = 'PT24H00M00S';
			$endDate = $this->_dateAdd();
			
			$this->data['last15DataSet'] = $this->fm_model->getLastDateDataTypeWise($startDate ,$endDate);

			//$this->data['lastmintsTTLflow_DataSet'] = $this->getTTLflowLastMinuteCalculation($startDate ,$endDate);

			/*echo "<pre>";
			var_dump($startDate);
			var_dump($endDate);*/		

			$this->data['TTL_flow_new'] = $this->getTTLflowNewCalculation($startDate ,$endDate,'PT24H00M00S');

			/*echo "<pre>";
			var_dump($this->data['TTL_flow_new']);
			die();*/
			////////////////////////////////
			
			///////////////////////////////
			$this->data['typeWise'] = $this->_getTotalTypeWise();
			$this->data['dateRange']['selectedDate'] =  date('Y-m-d',strtotime($_POST['startDate']));
			$this->data['dateRange']['selectedDateEnd'] =  date('Y-m-d',strtotime($_POST['endDate']));
			

			///////////// Get Data From Datalog CPP ****************Start
	        $this->data['last15DataSet_cpp'] = $this->cpp_model->getLastDateDataTypeWise($startDate ,$endDate);
	        //$this->data['last15DataSet_cpp'] = $this->cpp_model->getLastDateDataTypeWise('2017-11-08 13:00:00' ,'2017-11-09 13:00:00');
	        
	        /*echo "<pre>";
	        var_dump($startDate);
	        var_dump($endDate);
	        //var_dump($this->data['last15DataSet_cpp']);
	        $t = array();
	        foreach ($this->data['last15DataSet_cpp'] as $tmpkey => $tmpvalue) {
	        	if(isset($tmpvalue->meter_id) && $tmpvalue->meter_id==2 && $tmpvalue->pressure>0){
	        		//var_dump($tmpvalue->end_date_time);
	        		//var_dump($tmpvalue->pressure);
	        		$t[] = array('date'=>$tmpvalue->end_date_time,'p'=>$tmpvalue->pressure);
	        	}
	        }
	        var_dump(json_encode($t));*/

	        $this->data['result_cpp'] = $this->geCalculationResult_cpp();
	        
	        //var_dump($this->data['result_cpp']);
	        //die();
	        ///////////// Get Data From Datalog CPP *****************End


			$this->data['all'] = $this->load->view('report/all',$this->data, true);
		}
		
		$this->load->view('report/index', $this->data);
	}

	private function getTTLflowNewCalculationShift($startDate,$endDate,$interval){

		$newStartDate = $startDate;//date('Y-m-d H:i:s',strtotime($this->_dateSub($startDate,$interval)));
		$newEndDate = $endDate;//date('Y-m-d H:i:s',strtotime($this->_dateSub($endDate,$interval)));

		$begin = $newStartDate;//new DateTime($newStartDate);
		$end   = $newEndDate;//new DateTime($newEndDate);


		/*echo "<pre>";
		var_dump($startDate);
		var_dump($endDate);

		var_dump($newStartDate);
		var_dump($newEndDate);*/

		$arr = array();
		if(isset($begin) && $begin!='' && isset($end) && $end!=''){
			for($i = $begin; $i <= $end; $i=$this->_dateAddcustom($i,$interval)){

				//echo $i;
				if($i!=''){
					$arr[$i] = $this->fm_model->getTTL_flow_newDateDataTypeWise($i);
				}
			    
			}
		}

		//var_dump($arr);
		//die();
		$newArr = array();
		if(isset($arr) && count($arr)>0){
			foreach ($arr as $dkey => $dvalue) {
				foreach ($dvalue as $mkey => $mvalue) {					
					$newArr[$mvalue->type][$mvalue->meter_id][$dkey] = isset($mvalue->data_cfm) ? $mvalue->data_cfm : 0;	
					
				}
				
			}
		}
		/*echo "<pre>";
		var_dump($newArr);
		die();*/

		$resultArray = array();
		if(isset($newArr) && count($newArr)>0){
			foreach ($newArr as $tkey => $tvalue) {
				foreach ($tvalue as $mmkey => $mmvalue) {
					if(!isset($resultArray[$tkey][$mmkey]['TTL_flow'])) $resultArray[$tkey][$mmkey]['TTL_flow'] = 0;
					if(isset($mmvalue) && count($mmvalue)>0){
						$descArray = array_reverse($mmvalue);
						

						foreach ($descArray as $ddkey => $ddvalue) {
							$cDate = $ddkey;
							$lDate = $this->_dateSub($cDate,$interval);
							
							//var_dump($lDate);

							if(isset($lDate) && $lDate!='' && isset($ddvalue) && $ddvalue>0 && $ddvalue!='' && isset($descArray[$lDate]) && $descArray[$lDate]!='' && $descArray[$lDate]>0){
								

								$resultArray[$tkey][$mmkey]['TTL_flow']+= ($ddvalue - $descArray[$lDate]);	
							}
						}
					}
					
				}
			}
		}

		/*var_dump($resultArray);
		die();*/

		return $resultArray;
		//var_dump($resultArray);

	}

	private function getTTLflowNewCalculationShiftForOnclick($startDate,$endDate,$interval){

		$newStartDate = $startDate;//date('Y-m-d H:i:s',strtotime($this->_dateSub($startDate,$interval)));
		$newEndDate = $endDate;//date('Y-m-d H:i:s',strtotime($this->_dateSub($endDate,$interval)));

		$begin = $newStartDate;//new DateTime($newStartDate);
		$end   = $newEndDate;//new DateTime($newEndDate);


		/*echo "<pre>";
		var_dump($startDate);
		var_dump($endDate);

		var_dump($newStartDate);
		var_dump($newEndDate);*/
		$returnDataSet = array('splits'=>array(),'result'=>array());

		$arr = array();
		if(isset($begin) && $begin!='' && isset($end) && $end!=''){
			for($i = $begin; $i <= $end; $i=$this->_dateAddcustom($i,$interval)){

				//echo $i;
				if($i!=''){
					$arr[$i] = $this->fm_model->getTTL_flow_newDateDataTypeWise($i);
				}
			    
			}
		}

		//var_dump($arr);
		//die();
		$newArr = array();
		if(isset($arr) && count($arr)>0){
			foreach ($arr as $dkey => $dvalue) {
				foreach ($dvalue as $mkey => $mvalue) {					
					$newArr[$mvalue->type][$mvalue->meter_id][$dkey] = isset($mvalue->data_cfm) ? $mvalue->data_cfm : 0;	
					
				}
				
			}
		}
		/*echo "<pre>";
		var_dump($newArr);
		die();*/
		$returnDataSet['splits'] = $newArr;

		$resultArray = array();
		if(isset($newArr) && count($newArr)>0){

			foreach ($newArr as $tkey => $tvalue) {
				foreach ($tvalue as $mmkey => $mmvalue) {
					if(!isset($resultArray[$tkey][$mmkey]['TTL_flow'])) $resultArray[$tkey][$mmkey]['TTL_flow'] = 0;
					if(isset($mmvalue) && count($mmvalue)>0){
						$descArray = array_reverse($mmvalue);
						

						foreach ($descArray as $ddkey => $ddvalue) {
							$cDate = $ddkey;
							$lDate = $this->_dateSub($cDate,$interval);
							
							//var_dump($lDate);

							if(isset($lDate) && $lDate!='' && isset($ddvalue) && $ddvalue>0 && $ddvalue!='' && isset($descArray[$lDate]) && $descArray[$lDate]!='' && $descArray[$lDate]>0){
								

								$resultArray[$tkey][$mmkey]['TTL_flow']+= ($ddvalue - $descArray[$lDate]);	
							}
						}
					}
					
				}
			}
		}

		$returnDataSet['result'] = $resultArray;

		/*var_dump($resultArray);
		die();*/

		return $returnDataSet;
		//var_dump($resultArray);

	}

	private function getTTLflowNewCalculationAllDayForOnclick($startDate,$endDate,$interval){

		$newStartDate = $startDate;//date('Y-m-d H:i:s',strtotime($this->_dateSub($startDate,$interval)));
		$newEndDate = $endDate;//date('Y-m-d H:i:s',strtotime($this->_dateSub($endDate,$interval)));

		$begin = $newStartDate;//new DateTime($newStartDate);
		$end   = $newEndDate;//new DateTime($newEndDate);


		/*echo "<pre>";
		var_dump($startDate);
		var_dump($endDate);

		var_dump($newStartDate);
		var_dump($newEndDate);*/
		$returnDataSet = array('splits'=>array(),'result'=>array());

		$arr = array();
		if(isset($begin) && $begin!='' && isset($end) && $end!=''){
			for($i = $begin; $i <= $end; $i=$this->_dateAddcustom($i,$interval)){

				//echo $i;
				if($i!=''){
					$arr[$i] = $this->fm_model->getTTL_flow_newDateDataTypeWise($i);
				}
			    
			}
		}

		//var_dump($arr);
		//die();
		$newArr = array();
		if(isset($arr) && count($arr)>0){
			foreach ($arr as $dkey => $dvalue) {
				foreach ($dvalue as $mkey => $mvalue) {					
					$newArr[$mvalue->type][$mvalue->meter_id][$dkey] = isset($mvalue->data_cfm) ? $mvalue->data_cfm : 0;	
					
				}
				
			}
		}
		/*echo "<pre>";
		var_dump($newArr);
		die();*/
		$returnDataSet['splits'] = $newArr;

		$resultArray = array();
		if(isset($newArr) && count($newArr)>0){

			foreach ($newArr as $tkey => $tvalue) {
				foreach ($tvalue as $mmkey => $mmvalue) {
					if(!isset($resultArray[$tkey][$mmkey]['TTL_flow'])) $resultArray[$tkey][$mmkey]['TTL_flow'] = 0;
					if(isset($mmvalue) && count($mmvalue)>0){
						$descArray = array_reverse($mmvalue);
						

						foreach ($descArray as $ddkey => $ddvalue) {
							$cDate = $ddkey;
							$lDate = $this->_dateSub($cDate,$interval);
							
							//var_dump($lDate);

							if(isset($lDate) && $lDate!='' && isset($ddvalue) && $ddvalue>0 && $ddvalue!='' && isset($descArray[$lDate]) && $descArray[$lDate]!='' && $descArray[$lDate]>0){
								

								$resultArray[$tkey][$mmkey]['TTL_flow']+= ($ddvalue - $descArray[$lDate]);	
							}
						}
					}
					
				}
			}
		}

		$returnDataSet['result'] = $resultArray;

		/*var_dump($resultArray);
		die();*/

		return $returnDataSet;
		//var_dump($resultArray);

	}

	private function getTTLflowNewCalculation($startDate,$endDate,$interval){

		//$newStartDate = date('Y-m-d H:i:s',strtotime($this->_dateSub($startDate,$interval)));
		//$newEndDate = date('Y-m-d H:i:s',strtotime($this->_dateSub($endDate,$interval)));

		$begin = $startDate;//new DateTime($newStartDate);
		$end   = $endDate;//new DateTime($newEndDate);


		//echo "<pre>";
		//var_dump($startDate);
		//var_dump($endDate);

		//var_dump($begin);
		//var_dump($end);

		$arr = array();
		if(isset($begin) && $begin!='' && isset($end) && $end!=''){
			for($i = $begin; $i <= $end; $i=$this->_dateAddcustom($i,$interval)){

				//echo $i;
				if($i!=''){
					$arr[$i] = $this->fm_model->getTTL_flow_newDateDataTypeWise($i);
				}
			    
			}
		}

		/*var_dump($arr);
		die();*/
		$newArr = array();
		if(isset($arr) && count($arr)>0){
			foreach ($arr as $dkey => $dvalue) {
				foreach ($dvalue as $mkey => $mvalue) {					
					$newArr[$mvalue->type][$mvalue->meter_id][$dkey] = isset($mvalue->data_cfm) ? $mvalue->data_cfm : 0;	
					
				}
				
			}
		}
		/*echo "<pre>";
		var_dump($newArr);
		die();*/

		$resultArray = array();
		if(isset($newArr) && count($newArr)>0){
			foreach ($newArr as $tkey => $tvalue) {
				foreach ($tvalue as $mmkey => $mmvalue) {
					if(!isset($resultArray[$tkey][$mmkey]['TTL_flow'])) $resultArray[$tkey][$mmkey]['TTL_flow'] = 0;
					if(isset($mmvalue) && count($mmvalue)>0){
						$descArray = array_reverse($mmvalue);
						

						foreach ($descArray as $ddkey => $ddvalue) {
							$cDate = $ddkey;
							$lDate = $this->_dateSub($cDate,$interval);
							
							//var_dump($lDate);

							if(isset($lDate) && $lDate!='' && isset($ddvalue) && $ddvalue>0 && $ddvalue!='' && isset($descArray[$lDate]) && $descArray[$lDate]!='' && $descArray[$lDate]>0){
								

								$resultArray[$tkey][$mmkey]['TTL_flow']+= ($ddvalue - $descArray[$lDate]);	
							}
						}
					}
					
				}
			}
		}
		/*echo "<pre>";
		var_dump($resultArray);
		die();*/

		return $resultArray;
		//var_dump($resultArray);

	}

	private function getTTLflowLastMinuteCalculation($startDate,$endDate){

		$newStartDate = date('Y-m-d',strtotime($this->_dateSub($startDate,'PT24H00M00S')));
		$newEndDate = date('Y-m-d',strtotime($this->_dateSub($endDate,'PT24H00M00S')));

		$begin = new DateTime($newStartDate);
		$end   = new DateTime($newEndDate);

		$arr = array();
		if(isset($begin) && $begin!='' && isset($end) && $end!=''){
			for($i = $begin; $i <= $end; $i->modify('+1 day')){

				if($i->format("Y-m-d 23:45:00")!=''){
					$arr[$i->format("Y-m-d 23:45:00")] = $this->fm_model->getLastMinuteDateDataTypeWise($i->format("Y-m-d 23:45:00"));
				}
			    
			}
		}
		

			
		$newArr = array();
		if(isset($arr) && count($arr)>0){
			foreach ($arr as $dkey => $dvalue) {
				foreach ($dvalue as $mkey => $mvalue) {					
					$newArr[$mvalue->type][$mvalue->meter_id][$dkey] = isset($mvalue->TTL_flow) ? $mvalue->TTL_flow : 0;	
					
				}
				
			}
		}
		//echo "<pre>";
		//var_dump($newArr);

		$resultArray = array();
		if(isset($newArr) && count($newArr)>0){
			foreach ($newArr as $tkey => $tvalue) {
				foreach ($tvalue as $mmkey => $mmvalue) {
					if(!isset($resultArray[$tkey][$mmkey]['TTL_flow'])) $resultArray[$tkey][$mmkey]['TTL_flow'] = 0;
					if(isset($mmvalue) && count($mmvalue)>0){
						$descArray = array_reverse($mmvalue);
					
						foreach ($descArray as $ddkey => $ddvalue) {
							$cDate = $ddkey;
							$lDate = $this->_dateSub($cDate,'PT24H00M00S');
							
							if(isset($lDate) && $lDate!='' && isset($ddvalue) && $ddvalue>0 && $ddvalue!='' && isset($descArray[$lDate]) && $descArray[$lDate]!='' && $descArray[$lDate]>0){
								$resultArray[$tkey][$mmkey]['TTL_flow']+= ($ddvalue - $descArray[$lDate]);	
							}
						}
					}
					
				}
			}
		}
		
			
		return $resultArray;
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
				//echo $shift[0]->config_val;
				$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
				$startDate = $this->startDateTime = $this->_dateAdd();
			}elseif($shiftHours == 16){
				$this->dataTimeInterval = 'PT08H00M00S';
				$startDate = $this->startDateTime = $this->_dateAdd();
			}elseif($shiftHours == 24){
				$this->dataTimeInterval = 'PT16H00M00S';
				$startDate = $this->startDateTime = $this->_dateAdd();
			}
			

			$this->dataTimeInterval = 'PT08H00M00S';
			$endDate = $this->_dateAdd();

			$this->data['last15DataSet'] = $this->fm_model->getLastDateDataTypeWise($startDate ,$endDate);
			$this->data['typeWise'] = $this->_getTotalTypeWise();

			$nStartDate = $startDate;//$this->_dateSub($startDate,'PT24H00M00S');
			$nEndDate = $endDate;//$this->_dateSub($endDate,'PT24H00M00S');
			$this->data['TTL_flow_new'] = $this->getTTLflowNewCalculationShift($nStartDate ,$nEndDate,'PT08H00M00S');

			/*echo "<pre>";
			var_dump($this->data['TTL_flow_new']);
			die();*/

			//$this->data['dateRange'] = $this->_getDateDetails();
			$this->data['dateRange']['selectedDate'] =  date('Y-m-d',strtotime($_POST['startDate']));
			$this->data['shiftVal'] = strtotime($startDate);


			///////////// Get Data From Datalog CPP ****************Start
	        $this->data['last15DataSet_cpp'] = $this->cpp_model->getLastDateDataTypeWise($startDate ,$endDate);
	        //$this->data['last15DataSet_cpp'] = $this->cpp_model->getLastDateDataTypeWise('2017-11-08 13:00:00' ,'2017-11-08 20:00:00');

	        $this->data['result_cpp'] = $this->geCalculationResult_cpp();
	        
	        //var_dump($this->data['result_cpp']);
	        //die();
	        ///////////// Get Data From Datalog CPP *****************End


			$this->data['all'] = $this->load->view('shift/all',$this->data, true);
		}
		//die();
		
		$this->load->view('shift/index', $this->data);
	}

	private function geCalculationResult_cpp(){
		$retData = array();
		if(count($this->data['last15DataSet_cpp'])){
			$data = array();
			$typeWiseArr = array();
			foreach ($this->data['last15DataSet_cpp'] as $key => $value) {
				
				if($value->meter_id==1) continue;

				$data[$value->type]['meter'][$value->meter_id]['name'] = $value->name;

				$data[$value->type]['meter'][$value->meter_id]['pressure'][] = $value->pressure;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['pressure_count'])) $data[$value->type]['meter'][$value->meter_id]['pressure_count'] = 0;

				if($value->pressure>0){
					$data[$value->type]['meter'][$value->meter_id]['pressure_count'] = $data[$value->type]['meter'][$value->meter_id]['pressure_count'] + 1;
				}

				$data[$value->type]['meter'][$value->meter_id]['flow'][] = $value->flow;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['flow_count'])) $data[$value->type]['meter'][$value->meter_id]['flow_count'] = 0;
				if($value->flow>0){
					$data[$value->type]['meter'][$value->meter_id]['flow_count'] = $data[$value->type]['meter'][$value->meter_id]['flow_count'] + 1;
				}

				$data[$value->type]['meter'][$value->meter_id]['temp'][] = $value->temp;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['temp_count'])) $data[$value->type]['meter'][$value->meter_id]['temp_count'] = 0;
				if($value->temp>0){
					$data[$value->type]['meter'][$value->meter_id]['temp_count'] = $data[$value->type]['meter'][$value->meter_id]['temp_count'] + 1;
				}
				

			}
			
			if(count($data) > 0){
				foreach ($data as $key1 => $value1) {
					foreach($value1['meter'] as $key2 => $val2){
						

						$retData[$key1]['meter'][$key2]['name'] = $val2['name'];
						
						$retData[$key1]['meter'][$key2]['flow'] = (isset($val2['flow_count']) && $val2['flow_count']>0) ? (array_sum($val2['flow'])/$val2['flow_count']) : 0;

						$retData[$key1]['meter'][$key2]['pressure'] = (isset($val2['pressure_count']) && $val2['pressure_count']>0) ? (array_sum($val2['pressure'])/$val2['pressure_count']) : 0;
						
						$retData[$key1]['meter'][$key2]['temp'] = (isset($val2['temp_count']) && $val2['temp_count']>0) ? (array_sum($val2['temp'])/$val2['temp_count']) : 0;

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
						if(!isset($tempArr[$kk]['total']['pressure'])) $tempArr[$kk]['total']['pressure'] = 0;
						if(!isset($tempArr[$kk]['total']['temp'])) $tempArr[$kk]['total']['temp'] = 0;
						
						$tempArr[$kk]['total']['flow']+=isset($vv1['flow']) ? $vv1['flow'] : 0;

						$tempArr[$kk]['total']['pressure']+= ($vv1['pressure'] * $vv1['flow']);

						$tempArr[$kk]['total']['temp']+=$vv1['temp'];

					}
				}
				//echo "<pre>";
				//var_dump($tempArr);

			}
			if(count($tempArr) > 0){
				foreach($tempArr as $kk4=>$vv4){

					if(!isset($retData[$kk4]['total']['flow'])) $retData[$kk4]['total']['flow'] = 0;
					if(!isset($retData[$kk4]['total']['pressure'])) $retData[$kk4]['total']['pressure'] = 0;
					if(!isset($retData[$kk4]['total']['temp'])) $retData[$kk4]['total']['temp'] = 0;
					
					$retData[$kk4]['total']['flow'] = isset($vv4['total']['flow']) ? $vv4['total']['flow'] : 0;

					$retData[$kk4]['total']['pressure'] = (isset($vv4['total']['flow']) && $vv4['total']['flow']>0) ? ($vv4['total']['pressure'] / $vv4['total']['flow']) : 0;

					$retData[$kk4]['total']['temp'] = (isset($vv4['total']['temp']) && $vv4['total']['temp']>0) ? ($vv4['total']['temp']) : 0;

				}
			}
			
		}
		return $retData;
	}

	private function _getTotalTypeWise(){
		//echo "<pre>";
		//var_dump($this->data['last15DataSet']);

		$retData = array();
		if(count($this->data['last15DataSet'])){
			$data = array();
			$typeWiseArr = array();
			foreach ($this->data['last15DataSet'] as $key => $value) {
				//var_dump($value->meter_id);
				$data[$value->type]['meter'][$value->meter_id]['name'] = $value->name;

				$data[$value->type]['meter'][$value->meter_id]['P_pressure'][] = $value->P_pressure;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['P_pressure_count'])) $data[$value->type]['meter'][$value->meter_id]['P_pressure_count'] = 1;

				if($value->P_pressure>0){
					$data[$value->type]['meter'][$value->meter_id]['P_pressure_count'] = $data[$value->type]['meter'][$value->meter_id]['P_pressure_count'] + 1;
				}

				$data[$value->type]['meter'][$value->meter_id]['flow'][] = $value->flow;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['flow_count'])) $data[$value->type]['meter'][$value->meter_id]['flow_count'] = 1;
				if($value->flow>0){
					$data[$value->type]['meter'][$value->meter_id]['flow_count'] = $data[$value->type]['meter'][$value->meter_id]['flow_count'] + 1;
				}

				$data[$value->type]['meter'][$value->meter_id]['T_temp'][] = $value->T_temp;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['T_temp_count'])) $data[$value->type]['meter'][$value->meter_id]['T_temp_count'] = 1;
				if($value->T_temp>0){
					$data[$value->type]['meter'][$value->meter_id]['T_temp_count'] = $data[$value->type]['meter'][$value->meter_id]['T_temp_count'] + 1;
				}	

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

						//$retData[$key1]['meter'][$key2]['flow'] = array_sum($val2['flow'])/count($val2['flow']);
						$retData[$key1]['meter'][$key2]['flow'] = array_sum($val2['flow'])/$val2['flow_count'];

						//$retData[$key1]['meter'][$key2]['P_pressure'] = array_sum($val2['P_pressure'])/count($val2['P_pressure']);
						$retData[$key1]['meter'][$key2]['P_pressure'] = array_sum($val2['P_pressure'])/$val2['P_pressure_count'];
						
						//$retData[$key1]['meter'][$key2]['T_temp'] = array_sum($val2['T_temp'])/count($val2['T_temp']);
						$retData[$key1]['meter'][$key2]['T_temp'] = array_sum($val2['T_temp'])/$val2['T_temp_count'];						

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

						if($kk1!=6 && $kk1!=8){
							$tempArr[$kk]['total']['flow']+=isset($vv1['flow']) ? $vv1['flow'] : 0;
						}
						

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
			//var_dump($tempArr);
			if(!isset($tempArr['main']['total']['flow'])) $tempArr['main']['total']['flow'] = 0;
			if(!isset($tempArr['dist']['total']['flow'])) $tempArr['dist']['total']['flow'] = 0;

			$tempArr['dist']['total']['flow'] = ($tempArr['dist']['total']['flow']+$tempArr['main']['total']['flow']);

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
		$data_cpp = $this->cpp_model->getDateDetails($meter_id);

		$retData['max_date'] = '';
		$retData['min_date'] = '';
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$retData['max_date'] = $value->max_date;
				$retData['min_date'] = $value->min_date;
			}
		}

		if(count($data_cpp) > 0){
			foreach ($data_cpp as $key1 => $value1) {
				if($value1->max_date>$retData['max_date']){
					$retData['max_date'] = $value1->max_date;
				}
				if($value1->min_date<$retData['min_date']){
					$retData['min_date'] = $value1->min_date;
				}				
				
			}
		}
		return $retData;
	}

    private function _dateAdd(){
        $date = new DateTime($this->startDateTime);
        $date->add(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

    private function _dateAddcustom($date,$timestamp){
        $date = new DateTime($date);
        $date->add(new DateInterval($timestamp));
        return $date->format('Y-m-d H:i:s');
    }

    private function _dateSub($date,$timestamp){
        $date = new DateTime($date);
        $date->sub(new DateInterval($timestamp));
        return $date->format('Y-m-d H:i:s');
    }

    public function getMeterChart(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$selected = 'SM.name,SMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	if($type == 'temp'){
    		$selected .= ',SMD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',SMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',SMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$data = $this->fm_model->getSteamMeterDataDay($meter_id, $selected);
    	
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


    public function getMeterChartShiftTotalGNvsDIST(){

    	$type = $this->uri->segment(4);
    	//$meterType = $this->uri->segment(5);

    	$curVal = $this->uri->segment(5);
    	$deltaGenVal = $this->uri->segment(6);
    	

    	$genconsRow = $deltaGenVal;

    	$shiftDateStrToTime = "";
    	if($curVal > 0){
    		$shiftDateStrToTime = $curVal;
    	}
    	

    	$this->startDateTime = $shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$shiftEndDate = $this->_dateAdd();

    	//echo $shiftStartDate;
    	//echo $shiftEndDate;

    	$selected = 'SM.name,SMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	
    	if($type == 'temp'){
    		$selected .= ',SMD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',SMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',SMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$this->data['dayCount'] = 1;

    	// Get The Meter Details by Type(Generation or distribution)
    	//$meterdata = $this->fm_model->getMeterTypeWise($meterType);
    	$meterdata = $this->fm_model->getMeter();
    	
    	// Fetch the data meter wise
    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {

    			if(isset($mvalue->type) && $mvalue->type=='main') continue;

    			$data[$mvalue->type][$mvalue->meter_id] = $this->fm_model->getSteamMeterDataDayShift($mvalue->meter_id,'*',$shiftStartDate, $shiftEndDate);

    		}
    	}

    	// Iterate Array according to End Date Time
    	/*$dataArray = $this->_setData($data); */
    	$dataArray = array();
    	if(isset($data) && count($data)>0){
    		foreach ($data as $dkeyType => $dvalue) {
    			$dataArray[$dkeyType] = $this->_setData($dvalue); 
    		}
    	}  

    	// Calculation Of Value
    	/*$dataFinalArray = $this->_calculationTimestamp($dataArray,$genconsRow,'8');  */
    	$dataFinalArray = array();
    	if(isset($dataArray) && count($dataArray)>0){
    		foreach ($dataArray as $mTypekey => $mTypevalue) {
    			$dataFinalArray[$mTypekey] = $this->_calculationTimestamp($mTypevalue,$genconsRow,'8'); 
    		}
    	} 

    	// Re-Structure The Array
    	if($genconsRow!=NULL && $genconsRow=='genconsRow'){
            $aa = 'genconsRow';
        }
    	$newDataArray = array();
    	if(isset($dataFinalArray) && count($dataFinalArray)>0){
    		foreach ($dataFinalArray as $typeKey => $typeValue) {
    			foreach ($typeValue as $dateTimekeys => $dateTimeValues) {
    				$newDataArray[$dateTimekeys][$typeKey] = $dateTimeValues[$aa];
    			}
    		}
    	}
    	
    	// Calculation For Generation Vs Distribution
    	$resultArray = array();
    	if(isset($newDataArray) && count($newDataArray)>0){
    		foreach ($newDataArray as $dateTimeskey => $dateTimesvalue) {
    			if(isset($dateTimesvalue['gen']) && isset($dateTimesvalue['dist'])){
    				$resultArray[$dateTimeskey] = ($dateTimesvalue['gen'] - $dateTimesvalue['dist']);
    			}else{
    				if(isset($dateTimesvalue['gen'])){
    					$resultArray[$dateTimeskey] = $dateTimesvalue['gen'];
    				}elseif(isset($dateTimesvalue['dist'])){
    					$resultArray[$dateTimeskey] = $dateTimesvalue['gen'];
    				}else{
    					$resultArray[$dateTimeskey] = '0';
    				}
    			}
    		}
    	}

    	
    	$dataFinalArray = (count($resultArray)>0) ? array_reverse($resultArray) : array();

    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = 'Generation Vs Distribution';
    	$this->data['meterNameColumn'] = ($genconsRow!=NULL && $genconsRow=='genconsRow') ? 'Generation - Distribution' : $column;
    	

    	if(count($dataFinalArray) > 0){
    		//$this->data['meterName'] = $data[0]->name;
    		foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
    			
		        
	        	$dataSet .= '{
		            "label": "'.$dateTimeKey.'",
		            "value": "'.round($dateTimeValue,3).'"
		        },';
		        $maxMin[] = $dateTimeValue;
		        
		        
    		}
    	}   	


    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
    }


    public function getMeterChartShiftTotal(){

    	$type = $this->uri->segment(4);
    	$meterType = $this->uri->segment(5);

    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$shiftDateStrToTime = trim($this->uri->segment(9));

    	//echo date('Y-m-d H:i:s',$shiftDateStrToTime);

    	$genconsRow = $deltaGenVal;

    	if($shiftDateStrToTime == '' && $curVal > 0){
    		$shiftDateStrToTime = $curVal;
    	}
    	

    	$this->startDateTime = $shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$shiftEndDate = $this->_dateAdd();

    	//echo $shiftStartDate;
    	//echo $shiftEndDate;

    	$selected = 'SM.name,SMD.end_date_time';
    	$aa = 'T_temp';
  		$column = 'Temperature';
    	
    	if($type == 'temp'){
    		$selected .= ',SMD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',SMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',SMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$this->data['dayCount'] = 1;

    	// Get The Meter Details by Type(Generation or distribution)
    	$meterdata = $this->fm_model->getMeterTypeWise($meterType);

    	// Fetch the data meter wise
    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			if($mvalue->meter_id!=6 && $mvalue->meter_id!=8){
    				$data[$mvalue->meter_id] = $this->fm_model->getSteamMeterDataDayShift($mvalue->meter_id,'*',$shiftStartDate, $shiftEndDate);
    			}
    		}
    	}

    	if(isset($meterType) && $meterType=='dist'){
            $meterdataForMain = $this->fm_model->getMeterTypeWise('main');
            if(isset($meterdataForMain) && count($meterdataForMain)>0){
                foreach ($meterdataForMain as $mkeyMain => $mvalueMain) {

                    $data[$mvalueMain->meter_id] = $this->fm_model->getSteamMeterData($mvalueMain->meter_id);                    

                }
            }
        }
        

    	// Iterate Array according to End Date Time
    	$dataArray = $this->_setData($data); 

    	// Calculation Of Value
    	$dataFinalArray = $this->_calculationTimestamp($dataArray,$genconsRow,'8');  


    	
    	$dataFinalArray = (count($dataFinalArray)>0) ? array_reverse($dataFinalArray) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Distribution';
    	$this->data['meterNameColumn'] = ($genconsRow!=NULL && $genconsRow=='genconsRow') ? (($meterType=='gen') ? 'Generation' : 'Consumption') : $column;
    	

    	if(count($dataFinalArray) > 0){
    		//$this->data['meterName'] = $data[0]->name;
    		foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
    			
		        if($curVal != '' && $deltaGenVal != 0){
		        	
		        	/*$dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($deltaGenVal - $dateTimeValue[$aa],3).'"
			        },';
					*/
			        $dataSet .= '{
			            "date": "'.$dateTimeKey.'",
			            "paramVal": "'.round($deltaGenVal - $dateTimeValue[$aa],3).'"
			        },';

			        $maxMin[] = $deltaGenVal - $dateTimeValue[$aa];
		        	$consistencyArr[$dateTimeKey] = $deltaGenVal - $dateTimeValue[$aa];
		        }else{

		        	if($genconsRow!=NULL && $genconsRow=='genconsRow'){
	                    $aa = 'genconsRow';
	                }
	                /*
		        	$dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue[$aa],3).'"
			        },';
					*/
			        $dataSet .= '{
			            "date": "'.$dateTimeKey.'",
			            "paramVal": "'.round($dateTimeValue[$aa],3).'"
			        },';

			        $maxMin[] = $dateTimeValue[$aa];
		        }
		        
    		}
    	}
    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
    	    $this->load->view('am_chart/line',$this->data);
        }else{
            $this->load->view('am_chart/bar',$this->data);
        }
        
    	//$this->load->view('report/chart',$this->data);
    }


    public function getMeterChartShiftForTotaliser(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = $this->uri->segment(6);   	

    	
    	$shiftDateStrToTime = '';
    	if($curVal > 0){
    		$shiftDateStrToTime = $curVal;
    	}
    	$shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
    	$shiftEndDate = $this->_dateAddcustom($shiftStartDate,'PT08H00M00S');

    	//echo "<pre>";
    	//var_dump($shiftStartDate);
    	//var_dump($shiftEndDate);

    	$nStartDate = $shiftStartDate;//$this->_dateSub($shiftStartDate,'PT24H00M00S');
		$nEndDate = $shiftEndDate;//$this->_dateSub($shiftEndDate,'PT24H00M00S');
		$datas = $this->getTTLflowNewCalculationShiftForOnclick($nStartDate ,$nEndDate,'PT08H00M00S');
		

    	
    	$meter_details = $this->fm_model->getMeterName($meter_id);
    	$this->data['meterName'] = isset($meter_details[0]->name) ? $meter_details[0]->name : 'NA';
    	
    	$meterType = isset($meter_details[0]->type) ? $meter_details[0]->type : 'NA';
    	$this->data['meterNameColumn'] = $meterType=='gen' ? 'Generation' : 'Consumption';
    	
    	if(isset($datas['splits'][$meterType][$meter_id]) && count($datas['splits'][$meterType][$meter_id])>0){
    		$this->data['TTL_flow_new'] = $datas['splits'][$meterType][$meter_id];
    	}
    	//echo "<pre>";
    	//var_dump($datas);
    	//var_dump($this->data);
    	//die();
    	
    	$this->load->view('shift/table',$this->data);
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
    	$selected = 'SM.name,SMD.end_date_time';
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
    		$selected .= ',SMD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',SMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',SMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}
    	$this->data['dayCount'] = 1;
    	$data = $this->fm_model->getSteamMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);
    	
    	$meterType = $this->fm_model->getMeterType($meter_id);
    	//var_dump($meterType);
    	$meterType = isset($meterType[0]->type) ? $meterType[0]->type : '';

    	$data = (count($data)>0) ? array_reverse($data) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = ($deltaGenVal!=NULL && $deltaGenVal=='genconsRow') ? ($meterType=='gen' ? 'Generation' : 'Consumption') : $column;
    	//echo "<pre>";
    	//print_r($curVal);

    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		        if($curVal != '' && $deltaGenVal != 0){
		        	/*
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';
					*/
			        $dataSet .= '{
			            "date": "'.$value->end_date_time.'",
			            "paramVal": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';

			        $maxMin[] = $deltaGenVal - $value->{$aa};
		        	$consistencyArr[$value->end_date_time] = $deltaGenVal - $value->{$aa};
		        }else{

		        	if($deltaGenVal!=NULL && $deltaGenVal=='genconsRow'){
		        		//$valsss = $value->{$aa} * 8;
		        		$valsss = $value->{$aa};
		        	}else{
		        		$valsss = $value->{$aa};
		        	}
		        	/*
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($valsss,3).'"
			        },';
					*/
			        $dataSet .= '{
			            "date": "'.$value->end_date_time.'",
			            "paramVal": "'.round($valsss,3).'"
			        },';

			        $maxMin[] = $valsss;
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
    	
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
    	    $this->load->view('am_chart/line',$this->data);
        }else{
            $this->load->view('am_chart/bar',$this->data);
        }
    	
    	//$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartAllDayTotalGNvsDIST(){
    	
    	$type = $this->uri->segment(4);
    	//$meterType = $this->uri->segment(5);

    	$curVal = '';
    	$shift = $this->fm_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(5));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	$genconsRow = $this->uri->segment(7);

    	//$meterdata = $this->fm_model->getMeterTypeWise($meterType);
    	$meterdata = $this->fm_model->getMeter();
    	//echo "<pre>";
    	
    	//var_dump($startDate);
    	//var_dump($endDate);

    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	$retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);

    	$aa = $retSelectArr['aa'];
    	$column = $retSelectArr['column'];
    	$selected = $retSelectArr['selected'];

    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {

 				if(isset($mvalue->type) && $mvalue->type=='main') continue;

    			if($this->data['dayCount'] == 1){
		    		$data[$mvalue->type][$mvalue->meter_id] = $this->fm_model->getSteamMeterDataDayShift($mvalue->meter_id, '*', $startDate, $endDate);
		    	}else{

		    		
	    			$selected = 'DATE_FORMAT(SMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(SMD.T_temp) T_temp, AVG(SMD.P_pressure) P_pressure, AVG(SMD.TTL_flow) TTL_flow, AVG(SMD.flow) flow, AVG(SMD.steam_enthalpy) steam_enthalpy,AVG(SMD.steam_heat_content) steam_heat_content';
	    	


		    		$data[$mvalue->type][$mvalue->meter_id] = $this->fm_model->getSteamMeterDataDayWiseParam($mvalue->meter_id, $selected, $startDate, $endDate);
		    	}


    		}
    	}
    	
    	// Iterate Array according to End Date Time
    	$dataArray = array();
    	if(isset($data) && count($data)>0){
    		foreach ($data as $dkeyType => $dvalue) {
    			$dataArray[$dkeyType] = $this->_setData($dvalue); 
    		}
    	} 
    	
    	

    	// Calculation Of Value
    	//$dataFinalArray = $this->_calculationTimestamp($dataArray,$genconsRow);   

    	// Calculation Of Value
    	$dataFinalArray = array();
    	if(isset($dataArray) && count($dataArray)>0){
    		foreach ($dataArray as $mTypekey => $mTypevalue) {
    			$dataFinalArray[$mTypekey] = $this->_calculationTimestamp($mTypevalue,$genconsRow,'24'); 
    		}
    	} 
    	

    	// Re-Structure The Array
    	if($genconsRow!=NULL && $genconsRow=='genconsRow'){
			$aa = 'genconsRow';
		}
    	$newDataArray = array();
    	if(isset($dataFinalArray) && count($dataFinalArray)>0){
    		foreach ($dataFinalArray as $typeKey => $typeValue) {
    			
    			foreach ($typeValue as $dateTimekeys => $dateTimeValues) {
    				$newDataArray[$dateTimekeys][$typeKey] = $dateTimeValues[$aa];
    			}
    		}
    	}
    	

    	// Calculation For Generation Vs Distribution
    	$resultArray = array();
    	if(isset($newDataArray) && count($newDataArray)>0){
    		foreach ($newDataArray as $dateTimeskey => $dateTimesvalue) {
    			if(isset($dateTimesvalue['gen']) && isset($dateTimesvalue['dist'])){
    				$resultArray[$dateTimeskey] = ($dateTimesvalue['gen'] - $dateTimesvalue['dist']);
    			}else{
    				if(isset($dateTimesvalue['gen'])){
    					$resultArray[$dateTimeskey] = $dateTimesvalue['gen'];
    				}elseif(isset($dateTimesvalue['dist'])){
    					$resultArray[$dateTimeskey] = $dateTimesvalue['gen'];
    				}else{
    					$resultArray[$dateTimeskey] = '0';
    				}
    			}
    		}
    	}

    	
    	
    	$resultArray = (count($resultArray)>0) ? array_reverse($resultArray) : array();
    	//var_dump($dataFinalArray);
    	

    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = 'Generation Vs Distribution';

    	$this->data['meterNameColumn'] = ($genconsRow!=NULL && $genconsRow=='genconsRow') ? 'Generation - Distribution' : $column;
    	if(count($data) > 0){
    			
    		foreach ($resultArray as $dateTimeKey => $dateTimeValue) {
    			
		        $dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue,3).'"
			        },';
			        $maxMin[] = $dateTimeValue;
		        
    		}
    	}
    	//var_dump($dataSet);
    	//die();

    	//////////////
    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	//////////////////////
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartAllDayTotal(){
    	
    	$type = $this->uri->segment(4);
    	$meterType = $this->uri->segment(5);

    	$curVal = '';
    	$shift = $this->fm_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	$genconsRow = $this->uri->segment(8);

    	$meterdata = $this->fm_model->getMeterTypeWise($meterType);
    	
    	//echo "<pre>";
    	//var_dump($meterdataForMain);
    	//var_dump($startDate);
    	//var_dump($endDate);

    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	$retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);

    	$aa = $retSelectArr['aa'];
    	$column = $retSelectArr['column'];
    	$selected = $retSelectArr['selected'];

    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
 				
 				if($mvalue->meter_id!=6 && $mvalue->meter_id!=8){
	    			if($this->data['dayCount'] == 1){
			    		$data[$mvalue->meter_id] = $this->fm_model->getSteamMeterDataDayShift($mvalue->meter_id, '*', $startDate, $endDate);
			    	}else{

		    			$selected = 'DATE_FORMAT(SMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(SMD.T_temp) T_temp, AVG(SMD.P_pressure) P_pressure, AVG(SMD.TTL_flow) TTL_flow, AVG(SMD.flow) flow, AVG(SMD.steam_enthalpy) steam_enthalpy,AVG(SMD.steam_heat_content) steam_heat_content';
		    	
						$data[$mvalue->meter_id] = $this->fm_model->getSteamMeterDataDayWiseParam($mvalue->meter_id, $selected, $startDate, $endDate);
			    	}
			    }

    		}
    	}

    	if(isset($meterType) && $meterType=='dist'){
    		$meterdataForMain = $this->fm_model->getMeterTypeWise('main');
    		if(isset($meterdataForMain) && count($meterdataForMain)>0){
	    		foreach ($meterdataForMain as $mkeyMain => $mvalueMain) {
	 
	    			if($this->data['dayCount'] == 1){
			    		$data[$mvalueMain->meter_id] = $this->fm_model->getSteamMeterDataDayShift($mvalueMain->meter_id, '*', $startDate, $endDate);
			    	}else{

			    		
		    			$selected = 'DATE_FORMAT(SMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(SMD.T_temp) T_temp, AVG(SMD.P_pressure) P_pressure, AVG(SMD.TTL_flow) TTL_flow, AVG(SMD.flow) flow, AVG(SMD.steam_enthalpy) steam_enthalpy,AVG(SMD.steam_heat_content) steam_heat_content';
		    	


			    		$data[$mvalueMain->meter_id] = $this->fm_model->getSteamMeterDataDayWiseParam($mvalueMain->meter_id, $selected, $startDate, $endDate);
			    	}


	    		}
	    	}
    	}

    	// Iterate Array according to End Date Time
    	$dataArray = $this->_setData($data);

    	// Calculation Of Value
    	$dataFinalArray = $this->_calculationTimestamp($dataArray,$genconsRow,'24');   
    	
    	$dataFinalArray = (count($dataFinalArray)>0) ? array_reverse($dataFinalArray) : array();
    	//var_dump($dataFinalArray);
    	

    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Distribution';

    	$this->data['meterNameColumn'] = ($genconsRow!=NULL && $genconsRow=='genconsRow') ? (($meterType=='gen') ? 'Generation' : 'Consumption') : $column;
    	if(count($data) > 0){
    			
    		foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
    			
    			if($genconsRow!=NULL && $genconsRow=='genconsRow'){
    				$aa = 'genconsRow';
    			}
    			/*
		        $dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue[$aa],3).'"
			        },';
			    */
			    $dataSet .= '{
			            "date": "'.$dateTimeKey.'",
			            "paramVal": "'.round($dateTimeValue[$aa],3).'"
			        },';
			        $maxMin[] = $dateTimeValue[$aa];
		        
    		}
    	}
    	//var_dump($dataSet);
    	//die();

    	//////////////
    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	//////////////////////
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
    	    $this->load->view('am_chart/line',$this->data);
        }else{
            $this->load->view('am_chart/bar',$this->data);
        }
    	//$this->load->view('report/chart',$this->data);
    }

    private function _calculationTimestamp($dataSet,$genconsRow=NULL,$multihours=24){

    	$finalArray = array();
    	if(isset($dataSet) && count($dataSet)>0){
    		foreach ($dataSet as $dateKey => $dateValue) {
    			
    			foreach ($dateValue as $meterkey => $metersvalue) {

    				if($genconsRow!=NULL){
    					$finalArray[$dateKey]['genconsRow'][] = $metersvalue['flow'] * $multihours;
    				}else{
    					$finalArray[$dateKey]['genconsRow'] = array();
    				}
    				$finalArray[$dateKey]['TTL_flow'][] = $metersvalue['TTL_flow'];
    				$finalArray[$dateKey]['flow'][] = $metersvalue['flow'];
    				$finalArray[$dateKey]['steam_heat_content'][] = $metersvalue['steam_heat_content'];

    				$finalArray[$dateKey]['P_pressure'][] = $metersvalue['P_pressure'];
    				$finalArray[$dateKey]['P_pressure_prod'][] = $metersvalue['P_pressure'] * $metersvalue['flow'];

    				$finalArray[$dateKey]['T_temp'][] = $metersvalue['T_temp'];
    				$finalArray[$dateKey]['T_temp_prod'][] = $metersvalue['T_temp'] * $metersvalue['flow'];

    				$finalArray[$dateKey]['steam_enthalpy'][] = $metersvalue['steam_enthalpy'];
    				$finalArray[$dateKey]['steam_enthalpy_prod'][] = $metersvalue['steam_enthalpy'] * $metersvalue['flow'];

    			}

    			//$finalArray[$dateKey] = array_sum($dateValue);
    		}
    	} 

    	$dataFinalArray = array();
    	if(isset($finalArray) && count($finalArray)>0){
    		foreach ($finalArray as $dateMkey => $dateMvalue) {
    			$dataFinalArray[$dateMkey]['TTL_flow'] = array_sum($dateMvalue['TTL_flow']);
    			$dataFinalArray[$dateMkey]['flow'] = array_sum($dateMvalue['flow']);
    			$dataFinalArray[$dateMkey]['steam_heat_content'] = array_sum($dateMvalue['steam_heat_content']);

    			$dataFinalArray[$dateMkey]['genconsRow'] = array_sum($dateMvalue['genconsRow']);

    			if($dataFinalArray[$dateMkey]['flow']>0){
    				$dataFinalArray[$dateMkey]['P_pressure'] = array_sum($dateMvalue['P_pressure_prod']) / $dataFinalArray[$dateMkey]['flow'];
    			}else{
    				$dataFinalArray[$dateMkey]['P_pressure'] = 0;
    			}

    			if($dataFinalArray[$dateMkey]['flow']>0){
    				$dataFinalArray[$dateMkey]['T_temp'] = array_sum($dateMvalue['T_temp_prod']) / $dataFinalArray[$dateMkey]['flow'];
    			}else{
    				$dataFinalArray[$dateMkey]['T_temp'] = 0;
    			}

    			if($dataFinalArray[$dateMkey]['flow']>0){
    				$dataFinalArray[$dateMkey]['steam_enthalpy'] = array_sum($dateMvalue['steam_enthalpy_prod']) / $dataFinalArray[$dateMkey]['flow'];
    			}else{
    				$dataFinalArray[$dateMkey]['steam_enthalpy'] = 0;
    			}
    		}
    	}


    	return $dataFinalArray;
    }

    private function _setData($dataSet){

    	$dataArray = array();
    	if(isset($dataSet) && count($dataSet)>0){
    		foreach ($dataSet as $meterId => $meter_value) {
    			foreach ($meter_value as $akey => $aValue) {
    				$dataArray[$aValue->end_date_time][$meterId]['TTL_flow'] = $aValue->TTL_flow;
    				$dataArray[$aValue->end_date_time][$meterId]['flow'] = $aValue->flow;
    				$dataArray[$aValue->end_date_time][$meterId]['steam_heat_content'] = $aValue->steam_heat_content;
    				$dataArray[$aValue->end_date_time][$meterId]['P_pressure'] = $aValue->P_pressure;
    				$dataArray[$aValue->end_date_time][$meterId]['T_temp'] = $aValue->T_temp;
    				$dataArray[$aValue->end_date_time][$meterId]['steam_enthalpy'] = $aValue->steam_enthalpy;
    			}
    			
    		}
    	}

    	return $dataArray;
    }

    public function getMeterChartAllDay(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = '';
    	$shift = $this->fm_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	$genDistRow = $this->uri->segment(8);
    	$meterType = $this->uri->segment(9);
    	
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	$retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);
    	$aa = $retSelectArr['aa'];
    	$column = $retSelectArr['column'];
    	$selected = $retSelectArr['selected'];
    	if($this->data['dayCount'] == 1){
    		$data = $this->fm_model->getSteamMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	}else{
    		$data = $this->fm_model->getSteamMeterDataDayWiseParam($meter_id, $selected, $startDate, $endDate);
    	}
    	
    	$data = (count($data)>0) ? array_reverse($data) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = ($genDistRow!=NULL && $genDistRow=='genconsRow') ? ($meterType=='gen' ? 'Generation' : 'Consumption') : $column;
    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		        if($curVal != ''){
		        	/*$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';
					*/
			        $dataSet .= '{
			            "date": "'.$value->end_date_time.'",
			            "paramVal": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';

			        $maxMin[] = $deltaGenVal - $value->{$aa};
		        	$consistencyArr[$value->end_date_time] = $deltaGenVal - $value->{$aa};
		        }else{

		        	if($genDistRow!=NULL && $genDistRow=='genconsRow'){
		        		if($this->data['dayCount']==1){
		        			$valsss = $value->{$aa};
		        		}else{
		        			$valsss = $value->{$aa};
		        		}
		        		
		        	}else{
		        		$valsss = $value->{$aa};
		        	}
		        	/*
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($valsss,3).'"
			        },';
					*/
			        $dataSet .= '{
			            "date": "'.$value->end_date_time.'",
			            "paramVal": "'.round($valsss,3).'"
			        },';

			        $maxMin[] = $valsss;
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
    	

    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
    	    $this->load->view('am_chart/line',$this->data);
        }else{
            $this->load->view('am_chart/bar',$this->data);
        }
    	//$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartAllDayForTotaliser(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	//$curVal = '';
    	$shift = $this->fm_model->getShiftStart(1);
    	//$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$startDateTime = date('Y-m-d', $this->uri->segment(6));
    	//$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';

    	//$startDate = $this->startDateTime = $this->_dateAdd();
    	$startDate = $startDateTime = $this->_dateAddcustom($startDateTime,$dataTimeInterval);

    	//$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$startDateTime = date('Y-m-d', $this->uri->segment(7));

    	//$this->dataTimeInterval = 'PT24H00M00S';
    	$dataTimeInterval = 'PT24H00M00S';

    	$endDate = $this->_dateAddcustom($startDateTime,$dataTimeInterval);

    	
    	$datas = $this->getTTLflowNewCalculationAllDayForOnclick($startDate ,$endDate,'PT24H00M00S');
    	$meter_details = $this->fm_model->getMeterName($meter_id);
    	$this->data['meterName'] = isset($meter_details[0]->name) ? $meter_details[0]->name : 'NA';
    	
    	$meterType = isset($meter_details[0]->type) ? $meter_details[0]->type : 'NA';
    	$this->data['meterNameColumn'] = $meterType=='gen' ? 'Generation' : 'Consumption';
    	
    	if(isset($datas['splits'][$meterType][$meter_id]) && count($datas['splits'][$meterType][$meter_id])>0){
    		$this->data['TTL_flow_new'] = $datas['splits'][$meterType][$meter_id];
    	}
    	//echo "<pre>";
    	//var_dump($datas);
    	//var_dump($this->data);
    	//die();
    	
    	$this->load->view('report/table',$this->data);
    }

    private function _createSelectQuery($type = 'pres', $day = 1){
    	$retData = array();
    	if($day == 1){
    		$selected = 'SM.name,SMD.end_date_time';
	    	$aa = 'T_temp';
	  		$column = 'Temperature';
	    	if($type == 'temp'){
	    		$selected .= ',SMD.T_temp';
	    	}else if($type == 'pres'){
	    		$selected .= ',SMD.P_pressure';
	    		$aa = 'P_pressure';
	    		$column = 'Pressure';
	    	}else if($type == 'flow'){
	    		$selected .= ',SMD.flow';
	    		$aa = 'flow';
	    		$column = 'Flow';
	    	}else{
	    		$selected .= ',SMD.steam_enthalpy';
	    		$aa = 'steam_enthalpy';
	    		$column = 'Enthalpy';
	    	}
    	}else{
    		$selected = 'SM.name,DATE_FORMAT(SMD.end_date_time, "%Y-%m-%d") end_date_time';
	    	$aa = 'T_temp';
	  		$column = 'Temperature';
	    	if($type == 'temp'){
	    		$selected .= ',AVG(NULLIF(SMD.T_temp,0)) T_temp';
	    	}else if($type == 'pres'){
	    		$selected .= ',AVG(NULLIF(SMD.P_pressure,0)) P_pressure';
	    		$aa = 'P_pressure';
	    		$column = 'Pressure';
	    	}else if($type == 'flow'){
	    		$selected .= ',AVG(NULLIF(SMD.flow,0)) flow';
	    		$aa = 'flow';
	    		$column = 'Flow';
	    	}else{
	    		$selected .= ',AVG(NULLIF(SMD.steam_enthalpy,0)) steam_enthalpy';
	    		$aa = 'steam_enthalpy';
	    		$column = 'Enthalpy';
	    	}
    	}
    	$retData['selected'] = $selected;
    	$retData['aa'] = $aa;
    	$retData['column'] = $column;
    	return $retData;
    	
    }

    public function getMeterChartAllDayCon(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$shiftDateStrToTime = trim($this->uri->segment(9));
    	$shift = $this->fm_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(9));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	$this->data['dayCount'] = 1;
    	$selected = 'SM.name,SMD.end_date_time';
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
    		$selected .= ',SMD.T_temp';
    	}else if($type == 'pres'){
    		$selected .= ',SMD.P_pressure';
    		$aa = 'P_pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',SMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',SMD.steam_enthalpy';
    		$aa = 'steam_enthalpy';
    		$column = 'Enthalpy';
    	}

    	$data = $this->fm_model->getSteamMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	
    	$data = (count($data)>0) ? array_reverse($data) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $column;
    	/*echo "<pre>";
    	print_r($data);*/

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


	private function _getDateDiff($startDate = '', $endDate = '', $type = '%d'){
		if ($startDate != '' && $endDate != '') {
			$datetime1 = date_create($startDate);
		    $datetime2 = date_create($endDate);
		    $interval = date_diff($datetime1, $datetime2);
		    return $interval->format($type);
		}else{
			return null;
		}
		
	}



	public function getMeterChartAllDay_CPP(){
    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = '';
    	$shift = $this->cpp_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	//$genDistRow = $this->uri->segment(8);
    	//$meterType = $this->uri->segment(9);
    	
    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	$retSelectArr = $this->_createSelectQuery_CPP($type, $this->data['dayCount']);
    	$aa = $retSelectArr['aa'];
    	$column = $retSelectArr['column'];
    	$selected = $retSelectArr['selected'];
    	if($this->data['dayCount'] == 1){
    		$data = $this->cpp_model->getDataloggerMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	}else{
    		/*echo "<pre>";
    		var_dump($startDate);
    		var_dump($endDate);
    		var_dump($selected);*/

    		$data = $this->cpp_model->getDataloggerMeterDataDayWiseParam($meter_id, $selected, $startDate, $endDate);
    	}
    	
    	$data = (count($data)>0) ? array_reverse($data) : array();


    	$dataSet = '';
    	//$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $column;
    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		        /*if($curVal != ''){
		        	$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($deltaGenVal - $value->{$aa},3).'"
			        },';
			        $maxMin[] = $deltaGenVal - $value->{$aa};
		        	$consistencyArr[$value->end_date_time] = $deltaGenVal - $value->{$aa};
		        }else{*/

		        	/*if($genDistRow!=NULL && $genDistRow=='genconsRow'){
		        		$valsss = $value->{$aa} * 24;
		        	}else{*/
		        		$valsss = $value->{$aa};
		        	//}
		        	
		        	/*$dataSet .= '{
			            "label": "'.$value->end_date_time.'",
			            "value": "'.round($valsss,3).'"
			        },';*/
			        $dataSet .= '{
			            "date": "'.$value->end_date_time.'",
			            "paramVal": "'.round($valsss,3).'"
			        },';
			        $maxMin[] = $valsss;
		        //}
		        
    		}
    	}
    	$this->data['consisIndex'] = 'no';
    	//$dataSetConsis = '';
    	//$maxMinConsis = array();
    	//$this->data['max_val_con'] = 0;
    	//$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	/*if(count($consistencyArr) > 0){
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
    	}*/
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	//$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->data['chartData'] = $dataSet;
        if($this->data['dayCount'] == 1){
    	    $this->load->view('am_chart/line',$this->data);
        }else{
            $this->load->view('am_chart/bar',$this->data);
        }

    	//$this->load->view('report/chart',$this->data);
    }

    private function _createSelectQuery_CPP($type = 'pres', $day = 1){
    	$retData = array();
    	if($day == 1){
    		$selected = 'DM.name,DMD.end_date_time';
	    	$aa = 'temp';
	  		$column = 'Temperature';
	    	if($type == 'temp'){
	    		$selected .= ',DMD.temp';
	    	}else if($type == 'pres'){
	    		$selected .= ',DMD.pressure';
	    		$aa = 'pressure';
	    		$column = 'Pressure';
	    	}else if($type == 'flow'){
	    		$selected .= ',DMD.flow';
	    		$aa = 'flow';
	    		$column = 'Flow';
	    	}else{
	    		$selected .= ',DMD.pressure';
	    		$aa = 'pressure';
	    		$column = 'Pressure';
	    	}
    	}else{
    		$selected = 'DM.name,DATE_FORMAT(DMD.end_date_time, "%Y-%m-%d") end_date_time';
	    	$aa = 'temp';
	  		$column = 'Temperature';
	    	if($type == 'temp'){
	    		$selected .= ',AVG(NULLIF(DMD.temp,0)) temp';
	    	}else if($type == 'pres'){
	    		$selected .= ',AVG(NULLIF(DMD.pressure,0)) pressure';
	    		$aa = 'pressure';
	    		$column = 'Pressure';
	    	}else if($type == 'flow'){
	    		$selected .= ',AVG(NULLIF(DMD.flow,0)) flow';
	    		$aa = 'flow';
	    		$column = 'Flow';
	    	}else{
	    		$selected .= ',AVG(NULLIF(DMD.pressure,0)) pressure';
	    		$aa = 'pressure';
	    		$column = 'Pressure';
	    	}
    	}
    	$retData['selected'] = $selected;
    	$retData['aa'] = $aa;
    	$retData['column'] = $column;
    	return $retData;
    	
    }


    public function getMeterChartAllDayTotal_CPP(){
    	
    	$type = $this->uri->segment(4);
    	$meterType = $this->uri->segment(5);

    	$curVal = '';
    	$shift = $this->fm_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	

    	$meterdata = $this->cpp_model->getMeterTypeWise($meterType);
    	//echo "<pre>";
    	
    	//var_dump($startDate);
    	//var_dump($endDate);

    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	$retSelectArr = $this->_createSelectQuery_CPP($type, $this->data['dayCount']);

    	$aa = $retSelectArr['aa'];
    	$column = $retSelectArr['column'];
    	$selected = $retSelectArr['selected'];

    	$data = array();
    	

    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
 				
 				if($mvalue->id==1) continue;

    			if($this->data['dayCount'] == 1){
		    		$data[$mvalue->id] = $this->cpp_model->getDataloggerMeterDataDayShift($mvalue->id, '*', $startDate, $endDate);
		    	}else{

		    		
	    			$selected = 'DATE_FORMAT(DMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(NULLIF(DMD.temp,0)) temp, AVG(NULLIF(DMD.pressure,0)) pressure, AVG(NULLIF(DMD.flow,0)) flow';
	    	


		    		$data[$mvalue->id] = $this->cpp_model->getDataloggerMeterDataDayWiseParam($mvalue->id, $selected, $startDate, $endDate);
		    	}


    		}
    	}
    	
    	/*echo "<pre>";
    	var_dump($data);*/
    	// Iterate Array according to End Date Time
    	$dataArray = $this->_setData_CPP($data);    	
    	/*echo "<pre>";
    	var_dump($dataArray);*/
    	

    	// Calculation Of Value
    	$dataFinalArray = $this->_calculationTimestamp_CPP($dataArray);   
    	
    	$dataFinalArray = (count($dataFinalArray)>0) ? array_reverse($dataFinalArray) : array();
    	//var_dump($dataFinalArray);
    	

    	$dataSet = '';
    	//$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Head';

    	$this->data['meterNameColumn'] = $column;
    	if(count($data) > 0){
    			
    		foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
    			
    			/*if($genconsRow!=NULL && $genconsRow=='genconsRow'){
    				$aa = 'genconsRow';
    			}*/
    			/*
		        $dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue[$aa],3).'"
			        },';*/
			    $dataSet .= '{
			            "date": "'.$dateTimeKey.'",
			            "paramVal": "'.round($dateTimeValue[$aa],3).'"
			        },';
			        $maxMin[] = $dateTimeValue[$aa];
		        
    		}
    	}
    	

    	//////////////
    	$this->data['consisIndex'] = 'no';
    	//$dataSetConsis = '';
    	//$maxMinConsis = array();
    	//$this->data['max_val_con'] = 0;
    	//$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	//////////////////////
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	//$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->data['chartData'] = $dataSet;
        if($this->data['dayCount'] == 1){
    	    $this->load->view('am_chart/line',$this->data);
        }else{
            $this->load->view('am_chart/bar',$this->data);
        }

    	//$this->load->view('report/chart',$this->data);
    }


    private function _setData_CPP($dataSet){

    	$dataArray = array();
    	if(isset($dataSet) && count($dataSet)>0){
    		foreach ($dataSet as $meterId => $meter_value) {
    			foreach ($meter_value as $akey => $aValue) {
    				
    				$dataArray[$aValue->end_date_time][$meterId]['flow'] = $aValue->flow;
    				
    				$dataArray[$aValue->end_date_time][$meterId]['pressure'] = $aValue->pressure;
    				$dataArray[$aValue->end_date_time][$meterId]['temp'] = $aValue->temp;
    				
    			}
    			
    		}
    	}

    	return $dataArray;
    }

    private function _calculationTimestamp_CPP($dataSet){

    	$finalArray = array();
    	if(isset($dataSet) && count($dataSet)>0){
    		foreach ($dataSet as $dateKey => $dateValue) {
    			
    			foreach ($dateValue as $meterkey => $metersvalue) {

    				
    				$finalArray[$dateKey]['flow'][] = $metersvalue['flow'];
    				

    				$finalArray[$dateKey]['pressure'][] = $metersvalue['pressure'];
    				$finalArray[$dateKey]['pressure_prod'][] = $metersvalue['pressure'] * $metersvalue['flow'];

    				$finalArray[$dateKey]['temp'] = $metersvalue['temp'];
    			}

    			//$finalArray[$dateKey] = array_sum($dateValue);
    		}
    	} 

    	$dataFinalArray = array();
    	if(isset($finalArray) && count($finalArray)>0){
    		foreach ($finalArray as $dateMkey => $dateMvalue) {
    			
    			$dataFinalArray[$dateMkey]['flow'] = array_sum($dateMvalue['flow']);
    			
    			$dataFinalArray[$dateMkey]['temp'] = $dateMvalue['temp'];
    			

    			if($dataFinalArray[$dateMkey]['flow']>0){
    				$dataFinalArray[$dateMkey]['pressure'] = array_sum($dateMvalue['pressure_prod']) / $dataFinalArray[$dateMkey]['flow'];
    			}else{
    				$dataFinalArray[$dateMkey]['pressure'] = 0;
    			}

    		}
    	}


    	return $dataFinalArray;
    }




    public function getMeterChartAllDay_CPPgenVSpph(){
    	
    	$type = $this->uri->segment(4);
    	$meterType = 'gen';
    	$meter_id = 4;

    	$curVal = '';
    	$shift = $this->fm_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(5));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$retSelectArr = $this->_createSelectQuery_CPP($type, $this->data['dayCount']);

    	$aa = $retSelectArr['aa'];
    	$column = $retSelectArr['column'];
    	$selected = $retSelectArr['selected'];

    	///////////// Get Auxuliary Header 
    	

    	
    	$data_axhead = array();
    	if($this->data['dayCount'] == 1){
    		$data_axhead = $this->cpp_model->getDataloggerMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	}else{
    		$data_axhead = $this->cpp_model->getDataloggerMeterDataDayWiseParam($meter_id, $selected, $startDate, $endDate);
    	}
    	
    	$data_axhead = (count($data_axhead)>0) ? array_reverse($data_axhead) : array();

    	// Re-Structure Aux Header Point Data 
        $auxHeaderArray = array();
        if(count($data_axhead)>0){
            foreach ($data_axhead as $indexkey => $datavalue) {
                $auxHeaderArray[$datavalue->end_date_time] = $datavalue->{$aa};
            }
        }

    	
    	///////////// End Auxuliary Header
    	///////////// CPP GEN Total

    	$meterdata = $this->cpp_model->getMeterTypeWise($meterType);
    	
    	//echo "<pre>";
    	
    	//var_dump($startDate);
    	//var_dump($endDate);

    	//$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	//$retSelectArr = $this->_createSelectQuery_CPP($type, $this->data['dayCount']);

    	//$aa = $retSelectArr['aa'];
    	//$column = $retSelectArr['column'];
    	//$selected = $retSelectArr['selected'];

    	$data_CPPTotal = array();
    	

    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
 				
 				if($mvalue->id==1) continue;

    			if($this->data['dayCount'] == 1){
		    		$data_CPPTotal[$mvalue->id] = $this->cpp_model->getDataloggerMeterDataDayShift($mvalue->id, '*', $startDate, $endDate);
		    	}else{

		    		
	    			$selected = 'DATE_FORMAT(DMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(DMD.temp) temp, AVG(DMD.pressure) pressure, AVG(DMD.flow) flow';
	    	


		    		$data_CPPTotal[$mvalue->id] = $this->cpp_model->getDataloggerMeterDataDayWiseParam($mvalue->id, $selected, $startDate, $endDate);
		    	}


    		}
    	}
    	
    	
    	// Iterate Array according to End Date Time
    	$dataArray_cppTotal = $this->_setData_CPP($data_CPPTotal);    	
    	
    	

    	// Calculation Of Value
    	$dataFinalArray_CppTotal = $this->_calculationTimestamp_CPP($dataArray_cppTotal);   
    	
    	$dataFinalArray_CppTotal = (count($dataFinalArray_CppTotal)>0) ? array_reverse($dataFinalArray_CppTotal) : array();
    	//var_dump($dataFinalArray);

    	/*echo "<pre>";
    	var_dump($auxHeaderArray);
    	var_dump($dataFinalArray_CppTotal);
    	die();*/

    	////////////////// End CPP GEN Total

    	$dataSet = '';
    	//$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = 'CPP Generation VS Power plant header';

    	$this->data['meterNameColumn'] = $column;
    	if(count($dataFinalArray_CppTotal) > 0){
    			
    		foreach ($dataFinalArray_CppTotal as $dateTimeKey => $dateTimeValue) {
    			
    			/*if($genconsRow!=NULL && $genconsRow=='genconsRow'){
    				$aa = 'genconsRow';
    			}*/
    			$v = 0;
    			if(isset($auxHeaderArray[$dateTimeKey])){
    				$v = $dateTimeValue[$aa] - $auxHeaderArray[$dateTimeKey];
    			}
    			/*
		        $dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($v,3).'"
			        },';
			    */
			    $dataSet .= '{
			            "date": "'.$dateTimeKey.'",
			            "paramVal": "'.round($v,3).'"
			        },';
			        $maxMin[] = $v;
		        
    		}
    	}
    	

    	//////////////
    	$this->data['consisIndex'] = 'no';
    	//$dataSetConsis = '';
    	//$maxMinConsis = array();
    	//$this->data['max_val_con'] = 0;
    	//$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	//////////////////////
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	//$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->data['chartData'] = $dataSet;
    	if($this->data['dayCount'] == 1){
    	    $this->load->view('am_chart/line',$this->data);
        }else{
            $this->load->view('am_chart/bar',$this->data);
        }

    	//$this->load->view('report/chart',$this->data);
    }



    public function getMeterChartShift_CPP(){

    	

    	$meter_id = $this->uri->segment(4);
    	$type = $this->uri->segment(5);
    	$curVal = $this->uri->segment(6);
    	//$deltaGenVal = $this->uri->segment(7);

    	$staticDeltaVal = $this->uri->segment(8);
    	$shiftDateStrToTime = trim($this->uri->segment(9));
    	if($shiftDateStrToTime == '' && $curVal > 0){
    		$shiftDateStrToTime = $curVal;
    	}
    	$this->startDateTime = $shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$shiftEndDate = $this->_dateAdd();
    	$selected = 'DM.name,DMD.end_date_time';
    	$aa = 'temp';
  		$column = 'Temperature';
    	if($type == 'temp'){
    		$selected .= ',DMD.temp';
    	}else if($type == 'pres'){
    		$selected .= ',DMD.pressure';
    		$aa = 'pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',DMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',DMD.pressure';
    		$aa = 'pressure';
    		$column = 'Pressure';
    	}
    	$this->data['dayCount'] = 1;
    	$data = $this->cpp_model->getDataloggerMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);
    	

    	$meterType = $this->cpp_model->getMeterType($meter_id);
    	//var_dump($meterType);
    	$meterType = isset($meterType[0]->type) ? $meterType[0]->type : '';

    	$data = (count($data)>0) ? array_reverse($data) : array();
    	$dataSet = '';
    	//$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = '';
    	$this->data['meterNameColumn'] = $column;
    	

    	if(count($data) > 0){
    		$this->data['meterName'] = $data[0]->name;
    		foreach ($data as $key => $value) {
    			
		       
	        	$valsss = $value->{$aa};
	        	
	        	/*
	        	$dataSet .= '{
		            "label": "'.$value->end_date_time.'",
		            "value": "'.round($valsss,3).'"
		        },';
				*/
		        $dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($valsss,3).'"
		        },';

		        $maxMin[] = $valsss;
		      		        
    		}
    	}
    	$this->data['consisIndex'] = 'no';
    	
    	$this->data['graph_logic'] = '1';
    	
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	//$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->data['chartData'] = $dataSet;
    	$this->load->view('am_chart/line',$this->data);
    	//$this->load->view('report/chart',$this->data);
    }


    public function getMeterChartShiftTotal_CPP(){

    	$type = $this->uri->segment(4);
    	$meterType = $this->uri->segment(5);

    	$curVal = $this->uri->segment(6);
    	$deltaGenVal = $this->uri->segment(7);
    	$staticDeltaVal = $this->uri->segment(8);
    	$shiftDateStrToTime = trim($this->uri->segment(9));

    	//echo date('Y-m-d H:i:s',$shiftDateStrToTime);

    	//$genconsRow = $deltaGenVal;

    	if($shiftDateStrToTime == '' && $curVal > 0){
    		$shiftDateStrToTime = $curVal;
    	}
    	

    	$this->startDateTime = $shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$shiftEndDate = $this->_dateAdd();

    	//echo $shiftStartDate;
    	//echo $shiftEndDate;

    	$selected = 'DM.name,DMD.end_date_time';
    	$aa = 'temp';
  		$column = 'Temperature';
    	
    	if($type == 'temp'){
    		$selected .= ',DMD.temp';
    	}else if($type == 'pres'){
    		$selected .= ',DMD.pressure';
    		$aa = 'pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',DMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',DMD.pressure';
    		$aa = 'pressure';
    		$column = 'Pressure';
    	}

    	$this->data['dayCount'] = 1;

    	// Get The Meter Details by Type(Generation or distribution)
    	$meterdata = $this->cpp_model->getMeterTypeWise($meterType);

    	// Fetch the data meter wise
    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			
    			if($mvalue->id==1) continue;

    			$data[$mvalue->id] = $this->cpp_model->getDataloggerMeterDataDayShift($mvalue->id,'*',$shiftStartDate, $shiftEndDate);

    		}
    	}

    	// Iterate Array according to End Date Time
    	$dataArray = $this->_setData_CPP($data); 

    	// Calculation Of Value
    	$dataFinalArray = $this->_calculationTimestamp_CPP($dataArray);  


    	
    	$dataFinalArray = (count($dataFinalArray)>0) ? array_reverse($dataFinalArray) : array();
    	$dataSet = '';
    	//$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Head';
    	$this->data['meterNameColumn'] =  $column;
    	

    	if(count($dataFinalArray) > 0){
    		//$this->data['meterName'] = $data[0]->name;
    		foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
    			
		       
    				/*
		        	$dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue[$aa],3).'"
			        },';
					*/
			        $dataSet .= '{
			            "date": "'.$dateTimeKey.'",
			            "paramVal": "'.round($dateTimeValue[$aa],3).'"
			        },';

			        $maxMin[] = $dateTimeValue[$aa];
		        
		        
    		}
    	}
    	$this->data['consisIndex'] = 'no';
    	
    	$this->data['graph_logic'] = '1';
    	
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	//$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->data['chartData'] = $dataSet;
    	$this->load->view('am_chart/line',$this->data);
    	
    	//$this->load->view('report/chart',$this->data);
    }



    public function getMeterChartShift_CPPgenVSpph(){

    	$type = $this->uri->segment(4);
    	$meterType = 'gen';
    	$meter_id = 4;

    	$curVal = $this->uri->segment(5);
    	$deltaGenVal = $this->uri->segment(6);
    	$staticDeltaVal = $this->uri->segment(7);
    	$shiftDateStrToTime = trim($this->uri->segment(8));

    	//echo date('Y-m-d H:i:s',$shiftDateStrToTime);

    	//$genconsRow = $deltaGenVal;

    	if($shiftDateStrToTime == '' && $curVal > 0){
    		$shiftDateStrToTime = $curVal;
    	}
    	

    	$this->startDateTime = $shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
    	$this->dataTimeInterval = 'PT08H00M00S';
    	$shiftEndDate = $this->_dateAdd();

    	//echo $shiftStartDate;
    	//echo $shiftEndDate;

    	$selected = 'DM.name,DMD.end_date_time';
    	$aa = 'temp';
  		$column = 'Temperature';
    	
    	if($type == 'temp'){
    		$selected .= ',DMD.temp';
    	}else if($type == 'pres'){
    		$selected .= ',DMD.pressure';
    		$aa = 'pressure';
    		$column = 'Pressure';
    	}else if($type == 'flow'){
    		$selected .= ',DMD.flow';
    		$aa = 'flow';
    		$column = 'Flow';
    	}else{
    		$selected .= ',DMD.pressure';
    		$aa = 'pressure';
    		$column = 'Pressure';
    	}

    	$this->data['dayCount'] = 1;

    	/////////////////// Start Get Auxuliary Header 
    	$data_auxHeader = $this->cpp_model->getDataloggerMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);
    	$data_auxHeader = (count($data_auxHeader)>0) ? array_reverse($data_auxHeader) : array();

    	// Re-Structure Aux Header Point Data 
        $auxHeaderArray = array();
        if(count($data_auxHeader)>0){
            foreach ($data_auxHeader as $indexkey => $datavalue) {
                $auxHeaderArray[$datavalue->end_date_time] = $datavalue->{$aa};
            }
        }

    	////////////////// End Auxuliary header
    	/////////////////// Start CPP GEN TOtal
    	// Get The Meter Details by Type(Generation or distribution)
    	$meterdata = $this->cpp_model->getMeterTypeWise($meterType);

    	// Fetch the data meter wise
    	$data_CPPtotal = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			
    			if($mvalue->id==1) continue;
    			
    			$data_CPPtotal[$mvalue->id] = $this->cpp_model->getDataloggerMeterDataDayShift($mvalue->id,'*',$shiftStartDate, $shiftEndDate);

    		}
    	}

    	// Iterate Array according to End Date Time
    	$dataArray_cppTot = $this->_setData_CPP($data_CPPtotal); 

    	// Calculation Of Value
    	$dataFinalArray_cpptot = $this->_calculationTimestamp_CPP($dataArray_cppTot);  


    	
    	$dataFinalArray_cpptot = (count($dataFinalArray_cpptot)>0) ? array_reverse($dataFinalArray_cpptot) : array();

    	/////////////////// End CPP GEN TOtal
    	$dataSet = '';
    	//$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Head';
    	$this->data['meterNameColumn'] =  $column;
    	

    	if(count($dataFinalArray_cpptot) > 0){
    		//$this->data['meterName'] = $data[0]->name;
    		foreach ($dataFinalArray_cpptot as $dateTimeKey => $dateTimeValue) {
    				
    				$v = 0;
    				if(isset($auxHeaderArray[$dateTimeKey])){
    					$v = ($dateTimeValue[$aa] - $auxHeaderArray[$dateTimeKey]);
    				}		        
    				/*
		        	$dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($v,3).'"
			        },';
					*/
			        $dataSet .= '{
			            "date": "'.$dateTimeKey.'",
			            "paramVal": "'.round($v,3).'"
			        },';

			        $maxMin[] = $v;
		        
		        
    		}
    	}
    	$this->data['consisIndex'] = 'no';
    	//$dataSetConsis = '';
    	//$maxMinConsis = array();
    	//$this->data['max_val_con'] = 0;
    	//$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - 1;
    	$this->data['dataSet'] = $dataSet;
    	//$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->data['chartData'] = $dataSet;
    	$this->load->view('am_chart/line',$this->data);

    	//$this->load->view('report/chart',$this->data);
    }
	
}