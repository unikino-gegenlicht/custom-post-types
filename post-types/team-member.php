<?php
/*
 * Copyright (c) 2024. Philip Kaufmann, Jan Eike Suchard, Benjamin Witte
 * This file is copyright under the latest version of the EUPL.
 * Please see the LICENSE file for your rights.
 */

function ggl_post_type_team_member(): void
{
	register_post_type(
		'team-member',
		[
			'label' => __('Team Members', 'ggl-post-types'),
			'labels' => [
				'menu_name' => __('Team Members', 'ggl-post-types'),
				'name_admin_bar' => __('Team Member', 'ggl-post-types'),
				'singular_name' => __('Team Member', 'ggl-post-types'),
				'add_new_item' => __('Add Team Member', 'ggl-post-types'),
				'add_new' => __('Add Team Member', 'ggl-post-types'),
				'edit_item' => __('Edit Team Member', 'ggl-post-types'),
				'view_item' => __('View Team Member', 'ggl-post-types'),
				'all_items' => __('All Team Members', 'ggl-post-types'),
				'featured_image' => __('Photo', 'ggl-post-types'),
				'set_featured_image' => __('Set Photo', 'ggl-post-types'),
				'remove_featured_image' => __('Remove Photo', 'ggl-post-types'),
				'upload_featured_image' => __('Upload Photo', 'ggl-post-types'),
			],
			'public' => true,
			'has_archive' => 'team-members',
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'can_export' => true,
			'show_ui' => true,
			'show_in_rest' => true,
			'menu_position' => 7,
			'menu_icon' => 'dashicons-groups',
			'supports' => ['title', 'thumbnail', 'editor'],
			'rewrite' => [
				'slug' => 'team-members',
				'with_front' => true,
				'pages' => false,
			]
		]
	);
}

function team_member_register_meta_boxes($meta_boxes)
{
	$prefix = 'team-member';

	$meta_boxes[] = [
		'title' => esc_html__('Social Media Information', 'ggl-post-types'),
		'id' => 'social_links',
		'context' => 'side',
		'post_types' => ['team-member'],
		'fields' => [
			[
				'type' => 'url',
				'name' => esc_html__('Instagram Profile', 'ggl-post-types'),
				'id' => $prefix . 'instagram_link',
			],
			[
				'type' => 'url',
				'name' => esc_html__('Letterboxd Profile', 'ggl-post-types'),
				'id' => $prefix . 'letterboxd_link',
			],
			[
				'type' => 'url',
				'name' => esc_html__('Mastodon Profile', 'ggl-post-types'),
				'id' => $prefix . 'mastodon_link',
			],
		],
	];

	$meta_boxes[] = [
		'title' => esc_html__('Membership Type', 'ggl-post-types'),
		'id' => 'membership_information',
		'context' => 'before_permalink',
		'style' => 'seamless',
		'post_types' => ['team-member'],
		'fields' => [
			[
				'type'=> 'heading',
				'name' => esc_html__('Membership Type', 'ggl-post-types'),
			],
			[
				'type' => 'radio',
				'id' => $prefix . 'status',
				'inline' => true,
				'options' => [
					'probationary' => esc_html__('Probationary','ggl-post-types'),
					'active' => esc_html__('Active','ggl-post-types'),
					'former' => esc_html__('Former','ggl-post-types'),
				]
			]
		],
	];

	return $meta_boxes;
}


