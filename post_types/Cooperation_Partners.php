<?php

namespace CinemaPress\post_types;

use CinemaPress\interfaces\Post_Type;

class Cooperation_Partners implements Post_Type {

	public const string SLUG = 'cooperation_partner';

	/**
	 * @inheritDoc
	 */
	public static function register_data(): array {
		return array(
			'name'           => __( 'Cooperation Partners', 'cinema-press' ),
			'labels'         => array(
				'name'                   => __( 'Cooperation Partners', 'cinema-press' ),
				'singular_name'          => __( 'Cooperation Partners', 'cinema-press' ),
				'add_new'                => __( 'Add Cooperation Partner', 'cinema-press' ),
				'add_new_item'           => __( 'Add Cooperation Partner', 'cinema-press' ),
				'edit_item'              => __( 'Edit Cooperation Partner', 'cinema-press' ),
				'new_item'               => __( 'New Cooperation Partner', 'cinema-press' ),
				'view_item'              => __( 'View Cooperation Partner', 'cinema-press' ),
				'view_items'             => __( 'View Cooperation Partners', 'cinema-press' ),
				'search_items'           => __( 'Search Cooperation Partners', 'cinema-press' ),
				'not_found'              => __( 'No Cooperation Partners found.', 'cinema-press' ),
				'not_found_in_trash'     => __( 'No Cooperation Partners found in Trash.', 'cinema-press' ),
				'all_items'              => __( 'All Cooperation Partners', 'cinema-press' ),
				'attributes'             => __( 'Cooperation Partner Attributes', 'cinema-press' ),
				'insert_ino_item'        => __( 'Insert into Cooperation Partner', 'cinema-press' ),
				'uploaded_to_this_item'  => __( 'Uploaded to this Cooperation Partner', 'cinema-press' ),
				'items_list_navigation'  => __( 'Cooperation Partner list navigation', 'cinema-press' ),
				'items_list'             => __( 'Cooperation Partner list', 'cinema-press' ),
				'item_published'         => __( 'Cooperation Partner published.', 'cinema-press' ),
				'item_published_private' => __( 'Cooperation Partner published private.', 'cinema-press' ),
				'item_reverted_to_draft' => __( 'Cooperation Partner reverted to draft.', 'cinema-press' ),
				'item_trashed'           => __( 'Cooperation Partner trashed.', 'cinema-press' ),
				'item_scheduled'         => __( 'Cooperation Partner scheduled.', 'cinema-press' ),
				'item_updated'           => __( 'Cooperation Partner updated.', 'cinema-press' ),
			),
			'description'    => __( 'Cooperation Partners are entities that screen movies together with this cinema but are not members of the cinema', 'cinema-press' ),
			'public'         => true,
			'hierarchical'   => false,
			'has_archive'    => 'cooperations',
			'show_in_rest'   => true,
			'rest_namespace' => 'cinema-press',
			'supports'       => array( 'title', 'editor', 'thumbnail' ),
			'menu_position'  => - 20,
			'menu_icon'      => 'dashicons-networking',
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {
		return $meta_boxes;
	}
}