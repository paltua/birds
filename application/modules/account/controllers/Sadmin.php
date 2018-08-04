<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sadmin extends MY_Controller {

    private $_user_id;
    private $_org_id;
    private $_role_id;
	private $parent_arr =array();

    /*
	 * Constructor
	*/
    public function __construct(){
		parent::__construct();
        $this->load->library('Ion_user_auth');
        $this->ion_user_auth->isLoggedIn();
		//$this->ion_user_auth->permissionControl();
        $this->load->library('Template');
        $this->load->library('form_validation');
        $this->load->model('user_model');
        $this->load->model('tbl_generic_model');
        
        $this->_user_id = $this->session->userdata('user_id');
        $this->_org_id = $this->session->userdata('org_id');
        $this->_role_id = $this->session->userdata('role_id');

		if($this->_role_id != '1') die('No Access');
    }
	

	/*
	* function dashboard()
	* This function is used to show dashboard
	*/
	public function dashboard() 
	{
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
        $data = array();
        $data['msg'] = $this->template->getMessage($status,$msg);

        $this->template->setTitle('Dashboard');
        $this->template->setLayout('dashboard');
        $this->template->render('admin/dashboard', $data);
    }


    /*
	* function dashboard()
	* This function is used to show dashboard
	*/
	public function manageaccount() 
	{
		$status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
		$data = array();
		$data['msg'] = $this->template->getMessage($status,$msg);

		$this->template->setTitle('Manage Accounts');
        $this->template->setLayout('dashboard');
        $this->template->render('admin/listaccounts', $data);
	}



	/*
	 * function dataTable()
	 * This function is used to show account listing using ajax
	*/
    public function dataTable(){
		$requestData = $this->input->post();
		if(count($requestData) > 0) {
			$where['role_master_id'] = 2; // select all webmaster
			$recordsTotal = $this->user_model->getAjax($where);

			$searchData = $requestData['search']['value'];
			//$columns = array( 0 => 'UM.user_name', 1 => 'UM.email');
			$orderBy = array('col'=> $requestData['order'][0]['column'], 'val'=> $requestData['order'][0]['dir']);
			$limit = array('start'=> $requestData['start'], 'perpage'=> $requestData['length']);
			$users = $this->user_model->getAjaxSearch($searchData, $orderBy, $limit, $where);

			$recordsFiltered = $users['total'];
			$rows = $this->_getArrayData($users['rows']);
			$json_data = array(
				"draw"            => intval( $requestData['draw'] ),   // for every request/draw by userside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => intval( $recordsTotal ),  // total number of records
				"recordsFiltered" => intval( $recordsFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => $rows  // total data array
			);
			echo json_encode($json_data);  // send data as json format
		}else{
			die('No direct script access allowed.');
		}
	}
	

	/*
	 * function _getArrayData()
	 * This function is used to make array for user listing page
	*/
	private function _getArrayData($users = array())
	{
		$rows = array(); 
		if(count($users) > 0){
			foreach($users as $key => $val) {
				$nestedData = array();
				$str = '';
				$url = base_url('account/sadmin/viewAccDetails/'.$val->user_id);
				$str = '<a href="'.$url.'" data-toggle="tooltip" data-placement="left" title="Show account details"><span><i class="glyphicon glyphicon-zoom-in"></i></span></a>';
				
				$nestedData[] = $key + 1;
				$nestedData[] = $val->full_name;
				$nestedData[] = $val->user_name;
				$nestedData[] = $val->contact_no;
				$nestedData[] = ucfirst($val->user_status);
				$nestedData[] = $val->created_date;
				$nestedData[] = $val->webcounter;
				$nestedData[] = $str;
				$rows[] = $nestedData;
			}
		}                                       
		return $rows;
	}



	/*
	 * function viewAccDetails($user_id)
	 * This function is used to show listed website detials for an account
	*/
	public function viewAccDetails($user_id = 0)
	{
		$status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
		$data = array();
		$data['msg'] = $this->template->getMessage($status,$msg);

		$fields = array('full_name');
		$where = array('user_id' => $user_id);
		$table = 'user_master';
		$data['user_details'] = $this->tbl_generic_model->get($table, $fields, $where);

		$where = array('user_master_id' => $user_id);
		$data['account_details'] = $this->user_model->getSingle($where);

		//pr($data['user_details']); pr($data['account_details']); die;
		$this->template->setTitle('Account Details of '.$data['user_details'][0]->full_name);
        $this->template->setLayout('dashboard');
        $this->template->render('admin/account_details', $data);
	}
	


	/*
	 * function setpersonal($user_id)
	 * This function is used to set persoanl details of an account
	*/
	public function setpersonal()
	{
		$status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
        $post_data = $this->input->post();
		$data = array();
		$data['msg'] = $this->template->getMessage($status, $msg);

		$fields = array('full_name', 'user_name');
		$where = array('user_id' => $this->session->userdata('user_id'));
		$table = 'user_master';
		$data['user_details'] = $this->tbl_generic_model->get($table, $fields, $where);

		if($post_data) {
			$this->form_validation->set_rules('old_password', 'Old Password', 'required|trim');
			$this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]|matches[confirm_password]');
			$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required|min_length[6]|matches[new_password]');

			if ($this->form_validation->run() == FALSE) {
				$data['haserror'] = true;
				$data['msg'] = $this->template->getMessage('2', validation_errors());
			} else {
				$table = 'user_master';
				$data_pswd = array('pwd' => md5($post_data['new_password']));
				if($user_id = $this->isvalid_old_password($post_data['old_password'])) {
					$where = array('user_id' => $user_id);
					if($this->tbl_generic_model->edit($table, $data_pswd, $where)) {
						$data['is_success'] = true;
						$data['msg'] = $this->template->getMessage('1', 'Password changed successfully.');
					}
				} else {
					$data['haserror'] = true;
					$data['msg'] = $this->template->getMessage('2', 'Old password does not match.');
				}
			}
		} 

		$this->template->setTitle('Personal Settings');
        $this->template->setLayout('dashboard');
        $this->template->render('admin/personal_settings', $data);
	}
	

	/* 
	* function callback_oldPswdCheck()
	/* This function is a custom validation for old password check against the user
	*/
	public function isvalid_old_password($pswd)
	{
		$fields = array('user_id');
		$where = array('user_id' => $this->_user_id, 'pwd' => md5($pswd));
        $table = 'user_master';
        $data = $this->tbl_generic_model->get($table, $fields, $where, 0, 0, true, true);
        $user_id = ! empty($data) ? $data->user_id : null;

        if(empty($user_id)) {
        	$this->form_validation->set_message('isvalid_old_password', 'The {field} does not match with saved password');
            return FALSE;
        } else {
        	return $user_id;
        }
	}


	/* function changePassword()
	/* This function is used to Change Password for the Employer 
	*/
	public function changePassword()
	{
		$data = array();
		$data['haserror'] = false;
		$data['is_success'] = false;
		$post_data = $this->input->post();

		if($post_data) {
			$this->form_validation->set_rules('old_password', 'Old Password', 'required|trim');
			$this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]|matches[confirm_password]');
			$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required|min_length[6]|matches[new_password]');

			if ($this->form_validation->run() == FALSE) {
				$data['haserror'] = true;
				$data['msg'] = $this->template->getMessage('2', validation_errors());
			} else {
				$table = 'user_master';
				$data_pswd = array('pwd' => md5($post_data['new_password']));
				if($user_id = $this->isvalid_old_password($post_data['old_password'])) {
					$where = array('user_id' => $user_id);
					if($this->tbl_generic_model->edit($table, $data_pswd, $where)) {
						$data['is_success'] = true;
						$data['msg'] = $this->template->getMessage('1', 'Password changed successfully.');
					}
				} else {
					$data['haserror'] = true;
					$data['msg'] = $this->template->getMessage('2', 'Old password does not match.');
				}
			}
		} 

		$this->template->setTitle('Change Password');
        $this->template->setLayout('dashboard');
        $this->template->render('admin/personal_settings', $data);
	}


	/* function createWebMaster()
	/* This function is used to create a web master
	*/
	public function createWebMaster()
	{
		$data = array();
		$data['haserror'] = false;
		$data['is_success'] = false;
		$data['msg'] = null;

		$this->load->library('form_validation');
        if($this->input->post()){
            $this->form_validation->set_rules('full_name','Name','trim|required');
			$this->form_validation->set_rules('user_name','Email','trim|required|valid_email');
			$this->form_validation->set_rules('contact_no','Contact Number','trim|required|numeric|min_length[6]');
			$this->form_validation->set_rules('pwd','Password','trim|required|min_length[6]|max_length[12]');
            if($this->form_validation->run() === TRUE){
                $retData = $this->_doAdd();
				$data['is_success'] = $retData['status'] == '1' ? true : false;
				$data['msg'] = $this->template->getMessage($retData['status'], $retData['msg']);
            } else{
            	$data['haserror'] = true;
				$data['msg'] = $this->template->getMessage('2', validation_errors());
            }
        }

		$this->template->setTitle('Create Account');
        $this->template->setLayout('dashboard');
        $this->template->render('admin/addaccount', $data);
	}


	private function _doAdd()
	{
		$data['status'] = 0;
		$msg = '';
		$user_master = $this->input->post();
		$check = $this->_check_unique_email($user_master['user_name']);
		if($check == 1) {
				$user_data = array();
				$user_data['pwd'] = md5($user_master['pwd']);
				$user_data['role_master_id'] = '2';
				$user_data['user_name'] = $user_master['user_name'];
				$user_data['full_name'] = $user_master['full_name'];
				$user_data['contact_no'] = $user_master['contact_no'];
				$user_master_id = $this->tbl_generic_model->add('user_master', $user_data);
				if($user_master_id > 0){
					//$data['status'] = 1;
					//$msg = "Account Successfully Created.";

					$data['status'] = 1;
					$msg = "Account Successfully Created.";
					$this->session->set_flashdata('status',$data['status']);
					$this->session->set_flashdata('msg',$msg);

					redirect(base_url().'account/sadmin/manageaccount');
				}else{
					$data['status'] = 2;
					$msg = "Something went wrong. Please try again.";
					$where['client_id'] = $org_master_id;
					$this->tbl_generic_model->delete('org_master',$where);
				}                        
			
		} else {
			$data['status'] = 2;
			$msg = "This email is already registered with us.";
		}
		$retData['status'] = $data['status'];
		$retData['msg'] = $msg;
		return $retData;
	}



	private function _check_unique_email($email)
	{
        $tbl = 'user_master';
        $fields = 'user_id';
        $where['user_name'] = trim($email);
		/*if($this->_action == 'edit'){
			$where['user_id != '] = trim($this->input->post('account_user_id'));
		}*/
        $user = $this->tbl_generic_model->get($tbl,$fields,$where);
        if(count($user) > 0){
			return 2;
        }else{
            return 1;
        }
    }


	/* function checkParam()
	 * This function is used to check the parameter is int and not null
	*/
    public function checkParam($id = 0){
		if($id == '' || $id <= 0){
			//log_message('Debug', 'parameter is not correct.');
			//show_error('parameter is not correct.','500');
			$this->badRequest();
		}
	}

	
	/*
	 * function badRequest()
	 * This function is used to show bad request message
	*/
	public function badRequest(){
		$this->session->set_flashdata('status',2);
		$this->session->set_flashdata('msg','Sorry! Bad request.');
		redirect(base_url('account/sadmin'));
	}
}
