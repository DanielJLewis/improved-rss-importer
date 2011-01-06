<?php
require_once('rss-parser.php');
/**
 * RSS Importer
 *
 * @package WordPress
 * @subpackage Importer
 */

/**
 * RSS Importer
 *
 * Will process a RSS feed for importing posts into WordPress. This is a very
 * limited importer and should only be used as the last resort, when no other
 * importer is available.
 *
 * @since unknown
 */
class RSS_Import {
  var $parser = null;
	var $posts  = array ();
	var $file;
  function __contruct(){
  }
	function header() {
		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>'.__('Import RSS').'</h2>';
	}

	function footer() {
		echo '</div>';
	}

	function greet() {
		echo '<div class="narrow">';
		echo '<p>'.__('Howdy! This importer allows you to extract posts from an RSS 2.0 file into your blog. This is useful if you want to import your posts from a system that is not handled by a custom import tool. Pick an RSS file to upload and click Import.').'</p>';
		wp_import_upload_form("admin.php?import=rss&amp;step=1");
		echo '</div>';
	}


	function import() {
		$file = wp_import_handle_upload();
		if ( isset($file['error']) ) {
			echo $file['error'];
			return;
		}

		$this->file = $file['file'];
		//$this->parse_feed();
		$this->parser = new RSSParser();
    $this->parser->read(fopen($this->file,'r'));
		
		//$this->get_posts();
		//$result = $this->import_posts();
		//if ( is_wp_error( $result ) )
		//	return $result;
		wp_import_cleanup($file['id']);
		do_action('import_done', 'rss');

		echo '<h3>';
		printf(__('All done. <a href="%s">Have fun!</a>'), get_option('home'));
		echo '</h3>';
	}

	// This is the entry point
	function dispatch() {
		if (empty ($_GET['step']))
			$step = 0;
		else
			$step = (int) $_GET['step'];

		$this->header();

		switch ($step) {
			case 0 :
				$this->greet();
				break;
			case 1 :
				check_admin_referer('import-upload');
				// Show the results of the import
				$result = $this->import();
				if ( is_wp_error( $result ) )
					echo $result->get_error_message();
				break;
		}

		$this->footer();
	}

	function RSS_Import() {
		// Nothing.
	}
}

// Create the importer object and register it
$rss_import = new RSS_Import();

register_importer('rss', __('RSS'), __('Import posts from an RSS feed.'), array ($rss_import, 'dispatch'));
?>
