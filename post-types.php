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

require_once dirname( __FILE__ ) . "/src/public-functions.php";
require_once dirname( __FILE__ ) . '/src/inc/languages.php';
require_once dirname( __FILE__ ) . '/vendor/autoload.php';
require_once dirname( __FILE__ ) . "/src/const.php";
require_once dirname( __FILE__ ) . "/src/seo/schema-classes/Event.php";

require_once( dirname( __FILE__ ) . "/src/functions.php" );

add_filter( 'months_dropdown_results', '__return_empty_array' );

/* Register plugin settings */
add_action( "mb_settings_pages", "ggl_cpt__register_settings" );
add_filter( "rwmb_meta_boxes", "ggl_cpt__settings_meta_boxes" );

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
add_action( "saved_special-program", "ggl_special_program_pregenerate_stylesheet", 1, 1 );

require_once 'src/taxonomies/director.php';
add_action( 'init', 'ggl_taxonomy_director' );
add_action( "director_pre_add_form", "ggl_cpt__hide_edit_boxes" );
add_action( "director_pre_edit_form", "ggl_cpt__hide_edit_boxes" );

require_once 'src/taxonomies/actor.php';
add_action( 'init', 'ggl_taxonomy_actor' );
add_action( 'actor_pre_add_form', "ggl_cpt__hide_edit_boxes" );
add_action( 'actor_pre_edit_form', "ggl_cpt__hide_edit_boxes" );


/* Register the post types */
require_once 'src/post-types/movie.php';
add_action( 'init', 'ggl_post_type_movie' );
add_action( 'restrict_manage_posts', 'ggl_cpt__add_movie_semester_filter' );
add_action( 'pre_get_posts', 'ggl_cpt__apply_movie_semester_filter' );
add_filter( 'rwmb_meta_boxes', 'movie_extended_info_meta_boxes' );
add_filter( 'rwmb_meta_boxes', 'movie_text_boxes' );
add_action( 'save_post_movie', 'ensure_numerical_movie_link', 1 );
add_filter( "wpseo_schema_graph_pieces", "add_event_for_movie", 15, 2 );
function add_event_for_movie( $pieces, $context ) {
	$pieces[] = new Event( $context );

	return $pieces;
}

require_once 'src/post-types/event.php';
add_action( 'init', 'ggl_post_type_event' );
add_action( 'restrict_manage_posts', 'ggl_cpt__add_event_semester_filter' );
add_action( 'pre_get_posts', 'ggl_cpt__apply_event_semester_filter' );
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
add_action( "pre_get_posts", "ggl_cpt__check_hidden_teamie_redirect" );

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
	wpseo_register_var_replacement( "%%ggl_title%%", "ggl_cpt__seo_title", "advanced", "The protected title of a movie or event" );
	wpseo_register_var_replacement( "%%ggl_date%%", "ggl_cpt__seo_date", "advanced", "The formatted screening date for the entry" );
	wpseo_register_var_replacement( "%%ggl_details%%", "ggl_cpt__seo_tagline", "advanced", "The protected text for the entry" );
	wpseo_register_var_replacement( "%%ggl_text%%", "ggl_cpt__seo_summary", "advanced", "The protected text for the entry" );
} );

require_once "src/seo/oembed.php";
add_filter( 'oembed_response_data', 'ggl_cpt__update_oembed_data', 10, 4 );


add_action( "after_setup_theme", function () {
	add_image_size( 'opengraph', 1200, 675, crop: true );
	add_image_size( 'member-crop', 450, 600, crop: true );
	add_image_size( 'small-member-crop', 210, 280, crop: true );
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

function ggl_cpt__hide_edit_boxes() {
	?>
    <style>
        .term-slug-wrap,
        .term-description-wrap {
            display: none;
        }
    </style>
	<?php
}

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


add_action( 'admin_head', 'wpster_remove_permalink_section' );
function wpster_remove_permalink_section() {
	global $post;
	if ( isset( $post ) ) {
		if ( $post->post_type === 'movie' || $post->post_type === 'event' || $post->post_type === 'team-member' || $post->post_type === 'cooperation-partner' || $post->post_type === 'supporter' ) {
			echo "<style>#edit-slug-box {display:none;}</style>";
		}
	}

}