
<?php 

$countDays = 0;
if(isset($dateRange["selectedDate"]) && $dateRange["selectedDate"]!='' && isset($dateRange["selectedDateEnd"]) && $dateRange["selectedDateEnd"]!=''){
	$selectedStartDate = strtotime($dateRange["selectedDate"]);
	$selectedEndDate = strtotime($dateRange["selectedDateEnd"]);
	$datediff = $selectedEndDate - $selectedStartDate;
	$countDays = floor($datediff / (60 * 60 * 24)+1);
}

function _dateAdd($dataTimeInterval,$date){
    $date = new DateTime($date);
    $date->add(new DateInterval($dataTimeInterval));
    return $date->format('Y-m-d H:i:s');
}

if(count($result_cpp) > 0){
	
	//echo "<pre>";
	//var_dump($result_cpp);


	?>

<div class="panel panel-default">
    <div class="panel-heading">Daywise Aggregated Stats :- <strong>(CPP)</strong></div>
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
		    		
		    		if(isset($result_cpp) && count($result_cpp)>0){
		    			foreach ($result_cpp as $cppTypekey => $cppTypevalue) {
		    				foreach ($cppTypevalue['meter'] as $cppMkey => $cppMvalue) {
		    					if($cppMkey==2) continue;
		    					if($cppMkey==3) continue;
		    					if($cppMkey==4) continue;
		    					
		    	?>
		    	<tr>
		    		<td><?php echo $cppMvalue['name'];?></td>

		    		<td>

		    			<!-- <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDay_CPP/<?php echo $cppMkey;?>/pres/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"> -->

		    				<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDay_CPPforDoubleGraph/<?php echo $cppMkey;?>/pres/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>/flow">

		    				<?php echo (isset($cppMvalue['pressure']) && $cppMvalue['pressure']!=0) ? number_format($cppMvalue['pressure'],3) : '--';?>
		    			</a>
		    		</td>
		    		<td>
		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDay_CPP/<?php echo $cppMkey;?>/temp/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>">
		    				<?php echo (isset($cppMvalue['temp']) && $cppMvalue['temp']!=0) ? number_format($cppMvalue['temp'],3) : '--';?>
		    			</a>
		    		</td>
		    		<td>
		    			<!-- <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDay_CPP/<?php echo $cppMkey;?>/flow/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>">
		    				<?php echo (isset($cppMvalue['flow']) && $cppMvalue['flow']!=0) ? number_format($cppMvalue['flow'],3) : '--';?>
		    			</a> -->

		    			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDay_CPPforDoubleGraph/<?php echo $cppMkey;?>/flow/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>/pres">
		    				<?php echo (isset($cppMvalue['flow']) && $cppMvalue['flow']!=0) ? number_format($cppMvalue['flow'],3) : '--';?>
		    			</a>
		    		</td>
		    		
		    	</tr>
		    	<?php
		    				}
		    			}
		    	?>

		    	
		    	<?php
		    		}
		    	?>

		    </tbody>
		</table>
    </div>
</div>
<?php }?>

<?php 
if(count($typeWise) > 0){?>
<?php foreach ($typeWise as $key => $value) {
if($key=="main") continue;
	?>
<div class="panel panel-default">
    <div class="panel-heading"> Daywise aggregated Stats:- <?php echo ($key == 'gen')?'Generation':'Distribution';?> <strong>(WIL)</strong></div>
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
		        <th style="">
		          <p>Temperature (&#8451;)</p>
		        </th>
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
		          <p>Enthalpy (Kcal/Kg)</p>
		        </th> -->
		         <th style="">
		          <p><?php echo ($key == 'gen')?'Generation':'Consumption';?> (CFM)</p>
		        </th>
		    </tr>
		    </thead>  
		    <tbody> 
		    	
				<?php if(isset($typeWise[$key]['meter']) && count($typeWise[$key]['meter']) > 0){?>

				<?php 
				$totaliserTotal = 0;
				foreach ($typeWise[$key]['meter'] as $key1 => $value1) {?>
		    	<tr role="row" class="odd">
		          	<td>
		          		<p><?php echo isset($value1['name']) ? $value1['name'] : '';?></p>
		        	</td>
		        	<?php if($key == 'gen'){?>
		        	<td>
		        		<?php //echo $last15DataSet_KW[$key1][0];?>
		          		 <p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>ems/report/showGraphDay_Air/<?php echo $last15DataSet_KW[$key1][1];?>/KW/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime(_dateAdd('PT24H00M00S',$dateRange['selectedDateEnd']));?>/<?php echo $key1;?>"><?php echo $last15DataSet_KW[$key1][0];?></a></p> 

		        	</td>
		        	<?php } ?>
		        	<td>
		          		<!-- <p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDay/<?php echo $key1;?>/pres/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"><?php echo (isset($value1['P_pressure']) && $value1['P_pressure']!=0) ? round($value1['P_pressure'],3) : 'DNA';?></a></p> -->

		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayForDoubleGraph/<?php echo $key1;?>/pres/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>/flow"/><?php echo (isset($value1['P_pressure']) && $value1['P_pressure']!=0) ? round($value1['P_pressure'],3) : 'DNA';?></a></p>
		        	</td>
		        	<td>
		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDay/<?php echo $key1;?>/temp/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"><?php echo (isset($value1['T_temp']) && $value1['T_temp']!=0) ? round($value1['T_temp'],3) : 'DNA';?></a></p>
		        	</td>
		        	<td>
		          		<!-- <p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDay/<?php echo $key1;?>/flow/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"><?php echo (isset($value1['flow']) && $value1['flow']!='') ? round($value1['flow'],3) : 'DNA';?></a></p> -->

		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayForDoubleGraph/<?php echo $key1;?>/flow/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>/pres"><?php echo (isset($value1['flow']) && $value1['flow']!='') ? round($value1['flow'],3) : 'DNA';?></a></p>
		        	</td>

		        	<?php if($key == 'gen'){?>
		        	<td>
		          		<p>
		          			<?php //echo $cuFlow = isset($value1['flow']) ? round($value1['flow'],3) : '0';?>
		          			


		          			 <!-- <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChart_forKWCFM/<?php echo $key1;?>/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"><?php echo ($value1['flow']>0) ? number_format(($last15DataSet_KW[$key1][0] / $value1['flow']),3) : '0'; ?></a> -->

		          			 <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChart_forKWCFM_temp/<?php echo $key1;?>/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"><?php echo ($last15DataSet_KWsumproductCFM[$key1][0]>0) ? number_format(($last15DataSet_KWsumproductCFM[$key1][0]),3) : 'DNA';//echo ($value1['flow']>0) ? number_format(($last15DataSet_KW[$key1][0] / $value1['flow']),3) : '0'; ?></a>

		          		</p>
		          	</td>
		          	<?php } ?>
		          	<?php if($key == 'gen'){?>
		        	<td>
		          		<p>
		          			
		          			<?php 
		          				if(isset($value1['cfm_cu'])){
	          			 			if($value1['cfm_cu'] > 110){
	          			 				echo $shoCuError = 'Capacity above 110%. Check Data';
	          			 			}else{
	          			 	?>
	          			 			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayFORCFMCU_percent/<?php echo $key1;?>/flow/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>">
		          			 			<?php echo round($value1['cfm_cu'],2);?>
		          			 		</a>
	          			 	<?php	
	          			 			}
	          			 		}else{
	          			 			echo $shoCuError = 'NA';
	          			 		}
		          			 ?>
		          		</p>
		          	</td>
		          	<?php } ?>
		        	<!-- <td>
		          	<p>
		          		<a href="javascript:void(0);" class="showChart" meter-link="<?php //echo base_url();?>fm/report/getMeterChart/<?php //echo $key1;?>/enthalpy"><?php //echo isset($value1['steam_enthalpy']) ? round($value1['steam_enthalpy'],3) : '0';?></a>
		          	<?php //echo ($value1['steam_enthalpy']==0) ? 'NA' : round($value1['steam_enthalpy'],2);?></p>
		        	</td> -->
		        	<td>

		        		<?php		          			
		        			//echo $TTL_flow_new[$key][$key1]['TTL_flow'];
		          			$TTL_flowVal = isset($TTL_flow_new[$key][$key1]['TTL_flow']) ? round($TTL_flow_new[$key][$key1]['TTL_flow'],3) : '0';
		          			if($TTL_flowVal>0){
		          				$TTL_flowVal = round((($TTL_flowVal)/1440 / $countDays),3);
			          			$totaliserTotal+= $TTL_flowVal;

			          			if(isset($value1['flow']) && isset($TTL_flowVal) && ($TTL_flowVal>=($value1['flow']*0.9)) && ($TTL_flowVal<=($value1['flow']*1.1))){
			          				
			          				//echo $TTL_flowVal;
			          			?>
			          				<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayForTotaliser/<?php echo $key1;?>/totaliser/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"><?php echo $TTL_flowVal;?></a>
			          			<?php
			          			}else{
			          				//echo "<a href='' data-toggle='tooltip' style='color:red;' title='Possible error, as the totaliser value does not lie between +- 10% of the average flow value'>".$TTL_flowVal."</a>";
			          			?>
			          				<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayForTotaliser/<?php echo $key1;?>/totaliser/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>" data-toggle="tooltip" style="color:red;" title="Possible error, as the totaliser value does n't lie between +- 10% of the average flow value"><?php echo $TTL_flowVal;?></a>
			          			<?php
			          			}
			          		}else{
			          			echo 'DNA';
			          		}
		          			
		          	

		          		?>

		          		<!-- <p title="<?php echo isset($value1['TTL_flow']) ? round($value1['TTL_flow'],3) : '0';?>">

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
		          			
		          		</p> -->
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

			          			<?php //echo $last15DataSet_KW[$keyNcm][0];?>
			          			
			          			<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>ems/report/showGraphDay_Air/<?php echo $last15DataSet_KW[$keyNcm][1];?>/KW/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime(_dateAdd('PT24H00M00S',$dateRange['selectedDateEnd']));?>/<?php echo $keyNcm;?>"><?php echo $last15DataSet_KW[$keyNcm][0];?></a>

			          			<?php }?></p>
			        	</td>
			        	<td colspan="6" style="text-align: center;">
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
		          		
		          		<?php //echo (isset($compRelKW_total)) ? round($compRelKW_total,3) : '0';?>

		          		<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartKWTotal/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"><?php echo (isset($compRelKW_total)) ? round($compRelKW_total,3) : '0';?></a></p>

		        	</td>
		        	<?php }?>

		        	<td>
		          	<!-- <p><?php //echo isset($typeWise[$key]['total']['P_pressure']) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';?></p> -->

		          	<?php 
		          		//var_dump($totPressure_CPP);
		          	?>
		          	<!-- <p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayTotal/pres/<?php echo $key;?>/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"><?php echo isset($typeWise[$key]['total']['P_pressure']) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';?></a></p> -->

		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayTotalForDoubleGraph/pres/<?php echo $key;?>/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>/flow"><?php echo isset($typeWise[$key]['total']['P_pressure']) ? round($typeWise[$key]['total']['P_pressure'],3) : '0';?></a></p>


		        	</td>
		        	<td>
		          	<!-- <p><?php echo isset($typeWise[$key]['total']['T_temp']) ? round($typeWise[$key]['total']['T_temp'],3) : '0';?></p> -->

		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayTotal/temp/<?php echo $key;?>/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"><?php echo isset($typeWise[$key]['total']['T_temp']) ? round($typeWise[$key]['total']['T_temp'],3) : '0';?></a></p>

		        	</td>
		        	<td>
		          	<!-- <p><?php //echo isset($typeWise[$key]['total']['flow']) ? round($typeWise[$key]['total']['flow'],3) : '0';?></p> -->

		          	<!-- <p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayTotal/flow/<?php echo $key;?>/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"><?php echo isset($typeWise[$key]['total']['flow']) ? round($typeWise[$key]['total']['flow'],3) : '0';?></a></p> -->

		          	<p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayTotalForDoubleGraph/flow/<?php echo $key;?>/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>/pres"><?php echo isset($typeWise[$key]['total']['flow']) ? round($typeWise[$key]['total']['flow'],3) : '0';?></a></p>

		        	</td>

		        	<?php if($key == 'gen'){?>
		        	<td>NA</td>
		        	<?php }?>
		        	<?php if($key == 'gen'){?>
		        	<td>NA</td>
		        	<?php }?>

		        	<!-- <td>
		          	<p><?php //echo (isset($typeWise[$key]['total']['T_temp']) && $typeWise[$key]['total']['steam_enthalpy']!=0) ? round($typeWise[$key]['total']['steam_enthalpy'],3) : 'NA';?></p>
		        	</td>-->
		        	 <td> 
			          	<!-- <p title="<?php echo isset($typeWise[$key]['total']['TTL_flow']) ? round($typeWise[$key]['total']['TTL_flow'],3) : '0';?>">

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
			          	</p> -->
			          	<?php		          		
			          		if(isset($totaliserTotal) && $totaliserTotal>0){
			          			if(isset($typeWise[$key]['total']['flow']) && isset($totaliserTotal) && ($totaliserTotal>=($typeWise[$key]['total']['flow']*0.9)) && ($totaliserTotal<=($typeWise[$key]['total']['flow']*1.1))){
			          				echo $totaliserTotal;
			          			}else{
			          				echo "<span data-toggle='tooltip' style='color:red;' title='Possible error, as the totaliser value does not lie between +- 10% of the average flow value'>".$totaliserTotal."</span>";
			          				//echo "<a href='' data-toggle='tooltip' style='color:red;' title='Possible error, as the totaliser value does not lie between +- 10% of the average flow value'>".$totaliserTotal."</a>";
			          			}
			          		}else{
			          			echo 'DNA';
			          		}
		          		?>
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
		        <!-- <th style="">
		          <p>Generation - Distribution (CFM)</p>
		        </th> -->
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php if(isset($typeWise[$key]['total']) && count($typeWise[$key]['total']) > 0){?>
		    	<tr role="row" class="even">
		          	
		        	<td>
		        		<!-- <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayTotalGNvsDIST/pres/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"> -->

		        		<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayTotalGNvsDISTForDoubleGraph/pres/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>/flow">
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
		        		<!-- <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayTotalGNvsDIST/flow/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>"> -->

		        		<a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayTotalGNvsDISTForDoubleGraph/flow/<?php echo strtotime($dateRange['selectedDate']);?>/<?php echo strtotime($dateRange['selectedDateEnd']);?>/pres">
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
		          			/*if(isset($typeWise['gen']['total']['steam_enthalpy']) && $typeWise['gen']['total']['steam_enthalpy']!=0 && isset($typeWise['dist']['total']['steam_enthalpy']) && $typeWise['dist']['total']['steam_enthalpy']!=0){
		          				echo round($typeWise['gen']['total']['steam_enthalpy'] - $typeWise['dist']['total']['steam_enthalpy'],3);
		          			}else{
		          				echo 'NA';
		          			}*/

		          		?></p>
		        	</td> -->
		        	<!-- <td>
		        		<?php 
		        			if(isset($typeWise['gen']['total']['TTL_flow']) && $typeWise['gen']['total']['TTL_flow']!=0 && isset($typeWise['dist']['total']['TTL_flow']) && $typeWise['dist']['total']['TTL_flow']!=0){
		        				echo ($typeWise['gen']['total']['TTL_flow'] - $typeWise['dist']['total']['TTL_flow']);
		        			}else{
		        				echo 'NA';
		        			}
		        		?>
		        	</td> -->
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
		    			if(isset($typeWise['gen']['total']['P_pressure']) && isset($value2['P_pressure']) && $value2['P_pressure']>0 && $typeWise['gen']['total']['P_pressure']>0){
		    				$viewPressure = round($typeWise['gen']['total']['P_pressure'] - $value2['P_pressure'],3);
		    			}else{
		    				/*if(isset($typeWise['gen']['total']['P_pressure'])){
		    					$viewPressure = round($typeWise['gen']['total']['P_pressure'],3);
		    				}else if(isset($value2['P_pressure'])){
		    					$viewPressure = round($value2['P_pressure'],3);
		    				}else{
		    					$viewPressure = 0;
		    				}*/
		    				$viewPressure = 'DNA';
		    			}
		    			
		    			$viewPressureAlert = "";
		    			if(isset($viewPressure) && isset($value2['benchmark_delta_pressure']) && $viewPressure!='DNA'){
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
		          	<p><a href="javascript:void(0);" data-toggle="tooltip" class="showChart" meter-link="<?php echo base_url();?>air/report/getMeterChartAllDayCon/<?php echo $key2;?>/pres/<?php echo $viewPressure;?>/<?php echo isset($typeWise['gen']['total']['P_pressure']) ? $typeWise['gen']['total']['P_pressure'] : '0';?>/<?php echo isset($value2['benchmark_delta_pressure']) ? $value2['benchmark_delta_pressure'] : '0';?>/<?php echo strtotime($dateRange['selectedDate']);?>" title="Baseline : <?php echo $value2['benchmark_delta_pressure'];?>"><?php echo $viewPressure;?></a> </p>
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
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>