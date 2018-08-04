
<!DOCTYPE html>
<html lang="en">
<head>
    
    <?php $this->load->view('head');?>

    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">
    <script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>

    
    <!-- fusion chart js -->
    <script type="text/javascript" language="javascript" src="<?php echo base_url();?>resource/fchart/js/fusioncharts.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo base_url();?>resource/fchart/js/jquery_fusion.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo base_url();?>resource/fchart/js/themes/fusioncharts.theme.fint.js"></script>

    
    <script src="<?php echo base_url();?>resource/date-time/moment.js"></script>
    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.js"></script>

     <script src="<?php echo base_url();?>resource/js/timer.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
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
        $.post( "<?php echo base_url();?>ems/dashboard/liveRefresh/<?php echo $typeId;?>",{"<?php echo $this->security->get_csrf_token_name(); ?>" : "<?php echo $this->security->get_csrf_hash(); ?>"}, function( data ) {
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

            <h4><strong>Electricity Live Dashboard</strong> 
                <span style="font-size: 10px;">(Page will update after <span class="timer" id="timerId">&nbsp;</span>)
                </span>


                <a href="<?php echo base_url('ems/dashboard');?>" style="float: right;" class="btn btn-primary blbtn">Back</a>
                <span style="float: right;">&nbsp;</span>
                <a href="<?php echo base_url();?>ems/report/index/<?php echo $typeId;?>" style="float: right;" class="btn btn-primary blbtn">Report</a>
                <!-- <span style="float: right;">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span>
                <a href="<?php echo base_url('ems/report');?>" style="float: right;;">Day Wise Report</a> -->
            </h4>            
            <br/>
           
            <div id="allDataId">
                <?php echo $all;?>
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

    <?php  $this->load->view('footer');?>
    <br/>
</body>
</html> 

