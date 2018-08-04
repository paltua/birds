<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weaving_model extends CI_Model {
	public $plantKwId = 151;
	public $loomKwId = 152;
	public $airCfmId = 10;
	public $kwIdsQuery = '';
	public $cfmIdsQuery = '';
	public $weavingShift_1 ;
	public $weavingShift_2 ;
	public $weavingShift_3 ;
	public $otherShift ;
	function __construct() {
		parent::__construct();
		$this->kwIdsQuery = ' AND (EMD.device_id='.$this->plantKwId.' OR EMD.device_id='.$this->loomKwId.') ';
		$this->cfmIdsQuery = ' AND AMD.meter_id = '.$this->airCfmId;
		$this->weavingShift_1 = '00:45:00';
		$this->weavingShift_2 = '08:45:00';
		$this->weavingShift_3 = '16:45:00';
		$this->otherShift = '00:30:00';
		log_message('INFO', 'Weaving_model enter');
	}

	public function getCurrent(){
		$this->db->select('*');
		$this->db->from('welspun_weaving.cron_hist');
		//$this->db->from('welspun_ems.cron_hist');
		$this->db->where('id',1);
		return $this->db->get()->result();
	}

	public function getTotalCmpxProduce($startDate = '', $endDate = ''){
		$sql = "SELECT SUM(CMPX) sum_cmpx FROM welspun_weaving.curprod WHERE end_date_time = '".$endDate."'";
		/*exit;
		$sql = "SELECT SUM(picks) sum_picks,end_date_time FROM welspun_weaving.curprod WHERE end_date_time = '".$endDate."' 
				UNION 
				SELECT SUM(picks) sum_picks,end_date_time FROM welspun_weaving.curprod WHERE end_date_time = '".$startDate."'";*/
		return $this->db->query($sql)->result();
	}

	public function getTotalCfmConsumption($startDate = '', $endDate = ''){
		$sql = "SELECT AMD.ttl_flow, AMD.flow, AMD.p_pressure, AMD.t_temp 
				FROM welspun_air.air_meter_data AMD
				WHERE 1 ".$this->cfmIdsQuery."
					AND AMD.end_date_time >= '".$startDate."'
					AND AMD.end_date_time < '".$endDate."'";
			
		return $this->db->query($sql)->result();			
	}

	public function getTotalKwConsumption($startDate = '', $endDate = '', $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT SUM(EMD.data_KW) sum_kw 
				FROM welspun_ems.ems_meter_data  EMD
				WHERE 1 ".$this->kwIdsQuery." 
					AND EMD.end_date_time >= '".$startDate."' 
					AND EMD.end_date_time < '".$endDate."'";
		return $this->db->query($sql)->result();
	}

	public function getLoomWiseProduction($startDate = '', $endDate = ''){
		$sql = "SELECT 
			        CP.machine_id,
		            CP.style_id,
		            ((CP.rtime / (CP.rtime + CP.pstime + CP.npstime)) * 100) eff,
		            ((CP.picks * 60) / CP.rtime) rpm,
		            CP.picks,
		            CP.cmpx,
		            SSD.style
			    FROM
			        welspun_weaving.curprod CP
			    LEFT JOIN welspun_weaving.std_style SSD ON SSD.style_id = CP.style_id
			    WHERE
			        end_date_time = '".$endDate."'";
		/*$sql = "SELECT 
				    CP.machine_id,
				    CP.style_id,
				    ((CP.rtime / (CP.rtime + CP.pstime + CP.npstime)) * 100) eff,
				    ((CP.picks * 60) / CP.rtime) rpm,
				    CP.picks,
				    SSD.style,
				    (SELECT 
				            picks
				        FROM
				            welspun_weaving.curprod
				        WHERE
				            end_date_time = DATE_ADD(CP.end_date_time,
				                INTERVAL - 15 MINUTE) AND machine_id=CP.machine_id) prev_picks
				FROM
				    welspun_weaving.curprod CP
				        LEFT JOIN
				    welspun_weaving.std_style SSD ON SSD.style_id = CP.style_id
				WHERE
				    CP.end_date_time = '".$endDate."'";*/		        		    
		return $this->db->query($sql)->result();        
	}

	public function getStoppageDetails($startDate = '', $endDate = '', $machine_id = ''){
		$sql = "SELECT CS.*, SCD.descr
				FROM welspun_weaving.curstop CS
				LEFT JOIN welspun_weaving.std_scode SCD ON SCD.scode = CS.scode
				WHERE end_date_time >= '".$startDate."'
			        AND end_date_time < '".$endDate."'
			        AND machine_id = ".$machine_id." 
			    ORDER BY CS.BEGTIME DESC";
		return $this->db->query($sql)->result();  		        
	}

	public function getSingleLoomProduction($machine_id = '', $startDate = '', $endDate = '', $type = ''){
		$select = '';
		if($type == 'rpm'){
			$select = ', ((CP.picks * 60) / CP.rtime) data';
		}elseif($type == 'picks'){
			$select = ', CP.cmpx data';
		}elseif($type == 'eff'){
			$select = ', ((CP.rtime / (CP.rtime + CP.pstime + CP.npstime)) * 100) data';
		}

		$sql = "SELECT 
				    CP.machine_id,
				    CP.style_id,
				    CP.end_date_time
				    ".$select."
				FROM welspun_weaving.curprod CP
				LEFT JOIN welspun_weaving.std_style SSD ON SSD.style_id = CP.style_id  
				WHERE 1 
					AND CP.machine_id = ".$machine_id."
				    AND end_date_time > '".$startDate."'
				    AND end_date_time <= '".$endDate."'
				ORDER BY  CP.end_date_time ASC";
		//echo $sql;		    
		return $this->db->query($sql)->result();
	}

	public function getStyleDetails($style_id = 0){
		$this->db->select('*');
		$this->db->from('welspun_weaving.std_style');
		$this->db->where('style_id', $style_id);
		return $this->db->get()->result();
	}

	public function getStyleColumnName(){
		$sql = "SHOW COLUMNS FROM welspun_weaving.std_style;";
		return $this->db->query($sql)->result();
	}

	public function getTotalCmpxProduceDateWise($endDate = '', $startDate = ''){
		$sql = "SELECT 
				    CP.cmpx data,
				    CP.end_date_time
				FROM
				    welspun_weaving.curprod CP
				WHERE 1 AND
				    CP.end_date_time <= '".$endDate."'
				        AND CP.end_date_time > DATE_ADD('".$endDate."', INTERVAL - 8 HOUR)
				GROUP BY CP.end_date_time ";
		return $this->db->query($sql)->result();
	}

	public function getRunningKwDateWise($endDate = '', $startDate = '', $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
			        SUM(EMD.data_KW) data,
			            EMD.end_date_time orginal_end_date_time,
			            DATE_ADD(EMD.end_date_time, INTERVAL 15 MINUTE) end_date_time
			    FROM
			        welspun_ems.ems_meter_data EMD
			    WHERE 1 ".$this->kwIdsQuery."
			            AND EMD.end_date_time <= '".$endDate."'
			            AND EMD.end_date_time > DATE_ADD('".$endDate."', INTERVAL - 8 HOUR)
			    GROUP BY EMD.end_date_time ";
		//echo $sql;	    
		return $this->db->query($sql)->result();
	}	

	public function getKwCmpxDateWise($endDate = '', $startDate = ''){
 		$sql = "SELECT 
				    *
				FROM
				    (SELECT 
				        SUM(CP.PICKS) sum_picks, CP.end_date_time, (SELECT SUM(picks)  FROM welspun_weaving.curprod WHERE end_date_time = DATE_ADD(CP.end_date_time,
				        INTERVAL - 15 MINUTE)) prev_sum_picks
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				        CP.end_date_time <= '".$endDate."'
				            AND CP.end_date_time > DATE_ADD('".$endDate."', INTERVAL - 8 HOUR)
				    GROUP BY CP.end_date_time) AS newCurprod
				        LEFT JOIN
				    (SELECT 
				        SUM(EMD.data_KW) sum_kw,
				            EMD.end_date_time orginal_end_date_time,
				            DATE_ADD(EMD.end_date_time, INTERVAL 15 MINUTE) new_end_date_time
				    FROM
				        welspun_ems.ems_meter_data EMD
				    WHERE
				        1
				            ".$this->kwIdsQuery."
				            AND EMD.end_date_time <= '".$startDate."'
				            AND EMD.end_date_time > DATE_ADD('".$startDate."', INTERVAL - 8 HOUR)
				    GROUP BY EMD.end_date_time) AS newEms ON newEms.new_end_date_time = newCurprod.end_date_time";
		return $this->db->query($sql)->result();
	}

	public function getCfmDateWise($startDate = '', $endDate = '', $type = 'flow'){
		$select = '';
		if($type == 'flow'){
			$select = ' IFNULL(AMD.flow,0) data, ';
		}elseif($type == 'pres'){
			$select = ' IFNULL(AMD.p_pressure,0) data, ';
		}

		$sql = "SELECT 
					".$select."
		            AMD.end_date_time orginal_end_date_time,
		            DATE_ADD(AMD.end_date_time, INTERVAL 15 MINUTE) end_date_time
			    FROM
			        welspun_air.air_meter_data AMD
			    WHERE 1 ".$this->cfmIdsQuery."
		            AND AMD.end_date_time <= '".$endDate."'
		            AND AMD.end_date_time > DATE_ADD('".$endDate."', INTERVAL - 8 HOUR)
		        GROUP BY AMD.end_date_time ";
		//echo $sql;	    
		return $this->db->query($sql)->result();
	}	

	public function getCfmCmpxDateWise($endDate = '', $startDate = ''){
		$sql = "SELECT 
				    *
				FROM
				    (SELECT 
				        SUM(CP.PICKS) sum_picks, CP.end_date_time, (SELECT SUM(picks)  FROM welspun_weaving.curprod WHERE end_date_time = DATE_ADD(CP.end_date_time,
				        INTERVAL - 15 MINUTE)) prev_sum_picks
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				        CP.end_date_time <= '".$endDate."'
				            AND CP.end_date_time > DATE_ADD('".$endDate."', INTERVAL - 8 HOUR)
				    GROUP BY CP.end_date_time) AS newCurprod
				        LEFT JOIN
				    (SELECT 
				        AMD.flow,
				            AMD.end_date_time orginal_end_date_time,
				            DATE_ADD(AMD.end_date_time, INTERVAL 15 MINUTE) new_end_date_time
				    FROM
				        welspun_air.air_meter_data AMD
				    WHERE
				        1 ".$this->cfmIdsQuery."
				            AND AMD.end_date_time <= '".$startDate."'
				            AND AMD.end_date_time > DATE_ADD('".$startDate."', INTERVAL - 8 HOUR)
				    GROUP BY AMD.end_date_time) AS newAir ON newAir.new_end_date_time = newCurprod.end_date_time
				WHERE
				    newAir.new_end_date_time IS NOT NULL";		      
		return $this->db->query($sql)->result();		            
	}

	public function getCmpxDateWise($machine_id = '', $endDate = ''){
		$sql = "SELECT 
				    CP.end_date_time,
				    CP.cmpx data
				FROM
				    welspun_weaving.curprod CP
				WHERE
				    CP.end_date_time <= '".$endDate."'
				        AND CP.end_date_time > DATE_ADD('".$endDate."',
				        INTERVAL - 8 HOUR)
				        AND CP.machine_id = ".$machine_id;
		return $this->db->query($sql)->result();		        
	}

	public function getTotalCmpxToday($startDate = ''){
		
		$sql = "SELECT 
				    AVG(NT4.cmpx) cmpx
				FROM
				    (SELECT 
				        SUM(NT3.cmpx) cmpx, NT3.days
				    FROM
				        (SELECT 
				        NT2.days,
				            NT2.shifts,
				            SUM(CASE
				                WHEN NT2.total >= 0 THEN NT2.total
				                ELSE 0
				            END) cmpx
				    FROM
				        (SELECT 
				        NT1.machine_id,
				            NT1.days,
				            NT1.shifts,
				            SUBSTRING_INDEX(GROUP_CONCAT(CAST(NT1.picks AS SIGNED)
				                ORDER BY NT1.end_date_time DESC), ',', 1) - SUBSTRING_INDEX(GROUP_CONCAT(CAST(NT1.picks AS SIGNED)
				                ORDER BY NT1.end_date_time), ',', 1) AS total
				    FROM
				        (SELECT 
				        CP.machine_id,
				            CP.picks,
				            CP.end_date_time,
				            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
				           	IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				       CP.end_date_time >= CONCAT(DATE(NOW()),' ".$this->weavingShift_1."')
				            AND CP.end_date_time < NOW()) NT1
				    WHERE WEEK(NT1.days,1) = WEEK(NOW(),1)   
				    GROUP BY NT1.machine_id , NT1.days , NT1.shifts
				    ORDER BY NT1.machine_id) NT2
				    GROUP BY NT2.days , NT2.shifts) NT3
				    GROUP BY NT3.days) NT4";
		$data = $this->db->query($sql)->result();
		return $data[0]->cmpx;	    
	}

	public function getTotalCmpxWeekMonth($days = 7){

		$search = " AND CP.end_date_time >= CONCAT(DATE_ADD(CURRENT_DATE(), INTERVAL -".$days." DAY), ' ".$this->weavingShift_1."') AND CP.end_date_time < CONCAT(CURRENT_DATE(), ' ".$this->weavingShift_1."')";

		$sql = "SELECT 
				    AVG(IFNULL(NT4.data,0)) cmpx
				FROM
				    (SELECT 
			        SUM(NT3.cmpx) data, NT3.days end_date_time
			    FROM
			        (SELECT 
			        NT2.days,
			            NT2.shifts,
			            SUM(CASE
			                WHEN NT2.total >= 0 THEN NT2.total
			                ELSE 0
			            END) cmpx
			    FROM
			        (SELECT 
			        NT1.machine_id,
			            NT1.days,
			            NT1.shifts,
			            SUM(IF(NT1.cmpx > 0,NT1.cmpx,0)) total
			    FROM
			        (SELECT 
			        CP.machine_id,
			            CP.cmpx,
			            CP.end_date_time,
			            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
			           	IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
			    FROM
			        welspun_weaving.curprod CP
			    WHERE 1 ".$search.") NT1
			    GROUP BY NT1.machine_id , NT1.days , NT1.shifts
			    ORDER BY NT1.machine_id) NT2
			    GROUP BY NT2.days , NT2.shifts) NT3
			    WHERE NT3.days BETWEEN DATE_FORMAT(DATE_ADD(CURRENT_DATE(), INTERVAL - ".$days." DAY), '%Y-%m-%d') AND DATE_FORMAT(CURRENT_DATE(), '%Y-%m-%d')
			    GROUP BY NT3.days) NT4";
           
		$data = $this->db->query($sql)->result();
		return $data[0]->cmpx;		    
	}

	public function getTotalCmpxMonth(){
		$sql = "SELECT 
				    AVG(NT4.cmpx) cmpx
				FROM
				    (SELECT 
				        SUM(NT3.cmpx) cmpx, NT3.days
				    FROM
				        (SELECT 
				        NT2.days,
				            NT2.shifts,
				            SUM(CASE
				                WHEN NT2.total >= 0 THEN NT2.total
				                ELSE 0
				            END) cmpx
				    FROM
				        (SELECT 
				        NT1.machine_id,
				            NT1.days,
				            NT1.shifts,
				            SUBSTRING_INDEX(GROUP_CONCAT(CAST(NT1.picks AS SIGNED)
				                ORDER BY NT1.end_date_time DESC), ',', 1) - SUBSTRING_INDEX(GROUP_CONCAT(CAST(NT1.picks AS SIGNED)
				                ORDER BY NT1.end_date_time), ',', 1) AS total
				    FROM
				        (SELECT 
				        CP.machine_id,
				            CP.picks,
				            CP.end_date_time,
				            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
				           	IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				        DATE_FORMAT(CP.end_date_time, '%m') = DATE_FORMAT(NOW(), '%m')) NT1
				    WHERE DATE_FORMAT(NT1.days, '%m') = DATE_FORMAT(NOW(), '%m')    
				    GROUP BY NT1.machine_id , NT1.days , NT1.shifts
				    ORDER BY NT1.machine_id) NT2
				    GROUP BY NT2.days , NT2.shifts) NT3
				    GROUP BY NT3.days) NT4";
		$data = $this->db->query($sql)->result();
		return $data[0]->cmpx;		    
	}


	public function getTotalCfmToday($startDate = ''){
		$sql = "SELECT AVG(NULLIF(AMD.flow,0)) flow
				FROM welspun_air.air_meter_data AMD
				WHERE 1 ".$this->cfmIdsQuery."
					AND AMD.end_date_time >= CONCAT(DATE(NOW()),' ".$this->weavingShift_1."')
					AND AMD.end_date_time < NOW()";
		$data = $this->db->query($sql)->result();
		return $data[0]->flow;			
	}

	public function getTotalCfmWeekMonth($days = 7){
		$sql = "SELECT AVG(NULLIF(AMD.flow,0)) flow
				FROM welspun_air.air_meter_data AMD
				WHERE 1 ".$this->cfmIdsQuery."
					AND AMD.end_date_time >= DATE_ADD(CURRENT_DATE(), INTERVAL - ".$days." DAY)
				    AND AMD.end_date_time < CURRENT_DATE()";
		$data = $this->db->query($sql)->result();
		return $data[0]->flow;
	}

	public function getTotalCfmMonth(){
		$sql = "SELECT AVG(NULLIF(AMD.flow,0)) flow
				FROM welspun_air.air_meter_data AMD
				WHERE 1 ".$this->cfmIdsQuery."
					AND DATE_FORMAT(AMD.end_date_time, '%m') = DATE_FORMAT(NOW(), '%m')";
		$data = $this->db->query($sql)->result();
		return $data[0]->flow;
	}

	public function getTotalKwToday($startDate = '', $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
				    SUM(NT1.data_KW) sum_kw
				FROM
				    (SELECT 
				        EMD.device_id, AVG(NULLIF(EMD.data_KW,0)) data_KW
				    FROM
				        welspun_ems.ems_meter_data EMD
				    WHERE
				        1 ".$this->kwIdsQuery." 
					AND EMD.end_date_time >= CONCAT(DATE(NOW()),' ".$this->weavingShift_1."')
					AND EMD.end_date_time < NOW() 
				GROUP BY EMD.device_id) NT1";
		$data = $this->db->query($sql)->result();
		return $data[0]->sum_kw;
	}

	public function getTotalKwWeekMonth($type = 0, $days = 7){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
				    SUM(NT1.data_KW) sum_kw
				FROM
				    (SELECT 
				        EMD.device_id, AVG(NULLIF(EMD.data_KW,0)) data_KW
				    FROM
				        welspun_ems.ems_meter_data EMD
				    WHERE
				        1 ".$this->kwIdsQuery." 
				        AND EMD.end_date_time >= DATE_ADD(CURRENT_DATE(), INTERVAL - ".$days." DAY)
				    	AND EMD.end_date_time < CURRENT_DATE()
						GROUP BY EMD.device_id) NT1";
		$data = $this->db->query($sql)->result();
		return $data[0]->sum_kw;
	}

	public function getTotalKwMonth($type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
				    SUM(NT1.data_KW) sum_kw
				FROM
				    (SELECT 
				        EMD.device_id, AVG(NULLIF(EMD.data_KW,0)) data_KW
				    FROM
				        welspun_ems.ems_meter_data EMD
				    WHERE
				        1 ".$this->kwIdsQuery." 
					AND DATE_FORMAT(EMD.end_date_time, '%m') = DATE_FORMAT(NOW(), '%m') 
					GROUP BY EMD.device_id) NT1";
		$data = $this->db->query($sql)->result();
		return $data[0]->sum_kw;
	}

	public function getDaysOfWeekAndMonth(){
		$sql = "SELECT 
			    (WEEKDAY(NOW()) * 24) + HOUR(TIMEDIFF(NOW(), CONCAT(DATE(NOW()), ' ".$this->weavingShift_1."'))) daysOfWeek,
			    ((DAYOFMONTH(NOW()) - 1) * 24) + HOUR(TIMEDIFF(NOW(), CONCAT(DATE(NOW()), ' ".$this->weavingShift_1."'))) daysOfMonth,
			    HOUR(TIMEDIFF(NOW(), CONCAT(DATE(NOW()), ' ".$this->weavingShift_1."'))) hoursOfDays";
		return $this->db->query($sql)->result();
	}

	public function getStyleWiseCmpxDetails(){
		$sql = "SELECT 
				    NT4.*, SSD.style
				FROM
				    (SELECT 
				        NT3.style_id,
				            AVG(NT3.cmpx) avg_cmpx,
				            SUM(NT3.cmpx) total_cmpx
				    FROM
				        (SELECT 
				        NT2.machine_id, NT2.style_id, NT2.days, 
				        SUM(CASE
			                WHEN NT2.total >= 0 THEN NT2.total
			                ELSE 0
			            END) cmpx
				    FROM
				        (SELECT 
				        NT1.machine_id,
				            NT1.style_id,
				            NT1.days,
				            NT1.shifts,
				            SUM(IF(NT1.cmpx > 0,NT1.cmpx,0)) total
				    FROM
				        (SELECT 
				        CP.machine_id,
				            CP.style_id,
				            CP.cmpx,
				            CP.end_date_time,
				            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
				            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				        DATE_FORMAT(CP.end_date_time, '%m') = DATE_FORMAT(NOW(), '%m')) NT1
				    GROUP BY NT1.style_id , NT1.days , NT1.shifts
				    ORDER BY NT1.style_id) NT2
				    GROUP BY NT2.style_id , NT2.days) NT3
				    GROUP BY NT3.style_id) NT4
				        LEFT JOIN
				    welspun_weaving.std_style SSD ON SSD.style_id = NT4.style_id
				ORDER BY NT4.avg_cmpx DESC";		    
		return $this->db->query($sql)->result();
	}

	public function totalKwMonthGraph($startDate = '', $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
				    SUM(NT1.data_KW) data, DATE_FORMAT(NT1.end_date_time,'%Y-%m-%d') end_date_time
				FROM
				    (
				SELECT 
				    EMD.device_id, AVG(EMD.data_KW) data_KW, EMD.end_date_time
				FROM
				    welspun_ems.ems_meter_data EMD
				WHERE
				    1
				        ".$this->kwIdsQuery."
				        AND EMD.end_date_time >= DATE_ADD('".$startDate."', INTERVAL -30 DAY)
				        AND EMD.end_date_time <= '".$startDate."'
				        AND DATE_FORMAT(EMD.end_date_time,'%Y-%m-%d')!= CURRENT_DATE()
				GROUP BY EMD.device_id,DATE_FORMAT(EMD.end_date_time,'%Y-%m-%d') ) NT1
				GROUP BY NT1.end_date_time";
		return $this->db->query($sql)->result();
		 
	}
	

	public function totalKwWeekGraph($startDate = '' , $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
				    SUM(NT1.data_KW) data, DATE_FORMAT(NT1.end_date_time,'%Y-%m-%d') end_date_time
				FROM
				    (
				SELECT 
				    EMD.device_id, AVG(EMD.data_KW) data_KW, EMD.end_date_time
				FROM
				    welspun_ems.ems_meter_data EMD
				WHERE
				    1
				        ".$this->kwIdsQuery."
				        AND EMD.end_date_time >= DATE_ADD('".$startDate."', INTERVAL -7 DAY)
				        AND EMD.end_date_time <= '".$startDate."'
				        AND DATE_FORMAT(EMD.end_date_time,'%Y-%m-%d')!= CURRENT_DATE()
				GROUP BY EMD.device_id,DATE_FORMAT(EMD.end_date_time,'%Y-%m-%d') ) NT1
				GROUP BY NT1.end_date_time";
		return $this->db->query($sql)->result();
		 
	}

	public function totalKwTodayGraph($startDate = '' , $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
				     SUM(EMD.data_KW) data, EMD.end_date_time, DATE_ADD(EMD.end_date_time,
				                INTERVAL 15 MINUTE) graph_end_date_time
				FROM
				    welspun_ems.ems_meter_data EMD
				WHERE
				    1
				        ".$this->kwIdsQuery."
				        AND EMD.end_date_time >= CONCAT(DATE('".$startDate."'), ' ".$this->otherShift."')
				        AND EMD.end_date_time <= '".$startDate."'
				GROUP BY EMD.end_date_time";
		return $this->db->query($sql)->result();		
	}

	public function getAvgCmpxGraphByStyle($startDate = '', $style_id = ''){
		$sql = "SELECT 
				    NT2.style_id,
				    SUM(CASE
			                WHEN NT2.total >= 0 THEN NT2.total
			                ELSE 0
			            END) data,
				    DATE_FORMAT(NT2.end_date_time,'%Y-%m-%d') end_date_time,
				    SSD.style
				FROM
				    (SELECT 
				        NT1.machine_id,
				            NT1.style_id,
				            NT1.days,
				            NT1.shifts,
				            NT1.end_date_time,
				            SUM(IF(NT1.cmpx > 0,NT1.cmpx,0)) total
				    FROM
				        (SELECT 
				        CP.machine_id,
				            CP.style_id,
				            CP.cmpx,
				            CP.end_date_time,
				            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
				            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				        1
				            AND CP.end_date_time > DATE_ADD('".$startDate."', INTERVAL - 30 DAY)
				            AND CP.end_date_time <= '".$startDate."'
				            AND CP.style_id = ".$style_id.") NT1
				    GROUP BY NT1.style_id , NT1.days , NT1.shifts
				    ORDER BY NT1.style_id) NT2
				    LEFT JOIN
    				welspun_weaving.std_style SSD ON SSD.style_id = NT2.style_id
				GROUP BY NT2.days";
		//echo $sql;		
		return $this->db->query($sql)->result();			
	}

	public function totalCfmTodayGraph($startDate = ''){
		$sql = "SELECT 
			        IFNULL(AMD.flow,0) data,
		            AMD.end_date_time end_date_time,
		            DATE_ADD(AMD.end_date_time, INTERVAL 15 MINUTE) graph_end_date_time
			    FROM
			        welspun_air.air_meter_data AMD
			    WHERE 1 ".$this->cfmIdsQuery."
		            AND AMD.end_date_time > CONCAT(DATE('".$startDate."'), ' ".$this->weavingShift_1."')
		            AND AMD.end_date_time <= '".$startDate."'
		        GROUP BY AMD.end_date_time ";

		//echo $sql;	    
		return $this->db->query($sql)->result();
	}

	public function totalCfmWeekGraph($startDate = '', $days = 7){
		$sql = "SELECT 
			        AVG(IFNULL(AMD.flow,0)) data,
		            AMD.end_date_time orginal_end_date_time,
		            DATE_FORMAT(AMD.end_date_time,'%Y-%m-%d') end_date_time
			    FROM
			        welspun_air.air_meter_data AMD
			    WHERE 1 ".$this->cfmIdsQuery."
			    	AND AMD.end_date_time >= DATE_ADD(CURRENT_DATE(), INTERVAL -".$days." DAY)
			        AND AMD.end_date_time <= CURRENT_DATE()
			        AND DATE_FORMAT(AMD.end_date_time,'%Y-%m-%d')!= CURRENT_DATE()
		        GROUP BY DATE_FORMAT(AMD.end_date_time,'%Y-%m-%d')";

		//echo $sql;	    
		return $this->db->query($sql)->result();
	}

	public function totalCfmPresWeekMonthGraph($startDate = '', $days = 7){
		/*$sql = "SELECT 
			        AVG(IFNULL(AMD.flow,0)) data_flow,
			        AVG(IFNULL(AMD.P_pressure,0)) data_pres,
		            AMD.end_date_time orginal_end_date_time,
		            DATE_FORMAT(AMD.end_date_time,'%Y-%m-%d') end_date_time
			    FROM
			        welspun_air.air_meter_data AMD
			    WHERE 1 ".$this->cfmIdsQuery."
		            AND AMD.end_date_time >= DATE_ADD('".$startDate."', INTERVAL -".$days." DAY)
			        AND AMD.end_date_time < '".$startDate."'
			        AND DATE_FORMAT(AMD.end_date_time,'%Y-%m-%d')!= CURRENT_DATE()
		        GROUP BY DATE_FORMAT(AMD.end_date_time,'%Y-%m-%d')";*/
		$sql = "SELECT 
				    NTP1.*, IFNULL(NTC1.data_cfm,0) data_cfm
				FROM
				    (SELECT 
				        AVG(IFNULL(AMD.P_pressure, 0)) data_pres,
				            AMD.end_date_time orginal_end_date_time,
				            DATE_FORMAT(AMD.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        welspun_air.air_meter_data AMD
				    WHERE
				        1 AND AMD.meter_id = 10
				            AND AMD.end_date_time >= DATE_ADD('".$startDate."', INTERVAL - ".$days." DAY)
				            AND AMD.end_date_time <= '".$startDate."'
				            AND DATE_FORMAT(AMD.end_date_time, '%Y-%m-%d') != CURRENT_DATE()
				    GROUP BY DATE_FORMAT(AMD.end_date_time, '%Y-%m-%d')) NTP1
				        LEFT JOIN
				    (SELECT 
				        (NT1.last_data_cfm - NT1.data_cfm) data_cfm,
				            DATE_FORMAT(NT1.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        (SELECT 
				        AMDCFM.*,
				            (SELECT 
				                    AMDCFM1.data_cfm
				                FROM
				                    welspun_weaving.air_meter_data_cfm AMDCFM1
				                WHERE
				                    AMDCFM1.end_date_time = DATE_ADD(AMDCFM.end_date_time, INTERVAL 1 DAY)) AS last_data_cfm
				    FROM
				        welspun_weaving.air_meter_data_cfm AMDCFM
				    WHERE
				        1
				            AND AMDCFM.end_date_time >= DATE_ADD(CURRENT_DATE(), INTERVAL - ".$days." DAY)
				            AND AMDCFM.end_date_time <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 MINUTE)
				            AND DATE_FORMAT(AMDCFM.end_date_time, '%H:%i:00') = '".$this->otherShift."') NT1
				    WHERE
				        NT1.last_data_cfm IS NOT NULL) NTC1 ON NTC1.end_date_time = NTP1.end_date_time
				    ORDER BY    NTP1.end_date_time  " ;       

		//echo $sql;	    
		return $this->db->query($sql)->result();
	}

	public function totalCmpxTodayGraph($startDate = ''){
		$sql = "SELECT 
				    SUM(CP.picks) data,
				    CP.end_date_time,
				    (SELECT 
				            SUM(picks)
				        FROM
				            welspun_weaving.curprod
				        WHERE
				            end_date_time = DATE_ADD(CP.end_date_time,
				                INTERVAL - 15 MINUTE)) prev_data
				FROM
				    welspun_weaving.curprod CP
				WHERE
				    CP.end_date_time >= CONCAT(DATE('".$startDate."'), ' ".$this->weavingShift_1."')
				        AND CP.end_date_time <= '".$startDate."'
				GROUP BY CP.end_date_time";
		return $this->db->query($sql)->result();
	}

	public function totalCmpxWeekMonthGraph($startDate = '', $day = 7){
		$search = " AND CP.end_date_time >= CONCAT(DATE_ADD(CURRENT_DATE(), INTERVAL -".$day." DAY), ' ".$this->weavingShift_1."') AND CP.end_date_time < CONCAT(CURRENT_DATE(), ' ".$this->weavingShift_1."')";

		$sql = "SELECT 
			        SUM(NT3.cmpx) data, NT3.days end_date_time
			    FROM
			        (SELECT 
			        NT2.days,
			            NT2.shifts,
			            SUM(CASE
			                WHEN NT2.total >= 0 THEN NT2.total
			                ELSE 0
			            END) cmpx
			    FROM
			        (SELECT 
			        NT1.machine_id,
			            NT1.days,
			            NT1.shifts,
			            SUM(IF(NT1.cmpx > 0,NT1.cmpx,0)) total
			    FROM
			        (SELECT 
			        CP.machine_id,
			            CP.cmpx,
			            CP.end_date_time,
			            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
			           	IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
			    FROM
			        welspun_weaving.curprod CP
			    WHERE 1 ".$search.") NT1
			    GROUP BY NT1.machine_id , NT1.days , NT1.shifts
			    ORDER BY NT1.machine_id) NT2
			    GROUP BY NT2.days , NT2.shifts) NT3
			    WHERE NT3.days between DATE_FORMAT(DATE_ADD(CURRENT_DATE(), INTERVAL - ".$day." DAY), '%Y-%m-%d') AND DATE_FORMAT(CURRENT_DATE(), '%Y-%m-%d')
			    GROUP BY NT3.days";
		return $this->db->query($sql)->result();
	}

	public function getTotalCfmPresWeekMonth($days = 7){
		$sql = "SELECT AVG(NULLIF(AMD.P_pressure,0)) pressure
				FROM welspun_air.air_meter_data AMD
				WHERE 1 ".$this->cfmIdsQuery."
					AND AMD.end_date_time >= DATE_ADD(CURRENT_DATE(), INTERVAL - ".$days." DAY)
				    AND AMD.end_date_time < CURRENT_DATE()";
		$data = $this->db->query($sql)->result();
		return $data[0]->pressure;
	}

	public function getNewCFM($days = 7){
		$sql = "SELECT 
				    AVG(IFNULL(NT2.data,0)) cfm
				FROM
				    (SELECT 
				        (NT1.last_data_cfm - NT1.data_cfm) data,
				            DATE_FORMAT(NT1.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        (SELECT 
				        AMDCFM.*,
				            (SELECT 
				                    AMDCFM1.data_cfm
				                FROM
				                    welspun_weaving.air_meter_data_cfm AMDCFM1
				                WHERE
				                    AMDCFM1.end_date_time = DATE_ADD(AMDCFM.end_date_time, INTERVAL 1 DAY)) AS last_data_cfm
				    FROM
				        welspun_weaving.air_meter_data_cfm AMDCFM
				    WHERE
				        1
				            AND AMDCFM.end_date_time >= DATE_ADD(CURRENT_DATE(), INTERVAL - ".$days." DAY)
				            AND AMDCFM.end_date_time <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 MINUTE)
				            AND DATE_FORMAT(AMDCFM.end_date_time, '%H:%i:00') = '".$this->otherShift."') NT1
				    WHERE
				        NT1.last_data_cfm IS NOT NULL) NT2";
		$data = $this->db->query($sql)->result();
		return $data[0]->cfm;		    
	}

	public function getCmpxCfmDualWeekMonthGraphData($days = 7){
		$sql = "SELECT 
				    NTCMPX.*, IFNULL(NTC1.data_cfm, 0) data_cfm
				FROM
				    (SELECT 
				        SUM(NT3.cmpx) data, NT3.days end_date_time
				    FROM
				        (SELECT 
				        NT2.days,
				            NT2.shifts,
				            SUM(CASE
				                WHEN NT2.total >= 0 THEN NT2.total
				                ELSE 0
				            END) cmpx
				    FROM
				        (SELECT 
				        NT1.machine_id,
				            NT1.days,
				            NT1.shifts,
				            SUM(IF(NT1.cmpx > 0,NT1.cmpx,0)) total
				    FROM
				        (SELECT 
				        CP.machine_id,
				            CP.cmpx,
				            CP.end_date_time,
				            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'), DATE_FORMAT(DATE_ADD(CP.end_date_time, INTERVAL - 1 HOUR), '%Y-%m-%d'), DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
				            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				        1
				            AND CP.end_date_time >= CONCAT(DATE_ADD(CURRENT_DATE(), INTERVAL - ".$days." DAY), ' ".$this->weavingShift_1."')
				            AND CP.end_date_time < CONCAT(CURRENT_DATE(), ' ".$this->weavingShift_1."')) NT1
				    GROUP BY NT1.machine_id , NT1.days , NT1.shifts
				    ORDER BY NT1.machine_id) NT2
				    GROUP BY NT2.days , NT2.shifts) NT3
				    GROUP BY NT3.days) NTCMPX
				        LEFT JOIN
				    (SELECT 
				        (NT1.last_data_cfm - NT1.data_cfm) data_cfm,
				            DATE_FORMAT(NT1.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        (SELECT 
				        AMDCFM.*,
				            (SELECT 
				                    AMDCFM1.data_cfm
				                FROM
				                    welspun_weaving.air_meter_data_cfm AMDCFM1
				                WHERE
				                    AMDCFM1.end_date_time = DATE_ADD(AMDCFM.end_date_time, INTERVAL 1 DAY)) AS last_data_cfm
				    FROM
				        welspun_weaving.air_meter_data_cfm AMDCFM
				    WHERE
				        1
				            AND AMDCFM.end_date_time >= DATE_ADD(CURRENT_DATE(), INTERVAL - ".$days." DAY)
				            AND AMDCFM.end_date_time <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 MINUTE)
				            AND DATE_FORMAT(AMDCFM.end_date_time, '%H:%i:00') = '".$this->otherShift."') NT1
				    WHERE
				        NT1.last_data_cfm IS NOT NULL) NTC1 ON NTC1.end_date_time = NTCMPX.end_date_time
				ORDER BY NTCMPX.end_date_time";	
		return $this->db->query($sql)->result();		
	}

	

	
	
}