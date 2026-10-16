<?php
/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

namespace CinemaPress\taxonomies;

use CinemaPress\interfaces\Taxonomy;

class Actor implements Taxonomy {

	public const string SLUG = 'actor';

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {
		return $meta_boxes;
	}

	public static function register_data(): array {
		return array(
			'labels'             => array(
				'name'                       => __( 'Actors', 'cinema-press' ),
				'singular_name'              => __( 'Actor', 'cinema-press' ),
				'search_items'               => __( 'Search Actors', 'cinema-press' ),
				'popular_items'              => __( 'Popular Actors', 'cinema-press' ),
				'all_items'                  => __( 'All Actors', 'cinema-press' ),
				'name_field_description'     => __( 'The full name of the Actor', 'cinema-press' ),
				'slug_field_description'     => __( 'The slug for the Actor (auto-generated)', 'cinema-press' ),
				'desc_field_description'     => __( 'A short portrait about the Actor. This value might be shown depending on the used theme', 'cinema-press' ),
				'edit_item'                  => __( 'Edit Actor', 'cinema-press' ),
				'view_item'                  => __( 'View Actor', 'cinema-press' ),
				'update_item'                => __( 'Update Actor', 'cinema-press' ),
				'add_new_item'               => __( 'Add New Actor', 'cinema-press' ),
				'new_item_name'              => __( 'New Actor', 'cinema-press' ),
				'template_name'              => __( 'Actors', 'cinema-press' ),
				'separate_items_with_commas' => __( 'Separate Actors with commas', 'cinema-press' ),
				'add_or_remove_items'        => __( 'Add or remove Actors', 'cinema-press' ),
				'choose_from_most_used'      => __( 'Choose from the most used Actors', 'cinema-press' ),
				'not_found'                  => __( 'No Actors found. Maybe create one', 'cinema-press' ),
				'filter_by_item'             => __( 'Filter by Actor', 'cinema-press' ),
				'back_to_items'              => __( 'Back to Actors', 'cinema-press' ),
				'item_link'                  => __( 'Actor Link', 'cinema-press' ),
				'item_link_description'      => __( 'A link to a Actor', 'cinema-press' ),
			),
			'public'             => true,
			'show_in_menu'       => current_user_can( "edit_others_posts" ),
			'show_in_nav_menus'  => false,
			'rest_namespace'     => 'cinema-press',
			'show_tagcloud'      => false,
			'show_in_quick_edit' => false,
			'show_admin_column'  => true,
			'rewrite'            => array(
				'slug'       => 'actor',
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