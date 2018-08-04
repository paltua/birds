
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
      "marginRight": 70,
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
            "title": "Pressure"
        }, {
            "id":"v2",
            "axisColor": "#FCD202",
            "axisThickness": 2,
            "axisAlpha": 1,
            "position": "right",
            "title": "Flow"
        },],
      "graphs": [{
        "valueAxis": "v1",
        "balloonText": "<b>Pressure: [[value]]</b>",
        "fillColorsField": "color",
        "lineColor": "#FF6600",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "type": "column",
        "valueField": "pressure",
        "title": "Pressure",
        "labelText" : "[[value]]",
      },
      {
        "valueAxis": "v2",
        "balloonText": "<b> Flow: [[value]]</b>",
        "fillColorsField": "color",
        "lineColor": "#FCD202",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "type": "column",
        "valueField": "flow",
        "title": "Flow",
      }],
      "chartCursor": {
        "categoryBalloonEnabled": true,
        "cursorAlpha": 0,
        "zoomable": false
      },
      "categoryField": "date",
      "categoryAxis": {
        "gridPosition": "start",
        "labelRotation": 45,
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
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
    <div id="chartdiv">Chart Loading</div>
</div>


