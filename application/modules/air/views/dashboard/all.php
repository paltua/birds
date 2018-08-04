
<?php if(count($result_cpp) > 0){
	
	//echo "<pre>";
	//var_dump($result_cpp);


	?>

<div class="panel panel-default">
    <div class="panel-heading">Aggregated Stats for last 15 minutes:- <?php //echo date("F j, Y, g:i a", strtotime($result_cpp['timestamp']['start_time']));?>  <?php echo date("F j, Y, g:i a", strtotime($result_cpp['timestamp']['end_time']));?> <strong>(CPP)</strong></div>
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
		        		        
		    </tr>
		    </thead>  
		    <tbody>
		    	<?php
		    		if(isset($result_cpp['meter_data']) && count($result_cpp['meter_data'])>0){
		    			foreach ($result_cpp['meter_data'] as $cppTypekey => $cppTypevalue) {
		    				foreach ($cppTypevalue as $cppMkey => $cppMvalue) {
		    					if($cppMkey==2) continue;
		    					if($cppMkey==3) continue;
		    					if($cppMkey==4) continue;

		    	?>
		    	<tr>
		    		<td><?php echo $cppMvalue['name'];?></td>
		    		<td>
		    			<!-- <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart_CPP/<?php echo $cppMkey;?>/pres">
		    				<?php echo (isset($cppMvalue['pressure']) && $cppMvalue['pressure']!=0) ? number_format($cppMvalue['pressure'],3) : '--';?> -->
		    				<?php
		    					if(isset($cppMvalue['pressure']) && $cppMvalue['pressure']!=0){
		    				?>
		    				<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart_CPPForDoubleGraph/<?php echo $cppMkey;?>/pres">
		    				<?php echo (isset($cppMvalue['pressure']) && $cppMvalue['pressure']!=0) ? number_format($cppMvalue['pressure'],3) : '--';?>
		    				</a>
		    				<?php }else{echo '0';}?>
		    		</td>
		    		<td>
		    			<?php
		    				if(isset($cppMvalue['temp']) && $cppMvalue['temp']!=0){
		    			?>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart_CPP/<?php echo $cppMkey;?>/temp">
		    			<?php echo (isset($cppMvalue['temp']) && $cppMvalue['temp']!=0) ? number_format($cppMvalue['temp'],3) : '--';?>
		    			</a>
		    			<?php }else{echo '--';}?>
		    		</td>
		    		<td>
		    			<!-- <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart_CPP/<?php echo $cppMkey;?>/flow">
		    				<?php echo (isset($cppMvalue['flow']) && $cppMvalue['flow']!=0) ? number_format($cppMvalue['flow'],3) : '--';?> -->
		    				<?php
		    					if(isset($cppMvalue['flow']) && $cppMvalue['flow']!=0){
		    				?>
		    				<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart_CPPForDoubleGraph/<?php echo $cppMkey;?>/flow">
		    				<?php echo (isset($cppMvalue['flow']) && $cppMvalue['flow']!=0) ? number_format($cppMvalue['flow'],3) : '--';?>
		    				<?php }else{ echo '0';}?>
		    			</a>
		    		</td>
		    		
		    	</tr>
		    	<?php
		    				}
		    			}
		    		}
		    	?>
		    </tbody>
		</table>
    </div>
</div>
<?php }?>
<?php if(count($typeWise) > 0){
	
	//echo "<pre>";
	//var_dump($typeWise);


	?>
<?php foreach ($typeWise as $key => $value) {
	
	if($key=="main") continue;
	?>
<div class="panel panel-default">
    <div class="panel-heading">Aggregated Stats for last 15 minutes:- <?php echo ($key == 'gen')?'Generation':'Distribution';?> <strong>(WIL)</strong></div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		        <th style="">
		          <p></p>
		        </th>
		        <?php if($key == 'gen'){?>
		        <th style="">
		          <p>KW</p>
		        </th>
		        <?php } ?>
		        <th style="">
		          <p>Pressure (Bar)</p>
		        </th>
		        <!-- <th style="">
		          <p>Alert Pressure</p>
		        </th> -->
		        <th style="">
		          <p>Temperature (&#8451;)</p>
		        </th>
		        <!-- <th style="">
		          <p>Alert Temperature</p>
		        </th> -->
		        <th style="">
		          <p>Flow (CFM)</p>
		        </th>
		        <?php if($key == 'gen'){?>
		        <th style="">
		          <p> KW/CFM </p>
		        </th>
		        <?php } ?>
		        <?php if($key == 'gen'){?>
		        <th style="">
		          <p> CFM CU(%) </p>
		        </th>
		        <?php } ?>
		        <!-- <th style="">
		          <p><?php //echo ($key == 'gen')?'Generation':'Consumption';?> (CFM)</p>
		        </th> -->
		        <!-- <th style="">
		          <p>Mail Status</p>
		        </th> -->
		    </tr>
		    </thead>  
		    <tbody> 
		    	
				<?php if(isset($typeWise[$key]['meter']) && count($typeWise[$key]['meter']) > 0){?>

				<?php foreach ($typeWise[$key]['meter'] as $key1 => $value1) {?>
		    	<tr role="row" class="odd">
		          	<td>
		          		<p><?php echo isset($value1['name']) ? $value1['name'] : '';?></p>
		        	</td>
		        	<?php if($key == 'gen'){?>
		        	<td>
		        		<?php 
		        			if(isset($compRelKW[$key1][0]) && $compRelKW[$key1][0]>0){
		        		?>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>ems/dashboard/showGraph_Air/<?php echo $compRelKW[$key1][1];?>/KW/<?php echo $key1;?>"><?php echo $compRelKW[$key1][0];?></a></p>
		          		<?php }else{
		          				echo 'DNA';
		          			  }
		          		?>

		        	</td>
		        	<?php } ?>
		        	<td>
		        		<?php
		        			if(isset($value1['P_pressure']) && $value1['P_pressure']>0){
		        		?>
		          		<!-- <p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart/<?php echo $key1;?>/pres"><?php echo (isset($value1['P_pressure']) && $value1['P_pressure']>0) ?round($value1['P_pressure'],3) : 'DNA';?></a></p> -->
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartFordoubleGraph/<?php echo $key1;?>/pres"><?php echo (isset($value1['P_pressure']) && $value1['P_pressure']>0) ?round($value1['P_pressure'],3) : 'DNA';?></a></p>
		          		<?php }else{
		          			echo 'DNA';
		          		}?>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_counter_pressure']) ? $value1['alert_counter_pressure'] : 0;?></p>
		        	</td> -->
		        	<td>
		        		<?php 
		        			if(isset($value1['T_temp']) && $value1['T_temp']>0){
		        		?>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart/<?php echo $key1;?>/temp"><?php echo (isset($value1['T_temp']) && $value1['T_temp']>0) ? round($value1['T_temp'],3) : 'DNA';?></a></p>
		          		<?php }else{
		          			echo 'DNA';
		          		}?>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_counter_temp']) ? $value1['alert_counter_temp'] : 0;?></p>
		        	</td> -->
		        	<td>
		          		<!-- <p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart/<?php echo $key1;?>/flow"><?php $cuFlow = isset($value1['flow']) ? round($value1['flow'],3) : '0';
		          			echo (isset($value1['flow']) && $value1['flow']>0) ? round($value1['flow'],3) : 'DNA';
		          		?></a></p> -->
		          		<?php 
		          			if(isset($value1['flow']) && $value1['flow']>0){
		          		?>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartFordoubleGraph/<?php echo $key1;?>/flow"><?php $cuFlow = isset($value1['flow']) ? round($value1['flow'],3) : '0';
		          			echo (isset($value1['flow']) && $value1['flow']>0) ? round($value1['flow'],3) : 'DNA';
		          		?></a></p>
		          		<?php }else{
		          			echo 'DNA';
		          		}?>
		        	</td>
		        	<?php if($key == 'gen'){?>
		        	<td>
		          		<p>
		          			<?php //echo $cuFlow = isset($value1['flow']) ? round($value1['flow'],3) : '0';?>
		          			

		          			<?php 
		          				if(isset($cuFlow) && $cuFlow>0){
		          			?>
		          			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart_forKWCFM/<?php echo $key1;?>"><?php echo ($cuFlow>0) ? number_format(($compRelKW[$key1][0] / $cuFlow),3) : '0';?></a>
		          			<?php }else{
		          				echo '0';
		          			}?>

		          		</p>
		          	</td>
		          	<?php } ?>
		          	<?php if($key == 'gen'){?>
		        	<td>
		          		<p>
		          			
		          			<?php 
		          				$cuCap = $value1['capacity'];
		          				$showCap = 0;
		          				if($cuCap > 0){
		          					$showCap = ($cuFlow / $cuCap) * 100;
		          				}
		          				$shoCuError = '';
		          			 	if($showCap > 110){
		          			 		echo $shoCuError = 'Capacity above 110%. Check Data';
		          			 	}else{
		          			 	?>
		          			 		<?php
		          			 			if($showCap!=0){
		          			 		?>
		          			 		<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart/<?php echo $key1;?>/capacity">
		          			 		<?php echo round($showCap,2);?>
		          			 		</a>
		          			 		<?php }else{ echo '0';}?>
		          			 	<?php
		          			 	}
		          			?>
		          			
		          		</p>
		          	</td>
		          	<?php } ?>

		      	</tr>
				<?php }?>
				<!-- Tempurary used -->
				<?php  if($key == 'dist'){?>
					<tr role="row" class="odd">
			          	<td>
			          		<p>OE Draw Frame Blow Room Shed 6 & 7</p>
			        	</td>
			        	<td colspan="3" style="text-align: center;">
			          		<p>Meter Not Connected</p>
			        	</td>
			        	<!-- <td>
			          		<p>Meter Not Connected</p>
			        	</td>	 -->	        	
			      	</tr>			      	
				<?php }?>
				<?php  if($key == 'gen'){
					
					foreach ($notConnetedMeter as $keyNcm => $valueNcm) {
						# code...
						if($keyNcm==11) continue;
					
				?>
					<tr role="row" class="odd">
			          	<td>
			          		<p><?php echo $valueNcm;?></p>
			        	</td>
			        	<td >

			        		

			          		<p><?php if($keyNcm == 11){ echo 'N/A';}else{ ?>
			          			<?php //echo $compRelKW[$keyNcm][0];?>
			          			<?php 
			          				if(isset($compRelKW[$keyNcm][0]) && $compRelKW[$keyNcm][0]!=0){
			          			?>
			          			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>ems/dashboard/showGraph_Air/<?php echo $compRelKW[$keyNcm][1];?>/KW/<?php echo $keyNcm;?>"><?php echo $compRelKW[$keyNcm][0];?></a>
			          			<?php }else{
			          				echo '0';
			          			}?>
			          			<?php }?></p>
			        	</td>
			        	<td colspan="5" style="text-align: center;">
			          		<p><?php if(in_array($keyNcm, $auxArr)){ echo 'N/A';}else{ echo "Meter Not Connected";}?></p>
			        	</td>
			        	
			      	</tr>
			      	
			      	
				<?php }  }?>
				<!-- end Tempurary used -->
				<?php }?>
				<?php if(isset($typeWise[$key]['total']) && count($typeWise[$key]['total']) > 0){?>
		    	<tr role="row" class="even">
		          	<td>
		          	<p><?php echo ($key == 'gen')?'Generation':'Distribution';?> (Total)</p>
		        	</td>
		        	<?php if($key == 'gen'){?>
		        	<td>
		          		
		        		<?php 
		        			if(isset($compRelKW_total) && $compRelKW_total!=0){
		        		?>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartKWTotal/"><?php echo (isset($compRelKW_total)) ? round($compRelKW_total,3) : '0';?></a></p>
		          		<?php }else{
		          			echo '0';
		          		}?>

		        	</td>
		        	<?php }?>
		        	<td>
		          	<!-- <p><?php 
		          		echo (isset($typeWise[$key]['total']['P_pressure'])) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';
		          		?></p> -->

		          		<!-- <p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartTotal/pres/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['P_pressure'])) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';?></a></p> -->
		          		<?php
		          			if(isset($typeWise[$key]['total']['P_pressure'])){
		          		?>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartTotalforDoubleGraph/pres/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['P_pressure'])) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';?></a></p>
		          		<?php }else{
		          			echo '0';
		          		}?>

		        	</td>
		        	<!-- <td>
		          	<p><?php 
		          		//echo 'NA'
		          		?></p>
		        	</td> -->
		        	<td>
		          	<!-- <p><?php //echo (isset($typeWise[$key]['total']['T_temp'])) ? round($typeWise[$key]['total']['T_temp'],3) : '0';?></p> -->
		          	<?php
		          		if(isset($typeWise[$key]['total']['T_temp'])){
		          	?>
		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartTotal/temp/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['T_temp'])) ? round($typeWise[$key]['total']['T_temp'],3) : '0';?></a></p>
		          	<?php }else{
		          		echo '0';
		          	}?>

		        	</td>
		        	<!-- <td>
		          	<p><?php //echo 'NA';?></p>
		        	</td> -->
		        	<td>
		          	<!-- <p><?php //echo (isset($typeWise[$key]['total']['flow'])) ? round($typeWise[$key]['total']['flow'],3) : '0';?></p> -->


		          	<!-- <p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartTotal/flow/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['flow'])) ? round($typeWise[$key]['total']['flow'],3) : '0';?></a></p> -->
		          	<?php
		          		if(isset($typeWise[$key]['total']['flow'])){
		          	?>
		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartTotalforDoubleGraph/flow/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['flow'])) ? round($typeWise[$key]['total']['flow'],3) : '0';?></a></p>
		          	<?php }else{
		          		echo '0';
		          	}?>

		        	</td>
		        	<!-- <td>
		          	<p><?php //echo (isset($typeWise[$key]['total']['steam_enthalpy']) && $typeWise[$key]['total']['steam_enthalpy']!=0) ? round($typeWise[$key]['total']['steam_enthalpy'],3) : 'NA';?></p>
		        	</td> -->
		        	<!-- <td>
		          	<p title="<?php //echo (isset($typeWise[$key]['total']['TTL_flow'])) ? round($typeWise[$key]['total']['TTL_flow'],3) : '0';?>">

		          	<?php
		          		/*if(isset($typeWise[$key]['total']['TTL_flow'])){
		          			if($typeWise[$key]['total']['TTL_flow']>=0){
		          				echo round($typeWise[$key]['total']['TTL_flow'],3);
		          			}else{
		          				echo 'Error';
		          			}
		          		}else{
		          			echo '0';
		          		}*/
		          	?>
		          	</p>
		        	</td> -->
		        	<?php if($key == 'gen'){?>
			        	<td>
			          	<p><?php echo 'NA';?></p>
			        	</td>
		        	<?php } ?>
		        	<?php if($key == 'gen'){?>
			        	<td>
			          	<p><?php echo 'NA';?></p>
			        	</td>
		        	<?php } ?>
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
    <div class="panel-heading">Quality Indicators:- Generation vs Distribution </div>
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
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php if(isset($typeWise[$key]['total']) && count($typeWise[$key]['total']) > 0){?>
		    	<tr role="row" class="even">
		          	
		        	<td>
		        		<!-- <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartTotalGNvsDIST/pres"> -->
		        		<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartTotalGNvsDISTforDoubleGraph/pres">
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
		          		</a>
		        	</td>
		        	<!-- <td>
		          	<p>
		          		<?php 
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
		        		<!-- <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartTotalGNvsDIST/flow"> -->
		        		<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChartTotalGNvsDISTforDoubleGraph/flow">
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
			          	</a>
		        	</td>
		        	<!-- <td>
		          	<p><?php 

		          		/*if(isset($typeWise['gen']['total']['steam_enthalpy']) && isset($typeWise['dist']['total']['steam_enthalpy']) && $typeWise['gen']['total']['steam_enthalpy'] != 0 && $typeWise['dist']['total']['steam_enthalpy'] != 0){

		          			echo round($typeWise['gen']['total']['steam_enthalpy'] - $typeWise['dist']['total']['steam_enthalpy'],3);
		          		}else{
		          			echo '0';
		          		}*/

		          		?>
		          			
		          	</p>
		        	</td> -->
		      	</tr>
				<?php }?>
		  	</tbody>
		</table>
    </div>
</div>
<?php }else {$alert = 1;}?>

<?php if(isset($typeWise['gen']['total']['P_pressure']) && $typeWise['gen']['total']['P_pressure']>0 && isset($typeWise['gen']['total']['T_temp']) && $typeWise['gen']['total']['T_temp']>0  && isset($typeWise['dist']['meter'])  && count($typeWise['dist']['meter']) > 0){
	
?>
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
		        </th>
		        <th style="">
		          <p>Delta Temperature Alert</p>
		        </th>
		        <th style="">
		          <p>Delta Enthalpy (Kcal/Kg)</p>
		        </th> -->
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php if(isset($typeWise['dist']['meter'])  && count($typeWise['dist']['meter']) > 0){?>
		    	<?php foreach ($typeWise['dist']['meter'] as $key2 => $value2) {
		    				$viewPressure = 0;
		    				if(isset($typeWise['gen']['total']['P_pressure']) && isset($value2['P_pressure']) && $typeWise['gen']['total']['P_pressure']>0 && $value2['P_pressure']>0){
		    					$viewPressure = round($typeWise['gen']['total']['P_pressure'] - $value2['P_pressure'],3);	
		    				}else{
		    					/*if(isset($typeWise['gen']['total']['P_pressure'])){
		    						$viewPressure = round($typeWise['gen']['total']['P_pressure'],3);
		    					}else if(isset($value2['P_pressure'])){
		    						$viewPressure = round($value2['P_pressure'],3);
		    					}else{
		    						$viewPressure = "0";
		    					}*/
		    					$viewPressure = 'DNA';
		    				}
		    				
		    				$viewPressureAlert = "";
		    				if(isset($viewPressure) && $viewPressure!='DNA' && isset($value2['benchmark_delta_pressure'])){
		    					$viewPressureAlert = ($viewPressure > $value2['benchmark_delta_pressure'])?'<span style="color:red;">Alert</span>':'With in range';
		    				}else{
		    					$viewPressureAlert = 'DNA';
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
		    						$viewTemp = '0';
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
		        		<?php
		        			if($viewPressure!=0 && $viewPressure!=0){
		        		?>
		          	<p><a href="javascript:void(0);" data-toggle="tooltip" class="showChart" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart/<?php echo $key2;?>/pres/<?php echo $viewPressure;?>/<?php echo isset($typeWise['gen']['total']['P_pressure']) ? $typeWise['gen']['total']['P_pressure'] : '0';?>/<?php echo isset($value2['benchmark_delta_pressure']) ? $value2['benchmark_delta_pressure'] : '0';?>" title="Baseline : <?php echo $value2['benchmark_delta_pressure'];?>"><?php echo $viewPressure;?></a> </p>
		          		<?php }else{ echo $viewPressure;}?>
		        	</td>
		        	<td>
		        	<b><?php echo $viewPressureAlert;?></b>
		        	</td>
		        	<!-- <td>
		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php //echo base_url();?>fm/dashboard/getMeterChart/<?php //echo $key2;?>/temp/<?php //echo $viewTemp;?>/<?php //echo isset($typeWise['gen']['total']['T_temp']) ? $typeWise['gen']['total']['T_temp'] : '0';?>/<?php //echo isset($value2['benchmark_delta_temp']) ? $value2['benchmark_delta_temp'] : '0';?>"><?php //echo $viewTemp;?></a> </p>
		        	</td>
		        	<td>
		        	<b><?php //echo $viewTempAlert;?></b>
		        	</td>
		        	
		        	<td>
		          	<p><?php 


		          		/*if(isset($typeWise['gen']['total']['steam_enthalpy']) && $typeWise['gen']['total']['steam_enthalpy']!=0 && isset($value2['steam_enthalpy']) && $value2['steam_enthalpy']!=0){
		          			echo round($typeWise['gen']['total']['steam_enthalpy'] - $value2['steam_enthalpy'],3);
		          		}else{
		          			echo 'NA';
		          		}*/
		          	?></p>
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

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>




