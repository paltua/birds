<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Animal_master_model extends CI_Model {
    

    function __construct() {
        parent::__construct();
    }

    public function getAllData(){
        $this->db->select('*');
        $this->db->from('animal_category_master ACM');
        $this->db->join('animal_category_master_details ACMD','ACMD.acm_id=ACM.acm_id','INNER');
        $this->db->where('ACM.acm_is_deleted','0');
        $this->db->where('ACMD.language','en');
        return $this->db->get()->result();
    }

    public function check_name_url($str = ''){
        $this->db->select('acm_id');
        $this->db->where('acm_url_name', $str);
        return $this->db->from('animal_category_master')->count_all_results();
    }

    public function getSingle($acm_id = 0){
        $this->db->select('ACMD.*,LANG.lang_name ');
        $this->db->from('animal_category_master ACM');
        $this->db->join('animal_category_master_details ACMD','ACMD.acm_id=ACM.acm_id','INNER');
        $this->db->join('language LANG','LANG.language = ACMD.language');
        $this->db->where('ACM.acm_is_deleted','0');
        $this->db->where('ACM.acm_id', $acm_id);
        $this->db->order_by('LANG.lang_id', 'ASC');
        // $this->db->get();
        // echo $this->db->last_query();
        // exit();
        return $this->db->get()->result();
    }

    
   
}
