<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends MY_Controller 
{
    
    public function __construct(){
        parent::__construct();
         
         $this->ion_user_auth_admin->isLoggedIn();
         $this->load->library('Template');
         $this->_user_id = trim($this->session->userdata('aum_id'));
    }
    
    
    public function index() 
    {
        $data = array();
        $data['page_title'] = 'Dashboard';
        $data['status'] = 0;
        $msg = '';
        //$data['dash'] = $this->_getDashboardArray();
        $data['msg'] = $this->template->getMessage($data['status'], $msg);
        $this->template->setTitle('Dashboard');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/auth/home',$data);
    }

    
    
}