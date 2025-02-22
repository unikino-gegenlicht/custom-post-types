<?php
/*
 * Copyright (c) 2024. Philip Kaufmann, Jan Eike Suchard, Benjamin Witte
 * This file is copyright under the latest version of the EUPL.
 * Please see the LICENSE file for your rights.
 */

function ggl_post_type_supporter(): void {
	register_post_type( 'supporter',
		[
			'label'               => __( 'Supporters' ),
			'labels'              => [
				'menu_name'             => __( 'Supporters' ),
				'name_admin_bar'        => __( 'Supporter' ),
				'singular_name'         => __( 'Supporter' ),
				'add_new_item'          => __( 'Add Supporter' ),
				'add_new'               => __( 'Add Supporter' ),
				'edit_item'             => __( 'Edit Supporter' ),
				'view_item'             => __( 'View Supporter' ),
				'all_items'             => __( 'All Supporters' ),
				'featured_image'        => __( 'Logo' ),
				'set_featured_image'    => __( 'Set Logo' ),
				'remove_featured_image' => __( 'Remove Logo' ),
				'upload_featured_image' => __( 'Upload Logo' ),
			],
			'public'              => true,
			'has_archive'         => 'supporters',
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'can_export'          => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'menu_position'       => 16,
			'menu_icon'           => 'dashicons-superhero-alt',
			'supports'            => [ 'title', 'thumbnail', 'editor' ],
			'rewrite'             => [
				'slug'       => 'supporters',
				'with_front' => false,
				'pages'      => false,
			]
		] );
}

function supporter_register_meta_boxes( $meta_boxes ) {
    $prefix = 'supporter';

    $meta_boxes[] = [
        'title'   => esc_html__( 'Further Information' ),
        'id'      => 'supporter',
        'context' => 'side',
        'post_types' => ['supporter'],
        'fields'  => [
            [
                'type' => 'url',
                'name' => esc_html__( 'Website' ),
                'id'   => $prefix . 'website',
            ],
        ],
    ];

    return $meta_boxes;
}


