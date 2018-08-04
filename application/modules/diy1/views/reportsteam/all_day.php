<?php 

if(count($data) > 0){?>
<?php foreach ($data as $key => $value) {
if($key=="main") continue;
	?>
<div class="panel panel-default">
    <div class="panel-heading"> Steam Air Stats:- <?php echo ($key == 'gen')?'Generation':'Distribution';?></div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		        <th style="">
		        	<p></p>
		        </th>
		        <th style="">
		          <p>Pressure</p>
		        </th>
		        <th style="">
		          <p>Temperature</p>
		        </th>
		        <th style="">
		          <p>Flow</p>
		        </th>
		        
		    </tr>
		    </thead>  
		    <tbody> 
		    	
				<?php if(isset($data[$key]) && count($data[$key]) > 0){?>

				<?php foreach ($data[$key] as $key1 => $value1) {?>
		    	<tr role="row" class="odd">
		          	<td>
		          		<p><?php echo isset($value1['meter']['name']) ? $value1['meter']['name'] : '';?></p>
		        	</td>
		        	<td>
		          		<?php
		          			//var_dump($value1['meter']['P_pressure']);
		          			if(isset($value1['meter']['P_pressure']) && count($value1['meter']['P_pressure'])>0){
		          				echo 'Count : '.number_format($value1['meter']['P_pressure']['Count'],2).'<br/>';
			          			echo 'Min : '.number_format($value1['meter']['P_pressure']['Min'],2).'<br/>';
			          			echo 'Max : '.number_format($value1['meter']['P_pressure']['Max'],2).'<br/>';
			          			echo '1st Quantile : '.number_format($value1['meter']['P_pressure']['Quantiles']['1st Quantile'],2).'<br/>';
			          			echo '2nd Quantile : '.number_format($value1['meter']['P_pressure']['Quantiles']['2nd Quantile'],2).'<br/>';
			          			echo '3rd Quantile : '.number_format($value1['meter']['P_pressure']['Quantiles']['3rd Quantile'],2).'<br/>';
			          			echo 'Standard Deviation : '.number_format($value1['meter']['P_pressure']['Standard Deviation'],2).'<br/>';
			          			echo 'Mean : '.number_format($value1['meter']['P_pressure']['Mean'],2).'<br/>';
		          			}
		          			
		          		?>
		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['T_temp']);
	          				if(isset($value1['meter']['T_temp']) && count($value1['meter']['T_temp'])>0){
	          					echo 'Count : '.number_format($value1['meter']['T_temp']['Count'],2).'<br/>';
			          			echo 'Min : '.number_format($value1['meter']['T_temp']['Min'],2).'<br/>';
			          			echo 'Max : '.number_format($value1['meter']['T_temp']['Max'],2).'<br/>';
			          			echo '1st Quantile : '.number_format($value1['meter']['T_temp']['Quantiles']['1st Quantile'],2).'<br/>';
			          			echo '2nd Quantile : '.number_format($value1['meter']['T_temp']['Quantiles']['2nd Quantile'],2).'<br/>';
			          			echo '3rd Quantile : '.number_format($value1['meter']['T_temp']['Quantiles']['3rd Quantile'],2).'<br/>';
			          			echo 'Standard Deviation : '.number_format($value1['meter']['T_temp']['Standard Deviation'],2).'<br/>';
			          			echo 'Mean : '.number_format($value1['meter']['T_temp']['Mean'],2).'<br/>';
	          				}
		          			
		          		?>

		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['flow']);
	          				if(isset($value1['meter']['flow']) && count($value1['meter']['flow'])>0){
	          					echo 'Count : '.number_format($value1['meter']['flow']['Count'],2).'<br/>';
			          			echo 'Min : '.number_format($value1['meter']['flow']['Min'],2).'<br/>';
			          			echo 'Max : '.number_format($value1['meter']['flow']['Max'],2).'<br/>';
			          			echo '1st Quantile : '.number_format($value1['meter']['flow']['Quantiles']['1st Quantile'],2).'<br/>';
			          			echo '2nd Quantile : '.number_format($value1['meter']['flow']['Quantiles']['2nd Quantile'],2).'<br/>';
			          			echo '3rd Quantile : '.number_format($value1['meter']['flow']['Quantiles']['3rd Quantile'],2).'<br/>';
			          			echo 'Standard Deviation : '.number_format($value1['meter']['flow']['Standard Deviation'],2).'<br/>';
			          			echo 'Mean : '.number_format($value1['meter']['flow']['Mean'],2).'<br/>';
	          				}
		          			
		          		?>
		        	</td>
		        	<!-- <td>
		          	<p>
		          		<a href="javascript:void(0);" class="showChart" meter-link="<?php //echo base_url();?>fm/report/getMeterChart/<?php //echo $key1;?>/enthalpy"><?php //echo isset($value1['steam_enthalpy']) ? round($value1['steam_enthalpy'],3) : '0';?></a>
		          	<?php //echo ($value1['steam_enthalpy']==0) ? 'NA' : round($value1['steam_enthalpy'],2);?></p>
		        	</td> -->
		        	
		      	</tr>
				<?php }?>
				
				
				<?php }?>
				
		  	</tbody>
		</table>
    </div>
</div>
<?php }?>






<?php }?>