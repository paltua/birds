<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TagDetails extends MY_Controller {
	private $_dataArray;
    private $_headerArray;

    public function __construct(){
    	parent::__construct();
    	$this->_dataArray = array();
		$this->_headerArray = array();
    }

	public function index(){
		$fileName = base_url()."csv/meter_tag_location_device.csv";
		$this->_parse($fileName);
		/*print_r($this->_dataArray);
		exit;*/
		$this->_addData();
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
    		$this->_truncateTable();
    		$insertData = array();
	    	foreach ($this->_dataArray as $key => $value){
	    		if ($key != 0) {
	    			$loc_id = $this->_addLocation($value[7]);
		    		if($loc_id > 0){
		    			$device_id = $this->_addDevice($value[6]);
		    			if($device_id > 0){
		    				$tag_id = $this->_addTag($value[0],$value[4],$value[5]);
		    				echo "TAGID = ".$value[0]." :: Tag Name = ".$value[4]." :: Tag Des = ".$value[5]." <br>";
		    				if($tag_id > 0){
		    					$this->_addRelDeviceTag($device_id, $tag_id);
                                if($this->_checkExistDeviceLoc($loc_id, $device_id)){
                                    $this->_addRelLocationDevice($loc_id, $device_id);
                                }
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
    			$data['loc_name'] = $loc_name;
	    		$data['created_at'] = date('Y-m-d H:i:s');	
	    		$returnData = $this->tbl_generic_model->add('master_location',$data);		
    		}
    	}
    	return $returnData;
    }


    private function _addDevice($device_name = ''){
    	$returnData = 0;
    	if($device_name != ''){
    		$returnData = $this->_getId('master_device', 'device_id', 'device_name', $device_name);
    		if($returnData == 0){
    			$data['device_name'] = $device_name;
	    		$data['created_at'] = date('Y-m-d H:i:s');	
	    		$returnData = $this->tbl_generic_model->add('master_device',$data);		
    		}
    	}
    	return $returnData;
    }


    private function _addRelLocationDevice($loc_id = 0, $device_id = 0){
    	if($loc_id > 0 && $device_id > 0){
    		$data['loc_id'] = $loc_id;
    		$data['device_id'] = $device_id;	
    		$this->tbl_generic_model->add('relation_location_device',$data);	
    	}
    	return true;
    }

    private function _addTag($tag_id = 0, $tag_name = '', $tag_des = ''){
    	$returnData = 0;
    	if($tag_id  > 0 && $tag_name != '' && $tag_des != ''){
			$data['tag_id'] = $tag_id;
			$data['tag_name'] = $tag_name;
    		$data['tag_desc'] = $tag_des;
            $data['logic'] = rand(0,2);	
    		$data['created_at'] = date('Y-m-d H:i:s');	
    		$returnData = $this->tbl_generic_model->add('master_tag',$data);
    	}
    	return $returnData;
    }

    private function _addRelDeviceTag($device_id = 0, $tag_id = 0){
    	if($device_id > 0 && $tag_id > 0){
    		$data['device_id'] = $device_id;
    		$data['tag_id'] = $tag_id;	
    		$this->tbl_generic_model->add('relation_device_tag',$data);
    	}
    	return true;
    }

    private function _checkExistDeviceLoc($loc_id = 0, $device_id = 0){
        $retData = true;
        $rtm = $this->tbl_generic_model->get('relation_location_device', 'rlm_id', array('loc_id' => $loc_id, 'device_id' => $device_id));
        if(count($rtm) > 0){
            $retData = false;
        }
        return $retData;
    }


    private function _getId($tblName = '', $idName = '', $fieldName = '', $searchText = ''){
        $retData = 0;
        $searchText = trim($searchText);
        if($tblName != '' && $idName != '' && $fieldName != '' && $searchText != ''){
            $rtm = $this->tbl_generic_model->get($tblName, $idName, array($fieldName => $searchText));
            if(count($rtm) > 0){
                $retData = $rtm[0]->{$idName};
            }
        }
        return $retData;
    }

    private function _truncateTable(){
    	$this->tbl_generic_model->truncate('master_tag');
    	$this->tbl_generic_model->truncate('relation_device_tag');
    	$this->tbl_generic_model->truncate('master_device');
        $this->tbl_generic_model->truncate('relation_location_device');
        $this->tbl_generic_model->truncate('master_location');
    }

}
