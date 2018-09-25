<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends CI_Model { 

	function __construct() {
		parent::__construct();
		log_message('INFO', 'Dsahboard_model enter');
	}

    public function getTotalUser(){
        $this->db->where('um_deleted', '0');
        $this->db->from('user_master');
        return $this->db->count_all_results();
    }

    public function getTotalComment($com_id = 0){
        $this->db->from('comments');
        return $this->db->count_all_results();
    }

    public function getTotalProduct($com_id = 0){
        $this->db->where('am_deleted', '0');
        $this->db->from('animal_master');
        return $this->db->count_all_results();
    }

    public function getTotalContact($com_id = 0){
        $this->db->from('contact_us');
        return $this->db->count_all_results();
    }

    
}