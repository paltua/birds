<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron_send_reg_req_model extends CI_Model { 

	function __construct() {
		parent::__construct();
		log_message('INFO', 'Cron_send_reg_req_model enter');
	}

	public function getDetails(){
		$this->db->select('*');
		$this->db->from('user_master');
		/*$this->db->where('um_status', 'inactive');
		$this->db->where('um_deleted', '1');*/
		$this->db->where('user_id >= ', '650');
		return $this->db->get()->result();
	}

}