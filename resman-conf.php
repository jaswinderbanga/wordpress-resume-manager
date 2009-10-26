<?php //encoding: utf-8

function resman_admin_setup() {
	// Setup the admin menu item
	$file = WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/resman.php';
	add_menu_page(__('Résumé Mgr', 'resman'), __('Résumé Mgr', 'resman'), 'manage_options', $file, 'resman_conf');
	add_submenu_page($file, __('Résumé Manager', 'resman'), __('Settings', 'resman'), 'manage_options', $file, 'resman_conf');
	$pageref = add_submenu_page($file, __('Résumé Manager', 'resman'), __('Edit Résumé', 'resman'), 'manage_options', 'resman-edit-resume', 'resman_edit_resume');
	add_submenu_page($file, __('Résumé Manager', 'resman'), __('LiveDocx Info', 'resman'), 'manage_options', 'resman-livedocx-info', 'resman_livedocx_info');
	
	// Load our header info
	add_action('admin_head-'.$pageref, 'resman_admin_header');
	wp_enqueue_script('resman-admin', RESMAN_URL.'/js/admin.js', false, RESMAN_VERSION);
	wp_enqueue_script('jquery-ui-datepicker', RESMAN_URL.'/js/jquery-ui-datepicker.js', array('jquery-ui-core'), RESMAN_VERSION);
	wp_enqueue_style('resman-admin', RESMAN_URL.'/css/admin.css', false, RESMAN_VERSION);
	
	wp_enqueue_style('dashboard');
	
	// Load some JQuery stuff
	wp_enqueue_script('dashboard');
	wp_enqueue_script('jquery-ui-tabs');
}

function resman_admin_header() {
?>
<script type="text/javascript"> 
//<![CDATA[
addLoadEvent(function() {
	jQuery("#tabs").tabs();
	jQuery(".datepicker").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, gotoCurrent: true});
});
//]]>
</script> 
<?php
}

function resman_conf() {
	global $resman_formats;
	if(isset($_POST['resmanconfsubmit'])) {
		// Configuration form as been submitted. Updated the database.
		resman_conf_updatedb();
	}
?>
	<form action="" method="post">
	<input type="hidden" name="resmanconfsubmit" value="1" />
	<div class="wrap">

		<h2><?php _e('Résumé Manager: Settings', 'resman') ?></h2>
		<div id="dashboard-widgets-wrap">
		<div id='dashboard-widgets' class='metabox-holder'>
		<div class='postbox-container' style='width:60%;'>
		<div id='normal-sortables' class='meta-box-sortables'>
		<div class="meta-box-sortabless">

		<div id="resman-settings" class="postbox">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span><?php _e('Settings', 'resman') ?></span></h3>
		<div class="inside">

		<table class="form-table">
			<tr>
				<th scope="row"><?php _e('URL path', 'resman') ?></th>
				<td><input class="small-text code" type="text" name="page-name" value="<?php echo get_option('resman_page_name') ?>" /></td>
				<td><span class="description"><?php _e('Enter the URL you want the Résumé Manager to use for displaying your resume.', 'resman') ?></span></td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Date Format', 'resman') ?></th>
				<td><input class="small-text code" type="text" name="date-format" value="<?php echo get_option('resman_date_format') ?>" /></td>
				<td><span class="description"><?php echo sprintf(__('How would you like dates to be displayed on your Resume? Use the format from the <a href="%s">PHP date() function</a>.', 'resman'), 'http://php.net/manual/en/function.date.php') ?></span></td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Output formats', 'resman') ?></th>
				<td width="180px">
<?php
	foreach($resman_formats as $label => $format) {
?>
					<input type="checkbox" value="1" name="output-<? echo $label ?>" <?php echo (get_option('resman_output_'.$label))?('checked="checked" '):('') ?>/> <?php _e($format[1], 'resman') . ' (' . $format[0] . ')' ?><br />
<?php
	}
?>
				</td>
				<td><span class="description"><?php _e('Select which formats you want your résumé to be available in.', 'resman') ?></span></td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Enable Europass Web Service?', 'resman') ?></th>
				<td><input type="checkbox" value="1" name="service-europass" <?php echo (get_option('resman_service_europass'))?('checked="checked" '):('') ?>/></td>
				<td><span class="description"><?php _e('Do you want to have your blog act as a Europass server?', 'resman') ?></span></td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Show References on Public résumé?', 'resman') ?></th>
				<td><input type="checkbox" value="1" name="show-references" <?php echo (get_option('resman_show_references'))?('checked="checked" '):('') ?>/></td>
				<td><span class="description"><?php _e('Do you want to your references to be visible on your Public résumé?', 'resman') ?></span></td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Hide "Powered By" link?', 'resman') ?></th>
				<td><input type="checkbox" value="1" name="promo-link" <?php echo (get_option('resman_promo_link'))?('checked="checked" '):('') ?>/></td>
				<td><span class="description"><?php _e('If you\'re unable to donate, I would appreciate it if you left this unchecked.', 'resman') ?></span></td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="submit"  class="button-primary" value="<?php _e('Update Settings', 'resman') ?>" /></p>
		<div class="clear"></div>

		</div></div>

		<div id="resman-ldx" class="postbox">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span><?php _e('LiveDocx Authentication', 'resman') ?></span></h3>
		<div class="inside">

		<p><?php printf(__('Résumé Manager uses the <a href="%1s">LiveDocx</a> service to generate the Plain Text, PDF, Microsoft Word and Rich Text Format versions of your Résumé. In order to use this service, you will need a LiveDocx account. If you don\'t already have one, you can <a href="%2s">register for one here</a>.', 'resman'), 'http://www.livedocx.com/', 'https://www.livedocx.com/user/account_registration.aspx') ?></p>
		<p><span style="color:#f00; font-weight:bold;"><?php _e('Security Warning', 'resman') ?>:</span> <?php _e('Your LiveDocx username and password will be stored in plain text in your Wordpress database. It is recommended that you use a password different to any you have used elsewhere.', 'resman') ?></p>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e('LiveDocx Username', 'resman') ?></th>
				<td><input class="regular-text code" type="text" name="livedocx-username" value="<?php echo get_option('resman_livedocx_username') ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php _e('LiveDocx Password', 'resman') ?></th>
				<td><input class="regular-text code" type="password" name="livedocx-password" value="<?php echo get_option('resman_livedocx_password') ?>" /></td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="submit"  class="button-primary" value="<?php _e('Update Settings', 'resman') ?>" /></p>
		<div class="clear"></div>
		
		</div></div>
		
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
	</form>
<?php
}

function resman_edit_resume() {
	$pages = array(	'personal'  => __('Personal', 'resman'),
					'general'   => __('General', 'resman'),
					'experience'=> __('Experience', 'resman'),
					'education' => __('Education', 'resman'),
					'language'  => __('Languages', 'resman'),
					'skills'    => __('Skills', 'resman'),
					'references'=> __('References', 'resman')
			);
			
	if(isset($_POST['resmansubmit'])) {
		// Résumé form has been submitted. Update the database.
		resman_resume_updatedb();
	}
	if(isset($_POST['ldx-create'])) {
		// User wants to update the LiveDocx-based documents
		resman_livedocx_create_documents();
	}
?>
	<form action="" method="post">
	<input type="hidden" name="resmansubmit" value="1" />
	<div class="wrap">
		<h2><?php _e('Résumé Manager: Editor', 'resman') ?></h2>
		<div id="tabs">
		<ul class="tabs">
<?php
	foreach($pages as $page => $title) {
?>
			<li><a href="#resman-<?php echo $page ?>"><?php echo $title ?></a></li>
<?php
	}
?>
		</ul>
<?php

	foreach($pages as $page => $title) {
?>
		<div id="resman-<?php echo $page ?>" class="pane">
<?php
		resman_print_config($page, $title);
?>
		</div>
<?php
	}
?>
		</div>
		<p class="submit">
			<input type="checkbox" name="ldx-create" value="1" /> <?php _e('Re-create cached copies of the résumé? (This may take up to 10 seconds.)', 'resman') ?><br/><br/>
			<input type="submit" name="submit"  class="button-primary" value="<?php _e('Update Résumé', 'resman') ?>" />
		</p>
	</div>
	</form>
<?php
}

function resman_print_config($page, $title) {
	global $wpdb;
	
	$sql = 'SELECT f.id AS id, f.name AS name, f.label AS label, f.inputtype AS inputtype, g.name AS groupname, g.label AS grouplabel, d.value AS data, d.repeatgroup_count AS repeatgroup FROM ';
	$sql .= $wpdb->prefix . 'resman_fields AS f ';
	$sql .= 'LEFT JOIN ';
	$sql .= $wpdb->prefix . 'resman_repeated_groups AS g ';
	$sql .= 'ON g.id = f.groupid ';
	$sql .= 'LEFT JOIN ';
	$sql .= $wpdb->prefix . 'resman_data AS d ';
	$sql .= 'ON d.fieldid = f.id ';
	$sql .= "WHERE f.section='$page' ";
	$sql .= 'GROUP BY id ';
	$sql .= 'ORDER BY f.display_order, d.repeatgroup_count;';
	
	$fields = $wpdb->get_results($sql, ARRAY_A);
?>
				<h3><?php echo __('Résumé', 'resman') . ': ' . $title ?></h3>

				<table class="form-table">
<?php
	$repeatgroup = NULL;
	$repeatgroup_first = false;
	$repeatgroup_last = false;
	$repeatgroupset_last = false;
	$repeatgroup_count = 0;
	
	$repeatgroup_repeat = array();
	
	$output_html = '';

	foreach($fields as $key => $field) {
		if($repeatgroup == NULL && $field['groupname'] != NULL) {
			$repeatgroup = $field['groupname'];
			$repeatgroup_first = true;
			$internal_template = $template_html = '';
			
			$output_html .= '{{{' . $repeatgroup . 'placeholder}}}';
			
			$fieldidlist = array();
		}
		
		if($repeatgroup != NULL) {
			array_push($fieldidlist, $field['id']);
		}

		if($repeatgroup != NULL) {
			if(isset($fields[$key+1])) {
				if($fields[$key+1]['groupname'] != $repeatgroup) {
					$repeatgroup_last = true;
				}
			}
			else {
				$repeatgroup_last = true;
			}
		}
		
		$classname = '';
		if($repeatgroup != NULL) {
			$classname = 'repeatgroup';
		}
		if($repeatgroup_first) {
			$classname .= ' repeatgroup_first';
		}
		if($repeatgroup_last) {
			$classname .= ' repeatgroup_last';
		}
		
		$classtext = '';
		if($classname != '') {
			$classtext = ' class="' . $classname . '"';
		}
		
		if($repeatgroup != NULL) {
			$template_html .= '<tr' . $classtext . '>';
			$template_html .= '<th scope="row">';
			$internal_template .= '<tr' . $classtext . '>';
			$internal_template .= '<th scope="row">';
		} else {
			$output_html .= '<tr' . $classtext . '>';
			$output_html .= '<th scope="row">';
		}

		if($field['inputtype'] == 'title') {
			if($repeatgroup != NULL) {
				$template_html .= '<strong>' . __($field['label'], 'resman') . '</strong>';
				$internal_template .= '<strong>' . __($field['label'], 'resman') . '</strong>';
			}
			else {
				$output_html .= '<strong>' . __($field['label'], 'resman') . '</strong>';
			}
		}
		else {
			if($repeatgroup != NULL) {
				$template_html .= __($field['label'], 'resman');
				$internal_template .= __($field['label'], 'resman');
			}
			else {
				$output_html .= __($field['label'], 'resman');
			}
		}
		
		if($repeatgroup != NULL) {
			$template_html .= '</th><td>';
			$internal_template .= '</th><td>';
		}
		else {
			$output_html .= '</th><td>';
		}

		$fieldname = $page . $field['name'];
		$template_fieldname = $fieldname;
		if($repeatgroup != NULL) {
			$fieldname .= $repeatgroup_count;
		}
		switch($field['inputtype']) {
			case 'title':
				break;
			case 'option':
			case 'select':
			case 'checkbox':
				if($repeatgroup != NULL) {
					$template_html .= resman_print_config_group($field['inputtype'], $field['id'], $fieldname, $template_fieldname, NULL);
					$internal_template .= resman_print_config_group($field['inputtype'], $field['id'], $fieldname, $template_fieldname, '{{{' . $template_fieldname . 'data-placeholder}}}');
				}
				else {
					$output_html .= resman_print_config_group($field['inputtype'], $field['id'], $fieldname, '', $field['data']);
				}
				break;
			case 'textarea':
				if($repeatgroup != NULL) {
					$template_html .= '<textarea class="large-text code" rows="5" name="' . $template_fieldname . '"></textarea>';
					$internal_template .= '<textarea class="large-text code" rows="5" name="' . $template_fieldname . '">{{{' . $template_fieldname . 'data-placeholder}}}</textarea>';
				}
				else {
					$output_html .= '<textarea class="large-text code" rows="5" name="' . $fieldname . '">' . $field['data'] . '</textarea>';
				}
				break;
			case 'date':
				if($repeatgroup != NULL) {
					$template_html .= '<input class="regular-text code datepicker" type="text" name="' . $template_fieldname . '" />';
					$internal_template .= '<input class="regular-text code datepicker" type="text" name="' . $template_fieldname . '" value="{{{' . $template_fieldname . 'data-placeholder}}}" />';
				}
				else {
					$output_html .= '<input class="regular-text code datepicker" type="text" name="' . $fieldname . '" value="' . $field['data'] . '" />';
				}
				break;
			case 'text':
			default:
				if($repeatgroup != NULL) {
					$template_html .= '<input class="regular-text code" type="text" name="' . $template_fieldname . '" />';
					$internal_template .= '<input class="regular-text code" type="text" name="' . $template_fieldname . '" value="{{{' . $template_fieldname . 'data-placeholder}}}" />';
				}
				else {
					$output_html .= '<input class="regular-text code" type="text" name="' . $fieldname . '" value="' . $field['data'] . '" />';
				}
		}
		
		if($repeatgroup != NULL) {
			$template_html .= '</td><td><span class="description">Desc goes here</span></td></tr>';
			$internal_template .= '</td><td><span class="description">Desc goes here</span></td></tr>';
		}
		else {
			$output_html .= '</td><td><span class="description">Desc goes here</span></td></tr>';
		}
		
		if($repeatgroup_last) {
			$template_html .= '<tr><td colspan="3"></td></tr>';
			$internal_template .= '<tr><td colspan="3"></td></tr>';

			$output_html .= '<tr id="addnew-' . $repeatgroup . '"><td colspan="3" style="text-align: right">';
			$output_html .= '<script type="text/javascript">'."\n";
			$output_html .= "//<![CDATA[\n";
			$output_html .= 'resman_registerGroupTemplate(\'' . $repeatgroup . '\', \'' . $template_html . '\');'."\n";
			$output_html .= "//]]>\n";
			$output_html .= '</script>';

			$output_html .= '<input type="hidden" name="' . $repeatgroup . '-list" id="' . $repeatgroup . '-list" value="{{{' . $repeatgroup . 'max-placeholder}}}" />';
			$output_html .= '<a href="#" onclick="resman_addGroup(\'' . $repeatgroup . '\');return false;">' . __('Add New '.$field['grouplabel'], 'resman') . '</a>';
			$output_html .= '</td></tr>';
			
			$sql = 'SELECT f.name AS name, d.value AS data, d.repeatgroup_count AS repeatgroup_count FROM ' . $wpdb->prefix . 'resman_data AS d LEFT JOIN ' . $wpdb->prefix . 'resman_fields AS f ON f.id=d.fieldid WHERE d.fieldid IN (' . implode(',', $fieldidlist) . ') ORDER BY repeatgroup_count;';
			$data = $wpdb->get_results($sql, ARRAY_A);
			
			if(!count($data)) {
				// Nothing for this group. Output a blank template
				$formatted_html = preg_replace('/name="(\w+)"/i', 'name="${1}1"', $internal_template);
				$output_html = str_replace('{{{' . $repeatgroup . 'placeholder}}}', $formatted_html, $output_html);
				$repeatgroup_count = 1;
			}
			else {
				$repeatgroup_count = 0;
				$max_count = 0;
				$formatted_html = $full_formatted_html = '';
				foreach($data as $item) {
					if($max_count < $item['repeatgroup_count']) {
						$repeatgroup_count++;
						$max_count = $item['repeatgroup_count'];
						$full_formatted_html .= $formatted_html;
						$formatted_html = preg_replace('/name="(\w+)"/i', 'name="${1}' . $repeatgroup_count . '"', $internal_template);
					}
					$formatted_html = str_replace('{{{' . $page . $item['name'] . 'data-placeholder}}}', $item['data'], $formatted_html);
				}
				$full_formatted_html .= $formatted_html;
				$output_html = str_replace('{{{' . $repeatgroup . 'placeholder}}}', $full_formatted_html, $output_html);
			}
			
			$output_html = preg_replace('/{{{(\w+)data-placeholder}}}/i', '', $output_html);
			$output_html = str_replace('{{{' . $repeatgroup . 'max-placeholder}}}', implode(',', range(1, $repeatgroup_count)), $output_html);
			
			$repeatgroup = NULL;
		}
		$nextfield_padding = 0;
		$repeatgroup_first = false;
		$repeatgroup_last = false;
		$repeatgroupset_last = false;
	}
	
	echo $output_html;
?>
				</table>
<?php
}

function resman_print_config_group($type, $id, $fieldname, $template_fieldname, $data) {
	global $wpdb;
	
	$template_html = '';
	$output_html = '';
	
	$sql = 'SELECT label, value FROM ' . $wpdb->prefix . 'resman_field_options WHERE fieldid=' . $id . ';';
	
	$options = $wpdb->get_results($sql, ARRAY_A);
	
	if($type == 'select') {
		if($template_fieldname != '') {
			$template_html .= '<select name="' . $template_fieldname . '"><option value=""></option>';
		}
		else {
			$output_html .= '<select name="' . $template_fieldname . '"><option value=""></option>';
		}
	}
	
	$count = 1;
	foreach($options as $option) {
		switch($type) {
			case 'option':
				if($template_fieldname != '') {
					$template_html .= '<input type="radio" name="' . $template_fieldname . '" value="' . $option['value'] . '" /> ' . __($option['label'], 'resman');
				}
				else {
					$output_html .= '<input type="radio" name="' . $fieldname . '" value="' . $option['value'] . '" ';
					if($option['value'] == $data) {
						$output_html .= 'checked="checked" ';
					}
					$output_html .= '/> ' . __($option['label'], 'resman') . ' ';
				}
				break;
			case 'select':
				if($template_fieldname != '') {
					$template_html .= '<option value="'  . $option['value'] . '">' . __($option['label'], 'resman') . '</option>';
				}
				else {
					$output_html .= '<option value="' . $option['value'] . '"' . ($option['value']==$data)?(' selected="selected" '):('') . '>' . __($option['label'], 'resman') . '</option>';
				}
				break;
			case 'checkbox':
				if($template_fieldname != '') {
					$template_html .= '<input type="checkbox" name="' . $template_fieldname . '[]" value="' . $option['value'] . '" /> ' . __($option['label'], 'resman');
				}
				else {
					$output_html .= '<input type="checkbox" name="' . $fieldname . '[]" value="' . $option['value'] . '" ';
					if(array_search($option['value'], explode(',', $data)) !== false) {
						$output_html .= 'checked="checked" ';
					}
					$output_html .= '/> ' . __($option['label'], 'resman') . ' ';
				}

				if( ($count % 3) == 0 ) {
					if($template_fieldname != '') {
						$template_html .= '<br/>';
					}
					else {
						$output_html .= '<br/>';
					}
				}
				break;
		}
		$count++;
	}

	if($type == 'select') {
		if($template_fieldname != '') {
			$template_html .= '</select>';
		}
		else {
			$output_html .= '</select>';
		}
	}

	if($template_fieldname != '') {
		return $template_html;
	}

	return $output_html;
}

function resman_conf_updatedb() {
	global $resman_formats;
	update_option('resman_page_name', $_POST['page-name']);
	update_option('resman_date_format', $_POST['date-format']);

	foreach($resman_formats as $label => $format) {
		if($_POST['output-'.$label]) {
			update_option('resman_output_'.$label, 1);
		}
		else {
			update_option('resman_output_'.$label, 0);
		}
	}

	if($_POST['service-europass']) {
		update_option('resman_service_europass', 1);
	}
	else {
		update_option('resman_service_europass', 0);
	}

	if($_POST['show-references']) {
		update_option('resman_show_references', 1);
	}
	else {
		update_option('resman_show_references', 0);
	}
	
	if($_POST['promo-link']) {
		update_option('resman_promo_link', 1);
	}
	else {
		update_option('resman_link', 0);
	}
	
	update_option('resman_livedocx_username', $_POST['livedocx-username']);
	update_option('resman_livedocx_password', $_POST['livedocx-password']);
}

function resman_resume_updatedb() {
	global $wpdb;
	
	$sql = 'SELECT f.id AS id, f.name AS name, f.section AS section, f.inputtype AS inputtype, g.name AS groupname FROM ' . $wpdb->prefix . 'resman_fields AS f';
	$sql .= ' LEFT JOIN ' . $wpdb->prefix . 'resman_repeated_groups AS g ON g.id = f.groupid';
	
	$fields = $wpdb->get_results($sql, ARRAY_A);
	
	foreach($fields as $field) {
		$fieldname = $field['section'] . $field['name'];
		if($field['groupname']) {
			$grouplist = $_POST[$field['groupname'].'-list'];
			
			$itemcount = 1;
			foreach(explode(',', $grouplist) as $itemid) {
				if(!isset($_POST[$fieldname.$itemid])) {
					$data = '';
				}
				else if($field['inputtype'] == 'checkbox') {
					$data = implode(',', $_POST[$fieldname.$itemid]);
				}
				else {
					$data = $_POST[$fieldname.$itemid];
				}
				
				if($data == '') {
					// Delete the old entry
					$sql = 'DELETE FROM ' . $wpdb->prefix . 'resman_data WHERE fieldid=' . $field['id'] . ' AND repeatgroup_count=' . $itemcount . ';';
				}
				else {
					$sql = 'INSERT INTO ' . $wpdb->prefix . 'resman_data (fieldid, repeatgroup_count, value) VALUES(%d,%d,%s) ON DUPLICATE KEY UPDATE value=%s;';
					$sql = $wpdb->prepare($sql, $field['id'], $itemcount, stripslashes($data), stripslashes($data));
				}
				$wpdb->query($sql);
				
				$itemcount++;
			}
		}
		else {
			if(!isset($_POST[$fieldname])) {
				$data = '';
			}
			else if($field['inputtype'] == 'checkbox') {
				$data = implode(',', $_POST[$fieldname]);
			}
			else {
				$data = $_POST[$fieldname];
			}
			
			if($data == '') {
				// Delete the old entry
				$sql = 'DELETE FROM ' . $wpdb->prefix . 'resman_data WHERE fieldid=' . $field['id'] . ';';
			}
			else {
				$sql = 'INSERT INTO ' . $wpdb->prefix . 'resman_data (fieldid, repeatgroup_count, value) VALUES(%d,-1,%s) ON DUPLICATE KEY UPDATE value=%s;';
				$sql = $wpdb->prepare($sql, $field['id'], stripslashes($data), stripslashes($data));
			}
			$wpdb->query($sql);
		}
	}
}

function resman_print_info() {
?>
<div id="resman_donate" class="postbox">
	<div class="handlediv" title="Click to toggle"><br /></div>
	<h3 class="hndle"><span><?php _e('Donate', 'resman') ?></span></h3>
	<div class="inside">
		<p><?php _e('If this plugin helps you find a new job, move your career in the direction you want, or simply exposes you to new experiences, I\'d appreciate it if you shared the love, by way of my Donate or Amazon Wish List links below.', 'resman') ?></p>
		<ul>
			<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=gary%40pento%2enet&item_name=WordPress%20Plugin%20(Resume%20Manager)&item_number=Support%20Open%20Source&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8"><?php _e('Donate with PayPal', 'resman') ?></a></li>
			<li><a href="http://www.amazon.com/wishlist/1ORKI9ZG875BL"><?php _e('My Amazon Wish List', 'resman') ?></a></li>
		</ul>
	</div>
</div>
<div id="resman_about" class="postbox">
	<div class="handlediv" title="Click to toggle"><br /></div>
	<h3 class="hndle"><span><?php _e('About This Plugin', 'resman') ?></span></h3>
	<div class="inside">
		<ul>
			<li><a href="http://pento.net/"><?php _e('Gary Pendergast\'s Blog', 'resman') ?></a></li>
			<li><a href="http://twitter.com/garypendergast"><?php _e('Follow me on Twitter!', 'resman') ?></a></li>
			<li><a href="http://pento.net/projects/wordpress-resume-manager/"><?php _e('Plugin Homepage', 'resman') ?></a></li>
			<li><a href="http://code.google.com/p/wordpress-resume-mananger/issues/list"><?php _e('Submit a Bug/Feature Request', 'resman') ?></a></li>
		</ul>
	</div>
</div>
<?php
}
?>