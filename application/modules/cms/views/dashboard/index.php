


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

    <script src="<?php echo base_url();?>resource/js/timer.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            
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
            <h4><strong>Main Dashboard</strong></h4>
            <div class="row">
                <div class="col-sm-12">
                    <?php echo $connectivityStatus;?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?php echo $emsHtml;?>
                </div>
                <div class="col-sm-6">
                    <?php echo $steamHtml;?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?php echo $airHtml;?>
                </div>
                <!-- <div class="col-sm-6">
                    <?php echo $transHtml;?>
                </div> -->
            </div>
            
        </div>
    </section>        
</body>
</html> 

