<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends MY_Controller {
	public $controller;
	
	public function __construct(){
		parent::__construct();
		$this->controller = $this->router->fetch_class();
		$this->load->model('cms/cms_model');
        $this->load->model('product_model');
	}

	public function index(){
		$data['category'] = $this->cms_model->getLevelOneCategory();
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Contact Us');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/index', $data);
	}

	public function search($category_id = 0){
		$data = array();
        $status = '';
        $msg = '';
        $data['selectedCatDet'] = $this->cms_model->getSelectedCategory($category_id);
        $data['prodList'] = $this->product_model->getProductList($category_id);
        $data['minMaxPrice'] = $this->product_model->getMinMaxPrice();
        //pr($data);
		$data['category'] = $this->cms_model->getLevelOneCategory();
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Product Search');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/search', $data);
	}

	public function details($am_id = 0){
		$data = array();
        $status = '';
        $msg = '';
        
        $data['prodDet'] = $this->product_model->getProductDetails($am_id);
        $data['prodImg'] = $this->product_model->getProductImages($am_id);
		$data['category'] = $this->cms_model->getLevelOneCategory();
        $data['comments'] = $this->product_model->getCommentList($am_id);
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Product Search');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/details', $data);
	}

	



	
}