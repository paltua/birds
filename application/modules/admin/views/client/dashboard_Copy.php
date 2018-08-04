<section class="content-header">
        <h1>Client</h1>
        <ol class="breadcrumb">
            <li class=''><a href='<?php echo $breadcrumb['home'];?>'>Home</a></li>
            <li class='active'>Client</li>
        </ol>
</section>
<section class="content">
    <?php if($msg != ''){ echo $msg;} ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Listing Tables
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="dataTable_wrapper">
                        <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <div class="row">
                                <form action="" method="post" accept-charset="utf-8">
                                    <div class="col-sm-10">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>Organization Name:<input type="text" name="org_master[org_name]" id="org_master_org_name" class="form-control input-sm" placeholder="" aria-controls="dataTables-example"></label>
                                            <label>Contact person:<input type="text" name="user_master[user_name]" id="user_master_user_name" class="form-control input-sm" placeholder="" aria-controls="dataTables-example"></label>
                                            <label>Email:<input type="text" name="user_master[email]" id="user_master_email" class="form-control input-sm" placeholder="" aria-controls="dataTables-example"></label>
                                            <div class="form-group">
                                                <label>Payment:</label>
                                                <select class="form-control" name="org_master[subscription_status]" id="org_master_subscription_status">
                                                    <option value="">Select payment</option>
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label><button type="submit"  class="btn btn-primary">Submit</button></label>
                                            <label><button type="reset"  class="btn btn-default">Reset</button></label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="row"><div class="col-sm-12"><table class="table table-striped table-bordered table-hover dataTable no-footer" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info">
                            <thead>
                                <tr role="row">
                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 206px;">Organization Name</th>
                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 249px;">Contact Person</th>
                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 227px;">Contact Email</th>
                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 179px;">Motors</th>
                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 179px;">Payment</th>
                                    <th tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 132px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($clients) > 0){
                                foreach($clients as $k => $cl){
                                    if($k%2 == 0){ $class = 'odd';}else{ $class = 'even'; }    
                            ?>
                                <tr class="gradeA <?php echo $class;?>" role="row">
                                    <td><?php echo $cl->org_name;?></td>
                                    <td><?php echo $cl->user_name;?></td>
                                    <td><?php echo $cl->email;?></td>
                                    <td class="center"><?php echo $cl->motors;?></td>
                                    <td class="center"><?php if($cl->subscription_status == 0){?>No<?php }else{?>Yes<?php }?></td>
                                    <td class="center">
                                        <a href="<?php echo $url.'/client/edit/'.$cl->org_id.'/'.$page_number;?>"><i class="fa fa-edit" title="Edit"></i></a>
                                        <?php if($cl->client_status == 'active'){?>
                                        <i class="fa fa-check-circle" title="Active"></i>
                                        <?php }else{ ?>
                                        <i class="fa fa-times-circle-o" title="Inactive"> </i>    
                                        <?php }?>
                                        <!--<i class="fa fa-minus-circle"></i>
                                        <a href="<?php echo $url.'/client/hierarchy/'.$cl->org_id.'/'.$page_number;?>"><i class="fa fa-sitemap" title="Hierarchy"></i></a>-->
                                    </td>
                                </tr>
                            <?php
                                }
                            }else{
                            ?>
                                <tr class="gradeA odd" role="row">
                                    <td class="sorting_1" colspan="6">No data please.</td>
                                </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table></div></div>
                            <?php if($pagination){?>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                        <?php echo $pagination;?>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                    <!-- /.table-responsive -->
                    
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</section>
	
	