<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact_us_model extends CI_Model { 

	function __construct() {
		parent::__construct();
		log_message('INFO', 'Contact_us_model enter');
	}

    public function listing(){
        $this->db->select('*');
        $this->db->from('contact_us CM');
        $this->db->order_by('CM.con_id','DESC');
        $query = $this->db->get();
        return $query->result();
    }

    
}