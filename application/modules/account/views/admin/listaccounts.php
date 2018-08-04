<script>
$(document).ready(function() {
        var dataTable =  $('#data-table').DataTable({
        serverSide: true,
        ajax:{
                url :"<?php echo base_url();?>account/sadmin/dataTable", // json datasource
                type: "post"
            },
        /*dom: "frtiS",
        scrollY: 600,*/
        /*scroll off option*/
        //deferRender: true,
        //lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        /*scrollCollapse: true,
        scroller: {
            loadingIndicator: true
        },
        columnDefs: [{
            "targets": 'no-sort',
            "orderable": false,
        }],*/
        aoColumnDefs: [{ "bSortable": false, "aTargets": [0, 7] }],
    		columns: [
    			null,
          null,
          null,
          null,
          null,
          null,
          null,
    			{ className:"actionIcons" },
    		]
      });
});
</script>


<div class="row">
  <div class="content">
    <div class="content-container">

  	  <?php if($msg != ''){ echo $msg;} ?>

      <div class="row">
        <div class="col-md-12">
            <a href="<?php echo base_url()?>account/sadmin/createWebMaster" class="btn btn-success">
            <i class="fa fa-plus-circle"></i> Add New Account
            </a>
        </div>
      </div>

      <div class="row">&nbsp;</div>

  		<div class="row">
          <div class="col-md-12">
            <table id="data-table" class="table table-bordered table-highlight">
    				<thead>
    				  <tr>
              <th>Sl No.</th>
    					<th>Account Name</th>
              <th>Email</th>
              <th>Contact No.</th>
    					<th>Status</th>
    					<th>Registered Since</th>
              <th>Websites</th>
              <th>Action</th>
    				  </tr>
    				</thead>
            <tbody><tr><td colspan="6">Loading ...</td></tr></tbody>
    			  </table>
  		  </div>
  		</div>
	</div>
  </div>
</div>

	
	