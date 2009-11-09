<?php 
function resman_sync_resume() {
	if(isset($_REQUEST['resman-sync-conf'])) {
		resman_sync_update_settings();
	}

?>
	<input type="hidden" name="resmanconfsubmit" value="1" />
	<div class="wrap">

		<h2><?php _e('Résumé Manager: Synchronisation', 'resman') ?></h2>
<?php
	$widths = array('60%', '39%');
	$functions = array(
					array('resman_sync_hresume_box'),
					array('resman_print_donate_box', 'resman_print_about_box')
				);
	$titles = array(
				array(__('Synchronise hResume Source', 'resman')),
				array(__('Donate', 'resman'), __('About This Plugin', 'resman'))
			);
	
	jobman_create_dashboard($widths, $functions, $titles);
?>
	</div>
<?php
}

function resman_sync_hresume_box() {
	$freq = get_option('jobman_sync_frequency');
?>
		<form action="" method="post">
		<input type="hidden" name="resman-sync-conf" value="1" />
		<p><?php printf(__('Résumé Manager can keep your résumé in sync with any <a href="%1s">hResume</a> source, such as <a href="%2s">LinkedIn</a>, <a href="%3s">Xing</a>, your own personal source, or any of the sources <a href="%4s">mentioned here</a>.', 'resman'), 'http://microformats.org/wiki/hresume', 'http://www.linkedin.com/', 'http://www.xing.com/', 'http://microformats.org/wiki/hresume-implementations#Implementations') ?></p>
		<p><span style="color:#f00; font-weight:bold;"><?php _e('Warning', 'resman') ?>:</span> <?php _e('Using this feature will delete all existing résumé data you have stored. Please make sure it is backed up properly before continuing.', 'resman') ?></p>
<?php
		if(version_compare(PHP_VERSION, '5.0.0', '<')) {
?>
		<p><span style="color:#f00; font-weight:bold;"><?php _e('Compatibility Warning', 'resman') ?>:</span> <?php _e('This functionality requires at least PHP version 5.0. Please contact your system administrator to arrange an upgrade.', 'resman') ?></p>
<?php
		}
?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e('URL path', 'resman') ?></th>
				<td><input class="regular-text code" type="text" name="hresume-path" value="<?php echo get_option('jobman_hresume_path') ?>" /></td>
				<td><span class="description"><?php _e('Enter the URL you want the Résumé Manager to use for importing the hResume information.', 'resman') ?></span></td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Update Frequency', 'resman') ?></th>
				<td>
					<select name="sync-frequency">
						<option value="never"<?php echo ($freq=='never')?(' selected="selected"'):('') ?>><?php _e('Never', 'resman') ?></option>
						<option value="hourly"<?php echo ($freq=='hourly')?(' selected="selected"'):('') ?>><?php _e('Hourly', 'resman') ?></option>
						<option value="twicedaily"<?php echo ($freq=='twicedaily')?(' selected="selected"'):('') ?>><?php _e('Twice Daily', 'resman') ?></option>
						<option value="daily"<?php echo ($freq=='daily')?(' selected="selected"'):('') ?>><?php _e('Daily', 'resman') ?></option>
				</td>
				<td><span class="description"><?php _e('Do you want the Résumé Manager to update your local résumé on a regular basis? Selecting \'Never\' will update your résumé just this once.', 'resman') ?></span></td>
			</tr>
		</table>
		
		<p class="submit"><input type="submit" name="submit"  class="button-primary" value="<?php _e('Update hResume Settings', 'jobman') ?>" /></p>
		</form>
<?php
}

function resman_sync_update_settings() {
	wp_clear_scheduled_hook('resman_sync');

	update_option('jobman_hresume_path', $_POST['hresume-path']);
	update_option('jobman_sync_frequency', $_POST['sync-frequency']);
	
	resman_sync_callback();
	
	if($_POST['hresume-path'] != '' && in_array($_POST['sync-frequency'], array('hourly', 'twicedaily', 'daily'))) {
		wp_schedule_event(time(), $_POST['sync-frequency'], 'resman_sync');
	}
}

function resman_sync_callback() {
	resman_hresume_update();
}
?>