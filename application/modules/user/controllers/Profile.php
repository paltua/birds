<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {
	public $data = array();
	public $controller ;
	
	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->controller = $this->router->fetch_class();
	}

	public function details(){
		$status = '';
		$msg = '';
		$this->data['msg'] = $this->template->getMessage($status,$msg);
        $this->data['loginHtml'] = $this->load->view('profile/details_left', $this->data, TRUE);
        $this->data['registrationHtml'] = $this->load->view('profile/details_right', $this->data, TRUE);
        $this->template->setTitle('My profile');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/details', $this->data);
	}

	public function passwordChanges(){
        $this->data['details'] = $this->tbl_generic_model->get('welspun_ems.page_hit_status','*', array('email !=' => 'admin@etv.com'), array('created_date'=> 'DESC'));
		$this->load->view('profile/index', $this->data);
	}

	
}