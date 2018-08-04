   
<script type="text/javascript">

chart1Line();
function chart1Line(){
  $('#chartContainer').insertFusionCharts({
    <?php if($dayCount == 1){?>
    type: 'line',
    <?php }else{ ?>
    type: 'column3d',
    <?php }?>
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
            <?php if($dayCount == 1){?>
                "showvalues": "0",
            <?php }else{ ?> 
                "showvalues": "1",
                "placeValuesInside": "0",
                "rotateValues": "0",
                "valueFont": "Arial",
                "valueFontColor": "#6699cc",
                "valueFontSize": "12",
                "valueFontBold": "1",
                "valueFontItalic": "0",
            <?php }?> 
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
    <h4 class="modal-title"><?php echo $meterName;?></h4>
</div>
<div class="modal-body">
    <h4>Data set of <?php echo $meterNameColumn;?></h4>
    <div id="chartContainer">Chart Loading</div>
</div>