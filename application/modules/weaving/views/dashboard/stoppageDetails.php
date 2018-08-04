<?php if(count($details) > 10){?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>resource/datatable/css/jquery.dataTables.css">
<script type="text/javascript" src="<?php echo base_url();?>resource/datatable/js/jquery.dataTables.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
        var dataTable = $('#apisIdNew').DataTable( {
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
<?php }?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Loom #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
    <div class="panel panel-default">
    <div class="panel-heading">Stoppage Details(<?php echo date("F j, Y, g:i a", strtotime($startDateShow));?> - <?php echo date("F j, Y, g:i a", strtotime($endDateShow));?>)</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisIdNew" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
	      			<th style="">
			          	<p>Stop Time</p>
			        </th>
		      		<th style="">
			          	<p>Duration of stop(Sec)</p>
			        </th>
			        <th style="">
			          	<p>Stoppage Due to </p>
			        </th>
					<th style="">
			          	<p>Weaver Code</p>
			        </th>
			        <!-- <th style="">
			          	<p>KW/CMPX</p>
			        </th> -->
			    </tr>
			</thead>  
		    <tbody> 
			    <?php 
			    	if(count($details) > 0){
			    		foreach ($details as $value) {
			    			
			    ?>
			    <tr role="row">
	      			<td style="">
			          	<p><?php echo $value->BEGTIME;?></p>
			        </td>
		      		<td style="">
			          	<p><?php echo $value->DURATION;?> </p>
			        </td>
			        <td style="">
			          	<p><?php echo $value->descr;?></p>
			        </td>
					<td style="">
			          	<p><?php echo $value->WVR;?></p>
			        </td>
			        <!-- <td style="">
			          	<p></p>
			        </td> -->
			    </tr>
			    <?php } }else{ ?>
			    <tr role="row">
	      			<td style="" colspan="4">
			          	<p>No data please.</p>
			        </td>
			    </tr>
			    <?php }?>
		    </tbody>  
		    
		</table>
    </div>
</div>
</div>