<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }
    
    public function index()
    {
        $this->ion_user_auth->isLogIn();
        
        $this->login();
    }
    
    public function login1(){        
        
        $this->load->view('auth/maintenance');
    }

    public function login(){
        
        $this->ion_user_auth->isLogIn();
        $data = array();
        $status = '';
        $msg = '';
        $this->load->library('form_validation');
        if($this->input->post()){
            $this->form_validation->set_rules('user_master[email]','Email','trim|required|valid_email');
            $this->form_validation->set_rules('user_master[password]','Password','trim|required');
            if($this->form_validation->run() === TRUE){
                $user_master = $this->input->post('user_master');
                $retData = $this->ion_user_auth->activation($user_master);
                if($retData['status'] != 'danger'){
                 $this->_setRedirectRule();
                }else{
                    $status = $retData['status'];
                    $msg = $retData['msg'];
                }
            }else{
                $status = 'danger';
                $msg = validation_errors();
            }
        }
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Login');
        $this->template->setLayout('login');
        $this->template->loginRender('auth/login', $data);
        //$this->load->view('auth/login', $data);
    }

    public function registration(){
        
        $this->ion_user_auth->isLogIn();
        $data = array();
        $status = '';
        $msg = '';
        $this->load->library('form_validation');
        if($this->input->post()){
            $this->form_validation->set_rules('user_master[name]','Name','trim|required');
            $this->form_validation->set_rules('user_master[mobile]','Mobile','trim|required|is_natural_no_zero');
            $this->form_validation->set_rules('user_master[email]','Email','trim|required|valid_email');
            $this->form_validation->set_rules('password','Password','trim|required');
            $this->form_validation->set_rules('cnfPassword', 'Password Confirmation', 'trim|required|matches[password]');
            if($this->form_validation->run() === TRUE){
                $user_master = $this->input->post('user_master');
                $pwd = $this->input->post('password');
                $user_master['password'] = $this->getPassword($pwd);
                $user_master['um_status'] = 'inactive';
                $user_master['um_deleted'] = '1';
                $user_master['random_unique_id'] = date('Ymdhis').rand(100,999);
                $user_id = $this->tbl_generic_model->add('user_master', $user_master);
                if($user_id > 0){
                    $this->_sendActivateEmail($user_id, $user_master);
                    $status = 'success';
                    $msg = 'Please check your email. A account creation link has been sent to your email.';
                }else{
                    $status = 'danger';
                    $msg = "Something went wrong. Please try again later!";
                }
            }else{
                $status = 'danger';
                $msg = "Please check the error(s) as below.";
            }
        }
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Login');
        $this->template->setLayout('login');
        $this->template->loginRender('auth/registration', $data);
        //$this->load->view('auth/login', $data);
    }

    private function _sendActivateEmail($user_id = 0, $user_master = array()){
        $generatetime = urlencode(base64_encode($user_master['random_unique_id'])) ;
        $url = base_url() . "user/auth/activate/".$generatetime ;
        $viewData['url'] = $url;
        $viewData['full_name'] = $user_master['name'];
        $msgbody = $this->load->view('auth/activateEmail', $viewData,TRUE);
        $to = $user_master['email'];
        $subject = 'Account activation : '.SITENAME;
        $body = $msgbody;
        $this->tbl_generic_model->sendEmail($to, $subject, $body, array(), array());
    }

    public function activate($unique_link_no = 0){
        $where['random_unique_id'] = base64_decode(urldecode($unique_link_no));
        $where['um_status'] = 'inactive';
        $where['um_deleted'] = 1;
        $user_master['um_status'] = 'active';
        $user_master['email_validate_date'] = date('Y-m-d H:i:s');
        $user_master['um_deleted'] = '0';
        $user_master['random_unique_id'] = '';
        $this->tbl_generic_model->edit('user_master', $user_master, $where);
        redirect(base_url('user/auth/login'),'refresh');
    }

    private function _setRedirectRule(){
        $user_id = $this->session->userdata('user_id');
        if($user_id){
            redirect(base_url('user/dashboard'));
        }else{  
            redirect(base_url('user/auth/login'),'refresh');
        }
    }
    
    public function forgotPassword(){
        $this->ion_user_auth->isLogIn();
        $data = array();
        $status = '';
        $msg = '';
        $this->load->library('form_validation');
        if($this->input->post()){
            $this->form_validation->set_rules('email','Email','trim|required|valid_email');
            if($this->form_validation->run() === TRUE){

                $user_email = $this->input->post('email');

                $user_where = array('email' => $user_email);

                $user_data = $this->tbl_generic_model->get('web_user_master','*',$user_where);

                if($user_data){

                    $user_id = $user_data[0]->user_id;
                    $email = $user_data[0]->email;
                    $user_id = urlencode(base64_encode($user_id)) ;
                    $time = strtotime("now");
                    $generatetime = urlencode(base64_encode($time)) ;
                    $url = base_url() . "account/auth/resetPassword/".$user_id."/".$generatetime ;

                    $this->_forgotPasswordEmail($email,$url,$user_data[0]->full_name);

                    $status = 'success';
                    $msg = 'Please check your email. A password creation link has been sent to your email.';

                }else{
                    $status = 'danger';
                    $msg = 'You are not a registered user.';
                }
                
            }else{
                $status ='danger';
                $msg = validation_errors();
            }
        }
        
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Forgot Password');
        $this->template->setLayout('login');
        $this->template->loginRender('auth/forgot_password', $data);

    }

    public function resetPassword(){
        $this->ion_user_auth->isLogIn();
        $data = array();
        $status = '';
        $msg = '';
        $this->load->library('form_validation');

        if($this->input->post()){
            $user_id =$this->input->post('user_id');
            $one_time_check =$this->input->post('one_time_check');
            $user_id = base64_decode(urldecode($user_id));
            $unique_link_no = base64_decode(urldecode($one_time_check));
            $password = $this->input->post('password');          

            $user_fields = '*';
            $user_where = array('user_id' => $user_id);
            $show_pre_data = $this->tbl_generic_model->get('web_user_master',$user_fields,$user_where);
            if(count($show_pre_data) > 0){
                if($show_pre_data[0]->unique_link_no == $unique_link_no){  
                    $status = 'danger';
                    $msg = 'Sorry!! Your reset password link has expired. Please try again.';
                }else{

                    $this->form_validation->set_rules('password', 'Password', 'trim|required');
                    $this->form_validation->set_rules('passconf', 'Password Confirmation', 'trim|required');
                    $this->form_validation->set_rules('password', 'Password', 'required|matches[passconf]');

                    if($this->form_validation->run() === TRUE){
                        $password = modules::load('account/auth/')->getPassword($password);
                        $datasection =  array('password' => $password,'unique_link_no'=>$unique_link_no);
                        $user_where = array('user_id' => $user_id);
                        $deduction_master_add = $this->tbl_generic_model->edit('web_user_master',$datasection,$user_where);
                        
                        $status = 'success';
                        $msg = 'Password changed successfully. Click <a href="'.base_url('user/auth/login').'""> here </a> to login.';
                    }else{
                        $status = 'danger';
                        $msg = validation_errors();
                    }
                }
            }else{
                $status = 'danger';
                $msg = 'Sorry!! Your reset password link has expired. Please try again.';
            }
        }
        
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Reset Password');
        $this->template->setLayout('login');
        $this->template->loginRender('auth/reset_password', $data);
    }
    
    /* 
    * function logout()
    */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('user/auth/login'),'refresh');
    }


    public function expiry(){
        $this->ion_user_auth->isExpired();
        $this->load->view('expiry');
    }

    /*
    * function _sendForgotPswdEmail()
    * This function is used to send forgot new employees
    * In Parameters - Employee Name, Email, Password
    */
    private function _forgotPasswordEmail($mailid, $url ,$full_name){
        $viewData['url'] = $url;
        $viewData['full_name'] = $full_name;
        $msgbody = $this->load->view('auth/forgotPasswordEmail',$viewData,TRUE);
        $to = $mailid;
        $subject = 'Password Reset Mail : '.SITENAME;
        $body = $msgbody;
        $this->tbl_generic_model->sendEmail($to, $subject, $body, array(), array());
    }
    public function getPassword($pwd = ''){
        $newPwd = '';
        if($pwd != ''){
            $cost = $this->config->item('cost');
            $newPwd = password_hash($pwd,PASSWORD_BCRYPT, array('cost'=>$cost));
        }
        return $newPwd;
    }
}
