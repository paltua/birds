<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron_send_reg_req_model extends CI_Model { 

	function __construct() {
		parent::__construct();
		log_message('INFO', 'Cron_send_reg_req_model enter');
	}

	public function getDetails(){
		$this->db->select('*');
		$this->db->form('user_master');
		$this->db->where('user_id ', 704);
		return $this->db->get()->result();
	}

}