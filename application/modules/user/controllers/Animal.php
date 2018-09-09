<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Animal extends MY_Controller {
	public $data = array();
	public $controller;	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->controller = $this->router->fetch_class();
		$this->load->model('cms/cms_model');
	}

	public function index(){
		$this->listing();
	}

	public function listing(){
		$status = '';
		$msg = '';
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('My Listing');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/listing', $data);
	}

	public function add(){
		$status = '';
		$msg = '';
		$data['country'] = $this->tbl_generic_model->getCountryList();
		$data['category'] = $this->cms_model->getLevelOneCategory();
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('My Listing');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/add', $data);
	}

	public function edit(){
        $status = '';
		$msg = '';
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('My Listing');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/edit', $data);
	}

	public function imageListing(){

	}

	public function imageAdd(){

	}


	
}