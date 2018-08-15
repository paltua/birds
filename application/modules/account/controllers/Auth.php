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
            $this->form_validation->set_rules('user_master[email]','Email','trim|required');
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
            $this->form_validation->set_rules('user_master[email]','Email','trim|required');
            $this->form_validation->set_rules('user_master[password]','Password','trim|required');
            if($this->form_validation->run() === TRUE){
                
            }else{
                $status = 'danger';
                $msg = validation_errors();
            }
        }
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Login');
        $this->template->setLayout('login');
        $this->template->loginRender('auth/registration', $data);
        //$this->load->view('auth/login', $data);
    }

    private function _setRedirectRule(){
        $user_id = $this->session->userdata('user_id');
        if($user_id){
            redirect(base_url('account/dashboard'));
        }else{  
            redirect(base_url('account/auth/login'),'refresh');
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
                        $msg = 'Password changed successfully. Click <a href="'.base_url().'""> here </a> to login.';
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
        redirect(base_url('account/auth/login'),'refresh');
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
            
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        // More headers
        $headers .= 'From: '.SITENAME.' Notifications <'.SUPPORTEMAIL.'>';        
        
        if( @mail( $mailid, 'Password Reset Mail ', $msgbody, $headers ) ){            
            return true;            
        }else{            
            return false;            
        }
        
    }
    public function getPassword($pwd = ''){
        $newPwd = '';
        if($pwd != ''){
            $salt = $this->config->item('encryption_key');
            $cost = $this->config->item('cost');
            $newPwd = password_hash($pwd,PASSWORD_BCRYPT, array('cost'=>$cost,'salt'=>$salt));
        }
        return $newPwd;
    }
}
