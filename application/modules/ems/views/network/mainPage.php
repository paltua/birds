<div class="row">

    <form name="frmReport" action="" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="col-lg-4">
            <label>Location : <span class="red">*</span></label>
            <select class="form-control" name="p_device_id" id="p_device_id">
                <option value="">Select One</option>
                <?php if(count($pDevice) > 0){?>
                    <?php foreach($pDevice as $val){ $selected = '';if($pDeviceSelected == $val->device_id){ $selected = 'selected';}?>
                    <option value="<?php echo $val->device_id;?>" <?php echo $selected;?>><?php echo $val->device_name;?></option>
                    <?php }?>
                <?php }?>    
            </select>
        </div>
        <div class="col-lg-3">
            <label>Select Start Date:<span class="red">*</span></label>
            <input class="form-control" name="startDate" id="datepicker" value="<?php echo $dateRange["selectedDate"];?>" type="date">
        </div>
        <div class="col-lg-3">
            <label>Select End Date:<span class="red">*</span></label>
            <input class="form-control" name="endDate" id="datepicker_end" value="<?php echo $dateRange["selectedDateEnd"];?>" type="date">
        </div>
        
        <div class="col-lg-2">
            <input type="submit" name="btnSearch" class="btn btn-primary" value="Go">
        </div>
    </form>
</div>
<br>
<?php 
$parentLoss = 0;
$TinLoss = 0;
$ToutLoss = 0;
$TinKwArray = array();
$TinKwPfArray = array();
$ToutKwArray = array();
$capacity = 0;
?>
<div class="panel panel-info" id="netAllMeterDiv">
    <div class="panel-heading">NetWork Diagnostic</div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
            <thead>
              <tr role="row">
                <?php if(count($transDetails['parent']) > 0){?>
                <th style="">
                    <p>Meter Name</p>
                 </th>
                
                <th style="">
                    <p>KW</p>
                </th>
                <?php }else{?>
                <th style="">
                    <?php $errorData = '<span style="color:red;">'.$msg.'</span>';?>
                    <p><?php echo ($msg != '')?$errorData:'No Data Please.';?></p>
                 </th>
                <?php }?>
              
            </tr>
            </thead>
            <?php //print_r($viewParentData)  ;?>
            <tbody> 
                <?php if(count($viewParentData) > 0){?>
                <tr role="row" class="odd">
                    <th style="">
                      <p><?php echo $viewParentData['name'];?></p>
                    </th>
                   
                    <td>
                        <?php $ssInVal = 'Meter not connected.'; 
                        if(isset($viewParentData['KW'])){
                        ?>
                        <p><a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>ems/network/showNetGraphKw/<?php echo $viewParentData['device_id'];?>/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" device-id="<?php echo $viewParentData['device_id'];?>"><?php $parentLoss = round($viewParentData['KW'], 2);?>
                            <?php echo number_format((float)round($viewParentData['KW'],3), 2, '.', '');?>
                        </a></p>
                        <?php }else{?>
                            <?php echo $ssInVal;?>
                        <?php } ?>
                    </td>
                </tr>
                <?php }?>
<?php //print_r($viewTinData);?>
                <?php if(count($transDetails['in']) > 0){
                    $capacityA = array();?>
                <?php foreach ($transDetails['in'] as $key => $value) { 
                    $capacityA[$value['id']] = isset($viewTinData[$value['id']]['capacity'])?$viewTinData[$value['id']]['capacity']:0;?>
                    <tr role="row" class="odd">
                    <th style="">
                      <p><?php echo $value['name'];?></p>
                    </th>
                   
                    <td>
                        <p>
                            <?php 
                            $ssInVal = 'Meter not connected.';
                            if(isset($viewTinData[$value['id']])){
                                $ssInVal = round($viewTinData[$value['id']]['KW'], 2);
                            }

                            $TempTinLoss = $ssInVal;
                            if($ssInVal != 'Meter not connected.'){
                                $TinKwArray[$value['id']]['KW'] = $TempTinLoss;
                                if($TempTinLoss > 0){
                                    $TinKwPfArray[$value['id']]['KWPF'] = $viewTinData[$value['id']]['KWPF'];
                                }
                                $TinKwArray[$value['id']]['mnc'] = "";
                            }else{
                                $TinKwArray[$value['id']]['mnc'] = "MNC";
                            }                                    
                            ?>
                            <?php if($ssInVal != 'Meter not connected.'){?>
                                <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>ems/network/showNetGraphKw/<?php echo $value['id'];?>/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" device-id="<?php echo $value['id'];?>">
                                    <?php echo number_format((float)$TempTinLoss, 2, '.', '');?>
                                </a>    
                                <?php }else{ ?>
                                    <?php echo $ssInVal;?>
                                <?php }?>
                            
                        
                        </p>
                    </td>
                </tr>
                <?php  
                //if($value['id'] == 73){$TinLoss = $TinLoss + 0;}else{$TinLoss = $TinLoss + $TempTinLoss;}
                $TinLoss = $TinLoss + $TempTinLoss;
                } ?>

                <?php } ?>

                <?php if(count($transDetails['out']) > 0){?>
                <?php foreach ($transDetails['out'] as $key => $value) {?>
                    <tr role="row" class="odd">
                    <th style="">
                      <p><?php echo $value['name'];?></p>
                    </th>
                   
                    <td>
                        <p>
                            <?php 
                            $ssInVal = 'Meter not connected.';
                            if(isset($viewToutData[$value['id']])){
                                $ssInVal = round($viewToutData[$value['id']]['KW'], 2);
                            }

                            $tempToutLoss = $ssInVal;
                            if($ssInVal != 'Meter not connected.'){
                                $ToutKwArray[$value['id']]['KW'] = $tempToutLoss;
                                $ToutKwArray[$value['id']]['mnc'] = '';
                            }else{
                                $ToutKwArray[$value['id']]['mnc'] = 'MNC';
                            }     
                            ?>
                            <?php 
                                if($ssInVal != 'Meter not connected.'){?>
                                <a href="javascript:void(0);" class="showChart" meter-link="<?php echo base_url();?>ems/network/showNetGraphKw/<?php echo $value['id'];?>/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" device-id="<?php echo $value['id'];?>">
                                    <?php echo number_format((float)$tempToutLoss, 2, '.', '');?>
                                </a>    
                                <?php }else{
                                    echo $ssInVal;
                                }
                            ?>
                        
                        </p>
                    </td>
                </tr>
                <?php  $ToutLoss = $ToutLoss + $tempToutLoss; } ?>

                <?php } ?>

              
            </tbody>
        </table>
    </div>
</div>
<?php if(count($viewParentData) > 0){?>
<div class="panel panel-info">
    <div class="panel-heading">Loss</div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
            <thead>
              <tr role="row">
                <th style="">
                    <p>Description</p>
                 </th>
                
                <th style="">
                    <p>KW</p>
                </th>
                <th style="">
                    <p>%</p>
                </th>
            </tr>
            </thead>
            <?php //print_r($viewParentData)  ;?>
            <tbody> 
                
                <tr role="row" class="odd">
                    <th style="">
                      <p>Line Loss</p>
                    </th>
                   
                    <td>
                        <p>
                            <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphLineLoss/<?php echo $pDeviceSelected;?>/abs/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" class="showChart">
                                <?php $tempLineLossKw = round($parentLoss - $TinLoss,2);?>
                                <?php echo number_format((float)$tempLineLossKw, 2, '.', '');?>
                            </a></p>
                    </td>

                    <td>
                        <p>
                            <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphLineLoss/<?php echo $pDeviceSelected;?>/per/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" class="showChart">
                        <?php $tempLineLossKwPer =  ($parentLoss > 0)?round((($parentLoss - $TinLoss)/$parentLoss) * 100,2):0;?>
                                <?php echo number_format((float)$tempLineLossKwPer, 2, '.', '');?>
                            </a></p>
                    </td>
                </tr>

                <tr role="row" class="odd">
                    <th style="">
                      <p>Transformer Loss</p>
                    </th>
                   
                    <td>
                        <p><a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphTransformerLoss/<?php echo $pDeviceSelected;?>/abs/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" class="showChart">
                            <?php $tempTransLossKw = round($TinLoss - $ToutLoss,2);?>
                            <?php echo number_format((float)$tempTransLossKw, 2, '.', '');?>    
                            </a></p>
                    </td>
                    <td>
                        <p>
                            <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphTransformerLoss/<?php echo $pDeviceSelected;?>/per/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" class="showChart">
                                <?php $tempTransLossKwPer = ($TinLoss > 0)?round(((($TinLoss - $ToutLoss)/$TinLoss) * 100),2):0;?>
                                    <?php echo number_format((float)$tempTransLossKwPer, 2, '.', '');?> 
                                </a></p>
                    </td>
                </tr>

                <tr role="row" class="odd">
                    <th style="">
                      <p>Total Loss</p>
                    </th>
                   
                    <td>
                        <p>
                            <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphTotalLoss/<?php echo $pDeviceSelected;?>/abs/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" class="showChart">
                                <?php $tempTotalLossKw = round($parentLoss - $ToutLoss,2);?>
                                    <?php echo number_format((float)$tempTotalLossKw, 2, '.', '');?>
                                </a></p>
                    </td>
                    <td>
                        <p>
                            <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphTotalLoss/<?php echo $pDeviceSelected;?>/per/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" class="showChart">
                                <?php $tempTotalLossKwPer = ($TinLoss > 0)?round((($parentLoss - $ToutLoss)/$parentLoss)*100,2):0;?>
                                <?php echo number_format((float)$tempTotalLossKwPer, 2, '.', '');?>
                            </a></p>
                    </td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">Transformer Details</div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
            <thead>
              <tr role="row">
                <th style="">
                    <p>Description</p>
                 </th>
                
                <th style="">
                    <p>Loss (KW)</p>
                </th>
                <th style="">
                    <p>Loss (%)</p>
                </th>
                <th style="">
                    <p>Loading (%)</p>
                </th>
            </tr>
            </thead>
            <?php //echo $capacity;
            //echo "<pre>"; 
            //print_r($TinKwArray)  ;
            //print_r($ToutKwArray)  ;
            ?>
            <tbody> 
                <?php if(count($transDetails['inout']) > 0){
                    foreach ($transDetails['inout'] as $key => $value) {
                       
                 ?>      
                <tr role="row" class="odd">
                    <th style="">
                      <p> <?php echo $value['name'];?></p>
                    </th>
                   
                    <td>
                        <p><?php 
                        $newData = 'N/A';
                        if($TinKwArray[$value['t_in_device_id']]['mnc'] != 'MNC' && $ToutKwArray[$value['t_out_device_id']]['mnc'] != 'MNC' ){
                            $newData = round($TinKwArray[$value['t_in_device_id']]['KW'] - $ToutKwArray[$value['t_out_device_id']]['KW'], 2);
                        }
                        if($newData == 'N/A'){
                            echo $newData ;
                        }else{?>
                        <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphTransformerDetailsLoss/<?php echo $pDeviceSelected;?>/<?php echo $key;?>/abs/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" class="showChart">
                        <?php //echo $newData ;?>
                        <?php echo number_format((float)$newData, 2, '.', '');?>
                        </a>
                        <?php } ?></p>
                    </td>
                    <td>
                        <p>
                            <?php if($newData != 'N/A'){?>
                            <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphTransformerDetailsLoss/<?php echo $pDeviceSelected;?>/<?php echo $key;?>/per/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" class="showChart">
                                <?php //echo round(($newData/$TinKwArray[$value['t_in_device_id']]['KW'])*100,2);?>
                                <?php echo number_format((float)round(($newData/$TinKwArray[$value['t_in_device_id']]['KW'])*100,2), 2, '.', '');?>
                            </a>
                            <?php }else{ echo $newData; }?>
                        </p>
                    </td>
                    <td>
                        <p><?php 
                        //echo $value.'=='.$capacity.'==';
                            $showD = 'N/A';
                            if($TinKwArray[$value['t_in_device_id']]['mnc'] != 'MNC' && $ToutKwArray[$value['t_out_device_id']]['mnc'] != 'MNC' ){
                                $temPT = (isset($TinKwPfArray[$value['t_in_device_id']]['KWPF'])?$TinKwPfArray[$value['t_in_device_id']]['KWPF']:0);
                                if($temPT != 0){
                                    $showD =  round((($TinKwArray[$value['t_in_device_id']]['KW']/$temPT)/$capacityA[$value['t_in_device_id']]) * 100, 2);
                                }
                            }
                            if($showD == 'N/A'){
                                echo $showD;
                            }else{?>
                            <a href="javascript:void(0);" meter-link="<?php echo base_url();?>ems/network/showGraphTransformerDetailsLoading/<?php echo $pDeviceSelected;?>/<?php echo $key;?>/<?php echo strtotime($dateRange["selectedDate"]);?>/<?php echo strtotime($dateRange["selectedDateEnd"]);?>" class="showChart">
                                <?php //echo $showD;?>
                                <?php echo number_format((float)$showD, 2, '.', '');?>
                            </a>
                            <?php } ?></p>
                        
                    </td>

                </tr>
                <?php }}?>
                
            </tbody>
        </table>
    </div>
</div>
<?php }?>
<script type="text/javascript">
    $(document).ready(function(){
        /*$('#netAllMeterDiv').on('click','.netGraphKw', function() { 
            var device_id = $(this).attr("device-id");
            var startDate = '<?php echo strtotime($dateRange["selectedDate"]);?>';
            var endDate = '<?php echo strtotime($dateRange["selectedDateEnd"]);?>';
            var meter_link = '<?php echo base_url();?>ems/network/showNetGraphKw/'+device_id+'/'+startDate+'/'+endDate;
            $('.myMeterModal').modal('show');
            $('.myMeterModal').find(".modal-content").load(meter_link);
        });*/
    });
</script>
