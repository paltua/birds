<!DOCTYPE html>
<html lang="en">
<head>
    
    <?php $this->load->view('head');?>

    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">
    <script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.css"></script>
    <script src="<?php echo base_url();?>resource/date-time/moment.js"></script>
    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.js"></script>
      
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
            <a href="<?php echo base_url();?>weaving/dashboard/index" style="float: right;" class="btn btn-info blbtn">Dashboard</a><span style="float: right;">&nbsp;</span>
            <a href="<?php echo base_url();?>weaving/report/shift" style="float: right;" class="btn btn-primary blbtn"> Shift wise Report</a><span style="float: right;">&nbsp;</span>
            <a href="<?php echo base_url();?>weaving/report/day" style="float: right;" class="btn btn-primary blbtn"> Day wise Report</a><span style="float: right;">&nbsp;</span>
            <!-- <a href="<?php echo base_url();?>weaving/report/index" style="float: right;" class="btn btn-primary blbtn">Day wise Report</a> -->

            <h4><strong> Weaving Live Dashboard(<?php echo date("F j, Y, g:i a", strtotime($startDateShow));?> - <?php echo date("F j, Y, g:i a", strtotime($endDateShow));?>)</strong>
                
            </h4>
            
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

