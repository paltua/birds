<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class About_us_user extends MY_Controller {
    public $controller;

    public function __construct() {
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->load->library( 'Template' );
        $this->_user_id = trim( $this->session->userdata( 'aum_id' ) );
        $this->controller = $this->router->fetch_class();
        //$this->load->model( $this->controller.'_model' );
    }

    public function index() {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'About us User';
        $status = $this->session->flashdata( 'status' );
        $msg = $this->session->flashdata( 'msg' );
        //$data['lang'] = getLanguageArr();
        $data['msg'] = $this->template->getMessage( $status, $msg );
        $data['list'] = $this->tbl_generic_model->get( 'about_us_user', '*', array() );
        $this->template->setTitle( 'Admin : About us User' );
        $this->template->setLayout( 'dashboard' );

        $this->template->homeAdminRender( $this->controller.'/index', $data );
    }

    public function edit( $id = 0 ) {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'About us User';
        $status = $this->session->flashdata( 'status' );
        $msg = $this->session->flashdata( 'msg' );
        $this->load->library( 'form_validation' );
        $this->form_validation->set_rules( 'name', 'Name', 'required|trim' );
        //$this->form_validation->set_rules( 'mobile', 'Mobile', 'required|trim' );
        //$this->form_validation->set_rules( 'email', 'Email', 'required|trim|valid_email' );

        if ( $this->form_validation->run() === TRUE ) {
            $upData['name'] = trim( $this->input->post( 'name' ) );
            $upData['mobile'] = trim( $this->input->post( 'mobile' ) );
            $upData['email'] = trim( $this->input->post( 'email' ) );
            $upData['position'] = trim( $this->input->post( 'position' ) );
            $this->tbl_generic_model->edit( 'about_us_user', $upData, array( 'auu_id' => $id ) );
            $this->_upload( $id );
            $status = 'success';
            $msg = 'Successfully Updated';
            $this->session->set_flashdata( 'status', $status );
            $this->session->set_flashdata( 'msg', $msg );
            redirect( base_url().'admin/'.$this->controller );
        }
        $data['msg'] = $this->template->getMessage( $status, $msg );
        $data['editData'] = $this->tbl_generic_model->get( 'about_us_user', '*', array( 'auu_id' => $id ) );
        $this->template->setTitle( 'Admin : About us User Edit' );
        $this->template->setLayout( 'dashboard' );

        $this->template->homeAdminRender( $this->controller.'/edit', $data );
    }

    private function _upload( $auu_id = 0 ) {
        $config['upload_path']          = UPLOAD_ABOUT_US_USER;
        $config['allowed_types']        = 'gif|jpg|png';
        /*$config['max_size']             = 100;
        $config['max_width']            = 1024;
        $config['max_height']           = 768;
        */
        $config['file_name']            = date( 'YmdHis' ).$auu_id;
        $this->load->library( 'upload', $config );
        $this->load->library( 'image_lib' );
        if ( $_FILES && $_FILES['myFile']['name'] != '' ) {
            if ( ! $this->upload->do_upload( 'myFile' ) ) {
                $this->session->set_flashdata( 'status', 'danger' );
                $this->session->set_flashdata( 'msg', $this->upload->display_errors() );
                redirect( base_url().'admin/'.$this->controller.'/edit/'.$auu_id );
            } else {
                if ( $this->input->post( 'exist_file' ) != '' ) {
                    @unlink( UPLOAD_ABOUT_US_USER.trim( $this->input->post( 'exist_file' ) ) );
                    @unlink( UPLOAD_ABOUT_US_USER.'thumb/'.trim( $this->input->post( 'exist_file' ) ) );
                }
                $inData['img'] = $path = $this->upload->data( 'file_name' );
                $where['auu_id'] = $auu_id;
                $this->_resizeImage( $path );
                $this->tbl_generic_model->edit( 'about_us_user', $inData, $where );
            }
        }
    }

    private function _resizeImage( $imageName = '' ) {
        $config['image_library'] = 'gd2';
        $config['source_image'] = UPLOAD_ABOUT_US_USER.$imageName;
        $config['new_image'] = UPLOAD_ABOUT_US_USER.'thumb';
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = 300;
        $config['height']       = 150;
        $this->load->library( 'image_lib' );
        $this->image_lib->initialize( $config );
        $this->image_lib->resize();
    }

}