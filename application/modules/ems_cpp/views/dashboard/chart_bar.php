
<script src="<?php echo base_url();?>resource/am_chart/amcharts.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/pie.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/serial.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/themes_light.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/export.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>resource/am_chart/export.css" type="text/css" media="all" />
<style type="text/css">
    #chartContainer {
      width: 100%;
      height: 500px;
    }
    .amcharts-export-menu-top-right {
      top: 10px;
      right: 0;
    }
</style>
<script type="text/javascript">
    var chart = AmCharts.makeChart("chartContainer", {
        "theme": "light",
        "type": "serial",
        "marginRight": 70,
        "dataProvider": [
                <?php 
                    if(count($chartData) > 0){
                    foreach ($chartData as $value) {
                ?>
                {
                    "date" : "<?php echo $value->m_y;?>",
                    "kw" : <?php echo round($value->data,2);?>,
                },
                

                <?php }} ?>
        ],
        "startDuration": 1,
        "valueAxes": [{
            "id": "v1",
            "axisAlpha": 0.1,
            "title": "Values(KW)",
        }],
        "graphs": [{
            "balloonText": "<b>KW : [[value]]</b><b><br>Month-Year: [[category]]</b>",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletBorderColor": "#FFFFFF",
            "hideBulletsCount": 50,
            "valueField": "kw",
           	"fillColorsField": "color",
		    "lineColor": "#FF6600",
		    "fillAlphas": 0.9,
		    "lineAlpha": 0.2,
		    "type": "column",
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
            "title" : "Month-Year"
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
<?php //print_r($chartData);?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
    <!-- <h4><?php echo $meterNameColumn;?></h4> -->
    <div id="chartContainer">Chart Loading</div>
</div>