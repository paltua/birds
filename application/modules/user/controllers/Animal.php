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
        $this->load->model('animal_model');
	}

	public function index(){
		$this->listing();
	}

	public function listing(){
		$status = '';
		$msg = '';
        $data['list'] = $this->animal_model->getMyListing();
        //print_r($data['listing']);
        $data['controller'] = $this->controller;
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
        //pr($this->input->post());
        if ($this->form_validation->run() == TRUE){
        	$am_id = 0;
            $nameArr = $this->input->post('amd_name');
            $nameCheck = $this->name_check($nameArr, $am_id);
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
                $this->_upload($insertId);
                
                $status = 'success';
                $msg = 'Thank you to add this details.We will update you after Admin approval.';
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('msg', $msg);
                redirect(base_url().'user/'.$this->controller.'/listing');
            }else{
                $status = 'danger';
                $msg = 'This name is already used.';
            }
        }
        $data['country_id'] = 0;
		$data['country'] = $this->tbl_generic_model->getCountryList();
		$data['category'] = $this->cms_model->getLevelOneCategory();
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('My Listing : Add');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/add', $data);
	}

    private function _upload($am_id = 0){
        $config['upload_path']          = UPLOAD_PROD_PATH;
        $config['allowed_types']        = 'gif|jpg|png';
        /*$config['max_size']             = 100;
        $config['max_width']            = 1024;
        $config['max_height']           = 768;*/
        $config['file_name']            = date('YmdHis').$am_id;
        $this->load->library('upload', $config);
        $default = $this->input->post('default');
        if($_FILES){
            $inData = array();
            for ($i=1; $i < 6 ; $i++) { 
                if ($this->upload->do_upload('ami_path_'.$i)){
                    $inData[$i]['ami_path'] = $this->upload->data('file_name');
                    $inData[$i]['am_id'] = $am_id;
                    if($i == $default){
                        $inData[$i]['ami_default'] = 1;
                    }else{
                        $inData[$i]['ami_default'] = 0;
                    } 
                }
            }
            if(count($inData) > 0){
                $this->tbl_generic_model->add_batch('animal_master_images', $inData);
            }
        }
    }

	public function edit($am_id = 0){
        $status = '';
		$msg = '';
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cat_id', 'Category', 'required|trim');
        $this->form_validation->set_rules('amd_name', 'Title', 'required|trim');
        $this->form_validation->set_rules('buy_or_sell', 'Buy or Sell', 'required|trim');
        $this->form_validation->set_rules('amd_short_desc', 'Short Description', 'required|trim'); 
        $this->form_validation->set_rules('country_id', 'Country', 'required|trim'); 
        //pr($this->input->post());
        $where['am_id'] = $am_id;
        if ($this->form_validation->run() == TRUE){
            $nameArr = $this->input->post('amd_name');
            $nameCheck = $this->name_check($nameArr, $am_id);
            if($nameCheck){
                $shortDescArr = $this->input->post('amd_short_desc');
                $priceArr = $this->input->post('amd_price');
                $maData['am_status'] = 'inactive';
                $maData['am_title'] = url_title($nameArr);
                $maData['am_user_type'] = 'user';
                $maData['buy_or_sell'] = $this->input->post('buy_or_sell');
                $insertId = $this->tbl_generic_model->edit('animal_master', $maData, $where);
                $this->_updateProductCode($am_id);                
                $inData = array(
                    'language' => 'en',
                    'amd_name' => $nameArr,
                    'amd_price' => $priceArr,
                    'amd_short_desc' => $shortDescArr,
                );
                
                $this->tbl_generic_model->edit('animal_master_details', $inData, $where);
                $this->_addCategory($am_id);
                $this->_addUpdateLocation($am_id, 'edit');
                $this->_uploadEdit($am_id);
                
                $status = 'success';
                $msg = 'Thank you to update this details.We will update you after Admin approval.';
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('msg', $msg);
                redirect(base_url().'user/'.$this->controller.'/listing');
            }else{
                $status = 'danger';
                $msg = 'This Title is already used.';
            }
        }
        $data['country_id'] = 0;
        $data['country'] = $this->tbl_generic_model->getCountryList();
        $data['category'] = $this->cms_model->getLevelOneCategory();
        $data['details'] = $this->animal_model->getEditData($am_id);
        $data['images'] = $this->animal_model->getProductImages($am_id);
        //print_r($data['details']);
        $data['msg'] = $this->template->getMessage($status,$msg);
        $this->template->setTitle('My Listing : Edit');
        $this->template->setLayout('cms');
        $this->template->homeRender('user/'.$this->controller.'/edit', $data);
	}

    private function _uploadEdit($am_id = 0){
        $config['upload_path']          = UPLOAD_PROD_PATH;
        $config['allowed_types']        = 'gif|jpg|png';
        /*$config['max_size']             = 100;
        $config['max_width']            = 1024;
        $config['max_height']           = 768;*/
        $config['file_name']            = date('YmdHis').$am_id;
        $this->load->library('upload', $config);
        $default = $this->input->post('default');
        $existImage = $this->input->post('addedImage');
        if($_FILES){
            $inData = array();
            for ($i=1; $i < 6 ; $i++) { 
                if ($this->upload->do_upload('ami_path_'.$i)){
                    $inData[$i]['ami_path'] = $this->upload->data('file_name');
                    $inData[$i]['am_id'] = $am_id;
                    if($i == $default){
                        $inData[$i]['ami_default'] = 1;
                    }else{
                        $inData[$i]['ami_default'] = 0;
                    } 
                    if($existImage[$i] > 0){
                        $ami_id = $existImage[$i];
                        $this->_deleteUnlinkImage($ami_id);
                    }
                }
            }
            if(count($inData) > 0){
                $this->tbl_generic_model->add_batch('animal_master_images', $inData);
            }
        }
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
        $location['country_id'] = is_null($this->input->post('country_id'))?0:$this->input->post('country_id');
        $location['state_id'] = is_null($this->input->post('state_id'))?0:$this->input->post('state_id');
        
        $location['city_id'] = is_null($this->input->post('city_id'))?0:$this->input->post('city_id') ;
        if($action == 'add'){
            $location['am_id'] = $am_id;
            $this->tbl_generic_model->add('animal_location', $location);
        }else{
            $where['am_id'] = $am_id;
            $this->tbl_generic_model->edit('animal_location', $location, $where);
        }
    }

    public function name_check($str, $am_id = 0){
        $data = url_title($str);
        $ret = $this->animal_model->check_name_url($data, $am_id);
        if ($ret > 0){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    private function _updateProductCode($am_id = 0){
        $lentgh = strlen($am_id);
        $code = "P";
        for ($i=0; $i < 8-$lentgh; $i++) { 
            $code .= '0';
        }
        $inData['am_code'] = $code.$am_id;
        $where['am_id'] = $am_id;
        $this->tbl_generic_model->edit('animal_master', $inData, $where);
    }

    public function deleteImage(){
        $ami_id = $this->input->post('ami_id');
        $retData = array();
        if($ami_id > 0){
            $this->_deleteUnlinkImage($ami_id);
            $retData['msg'] = $this->template->getMessage('success', 'Image Successfully Deleted');
        }else{
            $this->session->set_flashdata('status', 'danger');
            $this->session->set_flashdata('msg', 'Wrong Parameter');
            $retData['msg'] = $this->template->getMessage('danger', 'Wrong Parameter');
        }
        echo json_encode($retData);
    }

    private function _deleteUnlinkImage($ami_id = 0){
        $where['ami_id'] = $ami_id;
        $data = $this->tbl_generic_model->get('animal_master_images','*', $where);
        if(!empty($data)){
            $this->tbl_generic_model->delete('animal_master_images', $where);
            $ami_path = $data[0]->ami_path;
            if($ami_path != ''){
                @unlink(UPLOAD_PROD_PATH.$ami_path);
            }
        }
    }


	
}