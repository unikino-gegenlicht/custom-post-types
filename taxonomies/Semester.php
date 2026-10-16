<?php
/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

namespace CinemaPress\taxonomies;

use CinemaPress\interfaces\Taxonomy;

class Semester implements Taxonomy {

	public const string SLUG = 'semester';

	/**
	 * @inheritDoc
	 */
	public static function register_data(): array {
		return array(
			'labels'             => array(
				'name'                       => __( 'Semesters', 'cinema-press' ),
				'singular_name'              => __( 'Semester', 'cinema-press' ),
				'search_items'               => __( 'Semesters', 'cinema-press' ),
				'popular_items'              => __( 'Most Used Semesters', 'cinema-press' ),
				'all_items'                  => __( 'All Semesters', 'cinema-press' ),
				'name_field_description'     => __( 'The name of the semester. Often used values are "Wintersemester 2026/2027"', 'cinema-press' ),
				'slug_field_description'     => __( 'The slug for the semester. This will be included in the URLs for the screenings', 'cinema-press' ),
				'desc_field_description'     => __( 'The description of the semester or an opening message that will be shown on the semesters detail page', 'cinema-press' ),
				'edit_item'                  => __( 'Edit Semester', 'cinema-press' ),
				'view_item'                  => __( "View Semester", 'cinema-press' ),
				'update_item'                => __( "Update Semester", 'cinema-press' ),
				'add_new_item'               => __( "Add New Semester", 'cinema-press' ),
				'new_item_name'              => __( "New Semester Name", 'cinema-press' ),
				'template_name'              => __( "Semesters", 'cinema-press' ),
				'separate_items_with_commas' => __( 'Separate Semesters with commas', 'cinema-press' ),
				'add_or_remove_items'        => __( 'Add or remove Semesters', 'cinema-press' ),
				'choose_from_most_used'      => __( 'Choose from the most used Semesters', 'cinema-press' ),
				'not_found'                  => __( 'No Semesters found.', 'cinema-press' ),
				'filter_by_item'             => __( 'Filter by Semester', 'cinema-press' ),
				'back_to_items'              => __( 'Back to Semesters', 'cinema-press' ),
				'item_link'                  => __( 'Semester Link', 'cinema-press' ),
				'item_link_description'      => __( 'A link to a semester', 'cinema-press' ),
			),
			'public'             => true,
			'show_in_menu'       => current_user_can( 'edit_others_posts' ),
			'show_in_nav_menus'  => true,
			'rest_namespace'     => 'cinema-press',
			'show_tagcloud'      => false,
			'show_in_quick_edit' => false,
			'show_admin_column'  => true,
			'rewrite'            => array(
				'slug'       => self::SLUG,
				'with_front' => false,
			),
			'meta_box_cb'        => false,
			'default_term'       => array(
				'name'        => 'Special',
				'slug'        => 'special',
				'description' => 'Dieses Semester ist ein besonderes Semester, denn über dieses werden alle Vorführungen die nicht in ein Semester zuweisbar sind findbar'
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {
		$meta_boxes[] = array(
			'title'      => __( 'Archival Data', 'cinema-press' ),
			'id'         => 'cinema_press-historic_data',
			'taxonomies' => [ self::SLUG ],
			'style'      => 'seamless',
			'autosave'   => true,
			'revision'   => true,
			'fields'     => array(
				[
					'id'                => 'historic_data',
					'type'              => 'group',
					'name'              => __( 'Manual Archival Data', 'cinema-press' ),
					'label_description' => __( 'The archival data can be used to add movies to the semester, even tough no screening has been created. This can be used to transfer or build up an archive without having to enter the full details for each movie.', 'cinema-press' ),
					'clone'             => true,
					'sort_clone'        => false,
					'group_title'       => 'Screening {#}',
					'collapsible'       => true,
					'fields'            => array(
						[
							'id'   => 'german_title',
							'type' => 'text',
							'name' => __( 'German Title', 'cinema-press' ),
						],
						[
							'id'   => 'english_title',
							'type' => 'text',
							'name' => __( 'English Title', 'cinema-press' ),
						],
						[
							'id'   => 'original_title',
							'type' => 'text',
							'name' => __( 'Original Title', 'cinema-press' ),
						],
						[
							'id'          => 'screening_date',
							'type'        => 'datetime',
							'name'        => __( 'Screening Date', 'cinema-press' ),
							'save_format' => 'Y-m-d H:i:s',
							'js_options'  => array(
								'date_format' => 'dd.mm.yy',
							)
						]
					)
				],

			)
		);

		return $meta_boxes;
	}
}