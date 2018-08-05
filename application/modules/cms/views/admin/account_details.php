<script>
$(document).ready(function() {
  $('#data-table').dataTable();
});
</script>


<div class="row">
  <div class="content">
    <div class="content-container">
  	  <?php if(isset($msg) && $msg != ''){ echo $msg;}?>

  		<div class="row">
        <div class="col-md-12">
          <table id="data-table" class="table table-bordered table-highlight">
    				<thead>
    				  <tr>
                <th>Sl No.</th>
      					<th>Website Title</th>
                <th>Site ID</th>
                <th>Domain</th>
      					<th>Status</th>
      					<th>Registered Since</th>
    				  </tr>
    				</thead>
            <tbody>
              <?php foreach ($account_details as $key => $acc) { ?>
              <tr>
                <td><?php echo $key + 1; ?></td>
                <td><?php echo $acc->website_title; ?></td>
                <td><?php echo $acc->tracking_Id ; ?></td>
                <td><?php echo $acc->domain_name; ?></td>
                <td><?php echo $acc->web_status; ?></td>
                <td><?php echo $acc->created_date; ?></td>
              </tr>
              <?php } ?>
            </tbody>
  			  </table>
  		  </div>
  		</div>
	</div>
  </div>
</div>

	
	