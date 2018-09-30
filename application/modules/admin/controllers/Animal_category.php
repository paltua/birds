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
        $this->template->setTitle('Admin : Product Types');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/index',$data);
    }

    public function add() {
        $acm_id = 0;
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
            $nameCheck = $this->name_check($nameArr['en'], $acm_id);
            if($nameCheck){
                $shortDescArr = $this->input->post('acmd_short_desc');
                $maData['acm_status'] = 'active';
                $maData['acm_url_name'] = url_title($nameArr['en']);
                $maData['parent_id'] = $this->input->post('parent_id_en');
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
                $this->_upload($insertId);
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
        //$data['parentCat'] = $this->animal_category_model->getParent(0);
        $data['parentCat'] = $this->_getParentCatArr();
        $data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : Product Types Add');
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
        if($_FILES['name'] != ''){
            if ($this->form_validation->run() == TRUE){
                $nameArr = $this->input->post('data');
                $eng_lang_id = $this->input->post('eng_lang_id');
                $nameCheck = $this->name_check($nameArr[$eng_lang_id]['acmd_name'], $id);
                if($nameCheck){
                    if(count($nameArr)){
                        $maWhere['acm_id'] = $id;
                        $maData['acm_url_name'] = url_title($nameArr[$eng_lang_id]['acmd_name']);
                        $maData['parent_id'] = $this->input->post('parent_id_en');
                        $this->tbl_generic_model->edit('animal_category_master', $maData, $maWhere);
                        foreach ($nameArr as $key => $value) {
                            $where = $upData = array();
                            $where['acmd_id'] = $key;
                            $upData['acmd_name'] = $value['acmd_name'];
                            $upData['acmd_short_desc'] = $value['acmd_short_desc'];
                            $this->tbl_generic_model->edit('animal_category_master_details', $upData, $where);
                        }
                    }
                    $this->_upload($id);
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
        }elseif($_FILES && $_FILES['name'] == ''){
            $status = 'danger';
            $msg = 'Please Select Category Image.';
        }
        $data['msg'] = $this->template->getMessage($status, $msg);
        //$data['parentCat'] = $this->animal_category_model->getParent(0);
        $data['parentCat'] = $this->_getParentCatArr();
        $this->template->setTitle('Admin : Product Types Edit');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/edit',$data);
    }

    private function _getParentCatArr(){
        $data = $this->animal_category_model->getParent(0);
        $retData = array();
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $retData[$value->parent_id][$value->acm_id] = $value->acmd_name;
            }
        }
        return $retData;
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

    private function _upload($acm_id = 0){
        $config['upload_path']          = 'uploads/category/';
        $config['allowed_types']        = 'gif|jpg|png';
        /*$config['max_size']             = 100;
        $config['max_width']            = 1024;
        $config['max_height']           = 768;*/
        $config['file_name']            = date('YmdHis').$acm_id;
        $this->load->library('upload', $config);
        if($_FILES){
            if ( ! $this->upload->do_upload('image_name')){
                $this->session->set_flashdata('status', 'danger');
                $this->session->set_flashdata('msg', $this->upload->display_errors());
            }else{
                $this->session->set_flashdata('status', 'success');
                $this->session->set_flashdata('msg', 'Successfully Uploaded');
                $inData['image_name'] = $this->upload->data('file_name');

                $where['acm_id'] = $acm_id;
                $data = $this->tbl_generic_model->get('animal_category_master','*', $where);
                if($data[0]->image_name != ''){
                    @unlink('uploads/category/'.$data[0]->image_name);
                }
                $this->tbl_generic_model->edit('animal_category_master', $inData, $where);
            }
        }
    }


    public function name_check($str ='', $acm_id = 0){
        $data = url_title($str);
        $ret = $this->animal_category_model->check_name_url($data, $acm_id);
        if ($ret > 0){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public function changeStatus(){
        $am_id = $this->input->post('am_id');
        $data['msg'] = $this->template->getMessage('success', 'Successfully changed the status.');
        $this->animal_category_model->changeStatus($am_id);
        echo json_encode($data);
    }

    
    
}