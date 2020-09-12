<?php  if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Blog_model extends CI_Model {
    function __construct() {
        parent::__construct();
        log_message( 'INFO', 'Blog_model enter' );
    }

    public function getDataTableTotalCount() {
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->_setSearchCond();
        return $this->db->count_all_results();
    }

    public function getDataTableFilteredCount( $searchData = '' ) {
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->_setSearchCond( $searchData );
        return $this->db->count_all_results();
    }

    private function _setSearchCond( $searchData = '' ) {
        $this->db->where( 'BREV.is_status !=', 'delete' );
        if ( $searchData != '' ) {
            $where = "(BREV.title LIKE '%".$searchData."%' 
                        OR BREV.short_desc LIKE '%".$searchData."%'
                    )";
            $this->db->where( $where );
        }

    }

    public function getDataTableData( $searchData = '', $orderBy = array(), $limit = array() ) {
        $this->db->select( 'B.*, BREV.title, BREV.short_desc, BREV.is_status, BREV.created_date,
        BIMG.image_path, BIMG.image_alt' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->db->join( 'blog_images BIMG', 'BIMG.blog_id = B.blog_id AND BIMG.orders = 1 AND BIMG.is_status != "delete"', 'LEFT' );
        $this->_setSearchCond( $searchData );
        $this->db->order_by( $orderBy['col'], $orderBy['val'] );
        $this->db->limit( $limit['perpage'], $limit['start'] );
        $data['rowsData'] = $this->db->get()->result();
        $data['recordsTotal'] = $this->getDataTableTotalCount();
        $data['recordsFiltered'] = $this->getDataTableFilteredCount( $searchData );
        return $data;
    }

    public function getAllAnimalParentCategory( $parent_id = 0 ) {
        $this->db->select( '*' );
        $this->db->from( 'animal_category_master ACM' );
        $this->db->join( 'animal_category_master_details ACMD', 'ACMD.acm_id=ACM.acm_id', 'INNER' );
        $this->db->where( 'ACM.acm_is_deleted', '0' );
        $this->db->where( 'ACM.acm_status', 'active' );
        $this->db->where( 'ACMD.language', 'en' );
        $this->db->where( 'ACM.parent_id', $parent_id );
        $this->db->order_by( 'ACMD.acmd_name', 'ASC' );
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    public function getSingleData( $blog_id = 0 ) {
        $this->db->select( 'BREV.*' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->db->where( 'BREV.is_status !=', 'delete' );
        $this->db->where( 'B.blog_id', $blog_id );
        // $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $this->db->get()->result();
    }

    public function getAssignCat( $blog_id = 0 ) {
        $this->db->select( 'B.blog_id,BAC.*' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->db->join( 'blog_animal_categorys BAC', 'BAC.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->db->where( 'BREV.is_status !=', 'delete' );
        $this->db->where( 'B.blog_id', $blog_id );
        $this->db->order_by( 'BAC.acm_id', 'ASC' );
        // $this->sql_print();
        return $this->db->get()->result();
    }

    public function getAssignCatForStatus( $blog_revision_id = 0 ) {
        $this->db->select( 'BAC.*' );
        $this->db->from( 'blog_animal_categorys BAC' );
        $this->db->where( 'BAC.blog_revision_id', $blog_revision_id );
        $this->db->order_by( 'BAC.acm_id', 'ASC' );
        // $this->sql_print();
        return $this->db->get()->result();
    }

    public function getSingle( $am_id = 0 ) {
        $this->db->select( '*' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        // $this->db->join( 'blog_images BIMG', 'BIMG.blog_id=B.blog_id', 'INNER' );
        $this->db->where( 'BREV.is_status !=', 'delete' );
        return $this->db->get()->result();
    }

    public function getImageList( $blog_id = 0 ) {
        $this->db->select( '*' );
        $this->db->from( 'blog_images BIMG' );
        if ( $blog_id > 0 ) {
            $this->db->where( 'BIMG.blog_id', $blog_id );
        }
        $this->db->order_by( 'BIMG.blog_image_id', 'DESC' );
        return $this->db->get()->result();
    }

    public function setDefaultImage( $blog_id = 0, $blog_image_id = 0 ) {
        $sql = 'UPDATE `blog_images` SET `orders`=IF(blog_image_id ='.$blog_image_id.',1,0) WHERE 1 AND blog_id='.$blog_id;
        $this->db->query( $sql );
        return true;
    }

    public function sql_print() {
        $this->db->get();
        echo $this->db->last_query();
        exit;
    }

}