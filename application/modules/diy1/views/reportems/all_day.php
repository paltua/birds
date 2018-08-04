<?php 

if(count($data) > 0){?>
<?php foreach ($data as $key => $value) {
if($key=="main") continue;
	?>
<div class="panel panel-default">
    <div class="panel-heading"> Electricity Stats:- <?php echo $key;//($key == 'gen')?'Generation':'Distribution';?></div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		        <th style="">
		        	<p></p>
		        </th>
		        <th style="">
		          <p>KW</p>
		        </th>
		        <th style="">
		          <p>PF</p>
		        </th>
		        <th style="">
		          <p>Volt</p>
		        </th>
		        <th style="">
		          <p>Amps</p>
		        </th>
		        <th style="">
		          <p>HZ</p>
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
		          			//var_dump($value1['meter']['KW']);
		          			if(isset($value1['meter']['KW']) && count($value1['meter']['KW'])>0){
		          				echo 'Count : '.number_format($value1['meter']['KW']['Count'],2).'<br/>';
			          			echo 'Min : '.number_format($value1['meter']['KW']['Min'],2).'<br/>';
			          			echo 'Max : '.number_format($value1['meter']['KW']['Max'],2).'<br/>';
			          			echo '1st Quantile : '.number_format($value1['meter']['KW']['Quantiles']['1st Quantile'],2).'<br/>';
			          			echo '2nd Quantile : '.number_format($value1['meter']['KW']['Quantiles']['2nd Quantile'],2).'<br/>';
			          			echo '3rd Quantile : '.number_format($value1['meter']['KW']['Quantiles']['3rd Quantile'],2).'<br/>';
			          			echo 'Standard Deviation : '.number_format($value1['meter']['KW']['Standard Deviation'],2).'<br/>';
			          			echo 'Mean : '.number_format($value1['meter']['KW']['Mean'],2).'<br/>';
		          			}
		          			
		          		?>
		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['PF']);
	          				if(isset($value1['meter']['PF']) && count($value1['meter']['PF'])>0){
	          					echo 'Count : '.number_format($value1['meter']['PF']['Count'],2).'<br/>';
			          			echo 'Min : '.number_format($value1['meter']['PF']['Min'],2).'<br/>';
			          			echo 'Max : '.number_format($value1['meter']['PF']['Max'],2).'<br/>';
			          			echo '1st Quantile : '.number_format($value1['meter']['PF']['Quantiles']['1st Quantile'],2).'<br/>';
			          			echo '2nd Quantile : '.number_format($value1['meter']['PF']['Quantiles']['2nd Quantile'],2).'<br/>';
			          			echo '3rd Quantile : '.number_format($value1['meter']['PF']['Quantiles']['3rd Quantile'],2).'<br/>';
			          			echo 'Standard Deviation : '.number_format($value1['meter']['PF']['Standard Deviation'],2).'<br/>';
			          			echo 'Mean : '.number_format($value1['meter']['PF']['Mean'],2).'<br/>';
	          				}
		          			
		          		?>

		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['Volt']);
	          				if(isset($value1['meter']['Volt']) && count($value1['meter']['Volt'])>0){
	          					echo 'Count : '.number_format($value1['meter']['Volt']['Count'],2).'<br/>';
			          			echo 'Min : '.number_format($value1['meter']['Volt']['Min'],2).'<br/>';
			          			echo 'Max : '.number_format($value1['meter']['Volt']['Max'],2).'<br/>';
			          			echo '1st Quantile : '.number_format($value1['meter']['Volt']['Quantiles']['1st Quantile'],2).'<br/>';
			          			echo '2nd Quantile : '.number_format($value1['meter']['Volt']['Quantiles']['2nd Quantile'],2).'<br/>';
			          			echo '3rd Quantile : '.number_format($value1['meter']['Volt']['Quantiles']['3rd Quantile'],2).'<br/>';
			          			echo 'Standard Deviation : '.number_format($value1['meter']['Volt']['Standard Deviation'],2).'<br/>';
			          			echo 'Mean : '.number_format($value1['meter']['Volt']['Mean'],2).'<br/>';
	          				}
		          			
		          		?>
		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['Amps']);
	          				if(isset($value1['meter']['Amps']) && count($value1['meter']['Amps'])>0){
	          					echo 'Count : '.number_format($value1['meter']['Amps']['Count'],2).'<br/>';
			          			echo 'Min : '.number_format($value1['meter']['Amps']['Min'],2).'<br/>';
			          			echo 'Max : '.number_format($value1['meter']['Amps']['Max'],2).'<br/>';
			          			echo '1st Quantile : '.number_format($value1['meter']['Amps']['Quantiles']['1st Quantile'],2).'<br/>';
			          			echo '2nd Quantile : '.number_format($value1['meter']['Amps']['Quantiles']['2nd Quantile'],2).'<br/>';
			          			echo '3rd Quantile : '.number_format($value1['meter']['Amps']['Quantiles']['3rd Quantile'],2).'<br/>';
			          			echo 'Standard Deviation : '.number_format($value1['meter']['Amps']['Standard Deviation'],2).'<br/>';
			          			echo 'Mean : '.number_format($value1['meter']['Amps']['Mean'],2).'<br/>';
	          				}
		          			
		          		?>
		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['HZ']);
	          				if(isset($value1['meter']['HZ']) && count($value1['meter']['HZ'])>0){
	          					echo 'Count : '.number_format($value1['meter']['HZ']['Count'],2).'<br/>';
			          			echo 'Min : '.number_format($value1['meter']['HZ']['Min'],2).'<br/>';
			          			echo 'Max : '.number_format($value1['meter']['HZ']['Max'],2).'<br/>';
			          			echo '1st Quantile : '.number_format($value1['meter']['HZ']['Quantiles']['1st Quantile'],2).'<br/>';
			          			echo '2nd Quantile : '.number_format($value1['meter']['HZ']['Quantiles']['2nd Quantile'],2).'<br/>';
			          			echo '3rd Quantile : '.number_format($value1['meter']['HZ']['Quantiles']['3rd Quantile'],2).'<br/>';
			          			echo 'Standard Deviation : '.number_format($value1['meter']['HZ']['Standard Deviation'],2).'<br/>';
			          			echo 'Mean : '.number_format($value1['meter']['HZ']['Mean'],2).'<br/>';
	          				}
		          			
		          		?>
		        	</td>		        	
		      	</tr>
				<?php }?>
				
				
				<?php }?>
				
		  	</tbody>
		</table>
    </div>
</div>
<?php }?>






<?php }?>