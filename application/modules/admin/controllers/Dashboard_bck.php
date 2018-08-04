<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Dashboard extends MX_Controller{
    private $_user_id;
    //private $_user_type_id;
    public function __construct(){
        parent::__construct();
        $this->load->library('Ion_user_auth_admin');
        $this->ion_user_auth_admin->isLoggedIn();
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        //$this->_user_type_id = trim($this->session->userdata('user_type_id'));
    }
    
    public function index(){
        echo base_url('admin/dashboard'); die();
        /*$data = array();
        $data['page_title'] = 'Dashboard';
        $data['status'] = 0;
        $msg = '';
        //$data['dash'] = $this->_getDashboardArray();
        $data['msg'] = $this->template->getMessage($data['status'],$msg);
        $this->template->setTitle('Dashboard');
        $this->template->setLayout('dashboard');    
        $this->template->loginRender('auth/home',$data);*/
    }

    private function _getDashboardArray(){
        $data['client']['count'] = $this->tbl_generic_model->countWhere('user_master',array('user_type_id' => 2));
        $data['client']['url'] = base_url().'account/client';
        $data['client']['icon'] = '<i class="fa fa-user" aria-hidden="true"></i>';
        $data['client']['iconClass'] = 'iconPrimary';
        $data['api']['count'] = $this->tbl_generic_model->countWhere('api_master');
        $data['api']['url'] = base_url().'applications/api/dashboard';
        $data['api']['icon'] = '<i class="fa fa-sitemap"></i>';
        $data['api']['iconClass'] = 'iconember';
        $data['subscription']['count'] = $this->tbl_generic_model->countWhere('subscription_master');
        $data['subscription']['url'] = base_url().'applications/subscription/listing';
        $data['subscription']['icon'] = '<i class="fa fa-paper-plane"></i>';
        $data['subscription']['iconClass'] = 'icongreen';
        $data['category']['count'] = $this->tbl_generic_model->countWhere('api_category');
        $data['category']['url'] = base_url().'applications/category/listing';
        $data['category']['icon'] = '<i class="fa fa-cog"></i>';
        $data['category']['iconClass'] = 'iconred';
        return $data;
    }

    
    
}