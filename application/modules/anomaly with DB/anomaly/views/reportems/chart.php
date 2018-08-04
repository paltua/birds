   
<script type="text/javascript">

chart1Line();
function chart1Line(){
  $('#chartContainer').insertFusionCharts({
    type: 'line',
    id: 'myChart1',
    width: '100%',
    height: '400',
    dataFormat: 'json',
    dataSource: {
        "chart": {
            "caption": "",
            "subcaption": "",
            "xaxisname": "Dates",
            "yaxisname": "Values",
            "showvalues": "0",
            "showYAxisValues":"1",
            "drawAnchors": "1",
            "palettecolors": "559c5c",
            <?php if($min_val){?>
            "yaxisminvalue": "<?php echo $min_val;?>",
            <?php }?>
            <?php if($max_val){?>
            "yaxismaxvalue": "<?php echo $max_val;?>",
            <?php }?>
            "canvasBgColor" : "#F7F7F7",
            "bgColor": "#F7F7F7",
           
            //Theme
            "theme": "fint",

        },
        "data": [<?php echo $dataSet;?>]
    }
  });
  /*var chartId = 'myChart1';
  chartImageSave(chartId);*/
}
<?php 

if($consisIndex == 'yes' && $graph_logic == 1){?>
chart2Line();
function chart2Line(){
  $('#chartContainerConsis').insertFusionCharts({
    type: 'line',
    id: 'myChart2',
    width: '100%',
    height: '400',
    dataFormat: 'json',
    dataSource: {
        "chart": {
            "caption": "",
            "subcaption": "",
            "xaxisname": "Dates",
            "yaxisname": "Values",
            "showvalues": "0",
            "showYAxisValues":"1",
            "numberSuffix": "%",
            "drawAnchors": "1",
            "palettecolors": "559c5c",
            "yaxismaxvalue": "100",
            /*<?php if($min_val_con){?>
            "yaxisminvalue": "<?php echo $min_val_con;?>",
            <?php }?>
            <?php if($max_val_con){?>
            "yaxismaxvalue": "<?php echo $max_val_con;?>",
            <?php }?>*/
            "canvasBgColor" : "#F7F7F7",
            "bgColor": "#F7F7F7",
           
            //Theme
            "theme": "fint",

        },
        "data": [<?php echo $dataSetConsis;?>]
    }
  });
  /*var chartId = 'myChart1';
  chartImageSave(chartId);*/
}
<?php } ?>
</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
	<h4>Data set of <?php echo $meterNameColumn;?></h4>
    <div id="chartContainer">Chart Loading</div>
    
    <?php if($consisIndex == 'yes'){?>
    <h4>Consistency Index of <?php echo $meterNameColumn;?></h4>
    <div id="chartContainerConsis">
        <?php if($graph_logic == 2 || $graph_logic == 3){?>
            <?php
                if($graph_logic == 2){
                    echo "Data set are out of range.";
                }
                if($graph_logic == 3){
                    echo "Data set are within range.";   
                }?>
        <?php }else{ ?>Chart Loading<?php } ?>
            
        </div>
    <?php } ?>
</div>
<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
