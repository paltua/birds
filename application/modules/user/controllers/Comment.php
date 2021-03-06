<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Comment extends MY_Controller {
    public $data = array();
    public $section ;
    public $formHeader ;

    public function __construct() {
        parent::__construct();
        $this->ion_user_auth->isLoggedIn();
    }

    public function index() {
        $this->listing();
    }

    public function listing() {

    }

    public function add() {

        $inData['user_id'] = $this->session->userdata( 'user_id' );
        $inData['com_parent_id'] = 0;
        $inData['comments'] = trim( $this->input->post( 'comments' ) );
        $inData['am_id'] = trim( $this->input->post( 'am_id' ) );
        $inData['com_status'] = 'active';
        $this->tbl_generic_model->add( 'comments', $inData );
        $data['html'] = '';
        $data['html'] = '<figure>
					<span class="pic"><img src="'.base_url( 'public/'.THEME.'/images/site-logo.png' ).'" alt=""></span>
					<figcaption>
						<p>'.$inData['comments'].'</p>
						<h3>'.$this->session->userdata( 'name' ).'</h3>
						<h4>Now</h4>
					</figcaption>
				</figure>';
        echo json_encode( $data );

    }

    public function blog_add() {

        $inData['user_id'] = $this->session->userdata( 'user_id' );
        $inData['parent_blog_com_id'] = 0;
        $inData['comments'] = trim( $this->input->post( 'comments' ) );
        $inData['blog_revision_id'] = trim( $this->input->post( 'blog_revision_id' ) );
        $inData['com_status'] = 'active';
        $this->tbl_generic_model->add( 'blog_comments', $inData );
        $data['html'] = 'success';

        echo json_encode( $data );

    }

    public function addForBlog() {

        $inData['user_id'] = $this->session->userdata( 'user_id' );
        $inData['com_parent_id'] = 0;
        $inData['comments'] = trim( $this->input->post( 'comments' ) );
        $inData['am_id'] = trim( $this->input->post( 'am_id' ) );
        ;
        $inData['com_status'] = 'active';
        $this->tbl_generic_model->add( 'comments', $inData );
        $data['html'] = '';
        $data['html'] = '<figure>
					<span class="pic"><img src="'.base_url( 'public/'.THEME.'/images/site-logo.png' ).'" alt=""></span>
					<figcaption>
						<p>'.$inData['comments'].'</p>
						<h3>'.$this->session->userdata( 'name' ).'</h3>
						<h4>Now</h4>
					</figcaption>
				</figure>';
        echo json_encode( $data );

    }

    public function reply() {

    }

}