<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model { 

	function __construct() {
		parent::__construct();
		log_message('INFO', 'User_model enter');
	}

    public function listing(){
        $this->db->select('*');
        $this->db->from('user_master');
        $this->db->where('um_deleted','0');
        $query = $this->db->get();
        return $query->result();
    }	
 
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    function add_user($data){
    	$this->db->insert('user_master', $data); 
    }

    function user_delete($user_id){
        $sql = "UPDATE `user_master` SET `um_deleted` = '1' WHERE 1 AND user_id=".$user_id;
        $this->db->query($sql);
        return true;
    }

    function check_email($email){
        $this->db->where('email', $email);
        $this->db->from('user_master');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function changeStatus($user_id = 0){
        $sql = "UPDATE `user_master` SET `um_status`=IF(um_status ='active','inactive','active'), `um_deleted` = '0' WHERE 1 AND user_id=".$user_id;
        $this->db->query($sql);
        return true;
    }
}