<?php
/*
 * Copyright (c) 2024. Philip Kaufmann, Jan Eike Suchard, Benjamin Witte
 * This file is copyright under the latest version of the EUPL.
 * Please see the LICENSE file for your rights.
 */

require_once plugin_dir_path(__FILE__) . '../inc/icons.php';

function ggl_post_type_cooperation_partner(): void {
	register_post_type( 'cooperation-partner',
		[
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
			'supports'            => [ 'title', 'thumbnail', 'editor' ],
			'rewrite'             => [
				'slug'       => 'cooperation-partners',
				'with_front' => false,
				'pages'      => false,
			]
		] );
}

function cooperation_partner_register_meta_boxes( $meta_boxes ) {
    $prefix = 'cooperation-partner';

    $meta_boxes[] = [
        'title'   => esc_html__( 'Further Information' ),
        'id'      => 'supporter',
        'context' => 'side',
        'post_types' => ['cooperation-partner'],
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


