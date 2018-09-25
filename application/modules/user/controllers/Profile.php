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
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password','Password','trim|required');
        $this->form_validation->set_rules('cnfPassword', 'Password Confirmation', 'trim|required|matches[password]');
        $where['user_id'] = $this->session->userdata('user_id');
        if($this->form_validation->run() === TRUE){
        	$pwd = $this->input->post('password');
        	$user_master['password'] = $this->getPassword($pwd);
        	$this->tbl_generic_model->edit('user_master', $user_master, $where);
        	$status = 'success';
            $msg = 'Successfully Password changed.';
        }else{
        	$status = 'danger';
            $msg = validation_errors();
        }

		$this->data['action'] = 'pChange'; 
		$this->data['details'] = $this->tbl_generic_model->get('user_master', '*', $where);
		$this->data['msg'] = $this->template->getMessage($status,$msg);
        $this->data['loginHtml'] = $this->load->view('profile/details_left', $this->data, TRUE);
        $this->data['registrationHtml'] = $this->load->view('profile/details_right', $this->data, TRUE);
        $this->template->setTitle('My profile');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/details', $this->data);
	}

	public function getPassword($pwd = ''){
        $newPwd = '';
        if($pwd != ''){
            $cost = $this->config->item('cost');
            $newPwd = password_hash($pwd,PASSWORD_BCRYPT, array('cost'=>$cost));
        }
        return $newPwd;
    }

	
}