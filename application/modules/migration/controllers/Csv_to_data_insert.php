<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Csv_to_data_insert extends MY_Controller 
{
    private $_headerArray ;
    private $_dataArray ;
    /*
     * Constructor
    */
    public function __construct(){
        parent::__construct();
        $this->_dataArray = array();
        $this->_headerArray = array();
        $this->load->model('db_model');
    }
    
    public function addAll(){

    }

    /*
    * function addEmsKwh()
    * This function is used to insert KWH data of EMS in database
    */
    public function addEmsKwh() {
        $filename = 'csv/KWH_TAG_ID_12_12_2017.csv';
        $this->_parse($filename);
        if(count($this->_dataArray) > 1){
            $this->masterTagStr = array();
            $this->deviceTagStr = array();
            $this->updateStr = '';
            $date = date('Y-m-d H:i:s');
            foreach ($this->_dataArray as $key => $value) {
                if($key > 0){
                    if($value[0] > 0 && $value[2] > 0){
                        $chDeviceTag = $this->db_model->checkTag($value[2]);
                        if($chDeviceTag[0]->total == 0){
                            $this->masterTagStr[] = "('".$value[2]."','".$value[3]."','KWH','".$value[4]."','".$date."')";
                            $this->deviceTagStr[] = "('".$value[0]."','".$value[2]."')";
                            $this->updateStr .= "UPDATE `master_device` SET `wil_device_id`= ".$value[5]." WHERE 1 AND `device_id`=".$value[0].";";
                        }
                    }
                }
            }
            $this->_addMasterTag();
            $this->_addDeviceTag();
            $this->_addWilDeviceId();
        }
        //print_r($this->_dataArray);
    }
    /*
    * Parsed CSV file
    */
    private function _parse($fileName = ''){
        $csvFile = $fileName;
        $file_handle = fopen($csvFile, 'r');
        $arr = array();
        while (!feof($file_handle) ) {
            $arr[] = fgetcsv($file_handle, 1024);
        }
        fclose($file_handle);
        $this->_headerArray = $arr[0];
        $this->_dataArray = $arr;
        unset($arr);
    }

    private function _tagExist($tag_id = ''){

    }

    private function _addMasterTag(){
        $sql = '';
        if($this->masterTagStr != ''){
            $sql = "INSERT INTO `master_tag` (`tag_id`,`tag_name`,`short_name`,`tag_desc`,`created_at`) 
            VALUES ".implode(',', $this->masterTagStr).";";
        }
        echo $sql;
    }

    private function _addDeviceTag(){
        $sql = '';
        if($this->deviceTagStr != ''){
            $sql = "INSERT INTO `relation_device_tag` (`device_id`,`tag_id`)  
            VALUES ".implode(',', $this->deviceTagStr).";";
        }
        echo $sql;
    }

    private function _addWilDeviceId(){
        echo $this->updateStr;
    }

    public function getAllTags(){
        $filename = 'csv/TF_tag_device_location_v1.csv';
        $this->_parse($filename);
        $tagArr = array();
        //print_r($this->_dataArray);
        if(count($this->_dataArray) > 1){
            foreach ($this->_dataArray as $key => $value) {
                if($key > 0){
                    if($value[0] > 0 ){
                        $tagArr[$value[0]] = (int)$value[0];
                    }
                }
            }
        }
        //echo count($tagArr)."<br>";
        echo implode(',', $tagArr);

    }


    
    
}