<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Air_model extends CI_Model {
	private $air_db;
	
	public function __construct() {
		parent::__construct();
		$this->air_db = $this->load->database('welspun_air', TRUE);
		log_message('INFO', 'Air_model enter');
	}
	

    public function getMeter(){
    	$sql = "SELECT * FROM air_meter";
    	return $this->air_db->query($sql)->result();
    }

    public function getAnomalyHistoryDataByDate($date,$meterId){
    	$sql = "SELECT * FROM anomaly_history_data WHERE meter_id = '".$meterId."' AND end_date_time = '".$date."'";
    	return $this->air_db->query($sql)->result();
    }

    public function getLastHistoryDateData($meterId){
    	$sql = "SELECT * FROM anomaly_history_data WHERE meter_id = '".$meterId."' ORDER BY end_date_time DESC LIMIT 0,1";
    	return $this->air_db->query($sql)->result();
    }

    public function getAllHistoryDateData($meterId){
    	$sql = "SELECT * FROM anomaly_history_data WHERE meter_id = '".$meterId."' ORDER BY end_date_time ASC";
    	return $this->air_db->query($sql)->result();
    }

    public function getMeterDetails($meterId){
    	$sql = "SELECT * FROM air_meter WHERE meter_id = '".$meterId."'";
    	return $this->air_db->query($sql)->result();
    }

    public function getShiftChart($meter_id = 0, $startDate = '', $working_shift = 0){
    	$sql = "SELECT * FROM air_meter_data AMD  LEFT JOIN air_meter AM ON AM.meter_id = AMD.meter_id WHERE 1 ";
    	if($meter_id > 0){
    		$sql .= " AND AMD.meter_id=".$meter_id ;
    	} 
    	if($startDate != ''){
    		$sql .= " AND date_format(end_date_time,'%Y-%m-%d') = '".$startDate."'" ;
    	}
		if($working_shift > 0){
			$sql .= " AND shift = ".$working_shift;
		}
		$sql .= " ORDER BY end_date_time, AM.type ASC";
		
		return $this->air_db->query($sql)->result();
    }

    public function getDateDetails($meter_id = 0){
    	$sql = "SELECT max(date_format(end_date_time,'%Y-%m-%d')) max_date,min(date_format(end_date_time,'%Y-%m-%d')) min_date 
		FROM air_meter_data ";
		if($meter_id > 0){
			$sql .= " where meter_id=".$meter_id." group by date_format(end_date_time,'%Y-%m-%d') ";
		}
		return $this->air_db->query($sql)->result();
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
				        AMD.end_date_time,
				            AVG(AMD.P_pressure) avg_pre,
				            AVG(AMD.T_temp) avg_temp,
				            SUM(AMD.TTL_flow) sum_flow,
				            SUM(AMD.T_temp * AMD.steam_enthalpy) / SUM(AMD.TTL_flow) st_enthalpy,
				            SUM(AMD.steam_heat_content) tot_heat_content,
				            AMD.shift,
				            AMD.hours
				    FROM
				        air_meter AM
				    LEFT JOIN air_meter_data AMD ON AMD.meter_id = AMD.meter_id
				    WHERE
				        AMD.type = '".$type."'
				    GROUP BY AMD.end_date_time) new1
				    GROUP BY new1.hours) newShift
				    GROUP BY newShift.shift) newDay
				GROUP BY date_format(newDay.end_date_time_hour,'%Y-%M-%D')";
		return $this->air_db->query($sql)->result();
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
				        AMD.end_date_time,
				            AVG(AMD.P_pressure) avg_pre,
				            AVG(AMD.T_temp) avg_temp,
				            SUM(AMD.TTL_flow) sum_flow,
				            SUM(AMD.T_temp * AMD.steam_enthalpy) / SUM(AMD.TTL_flow) st_enthalpy,
				            SUM(AMD.steam_heat_content) tot_heat_content,
				            AMD.shift,
				            AMD.hours
				    FROM
				        air_meter AM
				    LEFT JOIN air_meter_data AMD ON AMD.meter_id = AM.meter_id
				    WHERE
				        AM.type = '".$type."' AND AMD.shift = '".$shift."'
				    GROUP BY AMD.end_date_time) new1
				    GROUP BY new1.hours) newShift
				    GROUP BY newShift.shift";
		return $this->air_db->query($sql)->result();
    }


    public function getLast15DataTypeWise(){
    	$sql = "SELECT * FROM air_meter_data AMD
				LEFT JOIN air_meter AM ON AM.meter_id=AMD.meter_id
				WHERE AMD.end_date_time = (SELECT max(end_date_time) FROM air_meter_data)
				ORDER BY AM.type";
		return $this->air_db->query($sql)->result();		
    }
	   
	
	public function getAirMeterData($meter_id = 0, $select = '*'){
		$sql = "SELECT ".$select." FROM air_meter_data AMD
				LEFT JOIN air_meter AM ON AM.meter_id=AMD.meter_id
				WHERE AMD.meter_id = ".$meter_id."
				ORDER BY AMD.id DESC,AMD.end_date_time DESC
				LIMIT 32";
		return $this->air_db->query($sql)->result();	
	}

	public function getAirMeterDataDay($meter_id = 0, $select = '*'){
		$sql = "SELECT ".$select." FROM air_meter_data AMD
				LEFT JOIN air_meter AM ON AM.meter_id=AMD.meter_id
				WHERE AMD.meter_id = ".$meter_id."
				ORDER BY AMD.id DESC,AMD.end_date_time DESC
				LIMIT 96";
		return $this->air_db->query($sql)->result();	
	}

	public function getAirMeterDataDayShift($meter_id = 0, $select = '*', $startDate = '', $endDate = ''){
		
		$sql = "SELECT ".$select." FROM air_meter_data AMD
				LEFT JOIN air_meter AM ON AM.meter_id=AMD.meter_id
				WHERE AMD.meter_id = ".$meter_id." 
				AND AMD.end_date_time >= '".$startDate."' 
				AND AMD.end_date_time < '".$endDate."' 
				ORDER BY AMD.id DESC";
			
		return $this->air_db->query($sql)->result();	
	}

	public function getLastDateDataTypeWise($meter_id,$startDate = '', $endDate = ''){
		$sql = "SELECT * FROM air_meter_data AMD LEFT JOIN air_meter AM ON AM.meter_id=AMD.meter_id WHERE AMD.end_date_time >= '".$startDate."' AND AMD.end_date_time < '".$endDate."' AND AMD.meter_id = '".$meter_id."' ORDER BY AM.type";
		return $this->air_db->query($sql)->result();	
	}

	public function getShiftStart($config_id = 1){
		$sql = "SELECT * FROM master_config MC WHERE MC.config_id = '".$config_id."'";
		return $this->air_db->query($sql)->result();
	}

	public function insertInAnomalyHistory($meter_id,$P_pressure,$T_temp,$flow,$end_date_time,$user_id){

		$now = date('Y-m-d H:i:s');

		$sql = "INSERT INTO anomaly_history_data (meter_id, P_pressure, T_temp, flow, end_date_time, user_id, status, created_on)
				VALUES ('".$meter_id."', '".$P_pressure."', '".$T_temp."', '".$flow."', '".$end_date_time."', '".$user_id."', 'ACTIVE', '".$now."')";

		return $this->air_db->query($sql);
	}
	
	
}