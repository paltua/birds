
<?php 
if($this->session->userdata('user_id') > 0){
	$module = $this->uri->segment(1);
	$active = "";
	$moduleArr = array(
			'ems' => array('module' => 'ems', 'name' => 'Electricity', 'selected' => ''),
			'fm' => array('module' => 'fm', 'name' => 'Steam', 'selected' => ''),
			'air' => array('module' => 'air', 'name' => 'Compressed Air', 'selected' => '')
		);
	if($module != ''){
		$moduleArr[$module]['selected'] = 'active';
	}

?>
 	<!-- <li ><a href="<?php //echo base_url();?>account/dashboard">Home</a></li> -->
 	<li ><a href="#">Home</a></li><!-- class="active" -->
 	<?php foreach ($moduleArr as $key => $value) {?>
 		<li class="<?php echo $value['selected'];?>"><a href="<?php echo base_url();?><?php echo $value['module'];?>/dashboard"><?php echo $value['name'];?></a></li>
 	<?php } ?>


    <!-- <li><a href="<?php echo base_url();?>ems/dashboard">Electricity</a></li> -->
    <!-- <li><a href="#">EMS <span style="font-size: 9px;">(Coming Soon)</span></a></li> -->
    <!-- <li class="active"><a href="<?php echo base_url();?>fm/dashboard">Steam</a></li> -->
    <!-- <li><a href="#">AIR <span style="font-size: 9px;">(Coming Soon)</span></a></li> -->
    <!-- <li><a href="<?php echo base_url();?>air/dashboard">Compressed Air</a></li> -->
    <li style="float: right;"><a href="<?php echo base_url();?>account/auth/logout">Logout</a></li>
    <li style="float: right;"><a href="#"><?php echo $this->session->userdata('full_name');?></a></li>
    
 <?php }?>