<script type="text/javascript" language="javascript"  src="<?php echo base_url();?>node_modules/socket.io/node_modules/socket.io-client/socket.io.js"></script>

<div class="modal-header">
    <button type="button" class="close cls-modal" data-dismiss="modal" aria-label="Close"><img src="<?php echo base_url();?>resources/<?php echo CURRENT_THEME;?>/img/cross.png"></button>
    <h4 class="modal-title"><img src="<?php echo base_url();?>resources/<?php echo CURRENT_THEME;?>/img/st_2.png" class="icn">Point Management for <?php echo $org_name;?></h4>
</div>
<div id="addMsg"></div>
<div class="modal-body">
<div class="mBot20 clearfix">
    <div class="col-md-12"><h5 class="undline">Points Summary</h5></div>
    <div class="col-md-6">
        
        <h5>Points Available  : <span class="" id="point_available"><?php echo $point_ava;?></span> </h5>
        <h5>Points Used   : <?php echo $point_spent;?></h5>
    </div>
    <div class="col-md-6">
        <h5>Request for Points : <span class=""><?php echo $point_request;?></span></h5>
        <table class="noBorder">
            <tr>
                <td>Add Points : </td>
            </tr>
            <tr>
                <td class="pdTop15">
                    <div class="input-group">
                      <input type="text" name="add_point" id="add_point" class="form-control numeric" placeholder="" aria-describedby="basic-addon2">
                      <span class="input-group-addon btn btn-success" id="add_more_point_buttun">Add Points</span>
                    </div>
                    <span id="add_point_span"></span>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="col-md-12">
    <div class="mTop15">
        <h5 class="undline">Services Summary</h5>
        <form name="service_form" id="service_form" method="post">
            <table class="table table-orange no-footer smallheading" style="border-radius:8px;margin-top:20px; overflow:hidden">
               <thead>
                <tr>
                   <th></th>
                    <th width="">Services</th>
                    <th width="250px">Expiry</th>
                    <!-- <th>Units</th> -->
                    <th width="231px">Points/unit</th>
                    <!-- <th>Total Points</th> -->
                </tr>
                </thead>
                <?php if(count($assignServices) > 0):?>
                    <tbody>
                    <?php  foreach($assignServices as $v):?>
                <tr>
                    <td><input type="checkbox" class="services" name="service[<?php echo $org_id;?>][<?php echo $v->service_id;?>]" id="<?php echo $v->service_id;?>" value="yes" <?php if($v->id != ''){?> checked <?php }?>></td>
                    <td><?php echo $v->name;?></td>
                    <td>
                        <div class="input-group">
                        	<?php if($v->service_expiry == 1){?>
                        		<input type="text" class="form-control numeric" name="expiry[<?php echo $org_id;?>][<?php echo $v->service_id;?>]" id="expiry_<?php echo $v->service_id;?>" value="<?php echo $v->expiry_days;?>" aria-describedby="basic-addon2" data-id="<?php echo $v->service_id;?>">
                          		<span class="input-group-addon" id="basic-addon2">Days</span>
                        	<?php }elseif ($v->service_expiry == 0) { ?>
                        		<!-- Not expired -->
                        		<input type="hidden" class="form-control numeric" name="expiry[<?php echo $org_id;?>][<?php echo $v->service_id;?>]" id="expiry_<?php echo $v->service_id;?>" value="0" aria-describedby="basic-addon2" data-id="<?php echo $v->service_id;?>">
                        	<?php } ?>
                          
                        </div>
                        <span id="span_expiry_<?php echo $v->service_id;?>" class="error-text"></span>
                    </td>
                    <td>
                        <?php if($v->service_id == '4'){?>
                        <div class="form-group pull-left" style="width:100px;margin-right:5px">
                            <input type="text" class="form-control points" name="points[<?php echo $org_id;?>][<?php echo $v->service_id;?>]" id="points_<?php echo $v->service_id;?>" value="<?php echo $v->points;?>" data-id="<?php echo $v->service_id;?>">
                            <span id="span_points_<?php echo $v->service_id;?>" class="error-text"></span>
                        </div>
                        

                        <div class="form-group pull-left" style="width:103px">
                            <select class="form-control" name="cm_id[<?php echo $org_id;?>][<?php echo $v->service_id;?>]">
                                <?php if(count($currency) > 0){
                                    foreach ($currency as $key => $value) { ?>
                                        <option value="<?php echo $value->cm_id;?>" <?php if($value->cm_id == $v->cm_id){?>selected="selected" <?php }?>><?php echo $value->iso_code.'('.$value->html_code.')';?></option> 
                                    <?php } } ?>
                                
                            </select>
                        </div>
                        <div style="clear:both"></div>


                        <?php }else{?>
                        <div class="form-group pull-left" style="width:100%;margin-right:5px">
                            <input type="text" class="form-control points" name="points[<?php echo $org_id;?>][<?php echo $v->service_id;?>]" id="points_<?php echo $v->service_id;?>" value="<?php echo $v->points;?>" data-id="<?php echo $v->service_id;?>">
                            <span id="span_points_<?php echo $v->service_id;?>" class="error-text"></span>
                        </div>

                        <?php }?>
                    </td>
                </tr>
    <input type="hidden" class="form-control units" name="units[<?php echo $org_id;?>][<?php echo $v->service_id;?>]" id="units_<?php echo $v->service_id;?>" value="<?php echo $v->units;?>" data-id="<?php echo $v->service_id;?>">
    <input type="hidden" class="form-control" id="total_<?php echo $v->service_id;?>" value="<?php echo $v->total_points;?>">
                <?php endforeach;?>
                </tbody>
                 <?php  else : ?>


                <?php endif;?>
            </table> 
        
        <input type="hidden" name="org_id" id="org_id" value="<?php echo $org_id;?>">
        </form>
    </div>
    </div>
</div>
      <div class="modal-footer">
        
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="add_services_data" class="btn btn-primary" style="margin-right: 15px;">Save changes</button>
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

        $("#add_more_point_buttun").click(function(){
            addMorePoints();
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
            url:"<?php echo base_url($url);?>/client/addServices",
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

    function addMorePoints(){
        $("#add_point").removeClass('contrlRed');
        $("#add_point_span").removeClass('msgValidation');
        $("#add_point_span").html("");
        var org_id = '<?php echo $org_id;?>';
        var point = $("#add_point").val();
        var user_id = '<?php echo $user_id;?>';
        if(org_id > 0 && point > 0){
            $.ajax({
                url:"<?php echo base_url($url);?>/client/addPoints",
                type : "post",
                async : false,
                data : "org_id="+org_id+"&point="+point+"&user_id="+user_id,
                success:function(data){
                    res = $.parseJSON(data);
                    if(res.msg != ''){

                        var total_ava = parseInt($("#point_available").text()) + parseInt(point);
                        $("#point_available").text(total_ava);
                        $("#addMsg").html(res.msg);
                        $("#addMsg").show();
                        $("#add_point").val('');

                        if(res.notif_arr){
                            var data = res.notif_arr;
                            var socket = io.connect( 'http://'+window.location.hostname+':<?php echo NODE_JS_PORT;?>' );
                            socket.emit('new_count_message', { 
                              new_count_message: data.new_count_message,
                              to_user_id: data.to_user_id,
                              from_user_id: data.from_user_id
                            });

                            socket.emit('new_message', { 
                              description: data.description,
                              from_user_name : data.from_user_name,
                              to_user_id: data.to_user_id,
                              from_user_id: data.from_user_id,
                              notification_id:data.notification_id,
                              created_date:data.created_date
                            });
                        }

                        setTimeout('$("#addMsg").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
                    }
                }
            });
        }else{
            if(point == ""){
                $("#add_point").addClass('contrlRed');
                $("#add_point_span").addClass('msgValidation');
                $("#add_point_span").html("Please enter the point.");
            }
        }
    }

    

    </script>