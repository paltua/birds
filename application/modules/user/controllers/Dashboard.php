<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	public $data = array();
	public $section ;
	public $formHeader ;
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
	}

	public function index(){
		echo '<a href="'.base_url('user/auth/logout').'">logput</a>';
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