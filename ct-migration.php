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

		if( isset( $_GET[ 'migrate_tax' ] ) && $_GET[ 'migrate_tax' ] == true ){

			add_action( 'admin_init', array( &$this, 'migrate_sermon_taxonomy_terms' ) );

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

		echo '<h1>' . __( 'ChurchThemes.net Migration' ) . '</h1>';

		$posts = get_posts( array(
			'posts_per_page'=> $this->posts_per_page,
			'post_type' 	=> 'ct_sermon',
		) );

		echo '<h3>' . __( 'Migrate Sermons' ) . '</h3>';

		echo '<p>' . __( 'Click the buttons below to migrate your sermon content to the new format. PLEASE NOTE: The new sermon manager does not use "podcast" or "services" so these taxonomies will not be migrated.' ) . '</p>';

		if( is_array( $posts ) && count( $posts ) === 0 ){
			echo '<div class="completed"><i class="dashicons dashicons-yes" style="color: green;"></i> ' . __( 'Sermon Migration Complete') . '</div>';
		} else {
			echo '<p><a class="button button-primary" href="' . esc_url( add_query_arg( array( 'page' => 'ct-migration', 'migrate_sermons' => '1', 'migrate_tax' => '1' ), $_SERVER[ 'PHP_SELF' ] ) ) . '">' . __( 'Migrate Sermons' ) . '</a></p>';
		}

		$taxonomy_terms = get_terms( array( 'sermon_speaker', 'sermon_topic', 'sermon_series' ) );

		if( is_array( $taxonomy_terms ) && count( $taxonomy_terms ) === 0 ){
			echo '<div class="completed"><i class="dashicons dashicons-yes" style="color: green;"></i> ' . __( 'Sermon Taxonomy Migration Complete') . '</div>';
		} else {
			echo '<p><a class="button button-secondary" href="' . esc_url( add_query_arg( array( 'page' => 'ct-migration', 'migrate_tax' => '1' ), $_SERVER[ 'PHP_SELF' ] ) ) . '">' . __( 'Migrate Sermon Taxonomies' ) . '</a></p>';
		}

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
			) );

			if( $posts && is_array( $posts ) ){

				foreach( $posts as $post){

					$update = wp_update_post( array(
						'ID'		=> $post->ID,
						'post_type' => 'ctc_sermon',
					) );

					if ( $sermon_video === ( get_post_meta($post->ID, '_ct_sm_video_file', true) || get_post_meta( $post->ID , '_ct_sm_video_embed' , true ) ) ) {
						$updated_sermon_video = add_post_meta( $post->ID , '_ctc_sermon_video' , $sermon_video );

						if( $updated_sermon_video ){
							delete_post_meta( $post->ID , '_ct_sm_video_embed' );
							delete_post_meta( $post->ID , '_ct_sm_video_file' );
						}
					}

					if ( $sermon_audio === get_post_meta( $post->ID , '_ct_sm_audio_file' , true ) ) {
						$updated_sermon_audio = add_post_meta( $post->ID , '_ctc_sermon_audio' , $sermon_audio );

						if( $updated_sermon_audio ){
							delete_post_meta( $post->ID , '_ct_sm_audio_file' );
						}
					}

					if ( $sermon_pdf === get_post_meta( $post->ID , '_ctc_sermon_pdf' , true ) ) {
						$updated_sermon_pdf = add_post_meta( $post->ID , '_ctc_sermon_pdf' , $sermon_pdf );

						if( $updated_sermon_pdf ){
							delete_post_meta( $post->ID , '_ctc_sermon_pdf' );
						}
					}

					add_action('admin_notices',function(){ echo '<div class="updated"><p>' . printf( __( 'Updated sermon #%s' ), $update->post_id ) . '</p></div>'; });
				}
			}

			migrate_sermon_taxonomy_terms();
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
}

// Instantiate the main class
new Church_Theme_Content_Migration();
