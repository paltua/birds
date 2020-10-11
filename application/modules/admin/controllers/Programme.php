<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Programme extends MX_Controller
{
    public $adminName;
    public $data;
    public $action;

    public function __construct()
    {
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->adminName = ADMIN_NAME;
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        $this->controller = $this->router->fetch_class();
        $this->load->model($this->controller . '_model');
        $this->data['page_title'] = 'Programmes';
        $this->data['controller'] = $this->controller;
        $this->data['statusUrl'] = base_url('admin/' . $this->controller . '/changeStatus');
    }

    public function index()
    {
        $this->data['controller'] = $this->controller;
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
        $this->data['msg'] = $this->template->getMessage($status, $msg);
        $this->data['list'] = array();
        $this->data['dataTableUrl'] = base_url('admin/' . $this->controller . '/viewListDataTable');
        $this->template->setTitle('Admin : ' . $this->data['page_title']);
        $this->template->setLayout('dashboard');

        $this->template->homeAdminRender('admin/' . $this->controller . '/index', $this->data);
    }

    public function viewListDataTable()
    {
        $requestData = $this->input->post();
        $columns = array(
            0 => 'PRO.program_title',
            2 => 'PRO.program_short_desc',
            3 => 'PRO.program_status',
            4 => 'PRO.created_date',
        );
        if (!isset($requestData['order'][0]['column'])) {
            $orderBy['col'] = 'PRO.created_date';
            $orderBy['val'] = 'DESC';
        } else {
            $orderBy['col'] = $columns[$requestData['order'][0]['column']];
            $orderBy['val'] = $requestData['order'][0]['dir'];
        }

        $limit['start'] = $requestData['start'];
        $limit['perpage'] = $requestData['length'];
        $searchData = trim($requestData['search']['value']);
        $fetchedData = $this->programme_model->getDataTableData($searchData, $orderBy, $limit);
        $recordsTotal = $fetchedData['recordsTotal'];
        $recordsFiltered = $fetchedData['recordsFiltered'];
        $rowsData = $fetchedData['rowsData'];

        $rows = $this->_getArrayData($rowsData);
        // print_r( $rows );
        // exit;
        $json_data = array(
            'draw' => intval($requestData['draw']), // for every request/draw by clientside, they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            'recordsTotal' => intval($recordsTotal), // total number of records
            'recordsFiltered' => intval($recordsFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
            'data' => $rows  // total data array
        );
        echo json_encode($json_data);
        exit;
    }

    /*
    * function _getArrayData()
    * This function is used to make array for client listing page
    */

    private function _getArrayData($data = array())
    {
        $rows = array();
        if (count($data) > 0) {
            foreach ($data as $value) {
                $actionStr = '';
                // $actionStr .= '<a href="' . base_url() . 'admin/comment/index/' . $value->program_id . '" class="btn btn-success btn-xs"><i class="fa fa-comments"></i> Comments</a>';
                $actionStr .= '<a href="' . base_url() . 'admin/' . $this->controller . '/image/' . $value->program_id . '" class="btn btn-warning btn-xs"><i class="fa fa-picture-o"></i> Image</a>';
                $actionStr .= '<a href="' . base_url() . 'admin/' . $this->controller . '/edit/' . $value->program_id . '" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>';
                $actionStr .= '<a href="' . base_url() . 'admin/' . $this->controller . '/delete/' . $value->program_id . '" class="deleteChange btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>';
                $statusStr = '<a class="statusChange btn btn-' . ($value->is_status == 'active' ? 'info' : 'warning') . ' btn-xs" href="javascript:void(0);" title="Click to change Status" value="' . ($value->is_status == 'active' ? 'unlock' : 'lock') . '" id="status_' . $value->program_id . '" name="' . $value->program_id . '"><i id="i_status_' . $value->program_id . '" class="fa fa-' . ($value->is_status == 'active' ? 'unlock' : 'lock') . '"></i><span id="span_status_' . $value->program_id . '">' . ucfirst($value->is_status) . '</span>';

                $img = '<img height="100" width="200" alt="' . $value->image_alt . '" src="' . base_url('uploads/programme/thumb/' . $value->image_path) . '">';
                $nestedData[] = $value->program_title;
                $nestedData[] = ucfirst($value->program_status);
                $nestedData[] = $img;
                $nestedData[] = $value->program_short_desc;

                $nestedData[] = $statusStr;
                $nestedData[] = date('F j, Y, g:i a', strtotime($value->created_date));
                $nestedData[] = $actionStr;
                $rows[] = $nestedData;
                unset($actionStr);
                unset($statusStr);
                unset($nestedData);
            }
        }
        return $rows;
    }

    public function add()
    {
        $status = '';
        $msg = '';
        $this->action = $this->data['action'] = 'add';
        $this->data['program_id'] = 0;
        $this->add_edit();
        $this->data['editData'] = $this->programme_model->getSingleData($this->data['program_id']);
        $this->data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : ' . ucfirst($this->data['page_title']) . ' ' . ucfirst($this->action));
        $this->template->setLayout('dashboard');
        $this->template->homeAdminRender('admin/' . $this->controller . '/add_edit_form', $this->data);
    }

    public function add_edit()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('program_title', $this->data['page_title'] . ' Title', 'required|trim');
        $this->form_validation->set_rules('program_short_desc', 'Short Description', 'required|trim');
        if ($this->form_validation->run() == TRUE) {
            $masterData = array();
            $masterData['program_title'] = $this->input->post('program_title');
            $masterData['pro_title_url'] = url_title($this->input->post('program_title'), '-', true) . '-' . $this->_getCode();
            $masterData['program_short_desc'] = $this->input->post('program_short_desc');
            $masterData['program_desc'] = $this->input->post('program_desc');
            $masterData['program_about'] = $this->input->post('program_about');
            $masterData['program_status'] = $this->input->post('program_status');
            $masterData['program_objectives'] = $this->input->post('program_objectives');
            $masterData['created_by'] = $this->_user_id;
            if ($this->action == 'add') {
                $program_id = $this->tbl_generic_model->add('programs', $masterData);
            } elseif ($this->action == 'edit') {
                $where['program_id'] = $this->input->post('program_id');
                $this->tbl_generic_model->edit('programs', $masterData, $where);
            }
            $status = 'success';
            if ($this->action == 'add') {
                $msg = 'Successfully Added';
            } else {
                $msg = 'Successfully Updated';
            }
            $this->session->set_flashdata('status', $status);
            $this->session->set_flashdata('msg', $msg);
            redirect(base_url() . 'admin/' . $this->controller);
        }
    }



    private function _getCode()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $randomString = '';
        for ($i = 0; $i < 12; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    public function edit($program_id = 0)
    {
        $status = '';
        $msg = '';
        $this->action = $this->data['action'] = 'edit';
        $this->data['program_id'] = $program_id;
        $this->add_edit();
        $this->data['editData'] = $this->programme_model->getSingleData($this->data['program_id']);
        $this->data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : ' . ucfirst($this->data['page_title']) . ' ' . ucfirst($this->action));
        $this->template->setLayout('dashboard');
        $this->template->homeAdminRender('admin/' . $this->controller . '/add_edit_form', $this->data);
    }

    public function changeStatus()
    {
        $am_id = $this->input->post('am_id');
        // blog id
        $this->updateActionStatusDelete($am_id, 'status');
        $data['msg'] = $this->template->getMessage('success', 'Successfully changed the status .');
        echo json_encode($data);
    }

    public function delete($id = 0)
    {
        $status = 'success';
        $msg = 'Successfully Deleted';
        $this->updateActionStatusDelete($id, 'delete');
        $this->session->set_flashdata('status', $status);
        $this->session->set_flashdata('msg', $msg);
        redirect(base_url() . 'admin/' . $this->controller);
    }

    public function updateActionStatusDelete($program_id = 0, $action = 'delete')
    {
        if ($program_id > 0) {
            $editData = $this->programme_model->getSingleData($program_id);
            $masterData = array();
            if ($action == 'delete') {
                $masterData['is_status'] = 'delete';
            } else {
                $masterData['is_status'] = $editData[0]->is_status == 'active' ? 'inactive' : 'active';
            }
            $masterData['last_update_date'] = date_format(date_create(), "Y-m-d H:i:s");
            $where['program_id'] = $program_id;
            $this->tbl_generic_model->edit('programs', $masterData, $where);
        }
    }

    public function image($program_id = 0)
    {
        $data = array();
        $data['controller'] = $this->controller;
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');

        $data['editData'] = $this->programme_model->getSingle($program_id);
        $data['page_title'] = ucfirst($this->controller) . ' Images of ' . $data['editData'][0]->program_title;
        $this->_upload($program_id);
        $data['program_id'] = $program_id;
        $data['list'] = $this->programme_model->getImageList($program_id);
        $data['msg'] = '';
        if ($msg != '') {
            $data['msg'] = $this->template->getMessage($status, $msg);
        }
        $data['defaultPath'] = base_url('admin/' . $this->controller . '/setDefaultImage');
        $this->template->setTitle('Admin : ' . $data['page_title']);
        $this->template->setLayout('dashboard');

        $this->template->homeAdminRender('admin/' . $this->controller . '/image', $data);
    }

    private function _upload($program_id = 0)
    {
        $config['upload_path']          = UPLOAD_PROG_PATH;
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $config['min_width']            = 500;
        $config['min_height']           = 250;
        $config['file_name']            = date('YmdHis') . $program_id;
        $this->load->library('upload', $config);
        $this->load->library('image_lib');
        if ($_FILES) {
            if (!$this->upload->do_upload('myFile')) {
                $this->session->set_flashdata('status', 'danger');
                $this->session->set_flashdata('msg', $this->upload->display_errors());
            } else {
                $this->session->set_flashdata('status', 'success');
                $this->session->set_flashdata('msg', 'Successfully Uploaded');
                $inData['prog_img_name'] = $path = $this->upload->data('file_name');
                $inData['program_id'] = $program_id;
                $this->image_lib->clear();
                $this->_resizeImage($path, '1000', '500', '');
                $this->_resizeImage($path, '250', '125', 'thumb');
                $this->tbl_generic_model->add('programs_images', $inData);
            }
            redirect(base_url() . 'admin/' . $this->controller . '/image/' . $program_id);
        }
    }

    private function _resizeImage($imageName = '', $width = '1000', $height = '500', $folder = '')
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = UPLOAD_PROG_PATH . $imageName;
        $config['new_image'] = UPLOAD_PROG_PATH . $folder;
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = FALSE;
        $config['width']         = $width;
        $config['height']       = $height;
        $this->load->library('image_lib');
        $this->image_lib->initialize($config);
        $this->image_lib->resize();
    }

    public function image_delete($prog_img_id = 0)
    {
        $where['prog_img_id'] = $prog_img_id;
        $data = $this->tbl_generic_model->get('programs_images', '*', $where);
        if (!empty($data)) {
            $this->session->set_flashdata('status', 'success');
            $this->session->set_flashdata('msg', 'Successfully Deleted');
            $this->tbl_generic_model->delete('programs_images', $where);
            @unlink(UPLOAD_PROG_PATH . $data[0]->prog_img_name);
            @unlink(UPLOAD_PROG_PATH . 'thumb/' . $data[0]->prog_img_name);
            redirect(base_url() . 'admin/' . $this->controller . '/image/' . $data[0]->program_id);
        } else {
            $this->session->set_flashdata('status', 'danger');
            $this->session->set_flashdata('msg', 'Wrong Parameter');
            redirect(base_url() . 'admin/' . $this->controller . '/index');
        }
    }

    public function setDefaultImage()
    {
        $image_id = $this->input->post('image_id');
        $master_id = $this->input->post('master_id');
        $data['msg'] = $this->template->getMessage('success', 'Successfully set the default Image.');
        $this->programme_model->setDefaultImage($image_id, $master_id);
        echo json_encode($data);
    }
}