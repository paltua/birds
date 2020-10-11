<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Event extends MY_Controller
{
    public $controller;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Template');
        // $this->load->model('cms_model');
        $this->controller = $this->router->fetch_class();
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
        // $data['category'] = $this->cms_model->getLevelOneCategory();
        // $data['content'] = $this->cms_model->getPageContent('pd_charitable_trust');
        $data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Charity');
        $this->template->setLayout('cms');
        $this->template->homeRender('cms/' . $this->controller . '/list', $data);
    }

    public function details()
    {
        $data = array();
        $status = '';
        $msg = '';
        // $data['category'] = $this->cms_model->getLevelOneCategory();
        // $data['content'] = $this->cms_model->getPageContent('pd_charitable_trust');
        $data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Charity');
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