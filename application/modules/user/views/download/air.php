<!DOCTYPE html>
<html lang="en">
<head>
<?php $this->load->view('head');?>
<link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">
<script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>
<script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>resource/chosen/chosen.min.css">
<script src="<?php echo base_url();?>resource/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript">    


function callNewDataSet(){
    $.post( "<?php echo base_url();?>air/dashboard/getNewDataSet",{"<?php echo $this->security->get_csrf_token_name(); ?>" : "<?php echo $this->security->get_csrf_hash(); ?>"}, function( data ) {
        if(data.status == 'success'){
            $("#allDataId").html('');
            $("#allDataId").html(data.all);
        }               
    },'json');

}

$(document).ready(function(){
    $("#p_device_id").chosen({no_results_text: "Oops, No Device found!"});
    $('#allDataId').on('click','.showChart', function() { 
        var meter_link = $(this).attr("meter-link");
        $('.myMeterModal').modal('show');
        $('.myMeterModal').find(".modal-content").load(meter_link);
    });
});
</script>
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
              <a class="navbar-brand" href="#">WELSPUN EDA</a>
            </div>
        <ul class="nav navbar-nav" style="width: 80%;">
           <?php $this->load->view('header');?>
        </ul>
        </div>
    </nav>
    <section class="pad">
        <div class="container">
            <?php if($msg != ''){?>
            <div class="alert alert-danger fade in alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                <strong>Danger!</strong> <?php echo $msg;?>
            </div>
            <?php }?>
            <div class="panel panel-default" >
                <div class="panel-heading"><h4><strong><?php echo $this->formHeader;?> Data Download Platform</strong></h4></div>
                <div class="panel-body">
                    <form name="frmReport" action="" method="post">
                    <div class="row">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="col-lg-4 form-group">
                            <label>Device Name : <span class="red">*</span></label>
                            <!-- <select class="form-control" name="p_device_id[]" id="p_device_id" multiple=""> -->
                            <select class="form-control" name="p_device_id" id="p_device_id">
                                <option value="">Select One</option>
                                <?php if(count($pDevice) > 0){?>
                                    <?php foreach($pDevice as $val){ $selected = '';if($pDeviceSelected == $val->meter_id){ $selected = 'selected';}?>
                                    <option value="<?php echo $val->meter_id;?>" <?php echo $selected;?>><?php echo $val->name;?></option>
                                    <?php }?>
                                <?php }?>    
                            </select>
                            <?php echo form_error('p_device_id', '<span style="color: red;">', '</span>'); ?>
                        </div>
                        <div class="col-lg-4 form-group">
                            <label>Select Start Date:<span class="red">*</span></label>
                            <input class="form-control" name="startDate" id="datepicker" max="<?php echo $currentDate;?>" value="<?php echo $startDateShow;?>" type="date">
                            <?php echo form_error('startDate', '<span style="color: red;">', '</span>'); ?>
                        </div>
                        <div class="col-lg-4 form-group">
                            <label>Select End Date:<span class="red">*</span></label>
                            <input class="form-control" name="endDate" id="datepicker_end" max="<?php echo $currentDate;?>" value="<?php echo $endDateShow;?>" type="date">
                            <?php echo form_error('endDate', '<span style="color: red;">', '</span>'); ?>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label>Field:<span class="red">*</span></label><br>
                            <?php foreach ($column as $key => $value) {
                                $checked = '';
                                if(in_array($value->Field, $colSelected)){
                                    $checked = 'checked';
                                }
                                $showValue = '';
                                ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="col[]" value="<?php echo $value->Field;?>" <?php echo $checked;?>> <?php echo getEmsData($value->Field);?>
                            </label>
                            <?php }?>
                        </div>
                    </div>  
                    <div class="row">    
                        <div class="col-lg-12">
                            <button type="submit" name="btnSearch" class="btn btn-success"> <i class="fa fa-download"></i> Download</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal -->
    <div id="myModal" class="modal fade myMeterModal " role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                
            </div>
        </div>
    </div>

    <?php $this->load->view('footer');?>
    <br/>
</body>
</html> 

