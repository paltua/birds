<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Animal_master extends MY_Controller 
{
    public $controller;
    public function __construct(){
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        $this->controller = $this->router->fetch_class();
        $this->load->model($this->controller.'_model');
    }
    
    
    public function index() 
    {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'Dashboard';
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
        $data['lang'] = getLanguageArr();
        $data['msg'] = $this->template->getMessage($status, $msg);
        $data['list'] = $this->animal_master_model->getAllData();
        //pr($data['list']);
        $data['controller'] = $this->controller;
        $this->template->setTitle('Admin : Animal');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/index',$data);
    }

    public function add() {
        $am_id = 0;
        $data = array();
        $data['page_title'] = 'Dashboard';
        $status = '';
        $msg = '';
        $data['lang'] = getLanguageArr();
        $this->load->library('form_validation');
        foreach($data['lang'] as $key => $value){
            $this->form_validation->set_rules('amd_name['.$key.']', 'Name in '.$value, 'required|trim');
        }
        if ($this->form_validation->run() == TRUE){
            $nameArr = $this->input->post('amd_name');
            $nameCheck = $this->name_check($nameArr['en'], $am_id);
            if($nameCheck){
                $shortDescArr = $this->input->post('amd_short_desc');
                $priceArr = $this->input->post('amd_price');
                $maData['am_status'] = 'active';
                $maData['am_title'] = url_title($nameArr['en']);
                $maData['am_viewed_count'] = 0;
                $insertId = $this->tbl_generic_model->add('animal_master', $maData);
                $inData = array();
                if(count($nameArr)){
                    foreach ($nameArr as $key => $value) {
                        $inData[] = array(
                            'language' => $key,
                            'am_id' => $insertId,
                            'amd_name' => $value,
                            'amd_price' => $priceArr[$key],
                            'amd_short_desc' => $shortDescArr[$key],
                        );
                    }
                    $this->tbl_generic_model->add_batch('animal_master_details', $inData);
                    $this->_addCategory($insertId);
                }
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
        $data['animal_cat'] = $this->animal_master_model->getAllAnimalCategory();
        $data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : Animal ');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/add',$data);
    }

    public function edit($am_id = 0) {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'Dashboard';
        $status = '';
        $msg = '';
        $data['editData'] = $this->animal_master_model->getSingle($am_id);
        $this->load->library('form_validation');
        foreach($data['editData'] as $key => $value){
            $this->form_validation->set_rules('data['.$value->amd_id.'][amd_name]', 'Name in '.$value->lang_name, 'required|trim');
        }
        if ($this->form_validation->run() == TRUE){
            $nameArr = $this->input->post('data');
            $eng_lang_id = $this->input->post('eng_lang_id');
            $nameCheck = $this->name_check($nameArr[$eng_lang_id]['amd_name'], $am_id);
            if($nameCheck){
                $amData['am_title'] = url_title($nameArr[$eng_lang_id]['amd_name']);
                $amWhere['am_id'] = $am_id;
                $this->tbl_generic_model->edit('animal_master', $amData, $amWhere);
                if(count($nameArr)){
                    foreach ($nameArr as $key => $value) {
                        $where = $upData = array();
                        $where['amd_id'] = $key;
                        $upData['amd_name'] = $value['amd_name'];
                        $upData['amd_price'] = $value['amd_price'];
                        $upData['amd_short_desc'] = $value['amd_short_desc'];
                        $this->tbl_generic_model->edit('animal_master_details', $upData, $where);
                    }
                    $this->_addCategory($am_id);
                }
                $status = 'success';
                $msg = 'Successfully Updated';
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('msg', $msg);
                redirect(base_url().'admin/'.$this->controller);
            }else{
                $status = 'danger';
                $msg = 'This name is already used in English';
            }
        }
        $data['animal_cat'] = $this->animal_master_model->getAllAnimalCategory();
        $data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : Animal Category');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/edit',$data);
    }

    private function _addCategory($am_id = 0){
        $where['am_id'] = $am_id;
        $this->tbl_generic_model->delete('animal_category_relation', $where);
        $data = $this->input->post('acr');
        if(count($data) > 0){
            $inData = array();
            foreach ($data as $key => $value) {
                $inData[] = array(
                    'am_id' => $am_id,
                    'acm_id' => $value
                );
            }
            if(count($inData) > 0){
                $this->tbl_generic_model->add_batch('animal_category_relation', $inData);
            }
        }
        return true;
    }

    public function delete($id = 0) {
        $where['acm_id'] = $id;
        $data['acm_is_deleted'] = '1';
        $status = 'success';
        $msg = 'Successfully Deleted';
        $this->tbl_generic_model->edit('animal_category_master', $data, $where);
        $this->session->set_flashdata('status', $status);
        $this->session->set_flashdata('msg', $msg);
        redirect(base_url().'admin/'.$this->controller);
    }


    public function name_check($str, $am_id = 0){
        $data = url_title($str);
        $ret = $this->animal_master_model->check_name_url($data, $am_id);
        if ($ret > 0){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    
    
}