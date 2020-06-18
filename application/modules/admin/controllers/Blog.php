<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends MX_Controller{
    public $adminName;
    public $data;
    public function __construct(){
        parent::__construct();
        $this->adminName = ADMIN_NAME;
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        $this->controller = $this->router->fetch_class();
        $this->load->model($this->controller.'_model');
        $this->data['page_title'] = 'Blog';
    }

    public function index(){
        
        $this->data['controller'] = $this->controller;
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
        $this->data['lang'] = getLanguageArrAnimalMaster();
        $this->data['msg'] = $this->template->getMessage($status, $msg);
        $this->data['list'] = array();//$this->blog_model->getAllData();
        $this->data['dataTableUrl'] = base_url('admin/'.$this->controller.'/viewListDataTable');
        $this->template->setTitle('Admin : '.$this->data['page_title']);
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/index',$this->data);
    }

    public function viewListDataTable(){
        $requestData = $this->input->post();
        $columns = array(
            0 => 'BREV.title',
            2 => 'BREV.short_desc',
            3 => 'BREV.is_status',
            4 => 'BREV.created_date',
        );
        if (!isset($requestData['order'][0]['column'])) {
            $orderBy['col'] = 'BREV.created_date';
            $orderBy['val'] = 'DESC';
        } else {
            $orderBy['col'] = $columns[$requestData['order'][0]['column']];
            $orderBy['val'] = $requestData['order'][0]['dir'];
        }

        $limit['start'] = $requestData['start'];
        $limit['perpage'] = $requestData['length'];
        $searchData = trim($requestData['search']['value']);
        $fetchedData = $this->blog_model->getDataTableData($searchData, $orderBy, $limit);
        $recordsTotal = $fetchedData['recordsTotal'];
        $recordsFiltered = $fetchedData['recordsFiltered'];
        $rowsData = $fetchedData['rowsData'];

        $rows = $this->_getArrayData($rowsData);
        // print_r($rows);
        // exit;
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
            foreach ($data as $value) {
                $actionStr = '';
                $actionStr .= '<a href="'.base_url().'admin/'.$this->controller.'/image/'.$value->blog_id.'" class="btn btn-warning btn-xs"><i class="fa fa-picture-o"></i> Image</a>';
                $actionStr .= '<a href="'.base_url().'admin/'.$this->controller.'/edit/'.$value->blog_id.'" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>';
                $actionStr .= '<a href="'.base_url().'admin/'.$this->controller.'/delete/'.$value->blog_id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>';
                $statusStr = '<a class="statusChange btn btn-'.($value->is_status == "active"?"info":"warning").' btn-xs" href="javascript:void(0);" title="Click to change Status" value="'.($value->is_status == "active"?"unlock":"lock").'" id="status_'.$value->blog_id.'" name="'.$value->blog_id.'"><i id="i_status_'.$value->blog_id.'" class="fa fa-'.($value->is_status == "active"?"unlock":"lock").'"></i><span id="span_status_'.$value->blog_id.'">'.ucfirst($value->is_status).'</span>'; 
                $img .= '<img height="125" width="125" alt="'.$value->image_alt.'" src="'.base_url('uploads/blog/thumb/'.$value->image_path).'">';
                $nestedData[] = $value->title;
                $nestedData[] = $img;
                $nestedData[] = $value->short_desc;   
                $nestedData[] = $statusStr;
                $nestedData[] = date("F j, Y, g:i a", strtotime($value->am_created_date));
                $nestedData[] = $actionStr;
                $rows[] = $nestedData;
                unset($actionStr);unset($statusStr);unset($nestedData);
            }
        }
        return $rows;
    }

    public function add(){
        $am_id = 0;
        $status = '';
        $msg = '';

        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', 'Blog Title', 'required|trim');
        $this->form_validation->set_rules('short_desc', 'Short Description', 'required|trim');
        if ($this->form_validation->run() == TRUE){
            $shortDescArr = $this->input->post('title');
            $priceArr = $this->input->post('amd_price');
            $maData['title'] = $this->input->post('title');
            $maData['title_url'] = url_title($maData['title']);
            $insertId = $this->tbl_generic_model->add('animal_master', $maData);
            $status = 'success';
            $msg = 'Successfully Added';
            $this->session->set_flashdata('status', $status);
            $this->session->set_flashdata('msg', $msg);
            redirect(base_url().'admin/'.$this->controller);            
        }
        $this->data['animal_cat'] = $this->blog_model->getAllAnimalParentCategory(0);
        $this->data['country'] = [];
        $this->data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : '.$this->data['page_title'].' Add');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/add', $this->data);
    }
}