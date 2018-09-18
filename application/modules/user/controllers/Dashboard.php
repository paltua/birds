<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	public $data = array();
	public $controller;
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->controller = $this->router->fetch_class();
	}

	public function index(){
		$status = '';
		$msg = '';
		$data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('My Listing');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/index', $data);
	}

	public function listing(){
        
	}

	public function add(){
        $this->tbl_generic_model->sendEmail('paltua@gmail.com','Test','OK');
	}

	public function edit(){
        
	}

	public function imageListing(){

	}

	public function imageAdd(){

	}


	
}