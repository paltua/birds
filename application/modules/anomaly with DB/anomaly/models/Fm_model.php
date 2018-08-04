<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fm_model extends CI_Model {
	private $fm_db;
	
	public function __construct() {
		parent::__construct();
		$this->fm_db = $this->load->database('welspun_fm', TRUE);
		log_message('INFO', 'Fm_model enter');
	}
	

	function add($table,$data){
        $this->fm_db->set($data);
        $this->fm_db->insert($table,$data);
        //echo $this->fm_db->last_query();
		if ($this->fm_db->affected_rows() == '1') return $this->fm_db->insert_id();		
		return FALSE;      
    }

    public function getAnomalyHistoryDataByDate($date,$meterId){
    	$sql = "SELECT * FROM anomaly_history_data WHERE meter_id = '".$meterId."' AND end_date_time = '".$date."'";
    	return $this->fm_db->query($sql)->result();
    }

    public function getLastHistoryDateData($meterId){
    	$sql = "SELECT * FROM anomaly_history_data WHERE meter_id = '".$meterId."' ORDER BY end_date_time DESC LIMIT 0,1";
    	return $this->fm_db->query($sql)->result();
    }

    public function getAllHistoryDateData($meterId){
    	$sql = "SELECT * FROM anomaly_history_data WHERE meter_id = '".$meterId."' ORDER BY end_date_time ASC";
    	return $this->fm_db->query($sql)->result();
    }

    public function getMeterDetails($meterId){
    	$sql = "SELECT * FROM steam_meter WHERE meter_id = '".$meterId."'";
    	return $this->fm_db->query($sql)->result();
    }

	public function getCountMeterData($meter_id = 0){
    	$sql = "SELECT COUNT(*) AS C FROM steam_meter_data WHERE meter_id = '".$meter_id."'";
    	return $this->fm_db->query($sql)->result();
    }

    public function getMeter(){
    	$sql = "SELECT * FROM steam_meter";
    	return $this->fm_db->query($sql)->result();
    }

    public function getSteamACKCounterLastID($user_id){
    	$sql = "SELECT * FROM steam_acknowledge_counter WHERE user_id = '".$user_id."' ORDER BY id DESC LIMIT 0,1";
    	return $this->fm_db->query($sql)->result();
    }

    public function getShiftChart($meter_id = 0, $startDate = '', $working_shift = 0){
    	$sql = "SELECT * FROM steam_meter_data SMD  LEFT JOIN steam_meter SM ON SM.meter_id = SMD.meter_id WHERE 1 ";
    	if($meter_id > 0){
    		$sql .= " AND SMD.meter_id=".$meter_id ;
    	} 
    	if($startDate != ''){
    		$sql .= " AND date_format(end_date_time,'%Y-%m-%d') = '".$startDate."'" ;
    	}
		if($working_shift > 0){
			$sql .= " AND shift = ".$working_shift;
		}
		$sql .= " ORDER BY end_date_time, SM.type ASC";
		
		return $this->fm_db->query($sql)->result();
    }

    public function getDateDetails($meter_id = 0){
    	$sql = "SELECT max(date_format(end_date_time,'%Y-%m-%d')) max_date,min(date_format(end_date_time,'%Y-%m-%d')) min_date 
		FROM steam_meter_data ";
		if($meter_id > 0){
			$sql .= " where meter_id=".$meter_id." group by date_format(end_date_time,'%Y-%m-%d') ";
		}
		return $this->fm_db->query($sql)->result();
    }

    public function getDataMeterType($type = 'gen'){
    	$sql = "SELECT 
				    newDay.end_date_time_hour,
				    AVG(newDay.avg_pre_hour) avg_pre_hour,
				    AVG(newDay.avg_temp_hour) avg_temp_hour,
				    SUM(newDay.sum_flow_hour) sum_flow_hour,
				    SUM(newDay.avg_temp_hour * newDay.st_enthalpy_hour) / SUM(newDay.sum_flow_hour) st_enthalpy_hour,
				    SUM(newDay.tot_heat_content_hour) tot_heat_content_hour
				FROM
				    (SELECT 
				        newShift.end_date_time_hour,
				            AVG(newShift.avg_pre_hour) avg_pre_hour,
				            AVG(newShift.avg_temp_hour) avg_temp_hour,
				            SUM(newShift.sum_flow_hour) sum_flow_hour,
				            SUM(newShift.avg_temp_hour * newShift.st_enthalpy_hour) / SUM(newShift.sum_flow_hour) st_enthalpy_hour,
				            SUM(newShift.tot_heat_content_hour) tot_heat_content_hour
				    FROM
				        (SELECT 
				        new1.end_date_time end_date_time_hour,
				            AVG(new1.avg_pre) avg_pre_hour,
				            AVG(new1.avg_temp) avg_temp_hour,
				            SUM(new1.sum_flow) sum_flow_hour,
				            SUM(new1.avg_temp * new1.st_enthalpy) / SUM(new1.sum_flow) st_enthalpy_hour,
				            SUM(new1.tot_heat_content) tot_heat_content_hour,
				            new1.shift
				    FROM
				        (SELECT 
				        SMD.end_date_time,
				            AVG(SMD.P_pressure) avg_pre,
				            AVG(SMD.T_temp) avg_temp,
				            SUM(SMD.TTL_flow) sum_flow,
				            SUM(SMD.T_temp * SMD.steam_enthalpy) / SUM(SMD.TTL_flow) st_enthalpy,
				            SUM(SMD.steam_heat_content) tot_heat_content,
				            SMD.shift,
				            SMD.hours
				    FROM
				        steam_meter SM
				    LEFT JOIN steam_meter_data SMD ON SMD.meter_id = SM.meter_id
				    WHERE
				        SM.type = '".$type."'
				    GROUP BY SMD.end_date_time) new1
				    GROUP BY new1.hours) newShift
				    GROUP BY newShift.shift) newDay
				GROUP BY date_format(newDay.end_date_time_hour,'%Y-%M-%D')";
		return $this->fm_db->query($sql)->result();
    }

    public function getDataMeterTypeShift($type = 'gen', $shift = 1){
    	$sql = "SELECT 
				        newShift.end_date_time_hour,
				            AVG(newShift.avg_pre_hour) avg_pre_hour,
				            AVG(newShift.avg_temp_hour) avg_temp_hour,
				            SUM(newShift.sum_flow_hour) sum_flow_hour,
				            SUM(newShift.avg_temp_hour * newShift.st_enthalpy_hour) / SUM(newShift.sum_flow_hour) st_enthalpy_hour,
				            SUM(newShift.tot_heat_content_hour) tot_heat_content_hour
				    FROM
				        (SELECT 
				        new1.end_date_time end_date_time_hour,
				            AVG(new1.avg_pre) avg_pre_hour,
				            AVG(new1.avg_temp) avg_temp_hour,
				            SUM(new1.sum_flow) sum_flow_hour,
				            SUM(new1.avg_temp * new1.st_enthalpy) / SUM(new1.sum_flow) st_enthalpy_hour,
				            SUM(new1.tot_heat_content) tot_heat_content_hour,
				            new1.shift
				    FROM
				        (SELECT 
				        SMD.end_date_time,
				            AVG(SMD.P_pressure) avg_pre,
				            AVG(SMD.T_temp) avg_temp,
				            SUM(SMD.TTL_flow) sum_flow,
				            SUM(SMD.T_temp * SMD.steam_enthalpy) / SUM(SMD.TTL_flow) st_enthalpy,
				            SUM(SMD.steam_heat_content) tot_heat_content,
				            SMD.shift,
				            SMD.hours
				    FROM
				        steam_meter SM
				    LEFT JOIN steam_meter_data SMD ON SMD.meter_id = SM.meter_id
				    WHERE
				        SM.type = '".$type."' AND SMD.shift = '".$shift."'
				    GROUP BY SMD.end_date_time) new1
				    GROUP BY new1.hours) newShift
				    GROUP BY newShift.shift";
		return $this->fm_db->query($sql)->result();
    }


    public function getLast15DataTypeWise(){
    	$sql = "SELECT * FROM steam_meter_data SMD
				LEFT JOIN steam_meter SM ON SM.meter_id=SMD.meter_id
				WHERE SMD.end_date_time = (SELECT max(end_date_time) FROM steam_meter_data)
				ORDER BY SM.type";
		return $this->fm_db->query($sql)->result();		
    }
	   
	
	public function getSteamMeterData($meter_id = 0, $select = '*'){
		$sql = "SELECT ".$select." FROM steam_meter_data SMD
				LEFT JOIN steam_meter SM ON SM.meter_id=SMD.meter_id
				WHERE SMD.meter_id = ".$meter_id."
				ORDER BY SMD.id DESC,SMD.end_date_time DESC
				LIMIT 32";
		return $this->fm_db->query($sql)->result();	
	}

	public function getSteamMeterDataDay($meter_id = 0, $select = '*'){
		$sql = "SELECT ".$select." FROM steam_meter_data SMD
				LEFT JOIN steam_meter SM ON SM.meter_id=SMD.meter_id
				WHERE SMD.meter_id = ".$meter_id."
				ORDER BY SMD.id DESC,SMD.end_date_time DESC
				LIMIT 96";
		return $this->fm_db->query($sql)->result();	
	}

	public function getSteamMeterDataDayShift($meter_id = 0, $select = '*', $startDate = '', $endDate = ''){
		
		$sql = "SELECT ".$select." FROM steam_meter_data SMD
				LEFT JOIN steam_meter SM ON SM.meter_id=SMD.meter_id
				WHERE SMD.meter_id = ".$meter_id." 
				AND SMD.end_date_time >= '".$startDate."' 
				AND SMD.end_date_time < '".$endDate."' 
				ORDER BY SMD.id DESC,SMD.end_date_time DESC ";
			
		return $this->fm_db->query($sql)->result();	
	}

	public function getLastDateDataTypeWise($meter_id,$startDate = '', $endDate = ''){
		$sql = "SELECT * FROM steam_meter_data SMD LEFT JOIN steam_meter SM ON SM.meter_id=SMD.meter_id WHERE SMD.end_date_time >= '".$startDate."' AND SMD.end_date_time < '".$endDate."' AND SMD.meter_id = '".$meter_id."' ORDER BY SM.type";
		return $this->fm_db->query($sql)->result();	
	}

	public function getShiftStart($config_id = 1){
		$sql = "SELECT * FROM master_config MC WHERE MC.config_id = '".$config_id."'";
		return $this->fm_db->query($sql)->result();
	}

	public function insertInAnomalyHistory($meter_id,$P_pressure,$T_temp,$flow,$end_date_time,$user_id){

		$now = date('Y-m-d H:i:s');

		$sql = "INSERT INTO anomaly_history_data (meter_id, P_pressure, T_temp, flow, end_date_time, user_id, status, created_on)
				VALUES ('".$meter_id."', '".$P_pressure."', '".$T_temp."', '".$flow."', '".$end_date_time."', '".$user_id."', 'ACTIVE', '".$now."')";

		return $this->fm_db->query($sql);
	}
	
	
}