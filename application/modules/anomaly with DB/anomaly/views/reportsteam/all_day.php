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
		          		?>
		          			<?php
	          					if(isset($value1['meter']['P_pressure']['calculated']['outlier_percent_mean'])){
	          				?>
	          				Outlier Percent Mean : <?php echo isset($value1['meter']['P_pressure']['calculated']['outlier_percent_mean']) ? number_format($value1['meter']['P_pressure']['calculated']['outlier_percent_mean'],2) : '';?><br/>
	          				<?php }?>
	          				Outlier Towards : <?php echo isset($value1['meter']['P_pressure']['calculated']['outlier_towards']) ? $value1['meter']['P_pressure']['calculated']['outlier_towards'] : 'No Outlier';?><br/>

			          		Counter : <?php echo isset($value1['meter']['P_pressure']['calculated']['counter']) ? number_format($value1['meter']['P_pressure']['calculated']['counter'],2) : '';?><br/>
			          		<?php if(isset($value1['meter']['P_pressure']['calculated']['analysis'])){?>
			          		Analysis : <?php echo $value1['meter']['P_pressure']['calculated']['analysis'];?><br/>
			          		<?php }?>
			          		High : <?php echo isset($value1['meter']['P_pressure']['calculated']['high']) ? number_format($value1['meter']['P_pressure']['calculated']['high'],2) : '';?><br/>
			          		Low : <?php echo isset($value1['meter']['P_pressure']['calculated']['low']) ? number_format($value1['meter']['P_pressure']['calculated']['low'],2) : '';?><br/>
			          		
			          		<?php if(isset($value1['meter']['P_pressure']['given'])){?>
			          		Given : <?php echo $value1['meter']['P_pressure']['given'];?><br/>
			          		<?php }?>
		          		<?php
		          			}
		          			
		          		?>
		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['T_temp']);
	          				if(isset($value1['meter']['T_temp']) && count($value1['meter']['T_temp'])>0){
	          			?>
	          				<?php
	          					if(isset($value1['meter']['T_temp']['calculated']['outlier_percent_mean'])){
	          				?>
	          				Outlier Percent Mean : <?php echo isset($value1['meter']['T_temp']['calculated']['outlier_percent_mean']) ? number_format($value1['meter']['T_temp']['calculated']['outlier_percent_mean'],2) : '';?><br/>
	          				<?php }?>
	          				Outlier Towards : <?php echo isset($value1['meter']['T_temp']['calculated']['outlier_towards']) ? $value1['meter']['T_temp']['calculated']['outlier_towards'] : 'No Outlier';?><br/>


			          		Counter : <?php echo isset($value1['meter']['T_temp']['calculated']['counter']) ? number_format($value1['meter']['T_temp']['calculated']['counter'],2) : '';?><br/>
			          		<?php if(isset($value1['meter']['T_temp']['calculated']['analysis'])){?>
			          		Analysis : <?php echo $value1['meter']['T_temp']['calculated']['analysis'];?><br/>
			          		<?php }?>
			          		High : <?php echo isset($value1['meter']['T_temp']['calculated']['high']) ? number_format($value1['meter']['T_temp']['calculated']['high'],2) : '';?><br/>
			          		Low : <?php echo isset($value1['meter']['T_temp']['calculated']['low']) ? number_format($value1['meter']['T_temp']['calculated']['low'],2) : '';?><br/>
			          		
			          		<?php if(isset($value1['meter']['T_temp']['given'])){?>
			          		Given : <?php echo $value1['meter']['T_temp']['given'];?><br/>
			          		<?php }?>
	          			<?php
	          				}
		          			
		          		?>

		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['flow']);
	          				if(isset($value1['meter']['flow']) && count($value1['meter']['flow'])>0){
	          			?>
	          				<?php
	          					if(isset($value1['meter']['flow']['calculated']['outlier_percent_mean'])){
	          				?>
	          				Outlier Percent Mean : <?php echo isset($value1['meter']['flow']['calculated']['outlier_percent_mean']) ? number_format($value1['meter']['flow']['calculated']['outlier_percent_mean'],2) : '';?><br/>
	          				<?php }?>
	          				Outlier Towards : <?php echo isset($value1['meter']['flow']['calculated']['outlier_towards']) ? $value1['meter']['flow']['calculated']['outlier_towards'] : 'No Outlier';?><br/>


			          		Counter : <?php echo isset($value1['meter']['flow']['calculated']['counter']) ? number_format($value1['meter']['flow']['calculated']['counter'],2) : '';?><br/>
			          		<?php if(isset($value1['meter']['flow']['calculated']['analysis'])){?>
			          		Analysis : <?php echo $value1['meter']['flow']['calculated']['analysis'];?><br/>
			          		<?php }?>
			          		High : <?php echo isset($value1['meter']['flow']['calculated']['high']) ? number_format($value1['meter']['flow']['calculated']['high'],2) : '';?><br/>
			          		Low : <?php echo isset($value1['meter']['flow']['calculated']['low']) ? number_format($value1['meter']['flow']['calculated']['low'],2) : '';?><br/>
			          		
			          		<?php if(isset($value1['meter']['flow']['given'])){?>
			          		Given : <?php echo $value1['meter']['flow']['given'];?><br/>
			          		<?php }?>
	          			<?php
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