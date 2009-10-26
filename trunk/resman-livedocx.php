<?php //encoding: utf-8
function resman_livedocx_create_documents() {
	global $wpdb;
	
	$fieldValues = array();
	
	$username = get_option('resman_livedocx_username');
	$password = get_option('resman_livedocx_password');
	
	$dateformat = get_option('resman_date_format');
	if($dateformat == '') {
		$dateformat = 'Y-m-d';
	}
	
	$ldx = new SoapClient(LIVEDOCX_URL);
	
	$ldx->LogIn(
			array(
				'username' => $username,
				'password' => $password
			));
			
	$template = file_get_contents(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/template/classic.docx');
	
	$ldx->SetLocalTemplate(
			array(
				'template' => base64_encode($template),
				'format'   => 'docx'
			));

	// PERSONAL
	$sql = 'SELECT d.value AS value, f.name AS name FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="personal";';
	$data = $wpdb->get_results($sql, ARRAY_A);
	$nationality = array();
	$fieldValues['personal-count'] = 0;
	foreach($data as $item) {
		switch($item['name']) {
			case 'nationality':
				$nationality[] = $item['value'];
				break;
			default:
				$fieldValues['personal-'.$item['name']] = $item['value'];
		}
		$fieldValues['personal-count']++;
	}
	if(count($nationality)) {
		$fieldValues['personal-nationality'] = implode(', ', $nationality);
	}

	// GENERAL
	$sql = 'SELECT d.value AS value, f.name AS name FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="general";';
	$data = $wpdb->get_results($sql, ARRAY_A);
	$fieldValues['general-count'] = 0;
	foreach($data as $item) {
		$fieldValues['general-'.$item['name']] = $item['value'];
		$fieldValues['general-count']++;
	}

	// EXPERIENCE
	$sql = 'SELECT d.value AS value, f.name AS name, d.repeatgroup_count AS repeatgroup FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="experience" ORDER BY d.repeatgroup_count;';
	$data = $wpdb->get_results($sql, ARRAY_A);
	$experience = array();
	$fieldValues['experience-count'] = 0;
	foreach($data as $item) {
		switch($item['name']) {
			case 'start':
			case 'end':
				$experience[$item['repeatgroup']-1]['experience-'.$item['name']] = date($dateformat, strtotime($item['value']));
				break;
			case 'abstract':
				$experience[$item['repeatgroup']-1]['experience-'.$item['name']] = resman_livedocx_format_abstract($item['value']);
				break;
			default:
				$experience[$item['repeatgroup']-1]['experience-'.$item['name']] = $item['value'];
		}
	}
	
	$fields = array('start', 'end', 'title', 'abstract', 'name', 'address', 'sector');
	$expBlock = array();
	foreach($experience as $key => $exp) {
		if(!isset($experience[$key]['experience-start'])) {
			$experience[$key]['experience-start'] = __('Present', 'resman');
		}
		if(!isset($experience[$key]['experience-end'])) {
			$experience[$key]['experience-end'] = __('Present', 'resman');
		}
		
		foreach($fields as $field) {
			if(!isset($experience[$key]['experience-'.$field])) {
				$experience[$key]['experience-'.$field] = '';
			}
			
			$expBlock[$fieldValues['experience-count']]['experience-'.$field] = $experience[$key]['experience-'.$field];
		}
		$fieldValues['experience-count']++;
	}

	$ldx->SetBlockFieldValues(
			array(
				'blockName'        => 'experience',
				'blockFieldValues' => resman_multiAssocArrayToArrayOfArrayOfString($expBlock)
			));
			
	// EDUCATION
	$sql = 'SELECT d.value AS value, f.name AS name, d.repeatgroup_count AS repeatgroup FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="education" ORDER BY d.repeatgroup_count;';
	$data = $wpdb->get_results($sql, ARRAY_A);
	$education = array();
	$fieldValues['education-count'] = 0;
	foreach($data as $item) {
		switch($item['name']) {
			case 'start':
			case 'end':
				$education[$item['repeatgroup']-1]['education-'.$item['name']] = date($dateformat, strtotime($item['value']));
				break;
			case 'abstract':
				$education[$item['repeatgroup']-1]['education-'.$item['name']] = resman_livedocx_format_abstract($item['value']);
				break;
			default:
				$education[$item['repeatgroup']-1]['education-'.$item['name']] = $item['value'];
		}
	}
	
	$fields = array('start', 'end', 'title', 'abstract', 'name', 'address', 'field');
	$eduBlock = array();
	foreach($education as $key => $exp) {
		if(!isset($education[$key]['education-start'])) {
			$education[$key]['education-start'] = __('Present', 'resman');
		}
		if(!isset($education[$key]['education-end'])) {
			$education[$key]['education-end'] = __('Present', 'resman');
		}
		
		foreach($fields as $field) {
			if(!isset($education[$key]['education-'.$field])) {
				$education[$key]['education-'.$field] = '';
			}
			
			$eduBlock[$fieldValues['education-count']]['education-'.$field] = $education[$key]['education-'.$field];
		}
		$fieldValues['education-count']++;
	}

	$ldx->SetBlockFieldValues(
			array(
				'blockName'        => 'education',
				'blockFieldValues' => resman_multiAssocArrayToArrayOfArrayOfString($eduBlock)
			));
			

	// SKILLS
	$sql = 'SELECT d.value AS value, f.name AS name FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="skills";';
	$data = $wpdb->get_results($sql, ARRAY_A);
	$fieldValues['skills-count'] = 0;
	foreach($data as $item) {
		$fieldValues['skills-'.$item['name']] = resman_livedocx_format_abstract($item['value']);
		$fieldValues['skills-count']++;
	}


	$ldx->SetFieldValues(
			array(
				'fieldValues' => resman_assocArrayToArrayOfArrayOfString($fieldValues)
			));

	$ldx->CreateDocument();
	
	resman_livedocx_write_document($ldx, 'html');
	$doctypes = array('txt', 'pdf', 'doc', 'docx', 'rtf');
	foreach($doctypes as $doc) {
		if(get_option('resman_output_'.$doc)) {
			resman_livedocx_write_document($ldx, $doc);
		}
	}

	$ldx->LogOut();
	
	unset($ldx);
}

function resman_livedocx_write_document($ldx, $format) {
	$res = $ldx->RetrieveDocument(
				array(
					'format' => $format
				));
	
	$data = $res->RetrieveDocumentResult;
	
	file_put_contents(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/ldxcache/resume.'.$format, base64_decode($data));

}

function resman_livedocx_info() {
?>
	<div class="wrap">
		<h2><?php _e('LiveDocx Info', 'resman')?></h2>
		<div id="dashboard-widgets-wrap">
		<div id='dashboard-widgets' class='metabox-holder'>
			<div class='postbox-container' style='width:60%;'>
			<div id='normal-sortables' class='meta-box-sortables'>
			<div class="meta-box-sortabless">
<?php
	$up = resman_livedocx_test_connection();
?>
<?php
	if($up && $_REQUEST['resmanforcecreate']) {
		resman_livedocx_create_documents();
	}
	if($up) {
		resman_livedocx_force_create();
		resman_livedocx_fonts();
	}
?>
			</div></div></div>
			<div class='postbox-container' style='width:39%;'>
			<div id='normal-sortables' class='meta-box-sortables'>
			<div class="meta-box-sortabless">
<?php
	resman_print_info();
?>
			</div></div></div>
		</div></div>
	</div>
<?php
}

function resman_livedocx_test_connection() {
	$username = get_option('resman_livedocx_username');
	$password = get_option('resman_livedocx_password');
	
	$ldx = new SoapClient(LIVEDOCX_URL);
	
	$up = true;
	$error = '';
?>
<div id="ldx_status" class="postbox">
	<div class="handlediv" title="Click to toggle"><br /></div>
	<h3 class="hndle"><span><?php _e('LiveDocx Server Status', 'resman') ?></span></h3>
	<div class="inside"><p><?php _e('Server Is', 'resman') ?>: 
<?php
	try {
		$ldx->LogIn(
				array(
					'username' => $username,
					'password' => $password
				));
	} catch (Exception $e) {
		$error = $e->faultstring;
		$up = false;
	}

	echo '<strong';
	if($up) {
		echo '>';
		_e('Up', 'resman');
	} else {
		echo ' class="error">';
		_e('Down', 'resman');
	}
	echo '</strong>';

	if(strlen($error)) {
		echo '<br/>';
		echo $error;
	}
	
	if($up) {
		$ldx->LogOut();
	}
	
	unset($ldx);
?>
	</p></div>
</div>
<?php
	return $up;
}

function resman_livedocx_fonts() {
	$username = get_option('resman_livedocx_username');
	$password = get_option('resman_livedocx_password');
	
	$ldx = new SoapClient(LIVEDOCX_URL);

	$ldx->LogIn(
			array(
				'username' => $username,
				'password' => $password
			));

	$result = $ldx->GetFontNames();

?>
<div id="ldx_fonts" class="postbox"> 
	<div class="handlediv" title="Click to toggle"><br /></div>
	<h3 class="hndle"><span><?php _e('LiveDocx Fonts', 'resman') ?></span></h3>
	<div class="inside"><p>
		<ul>
<?php

	foreach ($result->GetFontNamesResult->string as $format) {
		if(strpos($format, '@') === 0) {
			continue;
		}
?>
	<li><span style="font-family:<?php echo $format ?>"><?php echo $format ?></span> (<?php echo $format ?>)</li>
<?php
	}	
?>
		</ul>
	</p></div>
</div>
<?php
	$ldx->LogOut();
	
	unset($ldx);
}

function resman_livedocx_force_create() {
?>
<form action="" method="post">
<div id="ldx_forcecreate" class="postbox"> 
	<div class="handlediv" title="Click to toggle"><br /></div>
	<h3 class="hndle"><span><?php _e('Create Résumé Documents', 'resman') ?></span></h3>
	<div class="inside">
		<input type="hidden" name="resmanforcecreate" value="1" />
		<p><?php _e('This will force new copies of your Résumé to be created by LiveDocx and cached on your WordPress host.', 'resman') ?></p>
		<p class="submit"><input type="submit" name="submit"  class="button-primary" value="<?php _e('Create Documents', 'resman') ?>" /></p>
		<div class="clear"></div>
	</div>
</div>
</form>
<?php
}

function resman_livedocx_format_abstract($text) {
	$textsplit = preg_split("[\n]", $text);
	
	foreach($textsplit as $key => $line) {
		$textsplit[$key] = preg_replace('/^[*]*/', '', $line);
	}

	$text = implode("\n", $textsplit);

	$text = preg_replace("/'''(.*?)'''/", '$1', $text);
	$text = preg_replace("/''(.*?)''/", '$1', $text);

	return $text;
}


function resman_assocArrayToArrayOfArrayOfString($assoc) {
	$arrayKeys   = array_keys($assoc);
	$arrayValues = array_values($assoc);
	
	return array($arrayKeys, $arrayValues);
}

function resman_multiAssocArrayToArrayOfArrayOfString($multi) {
	$arrayKeys   = array_keys($multi[0]);
	$arrayValues = array();

	foreach ($multi as $v) {
		$arrayValues[] = array_values($v);
	}

	$arrayKeys = array($arrayKeys);

	return array_merge($arrayKeys, $arrayValues);
}
?>