
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
        <form id="replyForm">
        <input type="hidden" name="user_email" value="<?php echo $list[0]->email;?>">
        <input type="hidden" name="user_name" value="<?php echo $list[0]->name;?>">
            <div class="form-group">
                <label for="comment">Comment:</label>
                <textarea class="form-control" name="message" id="message" rows="5" ></textarea>
            </div>
            <div class="col-lg-12" id="sucErrMsg">
            
            </div>
            <div class="col-lg-12" >
            <button type="button" class="btn btn-primary" id="send">Send</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div> 

<script type="text/javascript"> 
    $(document).ready(function(){
        $("#sucErrMsg").hide();
        var csfrData = {};
        csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                         = '<?php echo $this->security->get_csrf_hash(); ?>';
        //alert('<?php echo $this->security->get_csrf_hash(); ?>');
        $.ajaxSetup({
          data: csfrData
        });
        $("#send").click(function(){
            $("#sucErrMsg").html('');
            $("#sucErrMsg").show();
            $.post( "<?php echo base_url('admin/contact_us/sendEmailToUser');?>", 
            { user_email: '<?php echo $list[0]->email;?>',
                user_name: '<?php echo $list[0]->name;?>',
                con_id: '<?php echo $list[0]->con_id;?>',
                message: $("#message").val()
             }, 
            function( data ) {
                $("#sucErrMsg").html(data.msg);
                setTimeout(() => {
                    $("#message").val('');
                    $("#sucErrMsg").html('');
                    $("#sucErrMsg").hide(1000);
                    $('#myModal').modal('hide');
                }, 3000);
            }, "json");
        })
    });
</script>  

   