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

    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.css"></script>
    <script src="<?php echo base_url();?>resource/date-time/moment.js"></script>
    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.js"></script>

    <script src="<?php echo base_url();?>resource/js/timer.js"></script>

    <script type="text/javascript">
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
                <?php if($selectedDate != ''){?>
                defaultDate : '<?php echo date("Y-m-d", strtotime($selectedDate));?>',
                <?php }?>
                <?php if($minMaxDate["min_date"] != ''){?>
                minDate : '<?php echo date("Y-m-d", strtotime($minMaxDate["min_date"])); ?>',
                <?php }?>
                <?php if($minMaxDate["max_date"] != ''){?>
                maxDate : '<?php echo date("Y-m-d", strtotime($minMaxDate["max_date"])); ?>',
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

                
            <h4><strong>Electricity Report Dashboard</strong> 
                <!-- <span style="font-size: 10px;">(Page will update after <span class="timer" id="timerId">&nbsp;</span>)
                </span> -->

                <a href="<?php echo base_url();?>ems/dashboard/live/<?php echo $typeId;?>" style="float: right;;">Back</a>
            </h4>
            <br/>
            <div class="row">
            <form name="frmReport" action="" method="post">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-lg-4">
                    
                        <label>Select Date:<span class="red">*</span></label>
                        <input class="form-control" name="selectedDate" id="datepicker" value="" type="date">
                    
                </div>

                <div class="col-lg-4">
                    <label>Select Shift:</label>
                    <select name="shift" class="form-control">
                        <?php foreach ($shiftViewArr as $key => $value) {?>
                            <option value="<?php echo $key;?>" <?php if($selectedShift == $key){?> selected <?php } ?>><?php echo $value;?></option>
                        <?php } ?>
                        
                        
                    </select>
                </div>
                <div class="col-lg-4">
                    <input type="submit" name="go" id="basic_gap" value="Go" class="btn btn-primary blbtn">
                    
                </div>
                </form>
            </div>
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

    <?php $this->load->view('footer');?>
    <br/>
</body>
</html> 