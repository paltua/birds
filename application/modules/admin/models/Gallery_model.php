<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gallery_model extends CI_Model
{


    function __construct()
    {
        parent::__construct();
    }

    public function getAllData()
    {
        $this->db->select('*');
        $this->db->from('gallery');
        return $this->db->get()->result();
    }

    public function getDataTableTotalCount()
    {
        $this->db->from('gallery');
        $this->_setSearchCond();
        return $this->db->count_all_results();
    }

    public function getDataTableFilteredCount($searchData = '')
    {
        $this->db->from('gallery');
        $this->_setSearchCond($searchData);
        return $this->db->count_all_results();
    }

    private function _setSearchCond($searchData = '')
    {
        return true;
        // $this->db->where('EML.event_status !=', 'delete');
        // if ($searchData != '') {
        //     $where = "(EML.event_title LIKE '%" . $searchData . "%' 
        //                 OR EML.event_short_desc LIKE '%" . $searchData . "%'
        //             )";
        //     $this->db->where($where);
        // }
    }

    public function getDataTableData($searchData = '', $orderBy = array(), $limit = array())
    {
        $this->db->select('*');
        $this->db->from('gallery');
        $this->_setSearchCond($searchData);
        $this->db->order_by($orderBy['col'], $orderBy['val']);
        $this->db->limit($limit['perpage'], $limit['start']);
        $data['rowsData'] = $this->db->get()->result();
        $data['recordsTotal'] = $this->getDataTableTotalCount();
        $data['recordsFiltered'] = $this->getDataTableFilteredCount($searchData);
        return $data;
    }

    public function updateStatus($g_id = 0)
    {
        $sql = 'UPDATE `gallery` SET `g_status`=IF(g_status = "active","inactive","active") WHERE 1 AND g_id=' . $g_id;
        $this->db->query($sql);
        return true;
    }
}