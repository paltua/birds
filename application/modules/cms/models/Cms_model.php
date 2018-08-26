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
                WHERE
                    `ACM`.`acm_is_deleted` = '0' AND `ACMD`.`language` = 'en'
                    AND ACM.parent_id IN (SELECT acm_id FROM animal_category_master WHERE parent_id = 0)";
                    
        return $this->db->query($sql)->result();
    }

    

    
   
}
