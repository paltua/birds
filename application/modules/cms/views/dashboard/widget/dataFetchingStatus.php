<div class="panel panel-info">
    <div class="panel-heading">
        <div class="htext">
            <img class="icn" src="<?php echo base_url();?>resource/bootstrap/icon/chart_icon.png">Connectivity Status
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
            <thead>
                <tr role="row">
                    <th style="">
                        <p>Sector</p>
                    </th>
                    
                    <th style="">
                        <p>Steam </p>
                    </th>
                    <th style="">
                        <p>Air </p>
                    </th>
                    <th style="">
                        <p>Data Log </p>
                    </th>
                    <th style="">
                        <p>EMS </p>
                    </th>
                    <th style="">
                        <p>EMS(CPP) </p>
                    </th>
                    <th style="">
                        <p>WEAVING </p>
                    </th>
                </tr>
            </thead>  
            <tbody> 
                <tr role="row" class="odd">
                    <th style="">
                        <p>Status</p>
                    </th>
                    <td style="">
                        <p><?php $hourdiff = round((strtotime($steamStatus['last_run']) - strtotime($steamStatus['last_run_data']))/3600, 1);
                        if($hourdiff > 1){
                            echo '<span style="color:red;">NOT OK</span>';
                        }else{
                            echo "OK";
                        }?>
                        </p>
                    </td>
                    <td style="">
                        <p><?php $hourdiff = round((strtotime($airStatus['last_run']) - strtotime($airStatus['last_run_data']))/3600, 1);
                        if($hourdiff > 1){
                            echo '<span style="color:red;">NOT OK</span>';
                        }else{
                            echo "OK";
                        }?>
                        </p>
                    </td>
                    <td style="">
                        <p><?php $hourdiff = round((strtotime($dataLogStatus['last_run']) - strtotime($dataLogStatus['last_run_data']))/3600, 1);
                        if($hourdiff > 1){
                            echo '<span style="color:red;">NOT OK</span>';
                        }else{
                            echo "OK";
                        }?>
                        </p>
                    </td>
                    <td style="">
                        <p><?php $hourdiff = round((strtotime($emsStatus['last_run']) - strtotime($emsStatus['last_run_data']))/3600, 1);
                        if($hourdiff > 1){
                            echo '<span style="color:red;">NOT OK</span>';
                        }else{
                            echo "OK";
                        }?>
                        </p>
                    </td>
                    <td style="">
                        <p><?php $hourdiff = round((strtotime($emsCppstatus['last_run']) - strtotime($emsCppstatus['last_run_data']))/3600, 1);
                        if($hourdiff > 1){
                            echo '<span style="color:red;">NOT OK</span>';
                        }else{
                            echo "OK";
                        }?>
                        </p>
                    </td>
                    <td style="">
                        <p><?php $hourdiff = round((strtotime($dataWeavingStatus['last_run']) - strtotime($dataWeavingStatus['last_run_data']))/3600, 1);
                        if($hourdiff > 1){
                            echo '<span style="color:red;">NOT OK</span>';
                        }else{
                            echo "OK";
                        }?>
                        </p>
                    </td>
                </tr>
                <tr role="row" class="odd">
                    <th style="">
                        <p>Last Timestamp</p>
                    </th>
                    <td style="">
                        <p><?php echo date("F j, Y, g:i a", (strtotime($steamStatus['last_run_data']) + 900));?></p>
                    </td>
                    <td style="">
                        <p><?php echo date("F j, Y, g:i a", (strtotime($airStatus['last_run_data']) + 900));?></p>
                    </td>
                    <td style="">
                        <p><?php echo date("F j, Y, g:i a", (strtotime($dataLogStatus['last_run_data']) + 900));?></p>
                    </td>
                    <td style="">
                        <p><?php echo date("F j, Y, g:i a", (strtotime($emsStatus['last_run_data']) + 900));?></p>
                    </td>
                    <td style="">
                        <p><?php echo date("F j, Y, g:i a", (strtotime($emsCppstatus['last_run_data']) + 900));?></p>
                    </td>
                    <td style="">
                        <p><?php echo date("F j, Y, g:i a", (strtotime($dataWeavingStatus['last_run_data']) + 900));?></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>