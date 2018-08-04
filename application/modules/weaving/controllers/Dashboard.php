<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MX_Controller {
	public $data = array();
	public $currentStartDateTime = '';
	public $currentEndDateTime = '';
	public $dateTimeInterval = '';
    public $dashboardTodayTime = '';

	public function __construct(){
		parent::__construct();
		$this->ion_user_auth->isLoggedIn();
		$this->shiftArr = array(1 => '0',2 => '32',3 => '64');
		$this->load->model('weaving_model');
        $this->_setCurrentDateTime();
        addPageDetails();
	}

	public function index(){
		$startDateShow = $this->dashboardTodayTime;
        $this->data['startDateShow'] = strtotime($startDateShow);
		$this->data['cmpx']['today'] = '';//$this->weaving_model->getTotalCmpxToday($startDateShow);
        $this->data['cmpx']['week'] = $this->weaving_model->getTotalCmpxWeekMonth(7);
        $this->data['cmpx']['month'] = $this->weaving_model->getTotalCmpxWeekMonth(30);
        $this->data['cfm']['today'] = '';//$this->weaving_model->getTotalCfmToday($startDateShow);
        $this->data['cfm']['week'] = $this->weaving_model->getNewCFM(7);
        $this->data['cfm']['month'] = $this->weaving_model->getNewCFM(30);
        $this->data['cfm_pres']['week'] = $this->weaving_model->getTotalCfmPresWeekMonth(7);
        $this->data['cfm_pres']['month'] = $this->weaving_model->getTotalCfmPresWeekMonth(30);
        $this->data['kw']['today'] = '';//$this->weaving_model->getTotalKwToday($startDateShow, 0);
        //$this->data['kw']['week'] = $this->weaving_model->getTotalKwWeekMonth(0, 7);
        //$this->data['kw']['month'] = $this->weaving_model->getTotalKwWeekMonth(0, 31);
        $this->data['kw_hplant']['today'] = '';//$this->weaving_model->getTotalKwToday($startDateShow, 1);
        $this->data['kw_hplant']['week'] = $this->weaving_model->getTotalKwWeekMonth(1, 7) * 24;
        $this->data['kw_hplant']['month'] = $this->weaving_model->getTotalKwWeekMonth(1, 30) * 24;
        $this->data['kw_loom']['today'] = '';//$this->weaving_model->getTotalKwToday($startDateShow, 2);
        $this->data['kw_loom']['week'] = $this->weaving_model->getTotalKwWeekMonth(2, 7) * 24;
        $this->data['kw_loom']['month'] = $this->weaving_model->getTotalKwWeekMonth(2, 30) * 24;
        $this->data['kw']['week'] = $this->data['kw_hplant']['week'] + $this->data['kw_loom']['week'];
        $this->data['kw']['month'] = $this->data['kw_hplant']['month'] + $this->data['kw_loom']['month'];

        //$this->data['daysOfWeekAndMonth'] = $this->weaving_model->getDaysOfWeekAndMonth();
        $this->data['style'] = $this->weaving_model->getStyleWiseCmpxDetails();
		$this->data['all'] = $this->load->view('dashboard/all', $this->data, true);
		unset($this->data['table']);
		$this->load->view('dashboard/index', $this->data);
	}
	
	public function live(){
		$this->data['startDateShow'] = /*'2018-03-07 20:15:00';//*/$this->currentStartDateTime;
        $this->data['endDateShow'] = /*'2018-03-07 20:30:00';//*/$this->currentEndDateTime;
        $this->data['table'][1]['cmpx'] = $this->weaving_model->getTotalCmpxProduce($this->data['startDateShow'],$this->data['endDateShow']);
        $this->data['table'][1]['kw'] = $this->weaving_model->getTotalKwConsumption($this->data['startDateShow'], $this->data['endDateShow'], 0);
        $this->data['table'][1]['kw_hplant'] = $this->weaving_model->getTotalKwConsumption($this->data['startDateShow'], $this->data['endDateShow'], 1);
        $this->data['table'][1]['kw_loom'] = $this->weaving_model->getTotalKwConsumption($this->data['startDateShow'], $this->data['endDateShow'], 2);
        $this->data['table'][1]['cfm'] = $this->weaving_model->getTotalCfmConsumption($this->data['startDateShow'], $this->data['endDateShow']);
        $this->data['table'][2]['data'] = $this->weaving_model->getLoomWiseProduction($this->data['startDateShow'], $this->data['endDateShow']);
        //print_r($this->data['table']);
        $this->data['all'] = $this->load->view('dashboard/live_all', $this->data, true);
        unset($this->data['table']);
        $this->load->view('dashboard/live', $this->data);
	}

	private function _setCurrentDateTime(){
		$this->dateTimeInterval = 'PT00H15M00S';
		$currentDate = $this->weaving_model->getCurrent();
		//$this->currentStartDateTime = $this->startDateTime = $currentDate[0]->last_view_date_time;
		//$this->currentEndDateTime = $this->_dateAdd();
        $this->currentEndDateTime = $this->startDateTime = $this->dashboardTodayTime = $currentDate[0]->last_view_date_time;
        $this->currentStartDateTime = $this->_dateSub();
		/*$this->currentEndDateTime = $this->startDateTime = $currentDate[0]->end_date_time;
		$this->currentStartDateTime = $this->_dateSub();*/
	}

	private function _dateAdd(){
        $date = new DateTime($this->startDateTime);
        $date->add(new DateInterval($this->dateTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

    private function _dateSub(){
        $date = new DateTime($this->startDateTime);
        $date->sub(new DateInterval($this->dateTimeInterval));
        return $date->format('Y-m-d H:i:s');
    }

    public function getStoppageDetails(){
    	$machine_id = $this->uri->segment(4);
		$this->startDateTime = $this->data['startDateShow'] = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->endDate = $this->data['endDateShow'] =  date('Y-m-d H:i:s', $this->uri->segment(6));
		$this->data['meterName'] = $machine_id;
		$this->data['details'] = $this->weaving_model->getStoppageDetails($this->startDateTime, $this->endDate, $machine_id);
		$this->load->view('dashboard/stoppageDetails', $this->data);
    }

    public function getLoomProductionRpmChart(){
    	$this->data['machine_id'] = $machine_id = $this->uri->segment(4);
		$this->startDateTime = $this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->dateTimeInterval = 'PT08H00M00S';
		$this->startDateTime = $this->_dateSub();
		$this->data['meterNameColumn'] = 'RPM';
    	$data = $this->weaving_model->getSingleLoomProduction($machine_id, $this->startDateTime, $this->endDate, 'rpm');
    	//$data = array_reverse($data);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Loom # '.$machine_id;//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2).'"
		        },';
    		}
    	}
    	//echo $dataSet;
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getLoomProductionPicksChart(){
    	$this->data['machine_id'] = $machine_id = $this->uri->segment(4);
		$this->startDateTime = $this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->dateTimeInterval = 'PT08H00M00S';
		$this->startDateTime = $this->_dateSub();
		$this->data['meterNameColumn'] = 'PICKS';
    	$data = $this->weaving_model->getSingleLoomProduction($machine_id, $this->startDateTime, $this->endDate, 'picks');
    	//$data = array_reverse($data);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Loom # '.$machine_id;//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2).'"
		        },';
    		}
    	}
    	//echo $dataSet;
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getLoomProductionEffChart(){
    	$this->data['machine_id'] = $machine_id = $this->uri->segment(4);
		$this->startDateTime = $this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->dateTimeInterval = 'PT08H00M00S';
		$this->startDateTime = $this->_dateSub();
		$this->data['meterNameColumn'] = 'Efficiency';
    	$data = $this->weaving_model->getSingleLoomProduction($machine_id, $this->startDateTime, $this->endDate, 'eff');
    	//$data = array_reverse($data);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Loom # '.$machine_id;//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2).'"
		        },';
    		}
    	}
    	//echo $dataSet;
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getStyleDetails(){
    	$style_id = $this->uri->segment(4);
		$this->data['details'] = $this->weaving_model->getStyleDetails($style_id);
		$this->data['columns'] = $this->weaving_model->getStyleColumnName();
		$this->load->view('dashboard/styleDetails', $this->data);
    }

    public function getTotalCmpxProduceChart(){
		$this->startDateTime = $this->endDate = date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->dateTimeInterval = 'PT08H00M00S';
		$this->startDateTime = $this->_dateSub();
		$this->data['meterNameColumn'] = 'CMPX';
    	$data = $this->weaving_model->getTotalCmpxProduceDateWise($this->endDate, $this->startDateTime);
    	/*echo "<pre>";
    	print_r($data);*/
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Total CMPX Produce';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
    			$cmpx = ($value->data)/WEAVING_CMPX;
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($cmpx, 2).'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getRunningKwChart(){
		$this->startDateTime = $this->endDate = date('Y-m-d H:i:s', $this->uri->segment(4));
        $type = $this->uri->segment(5);
		$this->dateTimeInterval = 'PT08H00M00S';
		$this->startDateTime = $this->_dateSub();
		$this->data['meterNameColumn'] = 'KW';
    	$data = $this->weaving_model->getRunningKwDateWise($this->endDate, $this->startDateTime, $type);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Running '.$this->_getEmsKwName($type).' KW';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2).'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getKwCmpxChart(){
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		/*$this->dateTimeInterval = 'PT08H00M00S';
		$this->startDateTime = $this->_dateSub();*/
		$this->data['meterNameColumn'] = 'KW/CMPX';
    	$data = $this->weaving_model->getKwCmpxDateWise($this->endDate, $this->startDateTime);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'KW/CMPX';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
    			$data = $value->sum_kw/(($value->sum_picks - $value->prev_sum_picks)/WEAVING_CMPX);
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($data, 2).'"
		        },';
		        $data = 0;
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getCfmChart(){
		$this->startDateTime = $this->endDate = date('Y-m-d H:i:s', $this->uri->segment(4));
        $type = $this->uri->segment(5);
		$this->dateTimeInterval = 'PT08H00M00S';
		$this->startDateTime = $this->_dateSub();
    	$data = $this->weaving_model->getCfmDateWise($this->startDateTime, $this->endDate, $type);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
        if($type == 'flow'){
            $this->data['meterNameColumn'] = 'CFM';
    	   $this->data['meterName'] = 'Compressed Air Flow(CFM)';//$data[0]->device_name;
        }elseif($type == 'pres'){
            $this->data['meterNameColumn'] = 'Pressure';
            $this->data['meterName'] = 'Compressed Air Pressure';//$data[0]->device_name;
        }   

    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($value->data, 2).'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getCfmCmpxChart(){
		$this->startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->data['meterNameColumn'] = 'CFM/CMPX';
    	$data = $this->weaving_model->getCfmCmpxDateWise($this->endDate, $this->startDateTime);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'CFM/CMPX';//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
    			$data = $value->flow/(($value->sum_picks - $value->prev_sum_picks)/WEAVING_CMPX);
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.round($data, 2).'"
		        },';
		        $data = 0;
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function getLoomProductionCmpxChart(){
    	$this->machine_id = $this->uri->segment(4);
		$this->endDate = date('Y-m-d H:i:s', $this->uri->segment(5));
		$this->data['meterNameColumn'] = 'CMPX';
    	$data = $this->weaving_model->getCmpxDateWise($this->machine_id, $this->endDate);
    	$this->data['chartDataCount'] = count($data);
    	$dataSet = '';
    	$this->data['meterName'] = 'Loom #'.$this->machine_id;//$data[0]->device_name;
    	if(count($data) > 0){
    		foreach ($data as $key => $value) {
	        	$dataSet .= '{
		            "date": "'.$value->end_date_time.'",
		            "paramVal": "'.number_format((float)round($value->data/WEAVING_CMPX, 4), 4, '.', '').'"
		        },';
    		}
    	}
    	$this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }


    public function totalKwTodayGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $type = $this->uri->segment(5);
        $this->data['meterNameColumn'] = 'KW';
        $this->data['meterName'] = 'Total '.$this->_getEmsKwName($type).' KW For Today';
        $data = $this->weaving_model->totalKwTodayGraph($startDateTime, $type);
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->data, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function totalKwWeekGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $type = $this->uri->segment(5);
        $this->data['meterNameColumn'] = 'KWH';
        $this->data['meterName'] = 'Total '.$this->_getEmsKwName($type).' KWH For Last 7 Days';
        $data = $this->weaving_model->totalKwWeekGraph($startDateTime, $type);
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->data * 24, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
    }

    public function totalKwMonthGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $type = $this->uri->segment(5);
        $this->data['meterNameColumn'] = 'KWH';
        $this->data['meterName'] = 'Total '.$this->_getEmsKwName($type).' KWH For Last 30 Days';
        $data = $this->weaving_model->totalKwMonthGraph($startDateTime, $type);
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->data * 24, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
    }

    private function _getEmsKwName($type = 0){
        $rteData = '';
        if($type == 1){
            $rteData = "H-Plant";
        }elseif ($type == 2) {
            $rteData = "Loom";
        }
        return $rteData;
    }

    public function getAvgCmpxGraphByStyle(){
        $styleId = $this->uri->segment(4);
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(5));
        $type = $this->uri->segment(6);
        $this->data['meterNameColumn'] = 'CMPX';
        $this->data['meterName'] = 'CMPX For Last 30 Days';
        $data = $this->weaving_model->getAvgCmpxGraphByStyle( $startDateTime, $styleId);
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            $this->data['meterName'] = 'CMPX of '.$data[0]->style.' For Last 30 Days';
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->data/WEAVING_CMPX, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
    }

    public function totalCfmTodayGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CFM';
        $this->data['meterName'] = 'Total Compressed Air Flow(CFM) For Today';
        $data = $this->weaving_model->totalCfmTodayGraph($startDateTime);
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->data, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function totalCfmWeekGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CFM';
        $this->data['meterName'] = 'CFM vs Pressure For Last 7 Days';
        $data = $this->weaving_model->totalCfmPresWeekMonthGraph($startDateTime, 7);
        $this->data['chartDataCount'] = count($data);
        $this->data['title1'] = 'CFM';
        $this->data['title2'] = 'Pressure';
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "v1": "'.round($value->data_cfm/WEAVING_CFM, 2).'",
                    "v2": "'.round($value->data_pres, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/dual_bar',$this->data);
    }

    public function totalCfmMonthGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CFM';
        $this->data['meterName'] = 'CFM vs Pressure For Last 30 Days';
        $data = $this->weaving_model->totalCfmPresWeekMonthGraph($startDateTime, 30);
        $this->data['chartDataCount'] = count($data);
        $this->data['title1'] = 'CFM';
        $this->data['title2'] = 'Pressure';
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "v1": "'.round($value->data_cfm/WEAVING_CFM, 2).'",
                    "v2": "'.round($value->data_pres, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/dual_bar',$this->data);
    }

    public function totalCmpxTodayGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CMPX';
        $this->data['meterName'] = 'Total CMPX For Today';
        $data = $this->weaving_model->totalCmpxTodayGraph($startDateTime);
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->data/WEAVING_CMPX, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function totalCmpxWeekGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CMPX';
        $this->data['meterName'] = 'Total CMPX For Last 7 Days';
        $data = $this->weaving_model->totalCmpxWeekMonthGraph($startDateTime, 7);
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->data/WEAVING_CMPX, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
    }

    public function totalCmpxWeekMonthGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $days = $this->uri->segment(5);
        $this->data['meterNameColumn'] = 'CMPX and CFM';
        $this->data['meterName'] = 'CMPX vs CFM For Last '.$days.' Days';
        $data = $this->weaving_model->getCmpxCfmDualWeekMonthGraphData($days);
        $this->data['chartDataCount'] = count($data);
        $this->data['title1'] = 'CMPX';
        $this->data['title2'] = 'CFM';
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "v1": "'.round($value->data/WEAVING_CMPX, 2).'",
                    "v2": "'.round($value->data_cfm/WEAVING_CFM, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/dual_bar',$this->data);
    }

    public function totalCmpxMonthGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CMPX';
        $this->data['meterName'] = 'Total CMPX For Last 30 Days';
        $data = $this->weaving_model->totalCmpxWeekMonthGraph($startDateTime, 30);
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->data/WEAVING_CMPX, 1).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
        
    }

    public function kwCmpxTodayGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'KW/CMPX';
        $this->data['meterName'] = 'KW/CMPX For Today';
        $data = $this->weaving_model->totalCmpxTodayGraph($startDateTime);
        $dataKw = $this->weaving_model->totalKwTodayGraph($startDateTime, 0);
        $dataKwArr = array();
        if(count($dataKw) > 0){
            foreach ($dataKw as $val) {
                $dataKwArr[$val->graph_end_date_time] = $val->data;
            }
        }
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                if(isset($dataKwArr[$value->end_date_time])){
                    $dataSet .= '{
                        "date": "'.$value->end_date_time.'",
                        "paramVal": "'.round($dataKwArr[$value->end_date_time]/(($value->data - $value->prev_data)/WEAVING_CMPX), 2).'"
                    },';
                }
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function kwCmpxWeekGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'KWH/CMPX';
        $this->data['meterName'] = 'KWH/CMPX For Last 7 Days';
        $data = $this->weaving_model->totalCmpxWeekMonthGraph($startDateTime, 7);
        $dataKw = $this->weaving_model->totalKwWeekGraph($startDateTime, 0);
        $dataKwArr = array();
        if(count($dataKw) > 0){
            foreach ($dataKw as $val) {
                $dataKwArr[$val->end_date_time] = $val->data;
            }
        }
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                if(isset($dataKwArr[$value->end_date_time])){
                    $dataSet .= '{
                        "date": "'.$value->end_date_time.'",
                        "paramVal": "'.round(($dataKwArr[$value->end_date_time]/($value->data/WEAVING_CMPX)) * 24, 1).'"
                    },';
                }
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
    }

    public function kwCmpxMonthGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'KWH/CMPX';
        $this->data['meterName'] = 'KWH/CMPX For Last 30 Days';
        $data = $this->weaving_model->totalCmpxWeekMonthGraph($startDateTime, 30);
        $dataKw = $this->weaving_model->totalKwMonthGraph($startDateTime, 0);
        $dataKwArr = array();
        if(count($dataKw) > 0){
            foreach ($dataKw as $val) {
                $dataKwArr[$val->end_date_time] = $val->data;
            }
        }
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                if(isset($dataKwArr[$value->end_date_time]) && $value->data != 0){
                    $dataSet .= '{
                        "date": "'.$value->end_date_time.'",
                        "paramVal": "'.round(($dataKwArr[$value->end_date_time]/($value->data/WEAVING_CMPX)) * 24, 1).'"
                    },';
                }
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
    }
    
    public function cfmCmpxTodayGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CFM/CMPX';
        $this->data['meterName'] = 'CFM/CMPX For Today';
        $data = $this->weaving_model->totalCmpxTodayGraph($startDateTime);
        $dataCfm = $this->weaving_model->totalCfmTodayGraph($startDateTime);
        $dataCfmArr = array();
        if(count($dataCfm) > 0){
            foreach ($dataCfm as $val) {
                $dataCfmArr[$val->graph_end_date_time] = $val->data;
            }
        }
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                if(isset($dataCfmArr[$value->end_date_time])){
                    $dataSet .= '{
                        "date": "'.$value->end_date_time.'",
                        "paramVal": "'.round($dataCfmArr[$value->end_date_time]/(($value->data - $value->prev_data)/WEAVING_CMPX), 2).'"
                    },';
                }
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/line',$this->data);
    }

    public function cfmCmpxWeekGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CFM/CMPX';
        $this->data['meterName'] = 'CFM/CMPX For Last 7 Days';
        $data = $this->weaving_model->totalCmpxWeekMonthGraph($startDateTime, 7);
        $dataCfm = $this->weaving_model->totalCfmWeekGraph($startDateTime, 7);
        $dataCfmArr = array();
        if(count($dataCfm) > 0){
            foreach ($dataCfm as $val) {
                $dataCfmArr[$val->end_date_time] = $val->data;
            }
        }
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if($this->data['chartDataCount'] > 0){
            foreach ($data as $key => $value) {
                if(isset($dataCfmArr[$value->end_date_time])){
                    $dataSet .= '{
                        "date": "'.$value->end_date_time.'",
                        "paramVal": "'.round($dataCfmArr[$value->end_date_time]/($value->data/WEAVING_CMPX), 2).'"
                    },';
                }
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
    }

    public function cfmCmpxMonthGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CFM/CMPX';
        $this->data['meterName'] = 'CFM/CMPX For Last 30 Days';
        $data = $this->weaving_model->totalCmpxWeekMonthGraph($startDateTime, 30);
        //$dataCfm = $this->weaving_model->totalCfmMonthGraph($startDateTime, 30);
        $dataCfm = $this->weaving_model->totalCfmWeekGraph($startDateTime, 30);
        $dataCfmArr = array();
        if(count($dataCfm) > 0){
            foreach ($dataCfm as $val) {
                $dataCfmArr[$val->end_date_time] = $val->data;
            }
        }
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if($this->data['chartDataCount'] > 0){
            foreach ($data as $key => $value) {
                if(isset($dataCfmArr[$value->end_date_time]) && $value->data != 0){
                    $dataSet .= '{
                        "date": "'.$value->end_date_time.'",
                        "paramVal": "'.round($dataCfmArr[$value->end_date_time]/($value->data/WEAVING_CMPX), 2).'"
                    },';
                }
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
    }

    public function totalCfmPresWeekGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CFM Pressure';
        $this->data['meterName'] = 'CFM Pressure For Last 7 Days';
        $data = $this->weaving_model->totalCfmPresWeekMonthGraph($startDateTime, 7);
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->data, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
    }

    public function totalCfmPresMonthGraph(){
        $startDateTime = date('Y-m-d H:i:s', $this->uri->segment(4));
        $this->data['meterNameColumn'] = 'CFM Pressure';
        $this->data['meterName'] = 'CFM Pressure For Last 30 Days';
        $data = $this->weaving_model->totalCfmPresWeekMonthGraph($startDateTime, 30);
        $this->data['chartDataCount'] = count($data);
        $dataSet = '';
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $dataSet .= '{
                    "date": "'.$value->end_date_time.'",
                    "paramVal": "'.round($value->data, 2).'"
                },';
            }
        }
        $this->data['chartData'] = $dataSet;
        $this->load->view('weaving/chart/bar',$this->data);
    }
	
}