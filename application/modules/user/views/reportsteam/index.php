<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view('head');?>
    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">
    <script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>

    <script type="text/javascript">    

    function changeSection(sectionname){

        //alert(sectionname);
        if(sectionname=='air'){
            window.location.href = '<?php echo base_url();?>reportdownload/reportair';
        }else if(sectionname=='steam'){
            window.location.href = '<?php echo base_url();?>reportdownload/reportsteam';
        }else if(sectionname=='ems'){
            window.location.href = '<?php echo base_url();?>reportdownload/reportems';
        }else{
            alert('Something Wrong!! Please Try Again After SomeTime.')
        }
    }

    $(document).ready(function(){
        $('#allDataId').on('click','.showChart', function() { 
            var meter_link = $(this).attr("meter-link");
            $('.myMeterModal').modal('show');
            $('.myMeterModal').find(".modal-content").load(meter_link);
        });
    
    
    /*$('#datepicker').datetimepicker({
        useCurrent: false,
        sideBySide: true,
        format: 'YYYY-MM-DD',       
        <?php if($dateRange["selectedDate"] != ''){?>
        defaultDate : '<?php //echo date("Y-m-d", strtotime($dateRange["selectedDate"]));?>',
        <?php }?>
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

    $('#datepicker1').datetimepicker({
        useCurrent: false,
        sideBySide: true,
        format: 'YYYY-MM-DD',       
        <?php if($dateRange["selectedDate"] != ''){?>
        defaultDate : '<?php //echo date("Y-m-d", strtotime($dateRange["selectedDate"]));?>',
        <?php }?>
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

    });*/
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
            <h4><strong>Basic Statistical Analysis Report</strong>
            <!-- <a href="<?php echo base_url('air/dashboard');?>" style="float: right;;">Live Dashboard</a> -->
            </h4>
            
                <div class="row">
                    <?php if($msg != ''){?>
                    <div class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><?php echo $msg;?>
                    </div>
                    <?php }?>
                    <form name="frmReport" action="" method="post">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="col-xs-2">
                            <label for="email">Select Module </label>
                            <select name="sectionname" class="form-control" onchange="changeSection(this.value);" required>
                                <option value="ems" <?php echo ($this->uri->segment(2)=="reportems") ? 'selected':'';?>>Electricity</option>
                                <option value="steam" <?php echo ($this->uri->segment(2)=="reportsteam") ? 'selected':'';?>>Steam</option>
                                <option value="air" <?php echo ($this->uri->segment(2)=="reportair") ? 'selected':'';?>>Compressed Air</option>
                            </select>
                        </div>
                        
                        <div class="col-lg-2">
                            <label for="email">Start Date :</label>
                            <input type="date" required name="startDate" id="datepicker" value="<?php echo isset($_POST['startDate']) ? $_POST['startDate'] : '';?>" max="<?php echo $active_date[0]->max;?>" class="form-control">
                            
                        </div>
                        <div class="col-lg-2">
                            
                            <label for="email">End Date :</label>
                            <input type="date" required name="endDate" id="datepicker1" value="<?php echo isset($_POST['endDate']) ? $_POST['endDate'] : '';?>" max="<?php echo $active_date[0]->max;?>" class="form-control">
                        </div>

                        
                        <div class="col-lg-2"><br/>
                            <input type="submit" name="btnSearch" value="Go" class="btn btn-primary">
                        </div>
                    </form>
                </div>
                
                <div id="allDataId">
                    <?php  
                    
                    if($all!=""){
                        echo $all;
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