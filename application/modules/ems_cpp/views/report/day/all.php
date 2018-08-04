<?php //echo "<pre>"; print_r($typeWise);

$headerArr = array('Amps','HZ','KW','PF','Volt');
$g_total = array();
$d_total = array();
$g_sum = array();
$d_sum = array();
$linkArr = array('Amps','HZ','KW','PF','Volt');
?>
<!-- <div>
    Data sets being shown for <?php echo date('d-m-Y H:i:s',strtotime($startDateShow));?> to <?php echo date('d-m-Y H:i:s',strtotime($endDateShow));?>
</div> -->
<br/>
<?php if(count($typeWise) > 0){?>
<div class="panel panel-default">
    <div class="panel-heading">Aggregated Stats :- DG House Bus-<?php echo ($typeId == 1)?'A':(($typeId == 2)?'B':'C');?> Receiving </div>
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
		    		if(count($typeWise['G'.$typeId]) > 0){
		    			foreach ($typeWise['G'.$typeId] as $key => $value){
		    	?>

		    	<tr role="row" class="odd">
		    		<th style="">
			          <p><?php echo $value['name'];?></p>
			      	</th>
		      		<?php foreach($headerArr as $keyH => $valH){ 
		      			$g_sum[$valH][] = $value['data'][$valH][0];
		      		?>
		          	<td>
		          	<?php if(in_array($valH, $linkArr)){ ?>
		          		<p>
			          		<a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/report/showGraphDay/<?php echo $key;?>/<?php echo $valH;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>" class="showChart">
			          			<?php echo number_format((float)round($value['data'][$valH][0],2), 2, '.', '');?>
			          		</a>
		          		</p>
		          	<?php }else{?>
		          		<p><?php echo number_format((float)round($value['data'][$valH][0],2), 2, '.', '');?></p>
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
					          <p><a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/report/showGraphDayTotal/<?php echo $val;?>/G/<?php echo($typeId);?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>" class="showChart">
					          	
					          	<?php echo number_format((float)round($showGval,2), 2, '.', '');?>
					          	</a></p>
					        </th>
			      		<?php } ?>
			    	</tr>
		  	</tbody>
		</table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Aggregated Stats :- DG House Bus-<?php echo ($typeId == 1)?'A':(($typeId == 2)?'B':'C');?> Distribution </div>
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
		    		if(count($typeWise['D'.$typeId]) > 0){
		    			foreach ($typeWise['D'.$typeId] as $key => $value){
		    	?>

		    	<tr role="row" class="odd">
		    		<th style="">
			          <p><?php echo $value['name'];?></p>
			      	</th>
		      		<?php foreach($headerArr as $keyH => $valH){ 
		      			$d_sum[$valH][] = $value['data'][$valH][0];
		      		?>
		          	<td>
		          		<!-- <p><?php echo round($value['data'][$valH][0],2);?></p> -->
		          		<?php if(in_array($valH, $linkArr)){ ?>
			          		<p>
				          		<a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/report/showGraphDay/<?php echo $key;?>/<?php echo $valH;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>" class="showChart">
				          			<?php echo number_format((float)round($value['data'][$valH][0],2), 2, '.', '');?>
				          		</a>
			          		</p>
			          	<?php }else{?>
			          		<p>
			          			<?php echo number_format((float)round($value['data'][$valH][0],2), 2, '.', '');?>
			          		</p>
			          	<?php	} ?>
		          	</td>
		        	<?php } ?>
		    	</tr>
		    		<?php }?>
		    	<?php }?>
		    	<?php /*$newMeter = array('NEW ETP 3','G+2','COMPRESSOR-103');*/ 	?>
		    	<?php if($typeId == 3){?>
<!-- 		    	<tr role="row">
		    		<th style="">
			          	<p>NEW ETP 3, G+2, COMPRESSOR-103</p>
			      	</th>
			      	<th style="text-align: center;" colspan="5">
			      	Meters Not Connected 
			      	</th>
			    </tr> -->
			    <?php }?>

		    	<tr role="row">
			      	<th style="">
			          	<p>Total</p>
			      	</th>
			      		<?php foreach($headerArr as $key => $val){ 
			      			$showGval = 0; 
			      			if($val == 'KW'){
			      				$g_kw = $showGval = array_sum($d_sum[$val]) ; 
		          				$d_total[$val] = $g_kw;
			      			}elseif($val == 'PF'){
			      				$d_total[$val] = $showGval = 0;
			          			if(count($d_sum[$val]) > 0){
			          				$kwPf = 0;
			          				$kw = 0;
		          					foreach ($d_sum[$val] as $key1 => $value1) {
		          						if($value1 > 0){
		          							$kwPf = $kwPf + ($d_sum['KW'][$key1]/$value1);
		          							$kw = $kw + $d_sum['KW'][$key1] ;
		          						}
		          					}
		          					if($kwPf > 0){
		          						$showGval = $kw/$kwPf;
		          					}
			          			} 
			          			$d_total[$val] = $showGval;
			      			}elseif ($val == 'Amps') {
			      				$g_amps = array_sum($d_sum[$val]); 
		          				$d_total[$val] = $showGval =$g_amps;
			      			}elseif ($val == 'Volt') {
			      				$g_vol = array_sum($d_sum[$val])/count($d_sum[$val]) ; 
		          				$d_total[$val] = $showGval = $g_vol;
			      			}elseif($val == 'HZ'){
			      				$g_hz = array_sum($d_sum[$val])/count($d_sum[$val]); 
		          				$d_total[$val] = $showGval = $g_hz;
			      			}
			      		?>
			      			<th style="">
					          <p><a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/report/showGraphDayTotal/<?php echo $val;?>/D/<?php echo($typeId);?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>" class="showChart">
					          	<?php echo number_format((float)round($showGval,2), 2, '.', '');?>
					          </a></p>
					        </th>
			      		<?php } ?>
			    	</tr>
		  	</tbody>
		</table>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Loss </div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
		      		<th style="">
			          	<p></p>
			      	</th>
			      	<th style="">
			          	<p>Absolute value</p>
			      	</th>
			      	<th style="">
			          	<p>%</p>
			      	</th>
		      	</tr>
		    </thead>
		    <tbody>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>KW</p>
			      	</th>
			      	<th style="">
			          <p>
			          	<a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/report/showGraphDayTotalLoss/<?php echo($typeId);?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>/abs" class="showChart">
			          <?php $showKw = $showKw1 = ($g_total['KW'] - $d_total['KW']);?>
			          
			          	<?php echo number_format((float)round($showKw,2), 2, '.', '');?>
			          </a>
			          </p>
			      	</th>
			      	<th style="">
			          <p>
			          	<a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/report/showGraphDayTotalLoss/<?php echo($typeId);?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>/per" class="showChart">
			          <?php $showKw12 = ($showKw1/$g_total['KW'])*100;?>
			          
			          	<?php echo number_format((float)round($showKw12,2), 2, '.', '');?>
			          </a>
			          </p>
			      	</th>
			      	
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

    
