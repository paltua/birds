
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
            order: [[ 7 , "desc" ]],
            columnDefs: [{ targets: 'no-sort', orderable: false }],
        });

        

        var csfrData = {};
        csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                         = '<?php echo $this->security->get_csrf_hash(); ?>';
        
        $.ajaxSetup({
          data: csfrData
        });

        $(".statusChange").click(function(){
            var url = '<?php echo base_url('admin/animal_master/changeStatus');?>';
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
                    <th>SKU Code</th>
                    <th class="no-sort">Image</th>
                    <th>Pets Name</th>
                    <th>Price</th>
                    <!-- <th>Category</th>
                    <th>User Type</th> -->
                    <th>User Details</th>
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
                    <td><?php echo $value->am_code;?><br>
                        <?php if($value->days < 8){?>
                            <span class="badge badge-pill badge-danger">New</span><br>
                        <?php }?>
                        <span class="badge badge-pill badge-danger">Active for <?php echo $value->days;?> Days</span><br>
                    </td>
                    <td><?php if($value->default_image != ''){?> 
                        <img height="75" width="150" src="<?php echo base_url('uploads/animal/'.$value->default_image);?>">
                    <?php }?>
                    </td>
                    <td><?php echo $value->amd_name;?></td>
                    <td><?php echo $value->amd_price;?></td>
                    <!-- <td><?php echo $value->all_cat;?></td>
                    <td><?php echo $value->am_user_type;?></td> -->
                    <td>
                        <?php if($value->am_user_type == 'user'){?>
                            <i class="fa fa-user"></i> <?php echo $value->user_name;?><br>
                            <i class="fa fa-phone"></i> <?php echo $value->mobile;?><br>
                            <i class="fa fa-at"></i> <?php echo $value->email;?><br>
                        <?php }else{
                            echo 'Admin';
                        }
                        ?>
                    </td>
                    <td><?php echo $value->am_viewed_count;?></td>
                    <td> 
                        <a class="statusChange btn btn-<?php echo $value->am_status == 'active'?'info':'warning';?>" href="javascript:void(0);" title="Click to change Status" value="<?php echo $value->am_status == 'active'?'unlock':'lock';?>" id="status_<?php echo $value->am_id;?>" name="<?php echo $value->am_id;?>"><i id="i_status_<?php echo $value->am_id;?>" class="fa fa-<?php echo $value->am_status == 'active'?'unlock':'lock';?>"></i>
                            <span id="span_status_<?php echo $value->am_id;?>"><?php echo ucfirst($value->am_status);?></span>
                        </a>
                    </td>
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