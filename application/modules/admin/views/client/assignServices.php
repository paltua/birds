<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 class="modal-title">Assign services for <?php echo $org_name;?></h3>
</div>
<div id="addMsg"></div>
<div class="modal-body clearfix">
        <form name="service_form" id="service_form" method="post">
        <div>
            <table class="table table-bordered">
                <tr>
                   <th></th>
                    <th width="150px">Services</th>
                    <th width="150px">Expiry</th>
                    <th>Units</th>
                    <th>Points/unit</th>
                    <th>Total Points</th>
                </tr>
                <?php if(count($assignServices) > 0):
                        foreach($assignServices as $v):?>
                <tr>
                    <td><input type="checkbox" class="services" name="service[<?php echo $org_id;?>][<?php echo $v->service_id;?>]" id="<?php echo $v->service_id;?>" value="yes" <?php if($v->id != ''){?> checked <?php }?>></td>
                    <td><?php echo $v->name;?></td>
                    <td>
                        <div class="input-group">
                          <input type="text" class="form-control numeric" name="expiry[<?php echo $org_id;?>][<?php echo $v->service_id;?>]" id="expiry_<?php echo $v->service_id;?>" value="<?php echo $v->expiry_days;?>" aria-describedby="basic-addon2" data-id="<?php echo $v->service_id;?>">
                          <span class="input-group-addon" id="basic-addon2">Days</span>
                        </div>
                        <span id="span_expiry_<?php echo $v->service_id;?>" class="error-text"></span>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control numeric units" name="units[<?php echo $org_id;?>][<?php echo $v->service_id;?>]" id="units_<?php echo $v->service_id;?>" value="<?php echo $v->units;?>" data-id="<?php echo $v->service_id;?>">
                            <span id="span_units_<?php echo $v->service_id;?>" class="error-text" ></span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control points" name="points[<?php echo $org_id;?>][<?php echo $v->service_id;?>]" id="points_<?php echo $v->service_id;?>" value="<?php echo $v->points;?>" data-id="<?php echo $v->service_id;?>">
                            <span id="span_points_<?php echo $v->service_id;?>" class="error-text"></span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control" id="total_<?php echo $v->service_id;?>" value="<?php echo $v->total_points;?>" readonly="readonly">
                        </div>
                    </td>
                </tr>
                <?php endforeach;
                        else : ?>


                <?php endif;?>
            </table> 
        </div>
        <input type="hidden" name="org_id" value="<?php echo $org_id;?>">
        </form>
      </div>
      <div class="modal-footer">
        
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="add_services_data" class="btn btn-primary">Save changes</button>
      </div>
	
	<script>
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                     = '<?php echo $this->security->get_csrf_hash(); ?>';
    $(document).ready(function(){
        $.ajaxSetup({
            data: csfrData
        });
        $(".numeric").keyup(function(){
            var _val = $(this).val();
            var _length = _val.length;
            var ln =_length-1;
            if(isNaN($(this).val())){
                $(this).val(_val.slice(0,ln));
            }
            
        });

        $(".points").keyup(function(){
            var _val = $(this).val();
            var _length = _val.length;
            var ln =_length-1;
            if(isNaN($(this).val())){
                $(this).val(_val.slice(0,ln));
            }
            
        });
        $(".points, .units").on('keyup',function(){
            var service_id = $(this).attr('data-id');
            calculationTotalPoints(service_id);
        });
        /* Remove error After validation completed*/
        $(".form-control").change(function(){
            var service_id = $(this).attr('data-id');
            var id = $(this).attr('id');
            $(this).removeClass('contrlRed');
            if(id == 'expiry_'+service_id){
                $("#span_expiry_"+service_id).removeClass('msgValidation');
                $("#span_expiry_"+service_id).html('');
            }
            if(id == 'units_'+service_id){
                $("#span_units_"+service_id).removeClass('msgValidation');
                $("#span_units_"+service_id).html('');
            }
            if(id == 'points_'+service_id){
                $("#span_points_"+service_id).removeClass('msgValidation');
                $("#span_points_"+service_id).html('');
            }
            
        });
        /* end */
        $("#add_services_data").click(function(){
            var submitStat = checkingAssign();
            if(submitStat === true){
                submitForm();
            }
        });

    });

    function checkingAssign(){
        var counter = 0;
        var checkedCounter = 0;
        $(".services").each(function(){
            var service_id = $(this).attr('id');
            if($(this).is(":checked")){
                checkedCounter++;
                if($("#expiry_"+service_id).val() == ''){
                    counter++;
                    $("#expiry_"+service_id).addClass('contrlRed');
                    $("#span_expiry_"+service_id).addClass('msgValidation');
                    $("#span_expiry_"+service_id).html('Please enter the value.');
                }
                if($("#units_"+service_id).val() == ''){
                    counter++;
                    $("#units_"+service_id).addClass('contrlRed');
                    $("#span_units_"+service_id).addClass('msgValidation');
                    $("#span_units_"+service_id).html('Please enter the value.');
                }
                if($("#points_"+service_id).val() == ''){
                    counter++;
                    $("#points_"+service_id).addClass('contrlRed');
                    $("#span_points_"+service_id).addClass('msgValidation');
                    $("#span_points_"+service_id).html('Please enter the value.');
                }
            }else{
                $("#expiry_"+service_id).removeClass('contrlRed');
                $("#units_"+service_id).removeClass('contrlRed');
                $("#points_"+service_id).removeClass('contrlRed');
                $("#expiry_"+service_id).val('');
                $("#units_"+service_id).val('');
                $("#points_"+service_id).val('');
                $("#total_"+service_id).val('');
                $("#span_expiry_"+service_id).removeClass('msgValidation');
                $("#span_expiry_"+service_id).html('');
                $("#span_units_"+service_id).removeClass('msgValidation');
                $("#span_units_"+service_id).html('');
                $("#span_points_"+service_id).removeClass('msgValidation');
                $("#span_points_"+service_id).html('');
            }
            
        });
        if(counter == 0){
            /*if(checkedCounter == 0){
                $("#addMsg").html('<div class="alert alert-warning" role="alert"><a class="close" aria-label="close" data-dismiss="alert" href="javascript:void(0)">×</a> Please select a services.</div>');
                return false;
            }else{
                return true;
            }    */   
            return true;     
        }else{
            return false;
        }
            
    }

    function calculationTotalPoints(service_id){
        var units = ($("#units_"+service_id).val() == ''?0:$("#units_"+service_id).val());
        var points = ($("#points_"+service_id).val() == ''?0:$("#points_"+service_id).val());
        $("#units_"+service_id).val(parseInt(units));
        $("#points_"+service_id).val(points);
        var tot = units * points;
        $("#total_"+service_id).val(tot);
    }

    function submitForm(){
        $.ajax({
            url:"<?php echo base_url($url);?>client/addServices",
            type : "post",
            async : false,
            data : $('#service_form').serialize(),
            success:function(data){
                res = $.parseJSON(data);
                if(res.msg != ''){
                    $("#addMsg").html(res.msg);
                    $("#addMsg").show();
                    setTimeout('$("#addMsg").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
                }
            }
        });
    }

    

    </script>