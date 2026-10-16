<?php
/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

namespace CinemaPress\taxonomies;

use CinemaPress\interfaces\Taxonomy;

class Director implements Taxonomy {

	public const string SLUG = 'director';

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {
		return $meta_boxes;
	}

	public static function register_data(): array {
		return array(
			'labels'             => array(
				'name'                       => __( 'Directors', 'cinema-press' ),
				'singular_name'              => __( 'Director', 'cinema-press' ),
				'search_items'               => __( 'Search Directors', 'cinema-press' ),
				'popular_items'              => __( 'Popular Directors', 'cinema-press' ),
				'all_items'                  => __( 'All Directors', 'cinema-press' ),
				'name_field_description'     => __( 'The full name of the director', 'cinema-press' ),
				'slug_field_description'     => __( 'The slug for the director (auto-generated)', 'cinema-press' ),
				'desc_field_description'     => __( 'A short portrait about the director. This value might be shown depending on the used theme', 'cinema-press' ),
				'edit_item'                  => __( 'Edit Director', 'cinema-press' ),
				'view_item'                  => __( 'View Director', 'cinema-press' ),
				'update_item'                => __( 'Update Director', 'cinema-press' ),
				'add_new_item'               => __( 'Add New Director', 'cinema-press' ),
				'new_item_name'              => __( 'New Director', 'cinema-press' ),
				'template_name'              => __( 'Directors', 'cinema-press' ),
				'separate_items_with_commas' => __( 'Separate Directors with commas', 'cinema-press' ),
				'add_or_remove_items'        => __( 'Add or remove Directors', 'cinema-press' ),
				'choose_from_most_used'      => __( 'Choose from the most used Directors', 'cinema-press' ),
				'not_found'                  => __( 'No directors found. Maybe create one', 'cinema-press' ),
				'filter_by_item'             => __( 'Filter by Director', 'cinema-press' ),
				'back_to_items'              => __( 'Back to Directors', 'cinema-press' ),
				'item_link'                  => __( 'Director Link', 'cinema-press' ),
				'item_link_description'      => __( 'A link to a director', 'cinema-press' ),
			),
			'description'        => __( 'A Director is a person who controls a film\'s artistic and dramatic aspects and visualizes the screenplay ', 'cinema-press' ),
			'public'             => true,
			'show_in_menu'       => current_user_can( "edit_others_posts" ),
			'show_in_nav_menus'  => false,
			'rest_namespace'     => 'cinema-press',
			'show_tagcloud'      => false,
			'show_in_quick_edit' => false,
			'show_admin_column'  => true,
			'rewrite'            => array(
				'slug'       => 'director',
				'with_front' => false,
			),
			'meta_box_cb'        => false,
			'default_term'       => array(
				'name'        => 'Alan Smithee',
				'slug'        => 'alan-smithee',
				'description' => 'Alan Smithee steht als Pseudonym für einen fiktiven Regisseur, der Filme verantwortet, bei denen der eigentliche Regisseur seinen Namen nicht mit dem Werk in Verbindung gebracht haben möchte. Hier wird der Regisseur als Platzhalter verwendet, wenn einem Film noch kein Regisseur zugewiesen wurde'
			)
		);
	}
}