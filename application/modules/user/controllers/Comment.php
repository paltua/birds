<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment extends MY_Controller {
	public $data = array();
	public $section ;
	public $formHeader ;
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
	}

	public function index(){
		$this->listing();
	}

	public function listing(){

	}

	public function add(){
		$inData['user_id'] = $this->session->userdata('user_id');
		$inData['com_parent_id'] = 0;
		$inData['comments'] = "Fantastic2! Please tell me the price.";
		$inData['am_id'] = 2;
		$inData['com_status'] = 'inactive';
		$this->tbl_generic_model->add('comments', $inData);
	}

	public function reply(){

	}

	
}