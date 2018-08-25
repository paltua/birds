
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
            order: [[ 3 , "desc" ]]
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
                    $("#status_"+am_id).attr('value', 'unlock');
                    $("#i_status_"+am_id).removeClass('fa-lock').addClass('fa-unlock');
                }else{
                    $("#status_"+am_id).attr('value', 'lock');
                    $("#i_status_"+am_id).removeClass('fa-unlock').addClass('fa-lock');
                }
                $("#msgShow").html(data.msg);
            }, "json");
        });
    });
</script>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Product Types
            <div class="pull-right"><a href="<?php echo base_url('admin/'.$controller.'/add');?>" class="btn btn-info">
        <i class="fa fa-plus-circle"></i> Add</a></div></h1>
        
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
                    <th> Image</th>
                    <th> Name</th>
                    <th>Parent Category</th>
                    <th>Short Description</th>
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
                    <td><?php if($value->image_name != ''){?>
                        <img height="75" width="150" src="<?php echo base_url('uploads/category/'.$value->image_name);?>">
                    <?php } ?></td>
                    <td><?php echo $value->acmd_name;?></td>
                    <td><?php echo $value->parent_name;?></td>
                    <td><?php echo $value->acmd_short_desc;?></td>
                    <td>
                        <a class="statusChange" href="javascript:void(0);" title="Click to change Status" value="<?php echo $value->acm_status == 'active'?'unlock':'lock';?>" id="status_<?php echo $value->acm_id;?>" name="<?php echo $value->acm_id;?>"><i id="i_status_<?php echo $value->acm_id;?>" class="fa fa-<?php echo $value->acm_status == 'active'?'unlock':'lock';?>"></i></a>
                    </td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($value->acmd_created_date));?></td>
                    <td class="center">
                    <a href="<?php echo base_url();?>admin/animal_category/edit/<?php echo $value->acm_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>
                    <a href="<?php echo base_url();?>admin/animal_category/delete/<?php echo $value->acm_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-trash-o"></i> Delete</a>
                    </td>
                </tr>
                <?php } } ?>
                
            </tbody>
        </table>
    </div>
</div>