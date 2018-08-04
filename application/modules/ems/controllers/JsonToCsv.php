<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class JsonToCsv extends MY_Controller {
	private $_dataArray;
    private $_headerArray;
    public $data;

    public function __construct(){
    	parent::__construct();
    	$this->_dataArray = array();
		$this->_headerArray = array();
		$this->data = array();
    }

    public function index(){
    	$sql = "SELECT * FROM welspun_emailstore.ems_data GROUP BY start_date ORDER BY id DESC";
    	$data = $this->tbl_generic_model->ExecuteQuery($sql);
    	$this->_getTransDetails();
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
    			$date = date('Y-m-d',strtotime($value->start_date));
    			//$this->data['titles'][$date] = date("F j, Y",strtotime($date));
    			$this->data['dates'][] = $date;
    			//$this->data['sheetData'][$date] = json_decode($value->json_data);
    			$this->data['titles']['table1'] = 'Table 1 - CPP to WIL Stats';
    			$this->data['titles']['table2'] = 'Table 2 - Network Diagnostics';
    			$this->data['titles']['table3'] = 'Table 3 - Transformers Details';
    			$tt = json_decode($value->json_data);
    			$this->data['sheetData']['table1'][$date] = $tt->table1;
    			$this->data['sheetData']['table2'][$date] = $tt->table2;
    			$this->data['sheetData']['table3'][$date] = $tt->table3;
    			unset($tt);
    		}
    	}
    	$this->_generateAndDownload();
    }

    private function _getTransDetails(){
    	$sql = "SELECT ND.id, ND.t_in_device_capacity, ND.trans_name, MD.device_name location
				FROM welspun_ems.network_diogonastic ND
				LEFT JOIN master_device MD ON MD.device_id = ND.p_device_id";
    	$data = $this->tbl_generic_model->ExecuteQuery($sql);
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
    			$this->data['transDet'][$value->id]['name'] = $value->trans_name;
    			$this->data['transDet'][$value->id]['location'] = $value->location;
    		}
    	}
    }

    private function _generateAndDownload(){
		if(count($this->data['sheetData']) > 0){
			$this->data['fileName'] = 'ems_email_report_'.min($this->data['dates']).'_to_'.max($this->data['dates']).'.xlsx';
			$this->load->library('ems_email_excel');
			$this->ems_email_excel->createMultipleSheet($this->data['titles'], $this->data['sheetData'], $this->data['transDet']);
			$this->ems_email_excel->generate();
			$this->ems_email_excel->download($this->data['fileName']);
		}else{
			echo $this->data['msg'] = "No Record Found.";
			exit;
		}
	}
}
