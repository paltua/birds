<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends CI_Model {	
	public function __construct() {
		parent::__construct();
		log_message('INFO', 'Product_model enter');
	}

	public function getProductList($cat_id = 0){
        $this->db->select('AM.*, AMD.*, CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, AMI.ami_path, ACMD.acmd_name,UM.name user_name,UM.um_created_date,CONT.name country_name, ST.name state_name, CT.name city_name');
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
        $this->db->select('AM.*, AMD.*,CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, ACMD.acmd_name');
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

	

    
}