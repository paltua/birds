<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ion_user_auth->isLoggedIn();
    }
    
    public function index()
    {
        $this->load->view('dashboard/index');
    }
}