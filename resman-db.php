<?php //encoding: utf-8
	
function resman_create_db() {
	global $wpdb;
	
	$tablename = $wpdb->prefix . 'resman_repeated_groups';
	$sql = 'CREATE TABLE ' . $tablename . ' (
			  id INT NOT NULL,
			  name VARCHAR(100),
			  label VARCHAR(100),
			  PRIMARY KEY  (id));';
	$wpdb->query($sql);
	
	$sql = 'INSERT INTO ' . $tablename . "
			  (id, name, label) VALUES 
			  (0, 'nationality', 'Nationality'),
			  (1, 'experience', 'Experience'),
			  (2, 'education', 'Education'),
			  (3, 'mothertongue', 'Mother Tongue'),
			  (4, 'language', 'Language'),
			  (5, 'reference', 'Reference');";
	$wpdb->query($sql);
	
	$tablename = $wpdb->prefix . 'resman_fields';
	$sql = 'CREATE TABLE ' . $tablename . ' (
			  id INT NOT NULL,
			  name VARCHAR(100),
			  label VARCHAR(100),
			  inputtype VARCHAR(100),
			  display_order INT,
			  section VARCHAR(100),
			  groupid INT,
			  PRIMARY KEY  (id),
			  KEY display (section, display_order));';
	$wpdb->query($sql);
	
	$sql = 'INSERT INTO ' . $tablename . "
			  (id, name, label, inputtype, display_order, section, groupid) VALUES
			  (0, 'dob', 'Date of Birth', 'date', 1, 'personal', NULL),
			  (1, 'name', 'Name', 'text', 2, 'personal', NULL),
			  (2, 'address', 'Address', 'textarea', 4, 'personal', NULL),
			  (3, 'phone', 'Telephone', 'text', 9, 'personal', NULL),
			  (4, 'mobile', 'Mobile', 'text', 10, 'personal', NULL),
			  (5, 'fax', 'Fax', 'text', 11, 'personal', NULL),
			  (6, 'email', 'E-mail', 'text', 12, 'personal', NULL),
			  (7, 'nationality', 'Nationality', 'text', 13, 'personal', 0),
			  (8, 'gender', 'Gender', 'option', 14, 'personal', NULL),
			  
			  (9, 'occupation', 'Desired Occupation', 'text', 1, 'general', NULL),
			  (10, 'abstract', 'Abstract', 'textarea', 2, 'general', NULL),
			  
			  (11, 'start', 'Start Date', 'date', 1, 'experience', 1),
			  (12, 'end', 'End Date', 'date', 2, 'experience', 1),
			  (13, 'title', 'Position Title', 'text', 3, 'experience', 1),
			  (14, 'abstract', 'Activities and Responsibilities', 'textarea', 4, 'experience', 1),
			  (15, 'employer', 'Employer', 'title', 5, 'experience', 1),
			  (16, 'name', 'Name', 'text', 6, 'experience', 1),
			  (17, 'address', 'Address', 'textarea', 7, 'experience', 1),
			  (18, 'sector', 'Business Sector', 'text', 12, 'experience', 1),

			  (19, 'start', 'Start Date', 'date', 1, 'education', 2),
			  (20, 'end', 'End Date', 'date', 2, 'education', 2),
			  (21, 'title', 'Qualification Title', 'text', 3, 'education', 2),
			  (22, 'abstract', 'Subjects/Skills Covered', 'textarea', 4, 'education', 2),
			  (23, 'educator', 'Organisation providing education/training', 'title', 5, 'education', 2),
			  (24, 'name', 'Name', 'text', 6, 'education', 2),
			  (25, 'type', 'Type', 'text', 7, 'education', 2),
			  (26, 'address', 'Address', 'textarea', 8, 'education', 2),
			  (27, 'isced', 'ISCED Level', 'text', 13, 'education', 2),
			  (28, 'field', 'Education field', 'text', 14, 'education', 2),
			  
			  (29, 'mothertongue', 'Mother Tongue', 'text', 1, 'language', 3),
			  (30, 'language', 'Language', 'text', 2, 'language', 4),
			  (31, 'listening', 'Understanding - Listening', 'select', 3, 'language', 4),
			  (32, 'reading', 'Understanding - Reading', 'select', 4, 'language', 4),
			  (33, 'interaction', 'Spoken Interaction', 'select', 5, 'language', 4),
			  (34, 'production', 'Spoken Production', 'select', 6, 'language', 4),
			  (35, 'writing', 'Writing', 'select', 7, 'language', 4),
			  
			  (36, 'social', 'Social Skills', 'textarea', 1, 'skills', NULL),
			  (37, 'organisational', 'Organisational Skills', 'textarea', 2, 'skills', NULL),
			  (38, 'technical', 'Technical Skills', 'textarea', 3, 'skills', NULL),
			  (39, 'computer', 'Computer Skills', 'textarea', 4, 'skills', NULL),
			  (40, 'artisitic', 'Artisitic Skills', 'textarea', 5, 'skills', NULL),
			  (41, 'other', 'Other Skills', 'textarea', 6, 'skills', NULL),
			  (42, 'licence', 'Driving Licence(s)', 'checkbox', 7, 'skills', NULL),
			  
			  (43, 'name', 'Name', 'text', 1, 'references', 5),
			  (44, 'title', 'Title', 'text', 2, 'references', 5),
			  (45, 'employer', 'Employer', 'text', 3, 'references', 5),
			  (46, 'email', 'E-mail', 'text', 4, 'references', 5),
			  (47, 'phone', 'Telephone', 'text', 5, 'references', 5);";
	$wpdb->query($sql);
	
	$tablename = $wpdb->prefix . 'resman_field_options';
	$sql = 'CREATE TABLE ' . $tablename . ' (
			  fieldid INT,
			  label VARCHAR(100),
			  value VARCHAR(100),
			  KEY fieldid (fieldid));';
	$wpdb->query($sql);
	
	$sql = 'INSERT INTO ' . $tablename . "
			  (fieldid, label, value) VALUES
			  (8, 'Male', 'm'),
			  (8, 'Female', 'f'),
			  (31, 'Basic User (A1)', 'a1'),
			  (31, 'Basic User (A2)', 'a2'),
			  (31, 'Independent User (B1)', 'b1'),
			  (31, 'Independent User (B2)', 'b2'),
			  (31, 'Proficient User (C1)', 'c1'),
			  (31, 'Proficient User (C2)', 'c2'),
			  (32, 'Basic User (A1)', 'a1'),
			  (32, 'Basic User (A2)', 'a2'),
			  (32, 'Independent User (B1)', 'b1'),
			  (32, 'Independent User (B2)', 'b2'),
			  (32, 'Proficient User (C1)', 'c1'),
			  (32, 'Proficient User (C2)', 'c2'),
			  (33, 'Basic User (A1)', 'a1'),
			  (33, 'Basic User (A2)', 'a2'),
			  (33, 'Independent User (B1)', 'b1'),
			  (33, 'Independent User (B2)', 'b2'),
			  (33, 'Proficient User (C1)', 'c1'),
			  (33, 'Proficient User (C2)', 'c2'),
			  (34, 'Basic User (A1)', 'a1'),
			  (34, 'Basic User (A2)', 'a2'),
			  (34, 'Independent User (B1)', 'b1'),
			  (34, 'Independent User (B2)', 'b2'),
			  (34, 'Proficient User (C1)', 'c1'),
			  (34, 'Proficient User (C2)', 'c2'),
			  (35, 'Basic User (A1)', 'a1'),
			  (35, 'Basic User (A2)', 'a2'),
			  (35, 'Independent User (B1)', 'b1'),
			  (35, 'Independent User (B2)', 'b2'),
			  (35, 'Proficient User (C1)', 'c1'),
			  (35, 'Proficient User (C2)', 'c2'),
			  (42, 'Category A', 'a'),
			  (42, 'Category A1', 'a1'),
			  (42, 'Category B', 'b'),
			  (42, 'Category B1', 'b1'),
			  (42, 'Category BE', 'be'),
			  (42, 'Category C', 'c'),
			  (42, 'Category C1', 'c1'),
			  (42, 'Category CE', 'ce'),
			  (42, 'Category C1E', 'c1e'),
			  (42, 'Category D', 'd'),
			  (42, 'Category D1', 'd1'),
			  (42, 'Category DE', 'de'),
			  (42, 'Category D1E', 'd1e');";
	$wpdb->query($sql);
	
	$tablename = $wpdb->prefix . 'resman_data';
	$sql = 'CREATE TABLE ' . $tablename . ' (
			  fieldid INT,
			  repeatgroup_count INT,
			  value TEXT,
			  UNIQUE KEY field (fieldid, repeatgroup_count));';
	$wpdb->query($sql);
}

function resman_upgrade_db($oldversion) {
}

?>