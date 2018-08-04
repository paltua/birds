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
            "title": "<?php echo $title1;?>"
        }, {
            "id":"v2",
            "axisColor": "#FCD202",
            "axisThickness": 2,
            "axisAlpha": 1,
            "position": "right",
            "title": "<?php echo $title2;?>"
        },],
      "graphs": [{
        "valueAxis": "v1",
        /*"balloonText": "<b>Loading: [[value]]</b>",*/
        "balloonText": "<b><?php echo $title1;?>: [[value]]</b>",
        "fillColorsField": "color",
        "lineColor": "#FF6600",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "type": "column",
        "valueField": "v1",
        /*"title": "Loading",*/
        "title": "<?php echo $title1;?>",
        "labelText" : "[[value]]",
        <?php if($chartDataCount > 15){?>
        "labelRotation" : "-60"
        <?php }?>
      },
      {
        "valueAxis": "v2",
        "balloonText": "<b> <?php echo $title2;?>: [[value]]</b>",
        "fillColorsField": "color",
        "lineColor": "#FCD202",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "type": "column",
        "valueField": "v2",
        "title": "<?php echo $title2;?>",
        "labelText" : "[[value]]",
        <?php if($chartDataCount > 15){?>
        "labelRotation" : "-60"
        <?php }?>
      }],
      "chartCursor": {
        "categoryBalloonEnabled": true,
        "cursorAlpha": 0,
        "zoomable": false
      },
      <?php if($chartDataCount > 15){?>
        "chartScrollbar": {
            "scrollbarHeight": 5,
            "backgroundAlpha": 0.1,
            "backgroundColor": "#868686",
            "selectedBackgroundColor": "#67b7dc",
            "selectedBackgroundAlpha": 1,
        },
        <?php }?>
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
        ],
        "listeners": [{
                "event": "rendered",
                "method": function(e) {
                  var curtain = document.getElementById("curtain");
                  curtain.parentElement.removeChild(curtain);
                }
              }],
       
    }, 2000);

<?php if($chartDataCount > 15){?>
  chart.addListener("rendered", zoomChart);
  zoomChart();
  // this method is called when chart is first inited as we listen for "rendered" event
  function zoomChart() {
      // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
      chart.zoomToIndexes(Math.round(chart.dataProvider.length * 0.65), Math.round(chart.dataProvider.length * 1));
  }
<?php }?>
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo $meterName;?></h4>
</div>
<div class="modal-body">
    <div id="chartdiv"></div>
    <div id="curtain"><span>Chart is loading...</span></div>
</div>


