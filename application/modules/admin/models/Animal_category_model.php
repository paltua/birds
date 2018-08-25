<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Animal_category_model extends CI_Model {
    

    function __construct() {
        parent::__construct();
    }

    public function getAllData(){
        $this->db->select('ACM.*, ACMD.*, ACMD1.acmd_name parent_name');
        $this->db->from('animal_category_master ACM');
        $this->db->join('animal_category_master_details ACMD','ACMD.acm_id=ACM.acm_id','INNER');
        $this->db->join('animal_category_master ACM1','ACM1.acm_id=ACM.parent_id','LEFT');
        $this->db->join('animal_category_master_details ACMD1',"ACMD1.acm_id=ACM1.acm_id AND ACMD1.language='en'",'LEFT');
        $this->db->where('ACM.acm_is_deleted','0');
        $this->db->where('ACMD.language','en');
        return $this->db->get()->result();
    }

    public function check_name_url($str = '', $acm_id = 0){
        $this->db->select('acm_id');
        $this->db->where('acm_url_name', $str);
        if($acm_id > 0){
            $this->db->where('acm_id !=', $acm_id);
        }
        return $this->db->from('animal_category_master')->count_all_results();
    }

    public function getSingle($acm_id = 0){
        $this->db->select('ACM.*, ACMD.*,LANG.lang_name ');
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

    public function getParent($parent_id = 0){
        $this->db->select('ACMD.*,LANG.lang_name ');
        $this->db->from('animal_category_master ACM');
        $this->db->join('animal_category_master_details ACMD','ACMD.acm_id=ACM.acm_id','INNER');
        $this->db->join('language LANG','LANG.language = ACMD.language');
        $this->db->where('ACM.acm_is_deleted','0');
        $this->db->where('ACM.parent_id', $parent_id);
        $this->db->where('ACMD.language', 'en');
        // $this->db->get();
        // echo $this->db->last_query();
        // exit();
        return $this->db->get()->result();
    }

    public function changeStatus($acm_id = 0){
        $sql = "UPDATE `animal_category_master` SET `acm_status`=IF(acm_status ='active','inactive','active') WHERE 1 AND acm_id=".$acm_id;
        $this->db->query($sql);
        return true;
    }

    
   
}
