   
<script type="text/javascript">
chartMultiSeries();
//chart1Line();
//chart1LineLoading();

function chartMultiSeries(){
    
    $('#chartContainerMultiSeries').insertFusionCharts({
        <?php if($dayCount == 1){?>
        type: 'MSColumn3DLineDY',
        <?php }else{ ?>
        type: 'MSColumn3DLineDY',
        <?php }?>
        id: 'myChart1',
        width: '100%',
        height: '400',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                <?php if($dayCount == 1){?>
                "caption": "<?php echo $meterName;?>",
                "subCaption": "<?php echo $meterNameColumn;?>",
                "captionFontSize": "14",
                "subcaptionFontSize": "14",
                "subcaptionFontBold": "0",
                "paletteColors": "#0075c2,#1aaf5d",
                "bgcolor": "#ffffff",
                "showBorder": "0",
                "showShadow": "0",
                "showCanvasBorder": "0",
                "usePlotGradientColor": "0",
                "legendBorderAlpha": "0",
                "legendShadow": "0",
                "showAxisLines": "0",
                "showAlternateHGridColor": "0",
                "divlineThickness": "1",
                "divLineIsDashed": "1",
                "divLineDashLen": "1",
                "divLineGapLen": "1",
                "xAxisName": "Date",
                "pyaxisname":"<?php echo $seriesname;?>",
                "syaxisname":"<?php echo $seriesname2;?>",
                "showValues": "0"
                <?php }else{ ?>
                "caption": "<?php echo $meterName;?>",
                "subCaption": "<?php echo $meterNameColumn;?>",
                "captionFontSize": "14",
                "subcaptionFontSize": "14",
                "subcaptionFontBold": "0",
                "paletteColors": "#0075c2,#1aaf5d",
                "bgcolor": "#ffffff",
                "showBorder": "0",
                "showShadow": "0",
                "showCanvasBorder": "0",
                "usePlotGradientColor": "0",
                "legendBorderAlpha": "0",
                "legendShadow": "0",
                "showAxisLines": "0",
                "showAlternateHGridColor": "0",
                "divlineThickness": "1",
                "divLineIsDashed": "1",
                "divLineDashLen": "1",
                "divLineGapLen": "1",
                "xAxisName": "Date",
                "pyaxisname":"<?php echo $seriesname;?>",
                "syaxisname":"<?php echo $seriesname2;?>",
                "showValues": "0"
                <?php }?>    
            },
            "categories": [
                {
                    "category": [
                        <?php echo $dataSetDate;?>
                    ]
                }
            ],
            "dataset": [
                {
                    "seriesname": "<?php echo $seriesname;?>",
                    "data": [
                        <?php echo $dataSet;?>
                    ]
                },
                {
                    "seriesname": "<?php echo $seriesname2;?>",
                    "parentyaxis": "S",
                    "color": "CC3300",
                    "renderAs": "Line",
                    "data": [
                        <?php echo $dataSet2;?>
                    ]
                }
            ],
    
        }
    });
}




</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
    <div id="chartContainerMultiSeries">Chart Loading</div>
</div>
<!-- <div class="modal-body">
    <h4>Data set of <?php echo $meterNameColumn2;?></h4>
    <div id="chartContainerLoading">Chart Loading</div>
</div> -->
<!-- <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div> -->
<!-- <div class="modal-body">
    <h4>Data set of <?php echo $meterNameColumn1;?></h4>
    <div id="chartContainer">Chart Loading</div>
</div> -->

