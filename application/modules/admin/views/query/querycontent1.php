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
                    <th>Aum Id </th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
 
            
        </table>
    </div>
 
<script src="<?php echo base_url();?>resources/datatable/js/jquery-2.2.3.min.js"></script>
<script src="<?php echo base_url();?>resources/datatable/js/jquery.dataTables.min.js"></script>
 
 
<script type="text/javascript">
 var jq14 = jQuery.noConflict(true); 
 $=jQuery;
 (function ($) {
 var table;
 
$(document).ready(function() {

    $("#table").on('click', '.userDelete',function(){
        return confirm("Are you sure you want to delete this Client?");
    });


    /*$("#dialog").dialog({
            modal: true,
            autoOpen: false,
            title: "jQuery Dialog",
            width: 300,
            height: 150
        });*/

    $("#table").on('click', '#btnShow',function(){
      //alert("hi");
            $('#dialog').dialog('open');
            alert("hi");
        });
    
 
    //datatables
   table = $('#table').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
 
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url();?>admin/query/ajax_Querylist",
            "type": "POST"
        },
 
        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ 0,6], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],
 
    });
 
});
}(jq14));
</script>
