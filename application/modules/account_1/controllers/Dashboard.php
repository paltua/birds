<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MX_Controller
{
	public $data ;
	public $startDateTime ;
	public $dataTimeInterval ; 
    public function __construct()
    {
        parent::__construct();
        $this->ion_user_auth->isLoggedIn();
        $this->load->model('dashboard_model');
        $this->data = array();
        $this->startDateTime = date('Y-m-d H:i:s');
        $this->dataTimeInterval = 'PT00H15M00S';
    }
    
    public function index(){
    	$this->data['emsHtml'] = $this->_getEmsHtml();
    	$this->data['steamHtml'] = $this->_getSteamHtml();
    	$this->data['airHtml'] = $this->_getAirHtml();
        $this->load->view('dashboard/index', $this->data);
    }

    private function _getEmsHtml(){
    	$viewData = array();
        $viewData['show'] = 'no'; 
        $viewData['last15DataSet'] = $this->dashboard_model->getEmsLast15DataTypeWise();
        $viewData['monthlyDataSet'] = $this->dashboard_model->getEmsMonthlyDataTypeWise();
        if(count($viewData['last15DataSet']) > 0 && count($viewData['monthlyDataSet']) > 0){
            $viewData['show'] = 'yes'; 
        }
    	return $this->load->view('dashboard/widget/ems',$viewData, true);
    }

    private function _getSteamHtml(){
    	$viewData = array();
    	$viewData['show'] = 'no'; 
    	$viewData['last15DataSet'] = $this->dashboard_model->getSteamLast15DataTypeWise();
    	$viewData['monthlyDataSet'] = $this->dashboard_model->getSteamMonthlyDataTypeWise();
    	if(count($viewData['last15DataSet']) > 0 && count($viewData['monthlyDataSet']) > 0){
    		$viewData['show'] = 'yes'; 
    	}
    	return $this->load->view('dashboard/widget/steam',$viewData, true);
    }

    private function _getAirHtml(){
    	$viewData = array();
    	$viewData['show'] = 'no'; 
    	$viewData['last15DataSet'] = $this->dashboard_model->getAirLast15DataTypeWise();
    	$viewData['monthlyDataSet'] = $this->dashboard_model->getAirMonthlyDataTypeWise();
    	if(count($viewData['last15DataSet']) > 0 && count($viewData['monthlyDataSet']) > 0){
    		$viewData['show'] = 'yes'; 
    	} 
    	return $this->load->view('dashboard/widget/air', $viewData, true);
    }

    private function _getAir15MinutesData(){

    }

    private function _getAirMonthlyData(){
    	
    }

	private function _dateAdd(){
        $date = new DateTime($this->startDateTime);
        $date->add(new DateInterval($this->dataTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }
}