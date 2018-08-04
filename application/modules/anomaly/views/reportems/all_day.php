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
		          		?>
		          			<?php
	          					if(isset($value1['meter']['KW']['calculated']['outlier_percent_mean'])){
	          				?>
	          				Outlier Percent Mean : <?php echo isset($value1['meter']['KW']['calculated']['outlier_percent_mean']) ? number_format($value1['meter']['KW']['calculated']['outlier_percent_mean'],2) : '';?><br/>
	          				<?php }?>
	          				Outlier Towards : <?php echo isset($value1['meter']['KW']['calculated']['outlier_towards']) ? $value1['meter']['KW']['calculated']['outlier_towards'] : 'No Outlier';?><br/>

			          		<!-- Counter : <?php //echo isset($value1['meter']['KW']['calculated']['counter']) ? number_format($value1['meter']['KW']['calculated']['counter'],2) : '';?><br/> -->
			          		<?php if(isset($value1['meter']['KW']['calculated']['analysis'])){?>
			          		Analysis : <?php echo $value1['meter']['KW']['calculated']['analysis'];?><br/>
			          		<?php }?>
			          		High : <?php echo isset($value1['meter']['KW']['calculated']['high']) ? number_format($value1['meter']['KW']['calculated']['high'],2) : '';?><br/>
			          		Low : <?php echo isset($value1['meter']['KW']['calculated']['low']) ? number_format($value1['meter']['KW']['calculated']['low'],2) : '';?><br/>
			          		
			          		<?php if(isset($value1['meter']['KW']['given'])){?>
			          		Given : <?php echo $value1['meter']['KW']['given'];?><br/>
			          		<?php }?>
		          		<?php
		          			}
		          			
		          		?>
		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['PF']);
	          				if(isset($value1['meter']['PF']) && count($value1['meter']['PF'])>0){
	          			?>
	          				<?php
	          					if(isset($value1['meter']['PF']['calculated']['outlier_percent_mean'])){
	          				?>
	          				Outlier Percent Mean : <?php echo isset($value1['meter']['PF']['calculated']['outlier_percent_mean']) ? number_format($value1['meter']['PF']['calculated']['outlier_percent_mean'],2) : '';?><br/>
	          				<?php }?>
	          				Outlier Towards : <?php echo isset($value1['meter']['PF']['calculated']['outlier_towards']) ? $value1['meter']['PF']['calculated']['outlier_towards'] : 'No Outlier';?><br/>

			          		<!-- Counter : <?php //echo isset($value1['meter']['PF']['calculated']['counter']) ? number_format($value1['meter']['PF']['calculated']['counter'],2) : '';?><br/> -->
			          		<?php if(isset($value1['meter']['PF']['calculated']['analysis'])){?>
			          		Analysis : <?php echo $value1['meter']['PF']['calculated']['analysis'];?><br/>
			          		<?php }?>
			          		High : <?php echo isset($value1['meter']['PF']['calculated']['high']) ? number_format($value1['meter']['PF']['calculated']['high'],2) : '';?><br/>
			          		Low : <?php echo isset($value1['meter']['PF']['calculated']['low']) ? number_format($value1['meter']['PF']['calculated']['low'],2) : '';?><br/>
			          		
			          		<?php if(isset($value1['meter']['PF']['given'])){?>
			          		Given : <?php echo $value1['meter']['PF']['given'];?><br/>
			          		<?php }?>
	          			<?php
	          				}
		          			
		          		?>

		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['Volt']);
	          				if(isset($value1['meter']['Volt']) && count($value1['meter']['Volt'])>0){
	          			?>
	          				<?php
	          					if(isset($value1['meter']['Volt']['calculated']['outlier_percent_mean'])){
	          				?>
	          				Outlier Percent Mean : <?php echo isset($value1['meter']['Volt']['calculated']['outlier_percent_mean']) ? number_format($value1['meter']['Volt']['calculated']['outlier_percent_mean'],2) : '';?><br/>
	          				<?php }?>
	          				Outlier Towards : <?php echo isset($value1['meter']['Volt']['calculated']['outlier_towards']) ? $value1['meter']['Volt']['calculated']['outlier_towards'] : 'No Outlier';?><br/>

			          		<!-- Counter : <?php //echo isset($value1['meter']['Volt']['calculated']['counter']) ? number_format($value1['meter']['Volt']['calculated']['counter'],2) : '';?><br/> -->
			          		<?php if(isset($value1['meter']['Volt']['calculated']['analysis'])){?>
			          		Analysis : <?php echo $value1['meter']['Volt']['calculated']['analysis'];?><br/>
			          		<?php }?>
			          		High : <?php echo isset($value1['meter']['Volt']['calculated']['high']) ? number_format($value1['meter']['Volt']['calculated']['high'],2) : '';?><br/>
			          		Low : <?php echo isset($value1['meter']['Volt']['calculated']['low']) ? number_format($value1['meter']['Volt']['calculated']['low'],2) : '';?><br/>
			          		
			          		<?php if(isset($value1['meter']['Volt']['given'])){?>
			          		Given : <?php echo $value1['meter']['Volt']['given'];?><br/>
			          		<?php }?>
	          			<?php
	          				}
		          			
		          		?>
		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['Amps']);
	          				if(isset($value1['meter']['Amps']) && count($value1['meter']['Amps'])>0){
	          			?>
	          				<?php
	          					if(isset($value1['meter']['Amps']['calculated']['outlier_percent_mean'])){
	          				?>
	          				Outlier Percent Mean : <?php echo isset($value1['meter']['Amps']['calculated']['outlier_percent_mean']) ? number_format($value1['meter']['Amps']['calculated']['outlier_percent_mean'],2) : '';?><br/>
	          				<?php }?>
	          				Outlier Towards : <?php echo isset($value1['meter']['Amps']['calculated']['outlier_towards']) ? $value1['meter']['Amps']['calculated']['outlier_towards'] : 'No Outlier';?><br/>

			          		<!-- Counter : <?php //echo isset($value1['meter']['Amps']['calculated']['counter']) ? number_format($value1['meter']['Amps']['calculated']['counter'],2) : '';?><br/> -->
			          		<?php if(isset($value1['meter']['Amps']['calculated']['analysis'])){?>
			          		Analysis : <?php echo $value1['meter']['Amps']['calculated']['analysis'];?><br/>
			          		<?php }?>
			          		High : <?php echo isset($value1['meter']['Amps']['calculated']['high']) ? number_format($value1['meter']['Amps']['calculated']['high'],2) : '';?><br/>
			          		Low : <?php echo isset($value1['meter']['Amps']['calculated']['low']) ? number_format($value1['meter']['Amps']['calculated']['low'],2) : '';?><br/>
			          		
			          		<?php if(isset($value1['meter']['Amps']['given'])){?>
			          		Given : <?php echo $value1['meter']['Amps']['given'];?><br/>
			          		<?php }?>
	          			<?php
	          				}
		          			
		          		?>
		        	</td>
		        	<td>
	          			<?php
		          			//var_dump($value1['meter']['HZ']);
	          				if(isset($value1['meter']['HZ']) && count($value1['meter']['HZ'])>0){
	          			?>
	          				<?php
	          					if(isset($value1['meter']['HZ']['calculated']['outlier_percent_mean'])){
	          				?>
	          				Outlier Percent Mean : <?php echo isset($value1['meter']['HZ']['calculated']['outlier_percent_mean']) ? number_format($value1['meter']['HZ']['calculated']['outlier_percent_mean'],2) : '';?><br/>
	          				<?php }?>
	          				Outlier Towards : <?php echo isset($value1['meter']['HZ']['calculated']['outlier_towards']) ? $value1['meter']['HZ']['calculated']['outlier_towards'] : 'No Outlier';?><br/>

			          		<!-- Counter : <?php //echo isset($value1['meter']['HZ']['calculated']['counter']) ? number_format($value1['meter']['HZ']['calculated']['counter'],2) : '';?><br/> -->
			          		<?php if(isset($value1['meter']['HZ']['calculated']['analysis'])){?>
			          		Analysis : <?php echo $value1['meter']['HZ']['calculated']['analysis'];?><br/>
			          		<?php }?>
			          		High : <?php echo isset($value1['meter']['HZ']['calculated']['high']) ? number_format($value1['meter']['HZ']['calculated']['high'],2) : '';?><br/>
			          		Low : <?php echo isset($value1['meter']['HZ']['calculated']['low']) ? number_format($value1['meter']['HZ']['calculated']['low'],2) : '';?><br/>
			          		
			          		<?php if(isset($value1['meter']['HZ']['given'])){?>
			          		Given : <?php echo $value1['meter']['HZ']['given'];?><br/>
			          		<?php }?>
	          			<?php
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