<form role="form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            Update You Tube embed Link
            <!--  -->
        </div>
            <!-- /.panel-heading -->
        <div class="panel-body">
                <!-- Tab panes -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="hidden" name="id" value="youtube">
                        <input class="form-control" type="text" name="you_tube_link" value="<?php echo $set['you_tube_link'];?>">
                        <p>e.g. https://www.youtube.com/embed/ICIKly4Mh4k</p>
                    </div>
                    <button type="submit" value="youtube" class="btn btn-default btn-success">Update</button>
                </div>
                <!-- /.col-lg-6 (nested) -->
            </div>
                    
        </div>
            <!-- /.panel-body -->
    </div>
</form>