<?php $tableEvenCol = 3;?>

<style type="text/css">
	.headRCol {
		background: #90daa8;
	}
	.headR {
		background: #f3f0f0;
	}
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo $details[0]->STYLE;?></h4>
</div>
<div class="modal-body">
    <div class="panel panel-default">
    <div class="panel-heading">Details</div>
    <div class="panel-body">
    	<table class="table table-striped table-bordered table-hover no-footer" id="apisId" role="grid" aria-describedby="apisId_info">
		    <thead>
		      	<tr role="row">
		      		<?php for($header = 0; $header < $tableEvenCol; $header++){?>
	      			<th class="headRCol">
			          	<p>Header Name</p>
			        </th>
		      		<th class="headRCol">
			          	<p>Value</p>
			        </th>
			        <?php }?>
			        
			    </tr>
			    <?php 
			    $colCount = count($columns) - $tableEvenCol;
			    for ($styleLoop = $tableEvenCol; $styleLoop <= $colCount;  $styleLoop = $styleLoop + $tableEvenCol) {
			    ?>
			    <tr role="row">
			    	<?php for($header = 0; $header < $tableEvenCol; $header++){?>
				    	<?php if(isset($columns[$styleLoop + $header]->Field)){?>
			      			<th class="headR">
					          	<p><?php echo $head = $columns[$styleLoop + $header]->Field;?></p>
					        </th>
				      		<td style="">
					          	<p><?php echo $details[0]->{$head};?> </p>
					        </td>
					    <?php  }else{ ?>
					        <td style="">
					          	<p></p>
					        </td>
				      		<td style="">
					          	<p></p>
					        </td>
					    <?php }?>
				    <?php } ?>
			    </tr>
			    <?php } ?>
		    </thead> 
		</table>
    </div>
</div>
</div>