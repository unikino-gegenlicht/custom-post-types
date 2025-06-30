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
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      8.1
 * Author:            Jan Eike Suchard
 * Author URI:        https://suchard.cloud
 * Text Domain:       ggl-post-types
 * License:           EUPL-1.2
 * License URI:       https://joinup.ec.europa.eu/sites/default/files/custom-page/attachment/2020-03/EUPL-1.2%20EN.txt
 * Update URI:        false
 */

require_once dirname( __FILE__ ) . '/inc/countries.php';
require_once dirname( __FILE__ ) . '/inc/languages.php';

require_once( dirname( __FILE__ ) . "/functions.php" );

/* Register the taxonomies */
require_once 'taxonomies/semester.php';
unregister_taxonomy( 'semester' );
add_action( 'init', 'ggl_taxonomy_semester' );
add_filter( 'rwmb_meta_boxes', 'ggl_taxonomy_semester_meta_boxes' );


require_once 'taxonomies/special-program.php';
unregister_taxonomy( 'program-type' );
add_action( 'init', 'ggl_taxonomy_program_type' );
add_filter( 'rwmb_meta_boxes', 'ggl_taxonomy_program_type_meta_boxes' );

require_once 'taxonomies/director.php';
add_action( 'init', 'ggl_taxonomy_director' );

require_once 'taxonomies/actor.php';
add_action( 'init', 'ggl_taxonomy_actor' );

/* Register the post types */
require_once 'post-types/movie.php';
add_action( 'init', 'ggl_post_type_movie' );
add_filter( 'rwmb_meta_boxes', 'movie_extended_info_meta_boxes' );
add_filter( 'rwmb_meta_boxes', 'movie_licensing_and_age_rating_meta_boxes' );
add_filter( 'rwmb_meta_boxes', 'movie_sound_information_meta_boxes' );
add_filter( 'rwmb_meta_boxes', 'movie_screening_info_meta_boxes' );
add_filter( 'rwmb_meta_boxes', 'movie_text_boxes' );
add_filter( 'rwmb_meta_boxes', 'movie_short_movie_box' );
add_action( 'save_post_movie', 'ensure_numerical_movie_link' );

require_once 'post-types/event.php';
add_action( 'init', 'ggl_post_type_event' );
add_filter( 'rwmb_meta_boxes', 'event_extended_info_meta_boxes' );
add_filter( 'rwmb_meta_boxes', 'event_sound_information_meta_boxes' );
add_filter( 'rwmb_meta_boxes', 'event_screening_info_meta_boxes' );
add_filter( 'rwmb_meta_boxes', 'event_additional_information_box' );
add_action( 'save_post_event', 'generate_numerical_event_id' );

require_once 'post-types/supporter.php';
add_action( 'init', 'ggl_post_type_supporter' );
add_filter( 'rwmb_meta_boxes', 'supporter_register_meta_boxes' );

require_once 'post-types/cooperation-partner.php';
add_action( 'init', 'ggl_post_type_cooperation_partner' );
add_filter( 'rwmb_meta_boxes', 'cooperation_partner_register_meta_boxes' );

require_once 'post-types/team-member.php';
add_action( 'init', 'ggl_post_type_team_member' );
add_filter( 'rwmb_meta_boxes', 'team_member_register_meta_boxes' );


add_action( 'admin_menu', 'reorder_menu' );


add_action( 'plugins_loaded', 'ggl_post_types_load_textdomain' );
