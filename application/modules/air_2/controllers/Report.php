<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller {
	public $data = array();
	public $shiftArr = array();
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->shiftArr = array(1,2,3);
		$this->load->model('air_model');
		$this->load->model('cpp_model');

	}

	public function index(){
		$this->data = array();
		$this->data['all'] = null; 
		$startDate = '';
		$this->data['dateRange'] = $this->_getDateDetails();
		$this->data['dateRange']['selectedDate'] =  $this->data['dateRange']['min_date'];
		$this->data['dateRange']['selectedDateEnd'] =  $this->data['dateRange']['min_date'];
		if(isset($_POST['startDate'])){
			$this->startDateTime = date('Y-m-d',strtotime($_POST['startDate']));
			$shift = $this->air_model->getShiftStart(1);
			$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
			$this->startDateTime = date('Y-m-d',strtotime($_POST['endDate']));
			$this->dataTimeInterval = 'PT24H00M00S';
			$endDate = $this->_dateAdd();

			//var_dump($startDate);
			//var_dump($endDate);

            ///////////// Get Data From Datalog CPP ****************Start
            $this->data['last15DataSet_cpp'] = $this->cpp_model->getLastDateDataTypeWise($startDate ,$endDate);
            $this->data['result_cpp'] = $this->geCalculationResult_cpp();
            //$this->data['last15DataSet_cpp'] = $this->cpp_model->getLastDateDataTypeWise('2017-11-08 13:00:00' ,'2017-11-09 13:00:00');

			$this->data['last15DataSet'] = $this->air_model->getLastDateDataTypeWise($startDate ,$endDate);
			$this->data['typeWise'] = $this->_getTotalTypeWise();
			$this->data['dateRange']['selectedDate'] = date('Y-m-d', strtotime($_POST['startDate']));
			$this->data['dateRange']['selectedDateEnd'] = date('Y-m-d', strtotime($_POST['endDate']));

			
	        
	        //echo "<pre>";
	        
	        
	        //var_dump($this->data['typeWise']);
	        //die();
	        ///////////// Get Data From Datalog CPP *****************End

            $this->data['TTL_flow_new'] = $this->getTTLflowNewCalculation($startDate ,$endDate,'PT24H00M00S');

	        //echo '<pre>';
	        //var_dump($this->data['last15DataSet']);

	        $this->data['notConnetedMeter'] = array(6 => 'Compressor 2', 7 => 'Comp 7 (ZH7000)', 8 => 'Comp 9 (ZH15000+)', 9 => 'Comp 6 (ZR500VSD)', 10 => 'Comp 10 (ZH1600+)', 11 => 'Comp 11 (Cameron Turbine)', 12 => 'WATER PDB 5', 13 => 'COMPRESSOR WATER PDB 1', 14 => 'COMPRESSOR WATER PDB 2', 15 => 'WATER PDB 4');
			$this->data['auxArr'] = array(12,13,14);

	        /// Start Get KW Value

	        $this->data['last15DataSet_KW'] = $this->_getCompRelKW($startDate ,$endDate);
	       
            //echo "<pre>";
            //var_dump($this->data['last15DataSet_KW']);

	        $this->data['compRelKW_total'] = $this->_getCompRelKW_Total();

            //echo "<pre>";
            //var_dump($this->data['last15DataSet_KW']);

            //var_dump($this->data['compRelKW_total']);

	        // END KW Value
			
			$this->data['all'] = $this->load->view('report/all_day',$this->data, true);
		}

		$this->load->view('report/index', $this->data);
	}

    private function getTTLflowNewCalculation($startDate,$endDate,$interval){

        
        //$newStartDate = date('Y-m-d H:i:s',strtotime($this->_dateSubcustom($startDate,$interval)));
        //$newEndDate = date('Y-m-d H:i:s',strtotime($this->_dateSubcustom($endDate,$interval)));

        $begin = $startDate;//new DateTime($newStartDate);
        $end   = $endDate;//new DateTime($newEndDate);


       /* echo "<pre>";
        var_dump($startDate);
        var_dump($endDate);

        var_dump($newStartDate);
        var_dump($newEndDate);
        die();
*/

        $arr = array();
        if(isset($begin) && $begin!='' && isset($end) && $end!=''){
            for($i = $begin; $i <= $end; $i=$this->_dateAddcustom($i,$interval)){

                //echo $i;
                if($i!=''){
                    $arr[$i] = $this->air_model->getTTL_flow_newDateDataTypeWise($i);
                }
                
            }
        }
        /*echo "<pre>";
        var_dump($arr);
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
                            $lDate = $this->_dateSubcustom($cDate,$interval);
                            
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

    private function getTTLflowNewCalculationAllDayForOnclick($startDate,$endDate,$interval){

        //$newStartDate = date('Y-m-d H:i:s',strtotime($this->_dateSubcustom($startDate,$interval)));
        //$newEndDate = date('Y-m-d H:i:s',strtotime($this->_dateSubcustom($endDate,$interval)));

        $begin = $startDate;//new DateTime($newStartDate);
        $end   = $endDate;//new DateTime($newEndDate);


       /* echo "<pre>";
        var_dump($startDate);
        var_dump($endDate);

        var_dump($newStartDate);
        var_dump($newEndDate);
        die();
        */
        $returnDataSet = array('splits'=>array(),'result'=>array());

        $arr = array();
        if(isset($begin) && $begin!='' && isset($end) && $end!=''){
            for($i = $begin; $i <= $end; $i=$this->_dateAddcustom($i,$interval)){

                //echo $i;
                if($i!=''){
                    $arr[$i] = $this->air_model->getTTL_flow_newDateDataTypeWise($i);
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
                            $lDate = $this->_dateSubcustom($cDate,$interval);
                            
                            

                            if(isset($lDate) && $lDate!='' && isset($ddvalue) && $ddvalue>0 && $ddvalue!='' && isset($descArray[$lDate]) && $descArray[$lDate]!='' && $descArray[$lDate]>0){
                                

                                $resultArray[$tkey][$mmkey]['TTL_flow']+= ($ddvalue - $descArray[$lDate]);  
                            }
                        }
                    }
                    
                }
            }
        }

        $returnDataSet['result'] = $resultArray;
        /*echo "<pre>";
        var_dump($resultArray);
        die();*/

        return $returnDataSet;
        //var_dump($resultArray);

    }

    private function getTTLflowNewCalculationShiftForOnclick($startDate,$endDate,$interval){

        //$newStartDate = date('Y-m-d H:i:s',strtotime($this->_dateSubcustom($startDate,$interval)));
        //$newEndDate = date('Y-m-d H:i:s',strtotime($this->_dateSubcustom($endDate,$interval)));

        $begin = $startDate;//new DateTime($newStartDate);
        $end   = $endDate;//new DateTime($newEndDate);


       /* echo "<pre>";
        var_dump($startDate);
        var_dump($endDate);

        var_dump($newStartDate);
        var_dump($newEndDate);
        die();
        */
        $returnDataSet = array('splits'=>array(),'result'=>array());

        $arr = array();
        if(isset($begin) && $begin!='' && isset($end) && $end!=''){
            for($i = $begin; $i <= $end; $i=$this->_dateAddcustom($i,$interval)){

                //echo $i;
                if($i!=''){
                    $arr[$i] = $this->air_model->getTTL_flow_newDateDataTypeWise($i);
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
                            $lDate = $this->_dateSubcustom($cDate,$interval);
                            
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
        /*echo "<pre>";
        var_dump($resultArray);
        die();*/

        return $returnDataSet;
        //var_dump($resultArray);

    }

	private function _getCompRelKW_CFMimestamp($meter_id=NULL,$startDate,$endDate,$dayCount){

        $mappingData = $this->mappingAIRtoEMS();
        $deviceIds = isset($mappingData['deviceIds']) ? $mappingData['deviceIds'] : '';

        if(isset($meter_id) && $meter_id!=NULL){
            $deviceIds = isset($mappingData['data'][$meter_id]) ? $mappingData['data'][$meter_id] : $deviceIds;
        }
        //$startDate = $this->startDateTime;
        //$startDate = '';
        if($dayCount==1){
        	$sqlData = $this->air_model->getLastDateDataKW($deviceIds,$startDate,$endDate);
        }else{

        	$selected = 'DATE_FORMAT(EMD.end_date_time, "%Y-%m-%d") end_date_time, SUM(data_KW) data_KW, device_id';

        	$sqlData = $this->air_model->getEMSKWDayWiseParam($deviceIds,$startDate,$endDate,$selected);
        }
        
        $tempSql = array();
        /*echo "<pre>";
        var_dump($sqlData);*/
        if(count($sqlData) > 0){
            foreach ($sqlData as $key => $value) {
                $tempSql[$value->end_date_time] = $value->data_KW;//array('device_id'=>$value->device_id,'kw'=>$value->data_KW);
            }
        }

        return $tempSql;

    }


    public function getMeterChart_forKWCFM_shift(){

        $meter_id = $this->uri->segment(4);

    	$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));

    	if($this->uri->segment(6)==2){
    		$this->dataTimeInterval = 'PT08H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
    	}else if($this->uri->segment(6)==3){
    		$this->dataTimeInterval = 'PT16H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
    	}else{
    		$shift = $this->air_model->getShiftStart(1);
			$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
    	}

		$this->dataTimeInterval = 'PT08H00M00S';
		$endDate = $this->_dateAdd();

    	/*echo "<pre>";
    	var_dump($startDate);
    	var_dump($endDate);
    	die();*/

    	$this->data['dayCount'] = 1;


        // Get KW value according 15 mint timestamp interval
        $dataFinalArray_KW = $this->_getCompRelKW_CFMimestamp($meter_id,$startDate,$endDate,$this->data['dayCount']);       

        
        // Get CFM value according 15 mint timestamp interval
        //$selected = 'AM.name,AMD.end_date_time';
        //$selected .= ',AMD.flow';

        //$dataFinalArray_CFM = $this->air_model->getAirMeterData($meter_id, $selected);


        $retSelectArr = $this->_createSelectQuery('flow', $this->data['dayCount']);
    	$selected = $retSelectArr['selected'];
    	
    	$dataFinalArray_CFM = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	

        /*echo "<pre>";
        var_dump($selected);
        die();*/


        // Generate The KW/CFM Value
        $meterName = "NA";
        $dataFinalArray = array();
        if(isset($dataFinalArray_CFM) && count($dataFinalArray_CFM)>0){
            foreach ($dataFinalArray_CFM as $inkey => $invalue) {
                $meterName = isset($invalue->name) ? $invalue->name : 'NA';

                if(isset($dataFinalArray_KW[$invalue->end_date_time]) && isset($invalue->end_date_time))
                $dataFinalArray[$invalue->end_date_time] = (isset($invalue->flow) && $invalue->flow>0) ? ($dataFinalArray_KW[$invalue->end_date_time] / $invalue->flow) : 0;

            }
        }
        /*echo "<pre>";
        var_dump($meter_id);
        var_dump($dataFinalArray);
        die();*/

        $dataFinalArray = array_reverse($dataFinalArray);
        

        $dataSet = '';
        $consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = $meterName;
        $this->data['meterNameColumn'] = 'KW/CFM';
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
        //var_dump($dataSet);
        //die();
        /////////////////////////////////////////////////
        $this->data['consisIndex'] = 'no';
        $dataSetConsis = '';
        $maxMinConsis = array();
        $this->data['max_val_con'] = 0;
        $this->data['min_val_con'] = 0;
        $this->data['graph_logic'] = '1';
        /////////////////////////////////////////////////
        $this->data['max_val'] = (intval(max($maxMin))<=0) ? intval(max($maxMin)) : (intval(max($maxMin)) + 1);
        $this->data['min_val'] = (intval(min($maxMin))<=0) ? intval(min($maxMin)) : (intval(min($maxMin)) - ((min($maxMin) == 0)?0:1));
        //echo intval(min($maxMin));    
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSetConsis'] = $dataSetConsis;
        
        $this->load->view('report/chart',$this->data);

    }

	public function getMeterChart_forKWCFM(){

        $meter_id = $this->uri->segment(4);

        $shift = $this->air_model->getShiftStart(1);

        $this->startDateTime = date('Y-m-d', $this->uri->segment(5));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();


    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');


        // Get KW value according 15 mint timestamp interval
        $dataFinalArray_KW = $this->_getCompRelKW_CFMimestamp($meter_id,$startDate,$endDate,$this->data['dayCount']);       

        
        // Get CFM value according 15 mint timestamp interval
        //$selected = 'AM.name,AMD.end_date_time';
        //$selected .= ',AMD.flow';

        //$dataFinalArray_CFM = $this->air_model->getAirMeterData($meter_id, $selected);


        $retSelectArr = $this->_createSelectQuery('flow', $this->data['dayCount']);
    	$selected = $retSelectArr['selected'];
    	if($this->data['dayCount'] == 1){
    		$dataFinalArray_CFM = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	}else{

    		$selected = 'AM.name,DATE_FORMAT(AMD.end_date_time, "%Y-%m-%d") end_date_time,SUM(NULLIF(AMD.flow,0)) flow';
    		$dataFinalArray_CFM = $this->air_model->getAirMeterDataDayWiseParam($meter_id, $selected, $startDate, $endDate);
    	}

        /*echo "<pre>";
        var_dump($selected);
        die();*/


        // Generate The KW/CFM Value
        $meterName = "NA";
        $dataFinalArray = array();
        if(isset($dataFinalArray_CFM) && count($dataFinalArray_CFM)>0){
            foreach ($dataFinalArray_CFM as $inkey => $invalue) {
                $meterName = isset($invalue->name) ? $invalue->name : 'NA';

                if(isset($dataFinalArray_KW[$invalue->end_date_time]) && isset($invalue->end_date_time))
                $dataFinalArray[$invalue->end_date_time] = (isset($invalue->flow) && $invalue->flow>0) ? ($dataFinalArray_KW[$invalue->end_date_time] / $invalue->flow) : 0;

            }
        }
        /*echo "<pre>";
        var_dump($meter_id);
        var_dump($dataFinalArray);
        die();*/

        $dataFinalArray = array_reverse($dataFinalArray);
        

        $dataSet = '';
        $consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = $meterName;
        $this->data['meterNameColumn'] = 'KW/CFM';
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
        //var_dump($dataSet);
        //die();
        /////////////////////////////////////////////////
        $this->data['consisIndex'] = 'no';
        $dataSetConsis = '';
        $maxMinConsis = array();
        $this->data['max_val_con'] = 0;
        $this->data['min_val_con'] = 0;
        $this->data['graph_logic'] = '1';
        /////////////////////////////////////////////////
        $this->data['max_val'] = (intval(max($maxMin))<=0) ? intval(max($maxMin)) : (intval(max($maxMin)) + 1);
        $this->data['min_val'] = (intval(min($maxMin))<=0) ? intval(min($maxMin)) : (intval(min($maxMin)) - ((min($maxMin) == 0)?0:1));
        //echo intval(min($maxMin));    
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSetConsis'] = $dataSetConsis;
        
        $this->load->view('report/chart',$this->data);

    }

	
	private function _getCompRelKW_totaltimestamp($startDate,$endDate,$dayCount){
        
        $mappingData = $this->mappingAIRtoEMS();
        $deviceIds = isset($mappingData['deviceIds']) ? $mappingData['deviceIds'] : '';
        //$startDate = $this->startDateTime;
        //$startDate = '';
        if($dayCount==1){
        	$sqlData = $this->air_model->getLastDateDataKW($deviceIds,$startDate,$endDate);
        }else{

        	$selected = 'DATE_FORMAT(EMD.end_date_time, "%Y-%m-%d") end_date_time, SUM(data_KW) data_KW, device_id';

        	$sqlData = $this->air_model->getEMSKWDayWiseParam($deviceIds,$startDate,$endDate,$selected);
        }
        
        $tempSql = array();
        //echo "<pre>";
        //var_dump($sqlData);

        if(count($sqlData) > 0){
            foreach ($sqlData as $key => $value) {
                $tempSql[$value->end_date_time][$value->device_id] = $value->data_KW;//array('device_id'=>$value->device_id,'kw'=>$value->data_KW);
            }
        }

        
        $retData = array();
        if(isset($tempSql) && count($tempSql)>0){
            foreach ($tempSql as $timekey => $timevalue) {
                $kwSum = 0;
                foreach ($timevalue as $devicekey => $deviceKWvalue) {
                    $kwSum+= $deviceKWvalue;
                }
                $retData[$timekey] = $kwSum;
            }
        }
       
        return $retData;
    }

    public function getMeterChartKWTotal_shift(){

        $this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));

    	if($this->uri->segment(5)==2){
    		$this->dataTimeInterval = 'PT08H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
    	}else if($this->uri->segment(5)==3){
    		$this->dataTimeInterval = 'PT16H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
    	}else{
    		$shift = $this->air_model->getShiftStart(1);
			$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
			$startDate = $this->startDateTime = $this->_dateAdd();
    	}

		$this->dataTimeInterval = 'PT08H00M00S';
		$endDate = $this->_dateAdd();

    	/*echo "<pre>";
    	var_dump($startDate);
    	var_dump($endDate);
    	die();*/
        

        $this->data['dayCount'] = 1;
        

        $dataFinalArray = $this->_getCompRelKW_totaltimestamp($startDate,$endDate,$this->data['dayCount']);

        /*echo $startDate;
        echo $endDate;
        echo "<pre>";
        var_dump($dataFinalArray);

        die();*/

        $dataFinalArray = array_reverse($dataFinalArray);
        

        $dataSet = '';
        $consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = 'Total of Generation of KW';
        $this->data['meterNameColumn'] = 'KW';
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
        //var_dump($dataSet);
        //die();
        /////////////////////////////////////////////////
        $this->data['consisIndex'] = 'no';
        $dataSetConsis = '';
        $maxMinConsis = array();
        $this->data['max_val_con'] = 0;
        $this->data['min_val_con'] = 0;
        $this->data['graph_logic'] = '1';
        /////////////////////////////////////////////////
        $this->data['max_val'] = intval(max($maxMin)) + 1;
        $this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
        //echo intval(min($maxMin));    
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSetConsis'] = $dataSetConsis;
        
        $this->load->view('report/chart',$this->data);

    }

	public function getMeterChartKWTotal(){

		$shift = $this->air_model->getShiftStart(1);

		$this->startDateTime = date('Y-m-d', $this->uri->segment(4));
        $this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
        $startDate = $this->startDateTime = $this->_dateAdd();
        $this->startDateTime = date('Y-m-d', $this->uri->segment(5));
        $this->dataTimeInterval = 'PT24H00M00S';
        $endDate = $this->_dateAdd();

        

        $this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
        

        $dataFinalArray = $this->_getCompRelKW_totaltimestamp($startDate,$endDate,$this->data['dayCount']);

        /*echo $startDate;
        echo $endDate;
        echo "<pre>";
        var_dump($dataFinalArray);

        die();*/

        $dataFinalArray = array_reverse($dataFinalArray);
        

        $dataSet = '';
        $consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = 'Total of Generation of KW';
        $this->data['meterNameColumn'] = 'KW';
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
        //var_dump($dataSet);
        //die();
        /////////////////////////////////////////////////
        $this->data['consisIndex'] = 'no';
        $dataSetConsis = '';
        $maxMinConsis = array();
        $this->data['max_val_con'] = 0;
        $this->data['min_val_con'] = 0;
        $this->data['graph_logic'] = '1';
        /////////////////////////////////////////////////
        $this->data['max_val'] = intval(max($maxMin)) + 1;
        $this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
        //echo intval(min($maxMin));    
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSetConsis'] = $dataSetConsis;
        
        $this->load->view('report/chart',$this->data);

    }




	private function _getCompRelKW_Total(){

	 	//echo "<pre>";
	 	//var_dump($this->data['last15DataSet_KW']);

        $totalVals = 0;
        if(isset($this->data['last15DataSet_KW']) && count($this->data['last15DataSet_KW'])>0){

            foreach ($this->data['last15DataSet_KW'] as $airKey => $airval) {
                $totalVals+= isset($airval[0]) ? $airval[0] : 0;
            }
        }

        return $totalVals;
    }


	private function mappingAIRtoEMS(){
        $returnDataset['deviceIds'] = '48,46,47,44,43,45,51,52,53,56,57,49,50,55';
        $returnDataset['data'] = array(1 => 48, 2 => 47, 3=>45, 4=>44,5=>43,6=>46, 7=>51, 8=>52, 9=>53,10=> 56,11=>0,12=>57,13=>49,14=>50,15 => 55);


        return $returnDataset;
    }

    private function _getCompRelKW($startDate,$endDate){
		//$deviceIds = '48,46,47,44,43,45,51,52,53,56,57,49,50,55';
		//$data = array(1 => 48, 2 => 47, 3=>46, 4=>44,5=>43,6=>45, 7=>51, 8=>52, 9=>53,10=> 56,11=>0,12=>57,13=>49,14=>50,15 => 55);
		
        $mappingData = $this->mappingAIRtoEMS();
        $deviceIds = isset($mappingData['deviceIds']) ? $mappingData['deviceIds'] : '';
        $data = isset($mappingData['data']) ? $mappingData['data'] : '';

        //$startDate = $this->startDateTime;
		$sqlData = $this->air_model->getLastDateDataKW($deviceIds,$startDate,$endDate);

		//echo "<pre>";
		//var_dump($sqlData);

		$tempSql = array();
		if(count($sqlData) > 0){
			$totKW = 0;
			foreach ($sqlData as $key => $value) {
				//$totKW+=$value->data_KW;
				if($value->data_KW>0){
					$tempSql[$value->device_id][$value->end_date_time] = $value->data_KW;
				}
				
			}
		}
		//echo "<pre>";
		//var_dump($tempSql);
		

		$retData = array();
		foreach ($data as $key => $value) {
			if(isset($tempSql[$value])){
				$retData[$key] = array(round((array_sum($tempSql[$value])/count($tempSql[$value])),2),$value);
			}else{
				$retData[$key] = array('DNA',$value);
			}
		}

		//echo "<pre>";
		//var_dump($retData);

		return $retData;
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
				$this->dataTimeInterval = 'PT08H00M00S';
				$startDate = $this->startDateTime = $this->_dateAdd();
			}elseif($shiftHours == 24){
				$this->dataTimeInterval = 'PT16H00M00S';
				$startDate = $this->startDateTime = $this->_dateAdd();
			}
			

			$this->dataTimeInterval = 'PT08H00M00S';
			$endDate = $this->_dateAdd();

			//echo $startDate;
			//echo $endDate;
			//die();

            ///////////// Get Data From Datalog CPP ****************Start
            $this->data['last15DataSet_cpp'] = $this->cpp_model->getLastDateDataTypeWise($startDate ,$endDate);
            //$this->data['last15DataSet_cpp'] = $this->cpp_model->getLastDateDataTypeWise('2017-11-08 13:00:00' ,'2017-11-08 20:00:00');
            
            //echo "<pre>";
            $this->data['result_cpp'] = $this->geCalculationResult_cpp();
            
            //var_dump($this->data['result_cpp']);
            //die();
            ///////////// Get Data From Datalog CPP *****************End
            
			$this->data['last15DataSet'] = $this->air_model->getLastDateDataTypeWise($startDate ,$endDate);
			$this->data['typeWise'] = $this->_getTotalTypeWise();
			$this->data['dateRange']['selectedDate'] = $_POST['startDate'];
			$this->data['shiftVal'] = strtotime($startDate);


			
            $this->data['TTL_flow_new'] = $this->getTTLflowNewCalculation($startDate ,$endDate,'PT08H00M00S');
            
	        $this->data['notConnetedMeter'] = array(6 => 'Compressor 2', 7 => 'Comp 7 (ZH7000)', 8 => 'Comp 9 (ZH15000+)', 9 => 'Comp 6 (ZR500VSD)', 10 => 'Comp 10 (ZH1600+)', 11 => 'Comp 11 (Cameron Turbine)', 12 => 'WATER PDB 5', 13 => 'COMPRESSOR WATER PDB 1', 14 => 'COMPRESSOR WATER PDB 2', 15 => 'WATER PDB 4');
			$this->data['auxArr'] = array(12,13,14);

	        /// Start Get KW Value
			$this->data['shift_count'] = 1;
			if($_POST['sifthour']==16){
				$this->data['shift_count'] = 2;
			}
			if($_POST['sifthour']==24){
				$this->data['shift_count'] = 3;
			}


	        $this->data['last15DataSet_KW'] = $this->_getCompRelKW($startDate ,$endDate);
	        
	        $this->data['compRelKW_total'] = $this->_getCompRelKW_Total();

	        // END KW Value

	        
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
				
				if($value->meter_id==2) continue;
				if($value->meter_id==3) continue;
				if($value->meter_id==4) continue;

				$data[$value->type]['meter'][$value->meter_id]['name'] = $value->name;

				$data[$value->type]['meter'][$value->meter_id]['pressure'][] = $value->pressure;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['pressure_count'])) $data[$value->type]['meter'][$value->meter_id]['pressure_count'] = 1;

				if($value->pressure>0){
					$data[$value->type]['meter'][$value->meter_id]['pressure_count'] = $data[$value->type]['meter'][$value->meter_id]['pressure_count'] + 1;
				}

				$data[$value->type]['meter'][$value->meter_id]['flow'][] = $value->flow * 10;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['flow_count'])) $data[$value->type]['meter'][$value->meter_id]['flow_count'] = 1;
				if($value->flow>0){
					$data[$value->type]['meter'][$value->meter_id]['flow_count'] = $data[$value->type]['meter'][$value->meter_id]['flow_count'] + 1;
				}

				$data[$value->type]['meter'][$value->meter_id]['temp'][] = $value->temp;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['temp_count'])) $data[$value->type]['meter'][$value->meter_id]['temp_count'] = 1;
				if($value->temp>0){
					$data[$value->type]['meter'][$value->meter_id]['temp_count'] = $data[$value->type]['meter'][$value->meter_id]['temp_count'] + 1;
				}
				

			}
			
			if(count($data) > 0){
				foreach ($data as $key1 => $value1) {
					foreach($value1['meter'] as $key2 => $val2){
						

						$retData[$key1]['meter'][$key2]['name'] = $val2['name'];
						
						$retData[$key1]['meter'][$key2]['flow'] = array_sum($val2['flow'])/$val2['flow_count'];

						$retData[$key1]['meter'][$key2]['pressure'] = array_sum($val2['pressure'])/$val2['pressure_count'];
						
						$retData[$key1]['meter'][$key2]['temp'] = array_sum($val2['temp'])/$val2['temp_count'];

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
		$retData = array();
        $retData_cpp = array();
		if(count($this->data['last15DataSet'])){
			$data = array();
			$typeWiseArr = array();
			foreach ($this->data['last15DataSet'] as $key => $value) {
				
				$data[$value->type]['meter'][$value->meter_id]['name'] = $value->name;
                $data[$value->type]['meter'][$value->meter_id]['capacity'] = isset($value->capacity) ? $value->capacity : 0;
				
				$data[$value->type]['meter'][$value->meter_id]['P_pressure'][] = $value->P_pressure;
				
				if(!isset($data[$value->type]['meter'][$value->meter_id]['P_pressure_count'])) $data[$value->type]['meter'][$value->meter_id]['P_pressure_count'] = 1;

				if($value->P_pressure>0){
					$data[$value->type]['meter'][$value->meter_id]['P_pressure_count'] = $data[$value->type]['meter'][$value->meter_id]['P_pressure_count'] + 1;
				}
					
				// CFM CU %
                if($value->type=="gen"){
                   if(!isset($data[$value->type]['meter'][$value->meter_id]['cfm_cu'])) $data[$value->type]['meter'][$value->meter_id]['cfm_cu'] = array();
                    if(isset($data[$value->type]['meter'][$value->meter_id]['capacity']) && $data[$value->type]['meter'][$value->meter_id]['capacity']>0){
                        $data[$value->type]['meter'][$value->meter_id]['cfm_cu'][] = (($value->flow / $data[$value->type]['meter'][$value->meter_id]['capacity']) * 100);
                    } 
                }
                

				$data[$value->type]['meter'][$value->meter_id]['flow'][] = $value->flow;

				if(!isset($data[$value->type]['meter'][$value->meter_id]['flow_count'])) $data[$value->type]['meter'][$value->meter_id]['flow_count'] = 0;
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
			//echo "<pre>";
			//var_dump($data);

			if(count($data) > 0){
				foreach ($data as $key1 => $value1) {
					foreach($value1['meter'] as $key2 => $val2){
						$retData[$key1]['meter'][$key2]['name'] = $val2['name'];
                        $retData[$key1]['meter'][$key2]['capacity'] = isset($val2['capacity']) ? $val2['capacity'] : 0;

						$retData[$key1]['meter'][$key2]['benchmark_delta_pressure'] = $val2['benchmark_delta_pressure'];
						$retData[$key1]['meter'][$key2]['benchmark_delta_temp'] = $val2['benchmark_delta_temp'];

						//$retData[$key1]['meter'][$key2]['flow'] = array_sum($val2['flow'])/count($val2['flow']);
                        if(!isset($retData[$key1]['meter'][$key2]['flow'])) $retData[$key1]['meter'][$key2]['flow'] = 0;
                        if($val2['flow_count']>0){
                            $retData[$key1]['meter'][$key2]['flow'] = array_sum($val2['flow'])/$val2['flow_count'];
                        }
						
                        // CFM CU %
                        if(isset($key1) && $key1=="gen"){
                            if(!isset($retData[$key1]['meter'][$key2]['cfm_cu'])) $retData[$key1]['meter'][$key2]['cfm_cu'] = 0;
                            
                            if(isset($val2['cfm_cu']) && count($val2['cfm_cu'])>0){
                                $retData[$key1]['meter'][$key2]['cfm_cu'] = array_sum($val2['cfm_cu']) / count($val2['cfm_cu']);
                            }
                            
                        }

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
				

			}
            
            /*echo "<pre>";
            var_dump($this->data['result_cpp']);*/

            // For CPP
            if(isset($this->data['result_cpp']) && count($this->data['result_cpp'])>0){
                
                foreach($this->data['result_cpp'] as $cppkk=>$cppvv){
                    foreach($cppvv['meter'] as $cppkk1=>$cppvv1){

                        if(!isset($tempArr[$cppkk]['total']['flow'])) $tempArr[$cppkk]['total']['flow'] = 0;
                        if(!isset($tempArr[$cppkk]['total']['P_pressure'])) $tempArr[$cppkk]['total']['P_pressure'] = 0;
                        
                        $tempArr[$cppkk]['total']['flow']+=isset($cppvv1['flow']) ? $cppvv1['flow'] : 0;

                        $tempArr[$cppkk]['total']['P_pressure']+= ($cppvv1['pressure'] * $cppvv1['flow']);

                    }
                }

            }

            /*echo "<pre>";
            var_dump($tempArr);*/
            // For Cpp
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


    public function getMeterChartTotalGNvsDIST(){
    	
    	$type = $this->uri->segment(4);
    	//$meterType = $this->uri->segment(5);

    	$curVal = $this->uri->segment(5);
        $deltaGenVal = $this->uri->segment(6);
        $staticDeltaVal = $this->uri->segment(7);
        $shiftDateStrToTime = trim($this->uri->segment(8));

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

        $this->data['dayCount'] = 1;

    	$meterdata = $this->air_model->getMeter();
    	//echo "<pre>";


    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			if(isset($mvalue->type) && $mvalue->type=='main') continue;

    			//$data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterData($mvalue->meter_id);
    			$data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id,'*',$shiftStartDate, $shiftEndDate);

    		}
    	}
    	//var_dump($data);
    	//die();

    	// Iterate Array according to End Date Time
    	$dataArray = array();
    	if(isset($data) && count($data)>0){
    		foreach ($data as $dkeyType => $dvalue) {
    			$dataArray[$dkeyType] = $this->_setData($dvalue); 
    		}
    	}    	 	
    	
    	// Calculation Of Value
    	$dataFinalArray = array();
    	if(isset($dataArray) && count($dataArray)>0){
    		foreach ($dataArray as $mTypekey => $mTypevalue) {
    			$dataFinalArray[$mTypekey] = $this->_calculationTimestamp($mTypevalue); 
    		}
    	}    	

    	// Re-Structure The Array
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
    	
    	   	

    	$resultArray = array_reverse($resultArray);
    	//var_dump($resultArray);
    	//die();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = 'Generation Vs Distribution';
    	$this->data['meterNameColumn'] = $column;
    	if(count($resultArray) > 0){
    		//$this->data['meterName'] = $data[0]->name;
    		foreach ($resultArray as $dateTimeKey => $dateTimeValue) {
    			
		        $dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue,3).'"
			        },';
			        $maxMin[] = $dateTimeValue[$aa];
		        
    		}
    	}
    	//var_dump($dataSet);
    	//die();
    	/////////////////////////////////////////////////
    	$this->data['consisIndex'] = 'no';
    	$dataSetConsis = '';
    	$maxMinConsis = array();
    	$this->data['max_val_con'] = 0;
    	$this->data['min_val_con'] = 0;
    	$this->data['graph_logic'] = '1';
    	/////////////////////////////////////////////////
    	$this->data['max_val'] = intval(max($maxMin)) + 1;
    	$this->data['min_val'] = intval(min($maxMin)) - ((min($maxMin) == 0)?0:1);
    	//echo intval(min($maxMin));	
    	$this->data['dataSet'] = $dataSet;
    	$this->data['dataSetConsis'] = $dataSetConsis;
    	
    	$this->load->view('dashboard/chart',$this->data);
    }

    public function getMeterChartTotalGNvsDISTforDoubleGraph(){
        
        $type = $this->uri->segment(4);
        //$meterType = $this->uri->segment(5);

        $curVal = $this->uri->segment(5);
        $deltaGenVal = $this->uri->segment(6);
        $staticDeltaVal = $this->uri->segment(7);
        $shiftDateStrToTime = trim($this->uri->segment(8));

        if($shiftDateStrToTime == '' && $curVal > 0){
            $shiftDateStrToTime = $curVal;
        }
        $this->startDateTime = $shiftStartDate = date('Y-m-d H:i:s', $shiftDateStrToTime); 
        $this->dataTimeInterval = 'PT08H00M00S';
        $shiftEndDate = $this->_dateAdd();

        $selected = 'AM.name,AMD.end_date_time';
        $selected2 = 'AM.name,AMD.end_date_time';

        $aa = 'P_pressure';
        $aa2 = 'flow';

        $column = 'Pressure Vs Flow';
        $seriesname = 'Pressure(Bar)';
        $seriesname2 = 'Flow(CFM)';

        if($type == 'pres'){
            $selected .= ',AMD.P_pressure';
            $selected2 .= ',AMD.flow';
            $aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }else if($type == 'flow'){
            $selected .= ',AMD.flow';
            $selected2 .= ',AMD.P_pressure';
            $aa = 'flow';
            $aa2 = 'P_pressure';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Flow(CFM)';
            $seriesname2 = 'Pressure(Bar)';
        }else{
            $selected .= ',AMD.P_pressure';
            $selected2 .= ',AMD.flow';
            $aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }

        $this->data['dayCount'] = 1;

        $meterdata = $this->air_model->getMeter();
        //echo "<pre>";


        $data = array();
        if(isset($meterdata) && count($meterdata)>0){
            foreach ($meterdata as $mkey => $mvalue) {
                if(isset($mvalue->type) && $mvalue->type=='main') continue;

                //$data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterData($mvalue->meter_id);
                $data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id,'*',$shiftStartDate, $shiftEndDate);

            }
        }
        //var_dump($data);
        //die();

        // Iterate Array according to End Date Time
        $dataArray = array();
        if(isset($data) && count($data)>0){
            foreach ($data as $dkeyType => $dvalue) {
                $dataArray[$dkeyType] = $this->_setData($dvalue); 
            }
        }           
        
        // Calculation Of Value
        $dataFinalArray = array();
        if(isset($dataArray) && count($dataArray)>0){
            foreach ($dataArray as $mTypekey => $mTypevalue) {
                $dataFinalArray[$mTypekey] = $this->_calculationTimestamp($mTypevalue); 
            }
        }       

        // Re-Structure The Array 
        $newDataArray = array();
        $newDataArray2 = array();
        if(isset($dataFinalArray) && count($dataFinalArray)>0){
            foreach ($dataFinalArray as $typeKey => $typeValue) {
                foreach ($typeValue as $dateTimekeys => $dateTimeValues) {
                    $newDataArray[$dateTimekeys][$typeKey] = $dateTimeValues[$aa];
                    $newDataArray2[$dateTimekeys][$typeKey] = $dateTimeValues[$aa2];
                }
            }
        }

        // Calculation For Generation Vs Distribution For Forst Set
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

        // Calculation For Generation Vs Distribution For Second Set
        $resultArray2 = array();
        if(isset($newDataArray2) && count($newDataArray2)>0){
            foreach ($newDataArray2 as $dateTimeskey2 => $dateTimesvalue2) {
                if(isset($dateTimesvalue2['gen']) && isset($dateTimesvalue2['dist'])){
                    $resultArray2[$dateTimeskey2] = ($dateTimesvalue2['gen'] - $dateTimesvalue2['dist']);
                }else{
                    if(isset($dateTimesvalue2['gen'])){
                        $resultArray2[$dateTimeskey2] = $dateTimesvalue2['gen'];
                    }elseif(isset($dateTimesvalue2['dist'])){
                        $resultArray2[$dateTimeskey2] = $dateTimesvalue2['gen'];
                    }else{
                        $resultArray2[$dateTimeskey2] = '0';
                    }
                }
            }
        }
        
            

        $resultArray = array_reverse($resultArray);
        $resultArray2 = array_reverse($resultArray2);
        //var_dump($resultArray);
        //die();

        $this->data['meterName'] = 'Generation Vs Distribution';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;


        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';
        
        if(count($resultArray) > 0){
            //$this->data['meterName'] = $data[0]->name;
            foreach ($resultArray as $dateTimeKey => $dateTimeValue) {
                
                if(isset($dateTimeKey) && $dateTimeKey!=''){
                    $dataSetDate .= '{
                        "label": "'.$dateTimeKey.'"
                    },';

                    $dataSet .= '{
                        "value": "'.number_format((float)round($dateTimeValue,3), 2, '.', '').'"
                    },';

                    if(isset($resultArray2[$dateTimeKey])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($resultArray2[$dateTimeKey],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }

                }     
                
            }
        }
        $this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;
        
        $this->load->view('dashboard/chart_loading',$this->data);
    }

    public function getMeterChartShiftTotal(){

    	$type = $this->uri->segment(4);
    	$meterType = $this->uri->segment(5);

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

    	$this->data['dayCount'] = 1;

    	// Get The Meter Details by Type(Generation or distribution)
    	$meterdata = $this->air_model->getMeterTypeWise($meterType);

    	// Fetch the data meter wise
    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
    			
    			$data[$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id,'*',$shiftStartDate, $shiftEndDate);

    		}
    	}


    	//$data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);

    	// Iterate Array according to End Date Time
    	$dataArray = $this->_setData($data); 

    	// Calculation Of Value
    	$dataFinalArray = $this->_calculationTimestamp($dataArray);  


    	
    	$dataFinalArray = (count($dataFinalArray)>0) ? array_reverse($dataFinalArray) : array();
    	$dataSet = '';
    	$consistencyArr = array();
    	$maxMin = array();
    	$this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Distribution';
    	$this->data['meterNameColumn'] = $column;
    	//echo "<pre>";
    	//print_r($curVal);

    	if(count($dataFinalArray) > 0){
    		//$this->data['meterName'] = $data[0]->name;
    		foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
    			
		        if($curVal != '' && $deltaGenVal != 0){
		        	$dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($deltaGenVal - $dateTimeValue[$aa],3).'"
			        },';
			        $maxMin[] = $deltaGenVal - $dateTimeValue[$aa];
		        	$consistencyArr[$dateTimeKey] = $deltaGenVal - $dateTimeValue[$aa];
		        }else{
		        	$dataSet .= '{
			            "label": "'.$dateTimeKey.'",
			            "value": "'.round($dateTimeValue[$aa],3).'"
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
    	
    	$this->load->view('report/chart',$this->data);
    }

    public function getMeterChartShiftTotalforDoubleGraph(){

        $type = $this->uri->segment(4);
        $meterType = $this->uri->segment(5);

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
        $selected2 = 'AM.name,AMD.end_date_time';

        $aa = 'P_pressure';
        $aa2 = 'flow';

        $column = 'Pressure Vs Flow';
        $seriesname = 'Pressure(Bar)';
        $seriesname2 = 'Flow(CFM)';

        if($type == 'pres'){
            $selected .= ',AMD.P_pressure';
            $selected2 .= ',AMD.flow';
            $aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }else if($type == 'flow'){
            $selected .= ',AMD.flow';
            $selected2 .= ',AMD.P_pressure';
            $aa = 'flow';
            $aa2 = 'P_pressure';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Flow(CFM)';
            $seriesname2 = 'Pressure(Bar)';
        }else{
            $selected .= ',AMD.P_pressure';
            $selected2 .= ',AMD.flow';
            $aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }

        $this->data['dayCount'] = 1;

        // Get The Meter Details by Type(Generation or distribution)
        $meterdata = $this->air_model->getMeterTypeWise($meterType);

        // Fetch the data meter wise
        $data = array();
        if(isset($meterdata) && count($meterdata)>0){
            foreach ($meterdata as $mkey => $mvalue) {
                
                $data[$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id,'*',$shiftStartDate, $shiftEndDate);

            }
        }

        // Iterate Array according to End Date Time
        $dataArray = $this->_setData($data); 

        // Calculation Of Value
        $dataFinalArray = $this->_calculationTimestamp($dataArray);  


        
        $dataFinalArray = (count($dataFinalArray)>0) ? array_reverse($dataFinalArray) : array();

        $this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Distribution';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;
        
        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';

        if(count($dataFinalArray) > 0){
            //$this->data['meterName'] = $data[0]->name;
            foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
                
                if(isset($dateTimeKey) && $dateTimeKey!=''){
                    $dataSetDate .= '{
                        "label": "'.$dateTimeKey.'"
                    },';

                    if(isset($dateTimeValue[$aa])){
                        $dataSet .= '{
                            "value": "'.number_format((float)round($dateTimeValue[$aa],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet .= '{
                            "value": "0"
                        },';
                    }

                    if(isset($dateTimeValue[$aa2])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($dateTimeValue[$aa2],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }

                } 
                
            }
        }
        $this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;
        
        $this->load->view('report/chart_loading',$this->data);
    }

    public function getMeterChartShiftFORCFMCU_percent(){
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
            $selected .= ',AMD.flow,AM.capacity';
            $aa = 'flow';
            $column = 'Flow';
        }else{
            $selected .= ',AMD.steam_enthalpy';
            $aa = 'steam_enthalpy';
            $column = 'Enthalpy';
        }

        $this->data['dayCount'] = 1;
        $data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);
        
        $finalArray = array();
        if(isset($data) && count($data)>0){
            foreach ($data as $dkey => $dvalue) {
                $finalArray[$dkey]['name'] = isset($dvalue->name) ? $dvalue->name : 'NA';
                
                if(isset($dvalue->flow) && isset($dvalue->capacity) && $dvalue->capacity>0){
                    $finalArray[$dkey]['end_date_time'] = isset($dvalue->end_date_time) ? $dvalue->end_date_time : 'NA';
                    $finalArray[$dkey]['cuPercent'] = (($dvalue->flow / $dvalue->capacity) * 100);
                }
            }
        }
        
        $finalArray = (count($finalArray)>0) ? array_reverse($finalArray) : array();

        $dataSet = '';
        $consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = '';
        $this->data['meterNameColumn'] = 'CFM CU(%)';

        if(count($finalArray) > 0){
            $this->data['meterName'] = isset($finalArray[0]['name']) ? $finalArray[0]['name'] : 'NA';
            foreach ($finalArray as $key => $value) {
                
                if(isset($value['end_date_time']) && $value['end_date_time']!='' && isset($value['cuPercent'])){

                    $dataSet .= '{
                        "label": "'.$value['end_date_time'].'",
                        "value": "'.round($value['cuPercent'],3).'"
                    },';
                    
                }
                $maxMin[] = isset($value['cuPercent']) ? $value['cuPercent'] : 0;                
            }
        }
        $this->data['consisIndex'] = 'no';
        $dataSetConsis = '';
        $this->data['graph_logic'] = '1';
        
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

    	$this->data['dayCount'] = 1;
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

    public function getMeterChartShiftForDoubleGraph(){
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
        $selected2 = 'AM.name,AMD.end_date_time';

        $aa = 'P_pressure';
        $aa2 = 'flow';

        $column = 'Pressure Vs Flow';
        $seriesname = 'Pressure(Bar)';
        $seriesname2 = 'Flow(CFM)';

        if($type == 'pres'){
            $selected .= ',AMD.P_pressure';
            $selected2 .= ',AMD.flow';
            $aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }else if($type == 'flow'){
            $selected .= ',AMD.flow';
            $selected2 .= ',AMD.P_pressure';
            $aa = 'flow';
            $aa2 = 'P_pressure';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Flow(CFM)';
            $seriesname2 = 'Pressure(Bar)';
        }else{
            $selected .= ',AMD.P_pressure';
            $selected2 .= ',AMD.flow';
            $aa = 'P_pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }

        $this->data['dayCount'] = 1;
        $data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);
        $data2 = $this->air_model->getAirMeterDataDayShift($meter_id, $selected2, $shiftStartDate, $shiftEndDate);
        
        $data = (count($data)>0) ? array_reverse($data) : array();
        $data2 = (count($data2)>0) ? array_reverse($data2) : array();

        $this->data['meterName'] = isset($data[0]->name) ? $data[0]->name : 'NA';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;

        // Iterate First Data Set
        $newData = array();
        if (isset($data) && count($data)>0) {
            foreach ($data as $indKey => $indValue) {
                if(isset($indValue->end_date_time)){
                    $newData[$indValue->end_date_time] = isset($indValue->{$aa}) ? $indValue->{$aa} : 0;
                }
               
            }
        }
        // Iterate Second Data Set
        $newData2 = array();
        if (isset($data2) && count($data2)>0) {
            foreach ($data2 as $indKey2 => $indValue2) {
                if(isset($indValue2->end_date_time)){
                    $newData2[$indValue2->end_date_time] = isset($indValue2->{$aa2}) ? $indValue2->{$aa2} : 0;
                }
               
            }
        }     

        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';

        if(count($newData) > 0){

            foreach ($newData as $key => $value) {                
                if(isset($key) && $key!=''){
                    $dataSetDate .= '{
                        "label": "'.$key.'"
                    },';

                    $dataSet .= '{
                        "value": "'.number_format((float)round($value,3), 2, '.', '').'"
                    },';

                    if(isset($newData2[$key])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($newData2[$key],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }

                }            
                
            }
        }
        

        $this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;
        
        $this->load->view('report/chart_loading',$this->data);
    }

    public function getMeterChartAllDayTotalGNvsDIST(){
        
        $type = $this->uri->segment(4);
        //$meterType = $this->uri->segment(5);

        $curVal = '';
        $shift = $this->air_model->getShiftStart(1);
        $this->startDateTime = date('Y-m-d', $this->uri->segment(5));
        $this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
        $startDate = $this->startDateTime = $this->_dateAdd();
        $this->startDateTime = date('Y-m-d', $this->uri->segment(6));
        $this->dataTimeInterval = 'PT24H00M00S';
        $endDate = $this->_dateAdd();

        //$genconsRow = $this->uri->segment(7);

        //$meterdata = $this->fm_model->getMeterTypeWise($meterType);
        $meterdata = $this->air_model->getMeter();
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
                    $data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id, '*', $startDate, $endDate);
                }else{

                    
                    $selected = 'DATE_FORMAT(AMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(AMD.T_temp) T_temp, AVG(AMD.P_pressure) P_pressure, AVG(AMD.TTL_flow) TTL_flow, AVG(AMD.flow) flow, AVG(AMD.steam_enthalpy) steam_enthalpy,AVG(AMD.steam_heat_content) steam_heat_content';
            


                    $data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterDataDayWiseParam($mvalue->meter_id, $selected, $startDate, $endDate);
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
                $dataFinalArray[$mTypekey] = $this->_calculationTimestamp($mTypevalue); 
            }
        } 
        

        // Re-Structure The Array
        
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

        $this->data['meterNameColumn'] = $column;
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

    public function getMeterChartAllDayTotalGNvsDISTForDoubleGraph(){
    	
    	$type = $this->uri->segment(4);
    	//$meterType = $this->uri->segment(5);

    	$curVal = '';
    	$shift = $this->air_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(5));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

        $type2 = $this->uri->segment(7);
    	
    	$meterdata = $this->air_model->getMeter();
    	

    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	$retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);
        $retSelectArr2 = $this->_createSelectQuery($type2, $this->data['dayCount']);

    	$aa = $retSelectArr['aa'];
        $aa2 = $retSelectArr2['aa'];

        $selected = $retSelectArr['selected'];

    	$column = 'Pressure Vs Flow';
    	$seriesname = (isset($type) && $type=="pres") ? 'Pressure(Bar)' : 'Flow(CFM)';
        $seriesname2 = (isset($type2) && $type2=="pres") ? 'Pressure(Bar)' : 'Flow(CFM)';

    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {

 				if(isset($mvalue->type) && $mvalue->type=='main') continue;

    			if($this->data['dayCount'] == 1){
		    		$data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id, '*', $startDate, $endDate);
		    	}else{

		    		
	    			$selected = 'DATE_FORMAT(AMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(AMD.T_temp) T_temp, AVG(AMD.P_pressure) P_pressure, AVG(AMD.TTL_flow) TTL_flow, AVG(AMD.flow) flow, AVG(AMD.steam_enthalpy) steam_enthalpy,AVG(AMD.steam_heat_content) steam_heat_content';
	    	


		    		$data[$mvalue->type][$mvalue->meter_id] = $this->air_model->getAirMeterDataDayWiseParam($mvalue->meter_id, $selected, $startDate, $endDate);
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
    			$dataFinalArray[$mTypekey] = $this->_calculationTimestamp($mTypevalue); 
    		}
    	} 
    	

    	// Re-Structure The Array    	
    	$newDataArray = array();
        $newDataArray2 = array();
    	if(isset($dataFinalArray) && count($dataFinalArray)>0){
    		foreach ($dataFinalArray as $typeKey => $typeValue) {
    			
    			foreach ($typeValue as $dateTimekeys => $dateTimeValues) {
    				$newDataArray[$dateTimekeys][$typeKey] = $dateTimeValues[$aa];
                    $newDataArray2[$dateTimekeys][$typeKey] = $dateTimeValues[$aa2];
    			}
    		}
    	}
    	

    	// Calculation For Generation Vs Distribution For First Set
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
        // Calculation For Generation Vs Distribution For Second Set
        $resultArray2 = array();
        if(isset($newDataArray2) && count($newDataArray2)>0){
            foreach ($newDataArray2 as $dateTimeskey2 => $dateTimesvalue2) {
                if(isset($dateTimesvalue2['gen']) && isset($dateTimesvalue2['dist'])){
                    $resultArray2[$dateTimeskey2] = ($dateTimesvalue2['gen'] - $dateTimesvalue2['dist']);
                }else{
                    if(isset($dateTimesvalue2['gen'])){
                        $resultArray2[$dateTimeskey2] = $dateTimesvalue2['gen'];
                    }elseif(isset($dateTimesvalue2['dist'])){
                        $resultArray2[$dateTimeskey2] = $dateTimesvalue2['gen'];
                    }else{
                        $resultArray2[$dateTimeskey2] = '0';
                    }
                }
            }
        }

    	
    	
    	$resultArray = (count($resultArray)>0) ? array_reverse($resultArray) : array();
        $resultArray2 = (count($resultArray2)>0) ? array_reverse($resultArray2) : array();
    	//var_dump($dataFinalArray);
    	

        $this->data['meterName'] = 'Generation Vs Distribution';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;

        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';
    	if(count($resultArray) > 0){
    			
    		foreach ($resultArray as $dateTimeKey => $dateTimeValue) {
    			
		        if(isset($dateTimeKey) && $dateTimeKey!=''){
                    $dataSetDate .= '{
                        "label": "'.$dateTimeKey.'"
                    },';

                    $dataSet .= '{
                        "value": "'.number_format((float)round($dateTimeValue,3), 2, '.', '').'"
                    },';

                    if(isset($resultArray2[$dateTimeKey])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($resultArray2[$dateTimeKey],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }

                }     
		        
    		}
    	}
    	$this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;
    	
    	$this->load->view('report/chart_loading',$this->data);
    }

    public function getMeterChartAllDayTotal(){
        
        $type = $this->uri->segment(4);
        $meterType = $this->uri->segment(5);

        $curVal = '';
        $shift = $this->air_model->getShiftStart(1);
        $this->startDateTime = date('Y-m-d', $this->uri->segment(6));
        $this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
        $startDate = $this->startDateTime = $this->_dateAdd();
        $this->startDateTime = date('Y-m-d', $this->uri->segment(7));
        $this->dataTimeInterval = 'PT24H00M00S';
        $endDate = $this->_dateAdd();

        //$genconsRow = $this->uri->segment(8);

        $meterdata = $this->air_model->getMeterTypeWise($meterType);
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
 
                if($this->data['dayCount'] == 1){
                    $data[$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id, '*', $startDate, $endDate);
                }else{
                    
                    $selected = 'DATE_FORMAT(AMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(AMD.T_temp) T_temp, AVG(AMD.P_pressure) P_pressure, AVG(AMD.TTL_flow) TTL_flow, AVG(AMD.flow) flow, AVG(AMD.steam_enthalpy) steam_enthalpy,AVG(AMD.steam_heat_content) steam_heat_content';

                    $data[$mvalue->meter_id] = $this->air_model->getAirMeterDataDayWiseParam($mvalue->meter_id, $selected, $startDate, $endDate);
                }


            }
        }

        // Iterate Array according to End Date Time
        $dataArray = $this->_setData($data);  

        // Calculation Of Value
        $dataFinalArray = $this->_calculationTimestamp($dataArray);   
        
        $dataFinalArray = (count($dataFinalArray)>0) ? array_reverse($dataFinalArray) : array();
        //var_dump($dataFinalArray);
        

        $dataSet = '';
        $consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Distribution';

        $this->data['meterNameColumn'] = $column;
        if(count($data) > 0){
                
            foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
                
                
                
                $dataSet .= '{
                        "label": "'.$dateTimeKey.'",
                        "value": "'.round($dateTimeValue[$aa],3).'"
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
        
        $this->load->view('report/chart',$this->data);
    }

    public function getMeterChartAllDayTotalForDoubleGraph(){
    	
    	$type = $this->uri->segment(4);
    	$meterType = $this->uri->segment(5);

    	$shift = $this->air_model->getShiftStart(1);
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(6));
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

        $type2 = $this->uri->segment(8);

    	$meterdata = $this->air_model->getMeterTypeWise($meterType);

    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

    	$retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);
        $retSelectArr2 = $this->_createSelectQuery($type2, $this->data['dayCount']);

    	$aa = $retSelectArr['aa'];
        $aa2 = $retSelectArr2['aa'];
    	
    	$selected = $retSelectArr['selected'];

        $column = 'Pressure Vs Flow';
    	$seriesname = (isset($type) && $type=="pres") ? 'Pressure(Bar)' : 'Flow(CFM)';
        $seriesname2 = (isset($type2) && $type2=="pres") ? 'Pressure(Bar)' : 'Flow(CFM)';

    	$data = array();
    	if(isset($meterdata) && count($meterdata)>0){
    		foreach ($meterdata as $mkey => $mvalue) {
 
    			if($this->data['dayCount'] == 1){
		    		$data[$mvalue->meter_id] = $this->air_model->getAirMeterDataDayShift($mvalue->meter_id, '*', $startDate, $endDate);
		    	}else{
		    		
	    			$selected = 'DATE_FORMAT(AMD.end_date_time, "%Y-%m-%d") end_date_time, AVG(AMD.T_temp) T_temp, AVG(AMD.P_pressure) P_pressure, AVG(AMD.TTL_flow) TTL_flow, AVG(AMD.flow) flow, AVG(AMD.steam_enthalpy) steam_enthalpy,AVG(AMD.steam_heat_content) steam_heat_content';

		    		$data[$mvalue->meter_id] = $this->air_model->getAirMeterDataDayWiseParam($mvalue->meter_id, $selected, $startDate, $endDate);
		    	}


    		}
    	}

    	// Iterate Array according to End Date Time
    	$dataArray = $this->_setData($data);  

    	// Calculation Of Value
    	$dataFinalArray = $this->_calculationTimestamp($dataArray);   
    	
    	$dataFinalArray = (count($dataFinalArray)>0) ? array_reverse($dataFinalArray) : array();
    	
        $this->data['meterName'] = ($meterType=='gen') ? 'Total of Generation' : 'Total of Distribution';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;

        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';
    	if(count($dataFinalArray) > 0){
    			
    		foreach ($dataFinalArray as $dateTimeKey => $dateTimeValue) {
    			
		        if(isset($dateTimeKey) && $dateTimeKey!=''){
                    $dataSetDate .= '{
                        "label": "'.$dateTimeKey.'"
                    },';

                    if(isset($dateTimeValue[$aa])){
                        $dataSet .= '{
                            "value": "'.number_format((float)round($dateTimeValue[$aa],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet .= '{
                            "value": "0"
                        },';
                    }

                    if(isset($dateTimeValue[$aa2])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($dateTimeValue[$aa2],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }

                }
		        
    		}
    	}
    	$this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;
    	
        /*echo "<pre>";
        var_dump($dataFinalArray);
        die();*/


    	$this->load->view('report/chart_loading',$this->data);
    }

    private function _calculationTimestamp($dataSet){

    	$finalArray = array();
    	if(isset($dataSet) && count($dataSet)>0){
    		foreach ($dataSet as $dateKey => $dateValue) {
    			
    			foreach ($dateValue as $meterkey => $metersvalue) {

    				
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

    public function getMeterChartAllDayFORCFMCU_percent(){
        $meter_id = $this->uri->segment(4);
        $type = $this->uri->segment(5);
        $curVal = '';
        $this->startDateTime = date('Y-m-d', $this->uri->segment(6));
        $shift = $this->air_model->getShiftStart(1);
        $this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
        $startDate = $this->startDateTime = $this->_dateAdd();
        $this->startDateTime = date('Y-m-d', $this->uri->segment(7));
        $this->dataTimeInterval = 'PT24H00M00S';
        $endDate = $this->_dateAdd();

        $this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
        $retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);

        $aa = $retSelectArr['aa'];
        $column = $retSelectArr['column'];
        $selected = $retSelectArr['selected'];
        $selected.= ',AM.capacity';
        if($this->data['dayCount'] == 1){
            $data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
        }else{
            $data = $this->air_model->getAirMeterDataDayWiseParam($meter_id, $selected, $startDate, $endDate);
        }
        $finalArray = array();
        if(isset($data) && count($data)>0){
            foreach ($data as $dkey => $dvalue) {
                $finalArray[$dkey]['name'] = isset($dvalue->name) ? $dvalue->name : 'NA';
                
                if(isset($dvalue->flow) && isset($dvalue->capacity) && $dvalue->capacity>0){
                    $finalArray[$dkey]['end_date_time'] = isset($dvalue->end_date_time) ? $dvalue->end_date_time : 'NA';
                    $finalArray[$dkey]['cuPercent'] = (($dvalue->flow / $dvalue->capacity) * 100);
                }
            }
        }
        

        $deltaGenVal = '';
        $staticDeltaVal = '';

        $finalArray = (count($finalArray)>0) ? array_reverse($finalArray) : array();

        $dataSet = '';
        $consistencyArr = array();
        $maxMin = array();
        $this->data['meterName'] = '';
        $this->data['meterNameColumn'] = 'CFM CU(%)';
        if(count($finalArray) > 0){
            $this->data['meterName'] = isset($finalArray[0]['name']) ? $finalArray[0]['name'] : 'NA';
            foreach ($finalArray as $key => $value) {
                
                if(isset($value['end_date_time']) && $value['end_date_time']!='' && isset($value['cuPercent'])){

                    $dataSet .= '{
                        "label": "'.$value['end_date_time'].'",
                        "value": "'.round($value['cuPercent'],3).'"
                    },';
                    
                }
                $maxMin[] = isset($value['cuPercent']) ? $value['cuPercent'] : 0;
                
            }
        }
        $this->data['consisIndex'] = 'no';
        $dataSetConsis = '';
        $this->data['graph_logic'] = '1';

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
    	$shift = $this->air_model->getShiftStart(1);
    	$this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
    	$startDate = $this->startDateTime = $this->_dateAdd();
    	$this->startDateTime = date('Y-m-d', $this->uri->segment(7));
    	$this->dataTimeInterval = 'PT24H00M00S';
    	$endDate = $this->_dateAdd();

    	$this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
    	$retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);
    	$aa = $retSelectArr['aa'];
    	$column = $retSelectArr['column'];
    	$selected = $retSelectArr['selected'];
    	if($this->data['dayCount'] == 1){
    		$data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
    	}else{
    		$data = $this->air_model->getAirMeterDataDayWiseParam($meter_id, $selected, $startDate, $endDate);
    	}

    	$deltaGenVal = '';
    	$staticDeltaVal = '';

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

    /*private function _dateAddcustom($date,$timestamp){
        $date = new DateTime($date);
        $date->add(new DateInterval($timestamp));
        return $date->format('Y-m-d H:i:s');
    }*/

    public function getMeterChartAllDayForTotaliser(){
        $meter_id = $this->uri->segment(4);
        $type = $this->uri->segment(5);
        //$curVal = '';
        $shift = $this->air_model->getShiftStart(1);
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

        $meter_details = $this->air_model->getMeterDetails($meter_id);
        $this->data['meterName'] = isset($meter_details[0]->name) ? $meter_details[0]->name : 'NA';
        
        $meterType = isset($meter_details[0]->type) ? $meter_details[0]->type : 'NA';
        $this->data['meterNameColumn'] = $meterType=='gen' ? 'Generation' : 'Consumption';
        
        if(isset($datas['splits'][$meterType][$meter_id]) && count($datas['splits'][$meterType][$meter_id])>0){
            $this->data['TTL_flow_new'] = $datas['splits'][$meterType][$meter_id];
        }
        /*echo "<pre>";
        var_dump($meter_details);
        var_dump($datas);
        var_dump($this->data);
        die();*/
        
        $this->load->view('report/table',$this->data);
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
        

        
        $meter_details = $this->air_model->getMeterDetails($meter_id);
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

    public function getMeterChartAllDayForDoubleGraph(){
        $meter_id = $this->uri->segment(4);
        $type = $this->uri->segment(5);
        $this->startDateTime = date('Y-m-d', $this->uri->segment(6));
        $shift = $this->air_model->getShiftStart(1);
        $this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
        $startDate = $this->startDateTime = $this->_dateAdd();
        $this->startDateTime = date('Y-m-d', $this->uri->segment(7));
        $this->dataTimeInterval = 'PT24H00M00S';
        $endDate = $this->_dateAdd();

        $type2 = $this->uri->segment(8);

        $this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');
        $retSelectArr = $this->_createSelectQuery($type, $this->data['dayCount']);
        $retSelectArr2 = $this->_createSelectQuery($type2, $this->data['dayCount']);
        $aa = $retSelectArr['aa'];
        $aa2 = $retSelectArr2['aa'];
        $selected = $retSelectArr['selected'];
        $selected2 = $retSelectArr2['selected'];

        $column = 'Pressure Vs Flow';        
        $seriesname = (isset($type) && $type=="pres") ? 'Pressure(Bar)' : 'Flow(CFM)';
        $seriesname2 = (isset($type2) && $type2=="pres") ? 'Pressure(Bar)' : 'Flow(CFM)';

        if($this->data['dayCount'] == 1){
            $data = $this->air_model->getAirMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
            $data2 = $this->air_model->getAirMeterDataDayShift($meter_id, $selected2, $startDate, $endDate);
        }else{
            $data = $this->air_model->getAirMeterDataDayWiseParam($meter_id, $selected, $startDate, $endDate);
            $data2 = $this->air_model->getAirMeterDataDayWiseParam($meter_id, $selected2, $startDate, $endDate);
        }

        

        $data = (count($data)>0) ? array_reverse($data) : array();
        $data2 = (count($data2)>0) ? array_reverse($data2) : array();

        $this->data['meterName'] = isset($data[0]->name) ? $data[0]->name : 'NA';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;

        // Iterate First Data Set
        $newData = array();
        if (isset($data) && count($data)>0) {
            foreach ($data as $indKey => $indValue) {
                if(isset($indValue->end_date_time)){
                    $newData[$indValue->end_date_time] = isset($indValue->{$aa}) ? $indValue->{$aa} : 0;
                }
               
            }
        }
        // Iterate Second Data Set
        $newData2 = array();
        if (isset($data2) && count($data2)>0) {
            foreach ($data2 as $indKey2 => $indValue2) {
                if(isset($indValue2->end_date_time)){
                    $newData2[$indValue2->end_date_time] = isset($indValue2->{$aa2}) ? $indValue2->{$aa2} : 0;
                }
               
            }
        }

        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';

        if(count($newData) > 0){

            foreach ($newData as $key => $value) {                
                if(isset($key) && $key!=''){
                    $dataSetDate .= '{
                        "label": "'.$key.'"
                    },';

                    $dataSet .= '{
                        "value": "'.number_format((float)round($value,3), 2, '.', '').'"
                    },';

                    if(isset($newData2[$key])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($newData2[$key],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }

                }            
                
            }
        }
        

        $this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;
        
        $this->load->view('report/chart_loading',$this->data);
    }

    private function _dateSub($timeInterval,$date){

        $date = new DateTime($date); // For today/now, don't pass an arg.
        $date->modify($timeInterval);
        
        return $date->format("Y-m-d");

    }

    private function _dateSubcustom($date,$timestamp){
        $date = new DateTime($date);
        $date->sub(new DateInterval($timestamp));
        return $date->format('Y-m-d H:i:s');
    }

    private function restructureDataForPSapi($dataArray=array()){
    
        $resultArray = array();
        if(count($dataArray)>0){
          foreach ($dataArray as $datekey => $datevalue) {
            $dt = explode(' ', $datekey);

            $formatDate = isset($dt[0]) ? date('m/d/Y',strtotime($dt[0])) : '';
            $final_dt = (isset($formatDate) && $formatDate!='' && isset($dt[1])) ? $formatDate.'_'.$dt[1] : '';
            if(isset($final_dt) && $final_dt!=''){
              $resultArray[] = array("DateAndTime"=>$final_dt,"FLOW"=>$datevalue);
            }
            
          }
        }

        return $resultArray;
        
    }

    private function restructureDataForDIY1api($dataArray=array()){
    
        $resultArray = array();
        if(count($dataArray)>0){
          foreach ($dataArray as $datekey => $datevalue) {
            $dt = explode(' ', $datekey);

            $formatDate = isset($dt[0]) ? date('m/d/Y',strtotime($dt[0])) : '';
            $final_dt = (isset($formatDate) && $formatDate!='' && isset($dt[1])) ? $formatDate.'_'.$dt[1] : '';
            if(isset($final_dt) && $final_dt!=''){
              $resultArray[] = array("Date"=>$final_dt,"FLOW"=>$datevalue);
            }
            
          }
        }
      
        return $resultArray;
        
    }

    private function makeAPIcallDIY1($dataArray=array(),$keys){

    $returnArray = array();
    if(count($dataArray)>0 && $keys!=''){

      $arrF = array('filepath'=>$dataArray,"keys"=> $keys);
      $posts['dataset'] = json_encode($arrF);
      //echo "<pre>";
      //var_dump($posts);
      //die();
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL,"http://stage.en-view.com/DIY1api/index.php");
      //curl_setopt($ch, CURLOPT_URL,"http://boostenergyefficiency.com/apicall/index/");

      curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
      //'Content-Type: application/json',
      //'Content-Length: ' . strlen($posts),
      //'Accept: multipart/form-data',
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(                 
                        
                        'MPLACE-API-KEY: al0RYN8W2sECyJG2NNlBSDThxwZurjSBFTsDHBAJ3flCZ2EMP024',
                        'MPLACE-SECRETE-KEY: uYMsnSwvpF7TWr8LrKlmMhCw3GZ5niBPg7w0EiJfwdnGUa4VpB28'));

      curl_setopt($ch, CURLOPT_POST,1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);

      $result = curl_exec ($ch);
      
      //echo "<pre>";
      //var_dump($result);
      
      curl_close ($ch);

      $decodeResult = json_decode($result,true);
      /*if(isset($decodeResult['status']) && $decodeResult['status']=="true" && isset($decodeResult['data']) && count($decodeResult['data'])>0){
        $returnArray = $decodeResult['data'];
      }*/

      if(isset($decodeResult['status']) && isset($decodeResult['message'])){
        $returnArray = $decodeResult;
      }
    }

    

    return $returnArray;
  }

    private function makeAPIcallPS($dataArray=array(),$date_key,$date_format,$off_value){

        $returnArray = array();
        if(count($dataArray)>0 && $date_key!='' && $date_format!='' && $off_value!=''){

          $arrF = array('data'=>$dataArray,"date_key"=> $date_key, "date_format"=> $date_format, "off_value"=>$off_value);
          $posts['dataset'] = json_encode($arrF);
          //echo "<pre>";
          //var_dump($posts);
          //die();
          $ch = curl_init();

          curl_setopt($ch, CURLOPT_URL,"http://stage.en-view.com/powersignatureconti/index.php");
          //curl_setopt($ch, CURLOPT_URL,"http://boostenergyefficiency.com/apicall/index/");

          curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
          //'Content-Type: application/json',
          //'Content-Length: ' . strlen($posts),
          //'Accept: multipart/form-data',
          curl_setopt($ch,CURLOPT_HTTPHEADER,array(                 
                            
                            'MPLACE-API-KEY: cH2QA6w9XGEV2OBNncfZndofgnsnygkLXmByt8HqOlmQaXExaU19',
                            'MPLACE-SECRETE-KEY: uYMsnSwvpF7TWr8LrKlmMhCw3GZ5niBPg7w0EiJfwdnGUa4VpB28'));

          curl_setopt($ch, CURLOPT_POST,1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);

          $result = curl_exec ($ch);
          
          /*echo "<pre>";
          var_dump($result);*/
          
          curl_close ($ch);

          $decodeResult = json_decode($result,true);
          /*if(isset($decodeResult['status']) && $decodeResult['status']=="true" && isset($decodeResult['data']) && count($decodeResult['data'])>0){
            $returnArray = $decodeResult['data'];
          }*/


          if(isset($decodeResult['status']) && isset($decodeResult['data'])){
            $returnArray = $decodeResult;
          }
        }

        

        return $returnArray;
    }

    public function getCFMreport(){

        ini_set('max_execution_time', 300);

        // Get POST method data
        $meterid = $this->uri->segment(4);
        
        $endDate = date('Y-m-d',strtotime($this->uri->segment(5)));

        $offValue_type = $this->uri->segment(6);

        $daysdata = $this->uri->segment(7);

        $daysdata = (isset($daysdata) && $daysdata!='') ? $daysdata : '7';

        // Get Last 10 Days Date
        $startDate = $this->_dateSub('-'.$daysdata.' day',$endDate);

        // Generate Time
        $startDate = $startDate.' 00:00:00';
        $endDate = $endDate.' 00:00:00';

        $meterName = 'NA';
        $genArray = array();
        // If Meter Id Is Presented
        if(isset($meterid) && $meterid>0 && $meterid!='totgen' && $meterid!='totdist'){
            $meterName = isset($this->air_model->getMeterName($meterid)[0]->name) ? $this->air_model->getMeterName($meterid)[0]->name : 'NA';
            // Fetch The Flow and end time from database
            $retArray = $this->air_model->getCFMdataset($meterid,'AMD.end_date_time,AMD.flow',$startDate,$endDate);

            // Create An Array using timestamp and remove the zero values
            
            if(isset($retArray) && count($retArray)>0){
                foreach ($retArray as $key => $value) {
                    if(isset($value->flow) && $value->flow>0){
                        //$genArray[$value->end_date_time] = $value->flow;
                        $genArray[date('Y-m-d',strtotime($value->end_date_time))][$value->end_date_time] = $value->flow;
                    }
                }
            }
        }
        
        // If Total Generation or distibution
        //var_dump($meterid);
        if(isset($meterid) && !is_numeric($meterid) && ($meterid=='totgen' || $meterid='totdist')){
            $meterName = (($meterid=='totgen') ? 'Total Generation' : (($meterid=='totdist') ? 'Total Distribution' : 'NA'));
        
            // Get The Meter List
            $meteridArray = array();
            $mType = (($meterid=='totgen') ? 'gen' : (($meterid=='totdist') ? 'dist' : 'NA'));
            if(isset($mType) && $mType!='NA'){
                $meteridArray = $this->air_model->getMeterTypeWise($mType);
                //echo "<pre>";
                //var_dump($meteridArray);
                if(isset($meteridArray) && count($meteridArray)>0){
                    $meterDataArray = array();
                    foreach ($meteridArray as $mIndKey => $mIndValue) {
                        // Fetch The Flow and end time from database
                        if(isset($mIndValue->meter_id) && $mIndValue->meter_id!=''){
                            $meterDataArray[$mIndValue->meter_id] = $this->air_model->getCFMdataset($mIndValue->meter_id,'AMD.end_date_time,AMD.flow',$startDate,$endDate);
                        }
                        
                    }
                    
                    $calArray = array();
                    if(isset($meterDataArray) && count($meterDataArray)>0){
                        foreach ($meterDataArray as $meterIdkey => $meterIdvalue) {
                            foreach ($meterIdvalue as $objkey => $objvalue) {
                                if(isset($objvalue->end_date_time) && isset($objvalue->flow)){
                                    $calArray[$objvalue->end_date_time][$meterIdkey]=$objvalue->flow;
                                }
                                
                            }
                            
                        }
                    }
                    

                    $calResultArray = array();
                    if(isset($calArray) && count($calArray)>0){
                        foreach ($calArray as $akey => $avalue) {
                            $calResultArray[$akey] = array_sum($avalue);
                        }
                    }
                    
                    if(isset($calResultArray) && count($calResultArray)>0){
                        foreach ($calResultArray as $timeSkey => $timeSvalue) {
                            $genArray[date('Y-m-d',strtotime($timeSkey))][$timeSkey] = $timeSvalue;
                        }
                    }
                    //var_dump($genArray);
                    
                }
            }
            
        }   
        //echo "<pre>";
        //var_dump($genArray);

        $resultDataArray['PS_result'] = array();
        $newGenArray = array();
        $off_valueArray = array();
        if(isset($genArray) && count($genArray)>0){
            foreach ($genArray as $day => $dayvalue) {

                if(!isset($resultDataArray['PS_result'][$day])) $resultDataArray['PS_result'][$day] = array();

                if(isset($dayvalue) && count($dayvalue)>0){
                    $timestapDataDays = array_reverse($dayvalue);
                    // Generate the Average value on flow
                    $avgFlow = (array_sum($timestapDataDays) / count($timestapDataDays));
                    //var_dump($avgFlow);
                    // Check Off value type from user
                    if(isset($offValue_type) && $offValue_type=="AVG"){
                        $off_value = $avgFlow;
                    }else if((isset($offValue_type) && $offValue_type=="HIGH") || (isset($offValue_type) && $offValue_type=="LOW")){                
                            
                        // Make API Call For Get Standard Deviation
                        $dataReadyForDIY1api_SD = $this->restructureDataForDIY1api($timestapDataDays);
                        $getSD = $this->makeAPIcallDIY1($dataReadyForDIY1api_SD,'B');

                        if(isset($getSD['status']) && $getSD['status']=="true" && isset($getSD['message']['data']['B']['Standard Deviation'])){
                            if(isset($offValue_type) && $offValue_type=="HIGH"){
                                $off_value = ($avgFlow + $getSD['message']['data']['B']['Standard Deviation']);
                            }
                            if(isset($offValue_type) && $offValue_type=="LOW"){
                                $off_value = ($avgFlow - $getSD['message']['data']['B']['Standard Deviation']);
                            }
                        }

                    }else{
                        $off_value = $avgFlow;
                    }

                    // Regenerate The Data restructure for Call the power signature
                    $newArray_PS = $this->restructureDataForPSapi($timestapDataDays);

                    if(isset($newArray_PS) && count($newArray_PS)>0 && isset($off_value) && $off_value>0){
                        $off_valueArray[$day] = $off_value;
                        $resultofPS = $this->makeAPIcallPS($newArray_PS,'DateAndTime','mmddyyyy',$off_value);
                        if(isset($resultofPS['status']) && $resultofPS['status']=="true" && isset($resultofPS['data']) && count($resultofPS['data'])>0){

                            $resultDataArray['PS_result'][$day]['calculated'] = $this->calculatedOnPSanalysis($resultofPS);

                            $resultDataArray['PS_result'][$day]['details'] = $resultofPS;
                        }else{
                            $resultDataArray['PS_result'][$day]['details'] = array('status'=>"error",'data'=>"Error in PS Analysis.");
                        }
                        
                    }
                }
            }
        }

        /*echo "<pre>";
        var_dump($resultDataArray);
        die();*/
        
        /*
        //$genArray = (count($genArray)>0) ? array_reverse($genArray) : array();
        if(isset($genArray) && count($genArray)>0){

            // Generate the Average value on flow
            $avgFlow = (array_sum($genArray) / count($genArray));

            // Check Off value type from user
            if(isset($offValue_type) && $offValue_type=="AVG"){
                $off_value = $avgFlow;
            }else if((isset($offValue_type) && $offValue_type=="HIGH") || (isset($offValue_type) && $offValue_type=="LOW")){                
                    
                // Make API Call For Get Standard Deviation
                $dataReadyForDIY1api_SD = $this->restructureDataForDIY1api($genArray);
                $getSD = $this->makeAPIcallDIY1($dataReadyForDIY1api_SD,'B');

                if(isset($getSD['status']) && $getSD['status']=="true" && isset($getSD['message']['data']['B']['Standard Deviation'])){
                    if(isset($offValue_type) && $offValue_type=="HIGH"){
                        $off_value = ($avgFlow + $getSD['message']['data']['B']['Standard Deviation']);
                    }
                    if(isset($offValue_type) && $offValue_type=="LOW"){
                        $off_value = ($avgFlow - $getSD['message']['data']['B']['Standard Deviation']);
                    }
                }

            }else{
                $off_value = $avgFlow;
            }

            // Regenerate The Data restructure for Call the power signature
            $newArray_PS = $this->restructureDataForPSapi($genArray);
            

            if(isset($newArray_PS) && count($newArray_PS)>0 && isset($off_value) && $off_value>0){
                $resultDataArray['PS_result'] = $this->makeAPIcallPS($newArray_PS,'DateAndTime','mmddyyyy',$off_value);
            }           
            
        }*/

        $resultDataArray['meter_name'] = (isset($meterName) && $meterName!='') ? $meterName : 'NA';    
        $resultDataArray['modal'] = 2;
        $resultDataArray['offvalueforPSArr'] = (isset($off_valueArray) && count($off_valueArray)>0) ? $off_valueArray : 'NA';

        $this->load->view('report/tabular',$resultDataArray);
    
    }

    private function calculatedOnPSanalysis($psAnalysedDataset){
        $resutlArray = array();

        // Calculate No. Of Cycle
        $resutlArray['tot_cycle'] = (isset($psAnalysedDataset['data']) && count($psAnalysedDataset['data'])>0) ? count($psAnalysedDataset['data']) : 0;

        $tempArr = array();
        foreach ($psAnalysedDataset['data'] as $key => $value) {
            if(!isset($tempArr['avg_duration'])) $tempArr['avg_duration'] = array();
            if(!isset($tempArr['avg_duration_less_1'])) $tempArr['avg_duration_less_1'] = array();
            if(!isset($tempArr['avg_duration_1_2'])) $tempArr['avg_duration_1_2'] = array();
            if(!isset($tempArr['avg_duration_2_3'])) $tempArr['avg_duration_2_3'] = array();
            if(!isset($tempArr['avg_duration_above_3'])) $tempArr['avg_duration_above_3'] = array();

            // Average Duration
            if(isset($value['duration(Hr)']) && $value['duration(Hr)']>0){
                $tempArr['avg_duration'][] = $value['duration(Hr)'];
            }

            // % Cycles (<1 Hr)
            if(isset($value['duration(Hr)']) && $value['duration(Hr)']<1){
                $tempArr['avg_duration_less_1'][] = $value['duration(Hr)'];
            }

            // % Cycles (1-2 Hr)
            if(isset($value['duration(Hr)']) && $value['duration(Hr)']>=1 && $value['duration(Hr)']<=2){
                $tempArr['avg_duration_1_2'][] = $value['duration(Hr)'];
            }

            // % Cycles (2-3 Hr)
            if(isset($value['duration(Hr)']) && $value['duration(Hr)']>=2 && $value['duration(Hr)']<=3){
                $tempArr['avg_duration_2_3'][] = $value['duration(Hr)'];
            }
            // % Cycles (>3 Hr)
            if(isset($value['duration(Hr)']) && $value['duration(Hr)']>3){
                $tempArr['avg_duration_above_3'][] = $value['duration(Hr)'];
            }
            
        }

        if(isset($tempArr) && count($tempArr)>0){
            foreach ($tempArr as $textkey => $textvalue) {

                if(!isset($resutlArray['avg_duration'])) $resutlArray['avg_duration'] = 0;
                if(!isset($resutlArray['avg_duration_less_1'])) $resutlArray['avg_duration_less_1'] = 0;
                if(!isset($resutlArray['avg_duration_1_2'])) $resutlArray['avg_duration_1_2'] = 0;
                if(!isset($resutlArray['avg_duration_2_3'])) $resutlArray['avg_duration_2_3'] = 0;
                if(!isset($resutlArray['avg_duration_above_3'])) $resutlArray['avg_duration_above_3'] = 0;                
                if(count($textvalue)>0){
                    if($textkey=="avg_duration"){
                        $resutlArray[$textkey] = (array_sum($textvalue) / count($textvalue));
                    }else{
                       $resutlArray[$textkey] = ((count($textvalue)/$resutlArray['tot_cycle']) * 100); 
                    }
                }
                
            }
        }
        //echo "<pre>";
        //var_dump($resutlArray);

        return $resutlArray;
    }

    public function getCFMreport_setmeter(){

        //$resultDataArray['datetimestamp'] = $this->uri->segment(4);//date('Y-m-d',$this->uri->segment(4));

        $resultDataArray['max_date'] = $this->uri->segment(4);
        $resultDataArray['min_date'] = $this->uri->segment(5);

        $meterDetails = $this->air_model->getMeter();

        
        
        $meterArray = array();
        if(isset($meterDetails) && count($meterDetails)>0){
            foreach ($meterDetails as $key => $value) {
                if(isset($value->name) && isset($value->meter_id)){
                    $meterArray[$value->meter_id] = $value->name;
                }
            }
        }

        $resultDataArray['meter_details'] = $meterArray;
        /*echo "<pre>";
        var_dump($meterArray);*/
        $resultDataArray['modal'] = 1;

        $this->load->view('report/tabular',$resultDataArray);

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

	private function _createSelectQuery($type = 'press', $day = 1){
    	$retData = array();
    	if($day == 1){
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
    	}else{
    		$selected = 'AM.name,DATE_FORMAT(AMD.end_date_time, "%Y-%m-%d") end_date_time';
	    	$aa = 'T_temp';
	  		$column = 'Temperature';
	    	if($type == 'temp'){
	    		$selected .= ',AVG(NULLIF(AMD.T_temp,0)) T_temp';
	    	}else if($type == 'pres'){
	    		$selected .= ',AVG(NULLIF(AMD.P_pressure,0)) P_pressure';
	    		$aa = 'P_pressure';
	    		$column = 'Pressure';
	    	}else if($type == 'flow'){
	    		$selected .= ',AVG(NULLIF(AMD.flow,0)) flow';
	    		$aa = 'flow';
	    		$column = 'Flow';
	    	}else{
	    		$selected .= ',AVG(NULLIF(AMD.steam_enthalpy,0)) steam_enthalpy';
	    		$aa = 'steam_enthalpy';
	    		$column = 'Enthalpy';
	    	}
    	}
    	$retData['selected'] = $selected;
    	$retData['aa'] = $aa;
    	$retData['column'] = $column;
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
                    
                    $dataSet .= '{
                        "label": "'.$value->end_date_time.'",
                        "value": "'.round($valsss,3).'"
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
        
        $this->load->view('report/chart',$this->data);
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
	

    public function getMeterChartAllDay_CPPforDoubleGraph(){
        $meter_id = $this->uri->segment(4);
        $type = $this->uri->segment(5);
        //$curVal = '';
        $shift = $this->cpp_model->getShiftStart(1);
        $this->startDateTime = date('Y-m-d', $this->uri->segment(6));
        $this->dataTimeInterval = 'PT'.$shift[0]->config_val.'H00M00S';
        $startDate = $this->startDateTime = $this->_dateAdd();
        $this->startDateTime = date('Y-m-d', $this->uri->segment(7));
        $this->dataTimeInterval = 'PT24H00M00S';
        $endDate = $this->_dateAdd();

        $type2 = $this->uri->segment(8);

        //$genDistRow = $this->uri->segment(8);
        //$meterType = $this->uri->segment(9);
        
        $this->data['dayCount'] = $this->_getDateDiff($startDate, $endDate, '%d');

        $retSelectArr = $this->_createSelectQuery_CPP($type, $this->data['dayCount']);
        $retSelectArr2 = $this->_createSelectQuery_CPP($type2, $this->data['dayCount']);

        $aa = $retSelectArr['aa'];
        $aa2 = $retSelectArr2['aa'];
        $selected = $retSelectArr['selected'];
        $selected2 = $retSelectArr2['selected'];
        $column = 'Pressure Vs Flow';

        $seriesname = (isset($type) && $type=='pres') ? 'Pressure(Bar)' : 'Flow(CFM)';
        $seriesname2 = (isset($type2) && $type2=='pres') ? 'Pressure(Bar)' : 'Flow(CFM)';

        if($this->data['dayCount'] == 1){
            $data = $this->cpp_model->getDataloggerMeterDataDayShift($meter_id, $selected, $startDate, $endDate);
            $data2 = $this->cpp_model->getDataloggerMeterDataDayShift($meter_id, $selected2, $startDate, $endDate);
        }else{
            $data = $this->cpp_model->getDataloggerMeterDataDayWiseParam($meter_id, $selected, $startDate, $endDate);
            $data2 = $this->cpp_model->getDataloggerMeterDataDayWiseParam($meter_id, $selected2, $startDate, $endDate);
        }
        
        $data = (count($data)>0) ? array_reverse($data) : array();
        $data2 = (count($data2)>0) ? array_reverse($data2) : array();

        
        $this->data['meterName'] = isset($data[0]->name) ? $data[0]->name : 'NA';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;

        // Iterate First Data Set
        $newData = array();
        if (isset($data) && count($data)>0) {
            foreach ($data as $indKey => $indValue) {
                if(isset($indValue->end_date_time)){
                    $newData[$indValue->end_date_time] = isset($indValue->{$aa}) ? $indValue->{$aa} : 0;
                }
               
            }
        }
        // Iterate Second Data Set
        $newData2 = array();
        if (isset($data2) && count($data2)>0) {
            foreach ($data2 as $indKey2 => $indValue2) {
                if(isset($indValue2->end_date_time)){
                    $newData2[$indValue2->end_date_time] = isset($indValue2->{$aa2}) ? $indValue2->{$aa2} : 0;
                }
               
            }
        }

        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';

        if(count($newData) > 0){

            foreach ($newData as $key => $value) {                
                if(isset($key) && $key!=''){
                    $dataSetDate .= '{
                        "label": "'.$key.'"
                    },';

                    $dataSet .= '{
                        "value": "'.number_format((float)round($value,3), 2, '.', '').'"
                    },';

                    if(isset($newData2[$key])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($newData2[$key],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }

                }            
                
            }
        }

        $this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;
        
        /*echo "<pre>";
        var_dump($this->data);
        die();*/

        $this->load->view('report/chart_loading',$this->data);
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
                

                $dataSet .= '{
                    "label": "'.$value->end_date_time.'",
                    "value": "'.round($valsss,3).'"
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
        
        $this->load->view('report/chart',$this->data);
    }

    public function getMeterChartShift_CPPforDoubleGraph(){

        

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
        $selected2 = 'DM.name,DMD.end_date_time';
        $aa = 'pressure';
        $aa2 = 'flow';
        $column = 'Pressure Vs Flow';
        $seriesname = 'Pressure(Bar)';
        $seriesname2 = 'Flow(CFM)';

        if($type == 'pres'){
            $selected .= ',DMD.pressure';
            $selected2 .= ',DMD.flow';
            $aa = 'pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';

        }else if($type == 'flow'){
            $selected .= ',DMD.flow';
            $selected2 .= ',DMD.pressure';
            $aa = 'flow';
            $aa2 = 'pressure';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Flow(CFM)';
            $seriesname2 = 'Pressure(Bar)';
        }else{
            $selected .= ',DMD.pressure';
            $selected2 .= ',DMD.flow';
            $aa = 'pressure';
            $aa2 = 'flow';
            $column = 'Pressure Vs Flow';
            $seriesname = 'Pressure(Bar)';
            $seriesname2 = 'Flow(CFM)';
        }
        $this->data['dayCount'] = 1;
        $data = $this->cpp_model->getDataloggerMeterDataDayShift($meter_id, $selected, $shiftStartDate, $shiftEndDate);
        $data2 = $this->cpp_model->getDataloggerMeterDataDayShift($meter_id, $selected2, $shiftStartDate, $shiftEndDate);
        

        $data = (count($data)>0) ? array_reverse($data) : array();
        $data2 = (count($data2)>0) ? array_reverse($data2) : array();

        $this->data['meterName'] = isset($data[0]->name) ? $data[0]->name : 'NA';
        $this->data['meterNameColumn'] = $column;
        $this->data['seriesname'] = $seriesname;
        $this->data['seriesname2'] = $seriesname2;

        // Iterate First Data Set
        $newData = array();
        if (isset($data) && count($data)>0) {
            foreach ($data as $indKey => $indValue) {
                if(isset($indValue->end_date_time)){
                    $newData[$indValue->end_date_time] = isset($indValue->{$aa}) ? $indValue->{$aa} : 0;
                }
               
            }
        }
        // Iterate Second Data Set
        $newData2 = array();
        if (isset($data2) && count($data2)>0) {
            foreach ($data2 as $indKey2 => $indValue2) {
                if(isset($indValue2->end_date_time)){
                    $newData2[$indValue2->end_date_time] = isset($indValue2->{$aa2}) ? $indValue2->{$aa2} : 0;
                }
               
            }
        }
        $dataSetDate = '';
        $dataSet = '';
        $dataSet2 = '';

        if(count($newData) > 0){

            foreach ($newData as $key => $value) {                
                if(isset($key) && $key!=''){
                    $dataSetDate .= '{
                        "label": "'.$key.'"
                    },';

                    $dataSet .= '{
                        "value": "'.number_format((float)round($value,3), 2, '.', '').'"
                    },';

                    if(isset($newData2[$key])){
                        $dataSet2 .= '{
                            "value": "'.number_format((float)round($newData2[$key],3), 2, '.', '').'"
                        },';
                    }else{
                        $dataSet2 .= '{
                            "value": "0"
                        },';
                    }

                }            
                
            }
        }
        

        $this->data['dataSetDate'] = $dataSetDate;
        $this->data['dataSet'] = $dataSet;
        $this->data['dataSet2'] = $dataSet2;

        
        $this->load->view('report/chart_loading',$this->data);
    }
}