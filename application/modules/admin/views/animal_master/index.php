
<!-- DataTables CSS -->
<link href="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
<!-- DataTables Responsive CSS -->
<link href="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
<!-- DataTables JavaScript -->
<script src="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-responsive/dataTables.responsive.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true,
            order: [[ 3 , "desc" ]]
        });

        var csfrData = {};
        csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                         = '<?php echo $this->security->get_csrf_hash(); ?>';
        //alert('<?php echo $this->security->get_csrf_hash(); ?>');
        $.ajaxSetup({
          data: csfrData
        });

        $(".statusChange").click(function(){
            alert("ok");
            var url = '<?php echo base_url('admin/animal_master/changeStatus');?>'
            $.post( url, { am_id : $(this).attr('id'), function( data ) {
                //alert(data.msg);
                $("#msgShow").html(data.msg);
            }, "json");

            //window.location.href = '<?php echo base_url();?>animal_master/setMainImage/<?php echo $am_id;?>/'+$(this).val();
        });
    });
</script>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Pets and Pet Accessories 
            <div class="pull-right"><a href="<?php echo base_url('admin/'.$controller.'/add');?>" class="btn btn-info">
        <i class="fa fa-plus-circle"></i> Add</a> </div></h1>
        
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
        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
            <thead>
                <tr>
                    <th>Seq Number</th>
                    <th>Image</th>
                    <th>Pets Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>User Type</th>
                    <th>View Count</th>
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
                    <td><?php echo $value->seq_number;?></td>
                    <td><?php if($value->default_image != ''){?> 
                        <img height="75" width="150" src="<?php echo base_url('uploads/animal/'.$value->default_image);?>">
                    <?php }?>
                    </td>
                    <td><?php echo $value->amd_name;?></td>
                    <td><?php echo $value->amd_price;?></td>
                    <td><?php echo $value->all_cat;?></td>
                    <td><?php echo $value->am_user_type;?></td>
                    <td><?php echo $value->am_viewed_count;?></td>
                    <td> <a class="statusChange" href="javascript:void(0);" value="<?php echo $value->am_status;?>" id="<?php echo $value->am_id;?>"><i class="fa fa-<?php echo $value->am_status == 'active'?'unlock':'lock';?>"></i></a></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($value->am_created_date));?></td>
                    <td class="center">
                        <a href="<?php echo base_url();?>admin/<?php echo $controller;?>/image/<?php echo $value->am_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-picture-o"></i> Image</a>
                        <a href="<?php echo base_url();?>admin/<?php echo $controller;?>/edit/<?php echo $value->am_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>
                        <a href="<?php echo base_url();?>admin/<?php echo $controller;?>/delete/<?php echo $value->am_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-trash-o"></i> Delete</a>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>