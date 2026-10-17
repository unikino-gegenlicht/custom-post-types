<?php

namespace CinemaPress;

use CinemaPress\post_types\Admission_Type;
use CinemaPress\post_types\Cooperation_Partners;
use CinemaPress\post_types\Movie;
use CinemaPress\post_types\Screening;
use CinemaPress\post_types\Screening_Location;
use CinemaPress\post_types\Sponsors;
use CinemaPress\post_types\Team_Member;
use CinemaPress\taxonomies\Actor;
use CinemaPress\taxonomies\Director;
use CinemaPress\taxonomies\Program_Series;
use CinemaPress\taxonomies\Semester;

/**
 * # Cinema WordPress Post Types
 *
 * This WordPress plugin introduces new post types to a page which are helpful
 * when using WordPress to manage a cinema's website.
 * The post types have originally been developed for the usage at a small
 * university cinema, but will slowly be adapted for usage in even larger
 * scale cinemas.
 *
 * @package Cinema_Press
 * @author Jan Eike Suchard <jan@suchard.cloud>
 * @copyright 2024-2026 Unikino GEGENLICHT
 * @license EUPL-1.2-or-later
 *
 * @wordpress-plugin
 * Plugin Name: CinemaPress
 * Plugin URI: https://github.com/unikino-gegenlicht/custom-post-types
 * Description: CinemaPress is the go-to plugin for creating a website for your cinema. It features pre-made custom post types and taxonomies that make managing your website easier. This plugin is mainly targeted at smaller cinemas and university cinemas!
 * Version: CINEMA_PRESS_PLUGIN_VERSION
 * Requires at least: 6.1
 * Requires PHP: 8.4
 * Author: Jan Eike Suchard, Philip Kaufmann
 * Author URI: https://gegenlicht.net/team/
 * License: EUPL-1.2
 * License URI: https://joinup.ec.europa.eu/sites/default/files/custom-page/attachment/2020-03/EUPL-1.2%20EN.txt
 * Update URI: false
 * Text Domain: cinema-press
 * Domain Path: /i18n
 */

const GGL_CPT__WYSIWYG_OPTIONS = [
	'teeny'         => false,
	'media_buttons' => false,
	"quicktags"     => false,
	"textarea_rows" => 10,
	"wpautop"       => false,
	"tinymce"       => [
		"toolbar1"       => "bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,removeformat,undo,redo",
		"toolbar2"       => "",
		"valid_elements" => "strong/b,p[style],em,ul,ol,li,blockquote,del,br,span[style]",
		"invalid_styles" => "display position float clear z-index top right bottom left width height box-sizing overflow clip-path visibility margin margin-top margin-right margin-bottom margin-left padding padding-top padding-right padding-bottom padding-left border border-width border-style border-color border-radius border-top border-bottom border-right border-left outline outline-width outline-style outline-color background background-color background-image background-position background-size background-repeat background-clip background-origin background-attachment background-blend-mode backdrop-filter font-family font-size font-style font-weight font-variant color text-transform letter-spacing line-height word-spacing text-shadow white-space direction list-style-type list-style-position list-style-image flex flex-direction flex-wrap flex-flow flex-grow flex-shrink flex-basis justify-content align-items align-self align-content order grid grid-template-columns grid-template-rows grid-area grid-column grid-row grid-gap transition transition-property transition-duration transition-timing-function transition-delay animation animation-name animation-duration animation-timing-function animation-delay animation-iteration-count animation-direction animation-fill-mode animation-play-state filter box-shadow opacity mix-blend-mode isolation will-change transform transform-origin transform-style perspective perspective-origin backface-visibility border-collapse border-spacing table-layout caption-side empty-cells vertical-align cursor pointer-events resize user-select content quotes counter-set counter-increment counter-reset all"
	]
];

/* Initiating plugin by loading composer data */
require_once dirname( __FILE__ ) . "/vendor/autoload.php";
require_once dirname( __FILE__ ) . "/const.php";
require_once dirname( __FILE__ ) . "/functions.php";

/* Import all interfaces */
require_once dirname( __FILE__ ) . "/interfaces/Post_Type.php";
require_once dirname( __FILE__ ) . "/interfaces/Taxonomy.php";

/* Import all post types */
require_once dirname( __FILE__ ) . "/post_types/Screening_Location.php";
require_once dirname( __FILE__ ) . "/post_types/Movie.php";
require_once dirname( __FILE__ ) . "/post_types/Screening.php";
require_once dirname( __FILE__ ) . "/post_types/Team_Member.php";
require_once dirname( __FILE__ ) . "/post_types/Admission_Type.php";
require_once dirname( __FILE__ ) . "/post_types/Cooperation_Partners.php";
require_once dirname( __FILE__ ) . "/post_types/Sponsors.php";
// todo: import other post types

/* Import all taxonomies */
require_once dirname( __FILE__ ) . "/taxonomies/Actor.php";
require_once dirname( __FILE__ ) . "/taxonomies/Director.php";
require_once dirname( __FILE__ ) . "/taxonomies/Semester.php";
require_once dirname( __FILE__ ) . "/taxonomies/Program_Series.php";

/**
 * Register all new post types and taxonomies
 */
add_action( 'init', function () {

	register_post_type( Movie::SLUG, Movie::register_data() );
	add_action( 'rwmb_meta_boxes', array( Movie::class, 'meta_boxes' ) );
	register_post_type( Screening_Location::SLUG, Screening_Location::register_data() );
	add_action( 'rwmb_meta_boxes', array( Screening_Location::class, 'meta_boxes' ) );
	register_post_type( Screening::SLUG, Screening::register_data() );
	add_action( 'rwmb_meta_boxes', array( Screening::class, 'meta_boxes' ) );
	register_post_type( Admission_Type::SLUG, Admission_Type::register_data() );
	add_action( 'rwmb_meta_boxes', array( Admission_Type::class, 'meta_boxes' ) );

	if ( ( get_option( 'cinema-press' )['enable_team'] ?? false ) === 1 ) {
		register_post_type( Team_Member::SLUG, Team_Member::register_data() );
		add_action( 'rwmb_meta_boxes', array( Team_Member::class, 'meta_boxes' ) );
	}

	if ( ( get_option( 'cinema-press' )['enable_cooperation_partners'] ?? false ) === 1 ) {
		register_post_type( Cooperation_Partners::SLUG, Cooperation_Partners::register_data() );
		add_action( 'rwmb_meta_boxes', array( Cooperation_Partners::class, 'meta_boxes' ) );
	}

	if ( ( get_option( 'cinema-press' )['enable_sponsors'] ?? false ) === 1 ) {
		register_post_type( Sponsors::SLUG, Sponsors::register_data() );
	}


	register_taxonomy( Actor::SLUG, [ Movie::SLUG ], Actor::register_data() );
	register_taxonomy( Director::SLUG, [ Movie::SLUG ], Director::register_data() );
	register_taxonomy( Semester::SLUG, [ Semester::SLUG ], Semester::register_data() );
	add_action( 'rwmb_meta_boxes', array( Semester::class, 'meta_boxes' ) );
	register_taxonomy( Program_Series::SLUG, [ Screening::SLUG ], Program_Series::register_data() );
	add_action( 'rwmb_meta_boxes', array( Program_Series::class, 'meta_boxes' ) );

	// preregister cinema press settings
	if ( get_option( 'cinema-press' ) === false ) {
		add_option( 'cinema-press', [
			'enable_team'                        => 1,
			'enable_cooperation_partners'        => 1,
			'enable_sponsors'                    => 1,
			'enable_teamie_screening_assignment' => 1,
		] );
	}
} );

/* Instantiate settings page to control features of the plugin */
add_action( 'mb_settings_pages', function ( $settings ) {
	$settings[] = array(
		'id'         => "cinema-press",
		'menu_title' => __( 'CinemaPress Settings', 'cinema-press' ),
		'page_title' => __( 'CinemaPress Settings', 'cinema-press' ),
		'capability' => 'manage_options',
		'icon_url'   => 'dashicons-tickets',
		'customizer' => false,
		'style'      => 'no-boxes',
		'tabs'       => array(
			'features'             => array(
				'label' => __( 'Features', 'cinema-press' ),
				'icon'  => 'dashicons-admin-generic',
			),
			'screenings'           => array(
				'label' => __( 'Screenings', 'cinema-press' ),
				'icon'  => 'dashicons-calendar',
			),
			'events'               => array(
				'label' => __( 'Events', 'cinema-press' ),
				'icon'  => 'dashicons-calendar',
			),
			'team'                 => array(
				'label' => __( 'Team', 'cinema-press' ),
				'icon'  => 'dashicons-groups',
			),
			'cooperation-partners' => array(
				'label' => __( 'Cooperation Partners', 'cinema-press' ),
				'icon'  => 'dashicons-share-alt'
			),
			'sponsors'             => array(
				'label' => __( 'Sponsors', 'cinema-press' ),
				'icon'  => 'dashicons-awards',
			)
		)
	);

	return $settings;
} );

add_action( 'rwmb_meta_boxes', function ( $meta_boxes ) {
	$meta_boxes[] = array(
		'id'             => 'cinema-press_feature_settings',
		'title'          => __( 'CinemaPress Features', 'cinema-press' ),
		'settings_pages' => 'cinema-press',
		'context'        => 'normal',
		'tab'            => 'features',
		'fields'         => array(
			[
				'type' => 'custom_html',
				'std'  => '<div class="notice notice-info">' . __( 'After changing one of the feature states, you need to reload the page for the changes to appear', 'cinema-press' ) . '</div>',
			],
			[
				'id'   => "enable_team",
				'type' => 'checkbox',
				'name' => __( 'Enable Team Features', 'cinema-press' ),
				'desc' => __( 'Team features include the display of team members, assigning screenings to be proposals by team members and providing personalizable pages for members', 'cinema-press' ),
				'std'  => 1,
			],
			[
				'id'   => "enable_cooperation_partners",
				'type' => 'checkbox',
				'name' => __( 'Enable Cooperation Features', 'cinema-press' ),
				'desc' => __( 'Cooperation features include the display of cooperation partners, displaying cooperations on screenings and providing a overview of previous cooperations', 'cinema-press' ),
				'std'  => 1,
			],
			[
				'id'   => "enable_sponsors",
				'type' => 'checkbox',
				'name' => __( 'Enable Sponsors features', 'cinema-press' ),
				'desc' => __( 'This enables creating sponsors and giving them a shoutout on a special overview page.', 'cinema-press' ),
			]
		)
	);

	$meta_boxes[] = array(
		'id'             => 'cinema-press_team_settings',
		'title'          => __( 'Team Settings', 'cinema-press' ),
		'settings_pages' => 'cinema-press',
		'context'        => 'normal',
		'tab'            => 'team',
		'tabs'           => array(
			'current'  => array( "label" => __( "Current Member" ) ),
			'former'   => array( "label" => __( "Former Member" ) ),
			'honorary' => array( "label" => __( "Honorary Member" ) ),
		),
		'fields'         => array(
			[
				'id'      => 'team_disabled_hint',
				'type'    => 'custom_html',
				'std'     => '<p class="notice notice-info">' . esc_html__( 'The team management features have been disabled!', 'cinema-press' ) . '</p>',
				'visible' => [ 'enable_team', false ]
			],
			[
				'id'      => 'default_teamie_image',
				'type'    => 'single_image',
				'name'    => __( 'Default Team Member Image', 'cinema-press' ),
				'desc'    => __( 'This image is used if a team member doesn\'t have an image set or uploaded.', 'cinema-press' ),
				'visible' => [ 'enable_team', true ]
			],
			[
				'id'                => 'enable_teamie_screening_assignment',
				'type'              => 'checkbox',
				'name'              => __( 'Enable Teamie Screening Assignment', 'cinema-press' ),
				'label_description' => __( 'If checked you can assign teamies to a screening to show that they selected the movie for it', 'cinema-press' ),
				'visible'           => [ 'enable_team', true ]
			],
			[
				'type'    => 'custom_html',
				'std'     => '<h3 style="margin: -20px 0">' . esc_html__( 'Fallback Team Member Descriptions', 'cinema-press' ) . '</h3>',
				'visible' => [ 'enable_team', true ]
			],
			[
				'type'    => 'custom_html',
				'std'     => '<div class="rwmb-field" style="padding: 0"><div class="rwmb-label">
								<label>' . esc_html__( 'Available placeholders', 'cinema-press' ) . '</label>
								<p class="description">' . esc_html__( 'The following placeholders are available to be used in the fallback teamie description', 'cinema-press' ) . '</p>
								</div>
								<div class="rwmb-input">
									<dl>
									<dt><code>{joined-in}</code></dt>
									<dd>' . esc_html__( 'The year the member joined the cinema', 'cinema-press' ) . '</dd>
									<dt><code>{left-in}</code></dt>
									<dd>' . esc_html__( 'The year the member left the cinema', 'cinema-press' ) . '</dd>
									<dt><code>{name}</code></dt>
									<dd>' . esc_html__( 'The members name', 'cinema-press' ) . '</dd>
									<dt><code>{sceenings-count}</code></dt>
									<dd>' . esc_html__( 'The number of screenings the team member hosted', 'cinema-press' ) . '</dd>
									</dl>
								</div>
								</div>',
				'visible' => [ 'enable_team', true ]
			],
			[
				'id'      => 'teamie_fallback_description',
				'type'    => 'wysiwyg',
				'tab'     => 'current',
				'options' => CinemaPress_WYSIWYG_OPTIONS,
				'visible' => [ 'enable_team', true ]
			],
			[
				'id'      => 'former_teamie_fallback_description',
				'type'    => 'wysiwyg',
				'tab'     => 'former',
				'options' => CinemaPress_WYSIWYG_OPTIONS,
				'visible' => [ 'enable_team', true ]
			],
			[
				'id'      => 'honorary_teamie_fallback_description',
				'type'    => 'wysiwyg',
				'tab'     => 'honorary',
				'options' => CinemaPress_WYSIWYG_OPTIONS,
				'visible' => [ 'enable_team', true ]
			]
		)
	);

	add_filter( 'rwmb_outside_conditions', function ( $conditions ) {
		$condition                                                  = array(
			'hidden' => array( 'enable_team', false ),
		);
		$conditions['.rwmb-tab-former, .rwmb-tab-panel-former']     = $condition;
		$conditions['.rwmb-tab-current, .rwmb-tab-panel-current']   = $condition;
		$conditions['.rwmb-tab-honorary, .rwmb-tab-panel-honorary'] = $condition;

		return $conditions;
	} );

	$meta_boxes[] = array(
		'id' => 'cinema-press_screening_settings',

	);


	return $meta_boxes;
} );

add_action( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', function ( $order ) {
	return array(
		"index.php",
		"separator1",
		"edit.php?post_type=movie",
		"edit.php?post_type=screening",
		"edit.php?post_type=screening_location",
		"edit.php?post_type=team_member",
		"edit.php?post_type=cooperation_partner",
		"edit.php?post_type=sponsor",
		"separator2",
		"edit.php",
	);
} );

add_filter( "enter_title_here", function ( $title ) {
	switch ( get_current_screen()->post_type ) {
		case 'screening':
		case 'movie':
			$title = __( 'DO NOT ENTER A VALUE HERE', 'cinema-press' );
			break;
		case 'screening_location':
			$title = __( "Name of the screening venue", 'cinema-press' );
			break;
		case 'team_member':
			$title = __( "Name of the team member", 'cinema-press' );
	}

	return $title;
} );