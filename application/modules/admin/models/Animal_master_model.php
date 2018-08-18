<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Animal_master_model extends CI_Model {
    

    function __construct() {
        parent::__construct();
    }

    public function getAllData(){
        $this->db->select('AM.*, AMD.*, GROUP_CONCAT(ACMD.acmd_name SEPARATOR ",") all_cat');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','INNER');
        $this->db->join('animal_category_relation ACR','ACR.am_id=AM.am_id','LEFT');
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id=ACR.acm_id AND ACMD.language='en'",'LEFT');
        $this->db->where('AM.am_deleted','0');
        $this->db->where('AMD.language','en');
        $this->db->group_by('AM.am_id');
        return $this->db->get()->result();
    }

    public function check_name_url($str = '', $am_id = 0){
        $this->db->select('am_id');
        $this->db->where('am_title', $str);
        if($am_id > 0){
            $this->db->where('am_id != ', $am_id);
        }
        return $this->db->from('animal_master')->count_all_results();
    }

    public function getSingle($am_id = 0){
        $this->db->select('AM.*, AMD.*, LANG.lang_name, GROUP_CONCAT(ACR.acm_id SEPARATOR ",") all_cat');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','INNER');
        $this->db->join('language LANG','LANG.language = AMD.language');
        $this->db->join('animal_category_relation ACR','ACR.am_id=AM.am_id','LEFT');
        $this->db->where('AM.am_deleted','0');
        $this->db->where('AM.am_id', $am_id);
        $this->db->where('AMD.language','en');
        $this->db->group_by('AM.am_id');
        // $this->db->get();
        // echo $this->db->last_query();
        // exit();
        return $this->db->get()->result();
    }

    public function getAllAnimalCategory($acm_id = 0){
        $this->db->select('*');
        $this->db->from('animal_category_master ACM');
        $this->db->join('animal_category_master_details ACMD','ACMD.acm_id=ACM.acm_id','INNER');
        $this->db->where('ACM.acm_is_deleted','0');
        $this->db->where('ACMD.language','en');
        If($acm_id > 0){
            $this->db->where('ACM.acm_id', $acm_id);
        }
        $this->db->order_by('ACMD.acmd_name', 'ASC');
        return $this->db->get()->result();
    }

    public function getAllAnimalParentCategory($parent_id = 0){
        $this->db->select('*');
        $this->db->from('animal_category_master ACM');
        $this->db->join('animal_category_master_details ACMD','ACMD.acm_id=ACM.acm_id','INNER');
        $this->db->where('ACM.acm_is_deleted','0');
        $this->db->where('ACM.acm_status','active');
        $this->db->where('ACMD.language','en');
        $this->db->where('ACM.parent_id', $parent_id);
        $this->db->order_by('ACMD.acmd_name', 'ASC');
        return $this->db->get()->result();
    }

    public function getAllAnimalChildCategory($am_id = 0){
        $sql = "SELECT ACM1.*,ACMD.* 
                    FROM   (SELECT ACR.acm_id 
                            FROM   `animal_category_relation` ACR 
                                   JOIN animal_category_master ACM 
                                     ON ACM.acm_id = ACR.`acm_id` 
                            WHERE  1 
                                   AND ACR.`am_id` = ".$am_id." 
                                   AND ACM.parent_id = 0 
                                   AND ACM.acm_is_deleted = '0' 
                                   AND ACM.acm_status = 'active') NewTbl 
                            LEFT JOIN animal_category_master ACM1 
                                  ON ACM1.parent_id = NewTbl.`acm_id` 
                            JOIN animal_category_master_details ACMD ON ACMD.acm_id = ACM1.acm_id      
                    WHERE  1 
                           AND ACM1.acm_is_deleted = '0' 
                           AND ACM1.acm_status = 'active'
                           AND ACMD.language = 'en' ";
        return $this->db->query($sql)->result();                   
    }

    public function getImageList($am_id = 0){
        $this->db->select('*');
        $this->db->from('animal_master_images AMI');
        If($am_id > 0){
            $this->db->where('AMI.am_id', $am_id);
        }
        $this->db->order_by('AMI.ami_id', 'DESC');
        return $this->db->get()->result();
    }

    
   
}
