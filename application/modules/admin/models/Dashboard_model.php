<?php  if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Dashboard_model extends CI_Model {

    function __construct() {
        parent::__construct();
        log_message( 'INFO', 'Dsahboard_model enter' );
    }

    public function getTotalUser() {
        $this->db->where( 'um_deleted', '0' );
        $this->db->from( 'user_master' );
        return $this->db->count_all_results();
    }

    public function getTotalComment( $com_id = 0 ) {
        $this->db->where( 'is_deleted', '0' );
        $this->db->from( 'blog_comments' );
        return $this->db->count_all_results();
    }

    public function getTotalProduct( $com_id = 0 ) {
        $this->db->where( 'BR.is_status !=', 'delete' );
        $this->db->from( 'blogs B' );
        $this->db->join( 'blog_revisions BR', 'BR.blog_revision_id = B.blog_revision_id', 'inner' );
        return $this->db->count_all_results();
    }

    public function getTotalContact( $com_id = 0 ) {
        $this->db->from( 'contact_us' );
        return $this->db->count_all_results();
    }

}