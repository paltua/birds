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
            "title": "Values(<?php echo $meterNameColumn;?>)",
        }],
        "graphs": [{
            "balloonText": "<b><?php echo $meterNameColumn;?> : [[value]]</b><b><br>Date: [[category]]</b>",
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
            "labelPosition" : "top",
            <?php if($chartDataCount > 15){?>
            "labelRotation" : "-60"
            <?php }?>
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
           "listeners": [{
                "event": "rendered",
                "method": function(e) {
                  var curtain = document.getElementById("curtain");
                  curtain.parentElement.removeChild(curtain);
                }
              }],
       
    }, 2000);

    
</script>


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo $meterName;?></h4>
</div>
<div class="modal-body">
    <div id="chartdiv"></div>
    <div id="curtain"><span>Chart is loading...</span></div>
</div>
<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
