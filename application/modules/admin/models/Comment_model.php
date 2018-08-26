<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment_model extends CI_Model { 

	function __construct() {
		parent::__construct();
		log_message('INFO', 'Comment_model enter');
	}

    public function listing(){
        $this->db->select('*');
        $this->db->from('comments CM');
        $this->db->join('user_master UM','UM.user_id=CM.user_id');
        $this->db->order_by('CM.com_id','DESC');
        $query = $this->db->get();
        return $query->result();
    }

    
}