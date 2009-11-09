<?php

require_once(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/lib/simple_html_dom.php');

function resman_hresume_update() {
	global $wpdb;
	
	$url = get_option('jobman_hresume_path');

	if($url == '') {
		return;
	}
	
	// Set our User-Agent string, so whoever we're scraping knows it's us.
	$ua = ini_get('user_agent');
	ini_set('user_agent', 'Wordpress-Resume-Manager/' . RESMAN_VERSION . ' http://pento.net/projects/wordpress-resume-mananger-plugin/');

	$html = file_get_html($url);

	ini_set('user_agent', $ua);
	
	$data = $html->find('.hresume, #hresume');
	
	if(count($data) <= 0) {
		// There was no hResume base element in the html
		return;
	}
	
	$hresume = $data[0];
	
	echo $hresume;
}

?>