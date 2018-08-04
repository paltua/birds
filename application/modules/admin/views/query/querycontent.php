   <style type="text/css">
   table tr:nth-child(odd) td{
           background:#ccc;
   }
   table tr:nth-child(even) td{
            background:#fff; #1b4f72 
   }
   table th{
            background:#FFA07A;
   }

   </style>

   
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.9/jquery-ui.js" type="text/javascript"></script>
<link href="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.9/themes/blitzer/jquery-ui.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo base_url();?>resources/datatable/css/jquery.dataTables.min.css" rel="stylesheet">
    <div class="container">
        <div style="margin-left:1000px;">
                 <!--  <a href="<?php echo base_url().ADMIN_NAME;?>/query/addquery"><button class="btn btn-primary" type="button" >Add New Query</button></a> -->
               </div>
              <br>
        
        <table id="table" border="1" class="display" cellspacing="0" width="100%">
          <thead>
                <tr>
                    <th>SlNo</th>
                    <th>Name</th>
                    <th>Query Content</th>
                    <th>Status</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
 
            
        </table>
       

  <!-- Modal -->
  <div id="emp-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
   <div class="modal-dialog"> 
      <div class="modal-content">                  
         <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Query Details</h4>
        </div>
        <div class="modal-body"> 
          <div id="employee_data-loader" style="display: none; text-align: center;">
             </div> 
                <div id="employee-detail">
                 <p id="details" style="word-wrap:break-word;"></p>      
                </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

  <div class="modal fade" id="myModalNorm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span> </button>
                <h4 class="modal-title" id="myModalLabel">Send Email </h4>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                
                <form role="form">
                  <div class="form-group">
                      <label class="control-label col-md-4" for="first_name">To</label>
                      <div class="col-md-6">
                          <input type="text" class="form-control" id="Email_to" name="Email_to" />
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="control-label col-md-4" for="first_name">Subject</label>
                      <div class="col-md-6">
                          <input type="text" class="form-control" id="Email_subject" name="Email_subject" />
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="control-label col-md-4" for="comment">Message</label>
                      <div class="col-md-6">
                         <textarea rows="6" class="form-control" id="message" name="message"></textarea>
                      </div>
                  </div>

                  <div class="form-group">
                      <div class="col-md-6">
                          <button type="submit" value="Submit" class="btn btn-default">Send</button>
                      </div>
                  </div>
                  
                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                </form>
            </div><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
            <div class="modal-footer">
            </div>
      </div>
      
    </div>
  </div>
  
</div>
 
<script src="<?php echo base_url();?>resources/datatable/js/jquery-2.2.3.min.js"></script>
<script src="<?php echo base_url();?>resources/datatable/js/jquery.dataTables.min.js"></script>
 
 
<script type="text/javascript">
var csfrData = {};
 csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                  = '<?php echo $this->security->get_csrf_hash(); ?>';

 $.ajaxSetup({
    data: csfrData
 });
 /*var jq14 = jQuery.noConflict(true); 
 $=jQuery;
 (function ($) {*/

 var table;
 
$(document).ready(function() {

   table = $('#table').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
 
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url();?>admin/query/ajax_Querylist",
            "type": "POST"
           
        },
 
        "columnDefs": [
        { 
            "targets": [ 0,5], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],
 
    });
 
});

$(document).on('click', '#getEmployee', function(e){

    e.preventDefault();
    var aqc_id = $(this).data('aqc_id');
    var session_id = $(this).data('session_id');
    //alert(session_id);
    $('#employee-detail').hide();
    $('#employee_data-loader').show();
    $.ajax({
      type: 'POST',
      url: '<?php echo base_url();?>admin/query/ajax_Modallist',
      data: {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?PHP echo $this->security->get_csrf_hash(); ?>','aqc_id':$(this).data('aqc_id'),'session_id':$(this).data('session_id'), },
      dataType: 'json',
      cache: false
    })
.done(function(data)
{
  $('#employee-detail').show();
  
  $('#details').html(data.query_content);
})
.fail(function()
{

  $('#employee-detail').html('Error, Please try again...');
  $('#employee_data-loader').hide();
});
$('#emp-modal').on('hidden.bs.modal', function () {
   location.reload();
});

});

$(document).on('click', '#getEmail', function(e){

    e.preventDefault();
    var aqc_id = $(this).data('aqc_id');
    var session_id = $(this).data('session_id');
    //alert(session_id);
    $('#email-detail').hide();
    //$('#employee_data-loader').show();
    $.ajax({
      type: 'POST',
      url: '<?php echo base_url();?>admin/query/ajax_Email',
      data: {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?PHP echo $this->security->get_csrf_hash(); ?>','aqc_id':$(this).data('aqc_id'),'session_id':$(this).data('session_id'), },
      dataType: 'json',
      cache: false
    })
.done(function(data)
{
  $('#email-detail').show();
  
  $('#email-detail').html('yyyy');
})
.fail(function()
{

  $('#email-detail').html('Error, Please try again...');
});

});
//}(jq14));

</script>
