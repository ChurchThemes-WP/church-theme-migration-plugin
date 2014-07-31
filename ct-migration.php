<?php
/*
Plugin Name: ChurchThemes.net Content Migration Plugin
Plugin URI: http://churchthemes.net/plugins/content-migration/
Description: This plugin allows users to migrate from the old ChurchThemes.net themes to be able to use the Church Theme Content plugin.
Author: Chris Wallace
Version: 1.0
Author URI: http://churchthemes.net
*/


// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main class
 *
 * @since 0.1
 */
class Church_Theme_Content_Migration {

	/**
	 * Number of posts to migrate at a time
	 *
	 * @since 0.1
	 * @var array
	 */
	public $posts_per_page = -1;

	/**
	 * Constructor
	 *
	 * Add actions for methods that define constants and load includes.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function __construct() {

		// Load menu page
		add_action( 'admin_menu', array( &$this, 'hook_admin_page' ) );

		if( isset( $_GET[ 'migrate_sermons' ] ) && $_GET[ 'migrate_sermons' ] == true ){

			add_action( 'admin_init', array( &$this, 'migrate_sermons' ) );

		}

		if( isset( $_GET[ 'migrate_sermon_tax' ] ) && $_GET[ 'migrate_sermon_tax' ] == true ){

			add_action( 'admin_init', array( &$this, 'migrate_sermon_taxonomy_terms' ) );

		}

		if( isset( $_GET[ 'migrate_people' ] ) && $_GET[ 'migrate_people' ] == true ){

			add_action( 'admin_init', array( &$this, 'migrate_people' ) );

		}

		if( isset( $_GET[ 'migrate_people_tax' ] ) && $_GET[ 'migrate_people_tax' ] == true ){

			add_action( 'admin_init', array( &$this, 'migrate_people_taxonomy_terms' ) );

		}

		if( isset( $_GET[ 'migrate_locations' ] ) && $_GET[ 'migrate_locations' ] == true ){

			add_action( 'admin_init', array( &$this, 'migrate_locations' ) );

		}
	}

	/**
	 * Create plugin page
	 *
	 * @since 0.1
	 * @access public
	 */
	public function hook_admin_page() {

		add_management_page( 'ChurchThemes Migration', 'ChurchThemes Migration', 'manage_options', 'ct-migration', array( &$this,'display_admin_page') );

	}

	/**
	 * Display plugin page
	 *
	 * @since 0.1
	 * @access public
	 */
	public function display_admin_page() {

		echo '<div class="wrap">';

		echo '<h2>' . __( 'ChurchThemes.net Migration' ) . '</h2>';

		echo '<div id="ct-warning" class="error"><p>' . __( '<strong>WARNING</strong> Always perform a backup of your database before you make any major changes like this. We recommend using <a href="https://wordpress.org/plugins/backupwordpress/">BackUpWordPress</a>.' ) . '</p></div>';

		/**
		 * Migrate Sermons
		 */

		$posts = get_posts( array(
			'posts_per_page'=> $this->posts_per_page,
			'post_type' 	=> 'ct_sermon',
			'post_status'	=> 'all'
		) );

		$taxonomy_terms = get_terms( array( 'sermon_speaker', 'sermon_topic', 'sermon_series' ) );

		echo '<hr><h3>' . __( 'Migrate Sermons' ) . '</h3>';

		if( count( $posts ) > 0 ){
			echo '<div class="updated" style="margin: 0 0 22px;"><p>' . __( '<strong>PLEASE NOTE:</strong> The new sermon manager does not use "podcast" or "services" so these taxonomies will not be migrated.' ) . '</p></div>';
			echo '<p>' . __( 'Migrate your sermon content to the new format.' ) . '</p>';
		}

		if( count( $posts ) === 0 ){
			$sermons_completed = true;
			echo '<div class="completed"><i class="dashicons dashicons-yes" style="color: green;"></i> ' . __( 'Sermon Migration Complete') . '</div>';
		} else {
			echo '<p><a class="button button-primary" href="' . esc_url( add_query_arg( array( 'page' => 'ct-migration', 'migrate_sermons' => '1', 'migrate_sermon_tax' => '1' ), $_SERVER[ 'PHP_SELF' ] ) ) . '">' . __( 'Migrate All Sermons &amp; Taxonomies' ) . '</a></p>';
		}

		if( count( $taxonomy_terms ) === 0 ){
			$sermon_tax_completed = true;
			echo '<div class="completed"><i class="dashicons dashicons-yes" style="color: green;"></i> ' . __( 'Sermon Taxonomy Migration Complete') . '</div>';
		} else {
			echo '<p><a class="button button-secondary" href="' . esc_url( add_query_arg( array( 'page' => 'ct-migration', 'migrate_sermon_tax' => '1' ), $_SERVER[ 'PHP_SELF' ] ) ) . '">' . __( 'Migrate Sermon Taxonomies' ) . '</a></p>';
		}

		/**
		 * Migrate People
		 */

		$posts = get_posts( array(
			'posts_per_page'=> $this->posts_per_page,
			'post_type' 	=> 'ct_person',
			'post_status'	=> 'all'
		) );

		$ppl_cat_taxonomy_terms = get_terms( array( 'person_category' ) );

		echo '<hr><h3>' . __( 'Migrate People' ) . '</h3>';

		if( count( $posts ) > 0 ){
			echo '<div class="updated" style="margin: 0 0 22px;"><p>' . __( '<strong>PLEASE NOTE:</strong> we do not use "person tags" with the new framework. This taxonomy will not be migrated.' ) . '</p></div>';
			echo '<p>' . __( 'Migrate your staff profiles to the new format.' ) . '</p>';
		}

		if( ! count( $posts ) ){
			$people_completed = true;
			echo '<div class="completed"><i class="dashicons dashicons-yes" style="color: green;"></i> ' . __( 'People Migration Complete') . '</div>';
		} else {
			echo '<p><a class="button button-primary" href="' . esc_url( add_query_arg( array( 'page' => 'ct-migration', 'migrate_people' => '1', 'migrate_people_tax' => '1' ), $_SERVER[ 'PHP_SELF' ] ) ) . '">' . __( 'Migrate All People &amp; Taxonomies' ) . '</a></p>';
		}

		if( is_array( $ppl_cat_taxonomy_terms ) && count( $ppl_cat_taxonomy_terms ) === 0 ){
			$people_tax_completed = true;
			echo '<div class="completed"><i class="dashicons dashicons-yes" style="color: green;"></i> ' . __( 'People Taxonomy Migration Complete') . '</div>';
		} else {
			echo '<p><a class="button button-secondary" href="' . esc_url( add_query_arg( array( 'page' => 'ct-migration', 'migrate_people_tax' => '1' ), $_SERVER[ 'PHP_SELF' ] ) ) . '">' . __( 'Migrate People Taxonomies' ) . '</a></p>';
		}

		/**
		 * Migrate Locations
		 */

		$posts = get_posts( array(
			'posts_per_page'=> $this->posts_per_page,
			'post_type' 	=> 'ct_location',
			'post_status'	=> 'all'
		) );

		echo '<hr><h3>' . __( 'Migrate Locations' ) . '</h3>';

		if( count( $posts ) ){
			echo '<div class="updated" style="margin: 0 0 22px;"><p>' . __( '<strong>PLEASE NOTE:</strong> we do not use "person tags" with the new framework. This taxonomy will not be migrated.' ) . '</p></div>';
			echo '<p>' . __( 'This migration tool simply converts each location to the new post type, you will need to add your own maps/directions and additional data.' ) . '</p>';
		}

		if( ! count( $posts ) ){
			$locations_completed = true;
			echo '<div class="completed"><i class="dashicons dashicons-yes" style="color: green;"></i> ' . __( 'Location Migration Complete') . '</div>';
		} else {
			echo '<p><a class="button button-primary" href="' . esc_url( add_query_arg( array( 'page' => 'ct-migration', 'migrate_locations' => '1', 'migrate_locations_tax' => '1' ), $_SERVER[ 'PHP_SELF' ] ) ) . '">' . __( 'Migrate All Locations' ) . '</a></p>';
		}

		if( isset( $sermons_completed ) && $sermons_completed == true && isset( $sermon_tax_completed ) && $sermon_tax_completed == true && isset( $people_completed ) && $people_completed == true && isset( $people_tax_completed ) && $people_tax_completed == true && isset( $locations_completed ) && $locations_completed == true ){

			echo '<style>#ct-warning{display: none;}</style>';
			echo '<div style="margin-top: 20px; border-left: 3px solid green; font-size: 130%; padding: 40px; border-radius: 4px; background-color: white; line-height: 1.4"><h4 style="margin-top: 0;">' . __( 'Migration complete! Jesus may perform the best miracles, but this certainly qualifies as a spiritual gift.' ) . '</h4>' . sprintf( __( 'The last step is to <a href="https://upthemes.com/themes/uplifted/" target="_blank">install a beautiful new theme</a> (one that works with the Church Theme Content plugin), <a href="%s">disable this migration plugin</a>, and set up your homepage with the appropriate slides and widgets.' ), $this->change_plugin_state_url( 'deactivate', 'ct-migration.php' ) ) . '</div>';
		}

		echo '</div>';

	}

	/**
	 * Migrate sermons
	 *
	 * @since 0.1
	 * @access public
	 */
	public function migrate_sermons(){

		if( isset( $_GET[ 'migrate_sermons' ] ) && $_GET[ 'migrate_sermons' ] == true ){

			$posts = get_posts( array(
				'posts_per_page'=> $this->posts_per_page,
				'post_type' 	=> 'ct_sermon',
				'post_status'	=> 'all'
			) );

			if( $posts && is_array( $posts ) ){

				foreach( $posts as $post){

					$update = wp_update_post( array(
						'ID'		=> $post->ID,
						'post_type' => 'ctc_sermon',
					) );

					$sermon_video = get_post_meta($post->ID, '_ct_sm_video_file', true);

					if( ! $sermon_video ) {
						$sermon_video = get_post_meta( $post->ID , '_ct_sm_video_embed' , true );
					}

					if ( $sermon_video ) {
						$updated_sermon_video = add_post_meta( $post->ID , '_ctc_sermon_video' , $sermon_video );

						if( $updated_sermon_video ){
							delete_post_meta( $post->ID , '_ct_sm_video_embed' );
							delete_post_meta( $post->ID , '_ct_sm_video_file' );
						}
					}

					$sermon_audio = get_post_meta( $post->ID , '_ct_sm_audio_file' , true );

					if ( $sermon_audio ) {
						$updated_sermon_audio = add_post_meta( $post->ID , '_ctc_sermon_audio' , $sermon_audio );

						if( $updated_sermon_audio ){
							delete_post_meta( $post->ID , '_ct_sm_audio_file' );
						}
					}

					$sermon_pdf = get_post_meta( $post->ID , '_ct_sm_sg_file' , true );

					if ( $sermon_pdf ) {
						$updated_sermon_pdf = add_post_meta( $post->ID , '_ctc_sermon_pdf' , $sermon_pdf );

						if( $updated_sermon_pdf ){
							delete_post_meta( $post->ID , '_ct_sm_sg_file' );
						}
					}
				}
			}
		}
	}

	/**
	 * Migrate sermon taxonomies
	 *
	 * @since 0.1
	 * @access public
	 */
	public function migrate_sermon_taxonomy_terms(){

		$speakers_migrated = $this->migrate_taxonomy_terms( 'sermon_speaker', 'ctc_sermon_speaker' );

		if( isset( $speakers_migrated ) && $speakers_migrated ){
			add_action('admin_notices',function(){ echo '<div class="updated"><p>' . __('Sermon speakers migration complete.') . '</p></div>'; });
		}

		$topics_migrated = $this->migrate_taxonomy_terms( 'sermon_topic', 'ctc_sermon_topic' );

		if( isset( $topics_migrated ) && $topics_migrated ){
			add_action('admin_notices',function(){ echo '<div class="updated"><p>' . __('Sermon topic migration complete.') . '</p></div>'; });
		}

		$series_migrated = $this->migrate_taxonomy_terms( 'sermon_series', 'ctc_sermon_series' );

		if( isset( $series_migrated ) && $series_migrated ){
			add_action('admin_notices',function(){ echo '<div class="updated"><p>' . __('Sermon series migration complete.') . '</p></div>'; });
		}

	}

	/**
	 * Migrate people
	 *
	 * @since 0.1
	 * @access public
	 */
	public function migrate_people(){

		if( isset( $_GET[ 'migrate_people' ] ) && $_GET[ 'migrate_people' ] == true ){

			$posts = get_posts( array(
				'posts_per_page'=> $this->posts_per_page,
				'post_type' 	=> 'ct_person',
				'post_status'	=> 'all'
			) );

			if( $posts && is_array( $posts ) ){

				foreach( $posts as $post){

					$update = wp_update_post( array(
						'ID'		=> $post->ID,
						'post_type' => 'ctc_person',
					) );

					$person_role = get_post_meta($post->ID, '_ct_ppl_role', true);

					if ( $person_role ) {
						$updated_person_role = add_post_meta( $post->ID , '_ctc_person_position' , $person_role );

						if( $updated_person_role ){
							delete_post_meta( $post->ID , '_ct_ppl_role' );
						}
					}

					$person_email = get_post_meta($post->ID, '_ct_ppl_emailaddress', true);

					if ( $person_email ) {
						$updated_person_email = add_post_meta( $post->ID , '_ctc_person_email' , $person_email );

						if( $updated_person_email ){
							delete_post_meta( $post->ID , '_ct_ppl_emailaddress' );
						}
					}

					$person_phone = get_post_meta($post->ID, '_ct_ppl_phonenum1', true);

					if ( $person_phone ) {
						$updated_person_phone = add_post_meta( $post->ID , '_ctc_person_phone' , $person_phone );

						if( $updated_person_phone ){
							delete_post_meta( $post->ID , '_ct_ppl_phonenum1' );
						}
					}
				}
			}
		}
	}

	/**
	 * Migrate people taxonomies
	 *
	 * @since 0.1
	 * @access public
	 */
	public function migrate_people_taxonomy_terms(){

		$person_categories_migrated = $this->migrate_taxonomy_terms( 'person_category', 'ctc_person_group' );

		if( isset( $person_categories_migrated ) && $person_categories_migrated ){
			add_action('admin_notices',function(){ echo '<div class="updated"><p>' . __('Person category to groups migration complete.') . '</p></div>'; });
		}

	}


	/**
	 * Migrate people
	 *
	 * @since 0.1
	 * @access public
	 */
	public function migrate_locations(){

		if( isset( $_GET[ 'migrate_locations' ] ) && $_GET[ 'migrate_locations' ] == true ){

			$posts = get_posts( array(
				'posts_per_page'=> $this->posts_per_page,
				'post_type' 	=> 'ct_location',
				'post_status'	=> 'all'
			) );

			if( $posts && is_array( $posts ) ){

				foreach( $posts as $post){

					$update = wp_update_post( array(
						'ID'		=> $post->ID,
						'post_type' => 'ctc_location',
					) );

					$location_address = get_post_meta($post->ID, '_ct_loc_address1', true) . "\n" . get_post_meta($post->ID, '_ct_loc_address2', true) . "\n" . get_post_meta($post->ID, '_ct_loc_address3', true);

					if ( $location_address ) {
						$updated_location_address = add_post_meta( $post->ID , '_ctc_location_address' , $location_address );

						if( $updated_location_address ){
							delete_post_meta( $post->ID , '_ct_loc_address1' );
							delete_post_meta( $post->ID , '_ct_loc_address2' );
							delete_post_meta( $post->ID , '_ct_loc_address3' );
						}
					}

					$location_service_times = get_post_meta($post->ID, '_ct_loc_service1', true);

					if( get_post_meta($post->ID, '_ct_loc_service2', true) ){
						$location_service_times .= ', ' . get_post_meta($post->ID, '_ct_loc_service2', true);
					}

					if( get_post_meta($post->ID, '_ct_loc_service3', true) ){
						$location_service_times .= ', ' . get_post_meta($post->ID, '_ct_loc_service3', true);
					}

					if( get_post_meta($post->ID, '_ct_loc_service4', true) ){
						$location_service_times .= ', ' . get_post_meta($post->ID, '_ct_loc_service4', true);
					}

					if( get_post_meta($post->ID, '_ct_loc_service5', true) ){
						$location_service_times .= ', ' . get_post_meta($post->ID, '_ct_loc_service5', true);
					}

					if ( $location_service_times ) {
						$updated_location_service_times = add_post_meta( $post->ID , '_ctc_location_times' , $location_service_times );

						if( $updated_location_service_times ){
							delete_post_meta( $post->ID , '_ct_loc_service1' );
							delete_post_meta( $post->ID , '_ct_loc_service2' );
							delete_post_meta( $post->ID , '_ct_loc_service3' );
							delete_post_meta( $post->ID , '_ct_loc_service4' );
							delete_post_meta( $post->ID , '_ct_loc_service5' );
						}
					}




				}
			}
		}
	}

	/**
	 * Migrate taxonomy terms
	 *
	 * @since 0.1
	 * @access public
	 */
	public function migrate_taxonomy_terms( $old_taxonomy, $new_taxonomy ){

		if( taxonomy_exists( $old_taxonomy ) && taxonomy_exists( $new_taxonomy ) ){

			$terms = get_terms( array( $old_taxonomy ) );

			if( $terms && is_array( $terms ) ){

				$term_ids = array();

				foreach( $terms as $term ){

					$term_ids[] = $term->term_id;

				}

				$this->handle_change_tax( $term_ids, $old_taxonomy, $new_taxonomy );

			}

		} else {
			add_action('admin_notices',function(){ echo '<div class="error"><p>' . __('Taxonomy does not exist. Please ensure the Church Theme Content plugin is installed and activated and your ChurchThemes.net theme is enabled.') . '</p></div>'; });
		}

	}

	static function handle_change_tax( $term_ids, $old_taxonomy, $new_taxonomy ) {
		global $wpdb;

		$taxonomy = $old_taxonomy;
		$new_tax = $new_taxonomy;

		if ( !taxonomy_exists( $new_tax ) )
			return false;

		if ( $new_tax == $taxonomy )
			return false;

		$tt_ids = array();
		foreach ( $term_ids as $term_id ) {
			$term = get_term( $term_id, $taxonomy );

			if ( $term->parent && !in_array( $term->parent,$term_ids ) ) {
				$wpdb->update( $wpdb->term_taxonomy,
					array( 'parent' => 0 ),
					array( 'term_taxonomy_id' => $term->term_taxonomy_id )
				);
			}

			$tt_ids[] = $term->term_taxonomy_id;

			if ( is_taxonomy_hierarchical( $taxonomy ) ) {
				$child_terms = get_terms( $taxonomy, array(
					'child_of' => $term_id,
					'hide_empty' => false
				) );
				$tt_ids = array_merge( $tt_ids, wp_list_pluck( $child_terms, 'term_taxonomy_id' ) );
			}
		}
		$tt_ids = implode( ',', array_map( 'absint', $tt_ids ) );

		$wpdb->query( $wpdb->prepare( "
			UPDATE $wpdb->term_taxonomy SET taxonomy = %s WHERE term_taxonomy_id IN ($tt_ids)
		", $new_tax ) );

		if ( is_taxonomy_hierarchical( $taxonomy ) && !is_taxonomy_hierarchical( $new_tax ) ) {
			$wpdb->query( "UPDATE $wpdb->term_taxonomy SET parent = 0 WHERE term_taxonomy_id IN ($tt_ids)" );
		}

		delete_option( "{$taxonomy}_children" );
		delete_option( "{$new_tax}_children" );

		return true;
	}

	/**
	 * Generate an activation/deactivation/update URL for a plugin.
	 *
	 * @param  string 	$action 	activate || deactivate || update
	 * @param  string 	$plugin 	A plugin-folder/plugin-main-file.php path (e.g. "my-plugin/my-plugin.php")
	 *
	 * @return string 	$actionUrl 	The plugin activation url
	 */
	function change_plugin_state_url( $action, $plugin ) {
		// the plugin might be located in the plugin folder directly

		if ( strpos($plugin, '/') ) {
			$plugin = str_replace('/', '%2F', $plugin);
		}

		if( $action == 'activate' ){

			$actionUrl = sprintf(network_admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s'), $plugin);
			// change the plugin request to the plugin to pass the nonce check
			$_REQUEST['plugin'] = $plugin;
			$actionUrl = wp_nonce_url($actionUrl, 'activate-plugin_' . $plugin);

		} else if ( $action == 'deactivate' ){

			$actionUrl = sprintf(network_admin_url('plugins.php?action=deactivate&plugin=%s&plugin_status=all&paged=1&s'), $plugin);
			// change the plugin request to the plugin to pass the nonce check
			$_REQUEST['plugin'] = $plugin;
			$actionUrl = wp_nonce_url($actionUrl, 'deactivate-plugin_' . $plugin);

		}

		return $actionUrl;
	}
}

// Instantiate the main class
new Church_Theme_Content_Migration();
