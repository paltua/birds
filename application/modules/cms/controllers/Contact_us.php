<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contact_us extends MY_Controller 
{
    public $controller;
    public function __construct(){
        parent::__construct();
        $this->load->library('Template');
        $this->load->model('cms_model');
        $this->controller = $this->router->fetch_class();
    }
    
    
    public function index() 
    {
        $data = array();
        $status = '';
        $msg = '';
        $this->load->library('form_validation');
        if($this->input->post()){
            $this->form_validation->set_rules('contact_us[name]','Name','trim|required');
            $this->form_validation->set_rules('contact_us[mobile]','Mobile','trim|required|is_natural_no_zero');
            $this->form_validation->set_rules('contact_us[email]','Email','trim|required|valid_email');
            $this->form_validation->set_rules('contact_us[desccription]','Messages','trim|required');
            if($this->form_validation->run() === TRUE){
                $contact_us = $this->input->post('contact_us');
                $this->tbl_generic_model->add('contact_us', $contact_us);
                $status = 'success';
                $msg = 'Thank you to contact us.One of our executive will contact you as soon as possible.';
                $this->_sendEmailToAdmin($contact_us);
            }
        }
        $data['category'] = $this->cms_model->getLevelOneCategory();
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Contact Us');
        $this->template->setLayout('cms');
        $this->template->homeRender('cms/'.$this->controller.'/index', $data);

        // $data['msg'] = $this->template->getMessage($data['status'], $msg);
        // $this->template->setTitle('Home');
        // $this->template->setLayout('cms');    
        // $this->template->homeRender('cms/'.$this->controller.'/index', $data);
    }

    private function _sendEmailToAdmin($contact_us = array()){
        $data = $contact_us;
        $to = ADMIN_EMAIL;
        $data['to_name'] = ADMIN_NAME;
        $subject = "New Contact Us Request | Parrot Dipankar";
        $body = $this->load->view('cms/'.$this->controller.'/email', $data, TRUE);
        $this->tbl_generic_model->sendEmail($to, $subject, $body, array(), array());
    }

    
    
}