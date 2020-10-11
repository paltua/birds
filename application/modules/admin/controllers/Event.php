<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Event extends MX_Controller
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
        $this->data['page_title'] = 'Event';
        $this->data['controller'] = $this->controller;
        $this->data['statusUrl'] = base_url('admin/' . $this->controller . '/changeStatus');
    }

    public function index()
    {
        $this->data['controller'] = $this->controller;
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
        $this->data['lang'] = getLanguageArrAnimalMaster();
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
            0 => 'EML.event_title',
            2 => 'EML.event_short_desc',
            3 => 'EML.is_status',
            4 => 'EML.event_created_date',
        );
        if (!isset($requestData['order'][0]['column'])) {
            $orderBy['col'] = 'EML.event_created_date';
            $orderBy['val'] = 'DESC';
        } else {
            $orderBy['col'] = $columns[$requestData['order'][0]['column']];
            $orderBy['val'] = $requestData['order'][0]['dir'];
        }

        $limit['start'] = $requestData['start'];
        $limit['perpage'] = $requestData['length'];
        $searchData = trim($requestData['search']['value']);
        $fetchedData = $this->event_model->getDataTableData($searchData, $orderBy, $limit);
        $recordsTotal = $fetchedData['recordsTotal'];
        $recordsFiltered = $fetchedData['recordsFiltered'];
        $rowsData = $fetchedData['rowsData'];

        $rows = $this->_getArrayData($rowsData);
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
                // $actionStr .= '<a href="' . base_url() . 'admin/comment/index/' . $value->em_id . '" class="btn btn-success btn-xs"><i class="fa fa-comments"></i> Comments</a>';
                $actionStr .= '<a href="' . base_url() . 'admin/' . $this->controller . '/image/' . $value->em_id . '" class="btn btn-warning btn-xs"><i class="fa fa-picture-o"></i> Image</a>';
                $actionStr .= '<a href="' . base_url() . 'admin/' . $this->controller . '/edit/' . $value->em_id . '" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>';
                $actionStr .= '<a href="' . base_url() . 'admin/' . $this->controller . '/delete/' . $value->em_id . '" class="deleteChange btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>';
                $statusStr = '<a class="statusChange btn btn-' . ($value->event_status == 'active' ? 'info' : 'warning') . ' btn-xs" href="javascript:void(0);" title="Click to change Status" value="' . ($value->event_status == 'active' ? 'unlock' : 'lock') . '" id="status_' . $value->em_id . '" name="' . $value->em_id . '"><i id="i_status_' . $value->em_id . '" class="fa fa-' . ($value->event_status == 'active' ? 'unlock' : 'lock') . '"></i><span id="span_status_' . $value->em_id . '">' . ucfirst($value->event_status) . '</span>';

                $img = '<img height="100" width="200" alt="' . $value->event_title . '" src="' . base_url(UPLOAD_EVENT_PATH . 'thumb/' . $value->image_path) . '">';
                $nestedData[] = $value->event_title;
                $nestedData[] = $img;
                $nestedData[] = $value->event_short_desc;
                $nestedData[] = date('F j, Y, g:i a', strtotime($value->event_start_date_time)) . ' <br> ' . date('F j, Y, g:i a', strtotime($value->event_end_date_time));
                $nestedData[] = $value->location;
                $nestedData[] = $statusStr;
                $nestedData[] = date('F j, Y, g:i a', strtotime($value->event_created_date));
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
        $this->data['controller'] = $this->controller;
        $this->action = $this->data['action'] = 'add';
        $this->data['em_id'] = 0;
        $this->add_edit();
        $this->data['editData'] = $this->event_model->getSingleData($this->data['em_id']);
        $this->data['proAssignData'] =  $this->event_model->getAssignProgramme(0);
        $this->data['proData'] = $this->event_model->getProgramme();
        $this->data['country'] = $this->tbl_generic_model->get('countries', '*');
        $this->data['state'] = [];
        $this->data['city'] = [];
        $this->data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : ' . ucfirst($this->data['page_title']) . ' ' . ucfirst($this->action));
        $this->template->setLayout('dashboard');
        $this->template->homeAdminRender('admin/' . $this->controller . '/add_edit_form', $this->data);
    }

    public function add_edit()
    {
        // print_r($this->input->post());
        $this->load->library('form_validation');
        $this->form_validation->set_rules('event_title', ' Title', 'required|trim');
        $this->form_validation->set_rules('event_short_desc', 'Short Description', 'required|trim');
        $this->form_validation->set_rules('event_start_date_time', 'Event Start Date', 'required|trim');
        $this->form_validation->set_rules('event_end_date_time', 'Event End Date', 'required|trim');
        $this->form_validation->set_rules('country_id', 'Country', 'required|trim');
        $this->form_validation->set_rules('state_id', 'State', 'required|trim');
        $this->form_validation->set_rules('city_id', 'City', 'required|trim');
        $this->form_validation->set_rules('pin', 'Pin', 'required|trim');
        if ($this->form_validation->run() == TRUE) {
            $logData = array();
            $logData['event_title'] = $this->input->post('event_title');
            $logData['event_title_url'] = url_title($this->input->post('event_title'), '-', true) . '-' . $this->_getCode();
            $logData['event_short_desc'] = $this->input->post('event_short_desc');
            $logData['event_start_date_time'] = $this->_prepareDateFormat($this->input->post('event_start_date_time'));
            $logData['event_end_date_time'] = $this->_prepareDateFormat($this->input->post('event_end_date_time'));
            $logData['event_long_desc'] = $this->input->post('event_long_desc');
            $logData['event_about'] = $this->input->post('event_about');
            $logData['event_objectives'] = $this->input->post('event_objectives');
            $logData['em_id'] = $this->input->post('em_id');
            $eml_id = $this->tbl_generic_model->add('event_master_log', $logData);
            if ($this->action == 'add') {
                $masterData['eml_id'] = $eml_id;
                $em_id = $this->tbl_generic_model->add('event_master', $masterData);
                $upData['em_id'] = $em_id;
                $where['eml_id'] = $eml_id;
                $this->tbl_generic_model->edit('event_master_log', $upData, $where);
            } elseif ($this->action == 'edit') {
                $where['em_id'] = $logData['em_id'];
                $upData['eml_id'] = $eml_id;
                $this->tbl_generic_model->edit('event_master', $upData, $where);
            }
            if ($eml_id > 0) {
                $this->_addProgramme($eml_id);
                $this->_addAddress($eml_id);
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

    private function _prepareDateFormat($dates = '')
    {
        return date_format(date_create($dates), "Y-m-d H:i:s");
    }

    private function _addProgramme($eml_id = 0)
    {
        $data = $this->input->post('program_id');
        $inData = array();
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $inData[] = array(
                    'eml_id' => $eml_id,
                    'program_id' => $value
                );
            }
        }
        if (count($inData) > 0) {
            $this->tbl_generic_model->add_batch('event_programs_rel', $inData);
        }
        return true;
    }

    private function _addAddress($eml_id = 0)
    {
        $inData = array(
            'eml_id' => $eml_id,
            'country_id' => $this->input->post('country_id'),
            'state_id' => $this->input->post('state_id'),
            'city_id' => $this->input->post('city_id'),
            'pin' => $this->input->post('pin'),
            'address' => $this->input->post('address'),
        );
        $this->tbl_generic_model->add('event_location', $inData);
    }

    public function getChildCategory()
    {
        $parent_id = $this->input->post('parent_id');
        $childs = $this->input->post('selectedChild');
        $childArr = explode(',', $childs);
        $childCat = $this->event_model->getAllAnimalParentCategory($parent_id);
        $html = '<option value="">Select Some Options</option>';
        if (count($childCat) > 0) {
            foreach ($childCat as $value) {
                $selected = '';
                if (in_array($value->acm_id, $childArr)) {
                    $selected = 'selected';
                }
                $html .= '<option value="' . $value->acm_id . '" ' . $selected . '>' . $value->acmd_name . '</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode($data);
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

    public function edit($em_id = 0)
    {
        $status = '';
        $msg = '';
        $this->action = $this->data['action'] = 'edit';
        $this->data['em_id'] = $em_id;
        $this->add_edit();
        $this->data['editData'] = $this->event_model->getSingleData($this->data['em_id']);
        $this->data['proAssignData'] =  $this->event_model->getAssignProgramme($this->data['editData'][0]->eml_id);
        $this->data['proData'] = $this->event_model->getProgramme();
        $this->data['country'] = $this->tbl_generic_model->get('countries', '*');
        $this->data['state'] = $this->event_model->getStateList($this->data['editData'][0]->country_id);
        $this->data['city'] = $this->event_model->getCityList($this->data['editData'][0]->state_id);;
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

    public function updateActionStatusDelete($em_id = 0, $action = 'delete')
    {
        if ($em_id > 0) {
            $editData = $this->event_model->getSingleData($em_id);
            $logData = array();
            $logData['event_title'] = $editData[0]->event_title;
            $logData['event_title_url'] = url_title($editData[0]->event_title, '-', true) . '-' . $this->_getCode();
            $logData['event_short_desc'] = $editData[0]->event_short_desc;
            $logData['event_start_date_time'] = $this->_prepareDateFormat($editData[0]->event_start_date_time);
            $logData['event_end_date_time'] = $this->_prepareDateFormat($editData[0]->event_end_date_time);
            $logData['event_long_desc'] = $editData[0]->event_long_desc;
            $logData['event_about'] = $editData[0]->event_about;
            $logData['event_objectives'] = $editData[0]->event_objectives;
            $logData['em_id'] = $editData[0]->em_id;
            if ($action == 'delete') {
                $logData['event_status'] = 'delete';
            } else {
                $logData['event_status'] = $editData[0]->event_status == 'active' ? 'inactive' : 'active';
            }
            $eml_id = $this->tbl_generic_model->add('event_master_log', $logData);

            $where['em_id'] = $em_id;
            $upData['eml_id'] = $eml_id;
            $this->tbl_generic_model->edit('event_master', $upData, $where);

            if ($action != 'delete') {
                $inData = array(
                    'eml_id' => $eml_id,
                    'country_id' => $editData[0]->country_id,
                    'state_id' => $editData[0]->state_id,
                    'city_id' => $editData[0]->city_id,
                    'pin' => $editData[0]->pin,
                    'address' => $editData[0]->address,
                );
                $this->tbl_generic_model->add('event_location', $inData);

                $progData = $this->event_model->getAssignProgramme($editData[0]->eml_id);
                if (count($progData) > 0) {
                    $inData = array();
                    foreach ($progData as $key => $value) {
                        $inData[] = array(
                            'eml_id' => $eml_id,
                            'program_id' => $value->program_id
                        );
                    }
                    if (count($inData) > 0) {
                        $this->tbl_generic_model->add_batch('event_programs_rel', $inData);
                    }
                }
            }
        }
    }

    public function image($em_id = 0)
    {
        $data = array();
        $data['controller'] = $this->controller;
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');

        $data['editData'] = $this->event_model->getSingle($em_id);
        $data['page_title'] = ucfirst($this->controller) . ' Images of ' . $data['editData'][0]->event_title;
        $this->_upload($em_id);
        $data['em_id'] = $em_id;
        $data['list'] = $this->event_model->getImageList($em_id);
        $data['msg'] = '';
        if ($msg != '') {
            $data['msg'] = $this->template->getMessage($status, $msg);
        }
        $data['defaultPath'] = base_url('admin/' . $this->controller . '/setDefaultImage');
        $this->template->setTitle('Admin : ' . $data['page_title']);
        $this->template->setLayout('dashboard');
        $this->template->homeAdminRender('admin/' . $this->controller . '/image', $data);
    }

    private function _upload($em_id = 0)
    {
        $config['upload_path']          = UPLOAD_EVENT_PATH;
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $config['min_width']            = 500;
        $config['min_height']           = 250;
        $config['file_name']            = date('YmdHis') . $em_id;
        $this->load->library('upload', $config);
        $this->load->library('image_lib');
        if ($_FILES) {
            if (!$this->upload->do_upload('myFile')) {
                $this->session->set_flashdata('status', 'danger');
                $this->session->set_flashdata('msg', $this->upload->display_errors());
            } else {
                $this->session->set_flashdata('status', 'success');
                $this->session->set_flashdata('msg', 'Successfully Uploaded');
                $inData['ei_image_name'] = $path = $this->upload->data('file_name');
                $inData['em_id'] = $em_id;
                $this->image_lib->clear();
                $this->_resizeImage($path, '1000', '500', '');
                $this->_resizeImage($path, '250', '125', 'thumb');
                $this->tbl_generic_model->add('event_images', $inData);
            }
            redirect(base_url() . 'admin/' . $this->controller . '/image/' . $em_id);
        }
    }

    private function _resizeImage($imageName = '', $width = '1000', $height = '500', $folder = '')
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = UPLOAD_EVENT_PATH . $imageName;
        $config['new_image'] = UPLOAD_EVENT_PATH . $folder;
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = FALSE;
        $config['width']         = $width;
        $config['height']       = $height;
        $this->load->library('image_lib');
        $this->image_lib->initialize($config);
        $this->image_lib->resize();
    }

    public function image_delete($ei_id = 0)
    {
        $where['ei_id'] = $ei_id;
        $data = $this->tbl_generic_model->get('event_images', '*', $where);
        if (!empty($data)) {
            $this->session->set_flashdata('status', 'success');
            $this->session->set_flashdata('msg', 'Successfully Deleted');
            $this->tbl_generic_model->delete('event_images', $where);
            @unlink(UPLOAD_EVENT_PATH . $data[0]->ei_image_name);
            @unlink(UPLOAD_EVENT_PATH . 'thumb/' . $data[0]->ei_image_name);
            redirect(base_url() . 'admin/' . $this->controller . '/image/' . $data[0]->em_id);
        } else {
            $this->session->set_flashdata('status', 'danger');
            $this->session->set_flashdata('msg', 'Wrong Parameter');
            redirect(base_url() . 'admin/' . $this->controller . '/index');
        }
    }

    public function setDefaultImage()
    {
        $ei_id = $this->input->post('ei_id');
        $em_id = $this->input->post('em_id');
        $data['msg'] = $this->template->getMessage('success', 'Successfully set the default Image.');
        $this->event_model->setDefaultImage($em_id, $ei_id);
        echo json_encode($data);
    }

    public function getStateList()
    {
        $country_id = $this->input->post('country_id');
        $childs = $this->input->post('selectedChild');
        $childArr = explode(',', $childs);
        $state = $this->event_model->getStateList($country_id);
        $html = '<option value="">Select</option>';
        if (count($state) > 0) {
            foreach ($state as $value) {
                $selected = '';
                if (in_array($value->id, $childArr)) {
                    $selected = 'selected';
                }
                $html .= '<option value="' . $value->id . '" ' . $selected . '>' . $value->name . '</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode($data);
    }

    public function getCityList()
    {
        $state_id = $this->input->post('state_id');
        $childs = $this->input->post('selectedChild');
        $childArr = explode(',', $childs);
        $city = $this->event_model->getCityList($state_id);
        $html = '<option value="">Select</option>';
        if (count($city) > 0) {
            foreach ($city as $value) {
                $selected = '';
                if (in_array($value->id, $childArr)) {
                    $selected = 'selected';
                }
                $html .= '<option value="' . $value->id . '" ' . $selected . '>' . $value->name . '</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode($data);
    }
}