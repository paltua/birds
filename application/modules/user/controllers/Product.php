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
        $this->search();
	}

	public function search($category_id = 0){
		$data = array();
        $status = '';
        $msg = '';
        print_r($this->input->post());
        $data['keyWord'] = '';
        $data['selectedCatDet'] = $this->cms_model->getSelectedCategory($category_id);
        $data['prodListAll'] = $this->product_model->getProductListALl($category_id);
        $data['prodListComp'] = $this->product_model->getProductListComp($category_id);
        $data['prodListUser'] = $this->product_model->getProductListUser($category_id);
        $data['minMaxPrice'] = $this->product_model->getMinMaxPrice();
        $data['country'] = $this->tbl_generic_model->getCountryList();
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
        $data['am_id'] = $am_id;
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('Product Search');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/details', $data);
	}

    public function getStateList(){
        $country_id = $this->input->post('country_id');
        $childs = $this->input->post('selectedChild');
        $childArr = explode(',', $childs);
        $state = $this->tbl_generic_model->getStateList($country_id);
        $html = '<option value="">Select</option>';
        if(count($state) > 0){
            foreach ($state as $value) {
                $selected = '';
                if(in_array($value->id, $childArr)){
                    $selected = 'selected';
                }
                $html .= '<option value="'.$value->id.'" '.$selected.'>'.$value->name.'</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode($data);
    }

    public function getCityList(){
        $state_id = $this->input->post('state_id');
        $childs = $this->input->post('selectedChild');
        $childArr = explode(',', $childs);
        $city = $this->tbl_generic_model->getCityList($state_id);
        $html = '<option value="">Select</option>';
        if(count($city) > 0){
            foreach ($city as $value) {
                $selected = '';
                if(in_array($value->id, $childArr)){
                    $selected = 'selected';
                }
                $html .= '<option value="'.$value->id.'" '.$selected.'>'.$value->name.'</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode($data);
    }

	



	
}