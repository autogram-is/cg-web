<?php

 if ( !defined('WP_LOAD_IMPORTERS') ) {
	return;
}

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( !class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		require_once $class_wp_importer;
}

/**
 * CG CSV Importer
 *
 * Imports a CSV file with a fixed structure, and creates or updates posts of
 * the following types: `cg_sector`, `cg_service`, `cg_region`, and `cg_office`.
 */
if ( class_exists( 'WP_Importer' ) ) {
	class CG_CVS_Import extends WP_Importer {
		var $posts = array();
		var $file;

    /**
     * Display import page title
     */
    public function header() {
        echo '<div class="wrap">';
        echo '<h2>' . __('Import CSV', 'cg-csv') . '</h2>';
    }

    /**
     * Display import page footer
     */
    public function footer() {
        echo '</div>';
    }

    /**
     * Display introductory text and file upload form
     */
    public function greet() {
        echo '<div class="narrow">';
        echo '<p>'.__('Upload a CSV file to populate Sectors, Services, Regions, and Offices.', 'cg-csv').'</p>';
        wp_import_upload_form('admin.php?import=cg-csv&amp;step=1');
        echo '</div>';
    }
	
		function get_posts() {
			global $wpdb;
	
			$datalines = file($this->file); // Read the file into an array
			$header = str_getcsv($datalines[0]);

			$index = 0;
			foreach ($datalines as $line) {
				// Parse each line using str_getcsv
				$data = str_getcsv($line);
					
				// Skip empty lines and header row
				if (empty($data) || $index == 0) {
						$index++;
						continue;
				}
				
				$row = array_combine($header, $data);		

				// Prepare post data
				$post_data = array(
					'ID'            => $row['id'],
					'post_type'     => $row['type'],
					'post_title'    => sanitize_text_field($row['title']),
					'post_name'     => sanitize_text_field($row['slug']),
					'post_parent'   => $row['parent'], // If this is a title, we search for parent ID
					'region'        => $row['region'], // This becomes a post_meta or the post_parent
					'post_status'   => 'publish',
					'post_author'   => get_current_user_id(),
					'post_date'     => current_time('mysql'),
					'meta_input'    => $this->build_meta($row),
				);

				$this->posts[$index] = $post_data;

				// Sort the posts so posts with no region appear first

				$index++;
			}
		}

		function get_post_id_by_typed_slug($type, $slug) {
			foreach($this->post as $post) {
				if ($post['post_type'] === $type && $post['post_name'] === $slug) {
					return $post['post_id'] ?? $post['ID'];
				}
			}
		}

		function build_meta($row = []) {
			$meta = array(
				'_cg_imported' => current_time('mysql'),
			);
			if ($row['locale']) {
				$meta['locale'] = $row['locale'];
			}
			return $meta;
		}
	
		function import_posts() {
			echo '<ol>';
				
			foreach ($this->posts as $post) {
				echo('<li>Importing ' .$post['post_type']. ' "'. $post['post_title'] . '"… ');
	
				extract($post);

				if ($region) {
					$post['meta_input']['region'] = post_exists($region, '', '', 'cg_region');
				}

				if ($parent) {
					$post['post_parent'] = post_exists($parent, '', '', $post_type);
				}

				if ($post_id = post_exists($post_title, '', '', $post_type)) {
					// Actually, we should overwrite this
					_e('Post already imported', 'cg-cvs');
				} else {
					$post_id = wp_insert_post($post);
					if ( is_wp_error( $post_id ) )
						return $post_id;
					if (!$post_id) {
						_e('Couldn&#8217;t get post ID', 'cg-cvs');
						return;
					}
	
					_e('Done!', 'cg-cvs');
				}
				echo '</li>';
			}
	
			echo '</ol>';
		}
	
		function import() {
			$file = wp_import_handle_upload();
			if ( isset($file['error']) ) {
				echo $file['error'];
				return;
			}
	
			$this->file = $file['file'];
			$this->get_posts();
			$result = $this->import_posts();
			if ( is_wp_error( $result ) )
				return $result;
			wp_import_cleanup($file['id']);
			do_action('import_done', 'cg-csv');
	
			echo '<h3>';
			printf(__('<a href="%s">Import complete.</a>', 'cg-csv'), get_option('home'));
			echo '</h3>';
		}
	
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
					$result = $this->import();
					if ( is_wp_error( $result ) )
						echo $result->get_error_message();
					break;
			}
	
			$this->footer();
		}
	
	}
}	

 
function cgih_importer_init() {
	$cvs_import = new CG_CVS_Import();

	register_importer(
		'cg-csv',
		'Cumming Group Portfolio Data',
		'Imports stub records for Sectors, Services, Regions, and Offices.',
		array ($cvs_import, 'dispatch')
	);
}
add_action( 'admin_init', 'cgih_importer_init' );
 