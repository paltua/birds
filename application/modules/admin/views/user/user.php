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

    <link href="<?php echo base_url();?>resources/datatable/css/jquery.dataTables.min.css" rel="stylesheet">
    <div class="container">
        <div style="margin-left:1150px;">
                  <a href="<?php //echo base_url().ADMIN_NAME;?>/user/adduser"><button class="btn btn-primary" type="button" >Add New User</button></a>
               </div>
               <p>
                  Search Caterogy: 
                  <select id="table-filter">
                        <option value="">All</option>
                        <?php foreach ($list as $key => $value) {?>
                          <option ><?php echo $value;?></option>
                        <?php } ?>
                        
                  </select>
                </p>
              <br>
        
        <table id="table" border="1" class="display" cellspacing="0" width="100%">
          <thead>
                <tr>
                    <th>SlNo</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone No</th>
                    <th>Status</th>
                    <th>Category </th>
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
var csfrData = {};
 csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                  = '<?php echo $this->security->get_csrf_hash(); ?>';

 $.ajaxSetup({
   data: csfrData
 });
 var jq14 = jQuery.noConflict(true); 
 $=jQuery;
 (function ($) {
 var table;
 
$(document).ready(function() {

    $("#table").on('click', '.userDelete',function(){
        return confirm("Are you sure you want to delete this Client?");
    });
 
    //datatables
   table = $('#table').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
 
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url();?>admin/user/ajax_list",
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
   $('#table-filter').on('change', function(){
       table.search(this.value).draw();   
    });
 
});
}(jq14));
</script>
