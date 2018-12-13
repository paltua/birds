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

    	/*$to = 'paltua@gmail.com';
		$subject = 'testing';
		$body ='testing';
		$cc = array();
		$bcc = array();
		$this->tbl_generic_model->sendEmail($to , $subject , $body , $cc , $bcc);*/
    	$data = $this->cron_send_reg_req_model->getDetails();
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
                $email['pwd'] = $this->randomString(8);
                $user_master['password'] = $this->getPassword($email['pwd']);
                $email['random_unique_id'] = $user_master['random_unique_id'] = date('Ymdhis').rand(100,999);
    			
                $email['email'] = $value->email;
                $email['name'] = $value->name;
    			$this->_sendActivateEmail($email);
                $where['user_id'] = $value->user_id;
                $this->tbl_generic_model->edit('user_master', $user_master, $where);
    		}
    	}
    }

    private function _sendActivateEmail($user_master = array()){
        $generatetime = urlencode(base64_encode($user_master['random_unique_id'])) ;
        $url = base_url() . "user/auth/activate/".$generatetime ;
        $viewData['url'] = $url;
        $viewData['full_name'] = $user_master['name'];
        $viewData['pwd'] = $user_master['pwd'];
        $viewData['email'] = $user_master['email'];
        $msgbody = $this->load->view('user/auth/regEmailCron', $viewData,TRUE);
        $to = $user_master['email'];
        $subject = 'Account activation : '.SITENAME;
        $body = $msgbody;
        $this->tbl_generic_model->sendEmail($to, $subject, $body, array(), array());
    }

    public function getPassword($pwd = ''){
        $newPwd = '';
        if($pwd != ''){
            $cost = $this->config->item('cost');
            $newPwd = password_hash($pwd,PASSWORD_BCRYPT, array('cost'=>$cost));
        }
        return $newPwd;
    }

    function randomString($length = 7) {
        $str = "";
        $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }
}