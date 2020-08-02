<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Settings extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->load->library( 'Template' );
        $this->_user_id = trim( $this->session->userdata( 'aum_id' ) );
        $this->controller = $this->router->fetch_class();
        $this->load->library( 'form_validation' );
    }

    public function index() {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'Dashboard';
        $status = $this->session->flashdata( 'status' );
        $msg = $this->session->flashdata( 'msg' );
        $upStatus = $this->_updateData();
        if ( $upStatus['status'] != '' ) {
            $status = $upStatus['status'];
            $msg = $upStatus['msg'];
        }
        $data['lang'] = getLanguageArr();
        $settings = $this->tbl_generic_model->get( 'settings', '*', array() );
        //pr( $settings );
        if ( count( $settings ) > 0 ) {
            foreach ( $settings as $key => $value ) {
                $data['set'][$value->name] = $value->name_val;
            }
        }
        $data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Admin : Settings' );
        $this->template->setLayout( 'dashboard' );
        $this->template->homeAdminRender( $this->controller.'/index', $data );
    }

    private function _updateData() {
        $updata['updated_by'] = $this->_user_id;
        $where = array();
        $retData['status'] = '';
        $retData['msg'] = '';
        if ( $this->input->post( 'id' ) == 'youtube' ) {
            $where['name'] = 'you_tube_link';
            $updata['name_val'] = trim( $this->input->post( 'you_tube_link' ) );
            $this->tbl_generic_model->edit( 'settings', $updata, $where );
            $retData['status'] = 'success';
            $retData['msg'] = 'Successfully Youtube Link Updated.';
        } elseif ( $this->input->post( 'id' ) == 'about_bird' ) {
            $lang = getLanguageArr();
            foreach ( $lang as $key => $value ) {
                $where['name'] = 'about_bird_'.$key;
                $updata['name_val'] = trim( $this->input->post( 'about_bird_'.$key ) );
                $this->tbl_generic_model->edit( 'settings', $updata, $where );
            }
            $retData['status'] = 'success';
            $retData['msg'] = 'Successfully Know More About Birds Content Updated.';
        } elseif ( $this->input->post( 'id' ) == 'about_us' ) {
            $where['name'] = 'about_us';
            $updata['name_val'] = trim( $this->input->post( 'about_us' ) );
            $this->tbl_generic_model->edit( 'settings', $updata, $where );
            $retData['status'] = 'success';
            $retData['msg'] = 'Successfully About us updated.';
        } elseif ( $this->input->post( 'id' ) == 'pd_charitable_trust' ) {
            $where['name'] = 'pd_charitable_trust';
            $updata['name_val'] = trim( $this->input->post( 'pd_charitable_trust' ) );
            $this->tbl_generic_model->edit( 'settings', $updata, $where );
            $retData['status'] = 'success';
            $retData['msg'] = 'Successfully PD Charitable Trust updated.';
        }

        return $retData;

    }
}