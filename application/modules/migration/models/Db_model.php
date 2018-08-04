<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Db_model extends CI_Model {
    private $fm_db;
    private $air_db;
    private $ems_cpp;
    private $welspun_datalog;
    function __construct() {
        parent::__construct();
		//log_message('INFO', 'account/models/Dashboard_model enter');
        //$this->fm_db = $this->load->database('welspun_fm', TRUE);
        //$this->air_db = $this->load->database('welspun_air', TRUE);
        //$this->ems_cpp = $this->load->database('welspun_ems_cpp', TRUE);
        //$this->welspun_datalog = $this->load->database('welspun_datalog', TRUE);
    }

    public function checkDevice($device_id = ''){
        $sql = 'SELECT count(device_id) total FROM master_device MD WHERE MD.device_id ='.$device_id;
        return $this->db->query($sql)->result();
    }

    public function checkTag($tag_id = ''){
        $sql = 'SELECT count(tag_id) total FROM master_tag MT WHERE MT.tag_id ='.$tag_id;
        return $this->db->query($sql)->result();
    }

    public function checkDeviceTag($device_id = '', $tag_id = ''){
        $sql = 'SELECT count(rdt_id) total 
        FROM relation_device_tag  WHERE 1 AND tag_id ='.$tag_id.' AND device_id = '.$device_id;
        return $this->db->query($sql)->result();
    }

    
    
}
