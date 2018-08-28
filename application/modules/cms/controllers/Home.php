<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends MY_Controller 
{
    public $controller;
    public function __construct(){
        parent::__construct();
        $this->load->library('Template');
        $this->load->model('cms_model');
        $this->controller = 'home';
    }
    
    
    public function index() 
    {
        $data = array();
        $data['page_title'] = 'Home';
        $data['status'] = 0;
        $msg = '';
        $data['category'] = $this->cms_model->getLevelOneCategory();
        $data['latestProduct'] = $this->cms_model->getLetestProduct();
        $data['premiumProduct'] = $this->cms_model->getPremiumProduct();
        $data['msg'] = $this->template->getMessage($data['status'], $msg);
        $this->template->setTitle('Home');
        $this->template->setLayout('home');    
        $this->template->homeRender('cms/'.$this->controller.'/index', $data);
    }

    
    
}