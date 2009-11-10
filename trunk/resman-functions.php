<?php
function jobman_create_dashboard($widths, $functions, $titles) {
?>
<div id="dashboard-widgets-wrap">
	<div id='dashboard-widgets' class='metabox-holder'>
<?php
	$ii = 0;
	foreach($widths as $width) {
?>
		<div class='postbox-container' style='width:<?php echo $width ?>'>
			<div id='normal-sortables' class='meta-box-sortables'>
<?php
		$jj = 0;
		foreach($functions[$ii] as $function) {
			jobman_create_widget($function, $titles[$ii][$jj]);
			$jj++;
		}
?>
			</div>
		</div>
<?php
		$ii++;
	}
?>
	</div>
	<div class="clear"></div>
</div>
<?php
}

function jobman_create_widget($function, $title) {
?>
				<div id="resman-<?php echo $function ?>" class="postbox">
					<div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div>
					<h3 class='hndle'><span><?php echo $title ?></span></h3>
					<div class="inside">
<?php
	call_user_func($function);
?>
						<div class="clear"></div>
					</div>
				</div>
<?php
}

function resman_url($type = 'html') {
	$structure = get_option('permalink_structure');
	$url = get_option('resman_page_name');
	
	if($structure == '') {
		$return = get_option('home') . '?' . $url . '=' . $type;
	}
	else {
		$return = get_option('home') . '/' . $url . '/';
		if($type != 'html' && $type != '') {
			$return .=  $url . '.' . $type;
		}
	}

	return $return;
}

?>