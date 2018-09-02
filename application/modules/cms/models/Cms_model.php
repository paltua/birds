<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cms_model extends CI_Model {
    

    function __construct() {
        parent::__construct();
    }

    public function getLevelOneCategory(){
        $sql = "SELECT
                    `ACM`.`parent_id`,
                    `ACM`.`image_name`,
                    `ACMD`.*,
                    `LANG`.`lang_name`
                FROM
                    `animal_category_master` `ACM`
                INNER JOIN `animal_category_master_details` `ACMD` ON
                    `ACMD`.`acm_id` = `ACM`.`acm_id`
                JOIN `language` `LANG` ON
                    `LANG`.`language` = `ACMD`.`language`
                WHERE 1
                    AND `ACM`.`acm_is_deleted` = '0' 
                    AND `ACM`.`acm_status` = 'active' 
                    AND `ACMD`.`language` = 'en'
                    AND ACM.parent_id IN (SELECT acm_id FROM animal_category_master WHERE 1 AND parent_id = 0 AND `acm_is_deleted` = '0' AND `acm_status` = 'active')";
                    
        return $this->db->query($sql)->result();
    }

    public function getLetestProduct(){
        $sql = "SELECT AM.`am_id`, AMD.`amd_name`, CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, AMI.`ami_path` 
                FROM `animal_master` AM 
                JOIN `animal_master_details` AMD ON AMD.am_id=AM.am_id AND AMD.language='en' 
                LEFT JOIN `animal_master_images` AMI ON AMI.`am_id`=AM.`am_id` AND AMI.`ami_default`='1' 
                WHERE 1 
                    AND AM.`am_status`='active' 
                    AND AM.`am_deleted` = '0'
                ORDER BY AM.`am_id` DESC
                LIMIT 15";
        return $this->db->query($sql)->result();            
    }


    public function getPremiumProduct(){
        $sql = "SELECT AM.`am_id`, AMD.`amd_name`, CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, AMI.`ami_path`
                FROM `animal_master` AM 
                JOIN `animal_master_details` AMD ON AMD.am_id=AM.am_id AND AMD.language='en' 
                LEFT JOIN `animal_master_images` AMI ON AMI.`am_id`=AM.`am_id` AND AMI.`ami_default`='1' 
                WHERE 1 
                    AND AM.`am_status`='active' 
                    AND AM.`am_deleted` = '0'
                ORDER BY AMD.`amd_price` DESC
                LIMIT 15";
        return $this->db->query($sql)->result();
    }

    public function getSelectedCategory($cat_id = 0){
        $sql = "SELECT
                    `ACM`.`parent_id`,
                    `ACM`.`image_name`,
                    `ACMD`.*,
                    `LANG`.`lang_name`
                FROM
                    `animal_category_master` `ACM`
                LEFT JOIN `animal_category_master_details` `ACMD` ON
                    `ACMD`.`acm_id` = `ACM`.`acm_id`
                LEFT JOIN `language` `LANG` ON
                    `LANG`.`language` = `ACMD`.`language`
                WHERE 1
                    AND `ACM`.`acm_is_deleted` = '0' 
                    AND `ACM`.`acm_status` = 'active' 
                    AND `ACM`.`acm_id` = ".$cat_id."
                    AND ACM.parent_id IN (SELECT acm_id FROM animal_category_master WHERE 1 AND parent_id = 0 AND `acm_is_deleted` = '0' AND `acm_status` = 'active') 
                ORDER BY `LANG`.`lang_name` ASC ";        
        return $this->db->query($sql)->result();
    }

    public function getProductList($cat_id = 0){
        $this->db->select('AM.*, AMD.*, AMI.*');
        $this->db->from('animal_category_relation ACR');
        $this->db->join('animal_master AM',"AM.am_id = ACR.am_id AND am_status = 'active' AND am_deleted = '0'");
        $this->db->join('animal_master_details AMD','AMD.am_id=AM.am_id','LEFT');
        $this->db->join('animal_master_images AMI','AMD.am_id=ACR.am_id AND AMI.ami_default = 1','LEFT');
        $this->db->join('user_master UM', 'UM.user_id=AM.user_id', 'LEFT');
        $this->db->where('UM.um_status','active');
        $this->db->where('UM.um_deleted','0');
        if($cat_id > 0){
            $this->db->where('ACR.acm_id', $cat_id);
        }
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

    

    
   
}
