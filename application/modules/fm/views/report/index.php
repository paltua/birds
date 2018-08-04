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

    
    $(document).ready(function(){

        $('#datepicker').datetimepicker({
            useCurrent: false,
            sideBySide: true,
            format: 'YYYY-MM-DD',       
            <?php if($dateRange["selectedDate"] != ''){?>
                <?php if(isset($_POST['startDate']) && $_POST['startDate']!=''){?>
                    defaultDate : '<?php echo date("Y-m-d", strtotime($dateRange["selectedDate"]));?>',
                <?php }else{?>
                    defaultDate : '<?php echo date("Y-m-d", strtotime($dateRange["max_date"]));?>',
                <?php }?>
            
            <?php } ?>
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

        $('#datepicker_end').datetimepicker({
            useCurrent: false,
            sideBySide: true,
            format: 'YYYY-MM-DD',       
            <?php if($dateRange["selectedDateEnd"] != ''){?>
                <?php if(isset($_POST['endDate']) && $_POST['endDate']!=''){?>
                    defaultDate : '<?php echo date("Y-m-d", strtotime($dateRange["selectedDateEnd"]));?>',
                <?php }else{?>
                    defaultDate : '<?php echo date("Y-m-d", strtotime($dateRange["max_date"]));?>',
                <?php }?>
            
            <?php } ?>
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
              <a class="navbar-brand" href="#">WELSPUN EDA </a>
            </div>
        <ul class="nav navbar-nav" style="width: 80%;">
           <?php $this->load->view('header');?>
        </ul>
        </div>
    </nav>
    <section class="pad">
        <div class="container">
            <h4><strong>Steam Day Wise Report</strong>
                <a href="<?php echo base_url('fm/dashboard');?>" style="float: right;;">Live Dashboard</a>
            </h4>
            
                <div class="row">
                    <form name="frmReport" action="" method="post">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="col-lg-4">
                            <label>Select Start Date:<span class="red">*</span></label>
                            <input class="form-control" name="startDate" id="datepicker" value="" type="date">
                        </div>
                        <div class="col-lg-4">
                            <label>Select End Date:<span class="red">*</span></label>
                            <input class="form-control" name="endDate" id="datepicker_end" value="" type="date">
                        </div>
                        
                        <div class="col-lg-4">
                            <input type="submit" name="btnSearch" class="btn btn-primary" value="Go">
                        </div>
                    </form>
                </div><br/><br/>   


                <div id="allDataId">
                    <?php  
                    
                    if($all!=""){
                        echo $all;
                    }else{
                        echo 'Please Select Date Within Range';
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