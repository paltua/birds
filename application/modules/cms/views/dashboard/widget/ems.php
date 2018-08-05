
<div class="panel panel-info">
    <div class="panel-heading">
        <a href="<?php echo base_url();?>ems/dashboard">
            <div class="htext">
                <img class="icn" src="<?php echo base_url();?>resource/bootstrap/icon/chart_icon.png">Electricity
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
            <?php 

            $last15Result0 = round((isset($last15DataSet[0]->total_data)?$last15DataSet[0]->total_data:0), 2);
            $last15Result1 = round((isset($last15DataSet[1]->total_data)?$last15DataSet[1]->total_data:0), 2);
            ?>
            <tbody> 
                <tr role="row" class="odd">
                    <th style="">
                        <p>Generation (KW)</p>
                    </th>
                    <th style="">
                        <p><?php echo $last15Result0;?></p>
                    </th>
                    <th style="">
                        <p><?php echo (isset($monthlyDataSet[0]->total_data))?round($monthlyDataSet[0]->total_data, 2):0;?></p>
                    </th>
                </tr>
                <tr role="row" class="odd">
                    <th style="">
                        <p>Distribution (KW)</p>
                    </th>
                    <th style="">
                        <p><?php echo $last15Result1;?></p>
                    </th>
                    <th style="">
                        <p><?php echo (isset($monthlyDataSet[1]->total_data))?round($monthlyDataSet[1]->total_data, 2):0;?></p>
                    </th>
                </tr>
                <tr role="row" class="odd">
                    <th style="">
                        <p>Loss (%)</p>
                    </th>
                    <th style="">
                        <p>
                            <?php 
                                if($last15Result0 == 0){
                                    echo "Undefined";
                                }else{
                                    echo round(((($last15Result0 - $last15Result1)/$last15Result0)*100), 2);
                                }
                            ?>
                        </p>
                    </th>
                    <th style="">
                        <p>
                            <?php 
                            if(isset($monthlyDataSet[0]->total_data)){
                                if($monthlyDataSet[0]->total_data == 0){
                                    echo "Undefined";
                                }else{
                                    echo round(((($monthlyDataSet[0]->total_data - $monthlyDataSet[1]->total_data)/$monthlyDataSet[0]->total_data)*100), 2);
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