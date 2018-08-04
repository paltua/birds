<?php 
$headerArr = array('Amps','HZ','KW','PF','Volt');

$g_sum = array();
$d_sum = array();
$g_total = array();
$d_total = array();
$cpp_sum = array();

?>

<div class="panel panel-default">
    <div class="panel-heading">Aggregated Stats for last 15 minutes :- All Generation(Power Plant)</div>
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
		    	<?php if(isset($typeWiseCpp)){?>
		      		<?php foreach($typeWiseCpp as $key => $val){ ?>
				    	<tr role="row" class="odd">
				    		<th style="">
					          <p><?php echo $val['name'];?></p>
					      </th>
				    		<?php foreach($headerArr as $keyX => $valX){ 
				    			if($valX == 'HZ'){
				    		?>

				    		<td>
				          		<?php 
							       $cpp_sum[$valX][] = 0;
							      ?>
				          		<p>0</p>
				          	</td>

				    		<?php }else{?>
				          	<td>
				          		<?php 
				          		if($valX == 'KW' || $valX == 'Volt'){
				          			
							       		$cpp_sum[$valX][] = abs($val[$valX]) * 1000;
							       	
							      ?>
				          			<p><?php echo round(abs($val[$valX]), 2) * 1000;?></p>
				          			<?php 
				          		}else{
				          			
				          				$cpp_sum[$valX][] = abs($val[$valX]);
				          			
				          			?>
				          				<p><?php echo round($val[$valX], 2);?></p>
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
		          		 <p><?php echo round($cppData ,2);?></p>
		          	</td>
		        	<?php } ?>
		      	
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Aggregated Stats for last 15 minutes :- All Generation</div>
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
			          <p>Generation 1</p>
			      </th>
		    	<?php if(isset($typeWise['G1'])){?>
		      		<?php foreach($typeWise['G1'] as $key => $val){ ?>
		          	<td>
		          		<?php 
					       $g_sum[$key][] = $val;
					      ?>
		          		<p><?php echo round($val,2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>  	
		    	</tr>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Generation 2</p>
			      </th>
		    	<?php if(isset($typeWise['G2'])){?>
		      		<?php foreach($typeWise['G2'] as $key => $val){ ?>
		          	<td>
		          		<?php 
					      $g_sum[$key][] = $val;
					      
					      ?>
		          		<p><?php echo round($val,2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>
		    	</tr>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Generation 3</p>
			      </th>
		    	<?php if(isset($typeWise['G3'])){?>
		      		<?php foreach($typeWise['G3'] as $key => $val){ ?>
		          	<td>
		          		<?php 
					      $g_sum[$key][] = $val;
					      
					      ?>
		          		<p><?php echo round($val,2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>
		    	</tr>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Total</p>
			      </th>
		    	<?php if(isset($typeWise['G3'])){?>
		      		<?php foreach($typeWise['G3'] as $key => $val){ ?>
		          	<td>
		          		<?php if($key == 'KW'){ 
		          			$g_kw = array_sum($g_sum[$key]) ; 
		          			$g_total[$key] = $g_kw;
		          		?>
		          			<p><?php echo round($g_kw,2);?></p>
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
		          			$g_total[$key] = $showPfVal;
		          			?>
		          			<p><?php echo round($showPfVal,2);?></p>
		          		<?php }elseif($key == 'Volt'){ 
		          			$g_vol = array_sum($g_sum[$key])/count($g_sum[$key]) ; 
		          			$g_total[$key] = $g_vol;
		          			?>
		          			<p><?php echo round($g_vol,2);?></p>
		          		<?php	}elseif ($key == 'HZ') {
		          			$g_hz = array_sum($g_sum[$key])/count($g_sum[$key]); 
		          			$g_total[$key] = $g_hz;
		          			?>
		          			<p><?php echo round($g_hz,2);?></p>
		          		<?php }elseif($key == 'Amps'){
		          			$g_amps = array_sum($g_sum[$key]); 
		          			$g_total[$key] = $g_amps;
		          			?>
		          		<p><?php echo round($g_amps ,2);?></p>
		          		<?php } ?>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Loss(Transmission-From Power Plant To WIL)</div>
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
				          <p><?php echo $cpp_sum_new[$ecdlKey] = round(abs($ecdlVal->data),2) * 1000; ?></p>
				    </td>
				    <th style="">
				          <p><?php echo $emsDataLoss[$ecdlKey]->device_name;?></p>
				    </th>
				    <td style="">
				          <p><?php echo $wil_sum_new[$ecdlKey] = round(abs($emsDataLoss[$ecdlKey]->data),2);?></p>
				    </td>
				    <td style="">
				          <p><?php echo $per_total = round($cpp_sum_new[$ecdlKey] - $wil_sum_new[$ecdlKey],2);?></p>
				    </td>
				    <td style="">
				          <p><?php echo ($cpp_sum_new[$ecdlKey] == 0)?0:round(($per_total/$cpp_sum_new[$ecdlKey])*100,2);?></p>
				    </td>
		    	</tr>
		    	<?php } ?>
		    	<?php } ?>
		    	
		    	
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Total</p>
			      	</th>
			      	<td style="">
			          <p><?php echo $cpp_total = array_sum($cpp_sum_new);?></p>
				    </td>
				    <th style="">
				          <p>Total</p>
				    </th>
				    <td style="">
				          <p><?php echo $wil_total = array_sum($wil_sum_new);?></p>
				    </td>
				    <td style="">
				          <p><?php echo $all_total = round($cpp_total - $wil_total,2);?> </p>
				    </td>
				    <td style="">
				          <p><?php echo ($cpp_total == 0)?0:round(($all_total/$cpp_total) * 100,2);?></p>
				    </td>
		    	
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Aggregated Stats for last 15 minutes :- All Distribution</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		      <th style="">
		          <p>Type</p>
		      </th>
		      <?php 
		       $g_sum = array();
		      ?>
		      <?php if(isset($typeWise['D1'])){?>
		      		<?php foreach($typeWise['D1'] as $key => $val){ ?>
		      			<th style="">
				          <p><?php echo $key;?></p>
				        </th>
		      		<?php } ?>
		      <?php } ?>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Distribution 1</p>
			      </th>
		    	<?php if(isset($typeWise['D1'])){?>
		      		<?php foreach($typeWise['D1'] as $key => $val){ ?>
		          	<td>
		          		<?php 
					       $g_sum[$key][] = $val;
					      ?>
		          		<p><?php echo round($val,2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>  	
		    	</tr>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Distribution 2</p>
			      </th>
		    	<?php if(isset($typeWise['D2'])){?>
		      		<?php foreach($headerArr as $key => $val){ ?>
		          	<td>
		          		<?php 
					      	$g_sum[$val][] = $typeWise['D2'][$val];
					    ?>
		          		<p><?php echo round($typeWise['D2'][$val],2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>
		    	</tr>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Distribution 3</p>
			      </th>
		    	<?php if(isset($typeWise['D3'])){?>
		      		<?php foreach($typeWise['D3'] as $key => $val){ ?>
		          	<td>
		          		<?php 
					      $g_sum[$key][] = $val;
					      
					      ?>
		          		<p><?php echo round($val,2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>
		    	</tr>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Total</p>
			      </th>
		    	<?php if(isset($typeWise['D3'])){?>
		      		<?php foreach($typeWise['D3'] as $key => $val){ ?>
		          	<td>
		          		<?php if($key == 'KW'){
		          			$d_kw = array_sum($g_sum[$key]) ; 
		          			$d_total[$key] = $d_kw;
		          			?>
		          			<p><?php echo round($d_kw,2);?></p>
		          		<?php }elseif ($key == 'PF') {?>
		          			<?php 
		          			$showPfVal = 0;
		          			$d_total[$key] = $showPfVal;
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
		          			 $d_total[$key] = $showPfVal;
		          			 ?>
		          			<p><?php echo round($showPfVal,2);?></p>
		          		<?php }elseif($key == 'Volt'){ 
		          			$d_vol = array_sum($g_sum[$key])/count($g_sum[$key]) ; 
		          			$d_total[$key] = $d_vol;
		          			?>
		          			<p><?php echo round($d_vol,2);?></p>
		          		<?php	}elseif ($key == 'HZ') {
		          			$d_hz = array_sum($g_sum[$key])/count($g_sum[$key]); 
		          			$d_total[$key] = $d_hz;
		          			?>
		          			<p><?php echo round($d_hz, 2);?></p>
		          		<?php }elseif($key == 'Amps'){
		          			$d_amps = array_sum($g_sum[$key]); 
		          			$d_total[$key] = $d_amps;
		          			?>
		          		<p><?php echo round($d_amps,2);?></p>
		          		<?php } ?>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>
<?php //print_r($g_total);print_r($d_total);?>
<div class="panel panel-default">
    <div class="panel-heading">Distribution loss</div>
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
			          <?php $showKw = $showKw1 = ($g_total['KW'] - $d_total['KW']);?>
			          <?php echo round($showKw,2);?></p>
			      	</th>
			      	<th style="">
			          <p>
			          <?php $showKw12 = ($showKw1/$g_total['KW'])*100;?>
			          <?php echo round($showKw12,3);?></p>
			      	</th>
			      	
		    	</tr>
		    </tbody>
		</table>
	</div>	    
</div>




<div class="panel panel-default">
    <div class="panel-heading"><a href="<?php echo base_url();?>ems/dashboard/live/1">Aggregated Stats for last 15 minutes :- Generation 1 vs Distribution 1 </a></div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		      <th style="">
		          <p>Type</p>
		      </th>
		      <?php if(isset($typeWise['G1'])){?>
		      		<?php foreach($typeWise['G1'] as $key => $val){ ?>
		      			<th style="">
				          <p><?php echo $key;?></p>
				        </th>
		      		<?php } ?>
		      <?php } ?>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Generation 1</p>
			      </th>
		    	<?php if(isset($typeWise['G1'])){?>
		      		<?php foreach($typeWise['G1'] as $key => $val){ ?>
		          	<td>
		          		<p><?php echo round($val,2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>  	
		    	</tr>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Distribution 1</p>
			      </th>
		    	<?php if(isset($typeWise['D1'])){?>
		      		<?php foreach($typeWise['D1'] as $key => $val){ ?>
		          	<td>
		          		<p><?php echo round($val,2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>  	
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><a href="<?php echo base_url();?>ems/dashboard/live/2">Aggregated Stats for last 15 minutes :- Generation 2 vs Distribution 2 </a></div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
		      		<th style="">
				          <p>Type</p>
				      </th>
				      <?php if(count($typeWise['G2']) > 0){?>
				      		<?php foreach($typeWise['G2'] as $key => $val){ ?>
				      			<th style="">
						          <p><?php echo $key;?></p>
						        </th>
				      		<?php } ?>
				      <?php } ?>
		    	</tr>
		    </thead>  
		    <tbody> 
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Generation 2</p>
			      </th>
		    	<?php if(isset($typeWise['G2'])){?>
		      		<?php foreach($typeWise['G2'] as $key => $val){ ?>
		          	<td>
		          		<p><?php echo round($val,2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>  	
		    	</tr>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Distribution 2</p>
			      </th>
		    	<?php if(isset($typeWise['D2'])){?>
		      		<?php foreach($headerArr as $key => $val){ ?>
		          	<td>
		          		<p><?php echo round($typeWise['D2'][$val],2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>  	
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><a href="<?php echo base_url();?>ems/dashboard/live/3">Aggregated Stats for last 15 minutes :- Generation 3 vs Distribution 3 </a></div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		      <th style="">
		          <p>Type</p>
		      </th>
		      <?php if(count($typeWise['G3']) > 0){?>
		      		<?php foreach($typeWise['G3'] as $key => $val){ ?>
		      			<th style="">
				          <p><?php echo $key;?></p>
				        </th>
		      		<?php } ?>
		      <?php } ?>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Generation 3</p>
			      </th>
		    	<?php if(isset($typeWise['G3'])){?>
		      		<?php foreach($typeWise['G3'] as $key => $val){ ?>
		          	<td>
		          		<p><?php echo round($val,2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>  	
		    	</tr>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Distribution 3</p>
			      </th>
		    	<?php if(isset($typeWise['D3'])){?>
		      		<?php foreach($typeWise['D3'] as $key => $val){ ?>
		          	<td>
		          		<p><?php echo round($val,2);?></p>
		          	</td>
		        	<?php } ?>
		      	<?php } ?>  	
		    	</tr>
		    	<!-- <tr role="row" class="odd">
		    		<th style="">
			          <p>Total</p>
			      </th>
			    	<?php if(isset($typeWise['D3'])){?>
			      		<?php foreach($typeWise['D3'] as $key => $val){ ?>
			          	<td>
			          		<p><?php echo round($val,2);?></p>
			          	</td>
			        	<?php } ?>
			      	<?php } ?>  	
		    	</tr> -->
		  	</tbody>
		</table>
    </div>
</div>


<?php echo "<strong>Alert : Please note that some of the meter data have shown inconsistency and hence all indicators are not being displayed. Kindly speak to engineering/relevant department.</strong>";
?>