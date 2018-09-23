<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends MX_Controller 
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
        $data['page_title'] = 'User';
        $data['status'] = 0;
        $msg = '';
        $data['msg'] = $this->template->getMessage($data['status'],$msg);
        $data['list'] = $this->user_model->listing();
        $data['controller'] = $this->controller;

        $this->template->setTitle('Admin : User');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/index',$data);
    }

    public function adduser() 
    {
        $data = array();
        $data['page_title'] = 'AddUser';
        $data['status'] = 0;
        $msg = '';
        $data['list'] = $this->user_model->role_details();
        $data['msg'] = $this->template->getMessage($data['status'],$msg);
        $this->template->setTitle('AddUser');
        $this->template->setLayout('home');    
        $this->template->loginAdminRender('user/adduser',$data);
    }

    public function save() 
    { 
        $this->load->model('user_model');
        $data = array(
               'first_name'   => $this->input->post('first_name'),
               'last_name'    => $this->input->post('last_name'),
               'email'        => $this->input->post('email'),
               'password'     => $this->input->post('password'),
               'phone_no'     => $this->input->post('phone_no'),
               'role_id'      => $this->input->post('role_id')
                );
        $this->user_model->add_user($data);
        //$this->session->set_flashdata('msg', 'Successfully Added'); 
        redirect(base_url().$this->adminName.'/user');
    }

    public function edit() 
    { 

        $data = array();
        $data['page_title'] = 'EditUser';
        $data['status'] = 0;
        $msg = '';
        $data['msg'] = $this->template->getMessage($data['status'],$msg);

        $id = $this->uri->segment(4);
        $data['result'] = $this->user_model->details($id);
        $data['list'] = $this->user_model->role_details();
        $this->template->setTitle('EditUser');
        $this->template->setLayout('home');
        $this->template->loginAdminRender('user/edit',$data);

    }

    public function edituser()
    {
        $user_id = $this->input->post('user_id');
        $data = array(
               'first_name'   => $this->input->post('first_name'),
               'last_name'    => $this->input->post('last_name'),
               'email'        => $this->input->post('email'),
               'phone_no'     => $this->input->post('phone_no'),
               'role_id'      => $this->input->post('role_id')
                );
        $this->user_model->edit_user($data,$user_id);
        //$this->session->set_flashdata('msg', 'Successfully Update'); 
        redirect(base_url().$this->adminName.'/user');
    }

    public function delete() 
    { 
        $this->load->model('user_model');
        $id = $this->uri->segment(4);
        $this->user_model->user_delete($id);
        $this->session->set_flashdata('msg', 'Successfully Deleted'); 
        redirect(base_url().'admin/user');
    }

    public function ajax_list()
    {
        if(!IS_AJAX){die(DIRECT_ACCESS_MSG);} 
        $list = $this->user_model->get_datatables();
        $url = base_url().$this->adminName.'/user';
        $data = array();
        $no = $_POST['start'];
        //'.$url.'/edit/'.$user->user_id.'
        //'.$url.'/delete/'.$user->user_id.'

        foreach ($list as $user) {
            $str = '<a href=""><span class="btn btn-primary">Edit</span></a>';
            $str .= '<a href="" onclick="" class="userDelete" userId="'.$user->user_id.'"><span class="btn btn-primary">Delete</span></a>';
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $user->full_name;
            $row[] = $user->email;
            $row[] = $user->mobile_no;
            $row[] = $user->user_status;
            $row[] = $user->user_category;
            $row[] = $str;
 
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->user_model->count_all(),
                        "recordsFiltered" => $this->user_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }

    public function Ajax_CheckEmail()
    {
        if(!IS_AJAX){die(DIRECT_ACCESS_MSG);} 
        $email = $this->input->post('email');

        if(isset($email))
        {
        //echo $email;exit;
        $result = $this->user_model->check_email($email);
        //echo $result;
        
        if($result == 0)
        {
            $isAvailable = true;
        }else{
            $isAvailable = False;
        }   
        // Finally, return a JSON
        echo json_encode(array(
        'valid' => $isAvailable,
        ));
        }
    }

    public function sends(){
        $this->tbl_generic_model->sendEmail('paltua@gmail.com','Test','OK');
    }

    public function changeStatus(){
        $am_id = $this->input->post('am_id');
        $data['msg'] = $this->template->getMessage('success', 'Successfully changed the status.');
        $this->user_model->changeStatus($am_id);
        echo json_encode($data);
    }
     
}