<?php
/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

namespace CinemaPress\taxonomies;

use CinemaPress\interfaces\Taxonomy;

class Program_Series implements Taxonomy {

	public const string SLUG = 'program_series';

	/**
	 * @inheritDoc
	 */
	public static function register_data(): array {
		return array(
			'labels'             => array(
				'name'                       => __( 'Program Series', 'cinema-press' ),
				'singular_name'              => __( 'Program Series', 'cinema-press' ),
				'search_items'               => __( 'Search Program Series', 'cinema-press' ),
				'popular_items'              => __( 'Popular Program Series', 'cinema-press' ),
				'all_items'                  => __( 'All Program Series', 'cinema-press' ),
				'name_field_description'     => __( 'The name of the program series', 'cinema-press' ),
				'slug_field_description'     => __( 'The slug of the program series, which will be used to generate screening slugs if necessary', 'cinema-press' ),
				'desc_field_description'     => __( 'The description of the program series and it\'s contents. Try to refer to a generalized topic and not mention any movies as this description should provide general information like genres or other categories that influence the selection for the series', 'cinema-press' ),
				'edit_item'                  => __( 'Edit Program Series', 'cinema-press' ),
				'view_item'                  => __( 'View Program Series', 'cinema-press' ),
				'update_item'                => __( 'Update Program Series', 'cinema-press' ),
				'add_new_item'               => __( 'Add New Program Series', 'cinema-press' ),
				'new_item_name'              => __( 'New Program Series Name', 'cinema-press' ),
				'template_name'              => __( 'Program Series', 'cinema-press' ),
				'separate_items_with_commas' => __( 'Separate program series with commas', 'cinema-press' ),
				'add_or_remove_items'        => __( 'Add or remove program series', 'cinema-press' ),
				'choose_from_most_used'      => __( 'Choose from the most used program series', 'cinema-press' ),
				'not_found'                  => __( 'No program series found.', 'cinema-press' ),
				'filter_by_item'             => __( 'Filter by program series', 'cinema-press' ),
				'back_to_items'              => __( 'Back to program series', 'cinema-press' ),
				'item_link'                  => __( 'Link to program series', 'cinema-press' ),
				'item_link_description'      => __( 'Link to a program series', 'cinema-press' ),
			),
			"public"             => true,
			"show_in_menu"       => current_user_can( 'edit_others_posts' ),
			'show_in_nav_menus'  => true,
			'rest_namespace'     => 'cinema-press',
			'show_tagcloud'      => false,
			'show_in_quick_edit' => false,
			'show_admin_column'  => true,
			'rewrite'            => array(
				'slug'       => 'program-series',
				'with_front' => false,
			),
			'meta_box_cb'        => false,
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {
		$meta_boxes[] = array(
			'id'         => 'program_series_settings',
			'title'      => __( 'Display Settings', 'cinema-press' ),
			'revision'   => true,
			'autosave'   => true,
			'taxonomies' => self::SLUG,
			'fields'     => array(
				[
					'type' => 'custom_html',
					'std'  => '<p style="color: #646970;">' . __( 'These display settings are applied to screenings that have been tagged with this program series', 'cinema-press' ) . '</p>',
				],
				[
					'id'   => 'background',
					'name' => __( 'Background for Pages', 'cinema-press' ),
					'type' => 'background'
				],
				[
					'id'   => 'font_color',
					'name' => __( 'Font color', 'cinema-press' ),
					'type' => 'color'
				]
			)
		);

		return $meta_boxes;
	}
}