
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
    <div class="panel-heading">GAP</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
		      		<th style="">
		          		<p></p>
		      		</th>
		  			<th style="">
			          <p>Loss Value</p>
			        </th>
			        <th style="">
			          <p>Loss %</p>
			        </th>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php 
		    		if(count($gap) > 0){
		    			foreach ($gap as $key => $value) {
		    	?>			
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p><?php echo $value['name'];?></p>
			      	</th>
		    		<td>
		    			<p>
		    				<?php if($value['chart_link_abs'] != ''){?>
		    				<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?><?php echo $value['chart_link_abs'];?>">
			          		<?php echo number_format((float)round($value['loss_val'],2), 2, '.', '');?>
			          		</a>
		          		<?php }else{?>
		          		<?php echo number_format((float)round($value['loss_val'],2), 2, '.', '');?>
		          		<?php }?>
		          		</p>
		          	</td>
		          	<td>
		          		<p>
		          			<?php if($value['chart_link_per'] != ''){?>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?><?php echo $value['chart_link_per'];?>">
		          			<?php echo number_format((float)round($value['loss_per'],2), 2, '.', '');?>
		          			</a>
		          			<?php }else{?>
		          			<?php echo $value['loss_per'];?>
		          			<?php }?>
		          		</p>
		          	</td>
		    	</tr>
		    	<?php }}?>
		  	</tbody>
		</table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Electricity </div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
		      		<th style="">
		          		<p></p>
		      		</th>
		  			<th style="">
			          <p>Last 15 Minutes (KW)</p>
			        </th>
			        <th style="">
			          <p>Average (KW) of this Month</p>
			        </th>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php 
		    		$tempData_15 = array();
		    		$tempData_avg = array();
		    		if(count($electricity) > 0){
		    		foreach ($electricity as $key => $value) {
		    			$tempData_15[] = $value['kw_15'];
						$tempData_avg[] = $value['kw_avg_month'];
		    	?>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p><?php echo $value['name'];?></p>
			      	</th>
		    		<td>
		          		<p>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?><?php echo $value['chart_link_15'];?>">
		          				<?php echo number_format((float)round($value['kw_15'],2), 2, '.', '');?>
		          			</a>
		          		</p>
		          	</td>
		          	<td>
		          		<p>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?><?php echo $value['chart_link_month'];?>">
		          				<?php echo number_format((float)round($value['kw_avg_month'],2), 2, '.', '');?>
		          			</a>
		          		</p>
		          	</td>
		    	</tr>
		    	<?php }} ?>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Loss</p>
			      	</th>
		    		<td>
		          		<p><?php echo number_format((float)round(($tempData_15[0] - $tempData_15[1]),2), 2, '.', '');?></p>
		          	</td>
		          	<td>
		          		<p><?php echo number_format((float)round(($tempData_avg[0] - $tempData_avg[1]),2), 2, '.', '');?></p>
		          	</td>
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Steam </div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
		      		<th style="">
		          		<p></p>
		      		</th>
		  			<th style="">
			          <p>Last 15 Minutes (T/Hr)</p>
			        </th>
			        <th style="">
			          <p>Average (T/Hr) of this Month</p>
			        </th>
			        <th style="">
			          <p>Last 15 Minutes (Pressure)</p>
			        </th>
			        <th style="">
			          <p>Last 15 Minutes (Temperature)</p>
			        </th>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<?php 
		    		$tempData_flow_15 = array();
		    		$tempData_flow_avg = array();
		    		$tempData_pres_15 = array();
		    		$tempData_pres_avg = array();
		    		if(count($steam) > 0){
		    		foreach ($steam as $key => $value) {
		    			$tempData_flow_15[] = $value['flow_last_15'];
						$tempData_flow_avg[] = $value['flow_avg'];
						$tempData_pres_15[] = $value['pres_last_15'];
						$tempData_temp_15[] = $value['temp_last_15'];
		    	?>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p><?php echo $value['name'];?></p>
			      	</th>
		    		<td>
		          		<p>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?><?php echo $value['flow_last_15_url'];?>">
		          			<?php echo number_format((float)round($value['flow_last_15'],2), 2, '.', '');?>
		          			</a>
		          		</p>
		          	</td>
		          	<td>
		          		<p>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?><?php echo $value['flow_avg_url'];?>">
		          				<?php echo number_format((float)round($value['flow_avg'],2), 2, '.', '');?>
		          			</a>
		          		</p>
		          	</td>
		          	<td>
		          		<p>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?><?php echo $value['pres_last_15_url'];?>">
		          				<?php echo number_format((float)round($value['pres_last_15'],2), 2, '.', '');?>
		          			</a>
		          		</p>
		          	</td>
		          	<td>
		          		<p>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?><?php echo $value['temp_last_15_url'];?>">
		          				<?php echo number_format((float)round($value['temp_last_15'],2), 2, '.', '');?>
		          			</a>
		          		</p>
		          	</td>
		    	</tr>
		    	<?php }} ?>
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Loss</p>
			      	</th>
		    		<td>
		          		<p><?php echo number_format((float)round(($tempData_flow_15[0] - $tempData_flow_15[1]),2), 2, '.', '');?></p>
		          	</td>
		          	<td>
		          		<p><?php echo number_format((float)round(($tempData_flow_avg[0] - $tempData_flow_avg[1]),2), 2, '.', '');?></p>
		          	</td>
		          	<td>
		          		<p><?php echo number_format((float)round(($tempData_pres_15[0] - $tempData_pres_15[1]),2), 2, '.', '');?></p>
		          	</td>
		          	<td>
		          		<p><?php echo number_format((float)round(($tempData_temp_15[0] - $tempData_temp_15[1]),2), 2, '.', '');?></p>
		          	</td>
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Compressed Air (Date & Time)</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
		      		<th style="">
		          		<p></p>
		      		</th>
		  			<th style="">
			          <p>Last 15 Minutes (Pressure)</p>
			        </th>
			        <th style="">
			          <p>Last 15 Minutes ( CFM Flow )</p>
			        </th>
		    </tr>
		    </thead>  
		    <tbody> 
		    	<tr role="row" class="odd">
		    		<th style="">
			          <p>Turbo Compressor Air</p>
			      	</th>
		    		<td>
		          		<p>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart_CPPForDoubleGraph/1/pres">
			          			<?php $pre = isset($compressAirData[0]->pressure)?$compressAirData[0]->pressure:0;?>
			          			<?php echo number_format((float)round($pre,2), 2, '.', '');?>
		          			</a>
		          		</p>
		          	</td>
		          	<td>
		          		<p>
		          			<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>air/dashboard/getMeterChart_CPPForDoubleGraph/1/flow">
		          			<?php $flow = isset($compressAirData[0]->flow)?$compressAirData[0]->flow:0;?>
		          			<?php echo number_format((float)round($flow * 10,2), 2, '.', '');?>
		          			</a>
		          		</p>
		          	</td>
		    	</tr>
		  	</tbody>
		</table>
    </div>
</div>





<?php //echo "<strong>Alert : Please note that some of the meter data have shown inconsistency and hence all indicators are not being displayed. Kindly speak to engineering/relevant department.</strong>";
?>