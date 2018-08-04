<!DOCTYPE html>
<html lang="en">
<head>
    
    <?php $this->load->view('head');?>

    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">
    <script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>

    <script src="<?php echo base_url();?>resource/js/timer.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function(){
            $('#allDataId').on('click','.showChart', function() { 
                var meter_link = $(this).attr("meter-link");
                $('.myMeterModal').modal('show');
                $('.myMeterModal').find(".modal-content").load(meter_link);
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
            <a href="<?php echo base_url();?>ems/dashboard" style="float: right;;" class="btn btn-primary blbtn">Back</a> <span style="float: right;">&nbsp;</span>
            <h4>
                <strong> Report</strong>
            </h4>
            <br>
            <div class="row">
                <form name="frmReport" action="" method="post">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="col-lg-3">
                        <label>Date:<span class="red">*</span></label>
                        <input class="form-control dateTime" name="selectedDate" id="" value="<?php echo $selectedDate;?>" type="date">
                    </div>

                    <div class="col-lg-3">
                        <label>End Date:<span class="red">*</span></label>
                        <input class="form-control dateTime" name="endDate" id="" value="<?php echo $selectedDateEnd;?>" type="date">
                    </div>
                    
                    <div class="col-lg-3">
                        <input type="submit" name="go" id="basic_gap" value="Go" class="btn btn-primary blbtn">
                    </div>
                </form>
            </div>
            <br>
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

