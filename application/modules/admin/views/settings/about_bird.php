
<link href="<?php echo base_url().$resourceNameAdmin;?>vendor/summernote/summernote-bs4.css" rel="stylesheet">
<!-- DataTables JavaScript -->
<script src="<?php echo base_url().$resourceNameAdmin;?>vendor/summernote/summernote-bs4.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.contentTextarea').summernote({
            height: 500,   //set editable area's height
            codemirror: { // codemirror options
                theme: 'monokai'
            }
        });
    });
</script>
<form role="form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="about_bird">
    <div class="panel panel-default">
        <div class="panel-heading">
            KNow More About Birds Content 
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
                <div class="tab-content">
                    <?php if(count($lang) > 0){
                            foreach ($lang as $key => $value) {
                    ?>
                    <div class="tab-pane fade in <?php if($key == 'en'):?>active<?php endif;?>" id="<?php echo $key;?>">
                        <h4><?php echo $value;?> Content</h4>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Short Description</label>
                                <textarea class="form-control" rows="5" name="about_bird_<?php echo $key;?>"> <?php echo $set['short_about_bird_'.$key];?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Long Description</label>
                                <textarea class="form-control contentTextarea" rows="35" name="about_bird_<?php echo $key;?>"> <?php echo $set['about_bird_'.$key];?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php }} ?>
                    <button type="submit" value="about_birds" class="btn btn-default btn-success">Update</button>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
</form>