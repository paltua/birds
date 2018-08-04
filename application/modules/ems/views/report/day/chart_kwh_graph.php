<?php //echo "<pre>"; print_r($kwhData);?>

<script src="<?php echo base_url();?>resource/am_chart/amcharts.js"></script>
<script src="<?php echo base_url();?>resource/am_chart/pie.js"></script>
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
        "dataProvider": [
        <?php 
        if(count($kwhData) > 0){
            for ($i = 0; $i < count($kwhData) - 1; $i++) { 
               
        ?>
        {
            "name": "<?php echo date('Y-m-d',strtotime($kwhData[$i]->end_date_time)).$shiftName;?>",
            <?php if($kwhData[$i + 1]->data_kwh > 0 && $kwhData[$i]->data_kwh > 0){?>
            "startTime": <?php echo $kwhData[$i]->data_kwh;?>,
            "endTime": <?php echo $kwhData[$i + 1]->data_kwh;?>,
            "sub": <?php echo $kwhData[$i + 1]->data_kwh - $kwhData[$i]->data_kwh;?> ,
            <?php }else{?>
            "sub": 'N/A' ,
            <?php }?>
            
            "color": getRandomColor()
        },

        <?php }} ?>

        ],
        "valueAxes": [{
            "axisAlpha": 0,
            "gridAlpha": 0.1,
            "title": "KWH",
            "labelRotation": 60,
        }],
        "startDuration": 1,
        "graphs": [{
            "balloonText": "<b>[[category]]</b><br>Starts at : [[startTime]]<br>Ends at : [[endTime]]<br>Total KWH : [[sub]]",
            "colorField": "color",
            "fillAlphas": 0.8,
            "lineAlpha": 0,
            "openField": "startTime",
            "type": "column",
            "valueField": "endTime"
        }],
        "rotate": true,
        "columnWidth": 1,
        "categoryField": "name",
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
            "gridAlpha": 0.1,
            "position": "left",
            "title" : "Date",

        },
        "export": {
            "enabled": true
         },
        "titles": [{
            "text": "<?php echo $meterName;?>"
          }, {
            "text": "Values of <?php echo $meterNameColumn;?>",
            "bold": false
          }],
    });

    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

</script>


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Meter #<?php echo $meterName;?></h4>
</div>
<div class="modal-body">
    <div id="chartdiv">Chart Loading</div>
</div>
