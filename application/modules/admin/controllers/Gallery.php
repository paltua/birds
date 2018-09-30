<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Gallery extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        $this->controller = $this->router->fetch_class();
        $this->load->model($this->controller.'_model');
    }
    


    public function index() {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'Gallery';
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
        $this->_upload();
        $data['msg'] = $this->template->getMessage($status, $msg);
        $data['list'] = $this->gallery_model->getAllData();
        $data['controller'] = $this->controller;
        $this->template->setTitle('Admin : Gallery');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/image',$data);
    }


    private function _upload(){
        $config['upload_path']          = 'uploads/gallery/';
        $config['allowed_types']        = 'gif|jpg|png';
        /*$config['max_size']             = 100;
        $config['max_width']            = 1024;
        $config['max_height']           = 768;*/
        $config['file_name']            = date('YmdHis');
        $this->load->library('upload', $config);
        if($_FILES){
            if ( ! $this->upload->do_upload('myFile')){
                $this->session->set_flashdata('status', 'danger');
                $this->session->set_flashdata('msg', $this->upload->display_errors());
            }else{
                $this->session->set_flashdata('status', 'success');
                $this->session->set_flashdata('msg', 'Successfully Uploaded');
                $inData['g_path'] = $this->upload->data('file_name');
                $inData['created_by'] = $this->_user_id;
                $inData['am_id'] = 0;
                $this->tbl_generic_model->add('gallery', $inData);
                $this->_resizeImage($inData['g_path']);
                redirect(base_url().'admin/'.$this->controller.'/index/');
            }
        }
    }

    private function _resizeImage($imageName = ''){
        $config['image_library'] = 'gd2';
        $config['source_image'] = 'uploads/gallery/'.$imageName;
        $config['new_image'] = 'uploads/gallery/thumb';
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = 250;
        $config['height']       = 250;
        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
    }


    public function delete($g_id = 0){
        $where['g_id'] = $g_id;
        $data = $this->tbl_generic_model->get('gallery','*', $where);
        if(!empty($data)){
            $this->session->set_flashdata('status', 'success');
            $this->session->set_flashdata('msg', 'Successfully Deleted');
            $this->tbl_generic_model->delete('gallery', $where);
            if($data[0]->g_path != ''){
                @unlink('uploads/gallery/'.$data[0]->g_path);
                @unlink('uploads/gallery/thumb/'.$data[0]->g_path);
            }
            
            redirect(base_url().'admin/'.$this->controller.'/index/');
        }else{
            $this->session->set_flashdata('status', 'danger');
            $this->session->set_flashdata('msg', 'Wrong Parameter');
            redirect(base_url().'admin/'.$this->controller.'/index');
        }
    }


}
