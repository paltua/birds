<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Programme extends MY_Controller
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

    public function details($title_url = '')
    {
        $data = array();
        $status = '';
        $msg = '';
        $title_url = trim($title_url);
        $data['msg'] = $this->template->getMessage($status, $msg);
        $data['details'] = $this->cms_model->getProgrammesDetails($title_url);
        if (count($data['details']) <= 0) {
            redirect(base_url($this->module . '/event'));
        }
        $data['module'] = $this->module;
        $data['controller'] = $this->controller;
        $data['completed'] = $this->cms_model->getCompleted();
        $data['upcoming'] = $this->cms_model->getUpcoming();
        $this->template->setTitle('Programme');
        $this->template->setLayout('cms');
        $this->template->homeRender('cms/' . $this->controller . '/details', $data);
    }
}