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

  
<div class="panel panel-info">
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
			          	<p>Compressed Air Flow(CFM)</p>
			        </th>
			        <th style="">
			          	<p>Compressed Air Pressure</p>
			        </th>
			        <th style="">
			          	<p>H-Plant KW </p>
			        </th>
			        <th style="">
			          	<p>Loom KW </p>
			        </th>
			        <th style="">
			          	<p>Total KW <!-- KWH Consumption --></p>
			        </th>
					<!-- <th style="">
			          	<p>CFM/CMPX</p>
			        </th>
			        <th style="">
			          	<p>KWH/CMPX</p>
			        </th> -->
			    </tr>
			    <tr role="row">
		      		
	      			<td style="">
	      				<?php 
	      				$cmpx = 0;
	      				if(isset($table[1]['cmpx'][0]->sum_cmpx)){ 
	      					$cmpx = $table[1]['cmpx'][0]->sum_cmpx / WEAVING_CMPX;
	      				}
	      				?>
			          	<p>
			          		<?php if($cmpx != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getTotalCmpxProduceChart/<?php echo strtotime($endDateShow);?>" title="Total CMPX Produce Graph">
			          			<?php echo number_format((float)round($cmpx,2), 2, '.', '');?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
		      		<td style="">
			          	<?php 
			        	$sum_flow = 0;
	      				if(isset($table[1]['cfm'][0]->flow)){ $sum_flow = $table[1]['cfm'][0]->flow;}
	      				?>
			          	<p>
			          		<?php if($sum_flow != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getCfmChart/<?php echo strtotime($startDateShow);?>/flow" title="CFM Graph">
			          			<?php echo number_format((float)round($sum_flow,2), 2, '.', '');?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
			        <td style="">
			          	<?php 
			        	$sum_pressure = 0;
	      				if(isset($table[1]['cfm'][0]->p_pressure)){ $sum_pressure = $table[1]['cfm'][0]->p_pressure;}
	      				?>
			          	<p>
			          		<?php if($sum_pressure != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getCfmChart/<?php echo strtotime($startDateShow);?>/pres" title="Pressure Graph">
			          			<?php echo number_format((float)round($sum_pressure,2), 2, '.', '');?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
			        <td style="">
			        	<?php 
			        	$sum_kw = 0;
	      				if(isset($table[1]['kw'][0]->sum_kw)){ $sum_kw = $table[1]['kw_hplant'][0]->sum_kw ;}
	      				?>
			          	<p>
			          		<?php if($sum_kw != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getRunningKwChart/<?php echo strtotime($startDateShow);?>/1" title="Runnng H-Plant KW Graph">
				          		<?php echo number_format((float)round($sum_kw,2), 2, '.', '');?>
				          	</a>
				          	<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
			        <td style="">
			        	<?php 
			        	$sum_kw = 0;
	      				if(isset($table[1]['kw_hplant'][0]->sum_kw)){ $sum_kw = $table[1]['kw_loom'][0]->sum_kw ;}
	      				?>
			          	<p>
			          		<?php if($sum_kw != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getRunningKwChart/<?php echo strtotime($startDateShow);?>/2" title="Runnng Loom KW Graph">
				          		<?php echo number_format((float)round($sum_kw,2), 2, '.', '');?>
				          	</a>
				          	<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
			        <td style="">
			        	<?php 
			        	$sum_kw = 0;
	      				if(isset($table[1]['kw_loom'][0]->sum_kw)){ $sum_kw = $table[1]['kw'][0]->sum_kw ;}
	      				?>
			          	<p>
			          		<?php if($sum_kw != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getRunningKwChart/<?php echo strtotime($startDateShow);?>/0" title="Runnng Total KW Graph">
				          		<?php echo number_format((float)round($sum_kw,2), 2, '.', '');?>
				          	</a>
				          	<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
					<!-- <td style="">
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getCfmCmpxChart/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>" title="CFM/CMPX Chart">
			          		<?php 
		      				if($cmpx != 0){
		      				 	echo number_format((float)round($sum_flow/$cmpx,2), 2, '.', '');
		      				}else{
		      					echo 'Undefined';
		      				}?>
		      				</a>
		      			</p>
			        </td>
			        <td style="">
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getKwCmpxChart/<?php echo strtotime($startDateShow);?>/<?php echo strtotime($endDateShow);?>" title="KW/CMPX Chart">
				          		<?php 
			      				if($cmpx != 0){
			      				 	echo number_format((float)round($sum_kw/$cmpx,2), 2, '.', '');
			      				}else{
			      					echo 'Undefined';
			      				}?>
			      			</a>
	      				</p>
			        </td> -->
			    </tr>
		    </thead>  
		    
		</table>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">Loom Wise Production Details</div>
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
			        <th style="">
			          	<p>PICKS</p>
			        </th>
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
		    		foreach($table[2]['data'] as $val){
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
		      		<td style="">
		      			<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getLoomProductionEffChart/<?php echo $val->machine_id;?>/<?php echo strtotime($endDateShow);?>" title="Efficiency Chart">
			          			<?php echo number_format((float)round($val->eff,2), 2, '.', '');?>
			          		</a>
			          	</p>
			        </td>
			        <td style="">
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getLoomProductionRpmChart/<?php echo $val->machine_id;?>/<?php echo strtotime($endDateShow);?>" title="RPM Chart">
			          			<?php echo number_format((float)round($val->rpm,2), 2, '.', '');?>
			          		</a>
			          	</p>
			        </td>
			        <td style="">
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getLoomProductionPicksChart/<?php echo $val->machine_id;?>/<?php echo strtotime($endDateShow);?>" title="PICKS Chart">
			          		<!-- <a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getLoomProductionCmpxChart/<?php echo $val->machine_id;?>/<?php echo strtotime($endDateShow);?>" title="CMPX Chart"> -->	
			          			<?php if($val->cmpx < 0){?>
			          				N/A
			          			<?php }else{?>
			          		<?php echo number_format((float)round($val->cmpx,2), 2, '.', '');?>
			          		<?php } ?>
			          		</a>
			          	</p>
			        </td>
					<td style="">
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getLoomProductionCmpxChart/<?php echo $val->machine_id;?>/<?php echo strtotime($endDateShow);?>" title="CMPX Chart">
			          			<?php if($val->cmpx < 0){?>
			          			N/A
			          			<?php }else{?>
			          			<?php echo number_format((float)round(($val->cmpx)/WEAVING_CMPX, 4), 4, '.', '');?>
			          			<?php } ?>
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
