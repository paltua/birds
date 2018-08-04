<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AggregatorLogic extends MY_Controller {
	public $tagId = '98,100,101,102,103';
	public $tagDesc = 'Apparent-Power-avg';
	public $tagLogic = 2;
	public $startDateTime = '';
	public $dataTimeInterval = 'PT00H15M00S';
	public function __construct(){
		parent::__construct();
		$this->table = array('ems_cron_history','ems');
        $this->load->library('mongo_db');
	}

	public function index(){
		echo "Start Time = ".date('Y-m-d H:i:s')."==";	
		$tags = $this->_getTag();
		if(count($tags) > 0){
			foreach ($tags as $keyTag => $valueTag) {
				$this->tagId = $valueTag->tag_id;
				$this->tagDesc = url_title($valueTag->tag_desc);
		        //$this->mongo_db->limit(1);
		        //$cronHist = $this->mongo_db->get($this->table[0]);
		        $this->startDateTime = "2017-07-10 00:00:00";
	        	for ($i = 1; $i <= 192; $i++) {
	        		
	        		$valueHist['startDateTime'] = $this->startDateTime ;
	        		$valueHist['endDateTime'] = $this->_dateAdd();
	        		echo $valueHist['startDateTime']."==".$valueHist['endDateTime'].'<br>';
			        $where = array('TAGID' => $this->tagId);
			        $this->mongo_db->where($where);
			        $this->mongo_db->where_gte('DateAndTime', $valueHist['startDateTime']);
			        $this->mongo_db->where_lt('DateAndTime', $valueHist['endDateTime']);
			        $data = $this->mongo_db->get($this->table[1]);
			        $newData = $this->_prepareArray($data);
			        if(count($newData) > 0){
		                $this->tagId = $newData['TAGID'];
		                $this->calculation($newData['aggLogic'], $valueHist['startDateTime']);
			            unset($newData);
			        }
			        $this->startDateTime = 	$valueHist['endDateTime'];			        
			    }
			    
			}
		}
		echo "==End Time = ".date('Y-m-d H:i:s')."==<br>";	
	}


    private function _dateAdd(){
        $date = new DateTime($this->startDateTime);
        $date->add(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

	private function _prepareArray($data = array()){
		$retData = array();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$retData['TAGID'] = $value['TAGID'];
				$retData['aggLogic'][] = $value['TagValue'];
			}
		}
		return $retData;
	}

	public function calculation($newArr = array(), $startDateTime = ''){
		/*if($this->tagLogic == 0){*/
			$retData = array_sum($newArr)/count($newArr);
		/*}elseif ($this->tagLogic == 1) {
			$retData = array_sum($newArr);
		}elseif ($this->tagLogic == 2) {
			$first = reset($newArr);
			$last = end($newArr);
			$retData = $last - $first;
		}*/
		$aggregate_data['tag_id'] = $this->tagId;
		$aggregate_data['data'] = $retData;
		$aggregate_data['start_date_time'] = $startDateTime;
		$this->tbl_generic_model->add('aggregate_data', $aggregate_data);
	}

	public function check(){
		$where = array('TAGID' => $this->tagId);
        $this->mongo_db->where($where);
        $this->mongo_db->where_gte('DateAndTime', '2017-07-10 00:15:00');
        $this->mongo_db->where_lt('DateAndTime', '2017-07-10 00:30:00');
        $data = $this->mongo_db->get($this->table[1]);
        $newData = $this->_prepareArray($data);
        print_r($newData);
	}


	private function _getTag(){
		$table = "master_tag";
		$fields = "*";
		$where = array('tag_id' => 97);
		$orderby = array('tag_id' => 'ASC') ;
		$perpage = 10;
		$retData = $this->tbl_generic_model->get($table, $fields, $where, $orderby, $perpage);
		return $retData;
		/*count($retData);
		print_r($retData);*/
	}

	
}