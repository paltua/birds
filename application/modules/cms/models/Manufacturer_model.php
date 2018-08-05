<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Manufacturer_model extends CI_Model {
    /* Here all org_id is equal to account_id */

    function __construct() {
        parent::__construct();
    }

    /*
     * function getAjax()
     * This is used to get total Manufacturer
     */

    public function getAjax() {
        $sql = "SELECT MANUF.m_id
                FROM manufacturer_master MANUF";
        $query = $this->db->query($sql);
        return $query->num_rows();
    }

    /*
     * function getAjaxSearch()
     * This is used to get Manufacturer
     */

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
