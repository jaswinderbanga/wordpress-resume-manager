<?php //encoding: utf-8

function resman_activate() {
	$version = get_option('resman_version');
	$dbversion = get_option('resman_db_version');
	
	if($dbversion == '') {
		// Never been run, create the database.
		resman_create_db();
		resman_create_default_settings();
	}
	elseif($dbversion != RESMAN_DB_VERSION) {
		// New version, upgrade
		resman_upgrade_db($dbversion);
	}

	update_option('resman_version', RESMAN_VERSION);
	update_option('resman_dbversion', RESMAN_DB_VERSION);
}

function resman_create_default_settings() {
	update_option('resman_page_name', 'resume');
	update_option('resman_date_format', 'M Y');
}

function resman_deactive() {
	wp_clear_scheduled_hook('resman_sync');
}

function resman_uninstall() {
	global $resman_formats;
	delete_option('resman_version');
	delete_option('resman_dbversion');
	delete_option('resman_page_name');
	delete_option('resman_date_format');
	delete_option('resman_service_europass');
	delete_option('resman_show_references');
	delete_option('resman_promo_link');
	delete_option('resman_livedocx_username');
	delete_option('resman_livedocx_password');
	delete_option('resman_hresume_path');
	delete_option('resman_sync_frequency');

	foreach($resman_formats as $label => $format) {
		delete_option('resman_output_'.$label);
	}

	resman_drop_db();
}
?>