  <style>
   body{
    
     }
    .centered-form{
     margin-top: 60px;
     }
     .centered-form .panel{
     background: rgba(255, 255, 255, 0.8);
     box-shadow: rgba(0, 0, 0, 0.3) 20px 20px 20px;
    }
  </style> 
  <?php foreach($result as $data) { }?>
  <div class="container">
        <div class="row centered-form">
        <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                        <h3 class="panel-title">Edit User <small></small></h3>
                        </div>
                        <div class="panel-body">
                        <form role="form" method="post" action="<?php echo base_url().ADMIN_NAME;?>/user/edituser">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                       <input type="text" name="first_name" id="first_name" class="form-control input-sm" value="<?php echo $data->first_name;?>">
                                        <input type="hidden" name="aum_id" value="<?php echo $data->aum_id;?>">
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="last_name" class="form-control input-sm" value="<?php echo $data->last_name;?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                       <input type="email" name="email" id="email" class="form-control input-sm" value="<?php echo $data->email;?>">
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                       <input type="password" name="password" id="password" disabled class="form-control input-sm" value="<?php echo $data->password;?>">
                                   </div>
                               </div>
                           </div>

                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="phone_no" id="phone_no" class="form-control input-sm" value="<?php echo $data->phone_no;?>">
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <select class="form-control input-sm" name="role_id">
                                            <option value=''>---Select Role---</option>
                                                <?php foreach($list as $value) { ?>
                                                    <option value="<?php echo $value->arm_id ?>"<?php if(($value->arm_id)== ($data->role_id)){?> selected="selected" <?php } ?>>
                                                  <?php echo $value->role_name ?></option>
                                                <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="submit" value="Register" class="btn btn-info btn-block">
                        
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
  
