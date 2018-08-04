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
    <div class="panel-heading"><strong>SHED - 6</strong></div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
				    <th ></th>
	      			<!-- <th style="text-align: center;"><p>Avg. of Today</p></th> -->
		      		<th style="text-align: center;">
			          	<p>Avg. of Last 7 Days</p>
			        </th>
			        <th style="text-align: center;">
			          	<p>Avg. of Last 30 Days</p>
			        </th>
			    </tr>
			    <tr role="row">
		      		
	      			<th style="">
	      				<p>CFM/CMPX</p>
			        </th>
		      		<!-- <td style="">
			          	<p style="text-align: right;">
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/cfmCmpxTodayGraph/<?php echo $startDateShow;?>" title="Graph for Today">
			          		<?php if($cmpx['today'] != 0){ 
			          			echo number_format((float)round(($cfm['today'] / ($cmpx['today']/WEAVING_CMPX)), 2), 2, '.', ''); 
			          		}else{
		          				echo 'DNA';//number_format((float)round(0, 2), 2, '.', '');
		          			}	
		          			?>
		          			</a>
			          	</p>
			        </td> -->
			        <td style="">
			        	<p style="text-align: right;">
			        		
			          		<?php 
			          		$cfm_cmpx = 0;
			          		if($cmpx['week'] != 0){
			          			$cfm_cmpx = (($cfm['week']/WEAVING_CFM) / ($cmpx['week']/WEAVING_CMPX));
			          		}
			          		if($cfm_cmpx != 0){ ?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/cfmCmpxWeekGraph/<?php echo $startDateShow;?>" title="Graph for Last 7 Days">
			          		 	<?php echo number_format((float)round($cfm_cmpx, 2), 2, '.', ''); ?>
			          		 	</a>	
			          		<?php }else{
		          				echo 'DNA';//echo number_format((float)round(0, 2), 2, '.', '');
		          			}	?>
		          			
			          	</p>
			        </td>
					<td style="">
			          	<p style="text-align: right;">
			          		
			          		<?php 
			          		$cfm_cmpx = 0;
			          		if($cmpx['month'] != 0){
			          			$cfm_cmpx = (($cfm['month']/WEAVING_CFM) / ($cmpx['month']/WEAVING_CMPX));
			          		}
			          		if($cmpx['month'] != 0){ ?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/cfmCmpxMonthGraph/<?php echo $startDateShow;?>" title="Graph for Last 30 Days">
			          			<?php echo number_format((float)round($cfm_cmpx, 2), 2, '.', ''); ?>
			          			<?php 
			          			//echo number_format((float)round((($cfm['month']/WEAVING_CFM) / ($cmpx['month']/WEAVING_CMPX)), 2), 2, '.', ''); ?>
			          			</a>
			          		<?php }else{
		          				echo 'DNA';//echo number_format((float)round(0, 2), 2, '.', '');
		          			}	
		          			?>
		          			
			          	</p>
			        </td>
			        
			    </tr>
			    <tr role="row">
		      		
	      			<th style="">
	      				<p>KWH/CMPX</p>
			        </th>
		      		<!-- <td style="">
			          	<p style="text-align: right;">
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/kwCmpxTodayGraph/<?php echo $startDateShow;?>" title="Graph for Today">
			          		<?php if($cmpx['today'] != 0){ 
			          			 /*echo number_format((float)round(($kw['today'] / ($cmpx['today']/WEAVING_CMPX)) * $daysOfWeekAndMonth[0]->hoursOfDays, 2), 2, '.', '');*/ 
			          			 echo number_format((float)round(($kw['today'] / ($cmpx['today']/WEAVING_CMPX)), 2), 2, '.', '');
			          			}else{
			          				echo number_format((float)round(0, 2), 2, '.', '');
			          			}	
			          		?>
			          		</a>
			          	</p>
			        </td> -->
			        <td style="">
			        	<p style="text-align: right;">
			        		
			          		<?php if($cmpx['week'] != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/kwCmpxWeekGraph/<?php echo $startDateShow;?>" title="Graph for Last 7 Days">
			          			<?php 
			          			 /*echo number_format((float)round(($kw['week'] / ($cmpx['week']/WEAVING_CMPX)) * $daysOfWeekAndMonth[0]->daysOfWeek, 2), 2, '.', ''); */
			          			 echo number_format((float)round((($kw['week'] ) / ($cmpx['week']/WEAVING_CMPX)) , 2), 2, '.', ''); ?>
			          			</a>
			          			<?php }else{
			          				echo 'DNA';//echo number_format((float)round(0, 2), 2, '.', '');
			          			}	
			          		?>
			          	
			          	</p>
			        </td>
					<td style="">
			          	<p style="text-align: right;">
			          		
			          		<?php if($cmpx['month'] != 0){ ?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/kwCmpxMonthGraph/<?php echo $startDateShow;?>" title="Graph for Last 30 Days">
			          			<?php 
			          			/*echo number_format((float)round(($kw['month'] / ($cmpx['month']/WEAVING_CMPX)) * $daysOfWeekAndMonth[0]->daysOfMonth , 2), 2, '.', '');*/
			          			echo number_format((float)round((($kw['month'] ) / ($cmpx['month']/WEAVING_CMPX)) , 2), 2, '.', ''); ?>
			          		</a>
		          			<?php }else{
		          				echo 'DNA';//echo number_format((float)round(0, 2), 2, '.', '');
		          			} ?>
		          			
			          	</p>
			        </td>
			        
			    </tr>
			    <tr role="row">
		      		
	      			<th style="">
	      				<p>Total CMPX</p>
			        </th>
		      		<!-- <td style="">
			          	<p style="text-align: right;">
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalCmpxTodayGraph/<?php echo $startDateShow;?>" title="Graph for Today">
			          		<?php echo number_format((float)round($cmpx['today']/WEAVING_CMPX, 2), 2, '.', ''); ?>
			          		</a>
			          	</p>
			        </td> -->
			        <td style="">
			        	<p style="text-align: right;">
			        		<?php if($cmpx['week'] != 0){?>
			        		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalCmpxWeekMonthGraph/<?php echo $startDateShow;?>/7" title="Graph for Last 7 Days">
			          		<?php echo number_format((float)round($cmpx['week']/WEAVING_CMPX, 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
					<td style="">
			          	<p style="text-align: right;">
			          		<?php if($cmpx['month'] != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalCmpxWeekMonthGraph/<?php echo $startDateShow;?>/30" title="Graph for Last 30 Days">
			          		<?php echo number_format((float)round($cmpx['month']/WEAVING_CMPX, 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{ echo 'DNA'; } ?>
			          	</p>
			        </td>
			        
			    </tr>
			    <tr role="row">
		      		
	      			<th style="">
	      				<p>CFM</p>
			        </th>
		      		<!-- <td style="">
			          	<p style="text-align: right;">
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalCfmTodayGraph/<?php echo $startDateShow;?>" title="Graph for Today">
			          		<?php echo number_format((float)round($cfm['today'], 2), 2, '.', ''); ?>
			          		</a>
			          	</p>
			        </td> -->
			        <td style="">
			        	<p style="text-align: right;">
			        		<?php if($cfm['week'] != 0){?>
			        		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalCfmWeekGraph/<?php echo $startDateShow;?>" title="Graph for Last 7 Days">
			          		<?php echo number_format((float)round($cfm['week']/WEAVING_CFM, 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
					<td style="">
			          	<p style="text-align: right;">
			          		<?php if($cfm['month'] != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalCfmMonthGraph/<?php echo $startDateShow;?>" title="Graph for Last 30 Days">
			          		<?php echo number_format((float)round($cfm['month']/WEAVING_CFM, 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
			        
			    </tr>
			    <tr role="row">
		      		
	      			<th style="">
	      				<p>CFM Pressure</p>
			        </th>
		      		<!-- <td style="">
			          	<p style="text-align: right;">
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalCfmTodayGraph/<?php echo $startDateShow;?>" title="Graph for Today">
			          		<?php echo number_format((float)round($cfm['today'], 2), 2, '.', ''); ?>
			          		</a>
			          	</p>
			        </td> -->
			        <td style="">
			        	<p style="text-align: right;">
			        		<?php if($cfm_pres['week'] != 0){?>
			        		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalCfmWeekGraph/<?php echo $startDateShow;?>" title="Graph for Last 7 Days">
			          		<?php echo number_format((float)round($cfm_pres['week'], 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
					<td style="">
			          	<p style="text-align: right;">
			          		<?php if($cfm_pres['month'] != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalCfmMonthGraph/<?php echo $startDateShow;?>" title="Graph for Last 30 Days">
			          		<?php echo number_format((float)round($cfm_pres['month'], 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
			        
			    </tr>
			    <tr role="row">
		      		
	      			<th style="">
	      				<p>H-Plant KWH</p>
			        </th>
		      		<!-- <td style="">
			          	<p style="text-align: right;">
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalKwTodayGraph/<?php echo $startDateShow;?>/1" title="Graph for Today">
			          		<?php echo number_format((float)round($kw_hplant['today'], 2), 2, '.', ''); ?>
			          		</a>
			          	</p>
			        </td> -->
			        <td style="">
			        	<p style="text-align: right;">
			        		<?php if($kw_hplant['week'] != 0){?>
			        		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalKwWeekGraph/<?php echo $startDateShow;?>/1" title="Graph for Last 7 Days">
			          		<?php echo number_format((float)round($kw_hplant['week']  , 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
					<td style="">
			          	<p style="text-align: right;">
			          		<?php if($kw_hplant['month'] != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalKwMonthGraph/<?php echo $startDateShow;?>/1" title="Graph for Last 30 Days">
			          		<?php echo number_format((float)round($kw_hplant['month'] , 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
			    </tr>
			    <tr role="row">
		      		
	      			<th style="">
	      				<p>Loom KWH</p>
			        </th>
		      		<!-- <td style="">
			          	<p style="text-align: right;">
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalKwTodayGraph/<?php echo $startDateShow;?>/2" title="Graph for Today">
			          		<?php echo number_format((float)round($kw_loom['today'] , 2), 2, '.', ''); ?>
			          		</a>
			          	</p>
			        </td> -->
			        <td style="">
			        	<p style="text-align: right;">
			        		<?php if($kw_loom['week'] != 0){?>
			        		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalKwWeekGraph/<?php echo $startDateShow;?>/2" title="Graph for Last 7 Days">
			          		<?php echo number_format((float)round($kw_loom['week'] , 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
					<td style="">
			          	<p style="text-align: right;">
			          		<?php if($kw_loom['month'] != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalKwMonthGraph/<?php echo $startDateShow;?>/2" title="Graph for Last 30 Days">
			          		<?php echo number_format((float)round($kw_loom['month'], 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
			    </tr>
			    <tr role="row">
		      		
	      			<th style="">
	      				<p>Total KWH</p>
			        </th>
		      		<!-- <td style="">
			          	<p style="text-align: right;">
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalKwTodayGraph/<?php echo $startDateShow;?>/0" title="Graph for Today">
			          		<?php echo number_format((float)round($kw['today'] , 2), 2, '.', ''); ?>
			          		</a>
			          	</p>
			        </td> -->
			        <td style="">
			        	<p style="text-align: right;">
			        		<?php if($kw['week'] != 0){?>
			        		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalKwWeekGraph/<?php echo $startDateShow;?>/0" title="Graph for Last 7 Days">
			          		<?php echo number_format((float)round($kw['week'], 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
					<td style="">
			          	<p style="text-align: right;">
			          		<?php if($kw['month'] != 0){?>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/totalKwMonthGraph/<?php echo $startDateShow;?>/0" title="Graph for Last 30 Days">
			          		<?php echo number_format((float)round($kw['month'], 2), 2, '.', ''); ?>
			          		</a>
			          		<?php }else{
			          			echo 'DNA';
			          		}?>
			          	</p>
			        </td>
			    </tr>
		    </thead>  
		    
		</table>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading"><strong>Major product running this month</strong></div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer display" id="table_2" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
	      			<th style="">
			          	<p>Style ID</p>
			        </th>
			        <th style="">
			          	<p>Style Name</p>
			        </th>
		      		<th style="text-align: center;">
			          	<p>Avg CMPX this Month</p>
			        </th>
			        <th style="text-align: center;">
			          	<p>Total CMPX this Month</p>
			        </th>
			    </tr>
		    </thead>  
		    <tbody>
		    	<?php 
		    		if(count($style) > 0){ 
		    			foreach ($style as $key => $value) {
		    				

		    	?>
		    	<tr role="row">
	      			<td style="">
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getStyleDetails/<?php echo $value->style_id;?>" title="Style Details">
			          		<?php echo $value->style_id; ?>
			          		</a>
			          	</p>
			        </td>
			        <td style="">
			        	<p>
			        		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getStyleDetails/<?php echo $value->style_id;?>" title="Style Details">
			        		<?php echo $value->style; ?>
			        		</a>
			          	</p>
			        </td>
		      		<td style="text-align: right;">
		      			<p>
		      				<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getAvgCmpxGraphByStyle/<?php echo $value->style_id;?>/<?php echo $startDateShow;?>/avg" title="Graph Details of <?php echo $value->style; ?>">
		      				<?php echo number_format((float)round($value->avg_cmpx/WEAVING_CMPX, 2), 2, '.', ''); ?>
		      				</a>
			          	</p>
			        </td>
			        <td style="text-align: right;">
			          	<p>
			          		<a href="javascript:void(0);" class="ppChartShow" meter-link="<?php echo base_url();?>weaving/dashboard/getAvgCmpxGraphByStyle/<?php echo $value->style_id;?>/<?php echo $startDateShow;?>/sum" title="Graph Details of <?php echo $value->style; ?>">
			          		<?php echo number_format((float)round($value->total_cmpx/WEAVING_CMPX, 2), 2, '.', ''); ?>
			          		</a>
			          	</p>
			        </td>
			    </tr>
			    <?php }} ?>
		  	</tbody>
		</table>
    </div>
</div>


<?php //echo "<strong>Alert : Please note that some of the meter data have shown inconsistency and hence all indicators are not being displayed. Kindly speak to engineering/relevant department.</strong>";
?>
