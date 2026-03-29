<?php
/*
 * Copyright (c) 2024. Philip Kaufmann, Jan Eike Suchard, Benjamin Witte
 * This file is copyright under the latest version of the EUPL.
 * Please see the LICENSE file for your rights.
 */

use MatthiasMullie\Minify;

function ggl_taxonomy_program_type(): void {
	register_taxonomy( 'special-program', null, [
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
		'show_in_menu'  => current_user_can( "edit_others_posts" ),
		'public'        => true,
		'show_tagcloud' => false,
		'hierarchical'  => false,
		'show_in_rest'  => true,
		'meta_box_cb'   => false,
		'query_var'     => true,
		'rewrite'       => [
			'slug'         => 'special-program',
			'hierarchical' => false,
			'with_front'   => false,
		],
		"capabilities"  => [
			"manage_terms" => "publish_posts",
			"edit_terms"   => "publish_posts",
			"assign_terms" => "publish_posts",
			"delete_terms" => "edit_others_posts",
		]
	] );
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
				'type'     => 'color',
				'name'     => esc_html__( 'Background Color', 'ggl-post-types' ),
				'id'       => 'background_color',
				'desc'     => esc_html__( 'The color used as the background for content related the special programm', 'ggl-post-types' ),
				'revision' => true
			],
			[
				'type'     => 'color',
				'name'     => esc_html__( 'Text Color', 'ggl-post-types' ),
				'id'       => 'text_color',
				'desc'     => esc_html__( 'The color used for the text on the special programme program related pages', 'ggl-post-types' ),
				'revision' => true
			],
			[
				'type'     => 'color',
				'name'     => esc_html__( 'Background Color (Dark Mode)', 'ggl-post-types' ),
				'id'       => 'dark_background_color',
				'desc'     => esc_html__( 'The color used as the background in dark mode for content related the special programm', 'ggl-post-types' ),
				'revision' => true
			],

			[
				'type'     => 'color',
				'name'     => esc_html__( 'Text Color (Dark Mode)', 'ggl-post-types' ),
				'id'       => 'dark_text_color',
				'desc'     => esc_html__( 'The color used for the text in dark mode on the special programm related pages', 'ggl-post-types' ),
				'revision' => true
			],
			[
				'type'         => 'single_image',
				'name'         => __( 'Program Logo', 'ggl-post-types' ),
				'id'           => 'logo',
				'force_delete' => false,
				'desc'         => esc_html__( 'This logo is displayed on the front page to identify the special program', 'ggl-post-types' ),
				'revision'     => true
			],
			[
				'type'         => 'single_image',
				'name'         => __( 'Program Logo (Dark Mode)', 'ggl-post-types' ),
				'id'           => 'logo_dark',
				'force_delete' => false,
				'desc'         => esc_html__( 'This logo is displayed on the front page to identify the special program if the dark mode is active', 'ggl-post-types' ),
				'revision'     => true
			],
			[
				'type'         => 'single_image',
				'name'         => __( 'Image for Anonymized Detail Pages', 'ggl-post-types' ),
				'id'           => 'anonymous_image',
				'force_delete' => false,
				'desc'         => esc_html__( "Upload an image for fallback usage which is displayed on a movies detail page in case one hasn't been uploaded or the movie may not be advertised", 'gegenlicht' ),
				'revision'     => true
			],
		],
	];

	return $meta_boxes;
}

function ggl_get_special_program_anonymous_image_url( WP_Term|int $term, $size = "desktop" ): string {
	$term = get_term( $term );

	if ( $term->taxonomy !== 'special-program' ) {
		return '';
	}
	$anonymous_image_id = get_term_meta( $term->term_id, 'anonymous_image', true );
	var_dump( $anonymous_image_id );

	return wp_get_attachment_image_url( $anonymous_image_id, $size );
}

function ggl_get_special_program_colors( WP_Term|int $term ): array {
	$term = get_term( $term );
	if ( $term->taxonomy !== 'special-program' ) {
		return [];
	}

	return [
		"lightMode" => [
			"foregroundColor" => get_term_meta( $term->term_id, 'text_color', true ),
			"backgroundColor" => get_term_meta( $term->term_id, 'background_color', true ),
		],
		"darkMode"  => [
			"foregroundColor" => get_term_meta( $term->term_id, 'dark_text_color', true ),
			"backgroundColor" => get_term_meta( $term->term_id, 'dark_background_color', true ),
		]
	];
}

function ggl_special_program_pregenerate_stylesheet( int $term_id ): void {
	$term = get_term( $term_id );
	if ( $term->taxonomy !== 'special-program' ) {
		return;
	}
	$colors          = ggl_get_special_program_colors( $term );
	$destination_dir = path_join( WP_CONTENT_DIR, "cpt-styles" );
	wp_mkdir_p( $destination_dir );
	$destination_path = path_join( $destination_dir, "special-program.$term->slug.overrides.min.css" );

	ob_start();
	?>
    .special-program {
    --bulma-body-color: <?= $colors["lightMode"]["foregroundColor"] ?> !important;
    --bulma-body-background-color: <?= $colors["lightMode"]["backgroundColor"] ?> !important;
    }

    @media (prefers-color-scheme: dark) {
    .special-program {
    --bulma-body-color: <?= $colors["darkMode"]["foregroundColor"] ?> !important;
    --bulma-body-background-color: <?= $colors["darkMode"]["backgroundColor"] ?> !important;
    }
    }

    #special-program_<?= $term->slug ?>,
    #special-program_<?= $term->term_id ?>{
    --bulma-body-color: <?= $colors["lightMode"]["foregroundColor"] ?> !important;
    --bulma-body-background-color: <?= $colors["lightMode"]["backgroundColor"] ?> !important;
    }

    @media (prefers-color-scheme: dark) {
    #special-program_<?= $term->slug ?>,
    #special-program_<?= $term->term_id ?>{
    --bulma-body-color: <?= $colors["darkMode"]["foregroundColor"] ?> !important;
    --bulma-body-background-color: <?= $colors["darkMode"]["backgroundColor"] ?> !important;
    }
    }
	<?php
	$stylesheet = ob_get_clean();
	$minifier   = new Minify\CSS();
	$minifier->add( $stylesheet );
	$minifier->minify( $destination_path );
}

function ggl_special_program_get_stylesheet_url( WP_Term|int $term ): string {
	$term = get_term( $term );
	if ( $term->taxonomy !== 'special-program' ) {
		return '';
	}

	$expected_fs_path = path_join( WP_CONTENT_DIR, "cpt-styles/special-program.$term->slug.overrides.min.css" );

	if ( ! file_exists( $expected_fs_path ) ) {
		ggl_special_program_pregenerate_stylesheet( $term->term_id );
	}

	return content_url( "cpt-styles/special-program.$term->slug.overrides.min.css" );
}

function ggl_special_program_get_stylesheet_path( WP_Term|int $term ): string {
	$term = get_term( $term );
	if ( $term->taxonomy !== 'special-program' ) {
		return '';
	}

	$mode = is_singular( [ "movie", "event" ] ) ? "detail-page" : "content-block";


	$expected_fs_path = path_join( WP_CONTENT_DIR, "cpt-styles/special-program.$term->slug.overrides.min.css" );

	if ( ! file_exists( $expected_fs_path ) ) {
		ggl_special_program_pregenerate_stylesheet( $term->term_id );
	}

	return $expected_fs_path;
}