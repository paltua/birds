<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Gallery extends MX_Controller
{

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        $this->controller = $this->router->fetch_class();
        $this->load->model($this->controller . '_model');
        $this->data['page_title'] = 'Gallery';
        $this->data['controller'] = $this->controller;
        $this->data['statusUrl'] = base_url('admin/' . $this->controller . '/changeStatus');
    }



    public function index()
    {
        $this->data['msg'] = '';
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
        $this->_upload();
        $this->data['msg'] = $this->template->getMessage($status, $msg);
        // $this->data['list'] = $this->gallery_model->getAllData();

        $this->data['dataTableUrl'] = base_url('admin/' . $this->controller . '/viewListDataTable');
        $this->template->setTitle('Admin : ' . $this->data['page_title']);
        $this->template->setLayout('dashboard');
        $this->template->homeAdminRender('admin/' . $this->controller . '/image', $this->data);
    }

    public function viewListDataTable()
    {
        $requestData = $this->input->post();
        $columns = array(
            2 => 'g_status',
            3 => 'created_date',
        );
        if (!isset($requestData['order'][0]['column'])) {
            $orderBy['col'] = 'created_date';
            $orderBy['val'] = 'DESC';
        } else {
            $orderBy['col'] = $columns[$requestData['order'][0]['column']];
            $orderBy['val'] = $requestData['order'][0]['dir'];
        }

        $limit['start'] = $requestData['start'];
        $limit['perpage'] = $requestData['length'];
        $searchData = trim($requestData['search']['value']);
        $fetchedData = $this->gallery_model->getDataTableData($searchData, $orderBy, $limit);
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
                $actionStr .= '<a href="' . base_url() . 'admin/' . $this->controller . '/delete/' . $value->g_id . '" class="deleteChange btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>';
                $statusStr = '<a class="statusChange btn btn-' . ($value->g_status == 'active' ? 'info' : 'warning') . ' btn-xs" href="javascript:void(0);" title="Click to change Status" value="' . ($value->g_status == 'active' ? 'unlock' : 'lock') . '" id="status_' . $value->g_id . '" name="' . $value->g_id . '"><i id="i_status_' . $value->g_id . '" class="fa fa-' . ($value->g_status == 'active' ? 'unlock' : 'lock') . '"></i><span id="span_status_' . $value->g_id . '">' . ucfirst($value->g_status) . '</span>';

                $img = '<img height="200" width="200" alt="' . $value->g_alt . '" src="' . base_url(UPLOAD_EVENT_PATH . 'thumb/' . $value->g_path) . '">';
                $nestedData[] = $img;
                $nestedData[] = $statusStr;
                $nestedData[] = base_url(UPLOAD_EVENT_PATH . 'thumb/' . $value->g_path);
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


    private function _upload()
    {
        $config['upload_path']          = 'uploads/gallery/';
        $config['allowed_types']        = 'gif|jpg|png';
        /*$config['max_size']             = 100;
        $config['max_width']            = 1024;
        $config['max_height']           = 768;*/
        $config['file_name']            = date('YmdHis');
        $this->load->library('upload', $config);
        if ($_FILES) {
            if (!$this->upload->do_upload('myFile')) {
                $this->session->set_flashdata('status', 'danger');
                $this->session->set_flashdata('msg', $this->upload->display_errors());
            } else {
                $this->session->set_flashdata('status', 'success');
                $this->session->set_flashdata('msg', 'Successfully Uploaded');
                $inData['g_path'] = $this->upload->data('file_name');
                $inData['created_by'] = $this->_user_id;
                $inData['am_id'] = 0;
                $this->tbl_generic_model->add('gallery', $inData);
                $this->_resizeImage($inData['g_path']);
                redirect(base_url() . 'admin/' . $this->controller . '/index/');
            }
        }
    }

    private function _resizeImage($imageName = '')
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = 'uploads/gallery/' . $imageName;
        $config['new_image'] = 'uploads/gallery/thumb';
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = 250;
        $config['height']       = 250;
        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
    }


    public function delete($g_id = 0)
    {
        $where['g_id'] = $g_id;
        $data = $this->tbl_generic_model->get('gallery', '*', $where);
        if (!empty($data)) {
            $this->session->set_flashdata('status', 'success');
            $this->session->set_flashdata('msg', 'Successfully Deleted');
            $this->tbl_generic_model->delete('gallery', $where);
            if ($data[0]->g_path != '') {
                @unlink('uploads/gallery/' . $data[0]->g_path);
                @unlink('uploads/gallery/thumb/' . $data[0]->g_path);
            }

            redirect(base_url() . 'admin/' . $this->controller . '/index/');
        } else {
            $this->session->set_flashdata('status', 'danger');
            $this->session->set_flashdata('msg', 'Wrong Parameter');
            redirect(base_url() . 'admin/' . $this->controller . '/index');
        }
    }

    public function changeStatus()
    {
        $g_id = $this->input->post('am_id');
        $this->gallery_model->updateStatus($g_id);
        $data['msg'] = $this->template->getMessage('success', 'Successfully changed the status .');
        echo json_encode($data);
    }
}