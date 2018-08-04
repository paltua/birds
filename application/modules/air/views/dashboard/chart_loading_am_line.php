
<style type="text/css">
   
    #chartdiv {
      width: 100%;
      height: 500px;
    }
    .amcharts-export-menu-top-right {
      top: 10px;
      right: 0;
    }
</style> 
<script type="text/javascript">
var chart = AmCharts.makeChart("chartdiv", {
      "type": "serial",
      "theme": "light",
      "legend": {
            "useGraphSettings": true
        },
      "dataProvider": [<?php echo $chartData;?>],    
      "startDuration": 1,
      "valueAxes": [{
            "id":"v1",
            "axisColor": "#FF6600",
            "axisThickness": 2,
            "axisAlpha": 1,
            "position": "left",
            "title": "Pressure",
            "gridAlpha": 0.2,
        }, {
            "id":"v2",
            "axisColor": "#FCD202",
            "axisThickness": 2,
            "axisAlpha": 1,
            "position": "right",
            "title": "Flow",
            "gridAlpha": 0.2,
        },],
      "graphs": [{
        "valueAxis": "v1",
        "balloonText": "<b>Pressure: [[value]]</b>",
        "fillColorsField": "color",
        "lineColor": "#FF6600",
        /*"fillAlphas": 0.9,*/
        "lineAlpha": 2,
        /*"type": "line",*/
        "valueField": "pressure",
        "title": "Pressure",
        "bullet": "round",
        "bulletSize": 8,
        "bulletBorderAlpha": 1,
        "bulletBorderColor": "#FFFFFF",
      },
      {
        "valueAxis": "v2",
        "balloonText": "<b> Flow: [[value]]</b>",
        "fillColorsField": "color",
        "lineColor": "#FCD202",
        /*"fillAlphas": 0.9,*/
        "lineAlpha": 2,
        /*"type": "line",*/
        "valueField": "flow",
        "title": "Flow",
        "bullet": "round",
        "bulletSize": 8,
        "bulletBorderAlpha": 1,
        "bulletBorderColor": "#FFFFFF",
      }],
      "chartScrollbar": {
          "scrollbarHeight": 5,
          "backgroundAlpha": 0.1,
          "backgroundColor": "#868686",
          "selectedBackgroundColor": "#67b7dc",
          "selectedBackgroundAlpha": 1,
      },
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
        "title": "Date",
      },
      "export": {
        "enabled": true
      },
      "titles": [
            {
                "text": "<?php echo $meterName;?>",
                "size": 15
            }, {
                "text": "<?php echo $meterNameColumn;?>",
                "bold": false
              }
        ]

});

chart.addListener("rendered", zoomChart);
zoomChart();

// this method is called when chart is first inited as we listen for "rendered" event
function zoomChart() {
    // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
    chart.zoomToIndexes(Math.round(chart.dataProvider.length * 0.3), Math.round(chart.dataProvider.length * 0.65));
}
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
    <div id="chartdiv">Chart Loading</div>
</div>


