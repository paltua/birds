<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_model extends CI_Model {	
	public $selectedDb ;
	public function __construct() {
		parent::__construct();
		log_message('INFO', 'Data_model enter');
		$this->selectedDb = 'welspun_ems';
	}

	public function getEmsColumn($section = ''){
		$this->_setSelectedEmsDb($section);
		$sql = " SHOW FULL COLUMNS FROM `".$this->selectedDb."`.`ems_meter_data` WHERE `Field` NOT IN('emd_id','device_id')";
		return $this->db->query($sql)->result();
	}

	public function getEmsDeviceList($section = ''){
		$this->_setSelectedEmsDb($section);
		$sql = "SELECT  * FROM `".$this->selectedDb."`.`master_device` ORDER BY `device_name`";
		return $this->db->query($sql)->result();
	}

	public function getEmsDeviceName($ids = array(), $section = ''){
		$this->_setSelectedEmsDb($section);
		$this->db->select('*');
		$this->db->from($this->selectedDb.".master_device");
		$this->db->where_in('device_id', $ids);
		$this->db->order_by('device_name','ASC');
		return $this->db->get()->result();
	}

	public function getEmsDeviceData($startDate = '', $endDate = '', $ids = array(), $section = ''){
		$this->_setSelectedEmsDb($section);
		$this->db->select('*');
		$this->db->from($this->selectedDb.".ems_meter_data");
		$this->db->where("DATE_FORMAT(end_date_time, '%Y-%m-%d') >= ", $startDate);
		$this->db->where("DATE_FORMAT(end_date_time, '%Y-%m-%d') <= ", $endDate);
		$this->db->where_in('device_id', $ids);
		$this->db->order_by('end_date_time','DESC');
		return $this->db->get()->result();
	}

	private function _setSelectedEmsDb($section){
		if($section == 'ems'){
			$this->selectedDb = 'welspun_ems';
		}elseif($section == 'emsCpp'){
			$this->selectedDb = 'welspun_ems_cpp';
		}elseif($section == 'air'){
			$this->selectedDb = 'welspun_air';
		}elseif($section == 'steam'){
			$this->selectedDb = 'welspun_fm';
		}elseif($section == 'weaving'){
			$this->selectedDb = 'welspun_weaving';
		}
	}

	public function getAirColumn($section = ''){
		$this->_setSelectedEmsDb($section);
		$sql = " SHOW FULL COLUMNS FROM `".$this->selectedDb."`.`air_meter_data` WHERE `Field` NOT IN('id','meter_id')";
		return $this->db->query($sql)->result();
	}

	public function getAirDeviceList($section = ''){
		$this->_setSelectedEmsDb($section);
		$sql = "SELECT  * FROM `".$this->selectedDb."`.`air_meter` ORDER BY `name`";
		return $this->db->query($sql)->result();
	}

	public function getAirDeviceName($ids = array(), $section = ''){
		$this->_setSelectedEmsDb($section);
		$this->db->select('*');
		$this->db->from($this->selectedDb.".air_meter");
		$this->db->where_in('meter_id', $ids);
		$this->db->order_by('name','ASC');
		return $this->db->get()->result();
	}

	public function getAirDeviceData($startDate = '', $endDate = '', $ids = array(), $section = ''){
		$this->_setSelectedEmsDb($section);
		$this->db->select('*');
		$this->db->from($this->selectedDb.".air_meter_data");
		$this->db->where("DATE_FORMAT(end_date_time, '%Y-%m-%d') >= ", $startDate);
		$this->db->where("DATE_FORMAT(end_date_time, '%Y-%m-%d') <= ", $endDate);
		$this->db->where_in('meter_id', $ids);
		$this->db->order_by('end_date_time','DESC');
		return $this->db->get()->result();
	}

	public function getSteamColumn($section = ''){
		$this->_setSelectedEmsDb($section);
		$sql = " SHOW FULL COLUMNS FROM `".$this->selectedDb."`.`steam_meter_data` WHERE `Field` NOT IN('id','meter_id')";
		return $this->db->query($sql)->result();
	}

	public function getSteamDeviceList($section = ''){
		$this->_setSelectedEmsDb($section);
		$sql = "SELECT  * FROM `".$this->selectedDb."`.`steam_meter` ORDER BY `name`";
		return $this->db->query($sql)->result();
	}

	public function getSteamDeviceName($ids = array(), $section = ''){
		$this->_setSelectedEmsDb($section);
		$this->db->select('*');
		$this->db->from($this->selectedDb.".steam_meter");
		$this->db->where_in('meter_id', $ids);
		$this->db->order_by('name','ASC');
		return $this->db->get()->result();
	}

	public function getSteamDeviceData($startDate = '', $endDate = '', $ids = array(), $section = ''){
		$this->_setSelectedEmsDb($section);
		$this->db->select('*');
		$this->db->from($this->selectedDb.".steam_meter_data");
		$this->db->where("DATE_FORMAT(end_date_time, '%Y-%m-%d') >= ", $startDate);
		$this->db->where("DATE_FORMAT(end_date_time, '%Y-%m-%d') <= ", $endDate);
		$this->db->where_in('meter_id', $ids);
		$this->db->order_by('end_date_time','DESC');
		return $this->db->get()->result();
	}

	public function getWeavingColumn($section = ''){
		$this->_setSelectedEmsDb($section);
		$sql = " SHOW FULL COLUMNS FROM `".$this->selectedDb."`.`curprod` WHERE `Field` NOT IN('cp_id','SDATE','PRESCAN','prescan_date_time')";
		return $this->db->query($sql)->result();
	}

	public function getWeavingDeviceList($section = ''){
		$this->_setSelectedEmsDb($section);
		$sql = "SELECT  * FROM `".$this->selectedDb."`.`std_style` WHERE 1 AND `STYLE_ID` BETWEEN 175 AND 214 ORDER BY `STYLE_ID` ";
		return $this->db->query($sql)->result();
	}

	public function getWeavingDeviceName($ids = array(), $section = ''){
		$this->_setSelectedEmsDb($section);
		$this->db->select('*');
		$this->db->from($this->selectedDb.".std_style");
		$this->db->where_in('STYLE_ID', $ids);
		$this->db->order_by('STYLE','ASC');
		return $this->db->get()->result();
	}

	public function getWeavingDeviceData($startDate = '', $endDate = '', $ids = array(), $section = ''){
		$this->_setSelectedEmsDb($section);
		$this->db->select('*');
		$this->db->from($this->selectedDb.".curprod");
		$this->db->where("DATE_FORMAT(end_date_time, '%Y-%m-%d') >= ", $startDate);
		$this->db->where("DATE_FORMAT(end_date_time, '%Y-%m-%d') <= ", $endDate);
		$this->db->where_in('STYLE_ID', $ids);
		$this->db->order_by('end_date_time','DESC');
		$this->db->order_by('MACHINE_ID','ASC');
		/*$this->db->get();
		echo $this->db->last_query();exit;*/
		return $this->db->get()->result();
	}

    
}