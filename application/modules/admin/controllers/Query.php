<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Query extends MX_Controller 
{
     private $_user_id;
     public $adminName;
    
    public function __construct(){
        parent::__construct();
        
         //$this->load->library('Ion_user_auth_admin');
        $this->ion_user_auth_admin->isLoggedIn();
        $this->adminName = ADMIN_NAME;
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        $this->load->model('query_model');
    }
    

    public function index() 
    {
        $data = array();
        $data['page_title'] = 'QueryContent';
        $data['status'] = 0;
        $msg = '';
        $data['msg'] = $this->template->getMessage($data['status'],$msg);
        $this->template->setTitle('QueryContent');
        $this->template->setLayout('home');    
        $this->template->loginAdminRender('query/querycontent',$data);
        
    }
  

    public function ajax_Querylist()
    {
        if(!IS_AJAX){die(DIRECT_ACCESS_MSG);} 
        $list = $this->query_model->get_datatables();
        $url = base_url().$this->adminName.'/query';
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $user) {

            $str = '<button data-toggle="modal" data-target="#myModalNorm" data-aqc_id="'.$user->aqc_id.'" id="getEmail" data-session_id="'.$this->_user_id.'" class="btn btn-sm btn-info">Reply</button>';
            $queryContent = substr(($user->query_content),0,80).
                '...... <button data-toggle="modal" data-target="#emp-modal" data-aqc_id="'.$user->aqc_id.'" id="getEmployee" data-session_id="'.$this->_user_id.'" class="btn btn-sm btn-info"><i class="glyphicon glyphicon-eye-open"></i>View</button>';
    
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $user->name;
            $row[] = $queryContent;
            $row[] = $user->status;
            $row[] = $user->email;
            $row[] = $str;
 
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->query_model->count_all(),
                        "recordsFiltered" => $this->query_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }

    public function ajax_Modallist()
    {
        if(!IS_AJAX){die(DIRECT_ACCESS_MSG);} 
        $aqc_id = $this->input->post('aqc_id');
        $session_id = $this->input->post('session_id');
         $data = array(
               'aum_id'  => $session_id,
               'status'  => 'Read',
               'read_at' => date("Y-m-d H:i:s")
            );
        //echo $session_id ;
        if(isset($aqc_id))
        {
            $this->query_model->Modal_read($session_id,$aqc_id,$data);
            $result = $this->query_model->Modal_details($aqc_id);
            $data = array();
            $data['query_content'] = $result[0]->query_content;
            echo json_encode($data);
        }
    }

    public function ajax_Email()
    {
        if(!IS_AJAX){die(DIRECT_ACCESS_MSG);} 
        $aqc_id = $this->input->post('aqc_id');
        $session_id = $this->input->post('session_id');
         
        
    }

   
     
}