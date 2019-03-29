<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.smashstack.com/
 * @since      1.0.0
 *
 * @package    Smashstack_Csv_Importer
 * @subpackage Smashstack_Csv_Importer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smashstack_Csv_Importer
 * @subpackage Smashstack_Csv_Importer/admin
 * @author     smashstack-aeros <aeros.andrews@smashstack.com>
 */
class Smashstack_Csv_Importer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


		global $wpdb;
		$this->wpdb = $wpdb;

		$this->post_type = isset($_GET['post_type']) ? $_GET['post_type']:'post';

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smashstack_Csv_Importer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smashstack_Csv_Importer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smashstack-csv-importer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smashstack_Csv_Importer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smashstack_Csv_Importer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smashstack-csv-importer-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'script_obj', 
			array( 
				'ajax_url' => admin_url( 'admin-ajax.php'),
				'plugin_dir_admin' => plugin_dir_url( __FILE__ ),
				'site_url' => site_url()
			) 
		);

	}

	/**
	 * Add import as submenu to post types
	 *
	 * @since    1.0.0
	 */
	public function add_menu() {

		foreach ( get_post_types( '', 'names' ) as $post_type ) {
			if( $post_type == 'post' ) {
				add_submenu_page(
					'edit.php',
					'Import CSV',
					'Import CSV',
					'manage_options',
					'post_import_csv',
					array($this, 'import_csv_template')
				);
			}
			else {
				add_submenu_page(
					'edit.php?post_type='. $post_type,
					'Import CSV',
					'Import CSV',
					'manage_options',
					$post_type .'_import_csv',
					array($this, 'import_csv_template')
				);
			}
		}
	}

	/**
	 *
	 * Display a page for import csv page for custom post type
	 *
	 */
	public function import_csv_template() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/smashstack-csv-importer-admin-display.php';
	}

	/**
	 *
	 * Insert data from csv to wordpress post
	 *
	 */
	public function insert_csv_to_post() {
		$file = $_FILES['file']['tmp_name'];
		$post_type = $_POST['post_type'];
		$p_fields = json_decode( stripslashes($_POST['p_fields']), true ); // standard post fields
		$c_fields = json_decode( stripslashes($_POST['c_fields']), true ); // custom post fields
		$wpdb = $this->wpdb;

		$data = array();
		$response = array();		
		$errors = array();

		if ( is_readable( $file ) && $_file = fopen( $file, "r" ) ) {
			$post = array();

			// Get first row in CSV, which is of course the headers
	    	$header = fgetcsv( $_file );
	        while ( $row = fgetcsv( $_file ) ) {
	            foreach ( $header as $i => $key ) {
	            	if( array_key_exists($key, $p_fields) ) {
		            	$k = str_replace(' ', '_', $key);
	                    $post[$p_fields[$key]] = $row[$i];
	                }
                }
                $data[] = $post;
	        }
			fclose( $_file );
		} else {
			$errors[] = "File '$file' could not be opened. Check the file's permissions to make sure it's readable by your server.";
		}


		$urls = array();
		$count = 0;
		$skip = 0;
		$c_count = 0;
		foreach ( $data as $post ) {	
			
			if( isset($post["post_author"]) ) {
				//insert author of not exist, get ID
				$author_id = username_exists( $post["post_author"] );
				if ( !$author_id ) {
					$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
					$author_id = wp_create_user( $author, $random_password );
				}

				$post["post_author"] = $author_id;
			}
			if( isset($post["post_status"]) ) {
				//conver boolean to publish or draft
				$status = $post["post_status"]== 1 ? 'publish':'draft';
				$post["post_status"] = $status;
			}
			if( isset($post["post_date"]) ) {
				//convert string date to valid date format
				$post["post_date"] = date( 'Y-m-d H:i:s', strtotime($date_published) );
			}
			if( isset($post["post_modified"]) ) {
				//convert string date to valid date format
				$post["post_modified"] = date( 'Y-m-d H:i:s', strtotime($date_modified) );
			}
			if( isset($post["post_category"]) ) {
				//insert categories, convert to IDs
				$categories = explode(',', $post["post_category"]);
				$category_ids = array();
				foreach( $categories as $category ) {
					$category_name = trim( $category );
					$cat = get_term_by('name', $category_name, 'category');

					if( $cat == false ) {
						$cat_term = wp_insert_term( $category_name, 'category' );
						$category_ids[] = $cat_term['term_id'];
					}
					else {
						$category_ids[] = $cat->term_id;
					}
				}

				$post["post_category"] = $category_ids;
			}
			if( isset($post["tags_input"]) ) {
				//convert tags to array and remove whitespaces
				$post["tags_input"] = array_map( 'trim', explode(',', $tags) );
			}
			if( isset($post["post_title"]) ) {
				$post_exists = $this->post_exists( $post["post_title"], 'post_title' );
			}
			if( isset( $post["post_name"] ) ) {
				// get post slug from URL
				$tokens = explode('/', $post["post_name"]);
				$slug = $tokens[sizeof($tokens)-1];

				$post["post_name"] = trim($slug);

				$post_exists = $this->post_exists( $post["post_name"], 'post_name' );
			}


			// If the post exists, skip this post and go to the next one
			if ( $post_exists ) {
				$current_post = get_posts( array('name' => $slug, 'post_type' => $post_type) )[0];				

				$post_arg["ID"] = $current_post->ID;
				wp_update_post( $post ); // update existing post

				continue;
			}	
			

			// Insert the post into the database
			$post_arg["post_type"] = $post_type;
			$post['id'] = wp_insert_post( $post );

			// catch error if insert is invalid
			if( is_wp_error( $post['id'] ) ) {
				$errors[] = $post['id']->get_error_message();
			}
			else {
				$count++;
			}
		
		}//end foreach post data

		//handle response
		if( !empty( $errors ) ) {
			$response['status'] = 'Error';
			$response['errors'] = $errors;
		}
		else {
			$response['status'] = 'Success';

			if( $count > 0 ) {
				$response['message'] =  '<b>'. $count .'</b> successfully added posts.<br>';
			}
			if( $c_count > 0 ) {
				$response['message'] .=  '<b>'. $c_count .'</b> updated post content.<br>';
			}
			if( $skip > 0 ) {
				$response['message'] .=  '<b>'. $skip .'</b> skipped duplicate posts.';
			}
		}


		echo json_encode( $response );

		die();
	}


	/**
	 *
	 * Get Post Type custom fields
	 *
	 */
	function get_custom_fields_by_post_type() {
	    $wpdb = $this->wpdb;
	    $post_type = $this->post_type;

	    $query = "
	        SELECT * 
	        FROM  $wpdb->postmeta AS pm1, $wpdb->postmeta AS pm2, $wpdb->posts
	        WHERE pm1.meta_key = '_wp_types_group_post_types'
	        AND pm1.meta_value LIKE '%$post_type%'
	        AND pm2.post_id = pm1.post_id
	        AND pm2.meta_key = '_wp_types_group_fields'
	        AND $wpdb->posts.ID = pm2.post_id
	        ORDER BY $wpdb->posts.post_title ASC
	    ";
	 
	    $results = $wpdb->get_results ( $query );
	    $cf_meta = $this->get_custom_field_meta();
	    $i=0;
	    $my_cfs[post_type] = $post_type;
	    $cf_data = array();
	    foreach($results as $result) {
	        $my_cfs[data][$i][group_name] = $result->post_title;
	        $my_cfs[data][$i][group_slug] = $result->post_name;
	        $the_fields = explode(',',$result->meta_value); // custom fields stored as csv string, but with commas at front and back
	        $the_fields = array_filter($the_fields); // deletes empty array elements
	        $x=0;
	        foreach($the_fields as $the_field) {
	            $cf_data[] = $cf_meta[$the_field];
	            $x++;
	        }
	        $i++;
	    }
	    return $cf_data;
	}

	/**
	 *
	 * Get Post Type meta data
	 *
	 */
	public function get_custom_field_meta() {
	    $wpdb = $this->wpdb;

	    $cf_meta = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name = 'wpcf-fields'");
	    $cf_meta = unserialize($cf_meta->option_value);
	    return $cf_meta;
	}

	/**
	 *
	 * Return boolean if post exists in posts table
	 *
	 */
	public function post_exists($str, $column) {
		$wpdb = $this->wpdb;
		$post_type = $this->post_type;

		// Get an array of all posts within our custom post type
		$posts = $wpdb->get_col( "SELECT {$column} FROM {$wpdb->posts} WHERE post_type = '{$post_type}' LIMIT 7000" );

		// Check if the passed title exists in array
		return in_array( strtolower($str), $posts );
	}

}
