
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
        $('#dataTables-example').DataTable({
            responsive: true,
            order: [[ 4 , "desc" ]],
            columnDefs: [{ targets: 'no-sort', orderable: false }],
        });

        $(".statusChange").click(function(){
            var url = '<?php echo base_url('admin/'.$controller.'/changeStatus');?>';
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
        <h1 class="page-header">User <!-- <a href="<?php echo base_url('admin/'.$controller.'/add');?>" class="btn btn-info">
        <i class="fa fa-plus-circle"></i> Add</a> --></h1>
        
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div id="msgShow"></div>
    <?php if($msg != ''):?>
    <div class="col-lg-12">
    <?php echo $msg ;?>
    </div>
    <?php endif;?>
    <div class="col-lg-12">
        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Status</th>
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
                    <td><?php echo $value->name;?></td>
                    <td><?php echo $value->email;?></td>
                    <td><?php echo $value->mobile;?></td>
                    <td>
                        <a class="statusChange btn btn-<?php echo $value->um_status == 'active'?'info':'warning';?> btn-xs" href="javascript:void(0);" title="Click to change Status" value="<?php echo $value->um_status == 'active'?'unlock':'lock';?>" id="status_<?php echo $value->user_id;?>" name="<?php echo $value->user_id;?>"><i id="i_status_<?php echo $value->user_id;?>" class="fa fa-<?php echo $value->um_status == 'active'?'unlock':'lock';?>"></i>
                            <span id="span_status_<?php echo $value->user_id;?>"><?php echo ucfirst($value->um_status);?></span>
                        </a>
                    </td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($value->um_created_date));?></td>
                    <td class="center">
                        <!-- <a href="<?php echo base_url();?>admin/<?php echo $controller;?>/edit/<?php echo $value->user_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a> -->
                        <a href="<?php echo base_url();?>admin/<?php echo $controller;?>/delete/<?php echo $value->user_id;?>" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>