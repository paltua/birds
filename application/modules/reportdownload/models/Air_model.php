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

    public function getTTL_flow_newDateDataTypeWise($date = ''){
		
		$sql = "SELECT * FROM air_meter_data_cfm AMD LEFT JOIN air_meter AM ON AM.meter_id=AMD.meter_id WHERE AMD.end_date_time = '".$date."'";

		return $this->air_db->query($sql)->result();	
	}

	public function getCfmData($startDate = '', $endDate = ''){

		$sql = " SELECT 
				    AM.meter_id,
				    AM.type,
				    AMD.end_date_time,
				    DATE_FORMAT(AMD.end_date_time, '%Y-%m-%d') end_date,
				    AMD.data_cfm,
				    (SELECT data_cfm FROM  air_meter_data_cfm AMD1 WHERE AMD1.end_date_time = DATE_ADD(AMD.end_date_time, INTERVAL 1 DAY) AND AM.meter_id= AMD1.meter_id) prev_data_cfm
				FROM
				    air_meter AM
				        LEFT JOIN
				    air_meter_data_cfm AMD ON AMD.meter_id = AM.meter_id
				WHERE
				    AMD.end_date_time BETWEEN '".$startDate."' AND '".$endDate."'
				        AND DATE_FORMAT(AMD.end_date_time, '%H:%i:%s') = '00:00:00'  
				ORDER BY AMD.end_date_time DESC";
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

	public function getLastDateDataTypeWise($startDate = '', $endDate = ''){
		$sql = "SELECT * FROM air_meter_data AMD LEFT JOIN air_meter AM ON AM.meter_id=AMD.meter_id WHERE AMD.end_date_time >= '".$startDate."' AND AMD.end_date_time < '".$endDate."' ORDER BY AM.type";
		return $this->air_db->query($sql)->result();	
	}

	public function getShiftStart($config_id = 1){
		$sql = "SELECT * FROM master_config MC WHERE MC.config_id = '".$config_id."'";
		return $this->air_db->query($sql)->result();
	}

	public function getMeterDetails(){
		$sql = "SELECT * FROM air_meter AM WHERE AM.report_excel_position != '0' ORDER BY AM.report_excel_position ASC";
		return $this->air_db->query($sql)->result();
	}

	public function getLastDate(){
		$sql = "SELECT date_format(DATE_SUB(last_cron_run_date, INTERVAL 1 DAY),'%Y-%m-%d') max FROM agg_cron_hist";
		return $this->air_db->query($sql)->result();
	}
	
	
}