<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Gallery extends MY_Controller 
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
        $data['gallery'] = $this->tbl_generic_model->get('gallery', '*', array(), array('created_date' => 'DESC'));
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Gallery');
        $this->template->setLayout('cms');
        $this->template->homeRender($this->controller.'/index', $data);
    }
}