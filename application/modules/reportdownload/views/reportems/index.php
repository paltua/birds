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
            <div class="panel panel-default" >
                <div class="panel-heading"><h4><strong>Basic Statistical Analysis Report</strong></h4></div>
                <div class="panel-body">
                    <div class="row">
                        <?php if($msg != ''){?>
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><?php echo $msg;?>
                        </div>
                        <?php }?>
                        <form name="frmReport" action="" method="post">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            
                            
                            <div class="col-xs-3">
                                <label for="email">Select Module </label>
                                <select name="sectionname" class="form-control" onchange="changeSection(this.value);" required>
                                    <option value="ems" <?php echo ($this->uri->segment(2)=="reportems") ? 'selected':'';?>>Electricity</option>
                                    <option value="steam" <?php echo ($this->uri->segment(2)=="reportsteam") ? 'selected':'';?>>Steam</option>
                                    <option value="air" <?php echo ($this->uri->segment(2)=="reportair") ? 'selected':'';?>>Compressed Air</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="email">Start Date :</label>
                                <input type="date" required name="startDate" max="<?php echo $active_date[0]->max;?>" value="<?php echo $startDate;?>" class="form-control">
                                
                            </div>
                            <div class="col-lg-3">
                                <label for="email">End Date :</label>
                                <input type="date" required name="endDate" value="<?php echo $endDate;?>" max="<?php echo $active_date[0]->max;?>" class="form-control">
                            </div>
                            
                            <div class="col-lg-12"><br>
                                <!-- <input type="submit" name="btnSearch" value="Go" class="btn btn-primary"> -->
                                <button type="submit" name="btnSearch" class="btn btn-success"> <i class="fa fa-download"></i> Report Download</button>
                            </div>
                        </form>
                    </div>
                </div>
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