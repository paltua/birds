<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Birds_category extends MY_Controller 
{
    public $controller;
    public function __construct(){
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        $this->controller = 'birds_category';
        $this->load->model('birds_category_model');
    }
    
    
    public function index() 
    {
        $data = array();
        $data['page_title'] = 'Dashboard';
        $data['status'] = 0;
        $msg = '';
        //$data['dash'] = $this->_getDashboardArray();
        $data['lang'] = getLanguageArr();
        $data['msg'] = $this->template->getMessage($data['status'], $msg);
        $this->template->setTitle('Admin : Birds Category');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/index',$data);
    }

    public function add_edit() 
    {
        $data = array();
        $data['page_title'] = 'Dashboard';
        $data['status'] = 0;
        $msg = '';
        //$data['dash'] = $this->_getDashboardArray();
        $data['lang'] = getLanguageArr();
        $data['msg'] = $this->template->getMessage($data['status'], $msg);
        $this->template->setTitle('Admin : Birds Category');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/add_edit',$data);
    }

    
    
}