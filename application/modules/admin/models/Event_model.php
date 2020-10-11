<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Event_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        log_message('INFO', 'Event_model enter');
    }

    public function getDataTableTotalCount()
    {
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->_setSearchCond();
        return $this->db->count_all_results();
    }

    public function getDataTableFilteredCount($searchData = '')
    {
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->_setSearchCond($searchData);
        return $this->db->count_all_results();
    }

    private function _setSearchCond($searchData = '')
    {
        $this->db->where('EML.event_status !=', 'delete');
        if ($searchData != '') {
            $where = "(EML.event_title LIKE '%" . $searchData . "%' 
                        OR EML.event_short_desc LIKE '%" . $searchData . "%'
                    )";
            $this->db->where($where);
        }
    }

    public function getDataTableData($searchData = '', $orderBy = array(), $limit = array())
    {
        $this->db->select('EM.*, EML.*,CONCAT(EL.address,",",CT.name,",",ST.name,",",EL.pin,",",CN.name) location, EI.ei_image_name image_path');
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->join('event_location EL', 'EL.eml_id = EM.eml_id', 'INNER');
        $this->db->join('countries CN', 'CN.id = EL.country_id', 'LEFT');
        $this->db->join('states ST', 'ST.id = EL.state_id', 'LEFT');
        $this->db->join('cities CT', 'CT.id = EL.city_id', 'LEFT');
        $this->db->join('event_images EI', 'EI.em_id = EM.em_id AND EI.is_default = "1"', 'LEFT');
        $this->_setSearchCond($searchData);
        $this->db->order_by($orderBy['col'], $orderBy['val']);
        $this->db->limit($limit['perpage'], $limit['start']);
        $data['rowsData'] = $this->db->get()->result();
        $data['recordsTotal'] = $this->getDataTableTotalCount();
        $data['recordsFiltered'] = $this->getDataTableFilteredCount($searchData);
        return $data;
    }

    public function getProgramme($parent_id = 0)
    {
        $this->db->select('*');
        $this->db->from('programs');
        $this->db->where('is_status', 'active');
        $this->db->order_by('program_title', 'ASC');
        return $this->db->get()->result();
    }

    public function getSingleData($em_id = 0)
    {
        $this->db->select('EML.*, EL.*');
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->join('event_location EL', 'EL.eml_id = EM.eml_id', 'LEFT');
        $this->db->where('EML.event_status !=', 'delete');
        $this->db->where('EM.em_id', $em_id);
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    public function getAssignProgramme($eml_id = 0)
    {
        $this->db->select('EPR.*');
        $this->db->from('event_programs_rel EPR');
        $this->db->where('EPR.eml_id', $eml_id);
        // $this->sql_print();
        return $this->db->get()->result();
    }

    public function getAssignCatForStatus($blog_revision_id = 0)
    {
        $this->db->select('BAC.*');
        $this->db->from('blog_animal_categorys BAC');
        $this->db->where('BAC.blog_revision_id', $blog_revision_id);
        $this->db->order_by('BAC.acm_id', 'ASC');
        // $this->sql_print();
        return $this->db->get()->result();
    }

    public function getSingle($em_id = 0)
    {
        $this->db->select('EML.*');
        $this->db->from('event_master EM');
        $this->db->join('event_master_log EML', 'EML.eml_id = EM.eml_id', 'INNER');
        $this->db->where('EML.event_status !=', 'delete');
        $this->db->where('EM.em_id', $em_id);
        return $this->db->get()->result();
    }

    public function getImageList($em_id = 0)
    {
        $this->db->select('*');
        $this->db->from('event_images EIMG');
        if ($em_id > 0) {
            $this->db->where('EIMG.em_id', $em_id);
        }
        $this->db->order_by('EIMG.ei_id', 'DESC');
        return $this->db->get()->result();
    }

    public function setDefaultImage($em_id = 0, $ei_id = 0)
    {
        $sql = 'UPDATE `event_images` SET `is_default`=IF(ei_id =' . $ei_id . ',1,0) WHERE 1 AND em_id=' . $em_id;
        $this->db->query($sql);
        return true;
    }

    public function getStateList($country_id = 0)
    {
        $this->db->select('*');
        $this->db->from('states');
        $this->db->where('country_id', $country_id);
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result();
    }

    public function getCityList($state_id = 0)
    {
        $this->db->select('*');
        $this->db->from('cities');
        $this->db->where('state_id', $state_id);
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result();
    }

    public function sql_print()
    {
        $this->db->get();
        echo $this->db->last_query();
        exit;
    }
}