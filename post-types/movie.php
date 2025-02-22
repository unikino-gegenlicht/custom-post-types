<?php

require_once (dirname(__FILE__) ."../language-mapping.php");
function ggl_post_type_movie(): void
{
	register_post_type(
		'movie',
		[
			'label' => __('Movies'),
			'labels' => [
				'menu_name' => __('Movies'),
				'name_admin_bar' => __('Movie'),
				'singular_name' => __('Movie'),
				'add_new_item' => __('Add Movie'),
				'add_new' => __('Add Movie'),
				'edit_item' => __('Edit Movie'),
				'view_item' => __('Show Movie'),
				'search_items' => __('Search Movies'),
				'not_found' => __('No Movies found'),
				'not_found_in_trash' => __('No Movies found in Trash'),
				'parent_item_colon' => __('Parent Movies:'),
				'all_items' => __('All Movies'),
				'archives' => __('Old Movies'),
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
			'menu_position' => 2,
			'menu_icon' => 'dashicons-editor-video',
			'supports' => ['title', 'editor', 'thumbnail'],
			'taxonomies' => [],
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
		'title' => esc_html__('Extended Movie Information', 'ggl-post-types'),
		'id' => 'extended_movie_information',
		'context' => 'after_title',
		'post_types' => ['movie'],
		'autosave' => true,
		'fields' => [
			[
				'type' => 'text',
				'name' => esc_html__('German Title', 'ggl-post-types'),
				'id' => $prefix . 'german_title',
				'desc' => esc_html__('Please enter the German title of the movie here', 'ggl-post-types'),
			],
			[
				'type' => 'text',
				'name' => esc_html__('Original Title', 'ggl-post-types'),
				'id' => $prefix . 'original_title',
				'desc' => esc_html__('Please enter the original title here', 'ggl-post-types'),
			],
			[
				'type' => 'checkbox',
				'name' => esc_html__('Promotional License', 'ggl-post-types'),
				'id' => $prefix . 'promotional_license',
				'desc' => esc_html__('Please uncheck this field if we didn\'t get a promotional license for the movie', 'ggl-post-types'),
				'std' => true,
			],
			[
				'type' => 'select',
				'name' => esc_html__('Age Rating', 'ggl-post-types'),
				'id' => $prefix . 'age_rating',
				'desc' => esc_html__('The official age rating set by the FSK (https://www.fsk.de/?seitid=70&tid=70)', 'ggl-post-types'),
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
			],
			[
				'type' => 'number',
				'name' => esc_html__('Running Time', 'ggl-post-types'),
				'id' => $prefix . 'running_time',
				'desc' => esc_html__('The movie\'s running time in minutes', 'ggl-post-types'),
				'std' => 90,
				'step' => 1,
			],
			[
				'type' => 'select',
				'name' => esc_html__('Audio Language', 'ggl-post-types'),
				'id' => $prefix . 'audio_language',
				'desc' => esc_html__('The ISO 639-3 language for the spoken language in the movie', 'ggl-post-types'),
				'std' => 'eng',
				'options' => GGL_ISO693_3
			],
			[
				'type' => 'select',
				'name' => esc_html__('Subtitle Language', 'ggl-post-types'),
				'id' => $prefix . 'subtitle_language',
				'desc' => esc_html__('The ISO 639-3 language code for the subtitle\'s language', 'ggl-post-types'),
				'std' => 'eng',
				'options' => GGL_ISO693_3

			],
		],
	];

	return $meta_boxes;
}

function movie_screening_info_meta_boxes($meta_boxes)
{
	$prefix = 'movie_';

	$meta_boxes[] = [
		'title' => esc_html__('Screening Information', 'ggl-post-types'),
		'id' => 'screening_information',
		'context' => 'normal',
		'post_types' => ['movie'],
		'autosave' => true,
		'fields' => [
			[
				'type' => 'datetime',
				'name' => esc_html__('Screening Date', 'ggl-post-types'),
				'id' => $prefix . 'screening_date',
				'timestamp' => true,
				'js_options' => [
					'formatDate' => 'dd.mm.yy',
				],
			],
			[
				'type' => 'text',
				'name' => esc_html__('Screening Location', 'ggl-post-types'),
				'id' => $prefix . 'screening_location',
				'desc' => esc_html__('Screening locations name (or address if needed)', 'ggl-post-types'),
				'std' => __('Bühne 1 @ UNIKUM Oldenburg'),
			],
		],
	];

	return $meta_boxes;
}