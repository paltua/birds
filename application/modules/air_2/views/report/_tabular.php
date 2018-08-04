


<script type="text/javascript">
    function generateReport(url,mid,offValue,daysdata) { 
        
        var time = document.getElementById('datetimestamp').value;

        var meter_link = url+'air/report/getCFMreport/'+mid+'/'+time+'/'+offValue+'/'+daysdata;//$(this).attr("meter-link");

        //alert(meter_link);

        $('.myMeterModal').modal('show');
        $('.myMeterModal').find(".modal-content").load(meter_link);
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
    <h4 class="modal-title">Compressed Air Generation Analysis</h4>
</div>
<?php
    if(isset($modal) && $modal==1){
?>


<div class="modal-body">
	<h5>This analysis allows user to check the operating pattern of compressor in terms of CFM generation, providing insights on the generation consistency, multiple cycles and respective duration.</h5>
    
    <div class="row">
        <div class="col-lg-3">
            <label>Select Meter</label>
            <select name="meter_ids" id="meter_id" class="form-control">
                <option value="">Select Any Meter</option>
                <?php
                    if(isset($meter_details) && count($meter_details)>0){
                        foreach ($meter_details as $mKey => $mName) {
                ?>
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
            <a href="javascript:void(0);" class="btn btn-primary" onclick="generateReport('<?php echo base_url();?>',meterId,offValue,daysData)">Go and Wait</a>
        </div>
    </div>

    

</div>
<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
<?php }?>

<?php
    
    if(isset($modal) && $modal==2){
?>

<div class="modal-body">
    <h4>Meter # <?php echo (isset($meter_name) && $meter_name!='') ? $meter_name : 'NA';?></h4>
    
    <?php
        if(!isset($meter_name) || $meter_name=='NA' || $meter_name==''){
            echo "Meter Not Found, Please Select Any Meter.";
        }else{
            if(isset($PS_result['status']) && $PS_result['status']=="true" && count($PS_result['data'])>0){
                
    ?>

                <table border="1" id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
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
                        
                        foreach ($PS_result['data'] as $PSkey => $PSvalue) {
                            
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
</script>