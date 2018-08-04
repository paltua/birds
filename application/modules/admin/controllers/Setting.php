<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends MX_Controller {
    #code
    private $_admin_url_con;
    public $perPage;
	private $_action;
    public function __construct(){
        parent::__construct();
        $this->load->library('Admin_auth');
        $this->admin_auth->is_logged_in();
        $this->_admin_url_con = ADMIN_URL_CON;
        $this->perPage = ADMIN_PER_PAGE;
        $this->load->library('Admin_layout');
        $this->load->model('setting_model');
    }

    public function themes(){
    	$data['page_title'] = 'Manage Themes';
		$data['url'] = base_url($this->_admin_url_con.'/');
        $data['status'] = $this->session->flashdata('status');;
        $msg = $this->session->flashdata('msg');
        $data['msg'] = $this->admin_layout->getMessage($data['status'],$msg);
        $where = "setting_id IN(1,2)";
        $data['themeDetails'] = $this->Tbl_generic_model->get("admin_site_settings",'*',$where);
        $this->admin_layout->set_title($data['page_title']);
        $this->admin_layout->setLayout('dashboard');
        $this->admin_layout->render('setting/themes',$data);
    }

    public function others(){
    	$data['page_title'] = 'Manage Others';
        $data['breadcrumb'] = array('home'=>$this->_admin_url_con.'/');
		$data['url'] = base_url($this->_admin_url_con.'/');
        $data['status'] = $this->session->flashdata('status');;
        $msg = $this->session->flashdata('msg');
        
        $this->load->library('form_validation');
        if($this->input->post('data')){
	        $this->form_validation->set_rules('data[3]','Site Title','trim|required');
			$this->form_validation->set_rules('data[4]','Footer Caption','trim|required');
			$this->form_validation->set_rules('data[5]','Contact Number','trim|required');
			$this->form_validation->set_rules('data[6]','From Mail-ID','trim|required|valid_email');
	        if($this->form_validation->run() === TRUE){
	        	$formData = $this->input->post('data');
	        	if(count($formData) > 0){
	        		foreach ($formData as $key => $value) {
	        			$updateData = array();
	        			$updateData['key_value'] = $value;
	        			$updateData['updated_date'] = date('Y-m-d H:i:s');
	        			$where['setting_id'] = $key;
	        			$this->Tbl_generic_model->edit('admin_site_settings',$updateData,$where);
	        		}
	        		$data['status'] = 1;
	        		$msg = 'Data successfully updated.';
	        	}else{
	        		$data['status'] = 2;
	        		$msg = 'Something is wrong.';
	        	}

	        }else{
	        	$data['status'] = 2;
	        	$msg = validation_errors();
	        }
	    }
	    $data['msg'] = $this->admin_layout->getMessage($data['status'],$msg);
        $where = "setting_id IN(3,4,5,6)";
        $data['OtherDetails'] = $this->Tbl_generic_model->get("admin_site_settings",'*',$where);
        $this->admin_layout->set_title($data['page_title']);
        $this->admin_layout->setLayout('dashboard');
        $this->admin_layout->render('setting/others',$data);
    }

    public function email_templates(){
    	$data['page_title'] = 'Manage Email Templates';
        $data['breadcrumb'] = array('home'=>$this->_admin_url_con.'/');
		$data['url'] = base_url($this->_admin_url_con.'/');
        $data['status'] = $this->session->flashdata('status');;
        $msg = $this->session->flashdata('msg');
        $data['msg'] = $this->admin_layout->getMessage($data['status'],$msg);
        $this->admin_layout->set_title($data['page_title']);
        $this->admin_layout->setLayout('dashboard');
        $this->admin_layout->render('setting/email_templates',$data);
    }

    public function upload_logo(){
    	$retData = array();
		$new_name = "mylogo";	
		$this->load->helper('form');
		$this->load->helper("file");

		//set the path where the files uploaded will be copied. NOTE if using linux, set the folder to permission 777
		$config['upload_path'] = 'resources/settings/';

		$config['file_name'] = $new_name;
		$config['allowed_types'] = 'png';
		$config['overwrite'] = TRUE;
		
		//load the upload library
		$this->load->library('upload', $config);
		//if not successful, set the error message        			
		if (!$this->upload->do_upload('logo')) {
			$retData['msg'] = '<div class="alert alert-danger" role="alert"><a aria-label="close" data-dismiss="alert" class="close" href="javascript:void(0)">×</a>'.$this->upload->display_errors().'</div>';
		}else{
			$data['upload_data'] = $this->upload->data();
			$data = array("key_value" => $data['upload_data']['file_name'],"updated_date"=> date('Y-m-d H:i:s'));
			$where = array('setting_id' => 1);
			$this->Tbl_generic_model->edit("admin_site_settings",$data,$where);
			$retData['msg'] = '<div class="alert alert-success" role="alert"><a aria-label="close" data-dismiss="alert" class="close" href="javascript:void(0)">×</a>Logo has been uploaded successfully.</div>';
		}
		echo $retData['msg'];	
	}

	public function upload_css_file(){
		$retData = array();
		$new_name = "mycss";	
		$this->load->helper('form');
		$this->load->helper("file");

		//set the path where the files uploaded will be copied. NOTE if using linux, set the folder to permission 777
		$config['upload_path'] = 'resources/settings/';

		$config['file_name'] = $new_name;
		$config['allowed_types'] = '*';
		$config['overwrite'] = TRUE;
		
		//load the upload library
		$this->load->library('upload', $config);
		//if not successful, set the error message        			
		if (!$this->upload->do_upload('css_file')) {
			$retData['msg'] = '<div class="alert alert-danger" role="alert"><a aria-label="close" data-dismiss="alert" class="close" href="javascript:void(0)">×</a>'.$this->upload->display_errors().'</div>';
		}else{
			$data['upload_data'] = $this->upload->data();
			$data = array("key_value" => $data['upload_data']['file_name'],"updated_date"=> date('Y-m-d H:i:s'));
			$where = array('setting_id' => 2);
			$this->Tbl_generic_model->edit("admin_site_settings",$data,$where);
			$retData['msg'] = '<div class="alert alert-success" role="alert"><a aria-label="close" data-dismiss="alert" class="close" href="javascript:void(0)">×</a>Css file has been uploaded successfully.</div>';
		}
		echo $retData['msg'];
	}

}

