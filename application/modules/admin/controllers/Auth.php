<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Auth extends MX_Controller {
    public $adminName;

    public function __construct() {
        parent::__construct();
        $this->adminName = ADMIN_NAME;
        $this->load->library( 'template' );
        $this->load->library( 'ion_user_auth_admin' );
        $this->load->model( 'auth_model' );
    }

    public function index() {
        $this->ion_user_auth_admin->isLogIn();
        $this->login();
    }
    /**
    * This function is used to login for member of account/client/user
    */

    public function login() {
        $this->ion_user_auth_admin->isLogIn();
        $data = array();
        $status = '';
        $msg = '';
        $this->load->library( 'form_validation' );
        if ( $this->input->post() ) {
            $this->form_validation->set_rules( 'admin_user_master[email]', 'Email', 'trim|required' );
            $this->form_validation->set_rules( 'admin_user_master[password]', 'Password', 'required' );
            if ( $this->form_validation->run() === TRUE ) {
                $user_master = $this->input->post( 'admin_user_master' );
                $retData = $this->ion_user_auth_admin->activation( $user_master );
                if ( $retData['status'] != 'danger' ) {
                    $this->_setRedirectRule();
                } else {
                    $status = $retData['status'];
                    $msg = $retData['msg'];
                }
            } else {
                $status = 'danger';
                $msg = validation_errors();
            }
        }
        $data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Login' );
        $this->template->setLayout( 'login' );
        $this->template->loginAdminRender( 'admin/auth/login', $data );

    }
    /**
    * This function is used to set redirect url for member of account/client/user
    */

    private function _setRedirectRule() {
        $aum_id = $this->session->userdata( 'aum_id' );

        if ( $aum_id ) {
            redirect( base_url().$this->adminName.'/dashboard' );
        } else {

            redirect( base_url( $this->adminName.'/auth' ), 'refresh' );
        }
    }

    public function forgotPassword() {
        $this->ion_user_auth_admin->isLogIn();
        $data = array();
        $status = '';
        $msg = '';
        $this->load->library( 'form_validation' );
        if ( $this->input->post() ) {
            $this->form_validation->set_rules( 'email', 'Email', 'trim|required|valid_email' );
            if ( $this->form_validation->run() === TRUE ) {

                $user_email = $this->input->post( 'email' );

                $user_where = array( 'email' => $user_email );

                $user_data = $this->tbl_generic_model->get( 'admin_user_master', '*', $user_where );

                if ( $user_data ) {

                    $aum_id = $user_data[0]->aum_id;
                    $email = $user_data[0]->email;
                    $aum_id = urlencode( base64_encode( $aum_id ) ) ;
                    $time = strtotime( 'now' );
                    $generatetime = urlencode( base64_encode( $time ) ) ;
                    $url = base_url( ADMIN_NAME.'/auth/resetPassword/' ).$aum_id.'/'.$generatetime ;

                    $this->_forgotPasswordEmail( $email, $url, $user_data[0]->first_name );

                    $status = 'success';
                    $msg = 'Please check your email. A password creation link has been sent to your email.';

                } else {
                    $status = 'danger';
                    $msg = 'You are not a registered user.';
                }

            } else {
                $status = 'danger';
                $msg = validation_errors();
            }
        }

        $data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Forgot Password' );
        $this->template->setLayout( 'login' );
        $this->template->loginAdminRender( 'admin/auth/forgot_password', $data );

    }

    public function resetPassword() {
        $this->ion_user_auth_admin->isLogIn();
        $data = array();
        $status = 0;
        $msg = '';
        $this->load->library( 'form_validation' );

        if ( $this->input->post() ) {
            $aum_id = $this->input->post( 'aum_id' );
            $one_time_check = $this->input->post( 'one_time_check' );
            $aum_id = base64_decode( urldecode( $aum_id ) );
            $unique_link_no = base64_decode( urldecode( $one_time_check ) );
            $password = $this->input->post( 'password' );

            $user_fields = '*';
            $user_where = array( 'aum_id' => $aum_id );
            $show_pre_data = $this->tbl_generic_model->get( 'admin_user_master', $user_fields, $user_where );
            if ( count( $show_pre_data ) > 0 ) {
                if ( $show_pre_data[0]->unique_link_no == $unique_link_no ) {

                    $status = 'danger';
                    $msg = 'Sorry!! Your reset password link has expired. Please try again.';
                } else {

                    $this->form_validation->set_rules( 'password', 'Password', 'trim|required' );
                    $this->form_validation->set_rules( 'passconf', 'Password Confirmation', 'trim|required' );
                    $this->form_validation->set_rules( 'password', 'Password', 'required|matches[passconf]' );

                    if ( $this->form_validation->run() === TRUE ) {
                        $password = modules::load( 'admin/auth/' )->getPassword( $password );
                        $datasection =  array( 'password' => $password, 'unique_link_no'=>$unique_link_no );
                        $user_where = array( 'aum_id' => $aum_id );
                        $deduction_master_add = $this->tbl_generic_model->edit( 'admin_user_master', $datasection, $user_where );

                        $status = 'success';
                        $msg = 'Password changed successfully. Click <a href="'.base_url( $this->adminName ).'""> here </a> to login.';
                    } else {
                        $status = 'danger';
                        $msg = validation_errors();
                    }

                }
            } else {
                $status = 'danger';
                $msg = 'Sorry!! Your reset password link has expired. Please try again.';
            }

        }

        $data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Reset Password' );
        $this->template->setLayout( 'login' );
        $this->template->loginAdminRender( 'admin/auth/reset_password', $data );
    }

    private function _forgotPasswordEmail( $mailid, $url, $first_name ) {
        $viewData['url'] = $url;
        $viewData['first_name'] = $first_name;
        $msgbody = $this->load->view( 'admin/auth/forgotPasswordEmail', $viewData, TRUE );

        // Always set content-type when sending HTML email
        $headers = 'MIME-Version: 1.0' . '\r\n';
        $headers .= 'Content-type:text/html;charset=iso-8859-1' . '\r\n';
        // More headers
        $headers .= 'From: '.SITENAME.' Notifications <'.SUPPORTEMAIL.'>';

        if ( @mail( $mailid, 'Password Reset Mail ', $msgbody, $headers ) ) {

            return true;

        } else {

            return false;

        }

    }

    public function getPassword( $pwd = '' ) {
        $newPwd = '';
        if ( $pwd != '' ) {
            //$salt = $this->config->item( 'encryption_key' );
            $cost = $this->config->item( 'cost' );
            $newPwd = password_hash( $pwd, PASSWORD_BCRYPT, array( 'cost'=>$cost ) );
        }
        // '$2y$11$ZGlwYW5rYXIwMy0wOC0yM.ZRHZCPtm0Nqy7faCOHAownyI2jKPNcq'
        echo $newPwd;
        exit;
        return $newPwd;
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect( base_url().$this->adminName, 'refresh' );
    }

}