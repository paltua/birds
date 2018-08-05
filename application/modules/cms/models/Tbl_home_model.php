<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tbl_home_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    
    public function get_count($tbl = '',$search = array(),$perpage,$start){
        $this->db->select('CM.client_id, CM.name client_name, CM.motors, CM.client_status, CM.subscription_status, UM.name user_name, UM.email');
        $this->db->from('client_master CM');
        $this->db->join('web_user_master UM','UM.client_id = CM.client_id');
        if(count($search) > 0){
            $this->db->where($search);
        }
        $this->db->order_by('CM.client_id','DESC');
        $this->db->limit($perpage,$start);
        $query = $this->db->get();
        return $query->result();
    }
    /*
    * function getTotalAccount()
    * This is used to get total account
    * @return : INT
    */
    public function getTotalAccount(){
        $this->db->select('count(org_id) total');
        $this->db->from('org_parent_child_relation OPCH');
        $this->db->where('OPCH.parent_org_id',0);
        $query = $this->db->get()->result();
        return $query[0]->total;
    }
}
