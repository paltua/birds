<script src="<?php echo base_url();?>resource/am_chart/amcharts.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/serial.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/themes_light.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/export.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>resource/am_chart/export.css" type="text/css" media="all" />
<script src="<?php echo base_url();?>resource/am_chart/dataloader.min.js" type="text/javascript"></script>
<style type="text/css">    
    #chartdiv{
        width: 100%;
        height: 500px;
    }
    .amcharts-export-menu-top-right {
      top: 10px;
      right: 0;
    }

    #curtain {
        width: 100%;
        height: 100%;
        position: absolute;
        background: rgba(0, 0, 0, 0.8);
        color: #fff;
        font-size: 25px;
        top: 0;
        left: 0;
    }

    #curtain span {
      display: block;
      position: absolute;
      top: 49%;
      width: 100%;
      text-align: center;
    }

</style>
<script type="text/javascript">
    var chart = AmCharts.makeChart("chartdiv", {
        "theme": "light",
        "type": "serial",
        "marginRight": 70,
        "dataProvider": [<?php echo $chartData;?>],
        "startDuration": 1,
        "valueAxes": [{
            "id": "v1",
            "axisAlpha": 0.1,
            "title": "<?php echo $meterNameColumn;?>",
        }],
        "graphs": [{
            "balloonText": "<b><?php echo $meterNameColumn;?> : [[value]]</b><b><br>Timestamp: [[category]]</b>",
            "bullet": "round",
            "bulletSize": 8,
            "bulletBorderAlpha": 1,
            "bulletBorderColor": "#FFFFFF",
            /*"hideBulletsCount": 50,*/
            "lineThickness": 2,
            "lineColor": "#67b7dc",
            "valueField": "paramVal"
        }],
        <?php if($chartDataCount > 33){?>
        "chartScrollbar": {
            "scrollbarHeight": 5,
            "backgroundAlpha": 0.1,
            "backgroundColor": "#868686",
            "selectedBackgroundColor": "#67b7dc",
            "selectedBackgroundAlpha": 1,
        },
        <?php }?>
        "chartCursor": {
            "categoryBalloonEnabled": true,
            /*"cursorAlpha": 0,*/
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true
          },
        "categoryField": "date",
        "categoryAxis": {
            "autoGridCount": false,
            "gridCount": 96,
            "gridPosition": "start",
            "labelRotation": 60,
            "title" : "Timestamp"
        },
        "export": {
            "enabled": true
        },
        "titles": [{
            "text": "<?php echo $meterName;?>"
          }, {
            "text": "Data set of <?php echo $meterNameColumn;?>",
            "bold": false
          }],
        "listeners": [{
                "event": "rendered",
                "method": function(e) {
                  var curtain = document.getElementById("curtain");
                  curtain.parentElement.removeChild(curtain);
                }
              }],
       
    }, 2000);

    <?php if($chartDataCount > 33){?>
    chart.addListener("rendered", zoomChart);
    zoomChart();

    // this method is called when chart is first inited as we listen for "rendered" event
    function zoomChart() {
        // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
        chart.zoomToIndexes(Math.round(chart.dataProvider.length * 0.3), Math.round(chart.dataProvider.length * 0.65));
    }
    <?php }?>

</script>


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo $meterName;?></h4>
</div>
<div class="modal-body">
    <div id="chartdiv" class="chartDivContent"></div>
    <div id="curtain"><span>Chart is loading...</span></div>
</div>
