<?php 
if(count($typeWise) > 0){?>
<?php foreach ($typeWise as $key => $value) {
if($key=="main") continue;
	?>
<div class="panel panel-default">
    <div class="panel-heading"> Daywise aggregated Stats:- <?php echo ($key == 'gen')?'Generation':'Distribution';?></div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		        <th style="">
		        	<p></p>
		        </th>
		        <th style="">
		          <p>Pressure (Bar)</p>
		        </th>
		        <th style="">
		          <p>Temperature (&#8451;)</p>
		        </th>
		        <th style="">
		          <p>Flow (CFM)</p>
		        </th>
		        <!-- <th style="">
		          <p>Enthalpy (Kcal/Kg)</p>
		        </th> -->
		        <th style="">
		          <p><?php echo ($key == 'gen')?'Generation':'Consumption';?> (CFM)</p>
		        </th>
		    </tr>
		    </thead>  
		    <tbody> 
		    	
				<?php if(isset($typeWise[$key]['meter']) && count($typeWise[$key]['meter']) > 0){?>

				<?php foreach ($typeWise[$key]['meter'] as $key1 => $value1) {?>
		    	<tr role="row" class="odd">
		          	<td>
		          		<p><?php echo isset($value1['name']) ? $value1['name'] : '';?></p>
		        	</td>
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChart/<?php echo $key1;?>/pres"><?php echo isset($value1['P_pressure']) ? round($value1['P_pressure'],3) : '0';?></a></p>
		        	</td>
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChart/<?php echo $key1;?>/temp"><?php echo isset($value1['T_temp']) ? round($value1['T_temp'],3) : '0';?></a></p>
		        	</td>
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChart/<?php echo $key1;?>/flow"><?php echo isset($value1['flow']) ? round($value1['flow'],3) : '0';?></a></p>
		        	</td>
		        	<!-- <td>
		          	<p>
		          		<a href="javascript:void(0);" class="showChart" meter-link="<?php //echo base_url();?>fm/report/getMeterChart/<?php //echo $key1;?>/enthalpy"><?php //echo isset($value1['steam_enthalpy']) ? round($value1['steam_enthalpy'],3) : '0';?></a>
		          	<?php //echo ($value1['steam_enthalpy']==0) ? 'NA' : round($value1['steam_enthalpy'],2);?></p>
		        	</td> -->
		        	<td>
		          		<p title="<?php echo isset($value1['TTL_flow']) ? round($value1['TTL_flow'],3) : '0';?>">

			          		<?php
			          			if(isset($value1['TTL_flow'])){

			          				if($value1['TTL_flow']>=0){
			          					$TTLFlow = ($value1['TTL_flow']) / 1440;
			          					echo round($TTLFlow,3);
			          				}else{
			          					echo 'Error';
			          				}
			          			}else{
			          				echo '0';
			          			}
			          		?>
		          			
		          		</p>
		        	</td>
		      	</tr>
				<?php }?>
				<!-- Tempurary used -->
				<?php  if($key == 'dist'){?>
					<tr role="row" class="odd">
			          	<td>
			          		<p>OE Draw Frame Blow Room Shed 6 & 7</p>
			        	</td>
			        	<td colspan="4" style="text-align: center;">
			          		<p>Meter Not Connected</p>
			        	</td>
			        	<!-- <td>
			          		<p>Meter Not Connected</p>
			        	</td>	 -->	        	
			      	</tr>			      	
				<?php }?>
				<?php  if($key == 'gen'){?>
					<tr role="row" class="odd">
			          	<td>
			          		<p>Comp 1 (ZH15000), Comp 6 (ZR500VSD), <br/>Comp 7 (ZH7000), Comp 9 (ZH15000+), <br/>Comp 10 (ZH1600+), Comp 11 (Cameron Turbine)</p>
			        	</td>
			        	<td colspan="4" style="text-align: center;">
			          		<p>Meter Not Connected</p>
			        	</td>
			      	</tr>
			      	
				<?php }?>
				<!-- end Tempurary used -->
				<?php }?>
				<?php if(isset($typeWise[$key]['total']) && count($typeWise[$key]['total']) > 0){?>
		    	<tr role="row" class="even">
		          	<td>
		          	<p><?php echo ($key == 'gen')?'Generation':'Distribution';?> (Total)</p>
		        	</td>
		        	<td>
		          	<p><?php echo isset($typeWise[$key]['total']['P_pressure']) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';?></p>
		        	</td>
		        	<td>
		          	<p><?php echo isset($typeWise[$key]['total']['T_temp']) ? round($typeWise[$key]['total']['T_temp'],3) : '0';?></p>
		        	</td>
		        	<td>
		          	<p><?php echo isset($typeWise[$key]['total']['flow']) ? round($typeWise[$key]['total']['flow'],3) : '0';?></p>
		        	</td>
		        	<!-- <td>
		          	<p><?php //echo (isset($typeWise[$key]['total']['T_temp']) && $typeWise[$key]['total']['steam_enthalpy']!=0) ? round($typeWise[$key]['total']['steam_enthalpy'],3) : 'NA';?></p>
		        	</td>-->
		        	<td> 
			          	<p title="<?php echo isset($typeWise[$key]['total']['TTL_flow']) ? round($typeWise[$key]['total']['TTL_flow'],3) : '0';?>">

				          	<?php 
				          		if(isset($typeWise[$key]['total']['TTL_flow'])){
				          			if($typeWise[$key]['total']['TTL_flow']>=0){
				          				echo round($typeWise[$key]['total']['TTL_flow'] / 1440,3);
				          			}else{
				          				echo 'Error';
				          			}
				          		}else{
				          			echo '0';
				          		}
				          	?>		          		
			          	</p>
		        	</td>
		      	</tr>
				<?php }?>
		  	</tbody>
		</table>
    </div>
</div>
<?php }?>
<?php 
	$alert = 0;
	if(isset($typeWise['gen']['total']['P_pressure']) && isset($typeWise['dist']['total']['P_pressure']) && isset($typeWise['gen']['total']['T_temp']) && isset($typeWise['dist']['total']['T_temp']) && $typeWise['gen']['total']['P_pressure']>0 && $typeWise['dist']['total']['P_pressure']>0 && $typeWise['gen']['total']['T_temp']>0 && $typeWise['dist']['total']['T_temp']>0){	
?>
<div class="panel panel-default">
    <div class="panel-heading">Indicators:- Generation vs Distribution</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		          
		          <th style="">
		          <p>Delta Pressure (Bar)</p>
		        </th>
		        <!-- <th style="">
		          <p>Delta Temperature (&#8451;)</p>
		        </th> -->
		        <th style="">
		          <p>Delta Flow (CFM)</p>
		        </th>
		        <!-- <th style="">
		          <p>Delta Enthalpy (Kcal/Kg)</p>
		        </th> -->
		        <th style="">
		          <p>Generation - Distribution (CFM)</p>
		        </th>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php if(isset($typeWise[$key]['total']) && count($typeWise[$key]['total']) > 0){?>
		    	<tr role="row" class="even">
		          	
		        	<td>
		          	<p><?php 
		          		if(isset($typeWise['gen']['total']['P_pressure']) && isset($typeWise['dist']['total']['P_pressure'])){
		          			echo round($typeWise['gen']['total']['P_pressure'] - $typeWise['dist']['total']['P_pressure'],3);
		          		}else{
		          			if(isset($typeWise['gen']['total']['P_pressure'])){
		          				echo round($typeWise['gen']['total']['P_pressure'],3);
		          			}else if(isset($typeWise['dist']['total']['P_pressure'])){
		          				echo round($typeWise['dist']['total']['P_pressure'],3);
		          			}else{
		          				echo '0';
		          			}
		          		}
		          		?>
		          			
		          	</p>
		        	</td>
		        	<!-- <td>
		          	<p><?php 
		          		/*if(isset($typeWise['gen']['total']['T_temp']) && isset($typeWise['dist']['total']['T_temp'])){
		          			echo round($typeWise['gen']['total']['T_temp'] - $typeWise['dist']['total']['T_temp'],3); 
		          		}else{
		          			if(isset($typeWise['gen']['total']['T_temp'])){
		          				echo round($typeWise['gen']['total']['T_temp'],3);
		          			}else if(isset($typeWise['dist']['total']['T_temp'])){
		          				echo round($typeWise['dist']['total']['T_temp'],3);
		          			}else{
		          				echo '0';
		          			}
		          		}*/
		          		?>
		          		
		          	</p>
		        	</td> -->
		        	<td>
		          	<p><?php 
		          			if(isset($typeWise['gen']['total']['flow']) && isset($typeWise['dist']['total']['flow'])){
		          				echo round($typeWise['gen']['total']['flow'] - $typeWise['dist']['total']['flow'],3);
		          			}else{
		          				if(isset($typeWise['gen']['total']['flow'])){
		          					echo round($typeWise['gen']['total']['flow'],3);
		          				}else if(isset($typeWise['dist']['total']['flow'])){
		          					echo round($typeWise['dist']['total']['flow'],3);
		          				}else{
		          					echo '0';
		          				}
		          			}
		          		?>
		          		
		          	</p>
		        	</td>
		        	<!-- <td>
		          	<p><?php 
		          			/*if(isset($typeWise['gen']['total']['steam_enthalpy']) && $typeWise['gen']['total']['steam_enthalpy']!=0 && isset($typeWise['dist']['total']['steam_enthalpy']) && $typeWise['dist']['total']['steam_enthalpy']!=0){
		          				echo round($typeWise['gen']['total']['steam_enthalpy'] - $typeWise['dist']['total']['steam_enthalpy'],3);
		          			}else{
		          				echo 'NA';
		          			}*/

		          		?></p>
		        	</td> -->
		        	<td>
		        		<?php 
		        			if(isset($typeWise['gen']['total']['TTL_flow']) && $typeWise['gen']['total']['TTL_flow']!=0 && isset($typeWise['dist']['total']['TTL_flow']) && $typeWise['dist']['total']['TTL_flow']!=0){
		        				echo ($typeWise['gen']['total']['TTL_flow'] - $typeWise['dist']['total']['TTL_flow']);
		        			}else{
		        				echo 'NA';
		        			}
		        		?>
		        	</td>
		      	</tr>
				<?php }?>
		  	</tbody>
		</table>
    </div>
</div>
<?php }else{$alert = 1;}?>

<?php if(isset($typeWise['gen']['total']['P_pressure']) && $typeWise['gen']['total']['P_pressure']>0 && isset($typeWise['gen']['total']['T_temp']) && $typeWise['gen']['total']['T_temp']>0  && isset($typeWise['dist']['meter'])  && count($typeWise['dist']['meter']) > 0){?>

<div class="panel panel-default">
    <div class="panel-heading">Quality Indicators:- Distribution</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		          <th style="">
		          <p></p>
		        </th>
		          <th style="">
		          <p>Delta Pressure (Bar)</p>
		        </th>
		        <th style="">
		          <p>Delta Pressure Alert</p>
		        </th>
		        <!-- <th style="">
		          <p>Delta Temperature (&#8451;)</p>
		        </th> -->
		        <!-- <th style="">
		          <p>Delta Temperature Alert</p>
		        </th> -->
		        <!-- <th style="">
		          <p>Delta Flow (Ton/hr)</p>
		        </th> -->
		        <!-- <th style="">
		          <p>Delta Enthalpy (Kcal/Kg)</p>
		        </th> -->
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php if(isset($typeWise['dist']['meter']) && count($typeWise['dist']['meter']) > 0){?>
		    	<?php foreach ($typeWise['dist']['meter'] as $key2 => $value2) {

		    			$viewPressure = 0;
		    			if(isset($typeWise['gen']['total']['P_pressure']) && isset($value2['P_pressure'])){
		    				$viewPressure = round($typeWise['gen']['total']['P_pressure'] - $value2['P_pressure'],3);
		    			}else{
		    				if(isset($typeWise['gen']['total']['P_pressure'])){
		    					$viewPressure = round($typeWise['gen']['total']['P_pressure'],3);
		    				}else if(isset($value2['P_pressure'])){
		    					$viewPressure = round($value2['P_pressure'],3);
		    				}else{
		    					$viewPressure = 0;
		    				}
		    			}
		    			
		    			$viewPressureAlert = "";
		    			if(isset($viewPressure) && isset($value2['benchmark_delta_pressure'])){
		    				$viewPressureAlert = ($viewPressure > $value2['benchmark_delta_pressure'])?'<span style="color:red;">Alert</span>':'With in range';
		    			}
		    			$viewTemp = 0;
		    			if(isset($typeWise['gen']['total']['T_temp']) && isset($value2['T_temp'])){
		    				$viewTemp = round($typeWise['gen']['total']['T_temp'] - $value2['T_temp'],3);
		    			}else{
		    				if(isset($typeWise['gen']['total']['T_temp'])){
		    					$viewTemp = round($typeWise['gen']['total']['T_temp'],3);
		    				}else if(isset($value2['T_temp'])){
		    					$viewTemp = round($value2['T_temp'],3);
		    				}else{
		    					$viewTemp = 0;
		    				}
		    			}
		    			$viewTempAlert = "";
		    			if(isset($viewTemp) && isset($value2['benchmark_delta_temp'])){
		    				$viewTempAlert = ($viewTemp > $value2['benchmark_delta_temp'])?'<span style="color:red;">Alert</span>':'With in range';
		    			}
		    			
		    		?>
		    	<tr role="row" class="odd">
		          	<td>
		          		<p><?php echo isset($value2['name']) ? $value2['name'] : '';?></p>
		        	</td>
		        	<td>
		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChart/<?php echo $key2;?>/pres/<?php echo $viewPressure;?>/<?php echo isset($typeWise['gen']['total']['P_pressure']) ? $typeWise['gen']['total']['P_pressure'] : '0';?>/<?php echo isset($value2['benchmark_delta_pressure']) ? $value2['benchmark_delta_pressure'] : '0';?>"><?php echo $viewPressure;?></a> </p>
		        	</td>
		        	<td>
		        	<b><?php echo $viewPressureAlert;?></b>
		        	</td>
		        	<!-- <td>
		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php //echo base_url();?>fm/report/getMeterChart/<?php //echo $key2;?>/temp/<?php //echo $viewTemp;?>/<?php //echo isset($typeWise['gen']['total']['T_temp']) ? $typeWise['gen']['total']['T_temp'] : '0';?>/<?php //echo isset($value2['benchmark_delta_temp']) ? $value2['benchmark_delta_temp'] : '0';?>"><?php //echo $viewTemp;?></a> </p>
		        	</td>-->
		        	<!-- <td> 
		        	<b><?php //echo $viewTempAlert;?></b>
		        	</td> -->
		        	<!-- <td>
		          	<p><?php 
		          		/*if(isset($typeWise['gen']['total']['flow']) && $typeWise['gen']['total']['flow']!=0 && isset($value2['flow']) && $value2['flow']!=0){

		          			echo round($typeWise['gen']['total']['flow'] - $value2['flow'],3);
		          			
		          		}else{
		          			echo 'NA';
		          		}*/
		          		?></p>
		        	</td> -->
		        	<!-- <td>
		          	<p><?php 

		          			/*if(isset($typeWise['gen']['total']['steam_enthalpy']) && $typeWise['gen']['total']['steam_enthalpy']!=0 && isset($value2['steam_enthalpy']) && $value2['steam_enthalpy']!=0){
		          				echo round($typeWise['gen']['total']['steam_enthalpy'] - $value2['steam_enthalpy'],3);
		          			}else{
		          				echo 'NA';
		          			}*/
		          		?>
		          			
		          		</p>
		        	</td> -->
		      	</tr>
		      	<?php }?>
				<?php }?>
		  	</tbody>
		</table>
    </div>
</div>
<?php }else{$alert = 2;}?>


<?php 

	if($alert!=0){
		echo "<strong>Alert : Please note that some of the meter data have shown inconsistency and hence all indicators are not being displayed. Kindly speak to engineering/relevant department.</strong>";
	}
?>

<?php }?>