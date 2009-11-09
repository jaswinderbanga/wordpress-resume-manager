<?php

require_once(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/lib/simple_html_dom.php');

function resman_hresume_update() {
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
	
	if(preg_match('@^https?://[^.]*\.linkedin.com@', $url)) {
		$resume = resman_hresume_parse_linkedin($data[0]);
	}
	else if(preg_match('@^https?://[^.]*\.xing.com@', $url)) {
		$resume = resman_hresume_parse_xing($data[0]);
	}
	else {
		$resume = resman_hresume_parse_other($data[0]);
	}

	resman_hresume_update_db($resume);
}

function resman_hresume_parse_linkedin($hresume) {
	$resume = array();

	$resume['personal'] = array(
							'name'		=> $hresume->find('#name',0)->plaintext,
							'address'	=> $hresume->find('.contact .adr', 0)->plaintext
						);
	
	$resume['general'] = array(
							'occupation'	=> $hresume->find('.contact .title', 0)->plaintext,
							'abstract'		=> $hresume->find('#summary .summary', 0)->plaintext
						);

	$experience = $hresume->find('#experience .experience');
	
	$resume['experience'] = array();
	$ii = 0;
	foreach($experience as $exp) {
		$resume['experience'][$ii] = array(
										'start'		=> $exp->find('.dtstart', 0)->title,
										'end'		=> $exp->find('.dtend', 0)->title,
										'title'		=> $exp->find('.title', 0)->plaintext,
										'abstract'	=> $exp->find('.description', 0)->plaintext,
										'name'		=> $exp->find('.org', 0)->plaintext,
										'sector'	=> $exp->find('.organization-details', 0)->plaintext
									);
		$ii++;
	}
	
	$education = $hresume->find('#education .education');
	
	$resume['education'] = array();
	$ii = 0;
	foreach($education as $edu) {
		$resume['education'][$ii] = array(
										'start'		=> $edu->find('.dtstart', 0)->title,
										'end'		=> $edu->find('.dtend', 0)->title,
										'title'		=> $edu->find('.degree', 0)->plaintext,
										'abstract'	=> $edu->find('.notes', 0)->plaintext,
										'name'		=> $edu->find('.org', 0)->plaintext
									);
		$ii++;
	}
	
	$resume['skills'] = array(
							'other'	=> $hresume->find('#summary .skills', 0)->plaintext
						);
	
	return $resume;
}

function resman_hresume_parse_xing($hresume) {
	$resume = array();
	
	$resume['personal'] = array(
							'name'		=> $hresume->find('#address-container .name',0)->plaintext,
							'address'	=> $hresume->find('#photobox-city', 0)->plaintext . ', ' . $hresume->find('#photobox-country', 0)->plaintext
						);
	
	$resume['general'] = array(
							'occupation'	=> $hresume->find('#photobox-title', 0)->plaintext,
							'abstract'		=> $hresume->find('.col_two .clr .fl', 0)->plaintext
						);

	$experience = $hresume->find('.experience');
	
	$resume['experience'] = array();
	$ii = 0;
	foreach($experience as $exp) {
		$resume['experience'][$ii] = array(
										'start'		=> $exp->find('.dtstart', 0)->plaintext,
										'end'		=> $exp->find('.dtend', 0)->plaintext,
										'title'		=> $exp->find('.title', 0)->plaintext,
										'name'		=> $exp->find('.org', 0)->plaintext
									);
		$ii++;
	}
	
	$education = $hresume->find('.education');
	
	$resume['education'] = array();
	$ii = 0;
	foreach($education as $edu) {
		$resume['education'][$ii] = array(
										'start'		=> $edu->find('.dtstart', 0)->plaintext,
										'end'		=> $edu->find('.dtend', 0)->plaintext,
										'title'		=> $edu->find('.edu-department', 0)->plaintext,
										'abstract'	=> $edu->find('.edu-notes', 0)->plaintext,
										'name'		=> $edu->find('.edu-school .location', 0)->plaintext
									);
		$ii++;
	}
	
	$resume['skills'] = array();
	$other = array();
	foreach($hresume->find('.profile-col-content .skill') as $skill) {
		$other[] = $skill->plaintext;
	}
	$resume['skills']['other'] = implode(', ', $other);
	
	return $resume;
}

function resman_hresume_parse_other($hresume) {
}

function resman_hresume_update_db($resume) {
	global $wpdb;
}

?>