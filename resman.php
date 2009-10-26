<?php //encoding: utf-8
/*
Plugin Name: Résumé Manager
Plugin URI: http://code.google.com/p/wordpress-resume-mananger/
Description: A résumé management plugin for Wordpress. This plugin requires both the <a href="http://www.php.net/soap">PHP SOAP Extension</a> and the <a href="http://php.net/manual/en/book.openssl.php">PHP OpenSSL Extension</a> to be enabled for full functionalitys.
Version: 0.1
Author: Gary Pendergast
Author URI: http://pento.net/
Tags: resume, manager, cv, pdf, microsoft word, rtf, europass, hresume
*/

// Version
define('RESMAN_VERSION', '0.1');
define('RESMAN_DB_VERSION', 1);

// Define the URL to the plugin folder
define('RESMAN_FOLDER', dirname(plugin_basename(__FILE__)));
define('RESMAN_URL', get_option('siteurl').'/wp-content/plugins/' . RESMAN_FOLDER);

// LiveDocx settings
define('LIVEDOCX_URL', 'https://api.livedocx.com/1.2/mailmerge.asmx?WSDL');
ini_set('soap.wsdl_cache_enabled', 0);

$resman_formats = array(
					'txt' => array('txt', 'Plain text', 'text/plain'),
					'pdf' => array('pdf', 'PDF', 'application/pdf'),
					'odt' => array('odt', 'ODT - OpenOffice.org', 'application/vnd.oasis.opendocument.text'),
					'doc' => array('doc', 'Microsoft Word 95-2000', 'application/msword'),
					'docx' => array('docx', 'Microsoft Word 2003-2007', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
					'rtf' => array('rtf', 'Rich Text Format', 'application/rtf'),
					'europass' => array('xml', 'Europass CV', 'text/xml')
				);

//
// Load ResMan
//

// Resman setup (for installation/upgrades)
require_once(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/resman-setup.php');

// Resman database
require_once(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/resman-db.php');

// Resman LiveDocx functions
require_once(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/resman-livedocx.php');

// Resman admin
require_once(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/resman-conf.php');

// Resman frontend
require_once(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/resman-display.php');

// Add hooks at the end
require_once(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/resman-hooks.php');

?>