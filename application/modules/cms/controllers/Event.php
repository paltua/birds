<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Event extends MY_Controller
{
    public $controller;
    public $module;
    public $perPage;
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Template');
        $this->load->model('cms_model');
        $this->controller = $this->router->fetch_class();
        $this->module = $this->router->fetch_module();
        $this->perPage = 9;
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $data = array();
        $status = '';
        $msg = '';
        $data['limit']['start'] = 0;
        $data['limit']['perPage'] = $this->perPage;
        $data['msg'] = $this->template->getMessage($status, $msg);
        $data['list'] = $this->cms_model->getEventList($data['limit']);
        $data['listCount'] = $this->cms_model->getEventCount();
        $data['program'] = $this->cms_model->getProgrammes();
        $data['completed'] = $this->cms_model->getCompleted();
        $data['module'] = $this->module;
        $data['controller'] = $this->controller;
        $this->template->setTitle('Event');
        $this->template->setLayout('cms');
        $this->template->homeRender('cms/' . $this->controller . '/list', $data);
    }

    public function getAjaxData()
    {
        $category_id = $this->input->post('category_id');
        $postLimit = $this->input->post('startPage');
        $sendLim = $retData['startPage'] = $postLimit + 1;
        $dbLim = $sendLim * $this->perPage;
        $limit = array('start' => $dbLim, 'perPage' => $this->perPage);
        $list = $this->cms_model->getEventList($limit);
        $listCount = $this->cms_model->getEventCount();
        if ($listCount > ($dbLim + $this->perPage)) {
            $retData['loaderStatus'] = 'show';
        } else {
            $retData['loaderStatus'] = 'hide';
        }
        $html = '';
        foreach ($list as $key => $viewData) {
            $inData['module'] = $this->module;
            $inData['controller'] = $this->controller;
            $inData['row'] = $viewData;
            $html .= $this->load->view($this->module . '/' . $this->controller . '/single', $inData, true);
        }
        $retData['html'] = $html;
        $retData['status'] = 'success';
        echo json_encode($retData);
    }

    public function details($title_url = '')
    {
        $data = array();
        $status = '';
        $msg = '';
        $title_url = trim($title_url);
        $data['msg'] = $this->template->getMessage($status, $msg);
        $data['details'] = $this->cms_model->getSingleEvent($title_url);
        if (count($data['details']) <= 0) {
            redirect(base_url($this->module . '/' . $this->controller));
        }
        $data['module'] = $this->module;
        $data['controller'] = $this->controller;
        $data['images'] = $this->cms_model->getSingleEventImages($data['details'][0]->em_id);
        $data['completed'] = $this->cms_model->getCompleted($data['details'][0]->em_id);
        $data['upcoming'] = $this->cms_model->getUpcoming($data['details'][0]->em_id);
        $data['program'] = $this->cms_model->getProgrammes();
        // pr($data['images']);
        $this->template->setTitle('Event');
        $this->template->setLayout('cms');
        $this->template->homeRender('cms/' . $this->controller . '/details', $data);
    }

    private function _sendEmailToAdmin($contact_us = array())
    {
        $data = $contact_us;
        $to = ADMIN_EMAIL;
        $data['to_name'] = ADMIN_NAME;
        $subject = 'New Contact Us Request | Parrot Dipankar';
        $body = $this->load->view('cms/' . $this->controller . '/email', $data, TRUE);
        $this->tbl_generic_model->sendEmail($to, $subject, $body, array(), array());
    }
}