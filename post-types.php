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
 * Requires PHP:      8.3
 * Author:            Jan Eike Suchard
 * Author URI:        https://suchard.cloud
 * Text Domain:       ggl-post-types
 * License:           EUPL-1.2
 * License URI:       https://joinup.ec.europa.eu/sites/default/files/custom-page/attachment/2020-03/EUPL-1.2%20EN.txt
 * Update URI:        false
 */

require_once 'post-types/supporters.php';

add_action('init', 'ggl_post_type_supporter');
add_filter( 'rwmb_meta_boxes', 'supporter_register_meta_boxes' );


require_once 'post-types/movie.php';
 
add_action('init', 'ggl_post_type_movie');
add_filter('wp_insert_post_data', 'movie_check_name_leaking', 10, 3);
add_filter('rwmb_meta_boxes', 'movie_extended_info_meta_boxes');
add_filter('rwmb_meta_boxes', 'movie_screening_info_meta_boxes');