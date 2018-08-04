
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
	<h4>Data set of <?php echo $meterNameColumn;?></h4>
    
    
    <div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <td>Date</td>
                    <td>Value</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(isset($TTL_flow_new) && count($TTL_flow_new)>0){
                        foreach ($TTL_flow_new as $key => $value) {
                            
                ?>
                    <tr>
                        <td><?php echo $key;?></td>
                        <td><?php echo $value;?></td>
                    </tr>   
                <?php
                    }}else{
                ?>
                <tr>
                    <td colspan="">No Record Found!</td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
