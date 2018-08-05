<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Animal_category extends MY_Controller 
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
        $data['list'] = $this->animal_category_model->getAllData();
        $this->template->setTitle('Admin : Birds Category');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/index',$data);
    }

    public function add() {
        $data = array();
        $data['page_title'] = 'Dashboard';
        $status = '';
        $msg = '';
        $data['lang'] = getLanguageArr();
        $this->load->library('form_validation');
        foreach($data['lang'] as $key => $value){
            $this->form_validation->set_rules('acmd_name['.$key.']', 'Name in '.$value, 'required|trim');
        }
        if ($this->form_validation->run() == TRUE){
            $nameArr = $this->input->post('acmd_name');
            $nameCheck = $this->name_check($nameArr['en']);
            if($nameCheck){
                $shortDescArr = $this->input->post('acmd_short_desc');
                $maData['acm_status'] = 'active';
                $maData['acm_url_name'] = url_title($nameArr['en']);
                $insertId = $this->tbl_generic_model->add('animal_category_master', $maData);
                $inData = array();
                if(count($nameArr)){
                    foreach ($nameArr as $key => $value) {
                        $inData[] = array(
                            'language' => $key,
                            'acm_id' => $insertId,
                            'acmd_name' => $value,
                            'acmd_short_desc' => $shortDescArr[$key],
                        );
                    }
                    $this->tbl_generic_model->add_batch('animal_category_master_details', $inData);
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
        $data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : Animal Category');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/add',$data);
    }

    public function edit($id = 0) {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'Dashboard';
        $status = '';
        $msg = '';
        $data['editData'] = $this->animal_category_model->getSingle($id);
        $this->load->library('form_validation');
        foreach($data['editData'] as $key => $value){
            $this->form_validation->set_rules('data['.$value->acmd_id.'][acmd_name]', 'Name in '.$value->lang_name, 'required|trim');
        }
        if ($this->form_validation->run() == TRUE){
            $nameArr = $this->input->post('data');
            $eng_lang_id = $this->input->post('eng_lang_id');
            // $nameCheck = $this->name_check($nameArr[$eng_lang_id]['acmd_name']);
            // if($nameCheck){
                if(count($nameArr)){
                    foreach ($nameArr as $key => $value) {
                        $where = $upData = array();
                        $where['acmd_id'] = $key;
                        $upData['acmd_name'] = $value['acmd_name'];
                        $upData['acmd_short_desc'] = $value['acmd_short_desc'];
                        $this->tbl_generic_model->edit('animal_category_master_details', $upData, $where);
                    }
                }
                $status = 'success';
                $msg = 'Successfully Updated';
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('msg', $msg);
                redirect(base_url().'admin/'.$this->controller);
            // }else{
            //     $status = 'danger';
            //     $msg = 'This name is already used in English';
            // }
        }
        $data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : Animal Category');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/edit',$data);
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


    public function name_check($str){
        $data = url_title($str);
        $ret = $this->animal_category_model->check_name_url($data);
        if ($ret > 0){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    
    
}