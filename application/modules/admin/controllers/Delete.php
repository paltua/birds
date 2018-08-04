<?php

class Delete extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('Admin_auth');
        $this->admin_auth->is_logged_in();
        $this->_admin_url_con = ADMIN_URL_CON;
        $this->perPage = ADMIN_PER_PAGE;
        $this->load->model('Tbl_client_model');
        $this->load->library('Admin_layout');
    }
    
    function index(){
        $this->motor();
    }

    public function motor() {

        $data['page_title'] = 'Delete';
        $data['breadcrumb'] = array('home' => $this->_admin_url_con . '/');
        $data['msg'] = '';
        $this->load->library('form_validation');
        if ($this->input->post()) {
            $this->_validationRule();
            if ($this->form_validation->run() === TRUE) {
                $motor_id = $this->input->post('motor_id');
                $responce = $this->Tbl_client_model->deleteMotorDetail($motor_id);                
                if ($responce == 1) {
                    $msg = "Deleted Successfully";
                    $data['status'] = 1;
                } else {
                    $msg = "Invalid motor id";
                    $data['status'] = 2;
                }
            }
        }
        
        $data['msg'] = $this->admin_layout->getMessage($data['status'], $msg);
        $this->admin_layout->set_title($data['page_title']);
        $this->admin_layout->setLayout('dashboard');
        $this->admin_layout->render('delete_motor/delete', $data);
    }

    private function _validationRule() {
        $this->form_validation->set_rules('motor_id', 'Motor id', 'trim|required');
    }

}
