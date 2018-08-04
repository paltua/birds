


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

<!-- <script type="text/javascript" language="javascript" src="<?php echo base_url();?>resource/f-chart/fusioncharts.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>resource/f-chart/jquery_fusion.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>resource/f-chart/fusioncharts.theme.fint.js"></script> -->

    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.css"></script>
    <script src="<?php echo base_url();?>resource/date-time/moment.js"></script>
    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.js"></script>

    <script src="<?php echo base_url();?>resource/js/timer.js"></script>


    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>

    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">


<script src="<?php echo base_url();?>resource/am_chart/amcharts.js"></script>
    <script src="<?php echo base_url();?>resource/am_chart/serial.js"></script>
    <script src="<?php echo base_url();?>resource/am_chart/themes_light.js"></script>
    <script src="<?php echo base_url();?>resource/am_chart/export.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url();?>resource/am_chart/export.css" type="text/css" media="all" />
    <script src="<?php echo base_url();?>resource/am_chart/dataloader.min.js" type="text/javascript"></script>
    
<script type="text/javascript">    

$(document).ready(function(){
    
    /*$('#datepicker').datetimepicker({
        useCurrent: false,
        sideBySide: true,
        format: 'YYYY-MM-DD',
        <?php if($dateRange["min_date"] != ''){?>
        defaultDate : '<?php echo date("Y-m-d", strtotime($dateRange["min_date"]));?>',
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

    });*/

    var setTime = 900;
    var timer = new Timer({
        onstart : function(millisec) {
            var sec = Math.round(millisec / 1000);
            if(sec == 1){
                sec = sec+' Second';
            }else{
                sec = sec+' Seconds';
            }
            $("#timerId").text(sec);
        },
        ontick  : function(millisec) {
            var sec = Math.round(millisec / 1000);
            if(sec == 1){
                sec = sec+' Second';
            }else{
                sec = sec+' Seconds';
            }
            $("#timerId").text(sec);
        },
        
        onstop  : function() {
            //$("#timerId").text('stop');
        },
        onend   : function() {
            callNewDataSet();
            timer.start(setTime);
        }
    });
    
    timer.start(setTime);

});

function callNewDataSet(){
    $.post( "<?php echo base_url();?>air/dashboard/getNewDataSet",{"<?php echo $this->security->get_csrf_token_name(); ?>" : "<?php echo $this->security->get_csrf_hash(); ?>"}, function( data ) {
        if(data.status == 'success'){
            $("#allDataId").html('');
            $("#allDataId").html(data.all);
        }               
    },'json');
}

$(document).ready(function(){
    $('#allDataId').on('click','.showChart', function() { 
        var meter_link = $(this).attr("meter-link");
        $('.myMeterModal').modal('show');
        $('.myMeterModal').find(".modal-content").load(meter_link);
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
            <h4><strong>Compressed Air Live Dashboard</strong> 
                <!-- <span style="font-size: 10px;">(Page will update after <span class="timer" id="timerId">&nbsp;</span>)
                </span> -->


                 <a href="<?php echo base_url('air/report/shift');?>" style="float: right;;">Shift Wise Report</a>
                <span style="float: right;">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span>
                <a href="<?php echo base_url('air/report');?>" style="float: right;;">Day Wise Report</a> 
            </h4>
            <!-- <div>
                Data sets being shown for <?php //echo date('d-m-Y H:i:s',strtotime($startDateShow));?> to <?php //echo date('d-m-Y H:i:s',strtotime($endDateShow));?>
            </div> -->
            
            <br/><br/>
           
            <div id="allDataId">
                <?php echo $all;?>
                
                <div id="snackbar" class="alert alert-info">

                    <a href="javascript:void(0);" style="color:#fff;" class="showChart" meter-link="<?php echo base_url();?>air/report/getCFMreport_setmeter/<?php echo strtotime($dateRange["max_date"]);?>/<?php echo strtotime($dateRange["min_date"]);?>">
                        <strong>Compressor Network Digaonstics</strong></a>

                  <a href="#" class="close" data-dismiss="alert" aria-label="close" style="color:#fff;padding-left:10px;">&times;</a>
                </div>

                <script>

                    $( document ).ready(function() {
                        var x = document.getElementById("snackbar")
                        x.className = "show alert alert-info";
                        //setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
                    });
                </script>

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



