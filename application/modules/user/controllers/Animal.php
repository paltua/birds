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
		$this->load->library('form_validation');
        
        $this->form_validation->set_rules('cat_id', 'Category', 'required|trim');
        $this->form_validation->set_rules('amd_name', 'Title', 'required|trim');
        $this->form_validation->set_rules('buy_or_sell', 'Buy or Sell', 'required|trim');
        $this->form_validation->set_rules('amd_short_desc', 'Short Description', 'required|trim'); 
        $this->form_validation->set_rules('country_id', 'Country', 'required|trim'); 
        if ($this->form_validation->run() == TRUE){
        	$am_id = 0;
            $nameArr = $this->input->post('amd_name');
            $nameCheck = modules::load('admin/animal_master/')->name_check($nameArr, $am_id);
            if($nameCheck){
                $shortDescArr = $this->input->post('amd_short_desc');
                $priceArr = $this->input->post('amd_price');
                $maData['am_status'] = 'inactive';
                $maData['am_title'] = url_title($nameArr);
                $maData['am_viewed_count'] = 0;
                $maData['am_user_type'] = 'user';
                $maData['buy_or_sell'] = $this->input->post('buy_or_sell');
                $maData['user_id'] = $this->session->userdata('user_id');
                $insertId = $this->tbl_generic_model->add('animal_master', $maData);
                $this->_updateProductCode($insertId);
                
                $inData[] = array(
                    'language' => 'en',
                    'am_id' => $insertId,
                    'amd_name' => $nameArr,
                    'amd_price' => $priceArr,
                    'amd_short_desc' => $shortDescArr,
                );
                
                $this->tbl_generic_model->add_batch('animal_master_details', $inData);
                $this->_addCategory($insertId);
                $this->_addUpdateLocation($insertId, 'add');
                
                $status = 'success';
                $msg = 'Successfully Added';
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('msg', $msg);
                redirect(base_url().'admin/'.$this->controller);
            }else{
                $status = 'danger';
                $msg = 'This name is already used in English';
            }
        }
        
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

	private function _addCategory($am_id = 0){
        $where['am_id'] = $am_id;
        $this->tbl_generic_model->delete('animal_category_relation', $where);
        $inData = array();
        $p_acr = $this->input->post('cat_id');
        if($p_acr > 0){
            $inData[] = array(
                'am_id' => $am_id,
                'acm_id' => $p_acr
            );
        }
        
        if(count($inData) > 0){
            $this->tbl_generic_model->add_batch('animal_category_relation', $inData);
        }
        return true;
    }

    private function _addUpdateLocation($am_id = 0, $action = 'add'){
        $location['country_id'] = $this->input->post('country_id');
        $location['state_id'] = $this->input->post('state_id');
        $location['city_id'] = $this->input->post('city_id');
        if($action == 'add'){
            $location['am_id'] = $am_id;
            $this->tbl_generic_model->add('animal_location', $location);
        }else{
            $where['am_id'] = $am_id;
            $this->tbl_generic_model->edit('animal_location', $location, $where);
        }
    }

	public function imageListing(){

	}

	public function imageAdd(){

	}


	
}