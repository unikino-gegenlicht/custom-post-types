<?php

function ggl_taxonomy_semester(): void {
	register_taxonomy( 'semester', null, [
		'labels'        => [
			'name'          => __( 'Semesters', 'ggl-post-types' ),
			'singular_name' => __( 'Semester', 'ggl-post-types' ),
			'search_items'  => __( 'Search Semesters', 'ggl-post-types' ),
			'all_items'     => __( 'All Semesters', 'ggl-post-types' ),
			'edit_item'     => __( 'Edit Semester', 'ggl-post-types' ),
			'update_item'   => __( 'Update Semester', 'ggl-post-types' ),
			'add_new_item'  => __( 'Add New Semester', 'ggl-post-types' ),
			'new_item_name' => __( 'New Semester', 'ggl-post-types' ),
		],
		'description'   => __( 'Semesters keep things organized', 'ggl-post-types' ),
		'public'        => true,
		'meta_box_cb'   => false,
		'hierarchical'  => false,
		'show_tagcloud' => false,
		'query_var'     => true,
		'rewrite'       => [
			'slug'         => 'semester',
			'hierarchical' => false,
			'with_front'   => false,
		],
	] );
}

function ggl_taxonomy_semester_meta_boxes( $meta_boxes ): mixed {
	$prefix       = 'semester_';
	$meta_boxes[] = [
		'title'      => esc_html__( 'Extended Information', 'ggl-post-types' ),
		'id'         => 'extended-information',
		'taxonomies' => 'semester',
		'context'    => 'normal',
		'fields'     => [
			[
				'type'       => 'date',
				'name'       => esc_html__( 'Screening Start', 'ggl-post-types' ),
				'id'         => $prefix . 'start',
				'desc'       => esc_html__( 'The date at which the first official screening of the semester will take place', 'ggl-post-types' ),
				'timestamp'  => false,
				'js_options' => [
					'dateFormat' => 'dd.mm.yy',
				],
			],
		],
	];

	return $meta_boxes;
}