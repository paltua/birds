<div class="panel panel-info">
    <div class="panel-heading">
        <a href="<?php echo base_url();?>fm/dashboard">
            <div class="htext">
                <img class="icn" src="<?php echo base_url();?>resource/bootstrap/icon/chart_icon.png">Steam
            </div>
        </a>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
            <thead>
                <tr role="row">
                    <th style="">
                        <p></p>
                    </th>
                    <th style="">
                        <p>Last 15 minutes</p>
                    </th>
                    <th style="">
                        <p>Average this month</p>
                    </th>
                </tr>
            </thead>  
            <tbody> 
                <tr role="row" class="odd">
                    <th style="">
                        <p>Generation (T/Hr)</p>
                    </th>
                    <th style="">
                        <p><?php echo (isset($last15DataSet[0]->total_flow))?round($last15DataSet[0]->total_flow, 2):0;?>
                            <?php //echo round($last15DataSet[0]->total_flow, 2);?></p>
                    </th>
                    <th style="">
                        <p><?php echo (isset($monthlyDataSet[0]->total_flow))?round($monthlyDataSet[0]->total_flow, 2):0;?></p>
                    </th>
                </tr>
                <tr role="row" class="odd">
                    <th style="">
                        <p>Consumption (T/Hr)</p>
                    </th>
                    <th style="">
                        <p><?php echo (isset($last15DataSet[1]->total_flow))?round($last15DataSet[1]->total_flow, 2):0;?>
                            <?php //echo round($last15DataSet[1]->total_flow, 2);?></p>
                    </th>
                    <th style="">
                        <p><?php echo (isset($monthlyDataSet[1]->total_flow))?round($monthlyDataSet[1]->total_flow, 2):0;?></p>
                    </th>
                </tr>
                <tr role="row" class="odd">
                    <th style="">
                        <p>Loss (%)</p>
                    </th>
                    <th style="">
                        <p>
                            <?php 
                                if(!isset($last15DataSet[0]->total_flow) || $last15DataSet[0]->total_flow == 0){
                                    echo "Undefined";
                                }else{
                                    echo round(((($last15DataSet[0]->total_flow - $last15DataSet[1]->total_flow)/$last15DataSet[0]->total_flow)*100), 2);
                                }
                            ?>
                        </p>
                    </th>
                    <th style="">
                        <p>
                            <?php 
                            if(isset($monthlyDataSet[0]->total_flow)){
                                if($monthlyDataSet[0]->total_flow == 0){
                                    echo "Undefined";
                                }else{
                                    echo round(((($monthlyDataSet[0]->total_flow - $monthlyDataSet[1]->total_flow)/$monthlyDataSet[0]->total_flow)*100), 2);
                                }
                            }else{
                                echo "Undefined";
                            }
                            ?>
                        </p>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</div>