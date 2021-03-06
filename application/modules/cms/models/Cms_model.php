<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cms_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getLevelOneCategory()
    {
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

    public function getLetestProduct()
    {
        $sql = "SELECT AM.`am_id`, AMD.`amd_name`, CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, AMI.`ami_path` 
                FROM `animal_master` AM 
                JOIN `animal_master_details` AMD ON AMD.am_id=AM.am_id AND AMD.language='en' 
                LEFT JOIN `animal_master_images` AMI ON AMI.`am_id`=AM.`am_id` AND AMI.`ami_default`='1' 
                WHERE 1 
                    AND AM.`am_status`='active' 
                    AND AM.`am_deleted` = '0'
                    AND AM.`am_pet_choice` = 'yes'
                ORDER BY AM.`am_id` DESC
                LIMIT 35";
        return $this->db->query($sql)->result();
    }

    public function getDipChoicesProduct()
    {
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

    public function getPremiumProduct()
    {
        $sql = "SELECT AM.`am_id`, AMD.`amd_name`, CAST(AMD.`amd_price` AS DECIMAL(10,2)) amd_price, AMI.`ami_path`
                FROM `animal_master` AM 
                JOIN `animal_master_details` AMD ON AMD.am_id=AM.am_id AND AMD.language='en' 
                LEFT JOIN `animal_master_images` AMI ON AMI.`am_id`=AM.`am_id` AND AMI.`ami_default`='1' 
                WHERE 1 
                    AND AM.`am_status`='active' 
                    AND AM.`am_deleted` = '0'
                    AND AM.`am_food_choice` = 'yes'
                ORDER BY AMD.`amd_price` DESC
                LIMIT 35";
        return $this->db->query($sql)->result();
    }

    public function getSelectedCategory($cat_id = 0)
    {
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
                    AND `ACM`.`acm_id` = " . $cat_id . "
                    AND ACM.parent_id IN (SELECT acm_id FROM animal_category_master WHERE 1 AND parent_id = 0 AND `acm_is_deleted` = '0' AND `acm_status` = 'active') 
                ORDER BY `LANG`.`lang_name` ASC ";

        return $this->db->query($sql)->result();
    }

    public function getBestChoices()
    {
        $selectedCat = '33,34,43';
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
                    AND ACM.acm_id IN (" . $selectedCat . ')';

        return $this->db->query($sql)->result();
    }

    public function getAboutUsUser()
    {
        $this->db->select('*');
        $this->db->from('about_us_user');
        $this->db->where('name != ', '');
        return $this->db->get()->result();
    }

    public function getGalleryList($limit = 10)
    {
        $this->db->select('*');
        $this->db->from('gallery');
        $this->db->order_by('created_date', 'DESC');
        $this->db->limit('8');
        return $this->db->get()->result();
    }

    public function getPageContent($name = '')
    {
        $this->db->select('*');
        $this->db->from('settings');
        $this->db->where('name', $name);
        $this->db->limit('8');
        return $this->db->get()->result();
    }

    public function getMainEvents()
    {
        $this->db->select('EM.*, EML.*,CONCAT(EL.address,",",CT.name,",",ST.name,",",EL.pin,",",CN.name) location, EI.ei_image_name image_path');
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->join('event_location EL', 'EL.eml_id = EM.eml_id', 'INNER');
        $this->db->join('countries CN', 'CN.id = EL.country_id', 'LEFT');
        $this->db->join('states ST', 'ST.id = EL.state_id', 'LEFT');
        $this->db->join('cities CT', 'CT.id = EL.city_id', 'LEFT');
        $this->db->join('event_images EI', 'EI.em_id = EM.em_id AND EI.is_default = "1"', 'LEFT');
        $this->db->where('EML.event_status', 'active');
        $this->db->limit('8');
        return $this->db->get()->result();
    }

    public function getOthersEvent($name = '')
    {
        $this->db->select('EM.*, EML.*,CONCAT(EL.address,",",CT.name,",",ST.name,",",EL.pin,",",CN.name) location, EI.ei_image_name image_path');
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->join('event_location EL', 'EL.eml_id = EM.eml_id', 'INNER');
        $this->db->join('countries CN', 'CN.id = EL.country_id', 'LEFT');
        $this->db->join('states ST', 'ST.id = EL.state_id', 'LEFT');
        $this->db->join('cities CT', 'CT.id = EL.city_id', 'LEFT');
        $this->db->join('event_images EI', 'EI.em_id = EM.em_id AND EI.is_default = "1"', 'LEFT');
        $this->db->where('EML.event_status', 'active');
        return $this->db->get()->result();
    }

    public function getEventList($limit = array())
    {
        $this->db->select('EM.*, EML.*,CONCAT(EL.address,",",CT.name,",",ST.name,",",EL.pin,",",CN.name) location, EI.ei_image_name image_path');
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->join('event_location EL', 'EL.eml_id = EM.eml_id', 'INNER');
        $this->db->join('countries CN', 'CN.id = EL.country_id', 'LEFT');
        $this->db->join('states ST', 'ST.id = EL.state_id', 'LEFT');
        $this->db->join('cities CT', 'CT.id = EL.city_id', 'LEFT');
        $this->db->join('event_images EI', 'EI.em_id = EM.em_id AND EI.is_default = "1"', 'LEFT');
        $this->db->where('EML.event_status', 'active');
        $this->db->order_by('EML.event_start_date_time', 'DESC');
        $this->db->limit($limit['perPage'], $limit['start']);
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    public function getEventCount()
    {
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->where('EML.event_status', 'active');
        $query = $this->db->get();
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $query->num_rows();
    }

    public function getSingleEvent($title_url = '')
    {
        $this->db->select('EM.*, EML.*,CONCAT(EL.address,",",CT.name,",",ST.name,",",EL.pin,",",CN.name) location');
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->join('event_location EL', 'EL.eml_id = EM.eml_id', 'INNER');
        $this->db->join('countries CN', 'CN.id = EL.country_id', 'LEFT');
        $this->db->join('states ST', 'ST.id = EL.state_id', 'LEFT');
        $this->db->join('cities CT', 'CT.id = EL.city_id', 'LEFT');
        $this->db->where('EML.event_status', 'active');
        $this->db->where('EML.event_title_url', $title_url);
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    public function getSingleEventImages($em_id = 0)
    {
        $this->db->select('EI.*');
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->join('event_images EI', 'EI.em_id = EM.em_id', 'LEFT');
        $this->db->where('EM.em_id', $em_id);
        $this->db->order_by('EI.is_default', 'DESC');
        return $this->db->get()->result();
    }

    public function getCompleted($em_id = 0)
    {
        $this->db->select('EM.*, EML.*');
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->where('EML.event_status', 'active');
        $this->db->where('EML.event_end_date_time < NOW()');
        $this->db->where('EML.event_status', 'active');
        $this->db->where('EM.em_id != ', $em_id);
        $this->db->limit(10);
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    public function getUpcoming($em_id = 0)
    {
        $this->db->select('EM.*, EML.*');
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->where('EML.event_status', 'active');
        $this->db->where('EML.event_end_date_time > NOW()');
        $this->db->where('EML.event_status', 'active');
        $this->db->where('EM.em_id != ', $em_id);
        $this->db->limit(10);
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    public function getProgrammes()
    {
        $this->db->select('PR.*,PRI.prog_img_name');
        $this->db->from('programs PR');
        $this->db->join('programs_images PRI', 'PRI.program_id = PR.program_id AND PRI.is_default="1"', 'LEFT');
        $this->db->where('PR.is_status', 'active');
        $this->db->order_by('PR.program_title', 'ASC');
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    public function getProgrammesDetails($title_url = '')
    {
        $this->db->select('PR.*,PRI.prog_img_name');
        $this->db->from('programs PR');
        $this->db->join('programs_images PRI', 'PRI.program_id = PR.program_id', 'LEFT');
        $this->db->where('PR.is_status', 'active');
        $this->db->where('PR.pro_title_url', $title_url);
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }
}