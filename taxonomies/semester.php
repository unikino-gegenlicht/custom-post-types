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

function ggl_cpt__reorder_semesters( $query ) {
	if ( ! is_admin() || ! function_exists( 'get_current_screen' ) || 'edit-semester' !== get_current_screen()->id ) {
		return;
	}

	$query->query_vars["meta_key"] = "semester_start";
	$query->query_vars["orderby"]  = "meta_value_num";
	$query->query_vars["order"]    = "DESC";


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
				'timestamp'  => true,
				'required'   => true,
				'js_options' => [
					'dateFormat' => 'dd.mm.yy',
				],
			],
		],
	];

	$meta_boxes[] = [
		'title'      => esc_html__( 'Archival Data', 'ggl-post-types' ),
		'id'         => 'archival-information',
		'taxonomies' => 'semester',
		'context'    => 'normal',
		'fields'     => [
			[
				'type'      => 'switch',
				'name'      => esc_html__( 'Add Archival Data', 'ggl-post-types' ),
				'desc'      => esc_html__( 'Setting this value to on will lead to the movies entered down below to be added to the archival data instead of replacing it. (not recommended)', 'ggl-post-types' ),
				'id'        => $prefix . 'add_archival_data',
				'on_label'  => __( "Yes", 'ggl-post-types' ),
				'off_label' => __( "No", 'ggl-post-types' ),
			],
			[
				'type'        => 'key_value',
				'name'        => esc_html__( 'Shown Movies', 'ggl-post-types' ),
				'id'          => $prefix . 'shown_movies',
				'desc'        => esc_html__( 'When filling in this archival list, you will disable the reading of the created posts for the semester. The movies displayed here are sorted by the date you entered before being rendered.', 'ggl-post-types' ),
				'placeholder' => [
					'key'   => 'Date (dd.mm.yy)',
					'value' => 'Movie/Event Name',
				]
			],
		],
	];

	return $meta_boxes;
}