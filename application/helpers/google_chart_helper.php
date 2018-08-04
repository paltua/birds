<?php

function getLowestPaybackPeriod($limit='',$sector_id='',$subsector_id='', $colour=''){

	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $limit_sql = '';
    if($limit){
    	$limit_sql = "LIMIT ".$limit;
    }
    $sector_sql = '';
    if($sector_id){
    	$sector_sql = "AND usr.sector_subsector_id = ".$sector_id;
    }

    $subsector_sql = '';
    $ussr_dec = '';
    if($subsector_id){
        $subsec_arr   = explode(',', $subsector_id); 

        for ($i=0; $i < count($subsec_arr); $i++) { 
            $subsector_sql .= "AND ussr".$i.".sector_subsector_id = ".$subsec_arr[$i]." AND um.unit_id = ussr".$i.".unit_id AND ussr".$i.".sector_subsector_type = 'SUBSEC'";

            $ussr_dec .= ",
                            unit_sector_relation ussr".$i."";
        }
        
    }

    $chart1query = "SELECT ((SUM(rm.actual_investment_rs_lakh )  /
                    SUM(rm.actual_monetary_savings_rs_lakh))*12  ) avg_payback, rtyepmm.rtm_name
        FROM unit_master um,
        unit_sector_relation usr,
        recommendation_master rm ,
        recommendation_text_master rtm,
        recommendation_type_master rtyepmm        
        ".$ussr_dec."   
        WHERE 
        um.unit_id = usr.unit_id
        AND usr.sector_subsector_type='SEC'
        AND rm.rec_id = rtm.rec_id
        AND rtyepmm.rtm_id = rm.rtm_id
       	".$sector_sql."
        ".$subsector_sql."
        AND um.unit_id = rm.unit_id
        GROUP BY rtyepmm.rtm_name
        ORDER BY avg_payback ASC ".$limit_sql." ;";


        $CI->data['chart1data'] =$chart1data = $CI->tbl_generic_model->ExecuteQuery($chart1query);

        $chart1data_str = '';

        foreach ($chart1data as $key => $value) {
            if($value->avg_payback!=0){
                $chart1data_str .= "['".$value->rtm_name."',".$value->avg_payback.", '".$colour."'],";
            }
            
        }
	
	return $chart1data_str;	
}


function getRecommendationHighestEnergySaving($limit='',$sector_id='',$subsector_id='', $colour=''){

	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $limit_sql = '';
    if($limit){
    	$limit_sql = "LIMIT ".$limit;
    }
    $sector_sql = '';
    if($sector_id){
    	$sector_sql = "AND usr.sector_subsector_id = ".$sector_id;
    }
    $subsector_sql = '';
    $ussr_dec = '';
    if($subsector_id){
        $subsec_arr   = explode(',', $subsector_id); 

        for ($i=0; $i < count($subsec_arr); $i++) { 
            $subsector_sql .= "AND ussr".$i.".sector_subsector_id = ".$subsec_arr[$i]." AND um.unit_id = ussr".$i.".unit_id AND ussr".$i.".sector_subsector_type = 'SUBSEC'";

            $ussr_dec .= ",
                            unit_sector_relation ussr".$i."";
        }
        
    }

    $chart2query = "SELECT SUM(rm.proposed_total_energy_savings_mtoe ) proposed_total_energy_savings_mtoe, rtyepmm.rtm_name
        FROM unit_master um,
        unit_sector_relation usr,
        recommendation_master rm,
        recommendation_text_master rtm,
        recommendation_type_master rtyepmm  
        ".$ussr_dec."      
        WHERE 
        um.unit_id = usr.unit_id
        AND usr.sector_subsector_type='SEC'
        AND rm.rec_id = rtm.rec_id
        AND rtyepmm.rtm_id = rm.rtm_id
        ".$sector_sql."
        ".$subsector_sql."
        AND um.unit_id = rm.unit_id
        GROUP BY rtyepmm.rtm_name
        ORDER BY proposed_total_energy_savings_mtoe DESC ".$limit_sql.";";


        $CI->data['chart2data'] =$chart2data = $CI->tbl_generic_model->ExecuteQuery($chart2query);

        $chart2data_str = '';

        foreach ($chart2data as $key => $value) {
            if($value->proposed_total_energy_savings_mtoe!=0){
                $chart2data_str .= "['".$value->rtm_name."',".$value->proposed_total_energy_savings_mtoe.", '".$colour."'],";
            }            
        }
	
	return $chart2data_str;	
}

function getUnitsBasedOnNoImplementation($limit='',$sector_id='',$subsector_id='', $colour=''){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $limit_sql = '';
    if($limit){
        $limit_sql = "LIMIT ".$limit;
    }
    $sector_sql = '';
    if($sector_id){
        $sector_sql = "AND usr.sector_subsector_id = ".$sector_id;
    }
    $subsector_sql = '';
    $ussr_dec = '';
    if($subsector_id){
        $subsec_arr   = explode(',', $subsector_id); 

        for ($i=0; $i < count($subsec_arr); $i++) { 
            $subsector_sql .= "AND ussr".$i.".sector_subsector_id = ".$subsec_arr[$i]." AND um.unit_id = ussr".$i.".unit_id AND ussr".$i.".sector_subsector_type = 'SUBSEC'";

            $ussr_dec .= ",
                            unit_sector_relation ussr".$i."";
        }
        
    } 

    $chart2query = "SELECT count(rm.rec_id) as cnt, rtyepmm.rtm_name
                    FROM unit_master um,
                    unit_sector_relation usr,
                    recommendation_master rm,
                    recommendation_text_master rtm,                    
                    recommendation_type_master rtyepmm 
                    ".$ussr_dec."
                    WHERE um.unit_id = usr.unit_id
                    AND um.unit_id = usr.unit_id
                    AND usr.sector_subsector_type = 'SEC'
                    AND um.unit_id = rm.unit_id
                    AND rm.mv_status_yes_no = 'yes'
                    AND (rm.working_status = 'Implemented' OR rm.working_status ='In Progress')
                    AND rm.rec_id = rtm.rec_id
                    AND rtyepmm.rtm_id = rm.rtm_id
                    ".$sector_sql."
                    ".$subsector_sql."
                    GROUP BY rtyepmm.rtm_name
                    ORDER BY cnt DESC ".$limit_sql."
                    ;";


        $CI->data['chart2data'] =$chart2data = $CI->tbl_generic_model->ExecuteQuery($chart2query);

        $chart2data_str = '';

        foreach ($chart2data as $key => $value) {
            if($value->cnt!=0){
                $chart2data_str .= "['".$value->rtm_name."',".$value->cnt.", '".$colour."'],";
            }            
        }
    
    return $chart2data_str; 
}

function getEcpsBasedOnInvestment($limit='',$sector_id='',$subsector_id='', $colour=''){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $limit_sql = '';
    if($limit){
        $limit_sql = "LIMIT ".$limit;
    }
    $sector_sql = '';
    if($sector_id){
        $sector_sql = "AND usr.sector_subsector_id = ".$sector_id;
    }
    $subsector_sql = '';
    $ussr_dec = '';
    if($subsector_id){
        $subsec_arr   = explode(',', $subsector_id); 

        for ($i=0; $i < count($subsec_arr); $i++) { 
            $subsector_sql .= "AND ussr".$i.".sector_subsector_id = ".$subsec_arr[$i]." AND um.unit_id = ussr".$i.".unit_id AND ussr".$i.".sector_subsector_type = 'SUBSEC'";

            $ussr_dec .= ",
                            unit_sector_relation ussr".$i."";
        }
        
    } 

    $chart2query = "SELECT SUM(rm.actual_investment_rs_lakh) as investment, rtyepmm.rtm_name
                    FROM unit_master um,
                    unit_sector_relation usr,
                    recommendation_master rm,
                    recommendation_text_master rtm,
                    recommendation_type_master rtyepmm 
                    ".$ussr_dec."
                    WHERE um.unit_id = usr.unit_id
                    AND um.unit_id = usr.unit_id
                    AND usr.sector_subsector_type = 'SEC'
                    AND um.unit_id = rm.unit_id
                    AND rm.rec_id = rtm.rec_id
                    AND rtyepmm.rtm_id = rm.rtm_id
                    ".$sector_sql."
                    ".$subsector_sql."
                    GROUP BY rtyepmm.rtm_name
                    ORDER BY investment DESC 
                    ".$limit_sql."
                    ";


        $CI->data['chart2data'] =$chart2data = $CI->tbl_generic_model->ExecuteQuery($chart2query);

        $chart2data_str = '';

        foreach ($chart2data as $key => $value) {
            if($value->investment!=0){
                $chart2data_str .= "['".$value->rtm_name."',".round($value->investment,2).", '".$colour."'],";
            }            
        }
    
    return $chart2data_str; 
}


function getEcpsBasedOnCarbonEmissionReduction($limit='',$sector_id='',$subsector_id='', $colour=''){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $limit_sql = '';
    if($limit){
        $limit_sql = "LIMIT ".$limit;
    }
    $sector_sql = '';
    if($sector_id){
        $sector_sql = "AND usr.sector_subsector_id = ".$sector_id;
    }
    $subsector_sql = '';
    $ussr_dec = '';
    if($subsector_id){
        $subsec_arr   = explode(',', $subsector_id); 

        for ($i=0; $i < count($subsec_arr); $i++) { 
            $subsector_sql .= "AND ussr".$i.".sector_subsector_id = ".$subsec_arr[$i]." AND um.unit_id = ussr".$i.".unit_id AND ussr".$i.".sector_subsector_type = 'SUBSEC'";

            $ussr_dec .= ",
                            unit_sector_relation ussr".$i."";
        }
        
    } 

    $chart2query = "SELECT SUM(rm.actual_annual_ghg_reduction_tco2e) as cer, rtyepmm.rtm_name
                    FROM unit_master um,
                    unit_sector_relation usr,
                    recommendation_master rm,
                    recommendation_text_master rtm,                    
                    recommendation_type_master rtyepmm 
                    ".$ussr_dec."
                    WHERE um.unit_id = usr.unit_id
                    AND um.unit_id = usr.unit_id
                    AND usr.sector_subsector_type = 'SEC'
                    AND um.unit_id = rm.unit_id
                    AND rm.rec_id = rtm.rec_id
                    AND rtyepmm.rtm_id = rm.rtm_id
                    ".$sector_sql."
                    ".$subsector_sql."
                    GROUP BY rtyepmm.rtm_name
                    ORDER BY cer DESC 
                    ".$limit_sql."
                    ";


        $CI->data['chart2data'] =$chart2data = $CI->tbl_generic_model->ExecuteQuery($chart2query);

        $chart2data_str = '';

        foreach ($chart2data as $key => $value) {
            if($value->cer!=0){
                $chart2data_str .= "['".$value->rtm_name."',".round($value->cer,2).", '".$colour."'],";
            }            
        }
    
    return $chart2data_str; 
}



function get_technical_risk_factor($limit='',$sector_id='',$subsector_id='', $colour=''){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $limit_sql = '';
    if($limit){
        $limit_sql = "LIMIT ".$limit;
    }
    $sector_sql = '';
    if($sector_id){
        $sector_sql = "AND usr.sector_subsector_id = ".$sector_id;
    }
    $subsector_sql = '';
    $ussr_dec = '';
    if($subsector_id){
        $subsec_arr   = explode(',', $subsector_id); 

        for ($i=0; $i < count($subsec_arr); $i++) { 
            $subsector_sql .= "AND ussr".$i.".sector_subsector_id = ".$subsec_arr[$i]." AND um.unit_id = ussr".$i.".unit_id AND ussr".$i.".sector_subsector_type = 'SUBSEC'";

            $ussr_dec .= ",
                            unit_sector_relation ussr".$i."";
        }
        
    }

    $chart2query = "SELECT (SUM(rm.actual_monetary_savings_rs_lakh) / SUM(rm.proposed_monetary_savings_lakh_inr) ) achievement_ratio,rtyepmm.rtm_name
        FROM unit_master um,
        unit_sector_relation usr,
        recommendation_master rm,
        recommendation_type_master rtyepmm 
        ".$ussr_dec."       
        WHERE 
        um.unit_id = usr.unit_id
        AND usr.sector_subsector_type='SEC'
        AND um.unit_id = rm.unit_id 
        AND rtyepmm.rtm_id = rm.rtm_id         
        AND rm.mv_status_yes_no = 'Yes'
        AND (rm.working_status = 'Implemented' OR rm.working_status ='In Progress')
        ".$sector_sql."
        ".$subsector_sql."
        GROUP BY rtyepmm.rtm_name
        ORDER BY achievement_ratio DESC 
        ".$limit_sql.";";


    $CI->data['chart2data'] =$chart2data = $CI->tbl_generic_model->ExecuteQuery($chart2query);

    $chart2data_str = '';

    foreach ($chart2data as $key => $value) {
        if(((1-$value->achievement_ratio) >0) && ($value->achievement_ratio!=0)){
            $chart2data_str .= "['".$value->rtm_name."',".round(((1-$value->achievement_ratio)*100),2).", '".$colour."','<div class=\'ggl-tooltip\'><strong>".$value->rtm_name."</strong></br>Technical Risk Factor: <strong>".round(((1-$value->achievement_ratio)*100),2)."%</strong></div>'],";
        }
    }
    
    return $chart2data_str; 
}

function get_financial_risk_factor($limit='',$sector_id='',$subsector_id='', $colour=''){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $limit_sql = '';
    if($limit){
        $limit_sql = "LIMIT ".$limit;
    }
    $sector_sql = '';
    if($sector_id){
        $sector_sql = "AND usr.sector_subsector_id = ".$sector_id;
    }
    $subsector_sql = '';
    $ussr_dec = '';
    if($subsector_id){
        $subsec_arr   = explode(',', $subsector_id); 

        for ($i=0; $i < count($subsec_arr); $i++) { 
            $subsector_sql .= "AND ussr".$i.".sector_subsector_id = ".$subsec_arr[$i]." AND um.unit_id = ussr".$i.".unit_id AND ussr".$i.".sector_subsector_type = 'SUBSEC'";

            $ussr_dec .= ",
                            unit_sector_relation ussr".$i."";
        }
        
    }

    $chart2query = "SELECT IFNULL((SUM(rm.proposed_investment_rs_lakh) / SUM(rm.actual_investment_rs_lakh) ),0) achievement_ratio,rtyepmm.rtm_name
        FROM unit_master um,
        unit_sector_relation usr,
        recommendation_master rm,
        recommendation_type_master rtyepmm 
        ".$ussr_dec."       
        WHERE 
        um.unit_id = usr.unit_id
        AND usr.sector_subsector_type='SEC'
        AND um.unit_id = rm.unit_id 
        AND rtyepmm.rtm_id = rm.rtm_id          
        AND rm.mv_status_yes_no = 'Yes'
        AND (rm.working_status = 'Implemented' OR rm.working_status ='In Progress')
        ".$sector_sql."
        ".$subsector_sql."
        GROUP BY rtyepmm.rtm_name
        ORDER BY achievement_ratio DESC 
        ".$limit_sql.";";


    $CI->data['chart2data'] =$chart2data = $CI->tbl_generic_model->ExecuteQuery($chart2query);

    $chart2data_str = '';
    //pr($chart2data);

    foreach ($chart2data as $key => $value) {
        if(((1-$value->achievement_ratio) >0) && ($value->achievement_ratio!=0)){
            $chart2data_str .= "['".$value->rtm_name."',".round(((1-$value->achievement_ratio)*100),2).", '".$colour."','<div class=\'ggl-tooltip\'><strong>".$value->rtm_name."</strong></br>Technical Risk Factor: <strong>".round(((1-$value->achievement_ratio)*100),2)."%</strong></div>'],";
        }
    }
    return $chart2data_str; 
}

////// DUAL CHART ///////
function actualValue($dropdown1){
    $dropdown_1 = $dropdown1;
    $actual = array();
    if($dropdown_1 == 'Energy Savings')
    {
        $actual[] ='actual_total_energy_savings_mtoe';
    }
    if($dropdown_1 == 'Monetary Savings')
    {
        $actual[] ='actual_monetary_savings_rs_lakh';
    }
    if($dropdown_1 == 'GHG Reduction')
    {
        $actual[] ='actual_annual_ghg_reduction_tco2e';
    }
    if($dropdown_1 == 'Investment')
    {
        $actual[] ='actual_investment_rs_lakh';
    }
    if($dropdown_1 == 'Payback Period')
    {
        $actual[] ='actual_payback';
    }
    if($dropdown_1 == 'Technical Risk')
    {
        $actual[] ='technical_risk';
    }
    if($dropdown_1 == 'Financial Risk')
    {
        $actual[] ='financial_risk';
    }
    if($dropdown_1 == 'No of ECMs')
    {
        $actual[] ='recommendation';
    }
    return $actual;
}

function proposedValue($dropdown1){
    $dropdown_1 = $dropdown1;
    $proposed = array();
    if($dropdown_1 == 'Energy Savings')
    {
        $proposed[] ='proposed_total_energy_savings_mtoe';
    }
    if($dropdown_1 == 'Monetary Savings')
    {
        $proposed[] ='proposed_monetary_savings_lakh_inr';
    }
    if($dropdown_1 == 'GHG Reduction')
    {
        $proposed[] ='proposed_annual_ghg_reduction_tco2e';
    }
    if($dropdown_1 == 'Investment')
    {
        $proposed[] ='proposed_investment_rs_lakh';
    }
    if($dropdown_1 == 'Payback Period')
    {
        $proposed[] ='proposed_payback';
    }
    if($dropdown_1 == 'Technical Risk')
    {
        $proposed[] ='technical_risk';
    }
    if($dropdown_1 == 'Financial Risk')
    {
        $proposed[] ='financial_risk';
    }
    if($dropdown_1 == 'No of ECMs')
    {
        $proposed[] ='recommendation';
    }
    return $proposed;
}

function dualChartCluster($dropdown1,$dropdown2,$chartdata,$chartdata1,$name,$actual_0,$actual_1,$proposed_0,$proposed_1){
    $dropdown_1 = $dropdown1;
    $dropdown_2 = $dropdown2;

    $actual =$actual_0[0];
    $actual1 =$actual_1[0];

    $proposed = $proposed_0[0];
    $proposed1 = $proposed_1[0];

    if(($dropdown_1 == 'Technical Risk') || ($dropdown_2 == 'Technical Risk')||($dropdown_1 == 'Financial Risk') || ($dropdown_2 == 'Financial Risk')){
        
        if((($actual == 'technical_risk')&&($actual1 == 'financial_risk')) ||(($actual == 'financial_risk')&&($actual1 == 'technical_risk'))){
            $dualdata_str = '[["Month", " '.$dropdown_1.'", "'.$dropdown_2.'"],';
        }elseif(($actual == 'technical_risk')||($actual == 'financial_risk')){
            $dualdata_str = '[["Month", " '.$dropdown_1.'", "Actual '.$dropdown_2.'", "Proposed '.$dropdown_2.'"],';
        }else{
            $dualdata_str = '[["Month", "Actual '.$dropdown_1.'", "Proposed '.$dropdown_1.'",
            "'.$dropdown_2.'"],';
        }
    }else{
        $dualdata_str = '[["Month", "Actual '.$dropdown_1.'", "Proposed '.$dropdown_1.'",
       "Actual '.$dropdown_2.'", "Proposed '.$dropdown_2.'"],';
    }
    $i=0;
    foreach ($chartdata as $key => $value) 
    {
        if(($dropdown_1 == 'Technical Risk') || ($dropdown_2 == 'Technical Risk')||($dropdown_1 == 'Financial Risk') || ($dropdown_2 == 'Financial Risk')){
            
            if((($actual == 'technical_risk')&&($actual1 == 'financial_risk')) || (($actual == 'financial_risk')&&($actual1 == 'technical_risk'))){
                if((($value->$actual) >= 0) && (($value->$actual1) >= 0)){
                    $dualdata_str .= '["'.$value->$name.'",
                    '.round($value->$actual,2).',
                    '.round($value->$actual1,2).'],';
                }
            }elseif((($actual == 'technical_risk')||($actual == 'financial_risk'))&&($actual1 == 'recommendation')){
                if((($value->$actual) >= 0) && (($value->$actual1) >= 0)){
                    $dualdata_str .= '["'.$value->$name.'",
                    '.round($value->$actual,2).',
                    '.round($value->$actual1,2).',
                    '.round($chartdata1[$i]->recommendation).'],';
                }
            }elseif(($actual == 'recommendation') &&(($actual1 == 'technical_risk')||($actual1 == 'financial_risk'))){
                if((($value->$actual) >= 0) && (($value->$actual1) >= 0)){
                    $dualdata_str .= '["'.$value->$name.'",
                    '.round($value->$actual,2).',
                    '.round($chartdata1[$i]->recommendation).',
                    '.round($value->$actual1,2).'],';
                }          
            }elseif(($actual == 'technical_risk')||($actual == 'financial_risk')){
                if((($value->$actual) >= 0) && (($value->$actual1) >= 0)){
                    $dualdata_str .= '["'.$value->$name.'",
                    '.round($value->$actual,2).',
                    '.round($value->$actual1,2).',
                    '.round($value->$proposed1,2).'],';
                }
            }else{
                if(($value->$proposed1) >= 0){
                    $dualdata_str .= '["'.$value->$name.'",
                    '.round($value->$actual,2).',
                    '.round($value->$proposed,2).',
                    '.round($value->$proposed1,2).'],';
                }
            }
        }elseif($dropdown_1 == 'No of ECMs')
        {
            $dualdata_str .= '["'.$value->$name.'",
            '.round($value->$actual).',
            '.round($chartdata1[$i]->recommendation).',
            '.round($value->$actual1,2).',
            '.round($value->$proposed1,2).'],';
        }elseif($dropdown_2 == 'No of ECMs')
        {
            $dualdata_str .= '["'.$value->$name.'",
            '.round($value->$actual,2).',
            '.round($value->$proposed,2).',
            '.round($value->$actual1).',
            '.round($chartdata1[$i]->recommendation).'],';
        }else{
            $dualdata_str .= '["'.$value->$name.'",
            '.round($value->$actual,2).',
            '.round($value->$proposed,2).',
            '.round($value->$actual1,2).',
            '.round($value->$proposed1,2).'],';
        }
        $i++;
    }
    $dualdata_str = substr($dualdata_str, 0,-1);
    $dualdata_str .= "]";
    return $dualdata_str;
}

function dualChartQuery($sql,$limit_sql,$column_name,$type){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $chartquery = "SELECT SUM(rm.actual_total_energy_savings_mtoe) 
        actual_total_energy_savings_mtoe,SUM(rm.proposed_total_energy_savings_mtoe) 
        proposed_total_energy_savings_mtoe,SUM(rm.actual_monetary_savings_rs_lakh) 
        actual_monetary_savings_rs_lakh,SUM(rm.proposed_monetary_savings_lakh_inr) 
        proposed_monetary_savings_lakh_inr,SUM(rm.actual_annual_ghg_reduction_tco2e) 
        actual_annual_ghg_reduction_tco2e,SUM(rm.proposed_annual_ghg_reduction_tco2e) 
        proposed_annual_ghg_reduction_tco2e,SUM(rm.actual_investment_rs_lakh) 
        actual_investment_rs_lakh,SUM(rm.proposed_investment_rs_lakh) 
        proposed_investment_rs_lakh,(SUM(rm.actual_investment_rs_lakh) / 
        SUM(rm.actual_monetary_savings_rs_lakh)) actual_payback,
        (SUM(rm.proposed_investment_rs_lakh) / SUM(rm.proposed_monetary_savings_lakh_inr)) 
        proposed_payback,((1-(SUM(rm.actual_monetary_savings_rs_lakh)/
        SUM(rm.proposed_monetary_savings_lakh_inr)))%100) technical_risk,
        ((1-(SUM(rm.proposed_investment_rs_lakh)/ SUM(rm.actual_investment_rs_lakh)))%100) 
        financial_risk,count(rm.rec_id) recommendation,
        ".$column_name.",cm.cluster_id,sm.sector_id,
        rtyepmm.rtm_id,rtm.rec_text_id
        FROM unit_master um
        INNER JOIN unit_sector_relation usr ON um.unit_id = usr.unit_id
        INNER JOIN recommendation_master rm ON um.unit_id = rm.unit_id
        INNER JOIN unit_cluster_relation ucr ON um.unit_id = ucr.unit_id
        INNER JOIN cluster_master cm ON ucr.cluster_id = cm.cluster_id
        INNER JOIN sector_master sm ON usr.sector_subsector_id = sm.sector_id
        INNER JOIN recommendation_text_master rtm ON rm.rec_id = rtm.rec_id
        INNER JOIN recommendation_type_master rtyepmm ON rtyepmm.rtm_id = rm.rtm_id
        WHERE usr.sector_subsector_type='".$type."'
        AND rm.mv_status_yes_no = 'Yes'
        AND (rm.working_status = 'Implemented' OR rm.working_status ='In Progress')
        ".$sql."
        GROUP BY ".$column_name."
        ORDER BY actual_total_energy_savings_mtoe DESC ,
        proposed_total_energy_savings_mtoe DESC,
        actual_monetary_savings_rs_lakh DESC,
        proposed_monetary_savings_lakh_inr DESC,
        actual_annual_ghg_reduction_tco2e DESC,
        proposed_annual_ghg_reduction_tco2e DESC,
        actual_investment_rs_lakh DESC,
        proposed_investment_rs_lakh DESC ".$limit_sql.";";
    $CI->data['chartdata'] = $chartdata = $CI->tbl_generic_model->ExecuteQuery($chartquery);
    return $chartdata;
}

function dualChartQueryForEcm($sql,$limit_sql,$column_name,$type){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $chartquery = "SELECT count(rm.rec_id) recommendation,
        ".$column_name.",cm.cluster_id,sm.sector_id,
        rtyepmm.rtm_id,rtm.rec_text_id
        FROM unit_master um
        INNER JOIN unit_sector_relation usr ON um.unit_id = usr.unit_id
        INNER JOIN recommendation_master rm ON um.unit_id = rm.unit_id
        INNER JOIN unit_cluster_relation ucr ON um.unit_id = ucr.unit_id
        INNER JOIN cluster_master cm ON ucr.cluster_id = cm.cluster_id
        INNER JOIN sector_master sm ON usr.sector_subsector_id = sm.sector_id
        INNER JOIN recommendation_text_master rtm ON rm.rec_id = rtm.rec_id
        INNER JOIN recommendation_type_master rtyepmm ON rtyepmm.rtm_id = rm.rtm_id
        WHERE usr.sector_subsector_type='".$type."'
        ".$sql."
        GROUP BY ".$column_name."
        ORDER BY recommendation DESC ".$limit_sql.";";
    $CI->data['chartdata'] = $chartdata = $CI->tbl_generic_model->ExecuteQuery($chartquery);
    return $chartdata;
}

function getDualChartClusterWise($limit='',$dropdown_1,$dropdown_2,$select_cluster=array())
{
    $limit_sql = '';
    if($limit){
        $limit_sql = "LIMIT ".$limit;
    }
    $cluster_sql = '';
    if($select_cluster){
        $cluster_arr   = implode(',', $select_cluster); 
        $cluster_sql .= " AND cm.cluster_id IN (".$cluster_arr.")";
    }
    $type = 'SEC';
    $column_name = 'cm.cluster_name';
    $name = 'cluster_name';
    $chartdata = dualChartQuery($cluster_sql,$limit_sql,$column_name,$type);
    $chartdata1 = dualChartQueryForEcm($cluster_sql,$limit_sql,$column_name,$type);

    $actual_0 = actualValue($dropdown_1);
    $actual_1 = actualValue($dropdown_2);

    $proposed_0 = proposedValue($dropdown_1);
    $proposed_1 = proposedValue($dropdown_2);

    $clusterChartDual = dualChartCluster($dropdown_1,$dropdown_2,$chartdata,$chartdata1,$name,$actual_0,$actual_1,$proposed_0,$proposed_1);
    return $clusterChartDual;
}

function getDualChartSectorWise($limit='',$dropdown_1,$dropdown_2,$sector_name='')
{
    $limit_sql = '';
    if($limit){
        $limit_sql = "LIMIT ".$limit;
    }
    $sector_sql = '';
    if($sector_name){
        $sector_arr   = implode(',', $sector_name); 
        $sector_sql .= " AND sm.sector_id IN (".$sector_arr.")";
    }
    $type = 'SEC';
    $column_name = 'sm.sector_name';
    $name = 'sector_name';
    $chartdata = dualChartQuery($sector_sql,$limit_sql,$column_name,$type);
    $chartdata1 = dualChartQueryForEcm($sector_sql,$limit_sql,$column_name,$type);

    $actual_0 = actualValue($dropdown_1);
    $actual_1 = actualValue($dropdown_2);
    
    $proposed_0 = proposedValue($dropdown_1);
    $proposed_1 = proposedValue($dropdown_2);

    $clusterChartDual = dualChartCluster($dropdown_1,$dropdown_2,$chartdata,$chartdata1,$name,$actual_0,$actual_1,$proposed_0,$proposed_1);
    return $clusterChartDual;
}

function getDualChartSubSectorWise($limit='',$dropdown_1,$dropdown_2,$subsector_name='')
{
    $limit_sql = '';
    if($limit){
        $limit_sql = "LIMIT ".$limit;
    }
    $subsector_sql = '';
    if($subsector_name){
        $subsector_arr   = implode(',', $subsector_name); 
        $subsector_sql .= " AND sm.sector_id IN (".$subsector_arr.")";
    }
    $type = 'SUBSEC';
    $column_name = 'sm.sector_name';
    $name = 'sector_name';
    $chartdata = dualChartQuery($subsector_sql,$limit_sql,$column_name,$type);
    $chartdata1 = dualChartQueryForEcm($subsector_sql,$limit_sql,$column_name,$type);

    $actual_0 = actualValue($dropdown_1);
    $actual_1 = actualValue($dropdown_2);
    
    $proposed_0 = proposedValue($dropdown_1);
    $proposed_1 = proposedValue($dropdown_2);

    $clusterChartDual = dualChartCluster($dropdown_1,$dropdown_2,$chartdata,$chartdata1,$name,$actual_0,$actual_1,$proposed_0,$proposed_1);
    return $clusterChartDual;
}

function getDualChartECMWise($limit='',$dropdown_1,$dropdown_2,$ecm_type='')
{
    $limit_sql = '';
    if($limit){
        $limit_sql = "LIMIT ".$limit;
    }
    $ecm_sql = '';
    if($ecm_type){
        $ecm_arr   = implode(',', $ecm_type); 
        $ecm_sql .= " AND rtyepmm.rtm_id IN (".$ecm_arr.")";
    }
    $type = 'SUBSEC';
    $column_name = 'rtyepmm.rtm_name';
    $name = 'rtm_name';
    $chartdata = dualChartQuery($ecm_sql,$limit_sql,$column_name,$type);
    $chartdata1 = dualChartQueryForEcm($ecm_sql,$limit_sql,$column_name,$type);

    $actual_0 = actualValue($dropdown_1);
    $actual_1 = actualValue($dropdown_2);
    
    $proposed_0 = proposedValue($dropdown_1);
    $proposed_1 = proposedValue($dropdown_2);
    
    $clusterChartDual = dualChartCluster($dropdown_1,$dropdown_2,$chartdata,$chartdata1,$name,$actual_0,$actual_1,$proposed_0,$proposed_1);
    return $clusterChartDual;
}

function getDualChartECMTextWise($limit='',$dropdown_1,$dropdown_2,$ecm_text='')
{
    $limit_sql = '';
    if($limit){
        $limit_sql = "LIMIT ".$limit;
    }
    $ecmtext_sql = '';
    if($ecm_text){
        $ecm_arr   = implode(',', $ecm_text); 
        $ecmtext_sql .= " AND rtm.rec_text_id IN (".$ecm_arr.")";
    }
    $type = 'SUBSEC';
    $column_name = 'rtm.rec_text';
    $name = 'rec_text';
    $chartdata = dualChartQuery($ecmtext_sql,$limit_sql,$column_name,$type);
    $chartdata1 = dualChartQueryForEcm($ecmtext_sql,$limit_sql,$column_name,$type);

    $actual_0 = actualValue($dropdown_1);
    $actual_1 = actualValue($dropdown_2);
    
    $proposed_0 = proposedValue($dropdown_1);
    $proposed_1 = proposedValue($dropdown_2);

    $clusterChartDual = dualChartCluster($dropdown_1,$dropdown_2,$chartdata,$chartdata1,$name,$actual_0,$actual_1,$proposed_0,$proposed_1);
    return $clusterChartDual;
}