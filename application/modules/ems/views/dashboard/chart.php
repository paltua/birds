
<script src="<?php echo base_url();?>resource/am_chart/amcharts.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/serial.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/themes_light.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/export.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>resource/am_chart/export.css" type="text/css" media="all" />
<script src="<?php echo base_url();?>resource/am_chart/dataloader.min.js" type="text/javascript"></script>

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
        "dataProvider": [
                <?php 
                    if(count($chartData) > 0){
                    foreach ($chartData as $value) {
                ?>
                {
                    "date" : '<?php echo $value->end_date_time;?>',
                    "paramVal" : <?php echo round($value->data,2);?>
                },
                

                <?php }} ?>
        ],
        "startDuration": 1,
        "valueAxes": [{
            "id": "v1",
            "axisAlpha": 0.1,
            "title": "Values(<?php echo $meterNameColumn;?>)",
        }],
        "graphs": [{
            "balloonText": "<b><?php echo $meterNameColumn;?> : [[value]]</b><b><br>Timestamp: [[category]]</b>",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletBorderColor": "#FFFFFF",
            "hideBulletsCount": 50,
            "lineThickness": 2,
            "lineColor": "#67b7dc",
            "valueField": "paramVal"
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
       
    });

</script>


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
	<!-- <h4>Data set of <?php echo $meterNameColumn;?></h4> -->
    <!-- <div id="chartContainer">Chart Loading</div> -->
    <div id="chartdiv">Chart Loading</div>
</div>
<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
