<?php
/*
 * Copyright (c) 2024. Philip Kaufmann, Jan Eike Suchard, Benjamin Witte
 * This file is copyright under the latest version of the EUPL.
 * Please see the LICENSE file for your rights.
 */

require_once plugin_dir_path( __FILE__ ) . '../inc/icons.php';

function ggl_post_type_cooperation_partner(): void {
	register_post_type( 'cooperation-partner', [
		'label'               => __( 'Cooperation Partners', 'ggl-post-types' ),
		'labels'              => [
			'menu_name'             => __( 'Cooperation Partners', 'ggl-post-types' ),
			'name_admin_bar'        => __( 'Cooperation Partner', 'ggl-post-types' ),
			'singular_name'         => __( 'Cooperation Partner', 'ggl-post-types' ),
			'add_new_item'          => __( 'Add Cooperation Partner', 'ggl-post-types' ),
			'add_new'               => __( 'Add Cooperation Partner', 'ggl-post-types' ),
			'edit_item'             => __( 'Edit Cooperation Partner', 'ggl-post-types' ),
			'view_item'             => __( 'View Cooperation Partner', 'ggl-post-types' ),
			'all_items'             => __( 'All Cooperation Partners', 'ggl-post-types' ),
			'featured_image'        => __( 'Logo', 'ggl-post-types' ),
			'set_featured_image'    => __( 'Set Logo', 'ggl-post-types' ),
			'remove_featured_image' => __( 'Remove Logo', 'ggl-post-types' ),
			'upload_featured_image' => __( 'Upload Logo', 'ggl-post-types' ),
		],
		'public'              => true,
		'has_archive'         => 'cooperation-partners',
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'can_export'          => true,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'menu_position'       => 8,
		'menu_icon'           => IconHandshake,
		'supports'            => [ 'title', 'thumbnail', 'editor', 'revisions' ],
		'rewrite'             => [
			'slug'       => 'cooperation-partners',
			'with_front' => false,
			'pages'      => false,
		]
	] );
}

function cooperation_partner_register_meta_boxes( $meta_boxes ) {
	$prefix = 'cooperation-partner_';

	$meta_boxes[] = [
		'title'      => esc_html__( 'Further Information' ),
		'id'         => $prefix . 'additional_info',
		'context'    => 'after_title',
		'style'      => 'seamless',
		'post_types' => [ 'cooperation-partner' ],
		'autosave'   => true,
		'revision'   => true,
		'fields'     => [
			[
				'type' => 'heading',
				'name' => esc_html__( 'Further Details', 'ggl-post-types' ),
			],
			[
				'type'     => 'url',
				'name'     => esc_html__( 'Website', 'ggl-post-types' ),
				'id'       => $prefix . 'website',
				'revision' => true,
			],
		],
	];

	$meta_boxes[] = [
		'title'      => esc_html__( 'Archival Data', 'ggl-post-types' ),
		'id'         => 'manual-archive',
		'context'    => 'normal',
		'style'      => 'seamless',
		'post_types' => [ 'cooperation-partner' ],
		'autosave'   => true,
		'revision'   => true,
		'fields'     => [
			[
				'type' => 'heading',
				'name' => esc_html__( 'Archival Data', 'ggl-post-types' ),
			],
			[
				'type'        => 'key_value',
				'id'          => $prefix . 'shown_movies',
				'desc'        => esc_html__( 'When filling in this archival list the given values are added to the automatically read values', 'ggl-post-types' ),
				'placeholder' => [
					'key'   => 'Year',
					'value' => 'Movie/Event Name',
				],
				'revision'    => true,
			],
		],
	];

	return $meta_boxes;
}


/**
 * Get the movies that are associated to the cooperation partner
 *
 * The list will contain any movie that has the cooperation partner listed as a proposing party and that is displayed as
 * selected by a cooperation partner
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return array
 */
function ggl_get_partner_movies( int|WP_Post $post = 0 ): array {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( $post->post_type !== "cooperation-partner" ) {
		return [];
	}

	$query = new WP_Query( [
		"posts_per_page" => - 1,
		"post_type"      => "movie",
		"meta_query" => [
			[
				"key" => "selected_by",
				"value" => "coop"
			],
			[
				"key" => "cooperation_partner_id",
				"value" => [$post->ID],
				"compare" => "IN"
			]
		]
	] );
	return $query->posts;
}

/**
 * Get the URL for the teamie picture or get the fallback variant
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return string The URL pointing to the picture
 */
function ggl_get_partner_image_url( int|WP_Post $post = 0 ): string {
	$post = get_post( $post, filter: 'display' );
	if ( $post->post_type != "cooperation-partner" ) {
		return "";
	}

	$anonymous_image = rwmb_meta( "teamie_anonymous_image", [ "object_type" => "setting" ], "ggl_cpt__settings" );

	return get_the_post_thumbnail_url( $post, "member-crop" ) ?: $anonymous_image["sizes"]["member-crop"]["url"] ?? $anonymous_image["full_url"];
}

/**
 * Output the markup for the team members image
 *
 * @param int|WP_Post $post
 * @param string $classes
 *
 * @return void
 */
function ggl_the_partner_image( int|WP_Post $post = 0, string $classes = "image coop-logo", string $min_width = "" ): void {
	$url         = ggl_get_partner_image_url( $post );
	$teamie_name = ggl_get_title( $post );
	$title       = sprintf( __( "This logo represents %s", "ggl-post-types" ), $teamie_name );
	echo "<picture class='$classes' title='$title' style='min-width: $min_width !important; height: min-content;'>";
	echo "<img src='$url' alt=''/>";
	echo "</picture>";
}

