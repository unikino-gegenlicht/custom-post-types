<?php

namespace CinemaPress\post_types;

use CinemaPress\interfaces\Post_Type;

class Sponsors implements Post_Type {

	public const string SLUG = 'sponsor';


	/**
	 * @inheritDoc
	 */
	public static function register_data(): array {
		return array(
			'label'          => __( 'Sponsors', 'cinema-press' ),
			'labels'         => array(
				'name'                      => __( 'Sponsors', 'cinema-press' ),
				'singular_name'             => __( 'Sponsor', 'cinema-press' ),
				'add_new'                   => __( 'Add Sponsor', 'cinema-press' ),
				'add_new_item'              => __( 'Add Sponsor', 'cinema-press' ),
				'edit_item'                 => __( 'Edit Sponsor', 'cinema-press' ),
				'new_item'                  => __( 'New Sponsor', 'cinema-press' ),
				'view_item'                 => __( 'View Sponsor', 'cinema-press' ),
				'view_items'                => __( 'View Sponsors', 'cinema-press' ),
				'search_items'              => __( 'Search Sponsors', 'cinema-press' ),
				'not_found'                 => __( 'No Sponsors found', 'cinema-press' ),
				'not_found_in_trash'        => __( 'No Sponsors found in Trash', 'cinema-press' ),
				'all_items'                 => __( 'All Sponsors', 'cinema-press' ),
				'archives'                  => __( 'Sponsor Overview', 'cinema-press' ),
				'attributes'                => __( 'Sponsor Attributes', 'cinema-press' ),
				'insert_into_item'          => __( 'Insert into Sponsor', 'cinema-press' ),
				'uploaded_to_this_item'     => __( 'Uploaded to this sponsor', 'cinema-press' ),
				'set_featured_image'        => __( 'Set Sponsor Logo', 'cinema-press' ),
				'remove_features_image'     => __( 'Remove sponsor logo', 'cinema-press' ),
				'use_featured_image'        => __( 'Use Sponsor Logo', 'cinema-press' ),
				'filter_items_list'         => __( 'Filter Sponsors list', 'cinema-press' ),
				'items_list_navigation'     => __( 'Sponsor list navigation', 'cinema-press' ),
				'items_list'                => __( 'Sponsor list', 'cinema-press' ),
				'item_published'            => __( 'Sponsor published', 'cinema-press' ),
				'item_published_privateley' => __( 'Sponsor published privateley', 'cinema-press' ),
				'item_reverted_to_draft'    => __( 'Sponsor reverted to draft', 'cinema-press' ),
				'item_trashed'              => __( 'Sponsor trashed', 'cinema-press' ),
				'item_scheduled'            => __( 'Sponsor scheduled', 'cinema-press' ),
				'item_updated'              => __( 'Sponsor updated', 'cinema-press' ),
				'item_link'                 => __( 'Sponsor link', 'cinema-press' ),
				'item_link_description'     => __( 'Link to a sponsor', 'cinema-press' ),
			),
			'public'         => true,
			'hierarchical'   => false,
			'show_in_rest'   => true,
			'rest_namespace' => 'cinema-press',
			'supports'       => array( 'title', 'thumbnail', 'editor' ),
			'has_archive'    => "sponsors",
			'rewrite'        => array(
				'slug'       => 'sponsor',
				'with_front' => false,
				'feeds'      => false,
				'pages'      => false,
			),
			'menu_position'  => - 50,
			'menu_icon'      => 'dashicons-awards'
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {
		// TODO: Implement meta_boxes() method.
	}
}