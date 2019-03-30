
<div class="row">
    <?php if($msg != ''):?>
    <div class="col-lg-12">
    <?php echo $msg ;?>
    </div>
    <?php endif;?>
    <div class="col-lg-12 mt-3">
        <div class="card">
            <div class="card-header">Details</div>
            <div class="card-body">
                <!-- <div class="alert alert-success">
                    Name : <strong><?php echo $list[0]->name;?></strong> 
                </div> -->
                <span class="badge badge-info">Name : <?php echo $list[0]->name;?></span><br>
                <span class="badge badge-info">Email : <?php echo $list[0]->email;?></span><br>
                <span class="badge badge-info">Mobile : <?php echo $list[0]->mobile;?></span><br>
                <span class="badge badge-info">Content : <?php echo $list[0]->desccription;?></span>
            </div> 
        </div>
        <form>
            <div class="form-group">
                <label for="comment">Comment:</label>
                <textarea class="form-control" rows="5" id="comment"></textarea>
            </div>
            <button type="button" class="btn btn-primary">Send</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        </form>
    </div>
</div> 
   