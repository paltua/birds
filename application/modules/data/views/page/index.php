<!DOCTYPE html>
<html lang="en">
<head>
<?php $this->load->view('head');?>
<link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">
<script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>
<script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>resource/chosen/chosen.min.css">
<script src="<?php echo base_url();?>resource/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript">    


function callNewDataSet(){
    $.post( "<?php echo base_url();?>air/dashboard/getNewDataSet",{"<?php echo $this->security->get_csrf_token_name(); ?>" : "<?php echo $this->security->get_csrf_hash(); ?>"}, function( data ) {
        if(data.status == 'success'){
            $("#allDataId").html('');
            $("#allDataId").html(data.all);
        }               
    },'json');

}

$(document).ready(function(){
    $("#p_device_id").chosen({no_results_text: "Oops, No Device found!"});
    $('#allDataId').on('click','.showChart', function() { 
        var meter_link = $(this).attr("meter-link");
        $('.myMeterModal').modal('show');
        $('.myMeterModal').find(".modal-content").load(meter_link);
    });
});
</script>
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
              <a class="navbar-brand" href="#">WELSPUN EDA</a>
            </div>
        <ul class="nav navbar-nav" style="width: 80%;">
           <?php $this->load->view('header');?>
        </ul>
        </div>
    </nav>

<?php if(count($details) > 0){?>
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
<section class="pad">
    <div class="container">

        <div class="panel panel-info">
        <div class="panel-heading">Page View By WIL User</div>
        <div class="panel-body">
            <table class="table table-striped table-bordered table-hover no-footer" id="apisIdNew" role="grid" aria-describedby="apisId_info">
                <thead>
                    <tr role="row">
                        <th style="">
                            <p>IP</p>
                        </th>
                        <th style="">
                            <p>Email</p>
                        </th>
                        <th style="">
                            <p>URL</p>
                        </th>
                        <th style="">
                            <p>Date and Time</p>
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
                            <p><?php echo $value->ip;?></p>
                        </td>
                        <td style="">
                            <p><?php echo $value->email;?> </p>
                        </td>
                        <td style="">
                            <p><a href="<?php echo str_replace("index.php/","",$value->url);?>" target="_blank"><?php echo str_replace("index.php/","",$value->url);?></a></p>
                        </td>
                        <td style="">
                            <p><?php echo $value->created_date;?></p>
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
</section>
<?php $this->load->view('footer');?>
    <br/>
</body>
</html> 