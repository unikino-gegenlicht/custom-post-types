<?php

namespace CinemaPress\post_types;

use CinemaPress\interfaces\Post_Type;

class Admission_Type implements Post_Type {

	public const string SLUG = 'admission_type';

	/**
	 * @inheritDoc
	 */
	public static function register_data(): array {
		return array(
			'name'           => __( 'Admission Types', 'cinema-press' ),
			'labels'         => array(
				'name'                   => __( 'Admission Types', 'cinema-press' ),
				'singular_name'          => __( 'Admission Type', 'cinema-press' ),
				'add_new'                => __( 'Add Admission Type', 'cinema-press' ),
				'add_new_item'           => __( 'Add Admission Type', 'cinema-press' ),
				'edit_item'              => __( 'Edit Admission Type', 'cinema-press' ),
				'new_item'               => __( 'New Admission Type', 'cinema-press' ),
				'view_item'              => __( 'View Admission Type', 'cinema-press' ),
				'view_items'             => __( 'View Admission Types', 'cinema-press' ),
				'search_items'           => __( 'Search Admission Types', 'cinema-press' ),
				'not_found'              => __( 'No Admission Types found.', 'cinema-press' ),
				'not_found_in_trash'     => __( 'No Admission Types found in Trash.', 'cinema-press' ),
				'all_items'              => __( 'Admission Types', 'cinema-press' ),
				'attributes'             => __( 'Admission Type Attributes', 'cinema-press' ),
				'insert_into_item'       => __( 'Insert into Admission Type', 'cinema-press' ),
				'uploaded_to_this_item'  => __( 'Uploaded to this Admission Type', 'cinema-press' ),
				'items_list_navigation'  => __( 'Admission Type list navigation', 'cinema-press' ),
				'items_list'             => __( 'Admission Type list', 'cinema-press' ),
				'item_published'         => __( 'Admission Type published.', 'cinema-press' ),
				'item_published_private' => __( 'Admission Type published private.', 'cinema-press' ),
				'item_reverted_to_draft' => __( 'Admission Type reverted to draft.', 'cinema-press' ),
				'item_trashed'           => __( 'Admission Type trashed.', 'cinema-press' ),
				'item_scheduled'         => __( 'Admission Type scheduled.', 'cinema-press' ),
				'item_updated'           => __( 'Admission Type updated.', 'cinema-press' ),
			),
			'description'    => __( 'Admission Types are used to setup ticket prices and possible restrictions on tickets', 'cinema-press' ),
			'public'         => false,
			'show_ui'        => true,
			'hierarchical'   => false,
			'has_archive'    => false,
			'show_in_rest'   => true,
			'rest_namespace' => 'cinema-press',
			'supports'       => array( 'title' ),
			'show_in_menu'   => "edit.php?post_type=screening",
			'menu_position'  => - 10,
			'menu_icon'      => 'dashicons-tickets-alt'
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {
		$meta_boxes[] = array(
			'title'      => __( 'Admission Details', 'cinema-press' ),
			'id'         => 'cinema_press-admission_details',
			'context'    => 'normal',
			'style'      => 'seamless',
			'post_types' => array( self::SLUG ),
			'autosave'   => true,
			'revision'   => true,
			'fields'     => array(
				[
					'id'          => "fee",
					'name'        => __( 'Admission Fee', 'cinema-press' ),
					'type'        => "number",
					'placeholder' => "3.00",
					'step'        => 0.01,
					'append'      => "€"
				]
			)
		);

		return $meta_boxes;
	}
}