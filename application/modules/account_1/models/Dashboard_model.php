<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends CI_Model {
    private $fm_db;
    private $air_db;
    function __construct() {
        parent::__construct();
		log_message('INFO', 'account/models/Dashboard_model enter');
        $this->fm_db = $this->load->database('welspun_fm', TRUE);
        $this->air_db = $this->load->database('welspun_air', TRUE);
    }

    public function getEmsLast15DataTypeWise(){
        $sql = "SELECT 
                    MD.type_text, MD.type_level, SUM(AGD1.data) total_data
                FROM
                    master_tag MT
                        JOIN
                    relation_device_tag RDT ON RDT.tag_id = MT.tag_id
                        JOIN
                    master_device MD ON MD.device_id = RDT.device_id AND MD.type != ''
                        LEFT JOIN
                    aggregate_data_1 AGD1 ON AGD1.tag_id = MT.tag_id
                WHERE
                    1 AND MT.short_name = 'KW'

                        AND AGD1.end_date_time = (SELECT MIN(ACH.cron_last_run_1) end_date_time from master_device MD LEFT JOIN relation_device_tag RDT ON RDT.device_id=MD.device_id JOIN aggregate_cron_hist ACH ON ACH.tag_id = RDT.tag_id WHERE MD.type != '' ORDER BY MD.device_id)

                GROUP BY MD.type_text
                ORDER BY MD.type_text DESC";
        //echo $sql;        
        return $this->db->query($sql)->result();
    }

    public function getEmsMonthlyDataTypeWise(){
        $sql = "SELECT 
                    TBL1.type_text,
                    TBL1.type_text,
                    SUM(TBL1.total_data) total_data
                FROM
                    (SELECT 
                        MD.type_text, MD.type_level, AVG(AGD1.data) total_data
                    FROM
                        master_tag MT
                    JOIN relation_device_tag RDT ON RDT.tag_id = MT.tag_id
                    JOIN master_device MD ON MD.device_id = RDT.device_id
                    LEFT JOIN aggregate_data_1 AGD1 ON AGD1.tag_id = MT.tag_id
                    WHERE
                        1 AND MT.short_name = 'KW'
                            AND YEAR(AGD1.end_date_time) = YEAR(CURRENT_DATE())
                            AND MONTH(AGD1.end_date_time) = MONTH(CURRENT_DATE())
                            
                    GROUP BY MD.device_id
                    ORDER BY MD.type_text DESC) TBL1
                GROUP BY TBL1.type_text
                ORDER BY TBL1.type_text DESC";
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
                                            MAX(end_date_time)
                                        FROM
                                            air_meter_data)
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
                    WHERE
                        YEAR(AMD.end_date_time) = YEAR(CURRENT_DATE())
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
                                            MAX(end_date_time)
                                        FROM
                                            steam_meter_data)
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
                        YEAR(AMD.end_date_time) = YEAR(CURRENT_DATE())
                            AND MONTH(AMD.end_date_time) = MONTH(CURRENT_DATE())
                            
                    GROUP BY AM.meter_id
                    ORDER BY AM.type) TBL1
                GROUP BY TBL1.type
                ORDER BY TBL1.type";
        return $this->fm_db->query($sql)->result();
    }
    
    
}
