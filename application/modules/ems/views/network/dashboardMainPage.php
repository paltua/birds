<div class="row">
    <form name="frmReport" action="" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="col-lg-4">
            <label>Rating : <span class="red">*</span></label>
            <select class="form-control" name="t_in_device_capacity" id="t_in_device_capacity">
                <option value="">All</option>
                <?php if(count($capacity) > 0){?>
                    <?php foreach($capacity as $val){ 
                        $selected = '';
                        if($selectedCapacity == $val->t_in_device_capacity){ 
                            $selected = 'selected';
                        }
                    ?>
                    <option value="<?php echo $val->t_in_device_capacity;?>" <?php echo $selected;?>><?php echo $val->t_in_device_capacity;?></option>
                    <?php }?>
                <?php }?>    
            </select>
        </div>
        <div class="col-lg-3">
            <label>Select Date:<span class="red">*</span></label>
            <input class="form-control" name="startDate" id="datepicker" value="<?php echo $datePickerStart;?>" type="date">
        </div>
        <div class="col-lg-3">
            <label>Select End Date:<span class="red">*</span></label>
            <input class="form-control" name="endDate" id="datepicker_end" value="<?php echo $datePickerEnd;?>" type="date">
        </div>
        <div class="col-lg-2">
            <input type="submit" name="btnSearch" class="btn btn-primary" value="Go">
        </div>
    </form>
</div>
<br>
<?php if(count($transDetails) > 0){?>
<div class="panel panel-default" id="netAllMeterDiv">
    <div class="panel-heading">Transformer Dashboard</div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
            <thead>
              <tr role="row">
                <th style="">
                    <p>Location</p>
                </th>
                <th style="">
                    <p>Transfoemer Name</p>
                </th>
                <th style="">
                    <p>Rating</p>
                </th>
                <th style="">
                    <p>HT KW</p>
                </th>
                <th style="">
                    <p>LT KW</p>
                </th>
                <th style="">
                    <p>Loading(%)</p>
                </th>
                <th style="">
                    <p>Loss(KW)</p>
                </th>
                <th style="">
                    <p>Loss(%)</p>
                </th>
              
            </tr>
            </thead>
            <tbody> 
                    
                    <?php foreach ($transDetails as $key => $value) {?>
                       
                    <tr role="row" class="odd">
                    <th style="">
                      <p><?php echo $value->device_name;?></p>
                    </th>
                    <th style="">
                      <p><?php echo $value->trans_name;?></p>
                    </th>
                    <td>
                        <p><?php echo number_format((float)$value->t_in_device_capacity, 2, '.', '');?></p>
                    </td>
                    <td>
                        <p>
                            <?php 
                                if(is_null($value->t_in_kw)){
                                        echo 'N/A';
                                }else{
                            ?>
                            <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphHtLtKw/in/<?php echo $value->id;?>/<?php echo $viewStartDate;?>/<?php echo $viewEndDate;?>" class="showChart">
                                    <?php  echo number_format((float)$value->t_in_kw, 2, '.', ''); ?>
                                    </a>
                            <?php } ?>
                             
                        </p>
                    </td>
                    <td>
                        <p>
                            <?php 
                                if(is_null($value->t_out_kw)){
                                    echo 'N/A';
                                }else{
                            ?>
                            <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphHtLtKw/out/<?php echo $value->id;?>/<?php echo $viewStartDate;?>/<?php echo $viewEndDate;?>" class="showChart">            
                                    <?php echo number_format((float)$value->t_out_kw, 2, '.', '');?>
                                    </a>
                            <?php } ?>
                        </p>
                    </td>
                    <td>
                        <p>    
                            <?php 
                                if(is_null($value->loading) || is_null($value->t_out_kw)){
                                    echo 'N/A';
                                }else{
                            ?>
                                <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphTransformerDetailsLoading/<?php echo $value->p_device_id;?>/<?php echo $value->id;?>/<?php echo $viewStartDate;?>/<?php echo $viewEndDate;?>" class="showChart">
                                <?php  echo number_format((float)$value->loading, 2, '.', ''); ?>
                                </a>
                            <?php } ?>
                        </p>
                    </td>
                    <td>
                        <p>
                            <?php 
                                if(is_null($value->loss_kw) || is_null($value->loss_per) || is_null($value->t_out_kw)){
                                    echo 'N/A';
                                }else{ 
                            ?>
                                <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphTransformerDetailsLoss/<?php echo $value->p_device_id;?>/<?php echo $value->id;?>/abs/<?php echo $viewStartDate;?>/<?php echo $viewEndDate;?>" class="showChart">
                                 <?php echo number_format((float)$value->loss_kw, 2, '.', '');?>
                                 </a>
                            <?php }?>    
                            
                        </p>
                    </td>
                    <td>
                        <p>
                            <?php 
                                if(is_null($value->loss_kw) || is_null($value->loss_per) || is_null($value->t_out_kw)){
                                        echo 'N/A';
                                }else{ 
                            ?>
                            <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphTransformerDetailsLoss/<?php echo $value->p_device_id;?>/<?php echo $value->id;?>/per/<?php echo $viewStartDate;?>/<?php echo $viewEndDate;?>" class="showChart">
                                     <?php echo number_format((float)$value->loss_per, 2, '.', ''); ?>
                                     </a>
                            <?php } ?>
                        </p>
                    </td>
                </tr>
               <?php }?>
            </tbody>
        </table>
    </div>
</div>
<?php }?>
