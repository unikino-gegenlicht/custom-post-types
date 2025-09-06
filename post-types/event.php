<?php

function ggl_post_type_event(): void {
	register_post_type( 'event', [
		'label'               => __( 'Events', 'ggl-post-types' ),
		'labels'              => [
			'menu_name'          => __( 'Events', 'ggl-post-types' ),
			'name_admin_bar'     => __( 'Events', 'ggl-post-types' ),
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
		'supports'            => [ 'thumbnail' ],
		'taxonomies'          => [ 'semester', 'special-program' ],
		'rewrite'             => [
			'with_front' => true,
			'pages'      => false,
		]
	] );
}

function generate_numerical_event_id( $post_id ): void {
	$parent_id = wp_is_post_revision( $post_id );

	if ( false !== $parent_id ) {
		$post_id = $parent_id;
	}
	remove_action( 'save_post_event', 'generate_numerical_event_id' );
	wp_update_post( array(
		'ID'         => $post_id,
		'post_name'  => $post_id,
		'post_title' => ( array_key_exists( 'german_title', $_POST ) && array_key_exists( 'english_title', $_POST ) ) ? $_POST['german_title'] . " (" . $_POST['english_title'] . ")" : "TBA",
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

function ggl_cpt__add_event_program_filter( $post_type ): void {
	if ( $post_type !== 'event' ) {
		return;
	}

	$special_programs = get_terms( [
		"taxonomy"   => "special-program",
		"hide_empty" => true,
		"orderby"    => "name",
		"order"      => "ASC",
	] );
	?>
	<select name="program">
		<option value=""><?= esc_html__( "All Programs", "gegenlicht" ) ?></option>
		<option value="main" <?= selected( "main", @ $_GET["program"], 0 ) ?>><?= esc_html__( "Main Program", "gegenlicht" ) ?></option>
		<?php foreach ( $special_programs as $special_program ): ?>
			<option value="<?= $special_program->slug ?>" <?= selected( $special_program->slug, @ $_GET["program"], 0 ) ?>><?= $special_program->name ?></option>
		<?php endforeach; ?>
	</select>
	<?php

}

function ggl_cpt__apply_event_program_filter( WP_Query $query ) {
	$cs = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	// make sure we are on the right admin page
	if ( ! is_admin() || empty( $cs->post_type ) || $cs->post_type != 'event' || $cs->id != 'edit-event' ) {
		return;
	}

	if ( @ $_GET['program'] != - 1 && @ $_GET['program'] !== "" && @ $_GET['program'] !== null ) {
		$selected_program = @ $_GET['program'] ?: null;
		if ( $selected_program === "main" ) {
			$query->set( "meta_query", [
				[
					"key"   => "program_type",
					"value" => "main",
				]
			] );
		} else {
			$query->set( "meta_query", [
				"key"   => "program_type",
				"value" => "special_program",
			] );
			$query->set( "tax_query", [
				[
					"taxonomy" => "special-program",
					"terms"    => $selected_program,
					"field"    => "slug",
				]
			] );
		}
	}

}


function event_extended_info_meta_boxes( $meta_boxes ) {
	$meta_boxes[] = [
		'title'      => esc_html__( 'Event Information', 'ggl-post-types' ),
		'id'         => 'movie_information',
		'context'    => 'form_top',
		'style'      => 'seamless',
		'post_types' => [ 'event' ],
		'autosave'   => true,
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
				'required' => true
			],
			[
				'type'     => 'text',
				'name'     => esc_html__( 'English Title', 'ggl-post-types' ),
				'id'       => 'english_title',
				'desc'     => esc_html__( 'Please enter the English title of the event here', 'ggl-post-types' ),
				'required' => true
			],
			[
				'type'     => 'number',
				'name'     => esc_html__( 'Duration', 'ggl-post-types' ),
				'id'       => 'duration',
				'desc'     => esc_html__( 'The event\'s duration in minutes', 'ggl-post-types' ),
				'std'      => 90,
				'step'     => 1,
				'min'      => 0,
				'required' => true
			],
			[
				'type'     => 'select_advanced',
				'name'     => esc_html__( 'Event Language', 'ggl-post-types' ),
				'id'       => 'language',
				'std'      => 'eng',
				'options'  => generate_language_mapping(),
				'required' => false,
			],
			[
				'type'      => 'switch',
				'name'      => esc_html__( 'Has minimal attendee age', 'ggl-post-types' ),
				'id'        => 'age_restricted',
				'std'       => false,
				'on_label'  => esc_html__( 'Yes', 'ggl-post-types' ),
				'off_label' => esc_html__( 'No', 'ggl-post-types' ),
			],
			[
				'type'     => 'number',
				'name'     => esc_html__( 'Minimal Attendee Age', 'ggl-post-types' ),
				'id'       => 'minimal_age',
				'std'      => 16,
				'step'     => 1,
				'min'      => 0,
				'visible' => ["age_restricted"]
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
				]
			],
			[
				'type'        => 'post',
				'name'        => esc_html__( 'Team Member', 'ggl-post-types' ),
				'id'          => 'team_member_id',
				'post_type'   => 'team-member',
				'field_type'  => 'select_advanced',
				'add_new'     => true,
				'placeholder' => esc_html__( 'Select a Team Member', 'ggl-post-types' ),
				'query_args'  => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1
				],
				'visible'     => [ 'selected_by', '=', 'member' ]
			],
			[
				'type'        => 'post',
				'name'        => esc_html__( 'Cooperation Partner', 'ggl-post-types' ),
				'id'          => 'cooperation_partner_id',
				'post_type'   => 'cooperation-partner',
				'field_type'  => 'select_advanced',
				'placeholder' => esc_html__( 'Select a Cooperation Partner', 'ggl-post-types' ),
				'add_new'     => true,
				'query_args'  => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1
				],
				'visible'     => [ 'selected_by', '=', 'cooperation' ]
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
				'visible'     => [ 'program_type', '=', 'special_program' ]
			],
		],
	];

	return $meta_boxes;
}

function event_screening_info_meta_boxes( $meta_boxes ) {
	$meta_boxes[] = [
		'title'      => esc_html__( 'Screening Information', 'ggl-post-types' ),
		'id'         => 'screening_information',
		'context'    => 'side',
		'post_types' => [ 'event' ],
		'autosave'   => true,
		'fields'     => [
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
				'add_new'     => true
			],
			[
				'type'       => 'datetime',
				'name'       => esc_html__( 'Date and Time', 'ggl-post-types' ),
				'id'         => 'screening_date',
				'timestamp'  => true,
				'js_options' => [
					'dateFormat' => 'dd.mm.yy',
				],
				'required'   => true
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
				'ajax'       => true,
				'add_new'    => false,
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
				'std'      => 'paid',
			],
			[
				'type'    => 'number',
				'name'    => esc_html__( 'Fee', 'ggl-post-types' ),
				'id'      => 'admission_fee',
				'std'     => 3,
				'min'     => 0,
				'step'    => 0.01,
				'visible' => [ 'admission_type', "=", "paid" ]
			],
		],
	];

	return $meta_boxes;
}


function event_additional_information_box( $meta_boxes ) {
	$meta_boxes[] = [
		'title'      => esc_html__( "Why it's worth seeing", 'ggl-post-types' ),
		'id'         => 'additional_information',
		'context'    => 'normal',
		'post_types' => [ 'event' ],
		'style'      => 'seamless',
		'autosave'   => true,
		'fields'     => [
			[
				'type' => 'heading',
				'name' => esc_html__( "Event Summary", 'ggl-post-types' ),
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'summary',
				'required'              => true,
				'add_to_wpseo_analysis' => true,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS
			],
			[
				'type' => 'heading',
				'name' => esc_html__( "Why it's worth attending", 'ggl-post-types' ),
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'worth_to_see',
				'required'              => true,
				'add_to_wpseo_analysis' => true,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS
			],
			[
				'type' => 'heading',
				'name' => esc_html__( "Notices", 'ggl-post-types' ),
			],
			[
				'type' => 'checkbox',
				'name' => esc_html__( 'Show Notice', 'ggl-post-types' ),
				'id'   => 'show_content_notice',
				'std'  => 0,
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'content_notice',
				'required'              => false,
				'add_to_wpseo_analysis' => false,
				'dfw'                   => false,
				'visible'               => [ 'show_content_notice', true ],
				'desc'                  => esc_html__( 'The content notice will be displayed above the event summary', 'ggl-post-types' ),
				'options'               => GGL_CPT__WYSIWYG_OPTIONS
			],
		],
	];

	return $meta_boxes;
}