<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ems_model extends CI_Model {
	private $ems_cpp_db;
    private $datalog_cpp_db;
    private $fm_db;
	function __construct() {
		parent::__construct();
		$this->ems_cpp_db = $this->load->database('welspun_ems_cpp', TRUE);
		$this->datalog_cpp_db = $this->load->database('welspun_datalog', TRUE);
        $this->fm_db = $this->load->database('welspun_fm', TRUE);
		log_message('INFO', 'Ems_model enter');
	}

    public function getEmsCppLoss($date = '', $ids = ''){
    	$sql = "SELECT 
				    SUM(ABS(EMD.data_KW) * 1000)  data
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id";
		$sql .= " WHERE MD.device_id IN (".$ids.") AND EMD.end_date_time = '".$date."'";
		//echo $sql;
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getEmsLoss($date = '', $ids = ''){
    	$sql = "SELECT 
				    SUM(EMD.data_KW) data
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id";
		$sql .= " WHERE MD.device_id IN (".$ids.") AND EMD.end_date_time = '".$date."'";
		return $this->db->query($sql)->result();
    }

    public function getEmsMonthlyData($ids = ''){
        $sql = "SELECT 
                    SUM(newTbl.data_KW) data
                FROM
                    (SELECT 
                        MD.device_id,
                        AVG(NULLIF(EMD.data_KW, 0)) data_KW
                    FROM
                        master_device MD
                    LEFT JOIN ems_meter_data EMD ON EMD.device_id = MD.device_id
                    WHERE
                        MD.device_id IN (".$ids.")
                        AND YEAR(EMD.end_date_time) = YEAR(CURRENT_DATE())
                        AND MONTH(EMD.end_date_time) = MONTH(CURRENT_DATE())
                    GROUP BY MD.device_id) newTbl";
        return $this->db->query($sql)->result();
    }

    public function getEmsCppMonthlyData($ids = ''){
        $sql = "SELECT 
                    SUM(newTbl.data_KW) data
                FROM
                    (SELECT 
                		MD.device_id,
                        AVG(NULLIF(EMD.data_KW, 0) * 1000) data_KW
                    FROM
                        master_device MD
                    LEFT JOIN ems_meter_data EMD ON EMD.device_id = MD.device_id
                    WHERE
                        MD.device_id IN (".$ids.")
                        AND YEAR(EMD.end_date_time) = YEAR(CURRENT_DATE())
                        AND MONTH(EMD.end_date_time) = MONTH(CURRENT_DATE())
                    GROUP BY MD.device_id) newTbl";       
        return $this->ems_cpp_db->query($sql)->result();
    }

    public function showElectricityTableChart($type = 'wil', $ids = '', $startDate = '', $endDate = ''){
    	if($type == 'wil'){
    		$innerSql = " SUM(ABS(EMD.data_KW) * 1000 )  data ";
    	}else{
    		$innerSql = " SUM(ABS(EMD.data_KW)) data ";
    	}

    	$sql = "SELECT 
		    ".$innerSql.", EMD.end_date_time
		FROM
		    ems_meter_data EMD
		WHERE
		    1
		    AND EMD.device_id IN (".$ids.")	
	        AND EMD.end_date_time >= '".$startDate."'
	        AND EMD.end_date_time < '".$endDate."'
		GROUP BY EMD.end_date_time";
		//echo $sql;exit;
    	if($type == 'wil'){
			return $this->ems_cpp_db->query($sql)->result();
		}else{
			return $this->db->query($sql)->result();
		}
    }

    public function getDataLoggerFlowData($startDate = '', $ids = 0){
        $sql = "SELECT 
                    SUM(flow) sum_flow
                FROM
                    datalog_meter_data
                WHERE
                    meter_id IN (".$ids.")  
                    AND end_date_time = '".$startDate."'";
                    //echo $sql;
        return $this->datalog_cpp_db->query($sql)->result();
    }

    public function getDataLoggePreTempData($startDate = '', $ids = 0){
         $sql = "SELECT 
                    pressure,temp
                FROM
                    datalog_meter_data
                WHERE
                    meter_id = ".$ids."  
                    AND end_date_time = '".$startDate."'";
        return $this->datalog_cpp_db->query($sql)->result();
    }

    public function getGapSteamData($startDate = '', $ids = 0){
        $sql = "SELECT 
                    sum(flow) sum_flow,avg(P_pressure) avg_pre, avg(T_temp) avg_temp
                FROM
                    steam_meter_data
                WHERE
                    meter_id IN (".$ids.")
                    AND end_date_time = '".$startDate."'";
                    //echo $sql;
        return $this->fm_db->query($sql)->result();            
    }

    public function getCompressAirData($id = 1, $startDate = ''){
    	$this->datalog_cpp_db->select('pressure,flow,end_date_time');
    	$this->datalog_cpp_db->from('datalog_meter_data');
    	$this->datalog_cpp_db->where('meter_id', $id);
    	$this->datalog_cpp_db->where('end_date_time', $startDate);
    	/*$this->datalog_cpp_db->get();
    	echo $this->datalog_cpp_db->last_query();exit;*/
    	return $this->datalog_cpp_db->get()->result();
    }


    public function getMonthWiseAvg($type = 'wil', $ids = ''){
        if($type == 'wil'){
            $innerSql = " AVG(NULLIF(EMD.data_KW, 0) * 1000) ";
        }else{
            $innerSql = " AVG(NULLIF(EMD.data_KW, 0)) ";
        }
    	$sql = "SELECT 
				    newTbl.m_y, SUM(newTbl.data_KW) data
				FROM
				    (SELECT 
				        MD.device_id,
			            DATE_FORMAT(EMD.end_date_time, '%M-%Y') m_y,
			            DATE_FORMAT(EMD.end_date_time, '%m') m,
			            DATE_FORMAT(EMD.end_date_time, '%Y') y,
			            ".$innerSql." data_KW
				    FROM
				        master_device MD
				    LEFT JOIN ems_meter_data EMD ON EMD.device_id = MD.device_id
				    WHERE
				        MD.device_id IN (".$ids.")
				    GROUP BY MD.device_id , DATE_FORMAT(EMD.end_date_time, '%M-%Y')) newTbl
				GROUP BY newTbl.m_y
				ORDER BY newTbl.y ASC, newTbl.m ASC";       
        if($type == 'wil'){
            return $this->ems_cpp_db->query($sql)->result();
        }else{
            return $this->db->query($sql)->result();
        }        
    }

    public function getSteamDataloggerCurrentMonthAvg($type = 'dl', $ids = ''){
        if($type == 'dl'){
            $tbl = "datalog_meter_data";
        }else{
            $tbl = "steam_meter_data";
        }
        $sql = "SELECT 
                    SUM(newTbl.data_flow) data
                FROM
                    (SELECT 
                        meter_id,
                        AVG(NULLIF(flow, 0)) data_flow
                    FROM
                        ".$tbl."
                    WHERE
                        meter_id IN (".$ids.")
                        AND YEAR(end_date_time) = YEAR(CURRENT_DATE())
                        AND MONTH(end_date_time) = MONTH(CURRENT_DATE())
                    GROUP BY meter_id) newTbl";       
        if($type == 'dl'){
            return $this->datalog_cpp_db->query($sql)->result(); 
        }else{
            return $this->fm_db->query($sql)->result(); 
        } 
    }

    public function getSteamDataloggerMonthWiseAvg($type = 'dl', $ids = ''){
        if($type == 'dl'){
            $tbl = "datalog_meter_data";
        }else{
            $tbl = "steam_meter_data";
        }

        $sql = "SELECT 
                        newTbl.m_y, SUM(newTbl.data_flow) data
                FROM
                    (SELECT 
                        meter_id,
                        DATE_FORMAT(end_date_time, '%M-%Y') m_y,
                        DATE_FORMAT(end_date_time, '%m') m,
                        DATE_FORMAT(end_date_time, '%Y') y,
                        AVG(flow) data_flow
                    FROM
                        ".$tbl."
                    WHERE
                        meter_id IN (".$ids.")
                    GROUP BY meter_id , DATE_FORMAT(end_date_time, '%M-%Y')) newTbl
                GROUP BY newTbl.m_y
                ORDER BY newTbl.y ASC, newTbl.m ASC";
        //echo $sql;        
        if($type == 'dl'){
            return $this->datalog_cpp_db->query($sql)->result(); 
        }else{
            return $this->fm_db->query($sql)->result(); 
        }       
    }

    public function getCurrent(){
    	//$sql = "SELECT last_run_date_time end_date_time FROM cron_hist";
        $sql = "SELECT last_view_date_time end_date_time FROM cron_hist";
		$data = $this->db->query($sql)->result();
		$data1 = $this->ems_cpp_db->query($sql)->result();
		$sql = "SELECT TIMESTAMPDIFF(MINUTE,'".$data1[0]->end_date_time."','".$data[0]->end_date_time."') aa";
		$minutes = $this->db->query($sql)->result();
		if($minutes[0]->aa > 0){
			return $data;
		}else{
			return $data1;
		}
    }


    
	
	
}