<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Blog extends MY_Controller {
    public $controller;
    public $data;

    public function __construct() {
        parent::__construct();
        $this->controller = $this->router->fetch_class();
        $this->load->model( $this->controller+'_model' );
        $this->data['controller'] = $this->controller;
    }

    public function index() {

    }

    public function details() {

    }

}