<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cpp_model extends CI_Model {
	private $cpp_db;
	
	public function __construct() {
		parent::__construct();
		$this->cpp_db = $this->load->database('welspun_datalog', TRUE);
		log_message('INFO', 'Cpp_model enter');
	}

    public function getLast15DataTypeWise(){

    	$sql = "SELECT * FROM datalog_meter_data DMD
				LEFT JOIN datalog_meter DM ON DM.id=DMD.meter_id
				WHERE DMD.end_date_time = (SELECT max(end_date_time) FROM datalog_meter_data)
				ORDER BY DM.type";

		return $this->cpp_db->query($sql)->result();		
    }

    public function getLastDateDataTypeWise($startDate = '', $endDate = ''){
		$sql = "SELECT * FROM datalog_meter_data DMD 
				LEFT JOIN datalog_meter DM ON DM.id=DMD.meter_id 
				WHERE DMD.end_date_time >= '".$startDate."' AND DMD.end_date_time < '".$endDate."' 
				ORDER BY DM.type";

		return $this->cpp_db->query($sql)->result();	
	}

	public function getDateDetails($meter_id = 0){
    	$sql = "SELECT max(date_format(end_date_time,'%Y-%m-%d')) max_date,min(date_format(end_date_time,'%Y-%m-%d')) min_date 
		FROM datalog_meter_data ";
		if($meter_id > 0){
			$sql .= " where meter_id=".$meter_id." group by date_format(end_date_time,'%Y-%m-%d') ";
		}
		return $this->cpp_db->query($sql)->result();
    }

    public function getDataLoggerMeterData($meter_id = 0, $select = '*'){
		$sql = "SELECT ".$select." FROM datalog_meter_data DMD
				LEFT JOIN datalog_meter DM ON DM.id=DMD.meter_id
				WHERE DMD.meter_id = ".$meter_id."
				ORDER BY DMD.id DESC,DMD.end_date_time DESC
				LIMIT 32";
		return $this->cpp_db->query($sql)->result();	
	}

	public function getMeterTypeWise($type='gen'){
    	$sql = "SELECT * FROM datalog_meter WHERE type = '".$type."'";
    	return $this->cpp_db->query($sql)->result();
    }

    public function getShiftStart($config_id = 1){
		$sql = "SELECT * FROM master_config MC WHERE MC.config_id = '".$config_id."'";
		return $this->cpp_db->query($sql)->result();
	}

	public function getDataloggerMeterDataDayShift($meter_id = 0, $select = '*', $startDate = '', $endDate = ''){
		
		$sql = "SELECT ".$select." FROM datalog_meter_data DMD
				LEFT JOIN datalog_meter DM ON DM.id=DMD.meter_id
				WHERE DMD.meter_id = ".$meter_id." 
				AND DMD.end_date_time >= '".$startDate."' 
				AND DMD.end_date_time < '".$endDate."' 
				ORDER BY DMD.id DESC,DMD.end_date_time DESC ";
			
		return $this->cpp_db->query($sql)->result();	
	}

	public function getDataloggerMeterDataDayWiseParam($meter_id = 0, $select = '*', $startDate = '', $endDate = ''){
		$sql = "SELECT ".$select." FROM datalog_meter_data DMD
				LEFT JOIN datalog_meter DM ON DM.id=DMD.meter_id
				WHERE DMD.meter_id = ".$meter_id." 
				AND DMD.end_date_time >= '".$startDate."' 
				AND DMD.end_date_time < '".$endDate."' 
				GROUP BY DATE_FORMAT(DMD.end_date_time, '%Y%m%d')
				ORDER BY DMD.id DESC,DMD.end_date_time DESC ";

			
		return $this->cpp_db->query($sql)->result();	
	}
	
	public function getMeterType($mid){
    	$sql = "SELECT type FROM datalog_meter WHERE id = '".$mid."'";
    	return $this->cpp_db->query($sql)->result();
    }
}