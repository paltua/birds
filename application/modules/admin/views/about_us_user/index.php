
<!-- DataTables CSS -->
<link href="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
<!-- DataTables Responsive CSS -->
<link href="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
<!-- DataTables JavaScript -->
<script src="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-responsive/dataTables.responsive.js"></script>

<script>
    $(document).ready(function() {
        $('#exampleTable').DataTable({
            responsive: true,
            order: [[ 5 , "desc" ]],
            columnDefs: [{ targets: 'no-sort', orderable: false }],
        });

        var csfrData = {};
        csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                         = '<?php echo $this->security->get_csrf_hash(); ?>';
        
        $.ajaxSetup({
          data: csfrData
        });

        $(".statusChange").click(function(){
            var url = '<?php echo base_url('admin/animal_category/changeStatus');?>';
            var am_id = $(this).attr('name');
            var am_status = $(this).attr('value');
            $.post( url, { am_id : am_id}, function( data ) {
                if(am_status == 'lock'){
                    $("#status_"+am_id).attr('value', 'unlock').removeClass('btn-warning').addClass('btn-info');
                    $("#i_status_"+am_id).removeClass('fa-lock').addClass('fa-unlock');
                    $("#span_status_"+am_id).text('Active');
                }else{
                    $("#status_"+am_id).attr('value', 'lock').removeClass('btn-info').addClass('btn-warning');
                    $("#i_status_"+am_id).removeClass('fa-unlock').addClass('fa-lock');
                    $("#span_status_"+am_id).text('Inactive');
                }
                $("#msgShow").html(data.msg);
            }, "json");
        });
    });
</script>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">About Us User</h1>
        
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <?php if($msg != ''):?>
    <div class="col-lg-12">
    <?php echo $msg ;?>
    </div>
    <?php endif;?>
    <div id="msgShow"></div>
    <div class="col-lg-12">
        <table width="100%" class="table table-striped table-bordered table-hover" id="exampleTable">
            <thead>
                <tr>
                    <th> Profile Image</th>
                    <th> Name</th>
                    <th> Mobile</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Created Date</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($list) > 0){
                    foreach ($list as $key => $value) {
                        if($key%2 == 0){
                            $listClass = 'gradeC';
                        }else{
                            $listClass = 'gradeU';
                        }
                ?>
                <tr class="<?php echo $listClass;?> ">
                    <td><?php if($value->img != ''){?>
                        <img height="150" width="150" src="<?php echo base_url(UPLOAD_ABOUT_US_USER.'thumb/'.$value->img);?>">
                    <?php } ?></td>
                    <td><?php echo $value->name;?></td>
                    <td><?php echo $value->mobile;?></td>
                    <td><?php echo $value->email;?></td>
                    <td><?php echo $value->position;?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($value->last_updated_date));?></td>
                    <td class="center">
                    <a href="<?php echo base_url();?>admin/about_us_user/edit/<?php echo $value->auu_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>
                    </td>
                </tr>
                <?php } } ?>
                
            </tbody>
        </table>
    </div>
</div>