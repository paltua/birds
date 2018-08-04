<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>resource/datatable/css/jquery.dataTables.css">
<script type="text/javascript" src="<?php echo base_url();?>resource/datatable/js/jquery.dataTables.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $('#allDataId').on('click','.ppChartShow', function() { 
            var meter_link = $(this).attr("meter-link");
            $('.myMeterModal').modal('show');
            $('.myMeterModal').find(".modal-content").load(meter_link);
        });

        var dataTable = $('#table_2').DataTable( {
	       /* oLanguage: {
		        sProcessing: "<img src='loading.gif'>"
		    },
    		processing : true,*/
	        /*dom: "frtiS",*/
			/*scrollY: 600,*/
			/*scroll off option above*/
			lengthChange: false,
			deferRender: true,
			bProcessing: true,
	    	iDisplayLength: 10,
	    	bPaginate: true,
	        
	        scroller: {
	            loadingIndicator: true
	        },
	        columnDefs: [ {
	            "targets": 'no-sort',
	            "orderable": false,
	        } ],
	        aaSorting: [],
	        /*fnDrawCallback: function () {
	            
	            var $api = this.api();
	            var pages = $api.page.info().pages;
	            var rows = $api.data().length;
	            if(pages == 1){
	                if(rows < 10){
	                    $("#data-table_paginate").hide();
	                }
	            }
	            if(rows == 0){
	                $("#data-table_paginate").hide();
	            }
	        },*/
		
        });
        
    });
</script>
<br>
<?php if(count($table[2]['data']) > 0 && $showData == 1){ ?>
<div class="panel panel-default">
    <div class="panel-heading">Shed Summary</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
				    <th rowspan="2" style="vertical-align: middle;text-align: center;">
				        SHED - 6
				    </th>
		      		
	      			<th style="">
			          	<p>Total CMPX Produce</p>
			        </th>
		      		<th style="">
			          	<p><!-- Compressed Air Flow(CFM) -->CFM Consumption</p>
			        </th>
			        <th style="">
			        	<p>Compressed Air Pressure</p>
			        </th>
			        <th>
			        	Total KWH Consumption
			        </th>	
			        <th style="">
			          	<p>H-Plant KWH Consumption<!-- Running KW --> <!-- KWH Consumption --></p>
			        </th>
			        <th style="">
			          	<p>Loom KWH Consumption<!-- Running KW --> <!-- KWH Consumption --></p>
			        </th>
					<th style="">
			          	<p>CFM/CMPX</p>
			        </th>
			        <th style="">
			          	<p>KWH/CMPX</p>
			        </th>
			    </tr>
			    <tr role="row">
		      		
	      			<td style="text-align: right;">
	      				<?php 
	      				$cmpx = 0;
	      				if(isset($table[1]['cmpx'][0]->cmpx)){ 
	      					$cmpx = $table[1]['cmpx'][0]->cmpx/WEAVING_CMPX;
	      				}
	      				?>
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getTotalCmpxProduceChartDay/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>" title="Total CMPX Produce Chart">
			          			<?php echo number_format((float)round($cmpx,2), 2, '.', '');?>
			          		</a>
			          	</p>
			        </td>
		      		<td style="text-align: right;">
			          	<?php 
			        	$sum_cfm = 0;
	      				if(isset($table[1]['cfm'][0]->cfm)){ $sum_cfm = $table[1]['cfm'][0]->cfm / WEAVING_CFM;}
	      				?>
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getCfmChartDay/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>" title="CFM Chart">
			          			<?php echo number_format((float)round($sum_cfm,2), 2, '.', '');?>
			          		</a>
			          	</p>
			        </td>
			        <td style="text-align: right;">
			          	<?php 
			        	$sum_press = 0;
	      				if(isset($table[1]['press'][0]->pressure)){ $sum_press = $table[1]['press'][0]->pressure ;}
	      				?>
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getCfmPressChartDay/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>" title="Compressed Air Pressure Chart">
			          			<?php echo number_format((float)round($sum_press,2), 2, '.', '');?>
			          		</a>
			          	</p>
			        </td>
			        <td style="text-align: right;">
			        	<?php 
			        	$sum_kw = 0;
	      				if(isset($table[1]['kw'])){ $sum_kw = $table[1]['kw'];}
	      				?>
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getRunningKwChartDay/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>/0" title="Runnng KW Chart">
				          		<?php echo number_format((float)round($sum_kw,2), 2, '.', '');?>
				          	</a>
			          	</p>
			        </td>
			        <td style="text-align: right;">
			        	<?php 
			        	$hplant_kw = 0;
	      				if(isset($table[1]['hplant_kw'])){ $hplant_kw = $table[1]['hplant_kw'];}
	      				?>
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getRunningKwChartDay/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>/1" title="Runnng H-plant KW Chart">
				          		<?php echo number_format((float)round($hplant_kw,2), 2, '.', '');?>
				          	</a>
			          	</p>
			        </td>
			        <td style="text-align: right;">
			        	<?php 
			        	$loom_kw = 0;
	      				if(isset($table[1]['loom_kw'])){ $loom_kw = $table[1]['loom_kw'];}
	      				?>
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getRunningKwChartDay/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>/2" title="Runnng Loom KW Chart">
				          		<?php echo number_format((float)round($loom_kw,2), 2, '.', '');?>
				          	</a>
			          	</p>
			        </td>
					<td style="text-align: right;">
			          	<p>
			          		<?php if($dayCount == 1){?>
				          		<?php 
			      				if($cmpx != 0){
			      				 	echo number_format((float)round($sum_cfm/$cmpx,2), 2, '.', '');
			      				}else{
			      					echo 'Undefined';
			      				}?>
			          		<?php }else{?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getCfmCmpxChartDay/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>" title="CFM/CMPX Chart">
			          		<?php 
		      				if($cmpx != 0){
		      				 	echo number_format((float)round($sum_cfm/$cmpx,2), 2, '.', '');
		      				}else{
		      					echo 'Undefined';
		      				}?>
		      				</a>
		      				<?php }?>
		      			</p>
			        </td>
			        <td style="text-align: right;">
			          	<p>
			          		<?php if($dayCount == 1){?>
			          			<?php 
			      				if($cmpx != 0){
			      				 	echo number_format((float)round($sum_kw/$cmpx,2), 2, '.', '');
			      				}else{
			      					echo 'Undefined';
			      				}?>
			          		<?php }else{?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getKwCmpxChartDay/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>" title="KW/CMPX Chart">
				          		<?php 
			      				if($cmpx != 0){
			      				 	echo number_format((float)round($sum_kw/$cmpx,2), 2, '.', '');
			      				}else{
			      					echo 'Undefined';
			      				}?>
			      			</a>
			      			<?php }?>
	      				</p>
			        </td>
			    </tr>
		    </thead>  
		    
		</table>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Loom & Style Wise Production Details</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer display" id="table_2" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
				    <th style="">
				        <p>Loom No.</p>
				    </th>
		      		
	      			<th style="">
			          	<p>Style ID</p>
			        </th>
			        <th style="">
			          	<p>Style Name</p>
			        </th>
		      		<th style="">
			          	<p>Efficiency</p>
			        </th>
			        <th style="">
			          	<p>RPM</p>
			        </th>
			        <!-- <th style="">
			          	<p>PICKS</p>
			        </th> -->
					<th style="">
			          	<p>CMPX</p>
			        </th>
			        <!-- <th class="no-sort">
			          	<p>Stoppage Details</p>
			        </th> -->
			    </tr>
		    </thead>  
		    <tbody> 
		    	<?php if(count($table[2]['data']) > 0){ 
		    		$cmpxSum = 0;
		    		foreach($table[2]['data'] as $val){
		    			//$cmpxSum = $cmpxSum + ($val->picks/WEAVING_CMPX);
		    	?>
		    	<tr role="row">
				    <td style="">
				        <p><?php echo $val->machine_id;?></p>
				    </td>
		      		
	      			<td style="">
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getStyleDetails/<?php echo $val->style_id;?>" title="Style Details">
			          			<?php echo $val->style_id;?>
			          		</a>
			          	</p>
			        </td>
			        <td style="">
			        	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getStyleDetails/<?php echo $val->style_id;?>" title="Style Details">
			          			<?php echo $val->style;?>
			          		</a>
			          	</p>
			        </td>
		      		<td style="text-align: right;">
		      			<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getLoomStyleWiseEffRpmChartDay/<?php echo $val->machine_id;?>/<?php echo $val->style_id;?>/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>/Efficiency" title="Efficiency Chart">
			          			<?php echo number_format((float)round($val->eff,2), 2, '.', '');?>
			          		</a>
			          	</p>
			        </td>
			        <td style="text-align: right;">
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getLoomStyleWiseEffRpmChartDay/<?php echo $val->machine_id;?>/<?php echo $val->style_id;?>/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>/RPM" title="RPM Chart">
			          			<?php echo number_format((float)round($val->rpm,2), 2, '.', '');?>
			          		</a>
			          	</p>
			        </td>
			        <!-- <td style="text-align: right;">
			          	<p>
			          		
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getLoomProductionCmpxChartShift/<?php echo $val->machine_id;?>/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>/1" title="PICKS Chart">	
			          		<?php echo number_format((float)round($val->last_picks - $val->first_picks,2), 2, '.', '');?>
			          		</a>
			          	</p>
			        </td>  -->
					<td style="text-align: right;">
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/report/getLoomStyleCmpxChartDay/<?php echo $val->machine_id;?>/<?php echo $val->style_id;?>/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>/0" title="CMPX Chart">
			          			<?php echo number_format((float)round((($val->cmpx)/WEAVING_CMPX), 4), 4, '.', '');?>
			          		</a>	
			          	</p>
			        </td>
			        <!-- <td style="">
			          	<p class="text-center" style="margin: 0 0 0px;">
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getStoppageDetails/<?php echo $val->machine_id;?>/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>" title="Stoppage Details">
			          			<img style="width: 32px;" src="<?php echo base_url();?>resource/logo/stop.png">
			          		</a>
			          	</p>
			        </td> -->
			    </tr>
			    <?php } } ?>
		  	</tbody>
		</table>
    </div>
</div>

<?php //echo "<strong>Alert : Please note that some of the meter data have shown inconsistency and hence all indicators are not being displayed. Kindly speak to engineering/relevant department.</strong>";
?>
<?php }elseif(count($table[2]['data']) == 0 && $showData == 1){?>
<div class="panel panel-default">
    <div class="panel-heading">Report Tables</div>
    	<div class="panel-body">
			<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
			    <thead>
			      	<tr role="row">
					    <td>No Records Found</td>
				    </tr>
			    </thead> 
			</table>
		</div>
	</div>
</div>

<?php }elseif($showData == 0){ ?>
<div class="panel panel-default">
    <div class="panel-heading">Report Tables</div>
    	<div class="panel-body">
			<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
			    <thead>
			      	<tr role="row">
					    <td>Please select Date range to see the Records</td>
				    </tr>
			    </thead> 
			</table>
		</div>
	</div>
</div>
<?php } ?>

