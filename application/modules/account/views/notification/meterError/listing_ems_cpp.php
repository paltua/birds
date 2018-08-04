<!DOCTYPE html>
<html lang="en">
<head>
    
    <?php $this->load->view('head');?>

    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">
    <script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>

    <!-- <script src="<?php echo base_url();?>resource/data-table/jquery.dataTables.min.js"></script>
     <script src="<?php echo base_url();?>resource/data-table/dataTables.bootstrap.min.js"></script>
     <link rel="stylesheet" href="<?php echo base_url();?>resource/data-table/dataTables.bootstrap.min.css"> -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>resource/datatable/css/jquery.dataTables.css">
    <script type="text/javascript" src="<?php echo base_url();?>resource/datatable/js/jquery.dataTables.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').DataTable( {
                "order": [[ 6, "desc" ]],
                "columnDefs": [
                    {
                        "targets": [ 6 ],
                        "visible": false,
                        "searchable": false
                    }
                ]
            } );
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
    <?php $headerArr = array('Amps','HZ','KW','PF','Volt');?>
    <section class="pad">
        <div class="container">
            <a href="<?php echo base_url();?>account/dashboard" style="float: right;" class="btn btn-primary blbtn"> Back</a><span style="float: right;">&nbsp;</span>
            <h4><strong> Notification for Meter Error of EMS CPP on <?php echo date("F j, Y", (strtotime('-1 day', strtotime(date('Y-m-d')))));?></strong>
                
            </h4>
            
            <div id="allDataId">
                <table id="example" class="table table-striped table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Meter Name</th>
                            <?php foreach($headerArr as $key => $val){ ?>
                                <th>
                                  <?php echo $val;?>
                                </th>
                            <?php } ?>
                            <th>
                                Total
                            </th>
                        </tr>
                    </thead>
                    <!-- <tfoot>
                        <tr>
                            <th>Meter Name</th>
                            <?php foreach($headerArr as $key => $val){ ?>
                                <th>
                                  <?php echo $val;?>
                                </th>
                            <?php } ?>
                            <th>
                                Total
                            </th>
                        </tr>
                    </tfoot> -->
                    <tbody>
                        <?php if(count($list) > 0){?>
                        <?php foreach($list as $meterVal){?>
                        <tr>
                            <td><?php echo $meterVal->name;?></td>
                            <?php foreach($headerArr as $key => $val){ ?>
                                <td>
                                    <?php if($meterVal->{$val} > 0){ echo 'Error'.(($meterVal->{$val} >=2)?'(repeated)':'');}else{ echo 'No error';}?>
                                </td>
                            <?php } ?>
                            <td>
                                <?php echo $meterVal->total;?>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php }else{?>
                            <tr>
                                <td colspan="7">No data set please.</td>
                                
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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

