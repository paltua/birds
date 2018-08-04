<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Birds Category</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Add
            </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs">
                        <?php if(count($lang) > 0){
                                foreach ($lang as $key => $value) {
                        ?>
                        <li class="<?php if($key == 'en'):?>active<?php endif;?>">
                            <a href="#<?php echo $key;?>" data-toggle="tab"><?php echo $value;?></a>
                        </li>
                        <?php }} ?>
                        
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <?php if(count($lang) > 0){
                                foreach ($lang as $key => $value) {
                        ?>
                        <div class="tab-pane fade in <?php if($key == 'en'):?>active<?php endif;?>" id="<?php echo $key;?>">
                            <h4><?php echo $value;?></h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <form role="form" method="post">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input class="form-control" type="text" name="bcmd_name[<?php echo $key;?>]">
                                            <p class="help-block">Example block-level help text here.</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Short Description</label>
                                            <textarea class="form-control" rows="3" name="bcmd_short_desc[<?php echo $key;?>]"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Staus</label>
                                            <label class="radio-inline">
                                                <input name="bcmd_status[<?php echo $key;?>]" id="bcmd_status1" value="active" checked="" type="radio">Active
                                            </label>
                                            <label class="radio-inline">
                                                <input name="bcmd_status[<?php echo $key;?>]" id="bcmd_status2" value="inactive" type="radio">Inactive
                                            </label>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                        </div>
                        <?php }} ?>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
            
        </div>
    </div>
</div>