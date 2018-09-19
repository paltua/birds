
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
            order: [[ 8 , "desc" ]]
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
<section class="innerbanner">
	<div class="banner-cont">
		<h1 class="title">Publish Listing</h1>
		<div class="breadcramb">
			<ul>
				<li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
				<li>Publish Listing</li>
			</ul>
		</div>
	</div>
</section>
<section class="inner-layout">
	<div class="container">		
		<div class="inner-content">
			<div class="col-lg-12">
				
				<div class="row">
					<h1 class="page-header">My Listing 
            			<div class="pull-right">
            				<a href="<?php echo base_url('user/animal/add');?>" class="btn btn-info">
        						<i class="fa fa-plus-circle"></i> Add</a> 
        				</div>
        			</h1>
        			<?php echo $msg ;?>
					<table class="table table-striped table-bordered table-hover" id="dataTables-example">
			            <thead>
			                <tr>
			                    <th>SKU Code</th>
			                    <th class="no-sort">Image</th>
			                    <th>Pets Name</th>
			                    <th>Price</th>
			                    <th>Category</th>
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
			                    <td><?php echo $value->am_code;?></td>
			                    <td><?php if($value->default_image != ''){
			                    	$image = base_url(UPLOAD_PROD_PATH.$value->default_image);
			                     }else{
			                     	$image = base_url('public/'.THEME.'/images/no-image.jpg');
			                     }?>
			                        <img height="75" width="150" src="<?php echo $image;?>">
			                    
			                    </td>
			                    <td><?php echo $value->amd_name;?></td>
			                    <td><?php echo $value->amd_price;?></td>
			                    <td><?php echo $value->all_cat;?></td>
			                    <td><?php echo $value->am_viewed_count;?></td>
			                    <td><?php echo $value->am_status == 'inactive'?'Waiting for Approval':'Active' ;?>
			                    </td>
			                    <td><?php echo date("F j, Y, g:i a", strtotime($value->am_created_date));?></td>
			                    <td class="center">
			                        <a href="<?php echo base_url();?>user/<?php echo $controller;?>/edit/<?php echo $value->am_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>
			                    </td>
			                </tr>
			                <?php } } ?>
			            </tbody>
			        </table>
			    </div>
			</div>
		</div>
	</div>
</section>

<link rel="stylesheet" href="<?php echo base_url('public/'.THEME.'/');?>css/asRange.css" type="text/css">
<script src="<?php echo base_url('public/'.THEME.'/');?>js/jquery-asRange.js"></script>

<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/prefixfree.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/zoom-slideshow.js"></script>