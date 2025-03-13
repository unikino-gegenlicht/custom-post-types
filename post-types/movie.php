<?php


function ggl_post_type_movie(): void
{
	register_post_type(
		'movie',
		[
			'label' => __('Movies', 'ggl-post-types'),
			'labels' => [
				'menu_name' => __('Movies', 'ggl-post-types'),
				'name_admin_bar' => __('Movie', 'ggl-post-types'),
				'singular_name' => __('Movie', 'ggl-post-types'),
				'add_new_item' => __('Add Movie', 'ggl-post-types'),
				'add_new' => __('Add Movie', 'ggl-post-types'),
				'edit_item' => __('Edit Movie', 'ggl-post-types'),
				'view_item' => __('Show Movie', 'ggl-post-types'),
				'search_items' => __('Search Movies', 'ggl-post-types'),
				'not_found' => __('No Movies found', 'ggl-post-types'),
				'not_found_in_trash' => __('No Movies found in Trash', 'ggl-post-types'),
				'all_items' => __('All Movies', 'ggl-post-types'),
				'archives' => __('Old Movies', 'ggl-post-types'),
			],
			'public' => true,
			'has_archive' => 'archiv',
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'can_export' => true,
			'show_ui' => true, // implies show_in_menu, show_in_nav_menus, show_in_admin_bar
			'show_in_rest' => true,
			'delete_with_user' => false,
			'menu_position' => 6,
			'menu_icon' => 'dashicons-editor-video',
			'supports' => ['title', 'editor', 'thumbnail'],
			'taxonomies' => ['semester', 'special-program', 'director', 'actor', 'genre'],
			'rewrite' => [
				'with_front' => true,
				'pages' => false, 
			]
		]
	);
}

function movie_check_name_leaking($data, $postarr)
{
	if ($data['post_type'] !== 'movie') {
		return $data;
	}

	if (!in_array($data['post_status'], array('draft', 'pending', 'auto-draft'))) {
		return $data;
	}

	return $data;
}

function movie_extended_info_meta_boxes($meta_boxes)
{
	$prefix = 'movie_';

	$meta_boxes[] = [
		'title' => esc_html__('Movie Information', 'ggl-post-types'),
		'id' => 'movie_information',
		'context' => 'before_permalink',
		'style' => 'seamless',
		'post_types' => ['movie'],
		'autosave' => true,
		'fields' => [
			[
				'type' => 'heading',
				'name' => esc_html__('Movie Information', 'ggl-post-types'),
			],
			[
				'type' => 'text',
				'name' => esc_html__('German Title', 'ggl-post-types'),
				'id' => $prefix . 'german_title',
				'desc' => esc_html__('Please enter the German title of the movie here', 'ggl-post-types'),
				'required' => true
			],
			[
				'type' => 'text',
				'name' => esc_html__('Original Title', 'ggl-post-types'),
				'id' => $prefix . 'original_title',
				'desc' => esc_html__('Please enter the original title here', 'ggl-post-types'),
				'required' => true
			],
			[
				'type' => 'select_advanced',
				'name' => __('Country/Countries of Origin', 'ggl-post-types'),
				'id' => $prefix . 'country',
				'options' => generate_country_mapping(),
				'multiple' => true,
				'required' => true,
			],
			
			[
				'type' => 'number',
				'name' => esc_html__('Release Year', 'ggl-post-types'),
				'id' => $prefix . 'released_in',
				'required' => true,
				'std' => 1970,
				'step' => 1,
				'min' => 0,
			],
			[
				'type' => 'number',
				'name' => esc_html__('Running Time', 'ggl-post-types'),
				'id' => $prefix . 'running_time',
				'desc' => esc_html__('The movie\'s running time in minutes', 'ggl-post-types'),
				'std' => 90,
				'step' => 1,
				'min' => 0,
				'required' => true
			],
			[
				'type' => 'taxonomy',
				'name' => esc_html__('Directed by', 'ggl-post-types'),
				'id' => $prefix . 'director',
				'taxonomy' => 'director',
				'required' => true,
				'field_type' => 'select_advanced',
				'placeholder' => __('Select Director', 'ggl-post-types'),
				'add_new' => true,
				'query_args' => [
					'number' => 10,
				],
				'ajax' => false
			],
			[
				'type' => 'taxonomy',
				'name' => esc_html__('Starring', 'ggl-post-types'),
				'id' => $prefix . 'actors',
				'taxonomy' => 'actor',
				'required' => true,
				'field_type' => 'select_advanced',
				'add_new' => true,
				'multiple' => true,
				'placeholder' => __('Select Actors', 'ggl-post-types'),
				'query_args' => [
					'number' => 10,
				],
				'js_options' => [
					'maximumSelectionLength' => 3
				],
				'ajax' => false
			],
			[
				'type' => 'radio',
				'name' => esc_html__('Selected by', 'ggl-post-types'),
				'id' => $prefix . 'selected_by_type',
				'inline' => true,
				'required' => true,
				'options' => [
					'member' => esc_html__('Team Member', 'ggl-post-types'),
					'cooperation' => esc_html__('Cooperation Partner', 'ggl-post-types'),
					'hidden' => esc_html__('Don\'t show', 'ggl-post-types')
				]
			],
			[
				'type' => 'post',
				'name' => esc_html__('Team Member', 'ggl-post-types'),
				'id' => $prefix . 'member_id',
				'post_type' => 'team-member',
				'field_type' => 'select_advanced',
				'add_new' => true,
				'placeholder' => esc_html__('Select a Team Member', 'ggl-post-types'),
				'query_args' => [
					'post_status' => 'publish',
					'posts_per_page' => -1
				],
				'visible' => [$prefix . 'selected_by_type', '=', 'member'],
				'ajax' => false
			],
			[
				'type' => 'post',
				'name' => esc_html__('Cooperation Partner', 'ggl-post-types'),
				'id' => $prefix . 'cooperation_partner_id',
				'post_type' => 'cooperation-partner',
				'field_type' => 'select_advanced',
				'placeholder' => esc_html__('Select a Cooperation Partner', 'ggl-post-types'),
				'add_new' => true,
				'query_args' => [
					'post_status' => 'publish',
					'posts_per_page' => -1
				],
				'visible' => [$prefix . 'selected_by_type', '=', 'cooperation'],
				'ajax' => false
			],
			[
				'type' => 'radio',
				'name' => esc_html__('Program Type', 'ggl-post-types'),
				'id' => $prefix . 'program_type',
				'inline' => true,
				'required' => true,
				'options' => [
					'main' => esc_html__('Main Program', 'ggl-post-types'),
					'special_program' => esc_html__('Special Program', 'ggl-post-types'),
				]
			],
			[
				'type' => 'taxonomy',
				'name' => esc_html__('Special Program', 'ggl-post-types'),
				'placeholder' => esc_html__('Select a Special Program', 'ggl-post-types'),
				'id' => $prefix . 'special_program',
				'taxonomy' => 'special-program',
				'required' => false,
				'field_type' => 'select_advanced',
				'add_new' => true,
				'query_args' => [
					'number' => 10,
				],
				'visible' => [$prefix . 'program_type', '=', 'special_program'],
				'ajax' => false
			],
		],
	];

	return $meta_boxes;
}

function movie_licensing_and_age_rating_meta_boxes($meta_boxes): mixed {
	$prefix = 'movie_';

	$meta_boxes[] = [
		'title' => esc_html__('Licensing and Age Rating', 'ggl-post-types'),
		'id' => 'licensing_information',
		'context' => 'side',
		'post_types' => ['movie'],
		'autosave' => true,
		'fields' => [
			[
				'type' => 'radio',
				'name' => esc_html__('License Type', 'ggl-post-types'),
				'id' => $prefix . 'license_type',
				'inline' => true,
				'required' => true,
				'options' => [
					'full' => esc_html__('Advertisement License', 'ggl-post-types'),
					'pool' => esc_html__('Pool License', 'ggl-post-types'),
					'none' => esc_html__('No License', 'ggl-post-types'),
				]
			],
			[
				'type' => 'select',
				'name' => esc_html__('Age Rating', 'ggl-post-types'),
				'id' => $prefix . 'age_rating',
				'desc' => '<a href="https://www.fsk.de/?seitid=70&tid=70" target="_blank">'. __('FSK Title Lookup', 'ggl-post-types') . '</a>',
				'options' => [
					-2 => esc_html__('unknown', 'ggl-post-types'),
					-1 => esc_html__('Not rated', 'ggl-post-types'),
					0 => esc_html__('FSK 0', 'ggl-post-types'),
					6 => esc_html__('FSK 6', 'ggl-post-types'),
					12 => esc_html__('FSK 12', 'ggl-post-types'),
					16 => esc_html__('FSK 16', 'ggl-post-types'),
					18 => esc_html__('FSK 18', 'ggl-post-types'),
				],
				'std' => -2,
				'required' => true
			],
		]
	];

	return $meta_boxes;
}

function movie_sound_information_meta_boxes($meta_boxes): mixed
{
	$prefix = 'movie_';

	$meta_boxes[] = [
		'title' => esc_html__('Audio and Subtitles', 'ggl-post-types'),
		'id' => 'audio_and_subtitle_information',
		'context' => 'side',
		'post_types' => ['movie'],
		'autosave' => true,
		'fields' => [
			[
				'type' => 'radio',
				'name' => esc_html__('Audio Type', 'ggl-post-types'),
				'id' => $prefix . 'audio_type',
				'inline' => true,
				'required' => true,
				'options' => [
					'original' => esc_html__('Original', 'ggl-post-types'),
					'synchronization' => esc_html__('Synchronization', 'ggl-post-types'),
				],
			],
			[
				'type' => 'select_advanced',
				'name' => esc_html__('Audio Language', 'ggl-post-types'),
				'id' => $prefix . 'audio_language',
				'std' => 'eng',
				'options' => generate_language_mapping(),
				'required' => true,
			],
			[
				'type' => 'select_advanced',
				'name' => esc_html__('Subtitle Language', 'ggl-post-types'),
				'id' => $prefix . 'subtitle_language',
				'std' => 'deu',
				'options' => generate_language_mapping(),
				'required' => true,
			],
		]
	];
	return $meta_boxes;
}

function movie_screening_info_meta_boxes($meta_boxes)
{
	$prefix = 'movie_';

	$meta_boxes[] = [
		'title' => esc_html__('Screening Information', 'ggl-post-types'),
		'id' => 'screening_information',
		'context' => 'side',
		'post_types' => ['movie'],
		'autosave' => true,
		'fields' => [
			[
				'type' => 'taxonomy',
				'name' => esc_html__('Semester', 'ggl-post-type'),
				'id' => $prefix . 'semester',
				'placeholder' => esc_html__('Select a Semester', 'ggl-post-types'),
				'taxonomy' => 'semester',
				'required' => true,
				'field_type' => 'select_advanced',
				'query_args' => [
					'number' => 10,
				],
				'add_new' => true,
				'ajax' => false
			],
			[
				'type' => 'datetime',
				'name' => esc_html__('Date and Time', 'ggl-post-types'),
				'id' => $prefix . 'screening_date',
				'timestamp' => true,
				'js_options' => [
					'dateFormat' => 'dd.mm.yy',
				],
				'required' => true
			],
			[
				'type' => 'text',
				'name' => esc_html__('Location', 'ggl-post-types'),
				'id' => $prefix . 'screening_location',
				'desc' => esc_html__('Screening locations name (or address if needed)', 'ggl-post-types'),
				'std' => esc_html__('Stage 1 @ UNIKUM Oldenburg', 'ggl-post-types'),
				'required' => true
			],
			[
				'type' => 'radio',
				'name' => esc_html__('Addmission Type', 'ggl-post-types'),
				'id' => $prefix . 'addmission_type',
				'inline' => true,
				'required' => true,
				'options' => [
					'free' => esc_html__('Free', 'ggl-post-types'),
					'donation' => esc_html__('Donation', 'ggl-post-types'),
					'paid' => esc_html__('Paid', 'ggl-post-types')
				],
				'std' => 'paid',
			],
			[
				'type' => 'number',
				'name' => esc_html__('Fee', 'ggl-post-types'),
				'id' => $prefix . 'admission_fee',
				'std' => 3,
				'min' => 0,
				'step' => 0.01,
				'visible' => [$prefix . 'addmission_type', "=", "paid"]
			],
		],
	];

	return $meta_boxes;
}

function movie_additional_information_box($meta_boxes)
{
	$prefix = 'movie_';

	$meta_boxes[] = [
		'title' => esc_html__('Additional Information', 'ggl-post-types'),
		'id' => 'additional_information',
		'context' => 'normal',
		'post_types' => ['movie'],
		'style' => 'seamless',
		'autosave' => true,
		'fields' => [
			[
				'type' => 'heading',
				'name' => esc_html__('Additional Information', 'ggl-post-types'),
			],
			[
				'type' => 'wysiwyg',
				'desc' => esc_html__('This text will be shown underneath the movies description text', 'ggl-post-types'),
				'id' => $prefix . 'additional_information',
				'required' => false,
				'add_to_wpseo_analysis' => true
			]
		],
	];

	return $meta_boxes;
}

function movie_short_movie_box($meta_boxes)
{
	$prefix = 'movie_';

	$meta_boxes[] = [
		'title' => esc_html__('Short Movie', 'ggl-post-types'),
		'id' => 'short_movie',
		'context' => 'side',
		'post_types' => ['movie'],
		'autosave' => true,
		'fields' => [
			[
				'type' => 'radio',
				'name' => esc_html__('Advertise Short Movie', 'ggl-post-types'),
				'id' => $prefix . 'short_movie_screened',
				'required' => true,
				'options' => [
					'yes' => esc_html__('Yes', 'ggl-post-types'),
					'no' => esc_html__('No', 'ggl-post-types'),
				],
				'std' => 'yes',
			],
			[
				'type' => 'text',
				'name' => esc_html__('Title', 'ggl-post-types'),
				'id' => $prefix . 'short_movie_title',
				'visible' => [$prefix . 'short_movie_screened', '=', 'yes']
			],
			[
				'type' => 'text',
				'name' => esc_html__('Directed by', 'ggl-post-types'),
				'id' => $prefix . 'short_movie_directed_by',
				'visible' => [$prefix . 'short_movie_screened', '=', 'yes']

			],
			[
				'type' => 'text',
				'name' => esc_html__('Country', 'ggl-post-types'),
				'id' => $prefix . 'short_movie_country',
				'visible' => [$prefix . 'short_movie_screened', '=', 'yes']

			],
			[
				'type' => 'number',
				'name' => esc_html__('Running Time', 'ggl-post-types'),
				'id' => $prefix . 'short_movie_running_time',
				'desc' => esc_html__('The short\'s running time in minutes', 'ggl-post-types'),
				'std' => 5,
				'step' => 1,
				'min' => 0,
				'visible' => [$prefix . 'short_movie_screened', '=', 'yes']
			],
			[
				'type' => 'number',
				'name' => esc_html__('Release Year', 'ggl-post-types'),
				'id' => $prefix . 'short_movie_release_year',
				'std' => 1970,
				'step' => 1,
				'min' => 0,
				'visible' => [$prefix . 'short_movie_screened', '=', 'yes']
			]
		],
	];

	return $meta_boxes;
}