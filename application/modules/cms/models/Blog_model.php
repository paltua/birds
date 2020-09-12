<?php
if ( !defined( 'BASEPATH' ) )
exit( 'No direct script access allowed' );

class Blog_model extends CI_Model {
    function __construct() {
        parent::__construct();
    }

    function getBlogDashboard() {
        $this->db->select( 'B.*, BREV.title,BREV.title_url, BREV.short_desc, BREV.is_status, BREV.created_date,
        BIMG.image_path, BIMG.image_alt' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->db->join( 'blog_images BIMG', 'BIMG.blog_id = B.blog_id AND BIMG.orders = 1 AND BIMG.is_status != "delete"', 'LEFT' );
        $this->db->where( 'BREV.is_status', 'active' );
        $this->db->order_by( 'B.blog_id', 'DESC' );
        $this->db->limit( 4, 0 );
        return $this->db->get()->result();
    }

    function getBlogList() {

    }

    function getDetails( $title_url = '' ) {
        $this->db->select( 'B.*, BREV.*' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        // $this->db->join( 'blog_images BIMG', 'BIMG.blog_id = B.blog_id AND BIMG.orders = 1 AND BIMG.is_status != "delete"', 'LEFT' );
        $this->db->where( 'BREV.is_status', 'active' );
        $this->db->where( 'BREV.title_url', $title_url );
        return $this->db->get()->result();
    }

    function getDetailsImages( $blog_id = 0 ) {
        $this->db->select( 'B.*, BIMG.*' );
        $this->db->from( 'blogs B' );
        // $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->db->join( 'blog_images BIMG', 'BIMG.blog_id = B.blog_id AND BIMG.is_status != "delete"', 'LEFT' );
        $this->db->where( 'BREV.is_status', 'active' );
        $this->db->where( 'B.blog_id', $blog_id );
        $this->db->order_by( 'BIMG.orders', 'DESC' );
        return $this->db->get()->result();
    }

    function getComments( $blog_id = 0 ) {
        $this->db->select( 'BCOM.*,TIMESTAMPDIFF(SECOND, BCOM.created_date, NOW()) time_sec,TIMESTAMPDIFF(MINUTE, BCOM.created_date, NOW()) time_min,TIMESTAMPDIFF(HOUR, BCOM.created_date, NOW()) time_hour, BREV.blog_id,BREV.title,BREV.title_url, UM.name,UM.email,UM.mobile' );
        $this->db->from( 'blog_comments BCOM' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = BCOM.blog_revision_id', 'INNER' );
        $this->db->join( 'user_master UM', 'UM.user_id = BCOM.user_id', 'INNER' );
        $this->db->where( 'BCOM.is_deleted', '0' );
        $this->db->where( 'BREV.blog_id', $blog_id );
        $this->db->order_by( 'BCOM.created_date', 'DESC' );
        // $this->sql_print();
        return $this->db->get()->result();
    }

    function getOthersBlog( $blog_id = 0 ) {
        $this->db->select( 'B.*, BREV.*,BIMG.*' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->db->join( 'blog_images BIMG', 'BIMG.blog_id = B.blog_id AND BIMG.orders = 1 AND BIMG.is_status != "delete"', 'LEFT' );
        $this->db->where( 'BREV.is_status', 'active' );
        $this->db->where( 'B.blog_id != ', $blog_id );
        $this->db->order_by( 'BREV.created_date', 'DESC' );
        return $this->db->get()->result();
    }

    function getCategoryBlog( $category_id = 0 ) {
        $this->db->select( 'BREV.blog_id, BREV.title,BREV.title_url, BIMG.image_path' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->db->join( 'blog_images BIMG', 'BIMG.blog_id = B.blog_id AND BIMG.orders = 1 AND BIMG.is_status != "delete"', 'LEFT' );
        $this->db->join( 'blog_animal_categorys BAC', 'BAC.blog_revision_id = BREV.blog_revision_id AND (BAC.acm_id=1 OR  BAC.acm_id='.$category_id.')', 'INNER' );
        $this->db->where( 'BREV.is_status', 'active' );
        $this->db->group_by( 'BREV.blog_id, BREV.title,BREV.title_url, BIMG.image_path' );
        $this->db->order_by( 'BREV.created_date', 'DESC' );
        return $this->db->get()->result();
    }

    public function sql_print() {
        $this->db->get();
        echo $this->db->last_query();
        exit;
    }

}