<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends MX_Controller
{
	public $data ;
	public $startDateTime ;
	public $dataTimeInterval ; 
    public function __construct()
    {
        parent::__construct();
        $this->ion_user_auth->isLoggedIn();
        $this->load->model('notification_model');
        $this->data = array();
        $this->startDateTime = date('Y-m-d H:i:s');
        $this->dataTimeInterval = 'PT00H15M00S';
    }
    
    public function meterError(){
        $type = $this->uri->segment(4);
        $funcName = 'getMeterErrorListing_'.$type;
        $this->data['list'] = $this->notification_model->{$funcName}();
        $this->load->view('notification/meterError/listing_'.$type , $this->data);
    }
}