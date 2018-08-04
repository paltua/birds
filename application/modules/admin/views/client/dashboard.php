<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>resources/datatable/css/jquery.dataTables.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>resources/datatable/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>resources/datatable/js/dataTables.scroller.js"></script>


<script type="text/javascript" language="javascript" >
	var _site_per_page = "<?php echo ADMIN_PER_PAGE;?>";
    $(document).ready(function() {
       var dataTable =  $('#data-table').DataTable( {
			serverSide: true,
			ajax:{
					url :"<?php echo $url;?>/client/dataTable", // json datasource
					type: "post",  // method  , by default get
					error: function(){  // error handling
						$(".employee-grid-error").html("");
						$("#data-table").append('<tbody class="employee-grid-error"><tr><th colspan="5">No data found</th></tr></tbody>');
						$("#employee-grid_processing").css("display","none");
					}
				},
			/*dom: "frtiS",
			scrollY: 600,*/
			/*scroll off option above*/
			deferRender: true,
			bProcessing: true,
        	iDisplayLength: <?php echo ADMIN_PER_PAGE;?>,
        	bPaginate: true,
        	sPaginationType: _paginateType,
	        language: {
	                    paginate: _paginateTheme            
	                  },
			columnDefs: [ {
				"targets": 'no-sort',
				"orderable": false,
			} ],
	        fnDrawCallback: function () {
	            $("#remove_sort_class_manu").removeClass('sorting_asc');
	            $("#manualDataTable").removeClass('dataTable');

	            /*if($("#data-table").find("tr:not(.ui-widget-header)").length < <?php echo ADMIN_PER_PAGE;?>){
	            	$("#data-table_paginate").hide();
	            }*/
	            var $api = this.api();
	            var pages = $api.page.info().pages;
	            var rows = $api.data().length;
	            if(pages == 1){
	                if(rows < _site_per_page){
	                    $("#data-table_paginate").hide();
	                }
	            }
	        },
        });

	$("#data-table_filter").hide();
    $("#bsSearchId").keyup(function(){
        var searchValue = $.trim($(this).val());
		dataTable.search(searchValue).draw();
    });
       // Fill modal with content from link href
     $(".assignServices").on("show.bs.modal", function(e) {
         var link = $(e.relatedTarget);
         $(this).find(".modal-content").load(link.attr("name"));
     });
	    
    });
</script>

<div class="">

  	<div class="listingMsg">
		<div id="msg"><?php if($msg != ''){ echo $msg;} ?></div>
  	</div>
  	<div class="htext"><img src="<?php echo base_url();?>resources/<?php echo CURRENT_THEME;?>/img/st_2.png" class="icn"> Manage Account Admin
          <ul class="pull-right mng-btns">
            <li><a href="<?php echo $url;?>/client/create" class="btn btn-green"><i class="fa fa-plus-circle"></i> Add New Account Admin</a></li>
          </ul> 
          <div class="pull-right">
            <input type="text" name="bsSearch" id="bsSearchId" class="manage-search" placeholder="Search Client">
          </div>
    </div>
	<div class="">
        	<div class="row">
	          <div class="col-md-12">
	              <table id="data-table" class="table table-striped table-grey table-highlight" style="border-radius:7px; overflow:hidden">
					<thead>
					  <tr>
						<th>Name ( Account )</th>
						<th>Contact Person</th>
						<th>E-mail</th>
						<th>Payment Status</th>
						<th class="no-sort">Actions</th>
					  </tr>
				</thead>
			  </table>
		  </div>
	  </div>	  
	</div>
  </div>




<!-- modal : service assign  -->
<div id="assignServices" class="modal assignServices fade" aria-hidden="true">
  	<div class="vertical-alignment-helper">
	    <div class="modal-dialog modal-lg vertical-align-center">
		    <div class="modal-content">

			
		  	</div><!-- /.modal-content -->
	  	</div><!-- /.modal-dialog -->
	</div>
</div>



<script>
	$(document).ready(function(){
		<?php if($msg != ''){ ?>
			setTimeout('$("#msg").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
		<?php } ?>
		

		$("#data-table").on('click', '.accountAdminStatus',function(){
			var org_id = $(this).attr('id');
			var org_status = $(this).attr('title');
			if (org_id != '') {
				$("#msg").html('');
				$.ajax({
					url:"<?php echo $url;?>/client/statusChange",
					type : "post",
					data : "org_id="+org_id,
					//beforeSent: $(_this).parent().find('span').html('<img style="height:15px; width:15px" src="'+_base_url+'resources/img/motor/spinner.gif"></img> <span style="color:#00BA8B" >Checking Motor Tag Name</span>'),
					//beforeSent: $('#motor_master_tage_name_span').html('<img style="height:15px; width:15px" src="'+_base_url+'resources/img/motor/spinner.gif"></img> <span style="color:#00BA8B" >Checking Motor Tag Name</span>'),
					success:function(data){
						res = JSON.parse(data);
						if (org_status == 'active') {
                            $("#checkCircleId_"+org_id).removeClass('fa-check-circle green');
							$("#checkCircleId_"+org_id).addClass('fa-times-circle-o grey');
							$("#checkCircleId_"+org_id).parent().attr('title','inactive');
                        }else{
							$("#checkCircleId_"+org_id).removeClass('fa-times-circle-o grey');
							$("#checkCircleId_"+org_id).addClass('fa-check-circle green');
							$("#checkCircleId_"+org_id).parent().attr('title','active');
						}
						
						$("#msg").html(res.msg);
						$("#msg").show();
						setTimeout('$("#msg").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
					}
				});
			}else{
				$("#msg").html('<div role="alert" class="alert alert-danger"><p>Something went wrong.</p></div>');
			}
		});
	});
</script>