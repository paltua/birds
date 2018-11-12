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
        $data['latestProduct'] = $this->cms_model->getLetestProduct();// am_pet_choice
        $data['premiumProduct'] = $this->cms_model->getPremiumProduct();// am_food_choice
        $data['dipChoices'] = $this->cms_model->getDipChoicesProduct();
        $data['bestCat'] = $this->cms_model->getBestChoices();
        $data['gallery'] = $this->cms_model->getGalleryList();
        $settings = $this->tbl_generic_model->get('settings','*', array());
        //pr($settings);
        if(count($settings) > 0){
            foreach ($settings as $key => $value) {
                $data['set'][$value->name] = $value->name_val;
            }
        }
        $data['msg'] = $this->template->getMessage($data['status'], $msg);
        $this->template->setTitle('Home');
        $this->template->setLayout('home');    
        $this->template->homeRender('cms/'.$this->controller.'/index', $data);
    }

    public function know_more_about_birds(){
        $data = array();
        $status = '';
        $msg = '';
        $settings = $this->tbl_generic_model->get('settings','*', array());
        //pr($settings);
        if(count($settings) > 0){
            foreach ($settings as $key => $value) {
                $data['set'][$value->name] = $value->name_val;
            }
        }
        $data['category'] = $this->cms_model->getLevelOneCategory();
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('About Us');
        $this->template->setLayout('cms');
        $this->template->homeRender($this->controller.'/know_more_about_birds', $data);
    }

    
    
}