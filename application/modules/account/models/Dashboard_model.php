<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
		log_message('INFO', 'account/models/Dashboard_model enter');
        
    }

    public function getEmsLast15DataTypeWise(){
        $sql = "SELECT 
                    MD.type_text, MD.type_level, SUM(EMD.data_KW) total_data
                FROM
                    master_device MD
                        LEFT JOIN
                    ems_meter_data EMD ON EMD.device_id = MD.device_id
                WHERE
                    MD.type != ''
                        AND EMD.end_date_time = (SELECT 
                            last_run_date_time
                        FROM
                            cron_hist)
                GROUP BY MD.type_text
                ORDER BY MD.type_text DESC";
        //echo $sql;        
        return $this->db->query($sql)->result();
    }

    public function getEmsMonthlyDataTypeWise(){
        $sql = "SELECT 
                    newTbl.type_text,
                    newTbl.type_level,
                    SUM(newTbl.data_KW) total_data
                FROM
                    (SELECT 
                        MD.type_text,
                            MD.type_level,
                            AVG(NULLIF(EMD.data_KW, 0)) data_KW
                    FROM
                        master_device MD
                    LEFT JOIN ems_meter_data EMD ON EMD.device_id = MD.device_id
                    WHERE
                        MD.type != ''
                            AND MONTH(EMD.end_date_time) = MONTH(CURRENT_DATE())
                    GROUP BY MD.device_id) newTbl
                GROUP BY newTbl.type_text
                ORDER BY newTbl.type_text DESC";
        return $this->db->query($sql)->result();
    }

    public function getAirLast15DataTypeWise(){
        $sql = "SELECT 
                    AM.type, 
                    SUM(AMD.TTL_flow) total_ttl_flow, 
                    SUM(AMD.flow) total_flow, 
                    COUNT(AM.meter_id) meter_count
                FROM
                    air_meter_data AMD
                        LEFT JOIN
                    air_meter AM ON AM.meter_id = AMD.meter_id
                WHERE
                    AMD.end_date_time = (SELECT 
                                            last_cron_run_date
                                        FROM
                                            agg_cron_hist)
                GROUP BY AM.type
                ORDER BY AM.type";
        return $this->air_db->query($sql)->result();        
    }

    public function getAirMonthlyDataTypeWise(){
        $sql = "SELECT 
                    TBL1.type,
                    SUM(TBL1.avg_ttl_flow) total_ttl_flow,
                    SUM(TBL1.avg_flow) total_flow
                FROM
                    (SELECT 
                        AM.type,
                            AVG(AMD.TTL_flow) avg_ttl_flow,
                            AVG(AMD.flow) avg_flow
                    FROM
                        air_meter_data AMD
                    LEFT JOIN air_meter AM ON AM.meter_id = AMD.meter_id
                    WHERE 1
                            AND MONTH(AMD.end_date_time) = MONTH(CURRENT_DATE())
                            
                    GROUP BY AM.meter_id
                    ORDER BY AM.type) TBL1
                GROUP BY TBL1.type
                ORDER BY TBL1.type";
        return $this->air_db->query($sql)->result();
    }

    public function getSteamLast15DataTypeWise(){
        $sql = "SELECT 
                    AM.type,
                    SUM(AMD.TTL_flow) total_ttl_flow,
                    SUM(AMD.flow) total_flow,
                    COUNT(AM.meter_id) meter_count
                FROM
                    steam_meter_data AMD
                        LEFT JOIN
                    steam_meter AM ON AM.meter_id = AMD.meter_id
                WHERE
                    AMD.end_date_time = (SELECT 
                            last_cron_run_date
                        FROM
                            agg_cron_hist WHERE id=1)
                GROUP BY AM.type
                ORDER BY AM.type";
        return $this->fm_db->query($sql)->result();        
    }

    public function getSteamMonthlyDataTypeWise(){
        $sql = "SELECT 
                    TBL1.type,
                    SUM(TBL1.avg_ttl_flow) total_ttl_flow,
                    SUM(TBL1.avg_flow) total_flow
                FROM
                    (SELECT 
                        AM.type,
                            AVG(AMD.TTL_flow) avg_ttl_flow,
                            AVG(AMD.flow) avg_flow
                    FROM
                        steam_meter_data AMD
                    LEFT JOIN steam_meter AM ON AM.meter_id = AMD.meter_id
                    WHERE
                        1
                            AND MONTH(AMD.end_date_time) = MONTH(CURRENT_DATE())
                            
                    GROUP BY AM.meter_id
                    ORDER BY AM.type) TBL1
                GROUP BY TBL1.type
                ORDER BY TBL1.type";
        return $this->fm_db->query($sql)->result();
    }

    public function getEmsDataFetchingTime(){
        $retData = array();
        $sql = "SELECT last_run_date_time cron_last_run_1, last_view_date_time end_date_time from cron_hist WHERE id=1";
        $data = $this->db->query($sql)->result();
        $retData['last_run'] = $data[0]->cron_last_run_1;
        //$sql = "SELECT max(`end_date_time`) end_date_time FROM `ems_meter_data`";
        /*$sql = "SELECT last_view_date_time end_date_time from cron_hist WHERE id=1";
        $data = $this->db->query($sql)->result();*/
        $retData['last_run_data'] = $data[0]->end_date_time;
        return $retData;
    }

    public function getEmsCppDataFetchingTime(){
        $retData = array();
        $sql = "SELECT last_run_date_time as cron_last_run_1, last_view_date_time end_date_time from cron_hist WHERE id=1";
        $data = $this->ems_cpp->query($sql)->result();
        $retData['last_run'] = $data[0]->cron_last_run_1;
        //$sql = "SELECT max(`end_date_time`) end_date_time FROM `ems_meter_data`";
        /*$sql = "SELECT last_view_date_time end_date_time from cron_hist WHERE id=1";
        $data = $this->ems_cpp->query($sql)->result();*/
        $retData['last_run_data'] = $data[0]->end_date_time;
        return $retData;
    }

    public function getSteamDataFetchingTime(){
        $retData = array();
        $sql = "SELECT last_cron_run_date from agg_cron_hist";
        $data = $this->fm_db->query($sql)->result();
        $retData['last_run'] = $data[0]->last_cron_run_date;
        $sql = "SELECT MAX(end_date_time) end_date_time from steam_meter_data";
        $data = $this->fm_db->query($sql)->result();
        $retData['last_run_data'] = $data[0]->end_date_time;
        return $retData;
    }

    public function getAirDataFetchingTime(){
        $retData = array();
        $sql = "SELECT last_cron_run_date from agg_cron_hist";
        $data = $this->air_db->query($sql)->result();
        $retData['last_run'] = $data[0]->last_cron_run_date;
        $sql = "SELECT MAX(end_date_time) end_date_time from air_meter_data";
        $data = $this->air_db->query($sql)->result();
        $retData['last_run_data'] = $data[0]->end_date_time;
        return $retData;
    }

    public function getDataLogDataFetchingTime(){
        $retData = array();
        $sql = "SELECT last_cron_run_date from agg_cron_hist";
        $data = $this->welspun_datalog->query($sql)->result();
        $retData['last_run'] = $data[0]->last_cron_run_date;
        $sql = "SELECT MAX(end_date_time) end_date_time from datalog_meter_data";
        $data = $this->welspun_datalog->query($sql)->result();
        $retData['last_run_data'] = $data[0]->end_date_time;
        return $retData;
    }

    public function getWeavingDataFetchingTime(){
        $retData = array();
        $sql = "SELECT * from welspun_weaving.cron_hist WHERE id = 1";
        $data = $this->db->query($sql)->result();
        $retData['last_run'] = $data[0]->last_run_date_time;
        $retData['last_run_data'] = $data[0]->last_view_date_time;
        return $retData;
    }

    
    
    
}
