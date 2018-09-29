<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Animal_master_model extends CI_Model {
    

    function __construct() {
        parent::__construct();
    }

    public function getAllData(){
        $this->db->select('AM.*, AMD.*, GROUP_CONCAT(ACMD.acmd_name SEPARATOR ",") all_cat, AMI.ami_path default_image, DATEDIFF(NOW(), AM.am_created_date) days, UM.name user_name, UM.email, UM.mobile ');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id');
        $this->db->join('animal_category_relation ACR','ACR.am_id=AM.am_id','LEFT');
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id=ACR.acm_id AND ACMD.language='en'",'LEFT');
        $this->db->join('animal_master_images AMI','AMI.am_id=AM.am_id AND ami_default = 1','LEFT');
        $this->db->join('user_master UM', "UM.user_id=AM.user_id", 'LEFT');
        //$this->db->where('AM.am_deleted','0');
        //$this->db->where('AMD.language','en');
        $this->db->group_by('AM.am_id');
        return $this->db->get()->result();
    }

    public function getDataTableTotalCount($where = array()){
        $this->db->select('AM.*');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD',"AMD.am_id=AM.am_id");
        $this->db->where($where);
        $this->db->group_by('AM.am_id');
        /*$this->db->get();
        echo $this->db->last_query();
        exit;*/
        return $this->db->count_all_results();
    }

    public function getDataTableFilteredCount($searchData = '', $where = array()){
        $this->db->select('AM.*');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id');
        $this->db->join('animal_category_relation ACR','ACR.am_id=AM.am_id','LEFT');
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id=ACR.acm_id AND ACMD.language='en'",'LEFT');
        $this->db->join('animal_master_images AMI','AMI.am_id=AM.am_id AND ami_default = 1','LEFT');
        $this->db->join('user_master UM', "UM.user_id=AM.user_id", 'LEFT');
        $this->db->where($where);
        $this->_setSearchCond($searchData);
        $this->db->group_by('AM.am_id');
        return $this->db->count_all_results();
    }

    private function _setSearchCond($searchData = ''){
        if($searchData != ''){
            $where = "(AM.am_code LIKE '%".$searchData."%' 
                        OR AM.am_viewed_count LIKE '%".$searchData."%'
                        OR AM.am_status LIKE '%".$searchData."%'
                        OR AM.am_created_date LIKE '%".$searchData."%'
                        OR AMD.amd_name LIKE '%".$searchData."%' 
                        OR AMD.amd_price LIKE '%".$searchData."%'
                        OR UM.name LIKE '%".$searchData."%'
                        OR UM.email LIKE '%".$searchData."%'
                        OR UM.mobile LIKE '%".$searchData."%'
                    )";
            $this->db->where($where);
        }  
    }

    public function getDataTableData($searchData = '', $where = array(), $orderBy = array(), $limit = array()){
        $this->db->select('AM.*, AMD.*, GROUP_CONCAT(ACMD.acmd_name SEPARATOR ",") all_cat, AMI.ami_path default_image, DATEDIFF(NOW(), AM.am_created_date) days, UM.name user_name, UM.email, UM.mobile ');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id');
        $this->db->join('animal_category_relation ACR','ACR.am_id=AM.am_id','LEFT');
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id=ACR.acm_id AND ACMD.language='en'",'LEFT');
        $this->db->join('animal_master_images AMI','AMI.am_id=AM.am_id AND ami_default = 1','LEFT');
        $this->db->join('user_master UM', "UM.user_id=AM.user_id", 'LEFT');
        $this->db->where($where);
        $this->_setSearchCond($searchData);
        $this->db->group_by('AM.am_id');
        $this->db->order_by($orderBy['col'], $orderBy['val']);
        $this->db->limit($limit['perpage'], $limit['start']);
        /*$this->db->get();
        echo $this->db->last_query();
        exit;*/
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
        $this->db->select('AM.*, AMD.*, LANG.lang_name, GROUP_CONCAT( DISTINCT ACR.acm_id SEPARATOR ",") all_cat, AL.*');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','INNER');
        $this->db->join('language LANG','LANG.language = AMD.language','INNER');
        $this->db->join('animal_category_relation ACR','ACR.am_id=AM.am_id','LEFT');
        $this->db->join('animal_location AL','AL.am_id=AM.am_id','LEFT');
        $this->db->where('AM.am_deleted','0');
        $this->db->where('AM.am_id', $am_id);
        $this->db->group_by('AMD.amd_id');
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
        if($acm_id > 0){
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
        if($am_id > 0){
            $this->db->where('AMI.am_id', $am_id);
        }
        $this->db->order_by('AMI.ami_id', 'DESC');
        return $this->db->get()->result();
    }

    public function setDefaultImage($am_id =0, $ami_id = 0){
        $sql = "UPDATE `animal_master_images` SET `ami_default`=IF(ami_id =".$ami_id.",1,0) WHERE 1 AND am_id=".$am_id;
        $this->db->query($sql);
        return true;
    }


    public function changeStatus($am_id = 0){
        $sql = "UPDATE `animal_master` SET `am_status`=IF(am_status ='active','inactive','active') WHERE 1 AND am_id=".$am_id;
        $this->db->query($sql);
        return true;
    }

    public function getCountryList(){
        $this->db->select('*');
        $this->db->from('countries');
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result();
    }

    public function getStateList($country_id = 0){
        $this->db->select('*');
        $this->db->from('states');
        $this->db->where('country_id', $country_id);
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result();
    }

    public function getCityList($state_id = 0){
        $this->db->select('*');
        $this->db->from('cities');
        $this->db->where('state_id', $state_id);
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result();
    }

    
   
}
