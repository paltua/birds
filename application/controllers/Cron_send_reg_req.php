<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron_send_reg_req extends MX_Controller 
{
    public $controller;
    
    public function __construct(){
        parent::__construct();
        $this->controller = $this->router->fetch_class();
        $this->load->model($this->controller.'_model');
    }

    public function index(){

    	$to = 'paltua@gmail.com';
		$subject = 'testing';
		$body ='testing';
		$cc = array();
		$bcc = array();
		$this->tbl_generic_model->sendEmail($to , $subject , $body , $cc , $bcc);
    	/*$data = $this->cron_send_reg_req->getDetails();
    	if(count($data) > 0){
    		foreach ($variable as $key => $value) {

    		}
    	}*/
    }
}