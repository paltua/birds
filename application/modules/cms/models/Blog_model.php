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

    function getBlogDetails( $title_url = '' ) {
        $this->db->select( 'B.*, BREV.*' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        // $this->db->join( 'blog_images BIMG', 'BIMG.blog_id = B.blog_id AND BIMG.orders = 1 AND BIMG.is_status != "delete"', 'LEFT' );
        $this->db->where( 'BREV.is_status', 'active' );
        $this->db->where( 'BREV.title_url', $title_url );
        return $this->db->get()->result();
    }

    function getBlogDetailsImages( $title_url = '' ) {
        $this->db->select( 'B.*, BREV.*,BIMG.*' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BREV', 'BREV.blog_revision_id = B.blog_revision_id', 'INNER' );
        $this->db->join( 'blog_images BIMG', 'BIMG.blog_id = B.blog_id AND BIMG.is_status != "delete"', 'LEFT' );
        $this->db->where( 'BREV.is_status', 'active' );
        $this->db->where( 'BREV.title_url', $title_url );
        $this->db->order_by( 'BIMG.orders', 'DESC' );
        return $this->db->get()->result();
    }

}