<!DOCTYPE html>
<html lang="en">
<head>
    
    <?php $this->load->view('head');?>

    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">
    <script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>

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

                
            <h4><strong>Electricity Shift Wise Report Dashboard of DG House Bus-<?php echo ($typeId == 1)?'A':(($typeId == 2)?'B':'C');?> Receiving Vs DG House Bus-<?php echo ($typeId == 1)?'A':(($typeId == 2)?'B':'C');?> Distribution</strong> 
                <a href="<?php echo base_url();?>ems/dashboard/live/<?php echo $typeId;?>" style="float: right;" class="btn btn-primary blbtn">Back</a> <span style="float: right;">&nbsp;</span> <a href="<?php echo base_url();?>ems/report/index/<?php echo $typeId;?>" style="float: right;" class="btn btn-primary blbtn">Day wise report</a>
            </h4>
            <br/>
            <div class="row">
            <form name="frmReport" action="" method="post">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-lg-4">
                    
                        <label>Select Date:<span class="red">*</span></label>
                        <input class="form-control" name="selectedDate" id="datepicker" value="<?php echo $selectedDate;?>" type="date">
                    
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