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
				'archives'               => __( 'Our Cooperation Partners', 'cinema-press' ),
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
			'supports'       => array( 'title', 'editor' ),
			'menu_position'  => - 20,
			'menu_icon'      => 'dashicons-networking',
			'rewrite'        => array(
				'slug'         => 'cooperation-partner',
				'with_front'   => false,
				'hierarchical' => false,
				'feeds'        => false,
				'pages'        => false,
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {

		$meta_boxes[] = array(
			'title'      => __( 'Social Medias', 'cinema-press' ),
			'id'         => 'cinema-press_cooperation_socials',
			'context'    => 'after_title',
			'style'      => 'seamless',
			'post_types' => array( self::SLUG ),
			'autosave'   => true,
			'revision'   => true,
			'fields'     => array(
				[
					'type' => 'heading',
					'name' => __( 'External Links', 'cinema-press' ),
					'desc' => __( 'If you want to link the pages of the cooperation partner, you can set the usernames and links here', 'cinema-press' ),
				],
				[
					'id'          => 'website',
					'type'        => 'url',
					'name'        => __( 'Website', 'cinema-press' ),
					'translation' => 'copy'
				],
				[
					'id'          => 'instagram_username',
					'type'        => 'text',
					'name'        => __( 'Instagram Username', "cinema-press" ),
					'prepend'     => "https://instagram.com/",
					'translation' => 'copy',
				],
				[
					'id'          => 'mastodon_profile_url',
					'type'        => 'text',
					'name'        => __( 'Mastodon Profile URL', "cinema-press" ),
					'prepend'     => 'https://',
					'translation' => 'copy',
				],
				[
					'id'          => 'bluesky_username',
					'type'        => 'text',
					'name'        => __( 'Bluesky Username', "cinema-press" ),
					'prepend'     => 'https://bsky.app/profile/',
					'translation' => 'copy',
				],
			)
		);

		$meta_boxes[] = array(
			'title'      => __( 'Logo', 'cinema-press' ),
			'id'         => 'cinema-press_cooperation-logo',
			'context'    => 'after_title',
			'style'      => 'seamless',
			'post_types' => array( self::SLUG ),
			'autosave'   => true,
			'revision'   => true,
			'fields'     => array(
				[
					"type" => "heading",
					"name" => __( 'Logo', 'cinema-press' ),
				],
				[
					'id'      => 'logo',
					'type'    => 'single_image',
					'name'    => __( 'Logo', 'cinema-press' ),
					'desc'    => __( 'The logo of the cooperation partner for display in light mode conditions', 'cinema-press' ),
					'columns' => 6
				],
				[
					'id'      => 'logo_dark',
					'type'    => 'single_image',
					'name'    => __( 'Logo (Dark Mode)', 'cinema-press' ),
					'desc'    => __( 'The logo of the cooperation partner for display in dark mode conditions', 'cinema-press' ),
					'columns' => 6
				],

			)
		);

		return $meta_boxes;
	}
}