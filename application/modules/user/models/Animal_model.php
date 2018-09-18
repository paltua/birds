<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Animal_model extends CI_Model {	
	public function __construct() {
		parent::__construct();
		log_message('INFO', 'Animal_model enter');
	}

	public function getMyListing(){
        $this->db->select('AM.*, AMD.*, GROUP_CONCAT(ACMD.acmd_name SEPARATOR ",") all_cat, AMI.ami_path default_image,CONT.name country_name, ST.name state_name, CT.name city_name');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','INNER');
        $this->db->join('animal_category_relation ACR','ACR.am_id=AM.am_id','LEFT');
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id=ACR.acm_id AND ACMD.language='en'",'LEFT');
        $this->db->join('animal_master_images AMI','AMI.am_id=AM.am_id AND ami_default = 1','LEFT');
        $this->db->join('animal_location AL','AL.am_id=AM.am_id','LEFT');
        $this->db->join('countries CONT','CONT.id=AL.country_id','LEFT');
        $this->db->join('states ST','ST.id=AL.state_id','LEFT');
        $this->db->join('cities CT','CT.id=AL.city_id','LEFT');
        $this->db->where('AM.am_deleted','0');
        $this->db->where('AMD.language','en');
        $this->db->where('AM.user_id', $this->session->userdata('user_id'));
        $this->db->group_by('AM.am_id');
        return $this->db->get()->result();
        /*$this->db->get();
        echo $this->db->last_query();
        exit;*/
    }

    private function _search($search = array()){
        if($search['keyWord'] != ''){
            $keyWordSearch = "( AMD.amd_name LIKE '%".$search['keyWord']."%'
                                OR AMD.amd_short_desc LIKE '%".$search['keyWord']."%'
                        )";
            $this->db->where($keyWordSearch);            
        }
        if($search['country_id'] > 0){
            $this->db->where('CONT.id', $search['country_id']);
        }
        if($search['state_id'] > 0){
            $this->db->where('ST.id', $search['state_id']);
        }
        if(is_array($search['city_id'])){
            $this->db->where_in('CT.id', $search['city_id']);
        }
        if($search['price']['min_select'] > 0){
            $this->db->where('AMD.amd_price >= ', $search['price']['min_select']);
        }
        if($search['price']['max_select'] > 0){
            $this->db->where('AMD.amd_price <= ', $search['price']['max_select']);
        }
    }

    public function getProductListComp($cat_id = 0){
        $this->db->select('AM.*, AMD.*, CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, AMI.ami_path, ACMD.acmd_name,UM.name user_name,UM.um_created_date,UM.email, UM.mobile,CONT.name country_name, ST.name state_name, CT.name city_name');
        $this->db->from('animal_category_relation ACR');
        $this->db->join('animal_master AM',"AM.am_id = ACR.am_id AND AM.am_status = 'active' AND AM.am_deleted = '0'");
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','LEFT');
        $this->db->join('animal_master_images AMI','AMI.am_id=ACR.am_id AND AMI.ami_default = 1','LEFT');
        //$this->db->join('user_master UM', "UM.user_id=AM.user_id AND UM.um_status = 'active' AND UM.um_deleted = '0'", 'LEFT');
        $this->db->join('user_master UM', "UM.user_id=AM.user_id", 'LEFT');
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id = ACR.acm_id AND ACMD.language = 'en'");
        $this->db->join('animal_location AL','AL.am_id=AM.am_id','LEFT');
        $this->db->join('countries CONT','CONT.id=AL.country_id','LEFT');
        $this->db->join('states ST','ST.id=AL.state_id','LEFT');
        $this->db->join('cities CT','CT.id=AL.city_id','LEFT');
        if($cat_id > 0){
            $this->db->where('ACR.acm_id', $cat_id);
        }
        $this->db->where('AM.am_user_type','admin');
        $this->db->where('AMD.language','en');
        /*$this->db->get();
        echo $this->db->last_query();
        exit;*/
        return $this->db->get()->result();
    }

    public function getProductListUser($cat_id = 0){
        $this->db->select('AM.*, AMD.*, CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, AMI.ami_path, ACMD.acmd_name,UM.name user_name,UM.um_created_date,UM.email, UM.mobile,CONT.name country_name, ST.name state_name, CT.name city_name');
        $this->db->from('animal_category_relation ACR');
        $this->db->join('animal_master AM',"AM.am_id = ACR.am_id AND AM.am_status = 'active' AND AM.am_deleted = '0'");
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','LEFT');
        $this->db->join('animal_master_images AMI','AMI.am_id=ACR.am_id AND AMI.ami_default = 1','LEFT');
        //$this->db->join('user_master UM', "UM.user_id=AM.user_id AND UM.um_status = 'active' AND UM.um_deleted = '0'", 'LEFT');
        $this->db->join('user_master UM', "UM.user_id=AM.user_id", 'LEFT');
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id = ACR.acm_id AND ACMD.language = 'en'");
        $this->db->join('animal_location AL','AL.am_id=AM.am_id','LEFT');
        $this->db->join('countries CONT','CONT.id=AL.country_id','LEFT');
        $this->db->join('states ST','ST.id=AL.state_id','LEFT');
        $this->db->join('cities CT','CT.id=AL.city_id','LEFT');
        if($cat_id > 0){
            $this->db->where('ACR.acm_id', $cat_id);
        }
        $this->db->where('AM.am_user_type','user');
        $this->db->where('AMD.language','en');
        /*$this->db->get();
        echo $this->db->last_query();
        exit;*/
        return $this->db->get()->result();
    }

    public function getMinMaxPrice(){
        $this->db->select('MAX(AMD.amd_price) max_price, MIN(AMD.amd_price) min_price', false);
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','LEFT');
        $this->db->where('AM.am_status','active');
        $this->db->where('AM.am_deleted','0');
        return $this->db->get()->result();
    }

    public function getProductDetails($am_id = 0){
        $this->db->select('AM.*, AMD.*,CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price,UM.name user_name,UM.um_created_date,UM.email, UM.mobile, ACMD.acmd_name');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','LEFT');
        $this->db->join('user_master UM', "UM.user_id=AM.user_id AND UM.um_status = 'active' AND UM.um_deleted = '0'", 'LEFT');
        $this->db->join('animal_category_relation ACR','ACR.am_id = AM.am_id','LEFT');
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id = ACR.acm_id AND ACMD.language = 'en'");
        $this->db->where('AM.am_id', $am_id);
        $this->db->where('AM.am_status', 'active');
        $this->db->where('AM.am_deleted', '0');
        return $this->db->get()->result();
    }


    public function getProductImages($am_id = 0){
        $this->db->select('AMI.*');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_images AMI','AMI.am_id=AM.am_id ','LEFT');
        $this->db->where('AM.am_id', $am_id);
        $this->db->where('AM.am_status', 'active');
        $this->db->where('AM.am_deleted', '0');
        $this->db->order_by('AMI.ami_default','DESC');
        return $this->db->get()->result();
    }

    public function getCommentList($am_id = 0){
        $this->db->select('COM.*,UM.name');
        $this->db->from('comments COM');
        $this->db->join('user_master UM',"UM.user_id=COM.user_id AND UM.um_status='active' AND UM.um_deleted = '0'");
        $this->db->where('COM.am_id', $am_id);
        $this->db->where('COM.com_status', 'active');
        $this->db->order_by('COM.created_date','ASC');
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

    public function getEditData($am_id = 0){
        $this->db->select('AM.*, AMD.*,ACR.acm_id, AL.*');
        $this->db->from('animal_master AM');
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','INNER');
        $this->db->join('animal_category_relation ACR','ACR.am_id=AM.am_id','LEFT');
        $this->db->join('animal_location AL','AL.am_id=AM.am_id','LEFT');
        $this->db->where('AM.am_deleted','0');
        $this->db->where('AMD.language','en');
        $this->db->where('AM.am_id', $am_id);
        $this->db->where('AM.user_id', $this->session->userdata('user_id'));
        return $this->db->get()->result();
        /*$this->db->get();
        echo $this->db->last_query();
        exit;*/
    }

	

    
}