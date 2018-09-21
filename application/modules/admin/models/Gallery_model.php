<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gallery_model extends CI_Model {
    

    function __construct() {
        parent::__construct();
    }

    public function getAllData(){
        $this->db->select('*');
        $this->db->from('gallery');
        return $this->db->get()->result();
    }

    
   
}
