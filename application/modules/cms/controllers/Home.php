<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends MY_Controller 
{
    public $controller;
    public function __construct(){
        parent::__construct();
        $this->load->library('Template');
        $this->controller = 'home';
    }
    
    
    public function index() 
    {
        $data = array();
        $data['page_title'] = 'Home';
        $data['status'] = 0;
        $msg = '';
        //$data['dash'] = $this->_getDashboardArray();
        $data['msg'] = $this->template->getMessage($data['status'], $msg);
        $this->template->setTitle('Home');
        $this->template->setLayout('dashboard');    
        $this->template->homeRender('cms/'.$this->controller.'/index', $data);
    }

    
    
}