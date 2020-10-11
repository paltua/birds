<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Programme_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        log_message('INFO', 'Programme_model enter');
    }

    public function getDataTableTotalCount()
    {
        $this->db->from('programs PRO');
        $this->_setSearchCond();
        return $this->db->count_all_results();
    }

    public function getDataTableFilteredCount($searchData = '')
    {
        $this->db->from('programs PRO');
        $this->_setSearchCond($searchData);
        return $this->db->count_all_results();
    }

    private function _setSearchCond($searchData = '')
    {
        $this->db->where('PRO.is_status !=', 'delete');
        if ($searchData != '') {
            $where = "(PRO.program_title LIKE '%" . $searchData . "%' 
                        OR PRO.program_short_desc LIKE '%" . $searchData . "%'
                    )";
            $this->db->where($where);
        }
    }

    public function getDataTableData($searchData = '', $orderBy = array(), $limit = array())
    {
        $this->db->select('PRO.*, PROI.prog_img_name image_path,  PROI.prog_img_name image_alt');
        $this->db->from('programs PRO');
        $this->db->join('programs_images PROI', 'PROI.program_id = PRO.program_id AND is_default="1"', 'LEFT');
        $this->_setSearchCond($searchData);
        $this->db->order_by($orderBy['col'], $orderBy['val']);
        $this->db->limit($limit['perpage'], $limit['start']);
        $data['rowsData'] = $this->db->get()->result();
        $data['recordsTotal'] = $this->getDataTableTotalCount();
        $data['recordsFiltered'] = $this->getDataTableFilteredCount($searchData);
        return $data;
    }

    public function getAllAnimalParentCategory($parent_id = 0)
    {
        $this->db->select('*');
        $this->db->from('animal_category_master ACM');
        $this->db->join('animal_category_master_details ACMD', 'ACMD.acm_id=ACM.acm_id', 'INNER');
        $this->db->where('ACM.acm_is_deleted', '0');
        $this->db->where('ACM.acm_status', 'active');
        $this->db->where('ACMD.language', 'en');
        $this->db->where('ACM.parent_id', $parent_id);
        $this->db->order_by('ACMD.acmd_name', 'ASC');
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    public function getSingleData($blog_id = 0)
    {
        $this->db->select('PRO.*, PROI.prog_img_name');
        $this->db->from('programs PRO');
        $this->db->join('programs_images PROI', 'PROI.program_id = PRO.program_id AND is_default="1"', 'LEFT');
        $this->db->where('PRO.is_status !=', 'delete');
        $this->db->where('PRO.program_id', $blog_id);
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    public function getAssignCat($blog_id = 0)
    {
        $this->db->select('B.blog_id,BAC.*');
        $this->db->from('blogs B');
        $this->db->join('blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER');
        $this->db->join('blog_animal_categorys BAC', 'BAC.blog_revision_id = B.blog_revision_id', 'INNER');
        $this->db->where('BREV.is_status !=', 'delete');
        $this->db->where('B.blog_id', $blog_id);
        $this->db->order_by('BAC.acm_id', 'ASC');
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

    public function getSingle($program_id = 0)
    {
        $this->db->select('PRO.*');
        $this->db->from('programs PRO');
        $this->db->where('PRO.is_status !=', 'delete');
        $this->db->where('PRO.program_id', $program_id);
        return $this->db->get()->result();
    }

    public function getImageList($program_id = 0)
    {
        $this->db->select('*');
        $this->db->from('programs_images PROI');
        if ($program_id > 0) {
            $this->db->where('PROI.program_id', $program_id);
        }
        return $this->db->get()->result();
    }

    public function setDefaultImage($prog_img_id = 0, $program_id = 0)
    {
        $sql = 'UPDATE `programs_images` SET `is_default`=IF(prog_img_id =' . $prog_img_id . ',1,0) WHERE 1 AND program_id=' . $program_id;
        $this->db->query($sql);
        return true;
    }

    public function sql_print()
    {
        $this->db->get();
        echo $this->db->last_query();
        exit;
    }
}