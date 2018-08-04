<link href='http://127.0.0.1/e/admin_theme/nestable/nestable.css' rel='stylesheet' media='screen'>

<section class="content-header">
	<h1>Client Hierarchy</h1>
		<ol class="breadcrumb">
	<li class=''><a href='<?php echo $breadcrumb['home'];?>'>Home</a></li><li class='active'>Client Hierarchy</li></ol>
</section>
<section class="content">
	<?php if($msg != ''){ echo $msg;} ?>
	<div class="row">
		<div class="cf nestable-lists">
			<div class="dd" id="nestable">
				<ol class="dd-list">
					<li class="dd-item" data-id="0">
						<div class="dd-handle"><?php echo $clientDetails[0]->client_name;?>
						</div>
						<ol class="dd-list">
							<li class="dd-item" data-id="5">
								<div class="dd-handle">Item 5
								</div>
								<ol class="dd-list">
									<li class="dd-item" data-id="6"><div class="dd-handle">Item 6</div></li>
								</ol>
							</li>
						</ol>
					</li>
				</ol>
			</div>
			<div class="dd" id="nestable2">
				<ol class="dd-list">
					<li class="dd-item" data-id="13">
						<div class="dd-handle">Item 13</div>
					</li>
					<li class="dd-item" data-id="14">
						<div class="dd-handle">Item 14</div>
					</li>
				</ol>
			</div>
		</div>
		<p><strong>Serialised Output (per list)</strong></p>
		<textarea id="nestable-output"></textarea>
		<textarea id="nestable2-output"></textarea>
	</div>
</section>

<script src='http://127.0.0.1/e/admin_theme/nestable/jquery.nestable.js'></script>
<script>
$(document).ready(function(){
	var updateOutput = function(e){
	var list = e.length ? e : $(e.target),
	output = list.data('output');
	if (window.JSON) {
		output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
	} else {
		output.val('JSON browser support required for this demo.');
	}
};
// activate Nestable for list 1
$('#nestable').nestable({
	group: 1
})
.on('change', updateOutput);
// activate Nestable for list 2
$('#nestable2').nestable({
	group: 1
})
.on('change', updateOutput);
// output initial serialised data
updateOutput($('#nestable').data('output', $('#nestable-output')));
updateOutput($('#nestable2').data('output', $('#nestable2-output')));
$('#nestable-menu').on('click', function(e)
{
var target = $(e.target),
action = target.data('action');
if (action === 'expand-all') {
$('.dd').nestable('expandAll');
}
if (action === 'collapse-all') {
$('.dd').nestable('collapseAll');
}
});
$('#nestable3').nestable();
});
</script>
		
	