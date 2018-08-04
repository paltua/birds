<?php 
$headerArr = array('Amps','HZ','KW','PF','Volt');

$g_sum = array();
$d_sum = array();
$g_total = array();
$d_total = array();
$cpp_sum = array();

?>
<?php if(count($meterWiseDataCpp) > 0){?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#allDataId').on('click','.ppChartShow', function() { 
            var meter_link = $(this).attr("meter-link");
            $('.myMeterModal').modal('show');
            $('.myMeterModal').find(".modal-content").load(meter_link);
        });
    });
</script>  
<div class="panel panel-default">
	
    <div class="panel-heading">Aggregated Stats :- All Generation for WIL from Power Plant </div>
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
		    	<?php if(isset($meterWiseDataCpp)){?>
		      		<?php foreach($meterWiseDataCpp as $key => $val){ ?>
				    	<tr role="row" class="odd">
				    		<th style="">
					          <p><?php echo $val->device_name;?></p>
					      </th>
				    		<?php foreach($headerArr as $keyX => $valX){ 
				    			if($valX == 'HZ'){
				    		?>

				    		<td>
				          		<?php 
							       $cpp_sum[$valX][] = 0;
							      ?>
				          		<p><?php echo number_format((float)round(0,2), 2, '.', '');?></p>
				          	</td>

				    		<?php }else{?>
				          	<td>
				          		<?php 
				          		if($valX == 'KW' || $valX == 'Volt'){
							       		$cpp_sum[$valX][] = abs($val->{$valX}) * 1000;
							      ?>
				          			<p><a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGraphDayCpp/<?php echo $val->device_id;?>/<?php echo $valX;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
				          				<?php //echo round(abs($val->{$valX}), 2) * 1000;?>
				          					<?php echo number_format((float)round(abs($val->{$valX}) * 1000,2), 2, '.', '');?>
				          				</a></p>
				          			<?php 
				          		}else{
				          			
				          				$cpp_sum[$valX][] = abs($val->{$valX});
				          			
				          			?>
				          				<p><a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGraphDayCpp/<?php echo $val->device_id;?>/<?php echo $valX;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
				          					<?php //echo round($val->{$valX}, 2);?>
				          					<?php echo number_format((float)round($val->{$valX},2), 2, '.', '');?>
				          				</a></p>
				          		<?php } ?>
				          	</td>
				          	<?php } ?>
				          	<?php } ?>
				    	</tr>
			    	<?php } ?>
		      	<?php } ?> 

		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Total</p>
			      </th>
		    	<?php foreach($headerArr as $keyX => $valX){ $cppData = 0;?>
		          	<td>
		          		<?php 
		          			if($valX == 'KW'){ 
			          			$cppData = array_sum($cpp_sum[$valX]) ; 
			          		}elseif ($valX == 'PF') {
		          				$showPfVal = 0;
			          			if(count($cpp_sum[$valX]) > 0){
			          				$kwPf = 0;
			          				$kw = 0;
		          					foreach ($cpp_sum[$valX] as $key1 => $value1) {
		          						if($value1 > 0){
		          							$kwPf = $kwPf + ($cpp_sum['KW'][$key1]/$value1);
		          							$kw = $kw + $cpp_sum['KW'][$key1] ;
		          						}
		          					}
		          					if($kwPf > 0){
		          						$showPfVal = $kw/$kwPf;
		          					}
			          			} 
			          			$cppData = $showPfVal;
		          			}elseif($valX == 'Volt'){ 
		          				if($cpp_sum[$valX] > 0){
		          					$cppData = array_sum($cpp_sum[$valX])/count($cpp_sum[$valX]) ; 
		          				}else{
		          					$cppData = 0;
		          				}	
		          			}elseif ($valX == 'HZ') {
		          				if($cpp_sum[$valX] > 0){
		          					$cppData = array_sum($cpp_sum[$valX])/count($cpp_sum[$valX]); 
		          				}else{
		          					$cppData = 0; 
		          				}		          				
		          			}elseif($valX == 'Amps'){
			          			$cppData = array_sum($cpp_sum[$valX]);
		          		 	} 
		          		 ?>
		          		 <p>
		          		 	<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGraphDayCppTotal/<?php echo $valX;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
		          		 	<?php //echo round($cppData ,2);?>
		          		 	<?php echo number_format((float)round($cppData,2), 2, '.', '');?>
		          		 </a></p>
		          	</td>
		        	<?php } ?>
		      	
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>
<?php }?>

<div class="panel panel-default">
    <div class="panel-heading">Aggregated Stats :- All Receiving and generation of WIL</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		      <th style="">
		          <p>Type</p>
		      </th>
	      		<?php foreach($headerArr as $key => $val){ ?>
	      			<th style="">
			          <p><?php echo $val;?></p>
			        </th>
	      		<?php } ?>
		      
		    </tr>
		    </thead> 
		    <tbody> 
		    	<?php if(count($genDistAllData) > 0){ for($g = 0;$g <= 2; $g++){?>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>DG House Bus-<?php echo ($g == 0)?'A':(($g == 1)?'B':'C');?></p>
			      	</th>
			      	<?php foreach($headerArr as $key => $val){ ?>
			      	<td>
			      		<?php 
			      		$showGdata = 0;
			      		if($val == 'PF'){
			      			if($genDistAllData[$g]->KWPF > 0){
			      				$showGdata = $genDistAllData[$g]->sum_KW/$genDistAllData[$g]->KWPF;
			      			}else{
			      				$showGdata = 0;
			      			}
			      		}else{
			      			$showGdata = $genDistAllData[$g]->{$val};
			      		}
			      		$g_sum[$val][] = $showGdata;
			      		?>
			      		
			      		<p>
			      			<?php //echo round($showGdata,2);?>
			      			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGraphDayTotal/<?php echo $val;?>/<?php echo $genDistAllData[$g]->type_text;?>/<?php echo $genDistAllData[$g]->type_level;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
			      				<?php echo number_format((float)round($showGdata,2), 2, '.', '');?>
			      			</a>
			      			</p>
			      		
			      	</td>
			      	<?php } ?>
			    </tr>
			    <?php } } ?>
			    <tr role="row" class="odd">
		    		<th style="">
			          <p>Total</p>
			      	</th>
			      	<?php foreach($headerArr as $keyG => $valG){ $key = $valG; $showValG = 0;?>
			      	<td>
			      		<?php if($key == 'KW'){ 
		          			$g_kw = array_sum($g_sum[$key]) ; 
		          			$g_total[$key] = $showValG = $g_kw;
		          		?>
		          			<!-- <p><?php echo round($g_kw,2);?></p> -->
		          		<?php }elseif ($key == 'PF') {?>
		          			<?php 
		          			$g_total[$key] = $showPfVal = 0;
		          			if(count($g_sum[$key]) > 0){
		          				$kwPf = 0;
		          				$kw = 0;
	          					foreach ($g_sum[$key] as $key1 => $value1) {
	          						if($value1 > 0){
	          							$kwPf = $kwPf + ($g_sum['KW'][$key1]/$value1);
	          							$kw = $kw + $g_sum['KW'][$key1] ;
	          						}
	          					}
	          					if($kwPf > 0){
	          						$showPfVal = $kw/$kwPf;
	          					}
		          			} 
		          			$g_total[$key] = $showValG = $showPfVal;
		          			?>
		          			<!-- <p><?php echo round($showPfVal,2);?></p> -->
		          		<?php }elseif($key == 'Volt'){ 
		          			$g_vol = array_sum($g_sum[$key])/count($g_sum[$key]) ; 
		          			$g_total[$key] = $showValG = $g_vol;
		          			?>
		          			<!-- <p><?php echo round($g_vol,2);?></p> -->
		          		<?php	}elseif ($key == 'HZ') {
		          			$g_hz = array_sum($g_sum[$key])/count($g_sum[$key]); 
		          			$g_total[$key] = $showValG = $g_hz;
		          			?>
		          			<!-- <p><?php echo round($g_hz,2);?></p> -->
		          		<?php }elseif($key == 'Amps'){
		          			$g_amps = array_sum($g_sum[$key]); 
		          			$g_total[$key] = $showValG = $g_amps;
		          			?>
		          		
		          		<?php } ?>
		          		<p><?php //echo round($g_amps ,2);?>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGenDistAllTotal/G/<?php echo $valG;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
		          			<?php echo number_format((float)round($showValG,2), 2, '.', '');?>
		          		</a>
		          		</p>
			      	</td>
			      	<?php }?>
			    </tr>
			</tbody>    
		</table>
    </div>
</div>

<?php if(count($meterWiseDataCpp) > 0){?>
<div class="panel panel-default">
    <div class="panel-heading">Meterwise Transmission Loss CPP to WIL</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
			      <th style="">
			          <p>CPP Meter</p>
			      </th>
			      <th style="">
			          <p>KW</p>
			      </th>
			      <th style="">
			          <p>WIL Meter</p>
			      </th>
			      <th style="">
			          <p>KW</p>
			      </th>
			      <th style="">
			          <p>Loss(KW)</p>
			      </th>
			      <th style="">
			          <p>Loss(%)</p>
			      </th>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php 
		    	$cpp_sum_new = array();
		    	$wil_sum_new = array();
		    	if(count($emsCppDataLoss) > 0){ 

		    		foreach($emsCppDataLoss as $ecdlKey => $ecdlVal){ ?>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p><?php echo $ecdlVal->device_name;?></p>
				    </th>
				    <td style="">
				          <p><a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGraphDayCpp/<?php echo $ecdlVal->device_id;?>/KW/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
				          	<?php $cpp_sum_new[$ecdlKey] = abs($ecdlVal->data) * 1000; ?>
				          	<?php echo number_format((float)round($cpp_sum_new[$ecdlKey],2), 2, '.', '');?>	
				          	</a></p>
				    </td>
				    <th style="">
				          <p><?php echo $emsDataLoss[$ecdlKey]->device_name;?></p>
				    </th>
				    <td style="">
				          <p><a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGraphDay/<?php echo $emsDataLoss[$ecdlKey]->device_id;?>/KW/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
				          	<?php $wil_sum_new[$ecdlKey] = round(abs($emsDataLoss[$ecdlKey]->data),2);?>
				          		<?php echo number_format((float)$wil_sum_new[$ecdlKey], 2, '.', '');?>
				          	</a></p>
				    </td>
				    <td style="">
				          <p>
				          	<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showPPWILchartLoss/<?php echo $ecdlVal->device_id;?>/<?php echo $emsDataLoss[$ecdlKey]->device_id;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>/abs">
				          	<?php $per_total = round($cpp_sum_new[$ecdlKey] - $wil_sum_new[$ecdlKey],2);?>
				          	<?php echo number_format((float)round($per_total,2), 2, '.', '');?>
				          </a>
				          </p>
				    </td>
				    <td style="">
				          <p>
				          	<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showPPWILchartLoss/<?php echo $ecdlVal->device_id;?>/<?php echo $emsDataLoss[$ecdlKey]->device_id;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>/per">
				          	<?php $cpp_sum_new_temp[$ecdlKey] = ($cpp_sum_new[$ecdlKey] == 0)?0:round(($per_total/$cpp_sum_new[$ecdlKey])*100,2);?>
				          	<?php echo number_format((float)round($cpp_sum_new_temp[$ecdlKey],2), 2, '.', '');?>
				          </a>
				          </p>
				    </td>
		    	</tr>
		    	<?php } ?>
		    	<?php } ?>
		    	
		    	
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Total</p>
			      	</th>
			      	<td style="">
			          <p>
			          	<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showCppWillChartLossTotalAll/ems_cpp/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
			          		<?php $cpp_total = array_sum($cpp_sum_new);?>
			          	<?php echo number_format((float)round($cpp_total,2), 2, '.', '');?>
			          </a>
			          </p>
				    </td>
				    <th style="">
				          <p>Total</p>
				    </th>
				    <td style="">
				          <p>
				          	<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showCppWillChartLossTotalAll/ems/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
				          		<?php $wil_total = array_sum($wil_sum_new);?>
				          	<?php echo number_format((float)round($wil_total,2), 2, '.', '');?>
				          </a>
				          </p>
				    </td>
				    <td style="">
				          <p>
				          	<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showCppWillChartLossAll/abs/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
				          	<?php $all_total = round($cpp_total - $wil_total,2);?> 
				          <?php echo number_format((float)round($all_total,2), 2, '.', '');?>
				      </a></p>
				    </td>
				    <td style="">
				          <p>
				          	<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showCppWillChartLossAll/per/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
				          	<?php $cpp_total_temp = ($cpp_total == 0)?0:round(($all_total/$cpp_total) * 100,2);?>
				          	<?php echo number_format((float)round($cpp_total_temp,2), 2, '.', '');?>
				          </a>
				          </p>
				    </td>
		    	
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>
<?php }?>

<div class="panel panel-default">
    <div class="panel-heading">Aggregated Stats :- All Busbar Distribution From WIL DG House</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		      <th style="">
		          <p>Type</p>
		      </th>
	      		<?php foreach($headerArr as $key => $val){ ?>
	      			<th style="">
			          <p><?php echo $val;?></p>
			        </th>
	      		<?php } ?>
		      
		    </tr>
		    </thead> 
		    <tbody> 
		    	<?php if(count($genDistAllData) > 0){ for($d = 3;$d <= 5; $d++){?>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>DG House Bus-<?php echo ($d == 3)?'A':(($d == 4)?'B':'C');?> Distribution </p>
			      	</th>
			      	<?php foreach($headerArr as $key => $val){ ?>
			      	<td>
			      		<?php 
			      		$showGdata = 0;
			      		if($val == 'PF'){
			      			if($genDistAllData[$g]->KWPF > 0){
			      				$showGdata = $genDistAllData[$d]->sum_KW/$genDistAllData[$d]->KWPF;
			      			}else{
			      				$showGdata = 0;
			      			}
			      		}else{
			      			$showGdata = $genDistAllData[$d]->{$val};
			      		}
			      		$d_sum[$val][] = $showGdata;
			      		?>
			      		
			      		<p><?php //echo round($showGdata,2);?>
			      			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGraphDayTotal/<?php echo $val;?>/<?php echo $genDistAllData[$d]->type_text;?>/<?php echo $genDistAllData[$d]->type_level;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
			      			<?php echo number_format((float)round($showGdata,2), 2, '.', '');?>
			      		</a>
			      		</p>
			      		
			      	</td>
			      	<?php } ?>
			    </tr>
			    <?php } } ?>
			    <tr role="row" class="odd">
		    		<th style="">
			          <p>Total</p>
			      	</th>
			      	<?php foreach($headerArr as $keyG => $valG){ $key = $valG; $showTempdata = 0;?>
			      	<td>
			      		<?php if($key == 'KW'){ 
		          			$g_kw = array_sum($d_sum[$key]) ; 
		          			$d_total[$key] = $showTempdata = $g_kw;
		          		?>
		          			<!-- <p><?php echo round($g_kw,2);?></p> -->
		          		<?php }elseif ($key == 'PF') {?>
		          			<?php 
		          			$d_total[$key] = $showPfVal = 0;
		          			if(count($d_sum[$key]) > 0){
		          				$kwPf = 0;
		          				$kw = 0;
	          					foreach ($d_sum[$key] as $key1 => $value1) {
	          						if($value1 > 0){
	          							$kwPf = $kwPf + ($d_sum['KW'][$key1]/$value1);
	          							$kw = $kw + $d_sum['KW'][$key1] ;
	          						}
	          					}
	          					if($kwPf > 0){
	          						$showPfVal = $kw/$kwPf;
	          					}
		          			} 
		          			$d_total[$key] = $showTempdata = $showPfVal;
		          			?>
		          			<!-- <p><?php echo round($showPfVal,2);?></p> -->
		          		<?php }elseif($key == 'Volt'){ 
		          			$g_vol = array_sum($d_sum[$key])/count($d_sum[$key]) ; 
		          			$d_total[$key] = $showTempdata = $g_vol;
		          			?>
		          			<!-- <p><?php echo round($g_vol,2);?></p> -->
		          		<?php	}elseif ($key == 'HZ') {
		          			$g_hz = array_sum($d_sum[$key])/count($d_sum[$key]); 
		          			$d_total[$key] = $showTempdata = $g_hz;
		          			?>
		          			<!-- <p><?php echo round($g_hz,2);?></p> -->
		          		<?php }elseif($key == 'Amps'){
		          			$g_amps = array_sum($d_sum[$key]); 
		          			$d_total[$key] = $showTempdata = $g_amps;
		          			?>
		          		
		          		<?php } ?>
		          		<p><?php //echo round($g_amps ,2);?>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGenDistAllTotal/D/<?php echo $key;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
		          			<?php echo number_format((float)round($showTempdata,2), 2, '.', '');?>
		          		</a>
		          		</p>
			      	</td>
			      	<?php }?>
			    </tr>
			</tbody>    
		</table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Distribution loss from all Busbar receiving to all Busbar Distribution</div>
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
			          	<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGenDistLossAllTotal/abs/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
			          <?php $showKw = $showKw1 = ($g_total['KW'] - $d_total['KW']);?>
			          <?php //echo round($showKw,2);?>
			          	<?php echo number_format((float)round($showKw,2), 2, '.', '');?>
			          </a>
			          </p>
			      	</th>
			      	<th style="">
			          <p>
			          	<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGenDistLossAllTotal/per/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
			          <?php $showKw12 = ($showKw1/$g_total['KW'])*100;?>
			          <?php //echo round($showKw12,3);?>
			          	<?php echo number_format((float)round($showKw12,2), 2, '.', '');?>
			          </a>
			          </p>
			      	</th>
			      	
		    	</tr>
		    </tbody>
		</table>
	</div>	    
</div>

<?php if(count($genDistAllData) > 0){ 
	for($k = 0; $k < 3; $k++){
		$keyGLD = $k + 1;
		$gIndex = $k ;
		$dIndex = $gIndex + 3;
?>
<div class="panel panel-default">
    <div class="panel-heading"><a href="<?php echo base_url();?>ems/report/index/<?php echo $keyGLD;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">Aggregated Stats :- Bus-<?php echo ($keyGLD == 1)?'A':(($keyGLD == 2)?'B':'C');?> Receiving vs Bus-<?php echo ($keyGLD == 1)?'A':(($keyGLD == 2)?'B':'C');?> Distribution </a></div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		      <th style="">
		          <p>Type</p>
		      </th>
		      <?php foreach($headerArr as $key => $val){ ?>
	      			<th style="">
			          <p><?php echo $val;?></p>
			        </th>
	      		<?php } ?>
		    </tr>
		    </thead>  
		    <tbody> 
		    	
		    		
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>DG House Bus-<?php echo ($keyGLD == 1)?'A':(($keyGLD == 2)?'B':'C');?> Receiving </p>
			      </th>
		    	<?php foreach($headerArr as $key => $val){ ?>
		          	<td>
		          		<p><?php //echo round($valGLD[$k]->{$val},2);?>
		          			
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGraphDayTotal/<?php echo $val;?>/<?php echo $genDistAllData[$gIndex]->type_text;?>/<?php echo $genDistAllData[$gIndex]->type_level;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
		          			<?php echo number_format((float)round($genDistAllData[$gIndex]->{$val},2), 2, '.', '');?>
		          		</a>
		          		</p>
		          	</td>
		      	<?php } ?>  	
		    	</tr>

		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>DG House Bus-<?php echo ($keyGLD == 1)?'A':(($keyGLD == 2)?'B':'C');?> Distribution</p>
			      </th>
		    	<?php foreach($headerArr as $key => $val){ ?>
		          	<td>
		          		<p><?php //echo round($valGLD[$k]->{$val},2);?>
		          			
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>ems/report/showGraphDayTotal/<?php echo $val;?>/<?php echo $genDistAllData[$dIndex]->type_text;?>/<?php echo $genDistAllData[$dIndex]->type_level;?>/<?php echo $chartDate;?>/<?php echo $chartDateEnd;?>">
		          			<?php echo number_format((float)round($genDistAllData[$dIndex]->{$val},2), 2, '.', '');?>
		          		</a>
		          		</p>
		          	</td>
		      	<?php } ?>  	
		    	</tr>
		    	
		  	</tbody>
		</table>
    </div>
</div>
<?php } } ?>



<?php //echo "<strong>Alert : Please note that some of the meter data have shown inconsistency and hence all indicators are not being displayed. Kindly speak to engineering/relevant department.</strong>";
?>