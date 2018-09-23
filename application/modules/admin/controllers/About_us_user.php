<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class About_us_user extends MY_Controller 
{
    public $controller;
    public function __construct(){
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        $this->controller = $this->router->fetch_class();
        //$this->load->model($this->controller.'_model');
    }
    
    
    public function index() 
    {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'About us User';
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
        //$data['lang'] = getLanguageArr();
        $data['msg'] = $this->template->getMessage($status, $msg);
        $data['list'] = $this->tbl_generic_model->get('about_us_user', '*', array());
        $this->template->setTitle('Admin : About us User');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender($this->controller.'/index',$data);
    }

    public function edit($id = 0) {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'About us User';
        $status = '';
        $msg = '';
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        
        if ($this->form_validation->run() == TRUE){
            $upData['name'] = $this->input->post('name');
            $upData['mobile'] = $this->input->post('mobile');
            $upData['email'] = $this->input->post('email');
            $upData['position'] = $this->input->post('position');
            $this->tbl_generic_model->edit('about_us_user', $upData, array('auu_id' => $id));
            //$this->_upload($id);
            $status = 'success';
            $msg = 'Successfully Updated';
            $this->session->set_flashdata('status', $status);
            $this->session->set_flashdata('msg', $msg);
            redirect(base_url().'admin/'.$this->controller);
        }
        $data['msg'] = $this->template->getMessage($status, $msg);
        $data['editData'] = $this->tbl_generic_model->get('about_us_user', '*', array('auu_id' => $id));
        $this->template->setTitle('Admin : About us User Edit');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender($this->controller.'/edit', $data);
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