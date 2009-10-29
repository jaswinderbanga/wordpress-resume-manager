<?php //encoding: utf-8
function resman_queryvars($qvars) {
	$qvars[] = 'resman';
	return $qvars;
}

function resman_add_rewrite_rules($wp_rewrite) {
	$url = get_option('resman_page_name');
	if(!$url) {
		return;
	}
	$new_rules = array( 
						"$url/?$" => "index.php?resman=$url.html",
						"$url/(.+)" => 'index.php?resman=' .
						$wp_rewrite->preg_index(1) );

	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}

function resman_flush_rewrite_rules() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function resman_display_resume($posts) {
	global $wp_query, $resman_formats;

	if(!isset($wp_query->query_vars['resman'])) {
		return $posts;
	}
	
	$url = get_option('resman_page_name');
	if($wp_query->query_vars['resman'] == "$url.html") {
		return resman_display_resume_html();
	}
	
	foreach($resman_formats as $type => $format) {
		if($wp_query->query_vars['resman'] == $url . '.' . $format[0]) {
			resman_display_resume_file($type);
		}
	}
	return NULL;
}

function resman_display_init() {
	wp_enqueue_style('resman-display', RESMAN_URL.'/css/display.css', false, RESMAN_VERSION);
}

function resman_display_template() {
	global $wp_query;
	
	$url = get_option('resman_page_name');
	
	if(isset($wp_query->query_vars['resman']) && $wp_query->query_vars['resman'] = "$url.html") {
		include(TEMPLATEPATH . '/page.php');
		exit;
	}
}

function resman_display_title($title, $sep, $seploc) {
	global $wp_query;
	
	$url = get_option('resman_page_name');
	
	if(isset($wp_query->query_vars['resman']) && $wp_query->query_vars['resman'] = "$url.html") {
		if($seploc == 'right') {
			$title = __('Résumé', 'resman') . " $sep ";
		}
		else {
			$title = " $sep " . __('Résumé', 'resman');
		}
	}
	
	return $title;
}

function resman_display_edit_post_link($link) {
	global $wp_query;
	
	$url = get_option('resman_page_name');
	
	if(isset($wp_query->query_vars['resman']) && $wp_query->query_vars['resman'] = "$url.html") {
		return admin_url('admin.php?page=resman-edit-resume');
	}
	
	return $link;
}

function resman_display_resume_html() {
	global $resman_formats;
	global $wpdb;
	
	$page = new stdClass;
	$content = '';
	
	$url = get_option('resman_page_name');
	
	$dateformat = get_option('resman_date_format');
	if($dateformat == '') {
		$dateformat = 'Y-m-d';
	}
	
	$page->post_title = __('Résumé', 'resman');
	
	$display_downloads = false;
	foreach($resman_formats as $label => $format) {
		if(get_option('resman_output_'.$label)) {
			$display_downloads = true;
			break;
		}
	}
	
	$content .= '<div class="hresume">';
	
	if($display_downloads) {
		$content .= '<p>' . __('This résumé is available in several different formats:', 'resman') . '</p>';
		$content .= '<ul>';
		foreach($resman_formats as $label => $format) {
			if(get_option('resman_output_'.$label)) {
				$content .= '<li><a href="' . get_option('siteurl') . '/' . $url . '/' . $url . '.' . $format[0] . '">' . $url . '.' . $format[0] . '</a> - ' . __($format[1], 'resman') . '</li>';
			}
		}
		$content .= '</ul>';
	}
	
	// Personal
	$sql = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'resman_data WHERE fieldid IN (SELECT id FROM ' . $wpdb->prefix . 'resman_fields WHERE section="personal");';
	$count = $wpdb->get_var($sql);
	if($count) {
		$content .= '<h2>' . __('Personal', 'resman') . '</h2>';
		$sql = 'SELECT d.value AS value, f.name AS name FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="personal";';
		$data = $wpdb->get_results($sql, ARRAY_A);
		$display_data = array();
		foreach($data as $item) {
			switch($item['name']) {
				case 'nationality':
					$display_data['nationality'][] = $item['value'];
					break;
				default:
					$display_data[$item['name']] = $item['value'];
			}
		}
		
		$content .= '<address class="vcard contact">';
		
		if(isset($display_data['name'])) {
			$content .= '<strong>' . __('Name', 'resman') . ':</strong> <span class="fn n">' . $display_data['name'] . '</span><br/>';
		}
		if(isset($display_data['dob'])) {
			$content .= '<strong>' . __('Date of Birth', 'resman') . ':</strong> <span class="bday">' . $display_data['dob'] . '</span><br/>';
		}
		if(isset($display_data['gender'])) {
			switch($display_data['gender']) {
				case 'm':
					$gender = __('Male', 'resman');
					break;
				case 'f':
					$gender = __('Female', 'resman');
					break;
			}
			$content .= '<strong>' . __('Gender', 'resman') . ':</strong> ' . $gender . '<br/>';
		}
		if(isset($display_data['nationality'])) {
			if(count($display_data['nationality']) == 1) {
				$content .= '<strong>' . __('Nationality', 'resman') . ':</strong> ' . $display_data['nationality'][0] . '<br/>';
			}
			else {
				$content .= '<strong>' . __('Nationalities', 'resman') . ':</strong> ' . implode(', ', $display_data['nationality']) . '<br/>';
			}
		}
		if(isset($display_data['address'])) {
			$content .= '<strong>' . __('Address', 'resman') . ':</strong><br/><span class="adr">' . $display_data['address'] . '</span><br/>';
		}
		if(isset($display_data['phone'])) {
			$content .= '<span class="tel"><strong><span class="type">' . __('Telephone', 'resman') . '</span>:</strong> <span class="pref">' . $display_data['phone'] . '</span></span><br/>';
		}
		if(isset($display_data['mobile'])) {
			$content .= '<span class="tel"><strong><span class="type">' . __('Mobile', 'resman') . '</span>:</strong> <span class="cell">' . $display_data['mobile'] . '</span></span><br/>';
		}
		if(isset($display_data['fax'])) {
			$content .= '<span class="tel"><strong><span class="type">' . __('Fax', 'resman') . '</span>:</strong> <span class="fax">' . $display_data['fax'] . '</span></span><br/>';
		}
		if(isset($display_data['email'])) {
			$content .= '<strong>' . __('E-mail', 'resman') . ':</strong> <a href="mailto:' . $display_data['email'] . '" class="email">' . $display_data['email'] . '</a><br/>';
		}
	
		$content .= '</address>';
	}
	
	// General
	$sql = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'resman_data WHERE fieldid IN (SELECT id FROM ' . $wpdb->prefix . 'resman_fields WHERE section="general");';
	$count = $wpdb->get_var($sql);
	if($count) {
		$content .= '<h2>' . __('Summary', 'resman') . '</h2>';
		$sql = 'SELECT d.value AS value, f.name AS name FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="general";';
		$data = $wpdb->get_results($sql, ARRAY_A);
		$display_data = array();
		foreach($data as $item) {
			$display_data[$item['name']] = $item['value'];
		}
		
		$content .= '<div class="summary">';
		
		if(isset($display_data['occupation'])) {
			$content .= '<strong>' . __('Desired Occupation', 'resman') . ':</strong> ' . $display_data['occupation'];
		}
		if(isset($display_data['abstract'])) {
			$content .= '<p>' . resman_format_abstract($display_data['abstract']) . '</p>';
		}
		
		$content .= '</div>';
	}
	// Experience
	$sql = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'resman_data WHERE fieldid IN (SELECT id FROM ' . $wpdb->prefix . 'resman_fields WHERE section="experience");';
	$count = $wpdb->get_var($sql);
	if($count) {
		$content .= '<h2>' . __('Experience', 'resman') . '</h2>';
		$sql = 'SELECT d.value AS value, f.name AS name, d.repeatgroup_count AS repeatgroup FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="experience" ORDER BY d.repeatgroup_count;';
		$data = $wpdb->get_results($sql, ARRAY_A);
		$display_data = array();
		foreach($data as $item) {
			$display_data[$item['repeatgroup']][$item['name']] = $item['value'];
		}
		
		$content .= '<div class="vcalendar">';
		
		usort($display_data, 'resman_sort_experience_education');
	
		foreach($display_data as $item) {
			$content .= '<div class="experience vevent vcard">';
			
			if(isset($item['title']) || isset($item['name']) || isset($item['sector'])) {
				$content .= '<strong>';
			}
			if(isset($item['title'])) {
				$content .= '<span class="summary">' . $item['title'] . '</span>';
			}
			if(isset($item['title']) && (isset($item['name']) || isset($item['sector']))) {
				$content .= ' – ';
			}
			if(isset($item['name'])) {
				$content .= '<span class="org">' . $item['name'] . '</span>';
			}
			if(isset($item['name']) && isset($item['sector'])) {
				$content .= ' – ';
			}
			if(isset($item['sector'])) {
				$content .= $item['sector'];
			}
			if(isset($item['title']) || isset($item['name']) || isset($item['sector'])) {
				$content .= '</strong><br/>';
			}

			if(isset($item['start']) || isset($item['end'])) {
				$content .= '<strong>';
				if(isset($item['start'])) {
					$content .= '<span class="dtstart">' . date($dateformat, strtotime($item['start'])) . '</span>';
				}
				else {
					$content .= __('Present', 'resman');
				}
				$content .= ' – ';
				if(isset($item['end'])) {
					$content .= '<span class="dtend">' . date($dateformat, strtotime($item['end'])) . '</span>';
				}
				else {
					$content .= __('Present', 'resman');
				}
				$content .= '</strong><br/>';
			}

			if(isset($display_data['address'])) {
				$content .= '<strong class="addr location">' . $display_data['address'] . '</strong><br/>';
			}
			
			if(isset($item['abstract'])) {
				$content .= '<div class="description">' . "\n\n" . resman_format_abstract($item['abstract']) . '</div>';
			}
			
			$content .= '</div>';
		}
		
		$content .= '</div>';
	}
	
	// Education
	$sql = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'resman_data WHERE fieldid IN (SELECT id FROM ' . $wpdb->prefix . 'resman_fields WHERE section="education");';
	$count = $wpdb->get_var($sql);
	if($count) {
		$content .= '<h2>' . __('Education', 'resman') . '</h2>';
		$sql = 'SELECT d.value AS value, f.name AS name, d.repeatgroup_count AS repeatgroup FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="education" ORDER BY d.repeatgroup_count;';
		$data = $wpdb->get_results($sql, ARRAY_A);
		$display_data = array();
		foreach($data as $item) {
			$display_data[$item['repeatgroup']][$item['name']] = $item['value'];
		}

		$content .= '<div class="vcalendar">';
		
		usort($display_data, 'resman_sort_experience_education');
	
		foreach($display_data as $item) {
			$content .= '<div class="education vevent vcard">';
			if(isset($item['title']) || isset($item['name']) || isset($item['field'])) {
				$content .= '<strong>';
			}
			if(isset($item['title'])) {
				$content .= '<span class="summary">' . $item['title'] . '</span>';
				if(isset($item['isced'])) {
					$content .= ' (ISCED Level ' . $item['isced'] . ')';
				}
			}
			if(isset($item['title']) && (isset($item['name']) || isset($item['field']))) {
				$content .= ' – ';
			}
			if(isset($item['name'])) {
				$content .= '<span class="location">' . $item['name'] . '</span>';
			}
			if(isset($item['name']) && isset($item['field'])) {
				$content .= ' – ';
			}
			if(isset($item['field'])) {
				$content .= $item['field'];
			}
			if(isset($item['title']) || isset($item['name']) || isset($item['field'])) {
				$content .= '</strong><br/>';
			}

			if(isset($item['start']) || isset($item['end'])) {
				$content .= '<strong>';
				if(isset($item['start'])) {
					$content .= '<span class="dtstart">' . date($dateformat, strtotime($item['start'])) . '</span>';
				}
				else {
					$content .= __('Present', 'resman');
				}
				$content .= ' – ';
				if(isset($item['end'])) {
					$content .= '<span class="dtend">' . date($dateformat, strtotime($item['end'])) . '</span>';
				}
				else {
					$content .= __('Present', 'resman');
				}
				$content .= '</strong><br/>';
			}

			if(isset($display_data['address'])) {
				$content .= '<strong class="addr">' . $display_data['address'] . '</strong><br/>';
			}
			
			if(isset($item['abstract'])) {
				$content .= '<div class="description">' . "\n\n" . resman_format_abstract($item['abstract']) . '</div>';
			}
			
			$content .= '</div>';
		}
		
		$content .= '</div>';
	}
	// Languages
	$sql = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'resman_data WHERE fieldid IN (SELECT id FROM ' . $wpdb->prefix . 'resman_fields WHERE section="language");';
	$count = $wpdb->get_var($sql);
	if($count) {
		$content .= '<h2>' . __('Languages', 'resman') . '</h2>';
		$sql = 'SELECT d.value AS value, f.name AS name, d.repeatgroup_count AS repeatgroup FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="language" ORDER BY d.repeatgroup_count;';
		$data = $wpdb->get_results($sql, ARRAY_A);
		$languages = array();
		$mothertongue = array();
		foreach($data as $item) {
			if($item['name'] == 'mothertongue') {
				$mothertongue[] = $item['value'];
			}
			else {
				$languages[$item['repeatgroup']][$item['name']] = $item['value'];
			}
		}
		
		if(count($mothertongue) == 1) {
			$content .= '<strong>' . __('Mother Tongue', 'resman') . ':</strong> ' . $mothertongue[0] . '<br/>';
		}
		else if(count($mothertongue) > 1) {
			$content .= '<strong>' . __('Mother Tongues', 'resman') . ':</strong> ' . implode(', ', $mothertongue) . '<br/>';
		}
		
		if(count($languages) > 0) {
			$content .= '<table><tr><th>' . __('Language', 'resman') . '</th><th colspan="2">' . __('Understanding', 'resman') . '</th><th colspan="2">' . __('Speaking', 'resman') . '</th><th>' . __('Writing', 'resman') . '</th></tr>';
			$content .= '<tr><td></td><td>' . __('Listening', 'resman') . '</td><td>' . __('Reading', 'resman') . '</td><td>' . __('Spoken Interaction', 'resman') . '</td><td>' . __('Spoken Production', 'resman') . '</td><td></td></tr>';
			foreach($languages as $language) {
				$content .= '<tr><td>' . $language['language'] . '</td>';
				$content .= '<td>' . $language['listening'] . '</td>';
				$content .= '<td>' . $language['reading'] . '</td>';
				$content .= '<td>' . $language['interaction'] . '</td>';
				$content .= '<td>' . $language['production'] . '</td>';
				$content .= '<td>' . $language['writing'] . '</td></tr>';
			}
			$content .= '</table>';
		}
	}
	// Skills
	$sql = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'resman_data WHERE fieldid IN (SELECT id FROM ' . $wpdb->prefix . 'resman_fields WHERE section="skills");';
	$count = $wpdb->get_var($sql);
	if($count) {
		$content .= '<h2>' . __('Skills', 'resman') . '</h2>';
		$sql = 'SELECT d.value AS value, f.name AS name FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="skills";';
		$data = $wpdb->get_results($sql, ARRAY_A);
		$skills = array();
		foreach($data as $item) {
			$skills[$item['name']] = $item['value'];
		}
		
		$content .= '<div class="skills">' . "\n\n";
		
		if(isset($skills['social'])) {
			$content .= '<strong>' . __('Social Skills') . '</strong><br/>';
			$content .= resman_format_abstract($skills['social']);
		}
		if(isset($skills['organisational'])) {
			$content .= '<strong>' . __('Organisational Skills') . '</strong><br/>';
			$content .= resman_format_abstract($skills['organisational']);
		}
		if(isset($skills['technical'])) {
			$content .= '<strong>' . __('Technical Skills') . '</strong><br/>';
			$content .= resman_format_abstract($skills['technical']);
		}
		if(isset($skills['computer'])) {
			$content .= '<strong>' . __('Computer Skills') . '</strong><br/>';
			$content .= resman_format_abstract($skills['computer']);
		}
		if(isset($skills['artistic'])) {
			$content .= '<strong>' . __('Artistic Skills') . '</strong><br/>';
			$content .= resman_format_abstract($skills['artistic']);
		}
		if(isset($skills['other'])) {
			$content .= '<strong>' . __('Other Skills') . '</strong><br/>';
			$content .= resman_format_abstract($skills['other']);
		}
		if(isset($skills['licence'])) {
			if(strstr($skills['licence'], ',')) {
				$content .= '<strong>' . __('Driving Licences') . ':</strong> ';
			} else {
				$content .= '<strong>' . __('Driving Licence') . ':</strong> ';
			}
			$content .= preg_replace('/[,]/', ', ', $skills['licence']);
		}
		
		$content .= '</div>';
	}
	// References
	$sql = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'resman_data WHERE fieldid IN (SELECT id FROM ' . $wpdb->prefix . 'resman_fields WHERE section="references");';
	$count = $wpdb->get_var($sql);
	if($count) {
		$content .= '<h2>' . __('References', 'resman') . '</h2>';
		if(get_option('resman_show_references')) {
			// Show References
			$sql = 'SELECT d.value AS value, f.name AS name, d.repeatgroup_count AS repeatgroup FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f on f.id = d.fieldid WHERE f.section="references" ORDER BY d.repeatgroup_count;';
			$data = $wpdb->get_results($sql, ARRAY_A);
			$references = array();
			foreach($data as $item) {
				$references[$item['repeatgroup']][$item['name']] = $item['value'];
			}
			
			foreach($references as $reference) {
				$content .= '<p>';
				if(isset($reference['name'])) {
					$content .= '<strong>' . __('Name', 'resman') . ':</strong> ' . $reference['name'] . '<br/>';
				}
				if(isset($reference['title'])) {
					$content .= '<strong>' . __('Title', 'resman') . ':</strong> ' . $reference['title'] . '<br/>';
				}
				if(isset($reference['employer'])) {
					$content .= '<strong>' . __('Employer', 'resman') . ':</strong> ' . $reference['employer'] . '<br/>';
				}
				if(isset($reference['email'])) {
					$content .= '<strong>' . __('E-mail', 'resman') . ':</strong> ' . $reference['email'] . '<br/>';
				}
				if(isset($reference['phone'])) {
					$content .= '<strong>' . __('Telephone', 'resman') . ':</strong> ' . $reference['phone'] . '<br/>';
				}
				$content .= '</p>';
			}
		}
		else {
			$content .= '<p>' . __('Please contact for references.', 'resman') . '</p>';
		}
	}
	
	$content .= '</div>';
	
	$hidepromo = get_option('resman_promo_link');
	
	if(!$hidepromo) {
		$content .= '<p class="resmanpromo">' . sprintf(__('This résumé was created using <a href="%s" title="%s">Résumé Manager</a> for WordPress, by <a href="%s">Gary Pendergast</a>.', 'resman'), 'http://pento.net/projects/wordpress-resume-manager/', __('WordPress Résumé Manager', 'resman'), 'http://pento.net') . '</p>';
	}
	
	$page->post_content = $content;
		
	return array($page);
}

function resman_sort_experience_education($a, $b) {
	$astart = strtotime($a['start']);
	$aend = strtotime($a['end']);
	$bstart = strtotime($b['start']);
	$bend = strtotime($b['end']);
	
	// Ongoing item, always highest
	if($aend == false) {
		return -1;
	}
	if($bend == false) {
		return 1;
	}
	
	if($aend > $bend) {
		return -1;
	}
	if($aend < $bend) {
		return 1;
	}
	
	if($astart > $bstart) {
		return -1;
	}
	if($astart < $bstart) {
		return 1;
	}

	return 0;
}

function resman_format_abstract($text) {
	$textsplit = preg_split("[\n]", $text);
	
	$listlevel = 0;
	$starsmatch = array();
	foreach($textsplit as $key => $line) {
		preg_match('/^[*]*/', $line, $starsmatch);
		$stars = strlen($starsmatch[0]);
		
		$line = preg_replace('/^[*]*/', '', $line);
		
		$listhtml_start = '';
		$listhtml_end = '';
		while($stars > $listlevel) {
			$listhtml_start .= '<ul>';
			$listlevel++;
		}
		while($stars < $listlevel) {
			$listhtml_start .= '</ul>';
			$listlevel--;
		}
		if($listlevel > 0) {
			$listhtml_start .= '<li>';
			$listhtml_end = '</li>';
		}
		
		$textsplit[$key] = trim($listhtml_start . $line . $listhtml_end);
	}

	$text = implode("\n", $textsplit);

	while($listlevel > 0) {
		$text .= '</ul>';
		$listlevel--;
	}
	
	// Bold
	$text = preg_replace("/'''(.*?)'''/", '<strong>$1</strong>', $text);
	
	// Italic
	$text = preg_replace("/''(.*?)''/", '<em>$1</em>', $text);

	//$text = '<p>' . $text . '</p>';
	return $text;
}

function resman_display_resume_file($type) {
	global $resman_formats;
	$url = get_option('resman_page_name');
	
	header('Content-type: ' . $resman_formats[$type][2]);
	header('Content-Disposition: attachment; filename="' . $url . '.' . $resman_formats[$type][0] .'"');
	
	readfile(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/ldxcache/resume.'.$resman_formats[$type][0]);
	
	exit;
}

?>