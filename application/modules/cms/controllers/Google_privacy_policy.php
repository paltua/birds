<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Google_privacy_policy extends MY_Controller 
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
        $data['category'] = $this->cms_model->getLevelOneCategory();
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('About Us');
        $this->template->setLayout('cms');
        $this->template->homeRender('cms/'.$this->controller.'/index', $data);
    }

    
    
}