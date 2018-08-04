   
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



</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
	<h4>Data set of <?php echo $meterNameColumn;?></h4>
    <div id="chartContainer">Chart Loading</div>
</div>
<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
