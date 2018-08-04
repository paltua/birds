<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Network_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
		log_message('INFO', 'Network_model enter');
	}

	public function getParentDeviceList(){
		$this->db->distinct('p_device_id');
		$this->db->select('MD.device_id,MD.device_name');
		$this->db->from('network_diogonastic ND');
		$this->db->join('master_device MD','MD.device_id=ND.p_device_id', 'left');
		$this->db->order_by('MD.device_name');
		return $this->db->get()->result();
	}

	public function getDateDetails($meter_id = 0){
    	$sql = "SELECT max(date_format(end_date_time,'%Y-%m-%d')) max_date,min(date_format(end_date_time,'%Y-%m-%d')) min_date 
		FROM ems_meter_data ";
		if($meter_id > 0){
			$sql .= " where meter_id=".$meter_id." group by date_format(end_date_time,'%Y-%m-%d') ";
		}
		return $this->db->query($sql)->result();
    }

    public function getDeviceWiseTransferInData($device_id = 0, $startDate = '', $endDate = ''){

    	$sql = "SELECT 
				    ND.*,
				    ND.t_in_device_capacity,
				    MD.device_id,
				    MD.device_name,
				    AVG(EMD.data_KW) KW,
				    SUM(EMD.data_KW) sum_KW,
				    SUM(EMD.data_KW/EMD.data_PF) KWPF,
				    EMD.end_date_time
				FROM
				    network_diogonastic ND
				        LEFT JOIN
				    master_device MD ON MD.device_id = ND.t_in_device_id
				        LEFT JOIN ems_meter_data EMD ON EMD.device_id=MD.device_id
				WHERE
				    ND.p_device_id = '".$device_id."' 
				        AND EMD.end_date_time >= '".$startDate."' 
				        AND EMD.end_date_time < '".$endDate."'
				GROUP BY  MD.device_id       
				ORDER BY ND.id ASC";	
		return $this->db->query($sql)->result();

    }

    public function getDeviceWiseTransferOutData($device_id = 0, $startDate = '', $endDate = ''){
		$sql = "SELECT 
				    ND.*,
				    MD.device_id,
				    MD.device_name,
				    AVG(EMD.data_KW) KW,
				    EMD.end_date_time
				FROM
				    network_diogonastic ND
				        LEFT JOIN
				    master_device MD ON MD.device_id = ND.t_out_device_id
				        LEFT JOIN ems_meter_data EMD ON EMD.device_id=MD.device_id
				WHERE
				    ND.p_device_id = '".$device_id."' 
				        AND EMD.end_date_time >= '".$startDate."' 
				        AND EMD.end_date_time < '".$endDate."'
				GROUP BY  MD.device_id       
				ORDER BY ND.id ASC";		
		return $this->db->query($sql)->result();
    }

    public function getDeviceWiseTransferParentData($device_id = 0, $startDate = '', $endDate = ''){
		$sql = "SELECT 
				    MD.device_id,
				    MD.device_name,
				    AVG(EMD.data_KW) data,
				    EMD.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				        AND EMD.device_id = '".$device_id."'
				        AND EMD.end_date_time < '".$endDate."'
				        AND EMD.end_date_time >= '".$startDate."'
				WHERE MD.device_id = '".$device_id."'
				group by  MD.device_id ";	
		//echo $sql;				
		return $this->db->query($sql)->result();
    }


    public function getTransformerDetails($p_device_id = 0){
    	$sql = "SELECT 
				ND.*,
				MD1.device_name p_device_name,
				MD2.device_name t_in_device_name,
				MD3.device_name t_out_device_name
				from network_diogonastic ND
				LEFT JOIN master_device MD1 ON MD1.device_id=ND.p_device_id
				LEFT JOIN master_device MD2 ON MD2.device_id=ND.t_in_device_id
				LEFT JOIN master_device MD3 ON MD3.device_id=ND.t_out_device_id
				WHERE ND.p_device_id=".$p_device_id;
		$sql .= " ORDER BY ND.id DESC ";	
		//echo $sql;	
		return $this->db->query($sql)->result();
    }


    public function getTransformerDetailsByTransId($trans_id = 0){
    	$sql = "SELECT 
				ND.*,
				MD1.device_name p_device_name,
				MD2.device_name t_in_device_name,
				MD3.device_name t_out_device_name
				from network_diogonastic ND
				LEFT JOIN master_device MD1 ON MD1.device_id=ND.p_device_id
				LEFT JOIN master_device MD2 ON MD2.device_id=ND.t_in_device_id
				LEFT JOIN master_device MD3 ON MD3.device_id=ND.t_out_device_id
				WHERE ND.id=".$trans_id;
		$sql .= " ORDER BY ND.id DESC ";	
		//echo $sql;	
		return $this->db->query($sql)->result();
    }

    public function getMeterDetails($meter_id, $selected){
    	$this->db->select('MM.device_id,MM.device_name');
    	$this->db->from('master_device MM');
    	$this->db->where('MM.device_id', $meter_id);
    	return $this->db->get()->result();
    }

    public function getChartMeterData24Hours($device_id = 0, $startDate = '', $endDate = ''){
    	$this->db->select('data_KW data,end_date_time');
    	$this->db->from('ems_meter_data');
    	$this->db->where('device_id ', $device_id);
    	$this->db->where('end_date_time >=', $startDate);
    	$this->db->where('end_date_time <', $endDate);
    	$this->db->order_by('end_date_time', 'ASC');
    	return $this->db->get()->result();
    	//echo $this->db->last_query();exit;
    }

    public function getChartMeterDataDay($device_id = 0, $startDate = '', $endDate = ''){
    	$this->db->select('AVG(data_KW) data, DATE_FORMAT(end_date_time, "%Y-%m-%d") end_date_time');
    	$this->db->from('ems_meter_data');
    	$this->db->where('device_id ', $device_id);
    	$this->db->where('end_date_time >=', $startDate);
    	$this->db->where('end_date_time <', $endDate);
    	$this->db->group_by('DATE_FORMAT(end_date_time, "%Y%m%d")');
    	$this->db->order_by('end_date_time', 'ASC');
    	return $this->db->get()->result();
    	//echo $this->db->last_query();exit;
    }

    public function getDateDiff($startDate = '', $endDate = ''){
    	$sql = "SELECT DATEDIFF('".$endDate."','".$startDate."') as days";
		return $this->db->query($sql)->result();
    }

    public function getTransformerDetailsWithGroupConcatGraph($p_device_id = 0){
    	$sql = "SELECT 
				    ND.*,
				    MD1.device_name p_device_name,
				    GROUP_CONCAT(DISTINCT MD2.device_name
				        ORDER BY ND.t_in_device_id ASC
				        SEPARATOR ' + ') t_in_device_name,
				    GROUP_CONCAT(DISTINCT MD3.device_name
				        ORDER BY ND.t_out_device_id ASC
				        SEPARATOR ' + ') t_out_device_name
				FROM
				    network_diogonastic ND
				        LEFT JOIN
				    master_device MD1 ON MD1.device_id = ND.p_device_id
				        LEFT JOIN
				    master_device MD2 ON MD2.device_id = ND.t_in_device_id
				        LEFT JOIN
				    master_device MD3 ON MD3.device_id = ND.t_out_device_id
				WHERE
				    ND.p_device_id = ".$p_device_id."
				GROUP BY ND.p_device_id";
		return $this->db->query($sql)->result();		
    }

    public function getParentDeviceGraph($parentDeviceId = 0, $startDate = '', $endDate = '', $day='1'){
    	if($day == 1){
    		$sqlDate = " EMD.end_date_time ";
    	}else{
    		$sqlDate = " DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') ";
    	}
    	$sql = "SELECT 
				    AVG(EMD.data_KW) KW,
				    SUM(EMD.data_KW) sum_KW,
				    SUM(EMD.data_KW / EMD.data_PF) KWPF,
				    ".$sqlDate." end_date_time
				FROM
				    network_diogonastic ND
				        JOIN
				    ems_meter_data EMD ON EMD.device_id = ND.p_device_id
				WHERE ND.p_device_id = '".$parentDeviceId."'
			            AND EMD.end_date_time >= '".$startDate."'
			            AND EMD.end_date_time < '".$endDate."'
				GROUP BY ".$sqlDate." , ND.p_device_id";
		return $this->db->query($sql)->result();
    }

    public function getTransOutDeviceGraph($parentDeviceId = 0, $startDate = '', $endDate = '', $day='1'){
    	if($day == 1){
    		$sqlDate = " EMD.end_date_time ";
    	}else{
    		$sqlDate = " DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') ";
    	}
    	$sql = "SELECT 
				    SUM(TBL1.KW) KW,
				    SUM(TBL1.sum_KW) sum_KW,
				    SUM(TBL1.KWPF) KWPF,
				    TBL1.end_date_time
				FROM
				    (SELECT 
				        AVG(EMD.data_KW) KW,
				            SUM(EMD.data_KW) sum_KW,
				            SUM(EMD.data_KW / EMD.data_PF) KWPF,
				            ".$sqlDate." end_date_time
				    FROM
				        network_diogonastic ND
				    JOIN ems_meter_data EMD ON EMD.device_id = ND.t_out_device_id
				    WHERE
			        	ND.p_device_id = '".$parentDeviceId."'
			            AND EMD.end_date_time >= '".$startDate."'
			            AND EMD.end_date_time < '".$endDate."'
				    GROUP BY ".$sqlDate." , ND.t_out_device_id
				    ORDER BY EMD.end_date_time ASC , ND.t_out_device_id) TBL1
				GROUP BY TBL1.end_date_time";

		return $this->db->query($sql)->result();
    }

    public function getTransInDeviceGraph($parentDeviceId = 0, $startDate = '', $endDate = '', $day='1'){
    	if($day == 1){
    		$sqlDate = " EMD.end_date_time ";
    	}else{
    		$sqlDate = " DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') ";
    	}
    	$sql = "SELECT 
				    SUM(TBL1.KW) KW,
				    SUM(TBL1.sum_KW) sum_KW,
				    SUM(TBL1.KWPF) KWPF,
				    TBL1.end_date_time
				FROM
				    (SELECT 
				        AVG(EMD.data_KW) KW,
				            SUM(EMD.data_KW) sum_KW,
				            SUM(EMD.data_KW / EMD.data_PF) KWPF,
				            ".$sqlDate." end_date_time
				    FROM
				        network_diogonastic ND
				    JOIN ems_meter_data EMD ON EMD.device_id = ND.t_in_device_id
				    WHERE
			        	ND.p_device_id = '".$parentDeviceId."'
			            AND EMD.end_date_time >= '".$startDate."'
			            AND EMD.end_date_time < '".$endDate."'
				    GROUP BY ".$sqlDate." , ND.t_in_device_id
				    ORDER BY EMD.end_date_time ASC , ND.t_in_device_id) TBL1
				GROUP BY TBL1.end_date_time";

		return $this->db->query($sql)->result();
    }


    public function getTransInDetailsGraph($id = 0, $startDate = '', $endDate = '', $day='1'){
    	if($day > 1){
    		$sqlDate = " DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') ";
    	}else{
    		$sqlDate = " EMD.end_date_time ";
    	}
    	$sql = "SELECT 
			        AVG(EMD.data_KW) KW,
		            SUM(EMD.data_KW) sum_KW,
		            SUM(EMD.data_KW / EMD.data_PF) KWPF,
		            SUM(EMD.data_PF) sum_PF,
		            ND.t_in_device_capacity,
		            ".$sqlDate." end_date_time
			    FROM
			        network_diogonastic ND
			    JOIN ems_meter_data EMD ON EMD.device_id = ND.t_in_device_id
			    WHERE
		        	ND.t_in_device_id = '".$id."'
		            AND EMD.end_date_time >= '".$startDate."'
		            AND EMD.end_date_time < '".$endDate."'
			    GROUP BY ".$sqlDate."
			    ORDER BY EMD.end_date_time ASC ";
				//echo $sql;exit;
		return $this->db->query($sql)->result();
    }

    public function getTransOutDetailsGraph($id = 0, $startDate = '', $endDate = '', $day='1'){
    	if($day > 1){
    		$sqlDate = " DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') ";
    	}else{
    		$sqlDate = " EMD.end_date_time ";
    	}
    	$sql = "SELECT 
		        	AVG(EMD.data_KW) KW,
		            SUM(EMD.data_KW) sum_KW,
		            SUM(EMD.data_KW / EMD.data_PF) KWPF,
		            ".$sqlDate." end_date_time
			    FROM
			        network_diogonastic ND
			    JOIN ems_meter_data EMD ON EMD.device_id = ND.t_out_device_id
			    WHERE
		        	ND.t_out_device_id = '".$id."'
		            AND EMD.end_date_time >= '".$startDate."'
		            AND EMD.end_date_time < '".$endDate."'
			    GROUP BY ".$sqlDate."
			    ORDER BY EMD.end_date_time ASC ";

		return $this->db->query($sql)->result();
    }

    public function transformerDetailsWithDateRange($startDate = '', $endDate = '', $selectedCapacity = 0){
    	
    	/*((AVG(EMDIN.data_KW)/(SUM(EMDIN.data_KW) / SUM(EMDIN.data_KW / EMDIN.data_PF))/ND.t_in_device_capacity) * 100) loading*/
    	$sql = "SELECT 
				    TBL1.*,
				    TBL2.t_out_kw,
				    MD.device_name,
				    (TBL1.t_in_kw - TBL2.t_out_kw) loss_kw,
				    (((TBL1.t_in_kw - TBL2.t_out_kw) / TBL1.t_in_kw) * 100) loss_per
				FROM
				    (SELECT 
				        ND.*, AVG(EMDIN.data_KW) t_in_kw,
            			AVG(((EMDIN.data_KW / EMDIN.data_PF) / ND.t_in_device_capacity) * 100) loading
				    FROM
				        network_diogonastic ND
				    LEFT JOIN ems_meter_data EMDIN ON EMDIN.device_id = ND.t_in_device_id
				    WHERE
				        1
				            AND EMDIN.end_date_time >= '".$startDate."'
				            AND EMDIN.end_date_time < '".$endDate."'
				    GROUP BY ND.t_in_device_id) TBL1
				        LEFT JOIN
				    (SELECT 
				        ND.t_out_device_id, AVG(EMDOUT.data_KW) t_out_kw
				    FROM
				        network_diogonastic ND
				    LEFT JOIN ems_meter_data EMDOUT ON EMDOUT.device_id = ND.t_out_device_id
				    WHERE
				        1
				            AND EMDOUT.end_date_time >= '".$startDate."'
				            AND EMDOUT.end_date_time < '".$endDate."'
				    GROUP BY ND.t_out_device_id) TBL2 ON TBL2.t_out_device_id = TBL1.t_out_device_id
				        LEFT JOIN
				    master_device MD ON MD.device_id = TBL1.p_device_id";
		if($selectedCapacity > 0){
			$sql .= " WHERE TBL1.t_in_device_capacity = ".$selectedCapacity;
		}	    
		$sql .= " ORDER BY TBL1.p_device_id ASC, TBL1.id ASC";
		//echo $sql ;
		return $this->db->query($sql)->result();		
    }

    public function getTransformerCapacity(){
    	$this->db->distinct('t_in_device_capacity');
    	$this->db->select('t_in_device_capacity');
    	$this->db->from('network_diogonastic');
    	$this->db->order_by('t_in_device_capacity');
    	return $this->db->get()->result();
    }

    public function lastDate(){
    	$sql = "SELECT DATE_FORMAT(MAX(end_date_time), '%Y-%m-%d') max_date, DATE_FORMAT(MIN(end_date_time), '%Y-%m-%d') min_date
		FROM ems_meter_data WHERE DATE_FORMAT(end_date_time, '%Y-%m-%d') < '".date('Y-m-d')."'";
		return $this->db->query($sql)->result();
    }



}