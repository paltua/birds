<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends CI_Model {	
	public function __construct() {
		parent::__construct();
		log_message('INFO', 'Product_model enter');
	}

    
    public function getCountAll($cat_id = 0, $search = array()){
        $this->db->select('ACR.am_id');
        $this->db->from('animal_category_relation ACR');
        $this->db->join('animal_master AM',"AM.am_id = ACR.am_id AND AM.am_status = 'active' AND AM.am_deleted = '0'");
        $this->db->where('AM.am_deleted','0');
        $this->db->group_by('ACR.am_id');
        /*$this->db->get();
        echo $this->db->last_query();
        exit;*/
        return $this->db->count_all_results();
    }

    public function getProductCountAll($cat_id = 0, $search = array()){
        $this->db->select('ACR.am_id');
        $this->db->from('animal_category_relation ACR');
        $this->db->join('animal_master AM',"AM.am_id = ACR.am_id AND AM.am_status = 'active' AND AM.am_deleted = '0'");
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','LEFT');
        $this->db->join('animal_master_images AMI','AMI.am_id=ACR.am_id AND AMI.ami_default = 1','LEFT');
        //$this->db->join('user_master UM', "UM.user_id=AM.user_id AND UM.um_status = 'active' AND UM.um_deleted = '0'", 'LEFT');
        $this->db->join('user_master UM', "UM.user_id=AM.user_id", 'LEFT');
        $this->db->join('animal_category_master ACM',"ACM.acm_id = ACR.acm_id AND ACM.parent_id > 0");
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id = ACR.acm_id AND ACMD.language = 'en'");
        $this->db->join('animal_location AL','AL.am_id=AM.am_id','LEFT');
        $this->db->join('countries CONT','CONT.id=AL.country_id','LEFT');
        $this->db->join('states ST','ST.id=AL.state_id','LEFT');
        $this->db->join('cities CT','CT.id=AL.city_id','LEFT');
        if($cat_id > 0){
            $this->db->where('ACR.acm_id', $cat_id);
        }
        $this->_search($search);
        $this->db->group_by('ACR.am_id');
        /*$this->db->get();
        echo $this->db->last_query();
        exit;*/
        return $this->db->count_all_results();
    }

	public function getProductListAll($cat_id = 0, $search = array(), $limit = array(), $orderBy = array()){
        $this->db->select('AM.*, AMD.*, CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, AMI.ami_path, ACMD.acmd_name,UM.name user_name,UM.um_created_date,UM.email, UM.mobile,CONT.name country_name, ST.name state_name, CT.name city_name');
        $this->db->from('animal_category_relation ACR');
        $this->db->join('animal_master AM',"AM.am_id = ACR.am_id AND AM.am_status = 'active' AND AM.am_deleted = '0'");
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','LEFT');
        $this->db->join('animal_master_images AMI','AMI.am_id=ACR.am_id AND AMI.ami_default = 1','LEFT');
        //$this->db->join('user_master UM', "UM.user_id=AM.user_id AND UM.um_status = 'active' AND UM.um_deleted = '0'", 'LEFT');
        $this->db->join('user_master UM', "UM.user_id=AM.user_id", 'LEFT');
        $this->db->join('animal_category_master ACM',"ACM.acm_id = ACR.acm_id AND ACM.parent_id > 0");
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id = ACR.acm_id AND ACMD.language = 'en'");
        $this->db->join('animal_location AL','AL.am_id=AM.am_id','LEFT');
        $this->db->join('countries CONT','CONT.id=AL.country_id','LEFT');
        $this->db->join('states ST','ST.id=AL.state_id','LEFT');
        $this->db->join('cities CT','CT.id=AL.city_id','LEFT');
        if($cat_id > 0){
            $this->db->where('ACR.acm_id', $cat_id);
        }
        $this->_search($search);
        $this->db->group_by('AM.am_id');
        $this->db->order_by($orderBy['col'], $orderBy['act']);
        $this->db->limit($limit['perPage'], $limit['start']);
        
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    private function _search($search = array()){
        if($search['keyWord'] != ''){
            $keyWordSearch = "( AMD.amd_name LIKE '%".$search['keyWord']."%'
                                OR AMD.amd_short_desc LIKE '%".$search['keyWord']."%'
                                OR AM.am_code LIKE '%".$search['keyWord']."%'
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
        if($search['buy_or_sell'] == 'sell' || $search['buy_or_sell'] == 'buy'){
            $this->db->where('AM.buy_or_sell = ', $search['buy_or_sell']);
        }

        if($search['choices'] != ''){
            if($search['choices'] == 'dip'){
                $this->db->where('AM.am_dip_choice = ', 'yes');
            }elseif($search['choices'] == 'pet'){
                $this->db->where('AM.am_pet_choice = ', 'yes');
            }elseif($search['choices'] == 'food'){
                $this->db->where('AM.am_food_choice = ', 'yes');
            }
        }
        $this->db->where('AMD.language','en');
        $this->db->where('AM.am_deleted','0');
    }

    public function getProductListComp($cat_id = 0){
        $this->db->select('AM.*, AMD.*, CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, AMI.ami_path, ACMD.acmd_name,UM.name user_name,UM.um_created_date,UM.email, UM.mobile,CONT.name country_name, ST.name state_name, CT.name city_name');
        $this->db->from('animal_category_relation ACR');
        $this->db->join('animal_master AM',"AM.am_id = ACR.am_id AND AM.am_status = 'active' AND AM.am_deleted = '0'");
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','LEFT');
        $this->db->join('animal_master_images AMI','AMI.am_id=ACR.am_id AND AMI.ami_default = 1','LEFT');
        //$this->db->join('user_master UM', "UM.user_id=AM.user_id AND UM.um_status = 'active' AND UM.um_deleted = '0'", 'LEFT');
        $this->db->join('user_master UM', "UM.user_id=AM.user_id", 'LEFT');
        $this->db->join('animal_category_master ACM',"ACM.acm_id = ACR.acm_id AND ACM.parent_id > 0");
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id = ACR.acm_id AND ACMD.language = 'en'");
        $this->db->join('animal_location AL','AL.am_id=AM.am_id','LEFT');
        $this->db->join('countries CONT','CONT.id=AL.country_id','LEFT');
        $this->db->join('states ST','ST.id=AL.state_id','LEFT');
        $this->db->join('cities CT','CT.id=AL.city_id','LEFT');
        if($cat_id > 0){
            $this->db->where('ACR.acm_id', $cat_id);
        }
        $this->db->where('AM.am_user_type','admin');
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
        $this->db->join('animal_category_master ACM',"ACM.acm_id = ACR.acm_id AND ACM.parent_id > 0");
        $this->db->join('animal_category_master_details ACMD',"ACMD.acm_id = ACR.acm_id AND ACMD.language = 'en'");
        $this->db->join('animal_location AL','AL.am_id=AM.am_id','LEFT');
        $this->db->join('countries CONT','CONT.id=AL.country_id','LEFT');
        $this->db->join('states ST','ST.id=AL.state_id','LEFT');
        $this->db->join('cities CT','CT.id=AL.city_id','LEFT');
        if($cat_id > 0){
            $this->db->where('ACR.acm_id', $cat_id);
        }
        $this->db->where('AM.am_user_type','user');
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
        $this->db->select('COM.*,UM.name, TIMESTAMPDIFF(SECOND, COM.created_date, NOW()) time_sec,TIMESTAMPDIFF(MINUTE, COM.created_date, NOW()) time_min,TIMESTAMPDIFF(HOUR, COM.created_date, NOW()) time_hour ');
        $this->db->from('comments COM');
        $this->db->join('user_master UM',"UM.user_id=COM.user_id AND UM.um_status='active' AND UM.um_deleted = '0'",'LEFT');
        if($am_id > 0){
            $this->db->where('COM.am_id', $am_id);
        }
        $this->db->where('COM.com_status', 'active');
        $this->db->order_by('COM.created_date','ASC');
        return $this->db->get()->result();
    }

    public function updateViewedCount($am_id = 0){
        $sql = "UPDATE `animal_master` SET `am_viewed_count` = `am_viewed_count` + 1 WHERE `am_id` = '".$am_id."'";
        $this->db->query($sql);
        if ($this->db->affected_rows() >= 0) return TRUE;
        return FALSE; 
    }

	

    
}