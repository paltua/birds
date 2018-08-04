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
		          <p>Pressure (Kg/cm<sup>2</sup>)</p>
		        </th>
		        <th style="">
		          <p>Temperature (&#8451;)</p>
		        </th>
		        <th style="">
		          <p>Flow (Ton/hr)</p>
		        </th>
		        		        
		    </tr>
		    </thead>  
		    <tbody>
		    	<?php
		    		if(isset($result_cpp['meter_data']) && count($result_cpp['meter_data'])>0){
		    			foreach ($result_cpp['meter_data'] as $cppTypekey => $cppTypevalue) {
		    				foreach ($cppTypevalue as $cppMkey => $cppMvalue) {
		    					if($cppMkey==1) continue;
		    					if($cppMkey=="total") continue;
		    	?>
		    	<tr>

		    		


		    		<td><?php echo $cppMvalue['name'];?></td>
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart_CPP/<?php echo $cppMkey;?>/pres">
		    				<?php echo (isset($cppMvalue['pressure']) && $cppMvalue['pressure']!=0) ? number_format($cppMvalue['pressure'],3) : '--';?>
		    			</a>
		    		</td>
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart_CPP/<?php echo $cppMkey;?>/temp">
		    				<?php echo (isset($cppMvalue['temp']) && $cppMvalue['temp']!=0) ? number_format($cppMvalue['temp'],3) : '--';?>
		    			</a>
		    		</td>
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart_CPP/<?php echo $cppMkey;?>/flow">
		    				<?php echo (isset($cppMvalue['flow']) && $cppMvalue['flow']!=0) ? number_format($cppMvalue['flow'],3) : '--';?>
		    			</a>
		    		</td>
		    		
		    	</tr>
		    	<?php
		    				}
		    			}
		    	?>

		    	<tr role="row" class="even">
		    		<td>Total</td>
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal_CPP/pres/gen">

		    				<?php echo (isset($result_cpp['meter_data']['gen']['total']['pressure'])) ? round($result_cpp['meter_data']['gen']['total']['pressure'],3) : '0';?>
		    			</a>
		    		</td>
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal_CPP/temp/head">
		    				<?php echo (isset($result_cpp['meter_data']['head']['total']['temp'])) ? round($result_cpp['meter_data']['head']['total']['temp'],3) : '0';?>
		    			</head>
		    		</td>
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal_CPP/flow/gen">
		    				<?php echo (isset($result_cpp['meter_data']['gen']['total']['flow'])) ? round($result_cpp['meter_data']['gen']['total']['flow'],3) : '0';?>
		    			</a>
		    		</td>
		    	</tr>
		    	<?php
		    		}
		    	?>

		    </tbody>
		</table>
    </div>
</div>
<?php }?>

<?php if(count($result_cpp) > 0){?>

<div class="panel panel-default">
    <div class="panel-heading">CPP Generation VS Power plant header</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		        <th style="">
		          <p>Delta Pressure (Kg/cm<sup>2</sup>)</p>
		        </th>
		        <th style="">
		          <p>Delta Temperature (&#8451;)</p>
		        </th>
		        <th style="">
		          <p>Delta Enthalpy (Kcal/Kg)</p>
		        </th>
		        		        
		    </tr>
		    </thead>  
		    <tbody>
		    	
		    	<tr role="row" class="even">
		    		
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart_CPPgenVSpph/pres">
			    			<?php
				    			$deltaGenPresCPP = isset($result_cpp['meter_data']['gen']['total']['pressure']) ? $result_cpp['meter_data']['gen']['total']['pressure'] : '0';
				    			$deltaHeadPresCPP = isset($result_cpp['meter_data']['head'][4]['pressure']) ? $result_cpp['meter_data']['head'][4]['pressure'] : '0';
				    			

				    			echo number_format(($deltaGenPresCPP - $deltaHeadPresCPP),3);
			    			?>
		    			</a>

		    		<td>

		    			<?php
			    			/*$deltaGenTempCPP = isset($result_cpp['meter_data']['head']['total']['temp']) ? $result_cpp['meter_data']['head']['total']['temp'] : '0';
			    			$deltaHeadTempCPP = isset($result_cpp['meter_data']['head'][4]['temp']) ? $result_cpp['meter_data']['head'][4]['temp'] : '0';

			    			echo number_format(($deltaGenTempCPP - $deltaHeadTempCPP),3);*/
		    			?>
		    			NA
		    		</td>
		    		<td>
		    			NA
		    		</td>
		    	</tr>
		    	

		    </tbody>
		</table>
    </div>
</div>
<?php }?>

<?php if(count($typeWise) > 0){

	?>
<?php foreach ($typeWise as $key => $value) {
	if($key=="dist") continue;
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
		        <th style="">
		          <p>Pressure (Kg/cm<sup>2</sup>)</p>
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
		          <p>Flow (Ton/hr)</p>
		        </th>
		        <th style="">
		          <p>Enthalpy (Kcal/Kg)</p>
		        </th>
		        <!-- <th style="">
		          <p><?php //echo ($key == 'gen')?'Generation':'Consumption';?> (Tons)</p>
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
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $key1;?>/pres"><?php echo isset($value1['P_pressure']) ?round($value1['P_pressure'],3) : '0';?></a></p>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_counter_pressure']) ? $value1['alert_counter_pressure'] : 0;?></p>
		        	</td> -->
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $key1;?>/temp"><?php echo isset($value1['T_temp']) ? round($value1['T_temp'],3) : 0;?></a></p>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_counter_temp']) ? $value1['alert_counter_temp'] : 0;?></p>
		        	</td> -->
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $key1;?>/flow"><?php echo isset($value1['flow']) ? round($value1['flow'],3) : '0';?></a></p>
		        	</td>
		        	<td>
		          	<p>

		          		<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $key1;?>/enthalpy"><?php echo isset($value1['steam_enthalpy']) ? round($value1['steam_enthalpy'],3) : '0';?></a>

		          	<?php //echo ($value1['steam_enthalpy']==0) ? 'NA' : round($value1['steam_enthalpy'],2);?>
		          	</p>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['TTL_flow']) ? round($value1['TTL_flow'],3) : '0';?></p>
		        	</td> -->
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_mail_status']) ? ($value1['alert_mail_status']==1 ? 'YES' : 'NO') : 'NO';?></p>
		        	</td> -->
		      	</tr>
				<?php }?>
				<!-- Tempurary used -->
				<?php  if($key == 'gen'){?>
					<tr role="row" class="odd">
			          	<td>
			          		<p>TC1, TC2, SM270 (Boiler), SM120 (Boiler)</p>
			        	</td>
			        	<td colspan="4" style="text-align: center;">
			          		<p>Meters Not Connected</p>
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
		          	<p><!-- <?php 
		          		//echo (isset($typeWise[$key]['total']['P_pressure'])) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';
		          		?> --></p>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal/pres/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['P_pressure'])) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';?></a></p>

		        	</td>
		        	<!-- <td>
		          	<p><?php 
		          		//echo 'NA'
		          		?></p>
		        	</td> -->
		        	<td>
		          	<!-- <p><?php //echo (isset($typeWise[$key]['total']['T_temp'])) ? round($typeWise[$key]['total']['T_temp'],3) : '0';?></p> -->

		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal/temp/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['T_temp'])) ? round($typeWise[$key]['total']['T_temp'],3) : '0';?></a></p>

		        	</td>
		        	<!-- <td>
		          	<p><?php //echo 'NA';?></p>
		        	</td> -->
		        	<td>
		          	<!-- <p><?php //echo (isset($typeWise[$key]['total']['flow'])) ? round($typeWise[$key]['total']['flow'],3) : '0';?></p> -->
		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal/flow/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['flow'])) ? round($typeWise[$key]['total']['flow'],3) : '0';?></a></p>
		        	</td>
		        	<td>
		          	<!-- <p><?php //echo (isset($typeWise[$key]['total']['steam_enthalpy']) && $typeWise[$key]['total']['steam_enthalpy']!=0) ? round($typeWise[$key]['total']['steam_enthalpy'],3) : 'NA';?></p> -->
		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal/enthalpy/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['steam_enthalpy']) && $typeWise[$key]['total']['steam_enthalpy']!=0) ? round($typeWise[$key]['total']['steam_enthalpy'],3) : 'NA';?></a></p>
		        	</td>
		        	<!-- <td>
		          	<p><?php //echo (isset($typeWise[$key]['total']['TTL_flow'])) ? round($typeWise[$key]['total']['TTL_flow'],3) : '0';?></p>
		        	</td> -->
		        	<!-- <td>
		          	<p><?php //echo 'NA';?></p>
		        	</td> -->
		      	</tr>
				<?php }?>
		  	</tbody>
		</table>
    </div>
</div>
<?php }?>


<?php if(count($result_cpp) > 0 && count($typeWise)>0){?>

<div class="panel panel-default">
    <div class="panel-heading">Power Plant Header VS WIL Generation</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      <tr role="row">
		        <th style="">
		          <p>Delta Pressure (Kg/cm<sup>2</sup>)</p>
		        </th>
		        <th style="">
		          <p>Delta Temperature (&#8451;)</p>
		        </th>
		        <th style="">
		          <p>Delta Flow (Ton/hr)</p>
		        </th>
		        <th style="">
		          <p>Delta Enthalpy (Kcal/Kg)</p>
		        </th>
		        		        
		    </tr>
		    </thead>  
		    <tbody>
		    	
		    	<tr role="row" class="even">
		    		
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart_CPP_PPHvsWILgen/pres">
			    			<?php
				    			$deltaCPPheadPres = isset($result_cpp['meter_data']['head'][4]['pressure']) ? $result_cpp['meter_data']['head'][4]['pressure'] : '0';
				    			$deltaWILgenPres = isset($typeWise['gen']['total']['P_pressure']) ? $typeWise['gen']['total']['P_pressure'] : '0';
				    			
				    			echo number_format(($deltaCPPheadPres - $deltaWILgenPres),3);
			    			?>
			    		</a>
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart_CPP_PPHvsWILgen/temp">
			    			<?php
				    			$deltaCPPheadTemp = isset($result_cpp['meter_data']['head'][4]['temp']) ? $result_cpp['meter_data']['head'][4]['temp'] : '0';
				    			$deltaWILgenTemp = isset($typeWise['gen']['total']['T_temp']) ? $typeWise['gen']['total']['T_temp'] : '0';
				    			
				    			echo number_format(($deltaCPPheadTemp - $deltaWILgenTemp),3);
			    			?>
			    		</a>
		    		</td>
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart_CPP_PPHvsWILgen/flow">
			    			<?php
				    			$deltaCPPflow = isset($result_cpp['meter_data']['gen']['total']['flow']) ? $result_cpp['meter_data']['gen']['total']['flow'] : '0';
				    			$deltaWILflow = isset($typeWise['gen']['total']['flow']) ? $typeWise['gen']['total']['flow'] : '0';

				    			echo number_format(($deltaCPPflow - $deltaWILflow),3);
			    			?>
			    		</a>
		    		</td>
		    		<td>
		    			NA
		    		</td>
		    	</tr>
		    	

		    </tbody>
		</table>
    </div>
</div>
<?php }?>





<?php 
	//echo "<pre>";
	//var_dump($typeWise['main']['meter']);

	foreach ($typeWise as $key => $value) {
	if($key=="gen") continue;
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
		        <th style="">
		          <p>Pressure (Kg/cm<sup>2</sup>)</p>
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
		          <p>Flow (Ton/hr)</p>
		        </th>
		        <th style="">
		          <p>Enthalpy (Kcal/Kg)</p>
		        </th>
		        <!-- <th style="">
		          <p><?php //echo ($key == 'gen')?'Generation':'Consumption';?> (Tons)</p>
		        </th> -->
		        <!-- <th style="">
		          <p>Mail Status</p>
		        </th> -->
		    </tr>
		    </thead>  
		    <tbody> 
		    	
				<?php 

				if(isset($typeWise['main']['meter']) && count($typeWise['main']['meter'])>0){
					foreach ($typeWise['main']['meter'] as $keymain1 => $valuemain1) {
				?>
					<tr role="row" class="odd">
		          	<td>
		          		<p><?php echo isset($valuemain1['name']) ? $valuemain1['name'] : '';?></p>
		        	</td>
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $keymain1;?>/pres"><?php echo isset($valuemain1['P_pressure']) ?round($valuemain1['P_pressure'],3) : '0';?></a></p>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_counter_pressure']) ? $value1['alert_counter_pressure'] : 0;?></p>
		        	</td> -->
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $keymain1;?>/temp"><?php echo isset($valuemain1['T_temp']) ? round($valuemain1['T_temp'],3) : 0;?></a></p>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_counter_temp']) ? $value1['alert_counter_temp'] : 0;?></p>
		        	</td> -->
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $keymain1;?>/flow"><?php echo isset($valuemain1['flow']) ? round($valuemain1['flow'],3) : '0';?></a></p>
		        	</td>
		        	<td>
		          	<p>

		          		<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $keymain1;?>/enthalpy"><?php echo isset($valuemain1['steam_enthalpy']) ? round($valuemain1['steam_enthalpy'],3) : '0';?></a>

		          	<?php //echo ($value1['steam_enthalpy']==0) ? 'NA' : round($value1['steam_enthalpy'],2);?>
		          	</p>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['TTL_flow']) ? round($value1['TTL_flow'],3) : '0';?></p>
		        	</td> -->
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_mail_status']) ? ($value1['alert_mail_status']==1 ? 'YES' : 'NO') : 'NO';?></p>
		        	</td> -->
		      	</tr>

				<?php
					}
				}
				if(isset($typeWise[$key]['meter']) && count($typeWise[$key]['meter']) > 0){?>

				<?php foreach ($typeWise[$key]['meter'] as $key1 => $value1) {?>
		    	<tr role="row" class="odd">
		          	<td>
		          		<p><?php echo isset($value1['name']) ? $value1['name'] : '';?></p>
		        	</td>
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $key1;?>/pres"><?php echo isset($value1['P_pressure']) ?round($value1['P_pressure'],3) : '0';?></a></p>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_counter_pressure']) ? $value1['alert_counter_pressure'] : 0;?></p>
		        	</td> -->
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $key1;?>/temp"><?php echo isset($value1['T_temp']) ? round($value1['T_temp'],3) : 0;?></a></p>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_counter_temp']) ? $value1['alert_counter_temp'] : 0;?></p>
		        	</td> -->
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $key1;?>/flow"><?php echo isset($value1['flow']) ? round($value1['flow'],3) : '0';?></a></p>
		        	</td>
		        	<td>
		          	<p>

		          		<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $key1;?>/enthalpy"><?php echo isset($value1['steam_enthalpy']) ? round($value1['steam_enthalpy'],3) : '0';?></a>

		          	<?php //echo ($value1['steam_enthalpy']==0) ? 'NA' : round($value1['steam_enthalpy'],2);?>
		          	</p>
		        	</td>
		        	<!-- <td>
		          		<p><?php //echo isset($value1['TTL_flow']) ? round($value1['TTL_flow'],3) : '0';?></p>
		        	</td> -->
		        	<!-- <td>
		          		<p><?php //echo isset($value1['alert_mail_status']) ? ($value1['alert_mail_status']==1 ? 'YES' : 'NO') : 'NO';?></p>
		        	</td> -->
		      	</tr>
				<?php }?>
				<!-- Tempurary used -->
				<?php  if($key == 'gen'){?>
					<tr role="row" class="odd">
			          	<td>
			          		<p>TC1, TC2, SM270 (Boiler), SM120 (Boiler)</p>
			        	</td>
			        	<td colspan="4" style="text-align: center;">
			          		<p>Meters Not Connected</p>
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
		          	<p><!-- <?php 
		          		//echo (isset($typeWise[$key]['total']['P_pressure'])) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';
		          		?> --></p>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal/pres/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['P_pressure'])) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';?></a></p>

		        	</td>
		        	<!-- <td>
		          	<p><?php 
		          		//echo 'NA'
		          		?></p>
		        	</td> -->
		        	<td>
		          	<!-- <p><?php //echo (isset($typeWise[$key]['total']['T_temp'])) ? round($typeWise[$key]['total']['T_temp'],3) : '0';?></p> -->

		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal/temp/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['T_temp'])) ? round($typeWise[$key]['total']['T_temp'],3) : '0';?></a></p>

		        	</td>
		        	<!-- <td>
		          	<p><?php //echo 'NA';?></p>
		        	</td> -->
		        	<td>
		          	<!-- <p><?php //echo (isset($typeWise[$key]['total']['flow'])) ? round($typeWise[$key]['total']['flow'],3) : '0';?></p> -->
		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal/flow/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['flow'])) ? round($typeWise[$key]['total']['flow'],3) : '0';?></a></p>
		        	</td>
		        	<td>
		          	<!-- <p><?php //echo (isset($typeWise[$key]['total']['steam_enthalpy']) && $typeWise[$key]['total']['steam_enthalpy']!=0) ? round($typeWise[$key]['total']['steam_enthalpy'],3) : 'NA';?></p> -->
		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotal/enthalpy/<?php echo $key;?>"><?php echo (isset($typeWise[$key]['total']['steam_enthalpy']) && $typeWise[$key]['total']['steam_enthalpy']!=0) ? round($typeWise[$key]['total']['steam_enthalpy'],3) : 'NA';?></a></p>
		        	</td>
		        	<!-- <td>
		          	<p><?php //echo (isset($typeWise[$key]['total']['TTL_flow'])) ? round($typeWise[$key]['total']['TTL_flow'],3) : '0';?></p>
		        	</td> -->
		        	<!-- <td>
		          	<p><?php //echo 'NA';?></p>
		        	</td> -->
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
		          <p>Delta Pressure (Kg/cm<sup>2</sup>)</p>
		        </th>
		        <th style="">
		          <p>Delta Temperature (&#8451;)</p>
		        </th>
		        <th style="">
		          <p>Delta Flow (Ton/hr)</p>
		        </th>
		        <th style="">
		          <p>Delta Enthalpy (Kcal/Kg)</p>
		        </th>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php if(isset($typeWise[$key]['total']) && count($typeWise[$key]['total']) > 0){?>
		    	<tr role="row" class="even">
		          	
		        	<td>
		          	<p>
		          		<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotalGNvsDIST/pres">
		          		<?php 
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
		          		</a>		          			
		          	</p>
		        	</td>
		        	<td>
		          	<p>
		          		<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotalGNvsDIST/temp">
		          		<?php 
		          			if(isset($typeWise['gen']['total']['T_temp']) && isset($typeWise['dist']['total']['T_temp'])){
		          				echo round($typeWise['gen']['total']['T_temp'] - $typeWise['dist']['total']['T_temp'],3);
		          			}else{
		          				if(isset($typeWise['gen']['total']['T_temp'])){
		          					echo round($typeWise['gen']['total']['T_temp'],3);
		          				}else if(isset($typeWise['dist']['total']['T_temp'])){
		          					echo round($typeWise['dist']['total']['T_temp'],3);
		          				}else{
		          					echo '0';
		          				}
		          			}
		          		?>
		          		</a>
		          	</p>
		        	</td>
		        	<td>
		          	<p>
						<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotalGNvsDIST/flow">
		          		<?php 

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
		          		</a>		          		
		          	</p>
		        	</td>
		        	<td>
		          	<p>
		          	<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChartTotalGNvsDIST/enthalpy">
		          		<?php 

		          		if(isset($typeWise['gen']['total']['steam_enthalpy']) && isset($typeWise['dist']['total']['steam_enthalpy']) && $typeWise['gen']['total']['steam_enthalpy'] != 0 && $typeWise['dist']['total']['steam_enthalpy'] != 0){

		          			echo round($typeWise['gen']['total']['steam_enthalpy'] - $typeWise['dist']['total']['steam_enthalpy'],3);
		          		}else{
		          			echo '0';
		          		}

		          		?>
		          		</a>	
		          	</p>
		        	</td>
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
		          <p>Delta Pressure (Kg/cm<sup>2</sup>)</p>
		        </th>
		        <th style="">
		          <p>Delta Pressure Alert</p>
		        </th>
		        <th style="">
		          <p>Delta Temperature (&#8451;)</p>
		        </th>
		        <th style="">
		          <p>Delta Temperature Alert</p>
		        </th>
		        <th style="">
		          <p>Delta Enthalpy (Kcal/Kg)</p>
		        </th>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php if(isset($typeWise['dist']['meter'])  && count($typeWise['dist']['meter']) > 0){?>
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
		    						$viewPressure = "0";
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
		          	<p><a href="javascript:void(0);" data-toggle="tooltip" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $key2;?>/pres/<?php echo $viewPressure;?>/<?php echo isset($typeWise['gen']['total']['P_pressure']) ? $typeWise['gen']['total']['P_pressure'] : '0';?>/<?php echo isset($value2['benchmark_delta_pressure']) ? $value2['benchmark_delta_pressure'] : '0';?>" title="Baseline : <?php echo $value2['benchmark_delta_pressure'];?>"><?php echo $viewPressure;?></a> </p>
		        	</td>
		        	<td>
		        	<b><?php echo $viewPressureAlert;?></b>
		        	</td>
		        	<td>
		          	<p><a href="javascript:void(0);" data-toggle="tooltip" class="showChart" meter-link="<?php echo base_url();?>fm/dashboard/getMeterChart/<?php echo $key2;?>/temp/<?php echo $viewTemp;?>/<?php echo isset($typeWise['gen']['total']['T_temp']) ? $typeWise['gen']['total']['T_temp'] : '0';?>/<?php echo isset($value2['benchmark_delta_temp']) ? $value2['benchmark_delta_temp'] : '0';?>" title="Baseline : <?php echo $value2['benchmark_delta_temp'];?>"><?php echo $viewTemp;?></a> </p>
		        	</td>
		        	<td>
		        	<b><?php echo $viewTempAlert;?></b>
		        	</td>
		        	
		        	<td>
		          	<p><?php 


		          		if(isset($typeWise['gen']['total']['steam_enthalpy']) && $typeWise['gen']['total']['steam_enthalpy']!=0 && isset($value2['steam_enthalpy']) && $value2['steam_enthalpy']!=0){
		          			echo round($typeWise['gen']['total']['steam_enthalpy'] - $value2['steam_enthalpy'],3);
		          		}else{
		          			echo 'NA';
		          		}
		          	?></p>
		        	</td>
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


