<?php
/*
Plugin Name: RSS Slurp
Plugin URI: http://wordpress.org/extend/plugins/rss-slurp/
Description: Import posts from an RSS feed, including enclosures
Author: witkamp
Author URI: http://wordpress.org/
Version: 0.1
Stable tag: 0.1
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( !defined('WP_LOAD_IMPORTERS') )
	return;

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

require_once('rss-parser.php');

function rss_slurp_greet() {
	echo '<div class="narrow">';
	echo '<p>'.__('I need to SLURP your RSS').'</p>';
	// TODO: Change this to use URLs or file from computer
	wp_import_upload_form("admin.php?import=rss-slurp&amp;step=1");
	echo '</div>';
}

function rss_slurp_import(){
	// Check the Nonce in wp_import_upload_form
	check_admin_referer('import-upload');
	
	// Get uploaded file info
	$file_info = wp_import_handle_upload();
	if ( isset($file_info['error']) ) {
		echo $file_info['error'];
		return;
	}

	$file = $file_info['file'];
	
	// Parse & Import RSS
	$parser = new RSSParser();
	$parser->read(fopen($file,'r'));

	// Delete Attachment
	wp_delete_attachment($file_info['id']);
	
	do_action('import_done', 'rss-slurp');
	echo '<h3>';
	printf(__('All done. <a href="%s">Have fun!</a>'), get_option('home'));
	echo '</h3>';
}

function rss_slurp_dispatch(){
	if (empty ($_GET['step'])){
		$step = 0;
	}else{
		$step = (int) $_GET['step'];
	}

	echo '<div class="wrap">';
	// Put the tool icon on the page
	screen_icon();
	// Echo title
	echo '<h2>'.__('RSS Slurp').'</h2>';

	switch ($step) {
		case 0 :
			rss_slurp_greet();
			break;
		case 1 :
			// Show the results of the import
			rss_slurp_import();
			break;
	}
	echo '</div>';
}	

register_importer('rss-slurp', __('RSS Slurp'), __('Import posts from an RSS feed.'), rss_slurp_dispatch);
?>
