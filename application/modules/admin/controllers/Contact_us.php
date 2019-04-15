<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contact_us extends MX_Controller 
{
    public $controller;
    
    public function __construct(){
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        $this->controller = $this->router->fetch_class();
        $this->load->model($this->controller.'_model');
    }
    

    public function index() 
    {
        $data = array();
        $data['page_title'] = 'Contact Us';
        $data['status'] = 0;
        $msg = '';
        $data['msg'] = $this->template->getMessage($data['status'],$msg);
        $data['list'] = $this->contact_us_model->listing();
        $data['controller'] = $this->controller;
        $this->template->setTitle('Admin : Contact Us');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/index',$data);
    }

    public function reply($con_id = 0){
        $data = array();
        $data['status'] = 0;
        $msg = '';
        $data['msg'] = $this->template->getMessage($data['status'],$msg);
        $data['con_id'] = $con_id;
        $data['list'] = $this->contact_us_model->getOne($con_id);
        $this->load->view('admin/'.$this->controller.'/reply',$data);
    }

    public function sendEmailToUser($contact_us = array()){
        $message = $inData['comment'] = $this->input->post('message');
        $user_email = $this->input->post('user_email');
        $user_name = $this->input->post('user_name');
        $con_id = $inData['con_id'] = $this->input->post('con_id');
        $data['message'] = $message;
        if($data['message'] == ''){
            $status = $data['status'] = 'danger';
            $msg = 'Please provide the Reply comment.';
        }else{
            $to = $user_email;
            $data['to_name'] = $user_name;
            $subject = "Contact Us Reply | Parrot Dipankar";
            $data['email_body'] = $body = $this->load->view('admin/'.$this->controller.'/reply_email', $data, TRUE);
            $inData['created_by'] = $this->_user_id;
            $this->tbl_generic_model->add('contact_us_reply', $inData);
            $this->tbl_generic_model->sendEmail($to, $subject, $body, array(), array());
            $status = $data['status'] = 'success';
            $msg = 'You have successfully replied .';
        }
        $data['msg'] = $this->template->getMessage($status, $msg);
        echo json_encode($data);
    }
}