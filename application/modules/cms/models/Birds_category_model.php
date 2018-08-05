<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Birds_category_model extends CI_Model {
    

    function __construct() {
        parent::__construct();
    }

    public function getAllData(){
        $this->db->select('*');
        $this->db->from('birds_category_master BCM');
        $this->db->from('birds_category_master_details BCMD','BCMD.bcm_id=BCM.bcm_id');
        $this->db->where('BCM.is_deleted','0');
        $this->db->where('BCMD.language','en');
        return $this->db->get()->result();
    }

    public function getAjax() {
        $sql = "SELECT *
                FROM manufacturer_master MANUF";
        $query = $this->db->query($sql);
        return $query->num_rows();
    }

   

    public function getAjaxSearch($searchData = '', $orderBy = array(), $limit = array()) {
        $sql = "SELECT *
                FROM manufacturer_master MANUF
                WHERE 1   ";
        if ($searchData != '') {
            $sql .= " AND (MANUF.m_name LIKE '%" . $searchData . "%')";
        }
        $sql .= " ORDER BY " . $orderBy['col'] . " " . $orderBy['val'];
        $sql .= " LIMIT " . $limit['start'] . ", " . $limit['perpage'];
        $query = $this->db->query($sql);
        $data['total'] = $query->num_rows();
        $data['rows'] = $query->result();
        return $data;
    }

    
   
}
