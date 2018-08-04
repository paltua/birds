<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Csv_to_location_device_tag_insert extends MY_Controller 
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

    public function index(){
        $filename = 'csv/TF_tag_device_location_v1.csv';
        $this->_parse($filename);
        $this->_addData();
        /*echo "<pre>";
        print_r($this->_dataArray);
        exit;*/
    }

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

    private function _addData(){
    	if(count($this->_dataArray) > 0){
	    	foreach ($this->_dataArray as $key => $value){
	    		if ($key != 0) {
	    			$loc_id = $this->_addLocation($value[5]);
		    		if($loc_id > 0){
		    			$device_id = $this->_addDevice($value[4], $value[3]);
                        $this->_addRelLocationDevice($loc_id, $device_id);
		    			if($device_id > 0){
		    				$tag_id = $this->_addTag($value[0],$value[2],$value[1],$value[6]);
		    				echo $key."==>> TAGID = ".$value[0]." :: Tag Name = ".$value[1]." :: Tag Des = ".$value[2]." :: Device name = ".$value[4]." :: Location = ".$value[5]." <br>";
		    				if($tag_id > 0){
		    					$this->_addRelDeviceTag($device_id, $tag_id);
		    				}
		    			}
		    		}
	    		}
	    	}
	    }
    }
    

    private function _addLocation($loc_name = ''){
    	$returnData = 0;
    	if($loc_name != ''){
    		$returnData = $this->_getId('master_location', 'loc_id', 'loc_name', $loc_name);
    		if($returnData == 0){
    			/*$sql = "INSERT INTO master_location (loc_name, created_at) 
    					VALUES ('".$loc_name."','".date('Y-m-d H:i:s')."')";*/
                $tbl = "master_location" ;
                $data["loc_name"] = $loc_name;
                $data["created_at"] = date('Y-m-d H:i:s');    
    			$returnData = $this->tbl_generic_model->add($tbl, $data);		
    		}
    	}
    	return $returnData;
    }

    private function _addDevice($device_name = '', $wil_device_id = 0){
    	$returnData = 0;
    	if($device_name != ''){
    		$returnData = $this->_getId('master_device', 'device_id', 'device_name', $device_name);
    		if($returnData == 0){
    			/*$sql = "INSERT INTO master_device (device_name, created_at) 
    					VALUES ('".$device_name."','".date('Y-m-d H:i:s')."')";
    			$returnData = $this->insertQuery($sql);	*/
                $tbl = "master_device" ;
                $data["device_name"] = $device_name;
                $data["wil_device_id"] = $wil_device_id;
                $data["created_at"] = date('Y-m-d H:i:s');    
                $returnData = $this->tbl_generic_model->add($tbl, $data);	
    		}
    	}
    	return $returnData;
    }


    private function _addRelLocationDevice($loc_id = 0, $device_id = 0){
    	if($loc_id > 0 && $device_id > 0){
            $retData = $this->_checkExistLocationDevice($loc_id, $device_id);
            if($retData == 0){
        		/*$sql = "INSERT INTO relation_location_device (loc_id, device_id) 
        					VALUES ('".$loc_id."','".$device_id."')";
        		$this->insertQuery($sql);*/	
                $tbl = "relation_location_device" ;
                $data["loc_id"] = $loc_id;
                $data["device_id"] = $device_id; 
                $returnData = $this->tbl_generic_model->add($tbl, $data);   
            }
    	}
    	return true;
    }

    private function _addTag($tag_id = 0, $tag_name = '', $tag_des = '', $short_name = ''){
    	$returnData = 0;
    	if($tag_id  > 0 && $tag_name != '' && $tag_des != ''){
            $returnData = $this->_getId('master_tag', 'tag_id', 'tag_id', $tag_id);
            if($returnData == 0){
    			/*$sql = "INSERT INTO master_tag (tag_id, tag_name, short_name, tag_desc, created_at) 
    					VALUES (".$tag_id.", '".$tag_name."', '".$short_name."', '".$tag_des."', '".date('Y-m-d H:i:s')."')";
    			$returnData = $this->insertQuery($sql);*/	
                $tbl = "master_tag" ;
                $data["tag_id"] = $tag_id;
                $data["tag_name"] = $tag_name; 
                $data["tag_desc"] = $tag_des;
                $data["short_name"] = $short_name;
                $data["created_at"] = date('Y-m-d H:i:s');  
                $returnData = $this->tbl_generic_model->add($tbl, $data);
            }
    	}
    	return $returnData;
    }

    private function _addRelDeviceTag($device_id = 0, $tag_id = 0){
    	if($device_id > 0 && $tag_id > 0){
            $retData = $this->_checkExistDeviceTag($device_id , $tag_id );
            if($retData == 0){
        		/*$sql = "INSERT INTO relation_device_tag (device_id, tag_id) 
        					VALUES ('".$device_id."','".$tag_id."')";
        		$this->insertQuery($sql);*/	
                $tbl = "relation_device_tag" ;
                $data["tag_id"] = $tag_id;
                $data["device_id"] = $device_id; 
                $returnData = $this->tbl_generic_model->add($tbl, $data);
            }
    	}
    	return true;
    }


    private function _getId($tblName = '', $idName = '', $fieldName = '', $searchText = ''){
        $retData = 0;
        $searchText = trim($searchText);
        if($tblName != '' && $idName != '' && $fieldName != '' && $searchText != ''){
            $sql = "SELECT `".$idName."` FROM `".$tblName."` WHERE `".$fieldName."` = '".$searchText."'";
            $rtm = $this->tbl_generic_model->ExecuteQuery($sql);
            if(count($rtm) > 0){
                $retData = $rtm[0]->{$idName};
            }
        }
        return $retData;
    }

    private function _checkExistDeviceTag($device_id = 0, $tag_id = 0){
        $retData = 0;
        $sql = "SELECT count(rdt_id) total FROM `relation_device_tag` WHERE `device_id` = '".$device_id."' AND `tag_id` = '".$tag_id."'";
        $rtm = $this->tbl_generic_model->ExecuteQuery($sql);
        if(count($rtm) > 0){
            $retData = $rtm[0]->total;
        }
        return $retData ;
    }

    private function _checkExistLocationDevice($loc_id = 0, $device_id = 0){
        $retData = 0;
        $sql = "SELECT count(rlm_id) total FROM `relation_location_device` WHERE `device_id` = '".$device_id."' AND `loc_id` = '".$loc_id."'";
        $rtm = $this->tbl_generic_model->ExecuteQuery($sql);
        if(count($rtm) > 0){
            $retData = $rtm[0]->total;
        }
        return $retData ;
    }

}