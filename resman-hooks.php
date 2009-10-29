<?php //encoding: utf-8

// Hooks for initial setup
register_activation_hook(WP_PLUGIN_DIR.'/'.RESMAN_FOLDER.'/resman.php', 'resman_activate');

// Admin menu
add_action('admin_menu', 'resman_admin_setup');

//
// Display Hooks
//
// URL magic
add_filter('query_vars', 'resman_queryvars');
add_action('generate_rewrite_rules', 'resman_add_rewrite_rules');
add_action('init', 'resman_flush_rewrite_rules');
add_filter('the_posts', 'resman_display_resume', 1);
// Add our init stuff
add_action('init', 'resman_display_init');
// Set the template we want to use
add_action('template_redirect', 'resman_display_template');
// Set the <title> value
add_filter('wp_title', 'resman_display_title', 10, 3);
// Set the edit post link
add_filter('get_edit_post_link', 'resman_display_edit_post_link');
?>