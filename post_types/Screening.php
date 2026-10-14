<?php
/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

namespace CinemaPress\post_types;

use CinemaPress\interfaces\Post_Type;

class Screening implements Post_Type {

	public const string SLUG = 'screening';

	/**
	 * @inheritDoc
	 */
	public static function register_data(): array {
		return array(
			'name'                => __( 'Screenings', 'cinema-press' ),
			'labels'              => array(
				'name'                     => __( 'Screenings', 'cinema-press' ),
				'singular_name'            => __( 'Screening', 'cinema-press' ),
				'add_new'                  => __( 'Add Screening', 'cinema-press' ),
				'add_new_item'             => __( 'Add Screening', 'cinema-press' ),
				'edit_item'                => __( 'Edit Screening', 'cinema-press' ),
				'new_item'                 => __( 'New Screening', 'cinema-press' ),
				'view_item'                => __( 'View Screening', 'cinema-press' ),
				'view_items'               => __( 'View Screenings', 'cinema-press' ),
				'search_items'             => __( 'Search Screenings', 'cinema-press' ),
				'not_found'                => __( 'No Screenings found', 'cinema-press' ),
				'not_found_in_trash'       => __( 'No Screenings found in Trash', 'cinema-press' ),
				'all_items'                => __( 'All Screenings', 'cinema-press' ),
				'archives'                 => __( 'Archive', 'cinema-press' ),
				'attributes'               => __( 'Screening Attributes', 'cinema-press' ),
				'insert_into_item'         => __( 'Insert into screening', 'cinema-press' ),
				'uploaded_to_this_item'    => __( 'Uploaded to this screening', 'cinema-press' ),
				'featured_image'           => __( 'Featured Image', 'cinema-press' ),
				'set_featured_image'       => __( 'Set featured image', 'cinema-press' ),
				'remove_featured_image'    => __( 'Remove featured image', 'cinema-press' ),
				'use_featured_image'       => __( 'Use as featured image', 'cinema-press' ),
				'filter_items_list'        => __( 'Filter Screenings list', 'cinema-press' ),
				'items_list_navigation'    => __( 'Screening navigation', 'cinema-press' ),
				'items_list'               => __( 'Screening list', 'cinema-press' ),
				'item_published'           => __( 'Screening published', 'cinema-press' ),
				'item_published_privately' => __( 'Screening published private', 'cinema-press' ),
				'item_reverted_to_draft'   => __( 'Screening reverted to draft', 'cinema-press' ),
				'item_trashed'             => __( 'Screening trashed', 'cinema-press' ),
				'item_scheduled'           => __( 'Screening scheduled', 'cinema-press' ),
				'item_updated'             => __( 'Screening updated', 'cinema-press' ),
				'item_link'                => __( 'Screening link', 'cinema-press' ),
				'item_link_description'    => __( 'A link to a screening', 'cinema-press' ),
			),
			'description'         => __( 'A screening is used to instantiate a viewing of a movie', 'cinema-press' ),
			'public'              => true,
			'hierarchical'        => false,
			'exclude_from_search' => false,
			'show_in_nav_menus'   => true,
			'show_in_rest'        => true,
			'rest_namespace'      => 'cinema-press',
			'capability_type'     => array( 'screening', 'screenings' ),
			'supports'            => array( 'revisions', 'thumbnail', 'autosave', 'authors' ),
			'has_archive'         => true,
			'rewrite'             => array(
				'slug'       => 'screening',
				'with_front' => false,
				'feeds'      => false,
				'pages'      => false,
			),
			'query_var'           => 'screening',
			'delete_with_user'    => false,
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {
		$meta_boxes[] = array(
			'title'      => __( 'Screening Details', 'cinema-press' ),
			'id'         => 'cinema_press-screening_details',
			'context'    => 'form_top',
			'style'      => 'seamless',
			'post_types' => array( 'screening' ),
			'autosave'   => true,
			'revision'   => true,
			'fields'     => array(
				[
					'id'          => 'movie',
					'type'        => 'post',
					'name'        => __( 'Movie', 'cinema-press' ),
					'desc'        => __( 'Select the movie that will be screened', 'cinema-press' ),
					'placeholder' => __( 'Select a movie', 'cinema-press' ),
					'required'    => true,
					'revision'    => true,
					'post_type'   => 'movie',
					'query_args'  => array(
						'post_status'    => 'publish',
						'posts_per_page' => 10
					),
					'js_options'  => [
						'minimumInputLength' => 1,
					],
					'ajax'        => true,
				],
				[
					'id'            => 'screening_start',
					'type'          => 'datetime',
					'name'          => __( 'Start Date and Time', 'cinema-press' ),
					'required'      => true,
					'revision'      => true,
					'timestamp'     => false,
					'js_options'    => array(
						'dateFormat' => 'dd.mm.yyyy'
					),
					'save_format'   => 'Y-m-d H:i',
					'admin_columns' => array(
						'position'   => 'replace date',
						'title'      => __( "Screening Date", 'cinema-press' ),
						'link'       => 'none',
						'sort'       => true,
						'searchable' => false,
						'filterable' => false,
					)
				],
				[
					'id'          => 'location',
					'type'        => 'post',
					'name'        => __( 'Screening Location', 'cinema-press' ),
					'desc'        => __( 'The location the screening will happen in', 'cinema-press' ),
					'placeholder' => __( 'Select a location', 'cinema-press' ),
					'required'    => true,
					'revision'    => true,
					'post_type'   => 'screening_location',
					'query_args'  => array(
						'post_status'    => 'publish',
						'posts_per_page' => 10
					),
					'js_options'  => [
						'minimumInputLength' => 1,
					],
					'ajax'        => true,
					'add_new'     => true
				]
			)
		);

		return $meta_boxes;
	}
}