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
                LIMIT 35";
        return $this->db->query($sql)->result();            
    }
    
    public function getDipChoicesProduct(){
        $sql = "SELECT AM.`am_id`, AMD.`amd_name`, CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, AMI.`ami_path` 
                FROM `animal_master` AM 
                JOIN `animal_master_details` AMD ON AMD.am_id=AM.am_id AND AMD.language='en' 
                LEFT JOIN `animal_master_images` AMI ON AMI.`am_id`=AM.`am_id` AND AMI.`ami_default`='1' 
                WHERE 1 
                    AND AM.`am_status`='active' 
                    AND AM.`am_deleted` = '0'
                    AND AM.`am_dip_choice` = 'yes'
                ORDER BY AM.`am_id` DESC ";
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
                LIMIT 35";
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

    public function getAboutUsUser(){
        $this->db->select('*');
        $this->db->from('about_us_user');
        return $this->db->get()->result();
    }

    public function getGalleryList($limit = 10){
        $this->db->select('*');
        $this->db->from('gallery');
        $this->db->order_by('created_date','DESC');
        $this->db->limit('8');
        return $this->db->get()->result();
    }

    
    

    
   
}
