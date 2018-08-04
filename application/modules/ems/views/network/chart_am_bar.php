<?php //print_r($chartData);?>


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
        "theme": "light",
        "type": "serial",
        "marginRight": 70,
        "dataProvider": [<?php echo $chartData;?>],
        "startDuration": 1,
        "valueAxes": [{
            "id": "v1",
            "axisAlpha": 0.1,
            "title": "Values(KW)",
        }],
        "graphs": [{
            "balloonText": "<b>KW : [[value]]</b><b><br>Date: [[category]]</b>",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletBorderColor": "#FFFFFF",
            "hideBulletsCount": 50,
            "valueField": "paramVal",
            "fillColorsField": "color",
            "lineColor": "#FF6600",
            "fillAlphas": 0.9,
            "lineAlpha": 0.2,
            "type": "column",
            "labelText" : "[[value]]",
        }],
        /*"chartScrollbar": {
            "scrollbarHeight": 5,
            "backgroundAlpha": 0.1,
            "backgroundColor": "#868686",
            "selectedBackgroundColor": "#67b7dc",
            "selectedBackgroundAlpha": 1,
        },*/
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
            "labelRotation": 50,
            "title" : "Date"
        },
        "export": {
            "enabled": true
        },
        "titles": [{
            "text": "<?php echo $meterName;?>"
          }, {
            "text": "<?php echo $meterNameColumn;?>",
            "bold": false
          }],
       
    });

    
</script>


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo $meterName;?></h4>
</div>
<div class="modal-body">
	<!-- <h4>Data set of <?php echo $meterNameColumn;?></h4> -->
    <!-- <div id="chartContainer">Chart Loading</div> -->
    <div id="chartdiv">Chart Loading</div>
</div>
<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
