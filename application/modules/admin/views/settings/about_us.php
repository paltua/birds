<form role="form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
        value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="about_us">
    <div class="panel panel-default">
        <div class="panel-heading">
            About us
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <!-- Nav tabs -->

            <div class="tab-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">

                            <textarea class="form-control contentTextarea"
                                name="about_us"> <?php echo $set['about_us'];?></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" value="about_birds" class="btn btn-default btn-success">Update</button>
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
</form>