<?php

/*
* function getSectorDropdown()
* This function is used to generate sector dropdown html
*/

function getSectorDropdown($sector_id='NULL'){

	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $sectorQuery = "SELECT * FROM sector_master WHERE parent_sector_id =0 ORDER BY sector_name ASC";

	$sector_data = $CI->tbl_generic_model->ExecuteQuery($sectorQuery);

	$sector_str = "";
	$sector_str .= "<option  value=''> -- Select -- </option>";
	foreach ($sector_data as $key => $value) {
		if($sector_id!='NULL' && $sector_id==$value->sector_id){
			$selected = "selected";
		}else{
			$selected = "";
		}
		$sector_str .= "<option  value=".$value->sector_id." ".$selected." >". $value->sector_name."</option>";
	}
	
	return $sector_str;	
}

/*
* function getSubsectorDropdown()
* This function is used to generate subsector dropdown html
*/
function getSubsectorDropdown($sector_id='NULL',$parent_sector_id=''){

	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $sector_str = "";

    if($parent_sector_id!=''){

        

        if(is_array($parent_sector_id)){

            //pr($parent_sector_id);

            $sector_id_arr = '';

            foreach ($parent_sector_id as $kp => $vp) {
                $sector_id_arr .= $vp.",";
            }

            $sector_id_arr = substr($sector_id_arr, 0,-1);

            $subsectorQuery = "SELECT * FROM sector_master WHERE parent_sector_id IN (".$sector_id_arr.") ORDER BY sector_name ASC";

            $data = $CI->tbl_generic_model->ExecuteQuery($subsectorQuery);

            $selectedSubsectorAjax = '';
            foreach ($data as $key => $value) {
                $selectedSubsectorAjax .= $value->sector_id.',';
            }
            $selectedSubsectorAjax = substr($selectedSubsectorAjax, 0,-1);    

            $selectedSubsecAjaxQuery = '';
            if($selectedSubsectorAjax!=''){
                $selectedSubsecAjaxQuery = " AND sector_id IN (".$selectedSubsectorAjax.")";

                $subsector_selected_arr = explode(",",$sector_id);

                $subsectorQuery = "SELECT * FROM sector_master WHERE parent_sector_id !=0".$selectedSubsecAjaxQuery." ORDER BY sector_name ASC";

                $sector_data = $CI->tbl_generic_model->ExecuteQuery($subsectorQuery);

                
                $sector_str .= "<option  value=''> -- Select -- </option>";
                foreach ($sector_data as $key => $value) {
                    if($sector_id!='NULL' && in_array($value->sector_id, $subsector_selected_arr) ){
                        $selected = "selected";
                    }else{
                        $selected = "";
                    }
                    $sector_str .= "<option  value=".$value->sector_id." ".$selected." >". $value->sector_name."</option>";
                }
            }

            

        }else{
            $tbl ='sector_master';
            $fields = '*';
            $where = array('parent_sector_id' => $parent_sector_id);
            $orderby = array('sector_name'=>'ASC');
            $data = $CI->tbl_generic_model->get($tbl,$fields,$where,$orderby);

            $selectedSubsectorAjax = '';
            foreach ($data as $key => $value) {
                $selectedSubsectorAjax .= $value->sector_id.',';
            }
            $selectedSubsectorAjax = substr($selectedSubsectorAjax, 0,-1);    

            $selectedSubsecAjaxQuery = '';
            if($selectedSubsectorAjax!=''){
                $selectedSubsecAjaxQuery = " AND sector_id IN (".$selectedSubsectorAjax.")";

                $subsector_selected_arr = explode(",",$sector_id);

                $subsectorQuery = "SELECT * FROM sector_master WHERE parent_sector_id !=0".$selectedSubsecAjaxQuery." ORDER BY sector_name ASC";

                $sector_data = $CI->tbl_generic_model->ExecuteQuery($subsectorQuery);

                
                $sector_str .= "<option  value=''> -- Select -- </option>";
                foreach ($sector_data as $key => $value) {
                    if($sector_id!='NULL' && in_array($value->sector_id, $subsector_selected_arr) ){
                        $selected = "selected";
                    }else{
                        $selected = "";
                    }
                    $sector_str .= "<option  value=".$value->sector_id." ".$selected." >". $value->sector_name."</option>";
                }
            }

            
        }        
    }    
	
	return $sector_str;	
}




/*
* function getMajorSubsectorDropdown()
* This function is used to generate major subsector dropdown html
*/
function getMajorSubsectorDropdown($subSectorIds = array(), $selectedId = 0){    
    $sector_str = "<option  value=''> -- Select -- </option>";
    if(count($subSectorIds) > 0){
        $CI =& get_instance();
        $CI->load->model('basic_model');
        $sector_data = $CI->basic_model->getMajorSubSector($subSectorIds);
        foreach ($sector_data as $key => $value) {
            if($selectedId > 0 && $value->sector_id == $selectedId){
                $selected = "selected";
            }else{
                $selected = "";
            }
            $sector_str .= "<option  value=".$value->sector_id." ".$selected." >". $value->sector_name."</option>";
        }
    }    
    
    return $sector_str; 
}

/*
* function getTotalNoUnregisteredVisitors()
* This function is used to count visitors based on ip address
*/
function getTotalNoUnregisteredVisitors(){

	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');    

	$subsectorQuery = "SELECT count(DISTINCT ip_address) cnt_ip FROM web_ip_master";

	$ip_data = $CI->tbl_generic_model->ExecuteQuery($subsectorQuery);
	return $ip_data[0]->cnt_ip;	
}

/*
* function getTotalNoOfUnitsSectorSubsectorWise()
* This function is used to count units based on sector subsector
*/
function getTotalNoOfUnitsSectorSubsectorWise($sector_id='',$subsector_id=''){

	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $sector_sql = '';
    $get_unit_name_sql = ''; 

    if($sector_id){
    	$sector_sql = "AND usr.sector_subsector_id = ".$sector_id;
        $get_unit_name_sql = ", sm.sector_name";
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

	$subsectorQuery = "SELECT count(um.unit_id) as cnt ".$get_unit_name_sql."
						FROM unit_master um,
						unit_sector_relation usr,
                        sector_master sm
						".$ussr_dec."
						WHERE um.unit_id = usr.unit_id
						AND um.unit_id = usr.unit_id						
						AND usr.sector_subsector_type = 'SEC'
                        AND sm.sector_id = usr.sector_subsector_id
						".$sector_sql." ".$subsector_sql."
						";

	$unit_data = $CI->tbl_generic_model->ExecuteQuery($subsectorQuery);
	return $unit_data;	
}

/*
* function getTotalNoOfUnitsSectorSubsectorWise()
* This function is used to count recommendation based on different parameters passed
*/
function getTotalNoOfRecommendationSectorSubsectorWise($sector_id='',$subsector_id='', $ic_report ='', $implementation_status='',$mv_report ='',$working_status='',$func=''){

	$CI =& get_instance();
    $CI->load->model('tbl_generic_model'); 

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
    $ic_report_sql = '';
    if($ic_report){
    	$ic_report_sql = "AND rm.ic_report_available_yes_no = 'yes'"; 
    } 
    $implementation_status_sql ='';
    if($implementation_status){
    	$implementation_status_sql = "AND rm.implementation_status = 'implemented'"; 
    } 
    $mv_report_sql = '';
    if($mv_report){
    	$mv_report_sql = "AND rm.mv_status_yes_no = 'yes'"; 
    } 
    $working_status_sql = '';
    if($working_status){
    	$working_status_sql = "AND rm.working_status = 'Implemented'"; 
    }

    if($func='implemented_ecps'){
        $implementation_status_sql ='';
        if($implementation_status){
            $implementation_status_sql = "AND (rm.implementation_status = 'implemented' OR rm.implementation_status ='In Progress')"; 
        } 

        $working_status_sql = '';
        if($working_status){
            $working_status_sql = "AND (rm.working_status = 'Implemented' OR rm.working_status ='In Progress')"; 
        }
    }

	$subsectorQuery = "SELECT count(rm.rec_id) as cnt
						FROM unit_master um,
						unit_sector_relation usr,
						recommendation_master rm
						".$ussr_dec."
						WHERE um.unit_id = usr.unit_id
						AND um.unit_id = usr.unit_id
						".$sector_sql."
						".$subsector_sql."
						AND usr.sector_subsector_type = 'SEC'
						AND um.unit_id = rm.unit_id
						".$ic_report_sql."
						".$implementation_status_sql."
						".$mv_report_sql."
						".$working_status_sql."
						";

	$unit_data = $CI->tbl_generic_model->ExecuteQuery($subsectorQuery);
	return $unit_data[0]->cnt;	
}


/*
* function getProductCategoryDropDown()
* This function is used to get product category dropdown
*/
function getProductCategoryDropDown($pcm_type = 0, $pcm_id = 0){
	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');
	$where = array('pcm_type' => $pcm_type);
	$data = $CI->tbl_generic_model->get('product_category_master','*', $where);
	$pcd_str = '<option  value=""> -- Select -- </option>';
	foreach ($data as $key => $value) {
		if($pcm_id > 0 && $pcm_id == $value->pcm_id){
			$selected = "selected";
		}else{
			$selected = "";
		}
		$pcd_str .= '<option  value="'.$value->pcm_id.'" '.$selected.' >'. $value->pcm_name.'</option>';
	}
	return $pcd_str;
}

/*
* function getMajorProductCategoryDropDown()
* This function is used to to get Major product category dropdown w.r.t. main product category
*/
function getMajorProductCategoryDropDown($pcmIds = array(), $selectedId = 0){
    $pcd_str = '<option  value=""> -- Select -- </option>';
    if(count($pcmIds) > 0){
        $CI =& get_instance();
        $CI->load->model('gap/basic_model');
        $data = $CI->basic_model->getMajorProductCategory($pcmIds);
        foreach ($data as $key => $value) {
            if($selectedId > 0 && $selectedId == $value->pcm_id){
                $selected = "selected";
            }else{
                $selected = "";
            }
            $pcd_str .= '<option  value="'.$value->pcm_id.'" '.$selected.' >'. $value->pcm_name.'</option>';
        }
    }
    return $pcd_str;
}



function getProductCategoryDropDownFormBuilder($pcm_type = 0){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $where = array('pcm_type' => $pcm_type);
    $orderby = array('pcm_name'=>'ASC');
    $data = $CI->tbl_generic_model->get('product_category_master','*', $where,$orderby);

    $arr[''] = '--Select--';
    foreach ($data as $key => $value) {
        $arr[$value->pcm_id] = $value->pcm_name;
    }
    return $arr;
}

function getMajorProductCategoryDropDownFormBuilder($pcmIds = array(), $selectedId = 0){

    $CI =& get_instance();
    $CI->load->model('gap/basic_model');

    if($pcmIds){
        $data = $CI->basic_model->getMajorProductCategory($pcmIds);
        $arr[''] = '--Select--';
        foreach ($data as $key => $value) {
            $arr[$value->pcm_id] = $value->pcm_name;
        } 
    }else{
        $arr[''] = '--Select--';
    }
    
    return $arr;
}


function getEquipmentOrgMasterDropDown($equipment_id = 0){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');  
    $where = array();
    $sql = "SELECT * FROM equipment_master ORDER BY equipment_name";
    $data = $CI->tbl_generic_model->ExecuteQuery($sql);
    $pcd_str = '<option  value=""> -- Select -- </option>';
    foreach ($data as $key => $value) {
        if($equipment_id > 0 && $equipment_id == $value->equipment_id){
            $selected = "selected";
        }else{
            $selected = "";
        }
        $pcd_str .= '<option  value="'.$value->equipment_id.'" '.$selected.' >'. $value->equipment_name.'</option>';
    }
    return $pcd_str;
}

function getEquipmentMasterDropDown($aum_id = 0){
	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');	
    $where = array();
    $sql = "SELECT * FROM application_utility_master ORDER BY aum_name";
	$data = $CI->tbl_generic_model->ExecuteQuery($sql);
	$pcd_str = '<option  value=""> -- Select -- </option>';
	foreach ($data as $key => $value) {
		if($aum_id > 0 && $aum_id == $value->aum_id){
			$selected = "selected";
		}else{
			$selected = "";
		}
		$pcd_str .= '<option  value="'.$value->aum_id.'" '.$selected.' >'. $value->aum_name.'</option>';
	}
	return $pcd_str;
}

function getEquipmentMasterDropDownFormBuilder(){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');  
    $where = array();
    $orderby = array('aum_name'=>'ASC');
    $data = $CI->tbl_generic_model->get('application_utility_master','*', $where,$orderby);

    $arr[''] = '--Select--';
    foreach ($data as $key => $value) {
        $arr[$value->aum_id] = $value->aum_name;
    }
    return $arr;
}


function getFuelMasterDropDown($fuel_id = 0){
	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');	
    $where = array();
	$data = $CI->tbl_generic_model->get('fuel_master','*', $where);
	$pcd_str = '<option  value=""> -- Select -- </option>';
	foreach ($data as $key => $value) {
		if($fuel_id > 0 && $fuel_id == $value->fuel_id){
			$selected = "selected";
		}else{
			$selected = "";
		}
		$pcd_str .= '<option  value="'.$value->fuel_id.'" '.$selected.' >'. $value->fuel_name.'</option>';
	}
	return $pcd_str;
}

function getFuelMasterDropDownFormBuilder(){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');  
    $where = array();
    $orderby = array('fuel_name'=>'ASC');
    $data = $CI->tbl_generic_model->get('fuel_master','*', $where,$orderby);

    $arr[''] = '--Select--';
    foreach ($data as $key => $value) {
        $arr[$value->fuel_id] = $value->fuel_name;
    }
    return $arr;
}

function getEnergySourcesDropDownFormBuilder(){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');  
    $where = array();
    $orderby = array('es_name'=>'ASC');
    $data = $CI->tbl_generic_model->get('energy_sources','*', $where,$orderby);

    $arr[''] = '--Select--';
    foreach ($data as $key => $value) {
        $arr[$value->es_id] = $value->es_name;
    }
    return $arr;
}

function getRecommendationTagMasterDropDown($tag_id = 0){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');  
    $where = array();
    $sql = "SELECT * FROM tag_master WHERE tag_text IS NOT NULL ORDER BY tag_text";
    $data = $CI->tbl_generic_model->ExecuteQuery($sql);
    $pcd_str = '<option  value=""> -- Select -- </option>';
    foreach ($data as $key => $value) {
        if($tag_id > 0 && $tag_id == $value->tag_id){
            $selected = "selected";
        }else{
            $selected = "";
        }
        $pcd_str .= '<option  value="'.$value->tag_id.'" '.$selected.' >'. $value->tag_text.'</option>';
    }
    return $pcd_str;
}

function getRecommendationTypeMasterDropDown($rtm_id = 0){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');  
    $where = array();
    $sql = "SELECT * FROM recommendation_type_master ORDER BY rtm_name";
    $data = $CI->tbl_generic_model->ExecuteQuery($sql);
    $pcd_str = '<option  value=""> -- Select -- </option>';
    foreach ($data as $key => $value) {
        if($rtm_id > 0 && $rtm_id == $value->rtm_id){
            $selected = "selected";
        }else{
            $selected = "";
        }
        $pcd_str .= '<option  value="'.$value->rtm_id.'" '.$selected.' >'. $value->rtm_name.'</option>';
    }
    return $pcd_str;
}

function getAreaMasterDropDown($area_id = 0){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');  
    $where = array();
    $sql = "SELECT * FROM area_master ORDER BY area_name";
    $data = $CI->tbl_generic_model->ExecuteQuery($sql);
    $pcd_str = '<option  value=""> -- Select -- </option>';
    foreach ($data as $key => $value) {
        if($area_id > 0 && $area_id == $value->area_id){
            $selected = "selected";
        }else{
            $selected = "";
        }
        $pcd_str .= '<option  value="'.$value->area_id.'" '.$selected.' >'. $value->area_name.'</option>';
    }
    return $pcd_str;
}


/*
* function aasort()
* This function is used to sort multidimensional array
*/
function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

function get_saving_potential($sector_id = '',$subsector_id='', $fieldname='' ){

	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');

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

    $chart2query = "SELECT SUM(rm.".$fieldname.") res
        FROM unit_master um,
        unit_sector_relation usr,
        recommendation_master rm 
        ".$ussr_dec."       
        WHERE 
         um.unit_id = usr.unit_id
        AND usr.sector_subsector_type='SEC'
        AND um.unit_id = rm.unit_id    
        ".$sector_sql."
        ".$subsector_sql.";";


    $CI->data['chart2data'] =$chart2data = $CI->tbl_generic_model->ExecuteQuery($chart2query);
	
	return round($chart2data[0]->res/100,2);
}

function get_overall_reduction_total_energy_cost($sector_id = '',$subsector_id='' ){

	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');

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

    $chart2query = "SELECT SUM(rm.proposed_total_energy_savings_mtoe) proposed_total_energy_savings_mtoe, SUM(rm.actual_total_energy_savings_mtoe) actual_total_energy_savings_mtoe
        FROM unit_master um,
        unit_sector_relation usr,
        recommendation_master rm 
        ".$ussr_dec."       
        WHERE 
         um.unit_id = usr.unit_id
        AND usr.sector_subsector_type='SEC'
        AND um.unit_id = rm.unit_id  
        ".$sector_sql."
        ".$subsector_sql.";";


    $CI->data['chart2data'] =$chart2data = $CI->tbl_generic_model->ExecuteQuery($chart2query);

    $baselinequery = "SELECT SUM(ufr.annual_fuel_consumption * ufr.fuel_cost_per_unit_in_igdpr) + SUM(um.annual_electricity_consumption_kwh * um.electricity_cost_per_unit_in_igdpr) res 
        FROM unit_master um,
        unit_sector_relation usr,
        unit_fuel_relation ufr        
        ".$ussr_dec."       
        WHERE 
        um.unit_id = usr.unit_id
        AND usr.sector_subsector_type='SEC'        
		AND ufr.unit_id = um.unit_id 
        ".$sector_sql."
        ".$subsector_sql.";";


    $CI->data['baseline'] =$baseline = $CI->tbl_generic_model->ExecuteQuery($baselinequery);

    $actual_total_energy_savings_mtoe = ($chart2data[0]->actual_total_energy_savings_mtoe);
    $baseline = $baseline[0]->res;

    if($baseline>0){
        return round($actual_total_energy_savings_mtoe/$baseline,2);
    }else{
        return 0;
    }
	
	
}


function get_payback_period_within($sector_id = '',$subsector_id='' ){

	$CI =& get_instance();
    $CI->load->model('tbl_generic_model');

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

    $chart2query = "SELECT SUM(rm.actual_investment_rs_lakh ) actual_investment_rs_lakh , SUM(rm.actual_monetary_savings_rs_lakh) actual_monetary_savings_rs_lakh
        FROM unit_master um,
        unit_sector_relation usr,
        recommendation_master rm         
        ".$ussr_dec."           
        WHERE 
        um.unit_id = usr.unit_id
        AND usr.sector_subsector_type='SEC'
        AND um.unit_id = rm.unit_id   
        ".$sector_sql."
        ".$subsector_sql.";";


    $CI->data['chart2data'] =$chart2data = $CI->tbl_generic_model->ExecuteQuery($chart2query);

    if($chart2data[0]->actual_monetary_savings_rs_lakh>0){
        return round($chart2data[0]->actual_investment_rs_lakh/$chart2data[0]->actual_monetary_savings_rs_lakh,2);
    }else{
        return 0;
    }
	
	
}


function sector_subsector_ajax($sector_id='',$subsector_id='') 
{
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $CI->data['sector_data'] = getSectorDropdown($sector_id);
    $CI->data['subsector_data'] = getSubsectorDropdown($subsector_id,$sector_id);

    $viewdata = $CI->load->view('sector_subsector_ajax',$CI->data);
    return $viewdata;
}

function sector_subsector_ajax_registration_msme($sector_id='',$subsector_id='', $sectorreadonly='', $subsectorreadonly='') 
{
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $CI->data['sector_data'] = getSectorDropdown($sector_id);
    $CI->data['subsector_data'] = getSubsectorDropdown($subsector_id,$sector_id);

    $CI->data['sectorreadonly'] = $sectorreadonly;
    $CI->data['subsectorreadonly'] = $subsectorreadonly;

    $viewdata = $CI->load->view('sector_subsector_ajax_registration_msme',$CI->data);
    return $viewdata;
}

function sector_subsector_ajax_basic_gap($sector_id='',$subsector_id='') 
{
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $CI->data['sector_data'] = getSectorDropdown($sector_id);
    $CI->data['subsector_data'] = getSubsectorDropdown($subsector_id,$sector_id);

    $viewdata = $CI->load->view('sector_subsector_ajax_basic_gap',$CI->data);
    return $viewdata;
}




function getEnergySourcesDropDown($es_id = 0){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');  
    $where = array();
    $sql = "SELECT * FROM energy_sources ORDER BY es_name";
    $data = $CI->tbl_generic_model->ExecuteQuery($sql);
    $pcd_str = '<option  value=""> -- Select -- </option>';
    foreach ($data as $key => $value) {
        if($es_id > 0 && $es_id == $value->es_id){
            $selected = "selected";
        }else{
            $selected = "";
        }
        $pcd_str .= '<option  value="'.$value->es_id.'" '.$selected.' >'. $value->es_name.'</option>';
    }
    return $pcd_str;
}

function no_data_available_pie_chart() 
{
    $CI =& get_instance();
    $viewdata = $CI->load->view('no_data_available_pie_chart',$CI->data);
    return $viewdata;
}

function no_data_available_bar_chart() 
{
    $CI =& get_instance();
    $viewdata = $CI->load->view('no_data_available_bar_chart',$CI->data);
    return $viewdata;
}


function getProductCategoryName($pro = 1){
    $retData = '';
    if($pro == 1){ 
        $retData = "Product Material Type";
    }elseif($pro == 2){
        $retData = "Final Product Name";
    }elseif($pro == 3){
        $retData = "Final Product Type";
    }
    return $retData ;
}


/*
* function state_district_dropdown()
* This function is used to generate state wise district dropdown html
*/

function state_district_dropdown($StCode='',$district_id='', $statereadonly='', $distreadonly='',$is_multiple='N',$is_required='Y',$is_search_location='N') 
{
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');


    $stateQuery = "SELECT * FROM web_state";

    $state_data = $CI->tbl_generic_model->ExecuteQuery($stateQuery);

    $state_str = "";
    $state_str .= "<option  value=''> -- Select -- </option>";
    foreach ($state_data as $key => $value) {
        if($StCode!='NULL' && $StCode==$value->StCode){
            $selected = "selected";
        }else{
            $selected = "";
        }
        $state_str .= "<option  value=".$value->StCode." ".$selected." >". $value->StateName."</option>";
    }

    $CI->data['state_data'] = $state_str;


    $district_str = "";

    if($StCode!=''){
        $tbl ='web_district';
        $fields = '*';
        $where = array('StCode' => $StCode);
        $data = $CI->tbl_generic_model->get($tbl,$fields,$where); 

        $selectedDistrictAjaxQuery = '';
        if($district_id!=''){
            $selectedDistrictAjaxQuery = "WHERE DistCode = ".$district_id."";
        }

        $subsectorQuery = "SELECT * FROM web_district ".$selectedDistrictAjaxQuery;

        $district_data = $CI->tbl_generic_model->ExecuteQuery($subsectorQuery);

        
        $district_str .= "<option  value=''> -- Select -- </option>";
        foreach ($district_data as $key => $value) {
            if($district_id!='NULL' && $district_id==$value->DistCode) {
                $selected = "selected";
            }else{
                $selected = "";
            }
            $district_str .= "<option  value=".$value->DistCode." ".$selected." >". $value->DistrictName."</option>";
        }
    } 


    $CI->data['district_data'] = $district_str;

    $CI->data['statereadonly'] = $statereadonly;
    $CI->data['distreadonly'] = $distreadonly;
    $CI->data['district_str'] = $district_str;

    $CI->data['is_multiple'] = $is_multiple;
    $CI->data['is_required'] = $is_required;
    $CI->data['is_search_location'] = $is_search_location;

    

    $viewdata = $CI->load->view('state_district_ajax',$CI->data);

    
    return $viewdata;
}


/*
* function state_district_dropdown_multiple()
* This function is used to generate state wise district dropdown html with multiple selection
*/

function state_district_dropdown_multiple($StCode='',$district_id='', $statereadonly='', $distreadonly='') 
{
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');


    $stateQuery = "SELECT * FROM web_state";

    $state_data = $CI->tbl_generic_model->ExecuteQuery($stateQuery);

    $state_str = "";
    $state_str .= "<option  value=''> -- Select -- </option>";
    foreach ($state_data as $key => $value) {
        if($StCode!='NULL' && $StCode==$value->StCode){
            $selected = "selected";
        }else{
            $selected = "";
        }
        $state_str .= "<option  value=".$value->StCode." ".$selected." >". $value->StateName."</option>";
    }

    $CI->data['state_data'] = $state_str;


    $district_str = "";

    if($StCode!=''){
        $tbl ='web_district';
        $fields = '*';
        $where = array('StCode' => $StCode);
        $data = $CI->tbl_generic_model->get($tbl,$fields,$where); 

        $selectedDistrictAjaxQuery = '';
        if($district_id!=''){
            $selectedDistrictAjaxQuery = "AND wd.DistCode = ".$district_id."";
        }

        $subsectorQuery = "SELECT wd.*,ws.StateName FROM web_district wd,
                            web_state ws
                             WHERE wd.StCode IN (".$StCode.") 
                            AND ws.StCode = wd.StCode ".$selectedDistrictAjaxQuery;

        $district_data = $CI->tbl_generic_model->ExecuteQuery($subsectorQuery);

        
        $district_str .= "<option  value=''> -- Select -- </option>";
        $stateNamePrevLoop = '';
        foreach ($district_data as $key => $value) {

            if ($stateNamePrevLoop != $value->StateName) {
                if ($stateNamePrevLoop != '') {
                    $district_str .="</optgroup>";
                }
                $district_str .= "<optgroup label='".$value->StateName."'>";
            }


            if($district_id!='NULL' && $district_id==$value->DistCode) {
                $selected = "selected";
            }else{
                $selected = "";
            }
            $district_str .= "<option  value=".$value->DistCode." ".$selected." >". $value->DistrictName."</option>";

            $stateNamePrevLoop = $value->StateName;    
        }

        if ($stateNamePrevLoop != '') {
            $district_str .="</optgroup>";
        }
    } 


    $CI->data['district_data'] = $district_str;

    $CI->data['statereadonly'] = $statereadonly;
    $CI->data['distreadonly'] = $distreadonly;
    $CI->data['district_str'] = $district_str;




    $viewdata = $CI->load->view('state_district_ajax_multiple',$CI->data);
    return $viewdata;
}


/*
* function getDistrictDropdown()
* This function is used to generate district dropdown html
*/
function getDistrictDropdown($selectedDistrict='NULL',$stateCode=''){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $district_str = "";


    if($stateCode!=''){

        $selectedDistrictAjaxQuery = '';
        if($selectedDistrict!=''){
            $selectedDistrictAjaxQuery = "AND DistCode = ".$selectedDistrict."";
        }

        $subsectorQuery = "SELECT wd.*,ws.StateName FROM web_district wd,
                            web_state ws
                             WHERE wd.StCode IN (".$stateCode.") 
                            AND ws.StCode = wd.StCode ".$selectedDistrictAjaxQuery;


        $district_data = $CI->tbl_generic_model->ExecuteQuery($subsectorQuery);
        
        $district_str .= "<option  value=''> -- Select -- </option>";

        $stateNamePrevLoop = '';
        
        foreach ($district_data as $key => $value) {

            if ($stateNamePrevLoop != $value->StateName) {
                if ($stateNamePrevLoop != '') {
                    $district_str .="</optgroup>";
                }
                $district_str .= "<optgroup label='".$value->StateName."'>";
            }

            if($selectedDistrict!='NULL' && $selectedDistrict==$value->DistCode) {
                $selected = "selected";
            }else{
                $selected = "";
            }
            $district_str .= "<option  value=".$value->DistCode." ".$selected." >". $value->DistrictName."</option>";

            $stateNamePrevLoop = $value->StateName;    
        }

        if ($stateNamePrevLoop != '') {
            $district_str .="</optgroup>";
        }
        
    }     
    
    return $district_str; 
}


function getCategoryDropDown($pcm_id = '',$sector_id=''){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');


    if(is_array($sector_id)){
        $sector_id_arr = '';
        foreach ($sector_id as $kp => $vp) {
            $sector_id_arr .= $vp.",";
        }

        $sector_id_arr = substr($sector_id_arr, 0,-1);
        $sector_id = $sector_id_arr;
    }

    if($sector_id!=0){
        $cat_query = "SELECT DISTINCT(cm.category_id),cm.category_name
                        FROM `category_master` cm,
                        `unit_category_relation` ucr,
                        `unit_sector_relation` usr,
                        `sector_master` sm
                        WHERE cm.category_id = ucr.category_id
                        AND ucr.unit_id = usr.unit_id
                        AND usr.sector_subsector_id = sm.sector_id
                        AND sm.parent_sector_id =0
                        AND usr.sector_subsector_id IN (".$sector_id.")
                        ORDER BY category_name ASC;";

        $data = $CI->tbl_generic_model->ExecuteQuery($cat_query);
    }else{
        $where = array();
        $orderby = array('category_name'=>'ASC');
        $data = $CI->tbl_generic_model->get('category_master','*', $where,$orderby);
    }
    
    $pcd_str = '<option  value=""> -- Select -- </option>';
    foreach ($data as $key => $value) {
        if($pcm_id !='' && $pcm_id == $value->category_id){
            $selected = "selected";
        }else{
            $selected = "";
        }
        $pcd_str .= '<option  value="'.$value->category_id.'" '.$selected.' >'. $value->category_name.'</option>';
    }
    return $pcd_str;
}

function getClusterDropDown($pcm_id = ''){
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');
    $orderby = array('cluster_name'=>'ASC');
    $where = array();
    $data = $CI->tbl_generic_model->get('cluster_master','*', $where,$orderby);
    $pcd_str = '<option  value=""> -- Select -- </option>';
    foreach ($data as $key => $value) {
        if($pcm_id !='' && $pcm_id == $value->cluster_id){
            $selected = "selected";
        }else{
            $selected = "";
        }
        $pcd_str .= '<option  value="'.$value->cluster_id.'" '.$selected.' >'. $value->cluster_name.'</option>';
    }
    return $pcd_str;
}


function array_flatten($array) { 
    if (!is_array($array)) { 
        return FALSE; 
    } 
    $result = array(); 
    foreach ($array as $key => $value) { 
        if (is_array($value)) { 
            $result = array_merge($result, array_flatten($value)); 
        } else { 
            $result[$key] = $value; 
        } 
    } 
    return $result; 
} 

/*
* function getMajorProductCategoryDropDownSectorWise()
* This function is used to to get Major product category dropdown w.r.t. sector & Subsector 
*/
function getMajorProductCategoryDropDownSectorSubsectorWise($ids = '', $pType = 1){
    $pcd_str = '<option  value=""> -- Select -- </option>';
    if(count($ids) > 0){
        $CI =& get_instance();
        $CI->load->model('gap/basic_model');
        $data = $CI->basic_model->getProductCategoryListSectorSubsectorWise($ids, $pType);
        foreach ($data as $key => $value) {
            $selected = "";
            $pcd_str .= '<option  value="'.$value->pcm_id.'" '.$selected.' >'. $value->pcm_name.'</option>';
        }
    }
    return $pcd_str;
}



/*
* function getMajorProcessEquipmentDropDownSectorWise()
* This function is used to to get Major product category dropdown w.r.t. sector & Subsector
*/
function getProcessEquipmentListSectorSubsectorWise($ids = '', $type = 'sector'){
    $pcd_str = '<option  value=""> -- Select -- </option>';
    if(count($ids) > 0){
        $CI =& get_instance();
        $CI->load->model('gap/basic_model');
        $data = $CI->basic_model->getProcessEquipmentListSectorSubsectorWise($ids, $type);
        foreach ($data as $key => $value) {
            $selected = "";
            $pcd_str .= '<option  value="'.$value->aum_id.'" '.$selected.' >'. $value->aum_name.'</option>';
        }
    }
    return $pcd_str;
}


function sector_subsector_ajax_advance_gap($sector_id='',$subsector_id='') 
{
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $CI->data['sector_data'] = getSectorDropdown($sector_id);
    $CI->data['subsector_data'] = getSubsectorDropdown($subsector_id,$sector_id);

    $viewdata = $CI->load->view('sector_subsector_ajax_advance_gap',$CI->data);
    return $viewdata;
}

function get_major_process_equipment_subsectorwise($subSectorIds=''){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $query = "SELECT DISTINCT(aum.aum_name),aum.aum_id
                    FROM `application_utility_master` aum,
                    `recommendation_master` rm,
                    `unit_sector_relation` usr,
                    `sector_master` sm
                    WHERE aum.aum_id = rm.aum_id
                    AND rm.unit_id = usr.unit_id
                    AND usr.sector_subsector_id = sm.sector_id
                    AND sm.parent_sector_id !=0
                    AND usr.sector_subsector_id IN (".$subSectorIds.")
                    ORDER BY aum_name ASC;";

    $eq_data = $CI->tbl_generic_model->ExecuteQuery($query);

    $eq_str = "";
    $eq_str .= "<option  value=''> -- Select -- </option>";
    foreach ($eq_data as $key => $value) {
        /*if($sector_id!='NULL' && $sector_id==$value->sector_id){
            $selected = "selected";
        }else{
            $selected = "";
        }*/
        $selected = "";
        $eq_str .= "<option  value=".$value->aum_id." ".$selected." >". $value->aum_name."</option>";
    }
    
    return $eq_str; 
}

function get_major_process_equipment_subsectorwise_form_builder($subSectorIds=''){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $query = "SELECT DISTINCT(aum.aum_name),aum.aum_id
                    FROM `application_utility_master` aum,
                    `recommendation_master` rm,
                    `unit_sector_relation` usr,
                    `sector_master` sm
                    WHERE aum.aum_id = rm.aum_id
                    AND rm.unit_id = usr.unit_id
                    AND usr.sector_subsector_id = sm.sector_id
                    AND sm.parent_sector_id !=0
                    AND usr.sector_subsector_id IN (".$subSectorIds.")
                    ORDER BY aum_name ASC;";

    $data = $CI->tbl_generic_model->ExecuteQuery($query);

    $arr[''] = '--Select--';
    foreach ($data as $key => $value) {
        $arr[$value->aum_id] = $value->aum_name;
    }
    
    return $arr;    
}


function get_product_subsectorwise($subSectorIds='', $pcm_type=''){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    if($subSectorIds!=''){
        $subsec_query = "AND usr.sector_subsector_id IN (".$subSectorIds.")";
    }

    if($pcm_type!=''){              
        $pcm_query = "AND pcm_type = ".$pcm_type."";
    }

    $query = "SELECT DISTINCT(pcm.pcm_id),pcm.pcm_name
                FROM `product_category_master` pcm,
                `unit_product_category_relation` upcr,
                `unit_sector_relation` usr,
                `sector_master` sm
                WHERE pcm.pcm_id = upcr.pcm_id
                AND upcr.unit_id = usr.unit_id
                AND usr.sector_subsector_id = sm.sector_id
                AND sm.parent_sector_id !=0
                ".$subsec_query."
                ".$pcm_query."
                ORDER BY pcm_name ASC;
                ";

    $eq_data = $CI->tbl_generic_model->ExecuteQuery($query);

    $eq_str = "";
    $eq_str .= "<option  value=''> -- Select -- </option>";
    foreach ($eq_data as $key => $value) {
        /*if($sector_id!='NULL' && $sector_id==$value->sector_id){
            $selected = "selected";
        }else{
            $selected = "";
        }*/
        $selected = "";
        $eq_str .= "<option  value=".$value->pcm_id." ".$selected." >". $value->pcm_name."</option>";
    }
    
    return $eq_str; 
}


function get_product_subsectorwise_form_builder($subSectorIds='', $pcm_type=''){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    if($subSectorIds!=''){
        $subsec_query = "AND usr.sector_subsector_id IN (".$subSectorIds.")";
    }

    if($pcm_type!=''){              
        $pcm_query = "AND pcm_type = ".$pcm_type."";
    }

    $query = "SELECT DISTINCT(pcm.pcm_id),pcm.pcm_name
                FROM `product_category_master` pcm,
                `unit_product_category_relation` upcr,
                `unit_sector_relation` usr,
                `sector_master` sm
                WHERE pcm.pcm_id = upcr.pcm_id
                AND upcr.unit_id = usr.unit_id
                AND usr.sector_subsector_id = sm.sector_id
                AND sm.parent_sector_id !=0
                ".$subsec_query."
                ".$pcm_query."
                ORDER BY pcm_name ASC;
                ";

    $data = $CI->tbl_generic_model->ExecuteQuery($query);

    $arr[''] = '--Select--';
    foreach ($data as $key => $value) {
        $arr[$value->pcm_id] = $value->pcm_name;
    }
    
    return $arr;    
}


function sector_subsector_ajax_ecm_tool($sector_id='',$subsector_id='') 
{
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $CI->data['sector_data'] = getSectorDropdown($sector_id);
    $CI->data['subsector_data'] = getSubsectorDropdown($subsector_id,$sector_id);

    $viewdata = $CI->load->view('sector_subsector_ajax_ecm_tool',$CI->data);
    return $viewdata;
}


function get_max_min($field,$tbl_name){

    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $query = "SELECT max(cast(".$field." as unsigned) ) max, min(cast(".$field." as unsigned)) min FROM ".$tbl_name.";
                ";

    $data = $CI->tbl_generic_model->ExecuteQuery($query);

    $arr['max']=$data[0]->max;
    $arr['min']=$data[0]->min;
    
    return $arr;    
}

function state_district_dropdown_multiple_operation_area($stateCode = array(),$district_id = array(), $statereadonly='', $distreadonly='') 
{
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $stateQuery = "SELECT * FROM web_state";

    $state_data = $CI->tbl_generic_model->ExecuteQuery($stateQuery);

    $state_str = "";
    $state_str .= "<option  value=''> -- Select -- </option>";
    foreach ($state_data as $key => $value) {
        $selected = "";
        if(count($stateCode) > 0){
            if(!empty($stateCode) && in_array($value->StCode, $stateCode)){
                $selected = "selected";
            }
        }
        $state_str .= "<option  value=".$value->StCode." ".$selected." >". $value->StateName."</option>";
    }
    $CI->data['state_data'] = $state_str;
    
    $viewdata = $CI->load->view('state_district_ajax_multiple_operational_area',$CI->data);
    return $viewdata;  
}

function district_dropdown_multiple_operation_area($stateCode = array(),$district_id = array(), $statereadonly='', $distreadonly='') 
{
    $CI =& get_instance();
    $CI->load->model('tbl_generic_model');

    $district_str = "";

    if($stateCode!=''){
        $state = implode(',',$stateCode);
               
        $sqlDist = "SELECT wd.*,ws.StateName FROM web_district wd,
                            web_state ws
                             WHERE wd.StCode IN (".$state.") 
                            AND ws.StCode = wd.StCode ";
        
        $district = $CI->tbl_generic_model->ExecuteQuery($sqlDist);
        $districts = array();
        $district_str .= "<option  value=''> -- Select -- </option>";
        if(count($district) > 0){
            foreach ($district as $key => $value) {
                $districts[$value->StCode]['StateName'] = $value->StateName;
                $districts[$value->StCode]['dist'][$value->DistCode] = $value->DistrictName;
            }

            foreach ($districts as $key1 => $value1) {
                $district_str .= "<optgroup label='".$value1['StateName']."'>";
                foreach ($value1['dist'] as $key2 => $value2) {
                    $selected = "";
                    if(count($district_id) > 0){
                        if(!empty($district_id) && in_array($key2, $district_id)) {
                            $selected = "selected";
                        }
                    }
                    $district_str .= "<option  value=".$key2." ".$selected." >". $value2."</option>";
                }
                $district_str .="</optgroup>";
            }
        }
    } 
    $CI->data['district_data'] = $district_str;
    return $district_str;
}

?>