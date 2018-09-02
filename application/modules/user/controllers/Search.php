<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends MY_Controller {
	public $controller;
	
	public function __construct(){
		parent::__construct();
		$this->controller = $this->router->fetch_class();
		$this->load->model('cms/cms_model');
	}

	public function index(){
		$data['category'] = $this->cms_model->getLevelOneCategory();
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Contact Us');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/index', $data);
	}

	public function category($category_id = 0){
		$data = array();
        $status = '';
        $msg = '';
        $data['selectedCatDet'] = $this->cms_model->getSelectedCategory($category_id);
        $data['prodList'] = $this->cms_model->getProductList($category_id);
        $data['prodList'] = $this->cms_model->getProductList($category_id);
        $data['minMaxPrice'] = $this->cms_model->getMinMaxPrice();
        //pr($data);
		$data['category'] = $this->cms_model->getLevelOneCategory();
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Product Search');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/category', $data);
	}



	
}