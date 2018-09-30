<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Comment extends MX_Controller 
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
        $data['page_title'] = 'Comments';
        $status = $this->session->userdata('status');
        $msg = $this->session->userdata('msg');
        $data['msg'] = $this->template->getMessage($status,$msg);
        $data['dataTableUrl'] = base_url('admin/comment/viewListDataTable');
        $data['controller'] = $this->controller;

        $this->template->setTitle('Admin : Comments');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/index',$data);
    }

    public function viewListDataTable(){
        $requestData = $this->input->post();
        $columns = array(
            0 => 'UM.name',
            1 => 'CM.comments',
            2 => 'AM.am_code',
            3 => 'CM.com_status',
            4 => 'CM.created_date'
        );
        if (!isset($requestData['order'][0]['column'])) {
            $orderBy['col'] = 'CM.created_date';
            $orderBy['val'] = 'DESC';
        } else {
            $orderBy['col'] = $columns[$requestData['order'][0]['column']];
            $orderBy['val'] = $requestData['order'][0]['dir'];
        }

        $limit = array('start' => $requestData['start'], 'perpage' => $requestData['length']);
        $searchData = trim($requestData['search']['value']);
        $where = array();

        $recordsTotal = $this->comment_model->getDataTableTotalCount($where);
        $recordsFiltered = $this->comment_model->getDataTableFilteredCount($searchData, $where);
        $rowsData = $this->comment_model->getDataTableData($searchData, $where, $orderBy, $limit);

        $rows = $this->_getArrayData($rowsData);
        //print_r($rows);
        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval($recordsTotal), // total number of records
            "recordsFiltered" => intval($recordsFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $rows  // total data array
        );
        echo json_encode($json_data);
        exit;
    }

    /*
     * function _getArrayData()
     * This function is used to make array for client listing page
     */

    private function _getArrayData($data = array()) {
        $rows = array();
        if (count($data) > 0) {
            /*pr($data);*/
            foreach ($data as $val) {

                $actionStr = '<a href="'.base_url().'admin/'.$this->controller.'/delete/'.$val->com_id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>';
                
                $statusStr = '<a class="statusChange btn btn-'.($val->com_status == "active"?"info":"warning").' btn-xs" href="javascript:void(0);" title="Click to change Status" value="'.($val->com_status == 'active'?'unlock':'lock').'" id="status_'.$val->com_id.'" name="'.$val->com_id.'"><i id="i_status_'.$val->com_id.'" class="fa fa-'.($val->com_status == 'active'?'unlock':'lock').'"></i><span id="span_status_'.$val->com_id.'">'.ucfirst($val->com_status).'</span></a>'; 
                $nestedData[] = $val->name;
                $nestedData[] = $val->comments; 
                $nestedData[] = '<a href="'.base_url('user/product/details/'.$val->am_id).'" target="_blank">#'.$val->am_code.'</a>';
                $nestedData[] = $statusStr;
                $nestedData[] = date("F j, Y, g:i a", strtotime($val->created_date));
                $nestedData[] = $actionStr;
                $rows[] = $nestedData;
                unset($actionStr);unset($statusStr);unset($nestedData);
            }
        }
        return $rows;
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
        $where['com_id'] = $this->uri->segment(4);
        $this->tbl_generic_model->delete('comments', $where);
        $this->session->set_flashdata('status', 'success');
        $this->session->set_flashdata('msg', 'Successfully Deleted'); 
        redirect(base_url().'admin/comment/index');
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

    public function changeStatus(){
        $am_id = $this->input->post('am_id');
        $data['msg'] = $this->template->getMessage('success', 'Successfully changed the status.');
        $this->comment_model->changeStatus($am_id);
        echo json_encode($data);
    }
     
}