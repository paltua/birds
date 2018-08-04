
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
	<h4>Data set of <?php echo $meterNameColumn;?></h4>
    <div id="chartContainer">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <td>Date</td>
                    <td>Value</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(isset($kwhData) && count($kwhData)>0){
                        foreach ($kwhData as $key => $value) {
                            
                ?>
                    <tr>
                        <td><?php echo $value->end_date_time;?></td>
                        <td><?php echo number_format((float)round($value->data_kwh,2), 2, '.', '');?></td>
                    </tr>   
                <?php
                    }}else{
                ?>
                <tr>
                    <td colspan="2">No Record Found!</td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>

    </div>
</div>

