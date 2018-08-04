<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anomaly extends MY_Controller {
    public $tagId = '97';
    public $tagDesc = 'Apparent-Power-avg';
    public $startDateTime = '';
    public $dataTimeInterval = 'PT00H15M00S';
    public function __construct(){
        parent::__construct();
        $this->table = array('ems_cron_history','ems');
        $this->load->library('mongo_db');
    }

    public function index(){ 
        echo "Start Time = ".date('Y-m-d H:i:s')."==";   
        echo "<pre>";
        //$this->mongo_db->limit(10);
        $startDateTime = $this->startDateTime = '2017-07-10 00:00:00';
        $this->dataTimeInterval = 'PT06H00M00S';
        $endDateTime = $this->_dateAdd();
        //echo $startDateTime."==".$endDateTime.'<br>';
        $where = array('TAGID' => $this->tagId);
        $this->mongo_db->where($where);
        $this->mongo_db->where_gte('DateAndTime', $startDateTime);
        $this->mongo_db->where_lt('DateAndTime', $endDateTime);
        $data = $this->mongo_db->get($this->table[1]);
        if(count($data) > 0){
            $newData = $this->_prepareArray($data);
            if(count($newData) > 0){
                $this->tagId = $newData['TAGID'];
                $apiRes = $this->callApi($newData['anomaly']);
                //if($apiRes->status == 'SUCCESS'){
                    $this->_addResult($apiRes, $startDateTime);
                //}elseif($apiRes->status == 'ERROR'){
                    //echo $apiRes->data;
                //} 
                unset($newData);
            }  
        }

        $this->startDateTime = $endDateTime;
        $this->dataTimeInterval = 'PT00H15M00S';
        /*$this->mongo_db->where_gte('startDateTime', $this->startDateTime);
        $cronHist = $this->mongo_db->get($this->table[0]);
        if(count($cronHist) > 0){*/
        for ($i = 1; $i <= 88; $i++) {
            //echo $valueHist['startDateTime']."==".$valueHist['endDateTime'].'<br>';
            $valueHist['startDateTime'] = $this->startDateTime ;
            $valueHist['endDateTime'] = $this->_dateAdd();
            $where = array('TAGID' => $this->tagId);
            $this->mongo_db->where($where);
            $this->mongo_db->where_gte('DateAndTime', $valueHist['startDateTime']);
            $this->mongo_db->where_lt('DateAndTime', $valueHist['endDateTime']);
            $data = $this->mongo_db->get($this->table[1]);
            $newData = $this->_prepareArray($data);
            if(count($newData) > 0){
                $this->tagId = $newData['TAGID'];
                $apiRes = $this->callApi($newData['anomaly']);
                //if($apiRes->status == 'SUCCESS'){
                    $this->_addResult($apiRes, $valueHist['startDateTime']);
                //}elseif($apiRes->status == 'ERROR'){
                    //echo $apiRes->data;
                //}  
                $this->startDateTime =  $valueHist['endDateTime'];
                unset($newData);

            }                
        }
        echo "==End Time = ".date('Y-m-d H:i:s')."==<br>";  
        
    }

    private function _prepareArray($data = array()){
        $retData = array();
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $retData['TAGID'] = $value['TAGID'];
                $retData['anomaly'][] = array($this->tagDesc => $value['TagValue']);
            }
        }
        return $retData;
    }

    private function _dateAdd(){
        $date = new DateTime($this->startDateTime);
        $date->add(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

    public function callApi($dbData = array()){
        $running_info = $this->_makeRunningInfo();
        //print_r($running_info);
        $arr2 = array(
                    "data_key" => $this->tagDesc,
                    "given_high" => "",
                    "given_low" => "", 
                    "running_info" => $running_info
                );
        //print_r($arr2);
        $arrF = array('data'=>$dbData, "additional_info"=>$arr2);
        $post['dataset'] = json_encode($arrF);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://localhost:81/apimarketplace/apicall/index/");
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                     
                                        'MPLACE-API-KEY: LykaJWrQBcRn4E3Rna0Sd5bwOewoUYxzH8aFU4SMLCFuX7YSKr15',
                                        'MPLACE-SECRETE-KEY: uiQ2rkcKSDVgrLdCO5ZbdGcTYLFGGYDahtdIOptG3oXvaa8Zg72'));
        $result = curl_exec($ch);
        curl_close($ch);
        $apiRes = json_decode($result); 
        unset($result); 
        //print_r($apiRes);exit;
        return $apiRes;
    }

    private function _makeRunningInfo(){
        $running_info = array();
        $sql = "SELECT AJD.json_all_data FROM anomaly_relational_data ARD
                JOIN anomaly_json_data AJD ON AJD.ard_id=ARD.ard_id
                WHERE ARD.tag_id=".$this->tagId."
                ORDER BY AJD.id";
        $data = $this->tbl_generic_model->ExecuteQuery($sql);
        //print_r($data);
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                if($value != ''){
                    $val = json_decode($value->json_all_data);
                    if($val->status == 'SUCCESS'){
                        array_push($running_info, $val->data);
                    }
                }
            }
        }
        return $running_info;
    }

    private function _addResult($apiRes = array(), $startDateTime = ''){
        $anomaly_relational_data['tag_id'] = $this->tagId;
        $anomaly_relational_data['cal_outlier_percent_mean'] = isset($apiRes->data->calculated->outlier_percent_mean)?$apiRes->data->calculated->outlier_percent_mean:0;
        $anomaly_relational_data['cal_counter'] = isset($apiRes->data->calculated->counter)?$apiRes->data->calculated->counter:0;
        $anomaly_relational_data['cal_analysis'] = isset($apiRes->data->calculated->analysis)?$apiRes->data->calculated->analysis:0;
        $anomaly_relational_data['cal_high'] = isset($apiRes->data->calculated->high)?$apiRes->data->calculated->high:0;
        $anomaly_relational_data['cal_low'] = isset($apiRes->data->calculated->low)?$apiRes->data->calculated->low:0;
        $anomaly_relational_data['cal_outlier_towards'] = isset($apiRes->data->calculated->outlier_towards)?$apiRes->data->calculated->outlier_towards:0;
        $anomaly_relational_data['start_date_time'] = $startDateTime;
        $anomaly_relational_data['api_status'] = $apiRes->status;
        $ard_id = $this->tbl_generic_model->add('anomaly_relational_data', $anomaly_relational_data);
        unset($anomaly_relational_data);
        $anomaly_json_data['ard_id'] = $ard_id;
        $anomaly_json_data['json_all_data'] = json_encode($apiRes);
        $this->tbl_generic_model->add('anomaly_json_data', $anomaly_json_data);
        unset($anomaly_json_data);
    }

    public function csv(){
        $fileName = 'csv_data_of_TAGID_'.$this->tagId.'.csv';
        $where = array('TAGID' => $this->tagId);
        $this->mongo_db->where($where);
        /*$this->mongo_db->where_gte('DateAndTime', $startDateTime);
        $this->mongo_db->where_lt('DateAndTime', $endDateTime);*/
        $data = $this->mongo_db->get($this->table[1]);
        if(count($data) > 0){
            $retData[0] = array(
                    'DateAndTime' => 'DateAndTime', 
                    'TAGID' => 'TAGID', 
                    'TagValue' => 'TagValue',
                    'TagDescription' => 'TagDescription'
                );
            $i = 1;
            foreach ($data as $doc) {
                $retData[$i] = array(
                        'DateAndTime' => date('Y-m-d H:i:s', strtotime($doc['DateAndTime'])), 
                        'TAGID' => $doc['TAGID'], 
                        'TagValue' => $doc['TagValue'],
                        'TagDescription' => $this->tagDesc
                    );
                $i++;
            }
            unset($data);
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File Transfer');
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename={$fileName}");
            header("Expires: 0");
            header("Pragma: public");
            $fh = @fopen( 'php://output', 'w' );
            $headerDisplayed = false;
            foreach ( $retData as $data ) {
                // Add a header row if it hasn't been added yet
                if ( $headerDisplayed ) {
                    // Use the keys from $data as the titles
                    fputcsv($fh, $data);
                    $headerDisplayed = true;
                }
                // Put the data into the stream
                fputcsv($fh, $data);
            }
            // Close the file
            fclose($fh);
            // Make sure nothing else is sent, our file is done
            exit;
        }
        
    }

    
}