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
}

function resman_deactive() {
	wp_clear_scheduled_hook('resman_sync');
}

function resman_uninstall() {
	delete_option('resman_version');
	delete_option('resman_dbversion');

	resman_drop_db();
}
?>