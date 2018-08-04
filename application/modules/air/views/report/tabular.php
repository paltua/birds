


<script type="text/javascript">
    function generateReport(url,mid,offValue,daysdata) { 

        var time = document.getElementById('datetimestamp').value;

        var meter_link = url+'air/report/getCFMreport/'+mid+'/'+time+'/'+offValue+'/'+daysdata;//$(this).attr("meter-link");

        //alert(meter_link);

        $('.myMeterModal').modal('show');
        //$('.myMeterModal').find(".modal-content").load(meter_link);
        $("#myDiv").css("display", "block");
        $('.myMeterModal').find(".modal-content").load(meter_link,null, function(){
            $("#myDiv").css("display", "none");
            
        });
    }

    $('#datetimestamp').datetimepicker({
        useCurrent: false,
        sideBySide: true,
        format: 'YYYY-MM-DD',       
        <?php if($max_date!= ''){?>
        defaultDate : '<?php echo date("Y-m-d", $max_date);?>',
        <?php }?>
        <?php if($min_date!= ''){?>
        minDate : '<?php echo date("Y-m-d", $min_date); ?>',
        <?php }?>
        <?php if($max_date!= ''){?>
        maxDate : '<?php echo date("Y-m-d", $max_date); ?>',
        <?php }?>
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar-check-o",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down"
        }

    });
    </script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <!-- <h4 class="modal-title">Compressed Air Generation Analysis</h4> -->
    <h4 class="modal-title">Compressed Netwrok Diganstics Analysis</h4>
</div>
<?php
    if(isset($modal) && $modal==1){
?>


<div class="modal-body">
	<h5>This analysis allows user to check the operating pattern of compressor in terms of CFM generation or distribution, providing insights on the generation or distribution consistency, multiple cycles and respective duration.</h5>
    
    <div class="row">
        <div class="col-lg-3">
            <label>Select Meter</label>
            
            <select name="meter_ids" id="meter_id" class="form-control">
                <option value="">Select Any Meter</option>
                <?php
                    

                    if(isset($meter_details) && count($meter_details)>0){
                        foreach ($meter_details as $mKey => $mName) {
                            if($mKey==6){
                ?>
                            <option value="totgen" style="font-weight:bold;">Total Generation</option>
                            <?php }else if($mKey==18){?>
                            <option value="totdist" style="font-weight:bold;">Total Distribution</option>
                            <?php }?>
                <option value="<?php echo $mKey;?>"><?php echo $mName;?></option>
                <?php
                        }
                    }
                ?>
            </select>
            <script type="text/javascript">
                var meterId = 0;
                $("#meter_id").on("change", function(){          
                  meterId = $(this).val();
                });
            </script>
        </div>
        <div class="col-lg-3">
            <label>Select Date</label>
            <input type="date" name="datetimestamps" id="datetimestamp" value="" class="form-control">
        </div>
        <!-- <div class="col-lg-3">
            <label>Select Off value</label>
            <select name="offvalues" id="offvalue" class="form-control">
                <option value="AVG">Average</option>
                <option value="HIGH">High</option>
                <option value="LOW">Low</option>                
            </select>
            
        </div> -->
        <script type="text/javascript">
                var offValue = 'AVG';
                /*$("#offvalue").on("change", function(){          
                  offValue = $(this).val();
                });*/
            </script>
        <div class="col-lg-3">
            <label>Select Days</label>
            <select name="daysDatas" id="daysData" class="form-control">
                <option value="7">7 Days</option>
                <option value="15">15 Days</option>
                <option value="30">30 Days</option>                
            </select>
            <script type="text/javascript">
                var daysData = '7';
                $("#daysData").on("change", function(){          
                  daysData = $(this).val();
                });
            </script>
        </div>

        <div class="col-lg-3">
            <label>&nbsp;&nbsp;&nbsp;</label><br/>
            <a href="javascript:void(0);" class="btn btn-primary" onclick="generateReport('<?php echo base_url();?>',meterId,offValue,daysData)">Go</a>
        </div>
    </div>

    

</div>
<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
<?php }?>
<div id="myDiv" style='display: none; width: 100%;text-align: center;'>    
    <img src="<?php echo base_url();?>resource/logo/loader.gif">
</div>

<?php
    
    if(isset($modal) && $modal==2){
?>

<div class="modal-body">
    <h4>Meter # <?php echo (isset($meter_name) && $meter_name!='') ? $meter_name : 'NA';?> <strong id="dateforsecondtable" style="display: none;"></strong><br/><strong id="offValforsecondtable" style="display: none;"></strong></h4>

    
    
    <?php
        if(!isset($meter_name) || $meter_name=='NA' || $meter_name==''){
            echo "Meter Not Found, Please Select Any Meter.";
        }else{
            if(isset($PS_result) && count($PS_result)>0){
                 //echo "<pre>";
                 //var_dump($PS_result);

    ?>
                <div id="firstTable">
                    <table border="1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            
                            <th style="text-align:center;">Day</th>
                            <th style="text-align:center;">No of Cycles</th>
                            <th style="text-align:center;">Average Duration of Cycles</th>
                            <th style="text-align:center;">% Cycles (<1 Hr)</th>
                            <th style="text-align:center;">% Cycles (1-2 Hr)</th>
                            <th style="text-align:center;">% Cycles (2-3 Hr)</th>
                            <th style="text-align:center;">% Cycles (>3 Hr)</th>
                        </thead>
                        <tbody>

                        <?php
                            
                            foreach ($PS_result as $dateKey => $datealue) {
                                $tot_cycle = isset($datealue['calculated']['tot_cycle']) ? $datealue['calculated']['tot_cycle'] : 'NA';
                                $avg_duration = isset($datealue['calculated']['avg_duration']) ? number_format($datealue['calculated']['avg_duration'],3) : 'NA';
                                $avg_duration_less_1 = isset($datealue['calculated']['avg_duration_less_1']) ? number_format($datealue['calculated']['avg_duration_less_1'],2) : 'NA';
                                $avg_duration_1_2 = isset($datealue['calculated']['avg_duration_1_2']) ? number_format($datealue['calculated']['avg_duration_1_2'],2) : 'NA';
                                $avg_duration_2_3 = isset($datealue['calculated']['avg_duration_2_3']) ? number_format($datealue['calculated']['avg_duration_2_3'],2) : 'NA';
                                $avg_duration_above_3 = isset($datealue['calculated']['avg_duration_above_3']) ? number_format($datealue['calculated']['avg_duration_above_3'],2) : 'NA';

                                $offvalueforPS = isset($offvalueforPSArr[$dateKey]) ? $offvalueforPSArr[$dateKey] : 0;

                        ?>
                            <tr>
                                <td style="text-align:center;"><a href="javascript:void(0);" onclick="openSecondTable('<?php echo $dateKey; ?>','<?php echo round($offvalueforPS,3);?>','<?php echo strtotime($dateKey); ?>');"><?php echo $dateKey;?></a></td>
                                <td style="text-align:center;"><?php echo $tot_cycle;?></td>
                                <td style="text-align:center;"><?php echo $avg_duration;?></td>
                                <td style="text-align:center;"><?php echo $avg_duration_less_1;?></td>
                                <td style="text-align:center;"><?php echo $avg_duration_1_2;?></td>
                                <td style="text-align:center;"><?php echo $avg_duration_2_3;?></td>
                                <td style="text-align:center;"><?php echo $avg_duration_above_3;?></td>
                            </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    </table>
                </div>

                <?php 
                    foreach ($PS_result as $datekey2 => $dateValue2) {
                ?>
                    <div id="secondTable_<?php echo strtotime($datekey2);?>" style="display: none;">
                        <a href="javascript:void(0);" onclick="openFirstTable(<?php echo strtotime($datekey2);?>);" class="btn btn-primary" style="float: right;">Back</a>

                        <br/><br/>
                        <?php
                            //echo "<pre>";
                            //var_dump($dateValue2);

                            if(isset($dateValue2['details']['status']) && $dateValue2['details']['status']=="true" && count($dateValue2['details']['data'])>0){
                        ?>
                                <table border="1" id="example_<?php echo strtotime($datekey2);?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        
                                        <th style="text-align:center;">Cycles</th>
                                        <th style="text-align:center;">Average Count</th>
                                        <th style="text-align:center;">Low Count</th>
                                        <th style="text-align:center;">Anomalous Cycle</th>
                                        <th style="text-align:center;">High Count</th>
                                        <th style="text-align:center;">Duration (Hr)</th>
                                        <!-- <th style="text-align:center;">Running Time</th> -->
                                        <th style="text-align:center;">Anomaly Review</th>
                                        <th style="text-align:center;">Start Time</th>
                                        <th style="text-align:center;">End Time</th>
                                    </thead>
                                    <tbody>

                                    <?php
                                        
                                        foreach ($dateValue2['details']['data'] as $PSkey => $PSvalue) {
                                            
                                            $cycles = (isset($PSvalue['cycles']) && $PSvalue['cycles']!='') ? explode('_', $PSvalue['cycles']) : 'NA';

                                            $cycles = isset($cycles[1]) ? $cycles[1] : 'NA';
                                            $average_count = (isset($PSvalue['average_count']) && $PSvalue['average_count']!='') ? number_format($PSvalue['average_count'],3) : 'NA';
                                            $low_count = (isset($PSvalue['low_count']) && $PSvalue['low_count']!='') ? number_format($PSvalue['low_count'],3) : 'NA';
                                            $anomalous_cycle = (isset($PSvalue['anomalous_cycle']) && $PSvalue['anomalous_cycle']!='' && $PSvalue['anomalous_cycle']!=false) ? $PSvalue['anomalous_cycle'] : 'NA';
                                            $high_count = (isset($PSvalue['high_count']) && $PSvalue['high_count']!='') ? number_format($PSvalue['high_count'],3) : 'NA';
                                            $duration = (isset($PSvalue['duration(Hr)'])) ? $PSvalue['duration(Hr)'] : 'NA';
                                            /*$running_time = (isset($PSvalue['running_time'])) ? $PSvalue['running_time'] : 'NA';*/

                                            $anomaly_review = (isset($PSvalue['anomaly_review']) && $PSvalue['anomaly_review']!='' && $PSvalue['anomaly_review']!=false && $PSvalue['anomaly_review']!=NULL) ? str_replace('_',' ' ,$PSvalue['anomaly_review']) : 'NA';


                                            $start_time = (isset($PSvalue['start_time'])) ? $PSvalue['start_time'] : 'NA';
                                            $end_time = (isset($PSvalue['end_time'])) ? $PSvalue['end_time'] : 'NA';
                                    ?>
                                        <tr>
                                            
                                            <td style="text-align:center;"><?php echo $cycles;?></td>
                                            <td style="text-align:center;"><?php echo $average_count;?></td>
                                            <td style="text-align:center;"><?php echo $low_count;?></td>
                                            <td style="text-align:center;"><?php echo $anomalous_cycle;?></td>
                                            <td style="text-align:center;"><?php echo $high_count;?></td>
                                            <td style="text-align:center;"><?php echo $duration;?></td>
                                            <!-- <td style="text-align:center;"><?php //echo $running_time;?></td> -->
                                            <td style="text-align:center;"><?php echo $anomaly_review;?></td>
                                            <td style="text-align:center;"><?php echo $start_time;?></td>
                                            <td style="text-align:center;"><?php echo $end_time;?></td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                    </tbody>
                                </table>

                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        $('#example_'+<?php echo strtotime($datekey2);?>).DataTable();
                                    } );
                                </script>
                        <?php 
                            }else{
                                echo "We could not generate analysis. Please try again after sometimes. Or contact to system administrator.";
                            }
                        ?>
                    </div>
                <?php }?>
    <?php
            }else{
                echo "We could not generate analysis. Please try again after sometimes. Or contact to system administrator.";
            }
        }
    ?>
    
    
    <?php
        /*echo "<pre>";
        var_dump($PS_result);*/

    ?>
</div>
<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->

<?php }?>


<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable();
    } );

    function openSecondTable(date,offval,keys){
        $('#dateforsecondtable').css('display','block');
        $('#offValforsecondtable').css('display','block');
        $('#dateforsecondtable').html('('+date+')');
        $('#offValforsecondtable').html('Average for the day : '+offval);
        $('#firstTable').hide();
        $('#secondTable_'+keys).show();
    }
    function openFirstTable(keys){
        $('#dateforsecondtable').css('display','none');
        $('#offValforsecondtable').css('display','none');
        $('#dateforsecondtable').html('');
        $('#offValforsecondtable').html('');
        $('#firstTable').show();
        $('#secondTable_'+keys).hide();
    }
</script>