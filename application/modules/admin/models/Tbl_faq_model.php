<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tbl_faq_model extends CI_Model {
    /* Here all org_id is equal to account_id*/
    function __construct() {
        parent::__construct();
    }
    
    
    /*
    * function getFaqAjax()
    * This is used to get Total FAQ
    */
    public function getFaqAjax(){
        $this->db->select('faq_id');
        $this->db->from('cms_faq');
        $query = $this->db->get();
        return $query->num_rows();
    }
    

    /*
    * function getFaqAjaxSearch()
    * This is used to get search raesult of FAQ
    */
    public function getFaqAjaxSearch($searchData = '',$orderBy = array(),$limit = array()){
        $sql = "SELECT *
                FROM cms_faq ";
        if($searchData != ''){
            $sql .= " AND (cms_faq.question LIKE '%".$searchData."%'
                            cms_faq.answer LIKE '%".$searchData."%'
                            cms_faq.faq_status LIKE '%".$searchData."%'
                            )";
        }
        $sql .= " ORDER BY ".$orderBy['col']." ".$orderBy['val'] ;
        $sql .= " LIMIT ".$limit['start'].", ".$limit['perpage'] ;
        $query = $this->db->query($sql);
        $data['total'] = $query->num_rows();
        $data['rows'] = $query->result();
        return $data;
    }

    

}
