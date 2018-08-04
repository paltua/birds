<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view('head');?>
    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">

    <script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>

    <script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>

    <script type="text/javascript" language="javascript" src="<?php echo base_url();?>resource/fchart/js/fusioncharts.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>resource/fchart/js/jquery_fusion.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>resource/fchart/js/themes/fusioncharts.theme.fint.js"></script>
	<script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.css"></script>
	<script src="<?php echo base_url();?>resource/date-time/moment.js"></script>
	<script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.js"></script>

    <script src="<?php echo base_url();?>resource/js/timer.js"></script>

    <script type="text/javascript">    

    function changeSection(sectionname){

        //alert(sectionname);
        if(sectionname=='air'){
            window.location.href = '<?php echo base_url();?>anomaly/reportair';
        }else if(sectionname=='steam'){
            window.location.href = '<?php echo base_url();?>anomaly/reportsteam';
        }else if(sectionname=='ems'){
            window.location.href = '<?php echo base_url();?>anomaly/reportems';
        }else{
            alert('Something Wrong!! Please Try Again After SomeTime.')
        }
    }

    $(document).ready(function(){
        $('#allDataId').on('click','.showChart', function() { 
            var meter_link = $(this).attr("meter-link");
            $('.myMeterModal').modal('show');
            $('.myMeterModal').find(".modal-content").load(meter_link);
        });
    
    
    $('#datepicker').datetimepicker({
        useCurrent: false,
        sideBySide: true,
        format: 'YYYY-MM-DD',       
        <?php if($dateRange["selectedDate"] != ''){?>
        defaultDate : '<?php //echo date("Y-m-d", strtotime($dateRange["selectedDate"]));?>',
        <?php }?>
        <?php if($dateRange["min_date"] != ''){?>
        minDate : '<?php echo date("Y-m-d", strtotime($dateRange["min_date"])); ?>',
        <?php }?>
        <?php if($dateRange["max_date"] != ''){?>
        maxDate : '<?php echo date("Y-m-d", strtotime($dateRange["max_date"])); ?>',
        <?php }?>
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar-check-o",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down"
        }

    });

    $('#datepicker1').datetimepicker({
        useCurrent: false,
        sideBySide: true,
        format: 'YYYY-MM-DD',       
        <?php if($dateRange["selectedDate"] != ''){?>
        defaultDate : '<?php //echo date("Y-m-d", strtotime($dateRange["selectedDate"]));?>',
        <?php }?>
        <?php if($dateRange["min_date"] != ''){?>
        minDate : '<?php echo date("Y-m-d", strtotime($dateRange["min_date"])); ?>',
        <?php }?>
        <?php if($dateRange["max_date"] != ''){?>
        maxDate : '<?php echo date("Y-m-d", strtotime($dateRange["max_date"])); ?>',
        <?php }?>
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar-check-o",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down"
        }

    });
});


    </script>
	<style type="text/css">
		.panel-title {
		    font-size: 18px;
		    font-weight: normal !important;
		    position: relative;
		}
		.panel-heading {
		    padding: 7px 15px;
		    border-radius: 0;
		    border:none;
		    background: none;
		}
		.iconicbac {
		  padding: 30px 0 80px 0;
		}
	</style>
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
            <h4><strong>Anomaly Tendency</strong>
            <!-- <a href="<?php echo base_url('air/dashboard');?>" style="float: right;;">Live Dashboard</a> -->
            </h4>
            <!-- <div>Page will update after <span class="timer" id="timerId">&nbsp;</span></div> -->
                <div class="row">
                    <form name="frmReport" action="" method="post">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <br/><br/>
                        
                        <div class="col-xs-2">
                            Select Section <br/>
                            <select name="sectionname" class="form-control" onchange="changeSection(this.value);" required>
                                <option value="ems" <?php echo ($this->uri->segment(2)=="reportems") ? 'selected':'';?>>Electricity</option>
                                <option value="steam" <?php echo ($this->uri->segment(2)=="reportsteam") ? 'selected':'';?>>Steam</option>
                                <option value="air" <?php echo ($this->uri->segment(2)=="reportair") ? 'selected':'';?>>Compressed Air</option>
                            </select>
                        </div>
                        
                        <div class="col-lg-3">
                            <label for="email">Select Meter :</label>
                            <select name="meter" class="form-control" required>
                                <option value="">Select Meter</option>
                                <?php
                                    if(count($get_meter)>0){
                                        foreach ($get_meter as $MeterObjkey => $meterObjvalue) {
                                    ?>
                                    <option value="<?php echo $meterObjvalue->meter_id;?>" <?php echo (isset($_POST['meter']) && ($meterObjvalue->meter_id==$_POST['meter'])) ? 'selected': '';?>><?php echo $meterObjvalue->name;?></option>
                                    <?php }}?>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="email">Start Date :</label>
                            <input type="date" required name="startDate" id="datepicker" value="<?php echo isset($_POST['startDate']) ? $_POST['startDate'] : '';?>" class="form-control">
                            
                        </div>
                        <div class="col-lg-2">
                            
                            <label for="email">End Date :</label>
                            <input type="date" required name="endDate" id="datepicker1" value="<?php echo isset($_POST['endDate']) ? $_POST['endDate'] : '';?>" class="form-control">
                        </div>

                        
                        <div class="col-lg-2"><br/>
                            <input type="submit" name="btnSearch" value="Go" class="btn btn-primary">
                        </div>
                    </form>
                </div><br/><br/>
                <div id="allDataId">
                    <?php  
                    
                    if($all!=""){
                        echo $all;
                    }
                    ?>
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
</body>
</html> 