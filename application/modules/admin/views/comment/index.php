
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
            order: [[ 3 , "desc" ]]
        });
    });
</script>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Comments <!-- <a href="<?php echo base_url('admin/'.$controller.'/add');?>" class="btn btn-info">
        <i class="fa fa-plus-circle"></i> Add</a> --></h1>
        
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
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
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Action</th>
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
                    <td><?php echo $value->comments;?></td>
                    <td><span class="badge badge-pill badge-<?php echo $value->com_status == 'active'?'success':'secondary';?>"><?php echo ucfirst($value->com_status);?></span></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($value->created_date));?></td>
                    <td class="center">
                        <a href="<?php echo base_url();?>admin/<?php echo $controller;?>/edit/<?php echo $value->com_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>
                        <a href="<?php echo base_url();?>admin/<?php echo $controller;?>/delete/<?php echo $value->com_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-trash-o"></i> Delete</a>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>