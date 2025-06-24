<?php
/*
 * Copyright (c) 2024. Philip Kaufmann, Jan Eike Suchard, Benjamin Witte
 * This file is copyright under the latest version of the EUPL.
 * Please see the LICENSE file for your rights.
 */

function ggl_taxonomy_program_type(): void {
	register_taxonomy(
		'special-program',
		null,
		[
			'label'         => __( 'Special Programs', 'ggl-post-types' ),
			'description'   => 'This is the special programs category, if you have a movie which is falls into a special program, create it here first and then assign it to the movie',
			'labels'        => [
				'name'          => __( 'Special Programs', 'ggl-post-types' ),
				'singular_name' => __( 'Special Program', 'ggl-post-types' ),
				'search_items'  => __( 'Search Special Programs', 'ggl-post-types' ),
				'all_items'     => __( 'All Special Programs', 'ggl-post-types' ),
				'edit_item'     => __( 'Edit Special Program', 'ggl-post-types' ),
				'update_item'   => __( 'Update Special Program', 'ggl-post-types' ),
				'add_new_item'  => __( 'Add New Special Program', 'ggl-post-types' ),
				'new_item_name' => __( 'New Special Program Name', 'ggl-post-types' ),
				'menu_name'     => __( 'Special Programs', 'ggl-post-types' ),
			],
			'show_ui'       => true,
			'public'        => true,
			'show_tagcloud' => false,
			'hierarchical'  => false,
			'meta_box_cb'   => false,
			'query_var'     => true,
			'rewrite'       => [
				'slug'         => 'special-program',
				'hierarchical' => false,
				'with_front'   => false,
			],
		]
	);
}

function ggl_taxonomy_program_type_meta_boxes( $meta_boxes ): mixed {
	$prefix = 'special-program_';

	$meta_boxes[] = [
		'title'      => esc_html__( 'Extended Configuration', 'ggl-post-types' ),
		'id'         => 'extended-configuration',
		'taxonomies' => 'special-program',
		'context'    => 'normal',
		'fields'     => [
			[
				'type' => 'color',
				'name' => esc_html__( 'Background Color', 'ggl-post-types' ),
				'id'   => $prefix . 'banner_color',
				'desc' => esc_html__( 'The color used as the background for content related the special programm', 'ggl-post-types' ),
			],
			[
				'type' => 'color',
				'name' => esc_html__( 'Background Color (Dark Mode)', 'ggl-post-types' ),
				'id'   => $prefix . 'banner_color',
				'desc' => esc_html__( 'The color used as the background in dark mode for content related the special programm', 'ggl-post-types' ),
			],
			[
				'type' => 'color',
				'name' => esc_html__( 'Text Color', 'ggl-post-types' ),
				'id'   => $prefix . 'text_color',
				'desc' => esc_html__( 'The color used for the text on the special programme program related pages', 'ggl-post-types' ),
			],
			[
				'type' => 'color',
				'name' => esc_html__( 'Text Color (Dark Mode)', 'ggl-post-types' ),
				'id'   => $prefix . 'text_color_dark',
				'desc' => esc_html__( 'The color used for the text in dark mode on the special programm related pages', 'ggl-post-types' ),
			],
			[
				'type'         => 'single_image',
				'name'         => __( 'Program Logo', 'ggl-post-types' ),
				'id'           => $prefix . 'image',
				'force_delete' => false,
				'desc'         => esc_html__( 'This logo is displayed on the front page to identify the special program', 'ggl-post-types' ),
			],
			[
				'type'         => 'single_image',
				'name'         => __( 'Image for Anonymized Detail Pages', 'ggl-post-types' ),
				'id'           => $prefix . 'anonymous_detail_image',
				'force_delete' => false,
				'desc'         => esc_html__( 'This image might be show to unauthenticated users visiting the website' )
			],
		],
	];

	return $meta_boxes;
}