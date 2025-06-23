<?php
//require_once '../language-mapping.php';
$metaboxPrefix = "event_";

function ggl_post_type_event(): void {
	register_post_type(
		'event',
		[
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
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'can_export'          => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'delete_with_user'    => false,
			'menu_position'       => 7,
			'menu_icon'           => 'dashicons-schedule',
			'supports'            => [ 'editor', 'thumbnail' ],
			'taxonomies'          => [ 'semester', 'special-program' ],
			'rewrite'             => [
				'with_front' => true,
				'pages'      => false,
			]
		]
	);
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
		'post_title' => $_POST['event_english_title'],
	) );
	add_action( 'save_post_event', 'generate_numerical_event_id' );
}

function event_extended_info_meta_boxes( $meta_boxes ) {
	global $metaboxPrefix;
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
				'id'       => $metaboxPrefix . 'german_title',
				'desc'     => esc_html__( 'Please enter the German title of the event here', 'ggl-post-types' ),
				'required' => true
			],
			[
				'type'     => 'text',
				'name'     => esc_html__( 'English Title', 'ggl-post-types' ),
				'id'       => $metaboxPrefix . 'english_title',
				'desc'     => esc_html__( 'Please enter the English title of the event here', 'ggl-post-types' ),
				'required' => true
			],
			[
				'type'    => 'radio',
				'name'    => esc_html__( 'License Type', 'ggl-post-types' ),
				'id'      => $metaboxPrefix . 'license_type',
				'inline'  => true,
				'options' => [
					'full' => esc_html__( 'Advertisement License', 'ggl-post-types' ),
					'pool' => esc_html__( 'Pool License', 'ggl-post-types' ),
					'none' => esc_html__( 'No License', 'ggl-post-types' ),
					'n/a'  => esc_html__( 'Not Applicable', 'ggl-post-types' )
				],
				'desc'    => __( 'Please select the best fitting category for this event. If you are unsure, read the <a href="https://wiki.gegenlicht.net/doku.php/lexikon/lizensierung" target="_blank">following entry</a>.', 'ggl-post-types' )
			],
			[
				'type'     => 'select',
				'name'     => esc_html__( 'Age Rating', 'ggl-post-types' ),
				'id'       => $metaboxPrefix . 'age_rating',
				'desc'     => esc_html__( 'The official age rating set by the FSK (https://www.fsk.de/?seitid=70&tid=70)', 'ggl-post-types' ),
				'options'  => [
					- 3 => esc_html__( 'Not Applicable', 'ggl-post-types' ),
					- 2 => esc_html__( 'Unknown', 'ggl-post-types' ),
					- 1 => esc_html__( 'Not rated', 'ggl-post-types' ),
					0   => esc_html__( 'FSK 0', 'ggl-post-types' ),
					6   => esc_html__( 'FSK 6', 'ggl-post-types' ),
					12  => esc_html__( 'FSK 12', 'ggl-post-types' ),
					16  => esc_html__( 'FSK 16', 'ggl-post-types' ),
					18  => esc_html__( 'FSK 18', 'ggl-post-types' ),
				],
				'std'      => - 2,
				'required' => true
			],
			[
				'type'     => 'number',
				'name'     => esc_html__( 'Duration', 'ggl-post-types' ),
				'id'       => $metaboxPrefix . 'duration',
				'desc'     => esc_html__( 'The event\'s duration in minutes', 'ggl-post-types' ),
				'std'      => 90,
				'step'     => 1,
				'min'      => 0,
				'required' => true
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Selected by', 'ggl-post-types' ),
				'id'       => $metaboxPrefix . 'selected_by_type',
				'inline'   => true,
				'required' => true,
				'options'  => [
					'member'      => esc_html__( 'Team Member', 'ggl-post-types' ),
					'cooperation' => esc_html__( 'Cooperation Partner', 'ggl-post-types' ),
					'hidden'      => esc_html__( 'Don\'t show', 'ggl-post-types' )
				]
			],
			[
				'type'        => 'post',
				'name'        => esc_html__( 'Team Member', 'ggl-post-types' ),
				'id'          => $metaboxPrefix . 'member_id',
				'post_type'   => 'team-member',
				'field_type'  => 'select_advanced',
				'add_new'     => true,
				'placeholder' => esc_html__( 'Select a Team Member', 'ggl-post-types' ),
				'query_args'  => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1
				],
				'visible'     => [ $metaboxPrefix . 'selected_by_type', '=', 'member' ]
			],
			[
				'type'        => 'post',
				'name'        => esc_html__( 'Cooperation Partner', 'ggl-post-types' ),
				'id'          => $metaboxPrefix . 'cooperation_partner_id',
				'post_type'   => 'cooperation-partner',
				'field_type'  => 'select_advanced',
				'placeholder' => esc_html__( 'Select a Cooperation Partner', 'ggl-post-types' ),
				'add_new'     => true,
				'query_args'  => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1
				],
				'visible'     => [ $metaboxPrefix . 'selected_by_type', '=', 'cooperation' ]
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Program Type', 'ggl-post-types' ),
				'id'       => $metaboxPrefix . 'program_type',
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
				'id'          => $metaboxPrefix . 'special_program',
				'taxonomy'    => 'special-program',
				'required'    => false,
				'field_type'  => 'select_advanced',
				'add_new'     => true,
				'query_args'  => [
					'number' => 10,
				],
				'ajax'        => false,
				'visible'     => [ $metaboxPrefix . 'program_type', '=', 'special_program' ]
			],
		],
	];

	return $meta_boxes;
}

function event_sound_information_meta_boxes( $meta_boxes ): mixed {
	global $metaboxPrefix, $GGL_ISO693_3;

	$meta_boxes[] = [
		'title'      => esc_html__( 'Audio and Subtitles', 'ggl-post-types' ),
		'id'         => 'audio_and_subtitle_information',
		'context'    => 'side',
		'post_types' => [ 'event' ],
		'autosave'   => true,
		'fields'     => [
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Audio Type', 'ggl-post-types' ),
				'id'       => $metaboxPrefix . 'audio_type',
				'inline'   => true,
				'required' => true,
				'options'  => [
					'original'        => esc_html__( 'Original', 'ggl-post-types' ),
					'synchronization' => esc_html__( 'Synchronization', 'ggl-post-types' ),
				],
			],
			[
				'type'     => 'select_advanced',
				'name'     => esc_html__( 'Audio Language', 'ggl-post-types' ),
				'id'       => $metaboxPrefix . 'audio_language',
				'std'      => 'eng',
				'options'  => generate_language_mapping(),
				'required' => false,
			],
			[
				'type'     => 'select_advanced',
				'name'     => esc_html__( 'Subtitle Language', 'ggl-post-types' ),
				'id'       => $metaboxPrefix . 'subtitle_language',
				'std'      => 'deu',
				'options'  => generate_language_mapping(),
				'required' => true
			],
		]
	];

	return $meta_boxes;
}

function event_screening_info_meta_boxes( $meta_boxes ) {
	global $metaboxPrefix;

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
				'id'          => $metaboxPrefix . 'semester',
				'placeholder' => esc_html__( 'Select a Semester', 'ggl-post-types' ),
				'taxonomy'    => 'semester',
				'required'    => true,
				'field_type'  => 'select_advanced',
				'query_args'  => [
					'number' => 10,
				],
				'add_new'     => true
			],
			[
				'type'       => 'datetime',
				'name'       => esc_html__( 'Date and Time', 'ggl-post-types' ),
				'id'         => $metaboxPrefix . 'screening_date',
				'timestamp'  => true,
				'js_options' => [
					'dateFormat' => 'dd.mm.yy',
				],
				'required'   => true
			],
			[
				'type'     => 'text',
				'name'     => esc_html__( 'Location', 'ggl-post-types' ),
				'id'       => $metaboxPrefix . 'screening_location',
				'desc'     => esc_html__( 'Screening locations name (or address if needed)', 'ggl-post-types' ),
				'std'      => esc_html__( 'Stage 1 @ UNIKUM Oldenburg', 'ggl-post-types' ),
				'required' => true
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Addmission Type', 'ggl-post-types' ),
				'id'       => $metaboxPrefix . 'addmission_type',
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
				'id'      => $metaboxPrefix . 'admission_fee',
				'std'     => 3,
				'min'     => 0,
				'step'    => 0.01,
				'visible' => [ $metaboxPrefix . 'addmission_type', "=", "paid" ]
			],
		],
	];

	return $meta_boxes;
}



function event_additional_information_box( $meta_boxes ) {
	global $metaboxPrefix;

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
				'title'      => esc_html__( "Why it's worth seeing", 'ggl-post-types' ),
			],
			[
				'type'     => 'wysiwyg',
				'id'       => $metaboxPrefix . 'worth_seeing_since',
				'required' => false
			]
		],
	];

	return $meta_boxes;
}