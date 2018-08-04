<!DOCTYPE html>
<html lang="en">
<head>
    
    <?php $this->load->view('head');?>

    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">
    <script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>

   <!--  <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.css"></script>
    <script src="<?php echo base_url();?>resource/date-time/moment.js"></script>
    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.js"></script> -->

    <script src="<?php echo base_url();?>resource/am_chart/amcharts.js"></script>
    <script src="<?php echo base_url();?>resource/am_chart/serial.js"></script>
    <script src="<?php echo base_url();?>resource/am_chart/themes_light.js"></script>
    <script src="<?php echo base_url();?>resource/am_chart/export.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url();?>resource/am_chart/export.css" type="text/css" media="all" />
    <script src="<?php echo base_url();?>resource/am_chart/dataloader.min.js" type="text/javascript"></script>
    
    <link rel="stylesheet" href="<?php echo base_url();?>resource/chosen/chosen.min.css">
    <script src="<?php echo base_url();?>resource/chosen/chosen.jquery.min.js"></script>

    <script type="text/javascript"> 

        $(document).ready(function(){
            $('#allDataId').on('click','.showChart', function() { 
                $('.myMeterModal').find(".modal-content").html('');
                $('.myMeterModal').modal('show');
                var meter_link = $(this).attr("meter-link");
                $('.myMeterModal').find(".modal-content").load(meter_link);
            });
            $("#p_device_id").chosen({no_results_text: "Oops, No Transformer found!"});
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
            
            <h4><strong> Network Diagnostic Under Electricity </strong>
                <a href="<?php echo base_url();?>ems/dashboard" style="float: right;" class="btn btn-primary blbtn"> Back</a><span style="float: right;">&nbsp;</span>
                <a href="<?php echo base_url();?>ems/network/dashboard" style="float: right;" class="btn btn-primary blbtn">Report</a>
            </h4>
            
            
            <div id="allDataId">
                <?php echo $mainPage;?>
                <?php $this->load->view('trans_logic');?>
            </div>
        </div>
    </section>
    <!-- Modal -->
    <div id="myModal" class="modal fade myMeterModal " role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                
            </div>
        </div>
    </div>

    <?php $this->load->view('footer');?>
    <br/>
</body>
</html> 

