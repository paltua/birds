<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends MY_Controller {
	public $data = array();
	public $section ;
	public $formHeader ;
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->data['msg'] = '';
	}

	public function index(){
        $this->data['details'] = $this->tbl_generic_model->get('welspun_ems.page_hit_status','*', array('email !=' => 'admin@etv.com'), array('created_date'=> 'DESC'));
		$this->load->view('page/index', $this->data);
	}

	
}