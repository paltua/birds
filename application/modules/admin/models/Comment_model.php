<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment_model extends CI_Model { 

	function __construct() {
		parent::__construct();
		log_message('INFO', 'Comment_model enter');
	}

    public function changeStatus($com_id = 0){
        $sql = "UPDATE `comments` SET `com_status`=IF(com_status ='active','inactive','active') WHERE 1 AND com_id=".$com_id;
        $this->db->query($sql);
        return true;
    }

    public function getDataTableTotalCount($where = array()){
        $this->db->from('comments CM');
        $this->db->join('user_master UM','UM.user_id=CM.user_id');
        $this->db->join('animal_master AM','AM.am_id = CM.am_id');
        $this->db->where($where);
        /*$this->db->get();
        echo $this->db->last_query();
        exit;*/
        return $this->db->count_all_results();
    }

    public function getDataTableFilteredCount($searchData = '', $where = array()){
        $this->db->from('comments CM');
        $this->db->join('user_master UM','UM.user_id=CM.user_id');
        $this->db->join('animal_master AM','AM.am_id = CM.am_id');
        $this->db->where($where);
        $this->_setSearchCond($searchData);
        return $this->db->count_all_results();
    }

    private function _setSearchCond($searchData = ''){
        if($searchData != ''){
            $where = "(CM.comments LIKE '%".$searchData."%' 
                        OR UM.name LIKE '%".$searchData."%' 
                        OR AM.am_code LIKE '%".$searchData."%'
                        OR CM.com_status LIKE '%".$searchData."%'
                        OR CM.created_date LIKE '%".$searchData."%'
                    )";
            $this->db->where($where);
        }  
    }

    public function getDataTableData($searchData = '', $where = array(), $orderBy = array(), $limit = array()){
        $this->db->select('CM.*, UM.name, AM.am_code');
        $this->db->from('comments CM');
        $this->db->join('user_master UM','UM.user_id=CM.user_id');
        $this->db->join('animal_master AM','AM.am_id = CM.am_id');
        $this->db->where($where);
        $this->_setSearchCond($searchData);
        $this->db->order_by($orderBy['col'], $orderBy['val']);
        $this->db->limit($limit['perpage'], $limit['start']);
        /*$this->db->get();
        echo $this->db->last_query();
        exit;*/
        return $this->db->get()->result();
    }

    
}