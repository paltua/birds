<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transformer extends MX_Controller {
	public $data = array();
	public $currentStartDateTime = '';
	public $currentEndDateTime = '';
	public $dataTimeInterval = '';

	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->load->model('transformer_model');
		addPageDetails();
	}

	public function index(){
		//$this->load->library('api');
		$this->data['errStatus'] = false;
		$this->data['errMsg'] = 0;
		$this->data['selectedTransId'] = 0;
		$this->data['selectedLoading'] = '';
		$this->data['expectedLoss'] = 0;
		$this->data['chartData'] = array();
		$this->data['searchData'] = array();
		$this->data['data_a'] = 0;
		$this->data['data_b'] = 0;
		$this->data['data_c'] = 0;
		$this->data['low_loading'] = 0;
		$this->data['high_loading'] = 0;
		if($this->input->post('go')){
			$this->data['selectedTransId'] = $this->input->post('transId');
			$this->data['selectedLoading'] = $this->input->post('loading');
			if($this->data['selectedLoading'] == '' || $this->data['selectedTransId'] == ' '){
				$this->data['errStatus'] = true;
				$this->data['errMsg'] = 'Please enter loading value';
			}else{
				$this->data['searchData'] = $this->_getSearchData();
				if($this->data['selectedLoading'] < $this->data['low_loading'] || $this->data['selectedLoading'] > $this->data['high_loading']){
					$this->data['errStatus'] = true;
					$this->data['errMsg'] = 'Please select your loading between '.round($this->data['low_loading'], 2).' and '.round($this->data['high_loading'], 2);
				}
			}
		}
		
		$this->data['transDetails'] = $this->transformer_model->getTransformerDetails();
		$this->data['all'] = $this->load->view('transformer/diy3/all', $this->data, true);
		$this->load->view('transformer/diy3/index', $this->data);
	}

	private function _getSearchData(){
		$diy3Details = $this->transformer_model->getDiy3Details($this->data['selectedTransId']);
		if(count($diy3Details) > 0){
			$this->data['data_a'] = $diy3Details[0]->data_a;
			$this->data['data_b'] = $diy3Details[0]->data_b;
			$this->data['data_c'] = $diy3Details[0]->data_c;
			$this->data['high_loading'] = $diy3Details[0]->high_loading;
			$this->data['low_loading'] = $diy3Details[0]->low_loading;
			$this->data['transName'] = $diy3Details[0]->trans_name; 
		}
		$this->data['expectedLoss'] = round(($this->data['data_a'] * pow(($this->data['selectedLoading']),2)) + ($this->data['data_b'] * ($this->data['selectedLoading'])) + $this->data['data_c'], 2);
		for($i = round($this->data['low_loading']) - 1; $i <= round($this->data['high_loading']); $i = $i + 1){ 
            $this->data['chartData'][$i] = round(($this->data['data_a'] * pow(($i),2)) + ($this->data['data_b'] * ($i)) + $this->data['data_c'], 2);
            $loss = round(($this->data['data_a'] * pow(($i),2)) + ($this->data['data_b'] * ($i)) + $this->data['data_c'], 2);
            
            if($this->data['selectedLoading'] == $i){
            	$this->data['newChartData'][] = array('loading' => $this->data['selectedLoading'], 'loss' => $this->data['expectedLoss'], 'demand' => $this->data['expectedLoss']);
            }else{
            	$this->data['newChartData'][] = array('loading' => $i, 'loss' => $loss);
            }
            if($this->data['selectedLoading'] > $i && $this->data['selectedLoading'] < $i + 1){
            	$this->data['newChartData'][] = array('loading' => $this->data['selectedLoading'], 'loss' => $this->data['expectedLoss'], 'demand' => $this->data['expectedLoss']);
            }
        }
        
        /*$this->data['newChartData'][12.5] = array('loading' => $this->data['selectedLoading'], 'loss' => $this->data['expectedLoss']);*/
        //print_r($this->data['newChartData']);
	}


	public function getAjaxData(){
		$this->data['errStatus'] = false;
		$this->data['errMsg'] = 0;
		$this->data['transName'] = ''; 
		$this->data['selectedTransId'] = $this->uri->segment(4);
		$this->data['selectedLoading'] = $this->uri->segment(5);
		$this->data['expectedLoss'] = 0;
		$this->data['chartData'] = array();
		$this->data['newChartData'] = array();
		$this->data['data_a'] = 0;
		$this->data['data_b'] = 0;
		$this->data['data_c'] = 0;
		$this->data['low_loading'] = 0;
		$this->data['high_loading'] = 0;
		$this->data['searchData'] = $this->_getSearchData();
		if($this->data['selectedLoading'] < $this->data['low_loading'] || $this->data['selectedLoading'] > $this->data['high_loading']){
			$this->data['errStatus'] = true;
			$this->data['errMsg'] = 'Please select your loading between '.round($this->data['low_loading'], 2).' and '.round($this->data['high_loading'], 2);
		}
		echo json_encode($this->data);
	}

	public function getAjaxMinMaxLoading(){
		$this->data["selectedTransId"] = $this->uri->segment(4);
		$diy3Details = $this->transformer_model->getDiy3Details($this->data['selectedTransId']);
		$this->data["min"] = round($diy3Details[0]->low_loading, 2);
		$this->data["max"] = round($diy3Details[0]->high_loading, 2);
		$this->data["info"] = '<strong>Info!</strong> Please select your loading between '.$this->data["min"].' and '.$this->data["max"];
		echo json_encode($this->data);
	}
	
	
}