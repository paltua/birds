 
<?php 
if($this->session->userdata('user_id') > 0){
	$module = $this->uri->segment(1);
	$active = "";
    $moduleArr = array();
	$moduleArrAll = array(
			'account' => array('module' => 'account', 'name' => 'Dashboard', 'selected' => '', 'url' => 'dashboard'),
			'ems' => array('module' => 'ems', 'name' => 'Electricity', 'selected' => '', 'url' => 'dashboard'),
            'ems_cpp' => array('module' => 'ems_cpp', 'name' => 'CPP Dashboard', 'selected' => '', 'url' => 'dashboard'),
			'fm' => array('module' => 'fm', 'name' => 'Steam', 'selected' => '', 'url' => 'dashboard'),
			'air' => array('module' => 'air', 'name' => 'Compressed Air', 'selected' => '', 'url' => 'dashboard'),
			'reportdownload' => array('module' => 'reportdownload', 'name' => 'Reports', 'selected' => '', 'url' => 'reportair'),
            'weaving' => array('module' => 'weaving', 'name' => 'Weaving', 'selected' => '', 'url' => 'dashboard'),
			/*'diy1' => array('module' => 'diy1', 'name' => '', 'selected' => '', 'url' => 'reportair'),
			'anomaly' => array('module' => 'anomaly', 'name' => '', 'selected' => '', 'url' => 'reportair'),*/
			
		);
    $moduleArrWeaving = array(
            'weaving' => array('module' => 'weaving', 'name' => 'Weaving', 'selected' => '', 'url' => 'dashboard'),
        );
    if($this->session->userdata('user_id') == 9){
        $moduleArr = $moduleArrWeaving;
    }else{
        $moduleArr = $moduleArrAll;
    }
	if($module != ''){
		$moduleArr[$module]['selected'] = 'active';
	}
    $unsetModule = array('data');
    foreach ($unsetModule as $keyM => $valueM) {
        unset($moduleArr[$valueM]);
    }
    
?>

<?php foreach ($moduleArr as $key => $value) {?>
	<li class="<?php echo $value['selected'];?>"><a href="<?php echo base_url();?><?php echo $value['module'];?>/<?php echo $value['url'];?>"><?php echo $value['name'];?></a></li>
<?php } ?>
<li style="float: right;"><a href="<?php echo base_url();?>account/auth/logout">Logout</a></li>
<li style="float: right;"><a href="#"><?php echo $this->session->userdata('full_name');?></a></li>
<?php if($this->session->userdata('user_id') == 2){?>
<?php $noti = getHeaderNotification(); //pr($noti);?>

<link rel="stylesheet" href="<?php echo base_url();?>resource/css/style.css">
<link rel="stylesheet" href="<?php echo base_url();?>resource/font-awesome/css/font-awesome.min.css">

<ul class="nav navbar-top-links navbar-right">
    <li class="dropdown" style="float: right;">
        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
            <i class="fa fa-bell"></i>  <span class="label label-primary"><?php echo $noti['total'];?></span>
        </a>
        <ul class="dropdown-menu dropdown-alerts">
            <li>Error occured on <?php echo date("F j, Y", (strtotime('-1 day', strtotime(date('Y-m-d')))));?></li>
            <?php if(count($noti['li']) > 0){
                foreach ($noti['li'] as $key => $value) { ?>
                    <li>
                        <div class="notiBox">
                            <div class="headR"><?php echo $value['name'];?></div>
                            <div class="cntWrap">
                                Total Meter Error : <span class="lbl_hd"><?php echo $value['data'][0]->total_meter;?></span>
                                <span class="pull-right text-muted small"><a href="<?php echo base_url();?>account/notification/meterError/<?php echo $key;?>">View More <i class="fa fa-angle-right" aria-hidden="true"></i></a></span>
                            </div>
                        </div>
                        
                    </li>
                    
            <?php     
                }
             }?>
        </ul>
    </li>
</ul>
<ul class="nav navbar-top-links navbar-right">
    <li class="dropdown" style="float: right;">
        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
            <i class="fa fa-download"></i> Data Download
        </a>
        <ul class="dropdown-menu dropdown-alerts">
            <li></li>
            <li>
                <div class="notiBox">
                    <!-- <div class="headR"><i class="fa fa-download"></i><a target="_blank" href="<?php echo base_url();?>data/page/index"> Page View By WIL </a></div> -->
                    <div class="headR"><i class="fa fa-download"></i><a target="_blank" href="<?php echo base_url();?>data/download/ems"> EMS Data Download </a></div>
                    <div class="headR"><i class="fa fa-download"></i><a target="_blank" href="<?php echo base_url();?>data/download/emsCpp"> EMS CPP Data Download </a></div>
                    <div class="headR"><i class="fa fa-download"></i><a target="_blank" href="<?php echo base_url();?>data/download/air"> Air Data Download </a></div>
                    <div class="headR"><i class="fa fa-download"></i><a target="_blank" href="<?php echo base_url();?>data/download/steam"> Steam Data Download </a></div>
                    <div class="headR"><i class="fa fa-download"></i><a target="_blank" href="<?php echo base_url();?>data/download/weaving"> Weaving Data Download </a></div>
                </div>
            </li>
        </ul>
    </li>
</ul>
<?php } ?>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function(){
        $(".modal").on("hidden.bs.modal", function(){
            $(".chartDivContent").html("");
        });
    });
</script>