


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

    <!-- <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.css"></script>
    <script src="<?php echo base_url();?>resource/date-time/moment.js"></script>
    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.js"></script> -->

    <script src="<?php echo base_url();?>resource/js/timer.js"></script>

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
    $.post( "<?php echo base_url();?>fm/dashboard/getNewDataSet",{"<?php echo $this->security->get_csrf_token_name(); ?>" : "<?php echo $this->security->get_csrf_hash(); ?>"}, function( data ) {
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
            <h4><strong>Steam Live Dashboard</strong> 
                <!-- <span style="font-size: 10px;">(Page will update after <span class="timer" id="timerId">&nbsp;</span>)
                </span> -->


                <a href="<?php echo base_url('fm/report/shift');?>" style="float: right;;">Shift Wise Report</a>
                <span style="float: right;">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span>
                <a href="<?php echo base_url('fm/report');?>" style="float: right;;">Day Wise Report</a>
            </h4>
            <!-- <div>
                Data sets being shown for <?php //echo date('d-m-Y H:i:s',strtotime($startDateShow));?> to <?php //echo date('d-m-Y H:i:s',strtotime($endDateShow));?>
            </div> -->
            
            <br/><br/>
           
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


    <div id="dashboardModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
                    <h4 class="modal-title">Attention- Summary of important points/alerts</h4>
                </div>
                <div class="modal-body">
                    
                                <?php
                        //echo "<pre>";
                        //var_dump($totalInsertDataset);
                        $alertData = array();
                        if(isset($typeWise) && count($typeWise) > 0){
                            
                            foreach ($typeWise as $keyMeterType => $valueMeterType) {
                                if(isset($typeWise[$keyMeterType]['meter']) && count($typeWise[$keyMeterType]['meter']) > 0){
                                    foreach ($typeWise[$keyMeterType]['meter'] as $bbk => $bbv) {
                                        
                                        if(isset($totalInsertDataset[$bbk]) && $totalInsertDataset[$bbk]>80){
                                            //var_dump($bbv);
                                            $percentagePressure = 0;
                                            $percentageTemp = 0;
                                            if(isset($bbv['alert_counter_pressure'])){

                                                

                                                $percentagePressure = ($bbv['alert_counter_pressure'] / $totalInsertDataset[$bbk]) * 100;

                                                
                                            }
                                            if(isset($bbv['alert_counter_temp'])){
                                                $percentageTemp = ($bbv['alert_counter_temp'] / $totalInsertDataset[$bbk]) * 100;

                                            }
                                            //var_dump($percentageTemp);

                                            if($percentagePressure>50 || $percentageTemp>50){
                                                if($percentagePressure>50){
                                                    $alertData[$bbv['name']][1] = 'pressure';
                                                }
                                                if($percentageTemp>50){
                                                    $alertData[$bbv['name']][0] = 'temp'; 
                                                }
                                                
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if(count($alertData)){
                            $alertModalOpen = 1;
                    ?>
                    <div class="form-group">
                        <p> <strong>Meter Error/Consistency Error</strong></p>
                        <p> Some of the metering points and variables have been showing consistent flags againsts to the norms set as per June, 2017 performance. These might be because of metering error or some performance issue. Please check the following variables for respective meters.</p>
                        <?php 
                            
                            $html1Alert = "";
                            
                            foreach ($alertData as $meterNamekey => $meteralertvalue) {
                                $phtml = (isset($meteralertvalue[1]) && $meteralertvalue[1]=='pressure') ? 'Pressure' : '';
                                $thtml = (isset($meteralertvalue[0]) && $meteralertvalue[0]=='temp') ? 'Temperature' : '';
                                if($phtml!=''){
                                    $thtml = ','.$thtml;
                                }

                                $html1Alert.=<<<EOT
                                {$meterNamekey} => {$phtml} {$thtml}<br/>
EOT;

                            }
                            echo $html1Alert;
                        ?>
                    </div>
                    <?php }?>
                    <?php

                        $alertModalOpen = 0;
                        $genFlowTotal = (isset($typeWise['gen']['total']['flow'])) ? $typeWise['gen']['total']['flow'] : '0';
                        $distFlowTotal = (isset($typeWise['dist']['total']['flow'])) ? $typeWise['dist']['total']['flow'] : '0';
                        $genPressureAvg = 0;
                        $distPressureAvg = 0;
                        if(isset($typeWise['gen']['meter']) && count($typeWise['gen']['meter']) > 0){
                            $totCountPres = 0;
                            foreach ($typeWise['gen']['meter'] as $keygen1 => $valuegen1) {

                                $genPressureAvg += isset($valuegen1['P_pressure']) ? $valuegen1['P_pressure'] : '0';
                            $totCountPres++;}

                            $genPressureAvg = $genPressureAvg / $totCountPres;

                        }
                        if(isset($typeWise['dist']['meter']) && count($typeWise['dist']['meter']) > 0){
                            $totCountPres = 0;
                            foreach ($typeWise['dist']['meter'] as $keydist1 => $valuedist1) {

                                $distPressureAvg += isset($valuedist1['P_pressure']) ? $valuedist1['P_pressure'] : '0';
                            $totCountPres++;}
                            
                            $distPressureAvg = $distPressureAvg / $totCountPres;

                        }
                        if($genFlowTotal==0 && $distFlowTotal!=0 && $genPressureAvg>0 && $distPressureAvg>0){
                            $alertModalOpen = 2;
                    ?>
                    <div class="form-group">
                        <p><strong>Quality Indicator Not Visible</strong></p>
                        <?php echo "Steam consumption requirement is now being met by Boilers (SM120 and SM270), which are currently not integrated with the online system, hence the indicators like pressure drop and temperature drop would not be seen.";?>
                    </div>
                    <?php }?>
                </div>
                <div class="modal-footer" style="text-align: left;">
                    <p> Thank you for using EDA Solution by EnergyTech Ventures. This is a top of the value chain solution provided to ensure that you make the maximum use of your Data Assets.
                    <br/>
                    The solution is a work in progress and intelligence will evolve as we move along.
                    </p>
                </div>
                <?php
                    

                    $date7DaysAddedStrToTime = "";
                    if(isset($last_steam_ack_counter[0]->status_updated_on)){
                        $lastDateStrToTime = strtotime($last_steam_ack_counter[0]->status_updated_on);
                        $date7DaysAddedStrToTime = strtotime("+7 day", $lastDateStrToTime);

                        $currentDateStrToTime = strtotime(date('Y-m-d H:i:s'));

                        //var_dump($date7DaysAddedStrToTime);
                        //var_dump($currentDateStrToTime);
                    }

                ?>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="updateAcknowledge('ACK');">Acknowledge</button>
                    <button type="button" class="btn btn-success" onclick="updateAcknowledge('IGN');">Ignore</button>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('footer');?>

   
    <br/>

    <script type="text/javascript">
        
        function updateAcknowledge(status){

            $.post( "<?php echo base_url();?>fm/dashboard/updateACKStatus/"+status,{"<?php echo $this->security->get_csrf_token_name(); ?>" : "<?php echo $this->security->get_csrf_hash(); ?>"}, function( data ) {
                if(data.status == 'success'){
                    $('#dashboardModal').modal('hide');
                }               
            },'json');

        }

        $(document).ready(function(){
            <?php if($alertModalOpen>0){
                    if($date7DaysAddedStrToTime==''){
                ?>
                        $('#dashboardModal').modal({
                            show:true,
                            backdrop: 'static',
                            keyboard: false 
                        });
            <?php   }else if($currentDateStrToTime>$date7DaysAddedStrToTime){?>
                        $('#dashboardModal').modal({
                            show:true,
                            backdrop: 'static',
                            keyboard: false 
                        });
            <?php   }
                  }
            ?>
        });
    </script>
</body>
</html> 

