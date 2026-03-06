<?php

/**
 * GEGENLICHT Custom Post Types
 *
 * This plugin brings some new post types to WordPress installations.
 * These post types are tailored to the usage at our student cinema but may be
 * adapted by other cinemas and student organizations.
 *
 * @package GGL_Post_Types
 * @author Jan Eike Suchard
 * @copyright 2024 Jan Eike Suchard
 * @license EUPL-1.2
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Post Types for Movies, Team Members and Supporters
 * Plugin URI:        https://github.com/unikino-gegenlicht/custom-post-types
 * Description:       This plugin introduces custom post types to the WordPress installation which enable handling of movies, team members and supporters
 * Version:           GGL_PLUGIN_VERSION
 * Requires at least: 6.1
 * Requires PHP:      8.4
 * Author:            Jan Eike Suchard
 * Author URI:        https://suchard.cloud
 * Text Domain:       ggl-post-types
 * License:           EUPL-1.2
 * License URI:       https://joinup.ec.europa.eu/sites/default/files/custom-page/attachment/2020-03/EUPL-1.2%20EN.txt
 * Update URI:        false
 */

require_once dirname( __FILE__ ) . '/src/inc/countries.php';
require_once dirname( __FILE__ ) . '/src/inc/languages.php';
require_once dirname( __FILE__ ) . '/vendor/autoload.php';
require_once dirname( __FILE__ ) . "/src/const.php";

require_once( dirname( __FILE__ ) . "/src/functions.php" );

add_filter( 'months_dropdown_results', '__return_empty_array' );

/* Register the taxonomies */
require_once 'src/taxonomies/semester.php';
unregister_taxonomy( 'semester' );
add_action( 'init', 'ggl_taxonomy_semester' );
add_filter( 'rwmb_meta_boxes', 'ggl_taxonomy_semester_meta_boxes' );


require_once 'src/taxonomies/special-program.php';
unregister_taxonomy( 'program-type' );
add_action( 'init', 'ggl_taxonomy_program_type' );
add_filter( 'rwmb_meta_boxes', 'ggl_taxonomy_program_type_meta_boxes' );
add_action( "parse_term_query", "ggl_cpt__reorder_semesters", 2, 2 );

require_once 'src/taxonomies/director.php';
add_action( 'init', 'ggl_taxonomy_director' );

require_once 'src/taxonomies/actor.php';
add_action( 'init', 'ggl_taxonomy_actor' );
add_filter( 'rwmb_meta_boxes', 'ggl_taxonomy_actor_meta_boxes' );
add_action( 'actor_pre_add_form', function ( $term ) {
	echo '<div style="background-color: lightgoldenrodyellow; padding: 0.05rem 0.1rem; border-radius: 0.75rem;">
	<h3 style="text-align: center; font-variant-caps: small-caps">' . esc_html__( "Notice" ) . '</h3>
	<p>' . esc_html__( "Only add a new entry, if the one you want is not available. Adding values like 'multiple' or 'none' is not allowed and those entries will be removed without further notice" ) . '</p>
</div>';
}, 10, 2 );


/* Register the post types */
require_once 'src/post-types/movie.php';
add_action( 'init', 'ggl_post_type_movie' );
add_action( 'restrict_manage_posts', 'ggl_cpt__add_movie_semester_filter' );
add_action( 'pre_get_posts', 'ggl_cpt__apply_movie_semester_filter' );
add_action( 'restrict_manage_posts', 'ggl_cpt__add_movie_program_filter' );
add_action( 'pre_get_posts', 'ggl_cpt__apply_movie_program_filter' );
add_filter( 'rwmb_meta_boxes', 'movie_extended_info_meta_boxes' );
add_filter( 'rwmb_meta_boxes', 'movie_text_boxes' );
add_action( 'save_post_movie', 'ensure_numerical_movie_link', 1 );
add_action( "admin_head-edit.php", "ggl_cpt__replace_movie_title_in_table" );

require_once 'src/post-types/event.php';
add_action( 'init', 'ggl_post_type_event' );
add_action( 'restrict_manage_posts', 'ggl_cpt__add_event_semester_filter' );
add_action( 'pre_get_posts', 'ggl_cpt__apply_event_semester_filter' );
add_action( 'restrict_manage_posts', 'ggl_cpt__add_event_program_filter' );
add_action( 'pre_get_posts', 'ggl_cpt__apply_event_program_filter' );
add_filter( 'rwmb_meta_boxes', 'event_extended_info_meta_boxes' );
add_filter( 'rwmb_meta_boxes', 'event_additional_information_box' );
add_action( 'save_post_event', 'generate_numerical_event_id' );

require_once 'src/post-types/supporter.php';
add_action( 'init', 'ggl_post_type_supporter' );
add_filter( 'rwmb_meta_boxes', 'supporter_register_meta_boxes' );

require_once 'src/post-types/cooperation-partner.php';
add_action( 'init', 'ggl_post_type_cooperation_partner' );
add_filter( 'rwmb_meta_boxes', 'cooperation_partner_register_meta_boxes' );

require_once 'src/post-types/team-member.php';
add_action( 'init', 'ggl_post_type_team_member' );
add_action( "restrict_manage_posts", "ggl_cpt__add_team_member_status_filter" );
add_action( "pre_get_posts", "ggl_cpt__apply_team_member_status_filter" );
add_filter( 'rwmb_meta_boxes', 'team_member_register_meta_boxes' );

require_once 'src/post-types/screening-location.php';
add_action( 'init', 'ggl_post_type_screening_location' );
add_filter( 'rwmb_meta_boxes', 'location_register_meta_boxes' );

add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'ggl_menu_order' );
add_action( 'admin_menu', 'ggl_cpt__spaceout_admin_menu' );


add_action( 'plugins_loaded', 'ggl_post_types_load_textdomain' );


require_once "src/seo/opengraph.php";
add_filter( "wpseo_opengraph_image", "ggl_cpt__change_opengraph_image_url", 11 );
add_filter( "wpseo_opengraph_image_height", "ggl_cpt__change_opengraph_image_height", 11 );
add_filter( "wpseo_opengraph_image_width", "ggl_cpt__change_opengraph_image_width", 11 );

require_once "src/seo/replacements.php";
add_action( "wpseo_register_extra_replacements", function () {
	wpseo_register_var_replacement( "%%ggl_title%%", "ggl_pt_get_title", "advanced", "The protected title of a movie or event" );
	wpseo_register_var_replacement( "%%ggl_date%%", "ggl_pt_screening_date", "advanced", "The formatted screening date for the entry" );
	wpseo_register_var_replacement( "%%ggl_details%%", "ggl_pt_details", "advanced", "The protected text for the entry" );
	wpseo_register_var_replacement( "%%ggl_text%%", "ggl_pt_text", "advanced", "The protected text for the entry" );
} );

require_once "src/seo/oembed.php";
add_filter( 'oembed_response_data', 'ggl_cpt__update_oembed_data', 10, 4 );

add_action( "after_setup_theme", function () {
	add_image_size( 'opengraph', 1200, 675, crop: true );
} );

function ggl_cpt__change_title_text( $title ) {
	$screen = get_current_screen();

	if ( 'team-member' == $screen->post_type ) {
		$title = esc_html__( 'Enter Name of the Teamie', "ggl-post-types" );
	}

	if ( 'cooperation-partner' == $screen->post_type ) {
		$title = esc_html__( 'Enter Name of the Cooperation Partner', "ggl-post-types" );
	}

	if ( 'supporter' == $screen->post_type ) {
		$title = esc_html__( 'Enter Name of the Supporter', "ggl-post-types" );
	}

	if ( 'screening-location' == $screen->post_type ) {
		$title = esc_html__( 'Enter Name of the Screening Location', "ggl-post-types" );
	}

	return $title;
}

add_filter( 'enter_title_here', 'ggl_cpt__change_title_text' );


add_action( 'rwmb_meta_boxes', function ( $meta_boxes ) {
	$meta_boxes[] = [
		'title'  => __( "Extended Settings", "ggl-post-types" ),
		'type'   => 'user',
		'fields' => [
			[
				'name'       => __( 'Team Member', 'ggl-post-types' ),
				'id'         => 'teamie_id',
				'type'       => 'post',
				'post_type'  => 'team-member',
				'field_type' => 'select_advanced',
				'query_args' => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'meta_query'     => [
						[
							"key"   => "status",
							"value" => "active",
						]
					],
					'orderby'        => 'title',
					'order'          => 'ASC',
				],
				'ajax'       => true,
				'desc'       => __( "Select the team member that shall be linked with your account. This will automatically set you as the person who selected a movie/event during the creation of a new entry", "ggl-post-types" ),
			]
		]
	];

	return $meta_boxes;
} );

add_action( 'upgrader_process_complete', 'ggl_cpt__migrate_meta_boxes', 10, 2 );

function ggl_cpt__migrate_meta_boxes( $upgrader_object, $options ): void {
	$current_plugin_path_name = plugin_basename( __FILE__ );

	if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
		foreach ( $options['plugins'] as $each_plugin ) {
			if ( $each_plugin == $current_plugin_path_name ) {
				ggl_cpt__migrate_metabox_values();
			}
		}
	}
}

function ggl_cpt__migrate_metabox_values() {
	$country_source_file_path = dirname( __FILE__ ) . "/assets/iso3166.csv";
	$rows                     = array_map( 'str_getcsv', file( $country_source_file_path ) );
	$header                   = array_shift( $rows );
	$countries                = array();
	foreach ( $rows as $row ) {
		$countries[] = array_combine( $header, $row );
	}

	$checkable_posts = get_posts( [
		'post_type'      => [ 'movie', 'event' ],
		'posts_per_page' => - 1,
	] );

	foreach ( $checkable_posts as $checkable_post ) {
		$cc = get_post_meta( $checkable_post->ID, 'country', true );
		if ( preg_match( '/^[A-Z]{2}$/m', $cc ) ) {
			foreach ( $countries as $country ) {
				if ( $country["letter_code"] != $cc ) {
					continue;
				}
				$num_code = $country["num_id"];
				update_post_meta( $checkable_post->ID, 'country', $num_code );
			}
		}

		$release_date       = get_post_meta( $checkable_post->ID, 'release_date', true );
		$release_date_is_ts = preg_match( '/^[0-9]+$/', $release_date );
		if ( ! $release_date_is_ts ) {
			$release_date_ts = strtotime( $release_date );
			update_post_meta( $checkable_post->ID, 'release_date', $release_date_ts );
		}


	}
}

add_action( 'admin_head', 'wpster_remove_permalink_section' );
function wpster_remove_permalink_section() {
	global $post;
	if ( isset( $post ) ) {
		if ( $post->post_type === 'movie' || $post->post_type === 'event' || $post->post_type === 'team-member' || $post->post_type === 'cooperation-partner' || $post->post_type === 'supporter' ) {
			echo "<style>#edit-slug-box {display:none;}</style>";
		}
	}

}

?>