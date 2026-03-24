<?php

function ggl_post_type_event(): void {
	register_post_type( 'event', [
		'label'               => __( 'Events', 'ggl-post-types' ),
		'labels'              => [
			'menu_name'          => __( 'Events', 'ggl-post-types' ),
			'name_admin_bar'     => __( 'Event', 'ggl-post-types' ),
			'singular_name'      => __( 'Event', 'ggl-post-types' ),
			'add_new_item'       => __( 'Add Event', 'ggl-post-types' ),
			'add_new'            => __( 'Add Event', 'ggl-post-types' ),
			'edit_item'          => __( 'Edit Event', 'ggl-post-types' ),
			'view_item'          => __( 'Show Event', 'ggl-post-types' ),
			'search_items'       => __( 'Search Event', 'ggl-post-types' ),
			'not_found'          => __( 'No Events found', 'ggl-post-types' ),
			'not_found_in_trash' => __( 'No Events found in Trash', 'ggl-post-types' ),
			'all_items'          => __( 'All Events', 'ggl-post-types' ),
		],
		'public'              => true,
		'has_archive'         => 'event-archive',
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'can_export'          => true,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'delete_with_user'    => false,
		'menu_position'       => 6,
		'menu_icon'           => 'dashicons-schedule',
		'supports'            => [ 'thumbnail', 'revisions' ],
		'taxonomies'          => [ 'semester', 'special-program' ],
		'rewrite'             => [
			'with_front' => true,
			'pages'      => false,
		]
	] );
}

add_filter( 'manage_event_posts_columns', function( $columns ) {
	$columns['title'] = __("German Title", "ggl-post-types");
	return $columns;
} );

function generate_numerical_event_id( $post_id ): void {
	$parent_id = wp_is_post_revision( $post_id );

	if ( false !== $parent_id ) {
		$post_id = $parent_id;
	}

	$post = get_post( $post_id );

	$post_title  = $_POST['german_title'] ?: get_post_meta($post->ID, "german_title", true) ?: null;

	if ( $post->post_name == $post_id && $post->post_title == $post_title ) {
		return;
	}

	remove_action( 'save_post_event', 'generate_numerical_event_id' );
	wp_update_post( array(
		'ID'         => $post_id,
		'post_name'  => $post_id,
		'post_title' => $post_title,
	) );
	add_action( 'save_post_event', 'generate_numerical_event_id' );
}

function ggl_cpt__add_event_semester_filter( $post_type ): void {
	if ( $post_type !== 'event' ) {
		return;
	}

	$semesters = get_terms( [
		"taxonomy"   => "semester",
		"hide_empty" => true,
	] );

	$sortedSemesters = [];
	foreach ( $semesters as $semester ) {
		$startDate              = date_parse_from_format( "d.m.Y", get_term_meta( $semester->term_id, "semester_start", true ) );
		$ts                     = mktime( 0, 0, 0, $startDate['month'], $startDate['day'], $startDate['year'] ) ?: 0;
		$sortedSemesters[ $ts ] = $semester;
	}
	krsort( $sortedSemesters )
	?>
    <select name="semester">
        <option value=""><?= esc_html__( "All Semesters", "gegenlicht" ) ?></option>
		<?php foreach ( $sortedSemesters as $semester ): ?>
            <option value="<?= $semester->slug ?>" <?= selected( $semester->slug, @ $_GET["semester"], 0 ) ?>><?= $semester->name ?></option>
		<?php endforeach; ?>
    </select>
	<?php

}

function ggl_cpt__apply_event_semester_filter( WP_Query $query ) {
	$cs = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	// make sure we are on the right admin page
	if ( ! is_admin() || empty( $cs->post_type ) || $cs->post_type != 'event' || $cs->id != 'edit-event' ) {
		return;
	}

	if ( @ $_GET['semester'] != - 1 && @ $_GET['semester'] !== null && @ $_GET['semester'] !== "" ) {
		$selected_id = @ $_GET['semester'] ?: null;
		$query->set( 'tax_query', array( [ 'taxonomy' => 'semester', 'terms' => $selected_id, 'field' => "slug" ] ) );
	}

}

function event_extended_info_meta_boxes( $meta_boxes ) {
	$meta_boxes[] = [
		'title'      => esc_html__( 'Event Metaboxes', 'ggl-post-types' ),
		'id'         => 'event_meta',
		'context'    => 'form_top',
		'style'      => 'seamless',
		'post_types' => [ 'event' ],
		'autosave'   => true,
		'revision'   => true,
		'tabs'       => [
			"information"      => [
				"label" => __( "Basic Information", "ggl-post-types" ),
				"icon"  => "dashicons-info-outline"
			],
			'youth-protection' => [
				"label" => __( 'Youth Protection', "ggl-post-types" ),
				"icon"  => "dashicons-privacy"
			],
			'screening'        => [
				"label" => __( "Screening Details", "ggl-post-types" ),
				"icon"  => "dashicons-calendar"
			],
			'admission'        => [
				"label" => __( "Admission", "ggl-post-types" ),
				"icon"  => "dashicons-tickets"
			],
			'content-notice'   => [
				"label" => esc_html__( "Content Notice", "ggl-post-types" ),
				"icon"  => "dashicons-warning"
			],
		],
		'fields'     => [
			[
				'type' => 'heading',
				'name' => esc_html__( 'Event Information', 'ggl-post-types' ),
			],
			[
				'type'     => 'text',
				'name'     => esc_html__( 'German Title', 'ggl-post-types' ),
				'id'       => 'german_title',
				'desc'     => esc_html__( 'Please enter the German title of the event here', 'ggl-post-types' ),
				'required' => true,
				'revision' => true,
				'tab'      => 'information',
			],
			[
				'type'          => 'text',
				'name'          => esc_html__( 'English Title', 'ggl-post-types' ),
				'id'            => 'english_title',
				'desc'          => esc_html__( 'Please enter the English title of the event here', 'ggl-post-types' ),
				'required'      => true,
				'revision'      => true,
				'tab'           => 'information',
				'admin_columns' => [
					'position'   => 'after title',
					'link'       => 'none',

					'sort'       => true,
					'searchable' => true,
					'filterable' => false,
                ],
			],
			[
				'type'     => 'number',
				'name'     => esc_html__( 'Duration', 'ggl-post-types' ),
				'id'       => 'duration',
				'desc'     => esc_html__( 'The event\'s duration in minutes', 'ggl-post-types' ),
				'std'      => 90,
				'step'     => 1,
				'min'      => 0,
				'required' => true,
				'revision' => true,
				'tab'      => 'information',
			],
			[
				'type'     => 'select_advanced',
				'name'     => esc_html__( 'Event Language', 'ggl-post-types' ),
				'id'       => 'language',
				'std'      => 'eng',
				'options'  => generate_language_mapping(),
				'required' => false,
				'revision' => true,
				'tab'      => 'information',
			],
			[
				'type'      => 'switch',
				'name'      => esc_html__( 'Has minimal attendee age', 'ggl-post-types' ),
				'id'        => 'age_restricted',
				'std'       => false,
				'on_label'  => esc_html__( 'Yes', 'ggl-post-types' ),
				'off_label' => esc_html__( 'No', 'ggl-post-types' ),
				'revision'  => true,
				"tab"       => "youth-protection",
			],
			[
				'type'     => 'number',
				'name'     => esc_html__( 'Minimal Attendee Age', 'ggl-post-types' ),
				'id'       => 'age_rating',
				'std'      => 18,
				'step'     => 1,
				'min'      => 0,
				'visible'  => [ "age_restricted" ],
				'revision' => true,
				'tab'      => "youth-protection",
			],
			[
				'type'        => 'taxonomy',
				'name'        => esc_html__( 'Semester', 'ggl-post-type' ),
				'id'          => 'semester',
				'placeholder' => esc_html__( 'Select a Semester', 'ggl-post-types' ),
				'taxonomy'    => 'semester',
				'required'    => false,
				'field_type'  => 'select_advanced',
				'query_args'  => [
					'number' => 10,
				],
				'add_new'     => true,
				'revision'    => true,
				'tab'         => 'screening',
				'admin_columns' => [
					'position'   => 'before date',
					'link'       => 'none',
					'sort'       => false,
					'searchable' => false,
					'filterable' => false,
				]
			],
			[
				'type'       => 'datetime',
				'name'       => esc_html__( 'Date and Time', 'ggl-post-types' ),
				'id'         => 'screening_date',
				'timestamp'  => true,
				'js_options' => [
					'dateFormat' => 'dd.mm.yy',
				],
				'required'   => true,
				'revision'   => true,
				'tab'        => 'screening',
				'admin_columns' => [
					'position'   => 'replace date',
					'title' => __("Screening Date", 'ggl-post-types'),
					'link'       => 'none',
					'sort'       => true,
					'searchable' => false,
					'filterable' => false,
				]
			],
			[
				'type'       => 'post',
				'name'       => esc_html__( 'Location', 'ggl-post-types' ),
				'id'         => 'screening_location',
				'desc'       => esc_html__( 'The location the event will take place in', 'ggl-post-types' ),
				'required'   => true,
				'field_type' => 'select_advanced',
				'post_type'  => 'screening-location',
				'query_args' => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1
				],
				'std'        => get_theme_mod( "main_screening_location" ) ?: null,
				'ajax'       => true,
				'add_new'    => false,
				'revision'   => true,
				'tab'        => 'screening',
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Selected by', 'ggl-post-types' ),
				'id'       => 'selected_by',
				'inline'   => true,
				'required' => true,
				'options'  => [
					'member' => esc_html__( 'Team Member', 'ggl-post-types' ),
					'coop'   => esc_html__( 'Cooperation Partner', 'ggl-post-types' ),
					'hidden' => esc_html__( 'Don\'t show', 'ggl-post-types' )
				],
				'revision' => true,
				'tab'      => 'screening',
			],
			[
				'type'        => 'post',
				'name'        => esc_html__( 'Team Member', 'ggl-post-types' ),
				'id'          => 'team_member_id',
				'post_type'   => 'team-member',
				'field_type'  => 'select_advanced',
				'add_new'     => true,
				'multiple'    => true,
				'placeholder' => esc_html__( 'Select a Team Member', 'ggl-post-types' ),
				'query_args'  => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1
				],
				'visible'     => [ 'selected_by', '=', 'member' ],
				'revision'    => true,
				'tab'         => 'screening',
			],
			[
				'type'        => 'post',
				'name'        => esc_html__( 'Cooperation Partner', 'ggl-post-types' ),
				'id'          => 'cooperation_partner_id',
				'post_type'   => 'cooperation-partner',
				'field_type'  => 'select_advanced',
				'placeholder' => esc_html__( 'Select a Cooperation Partner', 'ggl-post-types' ),
				'add_new'     => true,
				'multiple'    => true,
				'query_args'  => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1
				],
				'visible'     => [ 'selected_by', '=', 'coop' ],
				'revision'    => true,
				'tab'         => 'screening',
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Program Type', 'ggl-post-types' ),
				'id'       => 'program_type',
				'inline'   => true,
				'required' => true,
				'options'  => [
					'main'            => esc_html__( 'Main Program', 'ggl-post-types' ),
					'special_program' => esc_html__( 'Special Program', 'ggl-post-types' ),
				],
				'revision' => true,
				'tab'      => 'screening',
				'admin_columns' => [
					'position'   => 'after semester',
					'title' => __("Program Type", 'ggl-post-types'),
					'link'       => 'none',
					'sort'       => false,
					'searchable' => false,
					'filterable' => true,
				]
			],
			[
				'type'        => 'taxonomy',
				'name'        => esc_html__( 'Special Program', 'ggl-post-types' ),
				'placeholder' => esc_html__( 'Select a Special Program', 'ggl-post-types' ),
				'id'          => 'special_program',
				'taxonomy'    => 'special-program',
				'required'    => false,
				'field_type'  => 'select_advanced',
				'add_new'     => true,
				'query_args'  => [
					'number' => 10,
				],
				'ajax'        => false,
				'visible'     => [ 'program_type', '=', 'special_program' ],
				'revision'    => true,
				'tab'         => 'screening',
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Admission Type', 'ggl-post-types' ),
				'id'       => 'admission_type',
				'inline'   => true,
				'required' => true,
				'options'  => [
					'free'     => esc_html__( 'Free', 'ggl-post-types' ),
					'donation' => esc_html__( 'Donation', 'ggl-post-types' ),
					'paid'     => esc_html__( 'Paid', 'ggl-post-types' )
				],
				'std'      => 'free',
				'revision' => true,
				'tab'      => 'admission',
			],
			[
				'type'     => 'number',
				'name'     => esc_html__( 'Fee', 'ggl-post-types' ),
				'id'       => 'admission_fee',
				'std'      => 3,
				'min'      => 0,
				'step'     => 0.01,
				'visible'  => [ 'admission_type', "=", "paid" ],
				'revision' => true,
				'tab'      => 'admission',
			],
			[
				'type'     => 'checkbox',
				'name'     => esc_html__( 'Show Content Notice', 'ggl-post-types' ),
				'id'       => 'show_content_notice',
				'std'      => 0,
				'revision' => true,
				'desc'     => esc_html__( 'If this box is ticked the below entered content notice will be displayed above the text for this movie.', 'ggl-post-types' ),
				'tab'      => 'content-notice'
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'content_notice',
				'required'              => false,
				'add_to_wpseo_analysis' => false,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS,
				'revision'              => true,
				'tab'                   => 'content-notice',
			],
		],
	];

	return $meta_boxes;
}

function event_additional_information_box( $meta_boxes ) {
	$meta_boxes[] = [
		'title'      => esc_html__( "Textfelder", 'ggl-post-types' ),
		'id'         => 'event_text_boxes',
		'context'    => 'normal',
		'post_types' => [ 'event' ],
		'style'      => 'seamless',
		'autosave'   => true,
		'revision'   => true,
		'tabs'       => [
			"summary"         => [ "label" => esc_html__( "Event Summary", 'ggl-post-types' ) ],
			"worth_to_attend" => [ "label" => esc_html__( "Why it's worth attending", 'ggl-post-types' ) ],
		],
		'fields'     => [
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'summary',
				'required'              => true,
				'add_to_wpseo_analysis' => true,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS,
				'revision'              => true,
				'tab'                   => "summary",
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'worth_to_see',
				'required'              => true,
				'add_to_wpseo_analysis' => true,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS,
				'revision'              => true,
				'tab'                   => "worth_to_attend",
			],
		],
	];

	return $meta_boxes;
}

/**
 * Get the urls and media queries for the event image generation
 *
 * @param int|WP_Post $post $post Optional . Post ID or `WP_Post` object.
 *    Defaults to global `$post`
 *
 * @return array
 */
function ggl_get_event_thumbnail_urls( int|WP_Post $post = 0 ): array {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	if ( $post->post_type != "event" ) {
		return [];
	}

	$anonymous_image = rwmb_meta( "event_anonymous_movie_image", [ "object_type" => "setting" ], "ggl_cpt__settings" );

	$show_details = apply_filters( "ggl__show_full_details", false, $post );
	if ( ! $show_details ) {
		return [
			[
				"url"         => $anonymous_image["sizes"]["mobile"]["url"] ?? $anonymous_image["url"],
				"media_query" => "(width <= 768px)"
			],

			[
				"url"         => $anonymous_image["sizes"]["desktop"]["url"] ?? $anonymous_image["url"],
				"media_query" => "(width > 768px)"
			]
		];
	}

	$image_urls   = [];
	$image_urls[] = [
		"url"         => get_the_post_thumbnail_url( $post->ID, "mobile" ) ?? $anonymous_image["sizes"]["mobile"]["url"] ?? $anonymous_image["full_url"],
		"media_query" => "(prefers-reduced-motion: reduce) and (width <= 768px)"
	];
	$image_urls[] = [
		"url"         => get_the_post_thumbnail_url( $post->ID, "desktop" ) ?? $anonymous_image["sizes"]["desktop"]["url"] ?? $anonymous_image["full_url"],
		"media_query" => "(prefers-reduced-motion: reduce) and (width > 768px)"
	];

	$has_animated_image = boolval( get_post_meta( $post->ID, "use_animated_feature_image", true ) );
	if ( ! $has_animated_image ) {
		for ( $i = 0; $i < count( $image_urls ); $i ++ ) {
			$image_urls[ $i ]["media_query"] = trim( str_replace( "(prefers-reduced-motion: reduce) and", "", $image_urls[ $i ]["media_query"] ) );
		}
	} else {
		$mobile_animated_image = rwmb_meta( "portrait_animated_feature_image" );
		$image_urls[]          = [
			"url"         => $mobile_animated_image["full_url"],
			"media_query" => "(width <= 768px)"
		];

		$desktop_animated_image = rwmb_meta( "landscape_animated_feature_image" );
		$image_urls[]           = [
			"url"         => $desktop_animated_image["full_url"],
			"media_query" => "(width > 768px)"
		];
	}

	return $image_urls;
}

/**
 * Output the movie thumbnail
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *   Defaults to global `$post`
 * @param string $classes Optional. The classes for the `<picture>` element.
 *   Defaults to `image movie-image`
 *
 * @return void
 */
function ggl_the_event_thumbnail( int|WP_Post $post = 0, string $classes = "image movie-image" ): void {
	$post = get_post( $post, filter: 'display' );

	if ( $post->post_type != "event" ) {
		return;
	}

	$images = ggl_get_event_thumbnail_urls( $post );
	if ( empty( $images ) ) {
		return;
	}
	$anonymous_image = rwmb_meta( "event_anonymous_movie_image", [ "object_type" => "setting" ], "ggl_cpt__settings" );
	?>
    <picture class="<?= $classes ?>">
		<?php foreach ( $images as $image ) : ?>
            <source media="<?= $image['media_query'] ?>" srcset="<?= $image['url'] ?>"/>
		<?php endforeach; ?>
        <img alt="" width="800" height="1000"
             src="<?= $anonymous_image["full_url"] ?>"/>
    </picture>
	<?php
}