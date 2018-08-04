<?php //echo "<pre>"; print_r($typeWise);

$headerArr = array('Amps','HZ','KW','PF','Volt');
$g_total = array();
$d_total = array();
$g_sum = array();
$d_sum = array();
$linkArr = array('Amps','HZ','KW','PF','Volt');
?>

<br/>
<?php if(count($meterWiseData) > 0){?>
<div class="panel panel-default">
    <div class="panel-heading">Cpp Meters </div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		      	<th style="">
		          	<p>Meter Name</p>
		      	</th>
		      		<?php foreach($headerArr as $key => $val){ ?>
		      			<th style="">
				          <p><?php echo $val;?></p>
				        </th>
		      		<?php } ?>
		    	</tr>
		    </thead>  
		    <tbody> 
		    	<?php 
		    		if(count($meterWiseData) > 0){
		    			foreach ($meterWiseData as $key => $value){
		    	?>

		    	<tr role="row" class="odd">
		    		<th style="">
			          <p><?php echo $value->device_name;?></p>
			      	</th>
		      		<?php foreach($headerArr as $keyH => $valH){ 
		      			$g_sum[$valH][] = $value->{$valH};
		      		?>
		          	<td>
		          	<?php if(in_array($valH, $linkArr)){ ?>
		          		<p>
			          		<a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/report/showGraphDayCpp/<?php echo $value->device_id;?>/<?php echo $valH;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>" class="showChart">
			          			<?php echo number_format((float)round($value->{$valH},2), 2, '.', '');?>
			          		</a>
		          		</p>
		          	<?php }else{?>
		          		<p><?php echo number_format((float)round($value->{$valH},2), 2, '.', '');?></p>
		          	<?php	} ?>
		          	</td>
		        	<?php } ?>
		    	</tr>
		    		<?php }?>

		    	<?php }?>
		    	<tr role="row">
			      	<th style="">
			          	<p>Total</p>
			      	</th>
			      		<?php foreach($headerArr as $key => $val){ 
			      			$showGval = 0; 
			      			if($val == 'KW'){
			      				$g_kw = $showGval = array_sum($g_sum[$val]) ; 
		          				$g_total[$val] = $g_kw;
			      			}elseif($val == 'PF'){
			      				$g_total[$val] = $showGval = 0;
			          			if(count($g_sum[$val]) > 0){
			          				$kwPf = 0;
			          				$kw = 0;
		          					foreach ($g_sum[$val] as $key1 => $value1) {
		          						if($value1 > 0){
		          							$kwPf = $kwPf + ($g_sum['KW'][$key1]/$value1);
		          							$kw = $kw + $g_sum['KW'][$key1] ;
		          						}
		          					}
		          					if($kwPf > 0){
		          						$showGval = $kw/$kwPf;
		          					}
			          			} 
			          			$g_total[$val] = $showGval;
			      			}elseif ($val == 'Amps') {
			      				$g_amps = array_sum($g_sum[$val]); 
		          				$g_total[$val] = $showGval =$g_amps;
			      			}elseif ($val == 'Volt') {
			      				$g_vol = array_sum($g_sum[$val])/count($g_sum[$val]) ; 
		          				$g_total[$val] = $showGval = $g_vol;
			      			}elseif($val == 'HZ'){
			      				$g_hz = array_sum($g_sum[$val])/count($g_sum[$val]); 
		          				$g_total[$val] = $showGval = $g_hz;
			      			}
			      		?>
			      			<th style="">
					          <p><a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/report/showGraphDayCppTotal/<?php echo $val;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>" class="showChart"><?php echo number_format((float)round($showGval,2), 2, '.', '');?></a></p>
					        </th>
			      		<?php } ?>
			    	</tr>
		  	</tbody>
		</table>
    </div>
</div>

<?php //echo "<strong>Alert : Please note that some of the meter data have shown inconsistency and hence all indicators are not being displayed. Kindly speak to engineering/relevant department.</strong>";
	
?>
<?php }else{?>
<div class="alert-danger">No Data Set Please.</div>
<?php }?>

    
