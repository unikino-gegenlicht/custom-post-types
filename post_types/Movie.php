<?php
/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

namespace CinemaPress\post_types;

use CinemaPress\interfaces\Post_Type;
use function CinemaPress\cp_county_mapping;

class Movie implements Post_Type {
	const string SLUG = 'movie';

	/**
	 * @inheritDoc
	 */
	public static function register_data(): array {
		return array(
			'label'               => __( "Movie", 'cinema-press' ),
			'labels'              => array(
				'name'                     => __( "Movies", 'cinema-press' ),
				'singular_name'            => __( "Movie", 'cinema-press' ),
				'add_new'                  => __( "Add Movie", 'cinema-press' ),
				'add_new_item'             => __( "Add Movie", 'cinema-press' ),
				'edit_item'                => __( "Edit Movie", 'cinema-press' ),
				'new_item'                 => __( "New Movie", 'cinema-press' ),
				'view_item'                => __( "Show Movie", 'cinema-press' ),
				'view_items'               => __( "Show Movies", 'cinema-press' ),
				'search_items'             => __( "Search Movies", 'cinema-press' ),
				'not_found'                => __( "No Movies found", 'cinema-press' ),
				'not_found_in_trash'       => __( "No Movies found in Trash", 'cinema-press' ),
				'all_items'                => __( "All Movies", 'cinema-press' ),
				'archives'                 => __( "Screened Movies", 'cinema-press' ),
				'attributes'               => __( "Movie Attributes", 'cinema-press' ),
				'insert_into_item'         => __( "Insert into Movie", 'cinema-press' ),
				'uploaded_to_this_item'    => __( "Uploaded to this Movie", 'cinema-press' ),
				'featured_image'           => __( "Movie Logo", 'cinema-press' ),
				'set_featured_image'       => __( "Set movie logo", 'cinema-press' ),
				'remove_featured_image'    => __( "Remove movie logo", 'cinema-press' ),
				'use_featured_image'       => __( "Use movie logo for this movie", 'cinema-press' ),
				'filter_items_list'        => __( "Filter Movie List", 'cinema-press' ),
				'filter_by_date'           => __( "Filter by screening date", 'cinema-press' ),
				'items_list_navigation'    => __( "Movie List Navigation", 'cinema-press' ),
				'items_list'               => __( "Movie List", 'cinema-press' ),
				'item_published'           => __( "Movie Published", 'cinema-press' ),
				'item_published_privately' => __( 'Movie Published Privately', 'cinema-press' ),
				'item_reverted_to_draft'   => __( "Movie Reverted to Draft", 'cinema-press' ),
				'item_trashed'             => __( "Movie Trashed", 'cinema-press' ),
				'item_scheduled'           => __( "Movie Scheduled", 'cinema-press' ),
				'item_updated'             => __( "Movie Updated", 'cinema-press' ),
				'item_link'                => __( "Movie Link", 'cinema-press' ),
				'item_link_description'    => __( "A link to a movie", 'cinema-press' ),
			),
			'description'         => __( "A movie stores the data about the release year, directors, actors, producers and other data that rarely changes", 'cinema-press' ),
			'public'              => true,
			'hierarchical'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'rest_namespace'      => 'cinema-press',
			'capability_type'     => array( 'movie', 'movies' ),
			'supports'            => array( 'revisions', 'thumbnail', 'autosave' ),
			'has_archive'         => true,
			'rewrite'             => array(
				'slug'       => 'movie',
				'with_front' => false,
				'feeds'      => false,
				'pages'      => false,
			),
			'query_var'           => false,
			'delete_with_user'    => false,
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {
		$meta_boxes[] = array(
			'title'      => __( 'Movie Data', 'cinema-press' ),
			'id'         => 'cinema_press-movie_base_data',
			'context'    => 'form_top',
			'style'      => 'seamless',
			'post_types' => array( 'movie' ),
			'autosave'   => true,
			'revision'   => true,
			'fields'     => array(
				[
					'id'            => 'german_title',
					'type'          => 'text',
					'name'          => __( 'German Title', 'cinema-press' ),
					'desc'          => __( 'The official german title of the movie', 'cinema-press' ),
					'required'      => true,
					'revision'      => true,
					'admin_columns' => array(
						'position'   => 'after title',
						'title'      => __( 'German Title', 'cinema-press' ),
						'sort'       => false,
						'searchable' => true,
						'link'       => 'edit'
					)
				],
				[
					'id'            => 'english_title',
					'type'          => 'text',
					'name'          => __( 'English Title', 'cinema-press' ),
					'desc'          => __( 'The official English title of the movie', 'cinema-press' ),
					'required'      => true,
					'revision'      => true,
					'admin_columns' => array(
						'position'   => 'after german_title',
						'title'      => __( 'English Title', 'cinema-press' ),
						'sort'       => false,
						'searchable' => true,
						'link'       => 'edit'
					)
				],
				[
					'id'       => "original_title",
					'type'     => 'text',
					'name'     => __( 'Original Title', 'cinema-press' ),
					'desc'     => __( 'The original title of the movie', 'cinema-press' ),
					'required' => true,
					'revision' => true,
				],
				[
					'id'       => "country_of_origin",
					'type'     => "select_advanced",
					'name'     => __( 'Country of Origin', 'cinema-press' ),
					'desc'     => __( 'Select the country/countries of origin for this movie', 'cinema-press' ),
					'required' => true,
					'revision' => true,
					'options'  => cp_county_mapping()
				],
				[
					'id'       => 'german_release_year',
					'type'     => 'number',
					'name'     => __( 'Release Year in Germany', 'cinema-press' ),
					'required' => true,
					'revision' => true,
					'std'      => intval( date( 'Y' ) ),
					'min'      => 0,
					'step'     => 1
				],
				[
					'id'       => 'local_release_year',
					'type'     => 'number',
					'name'     => __( 'Release Year in the Country of Origin', 'cinema-press' ),
					'desc'     => __( 'In case of multiple countries of origin, select the earliest year you can find.', 'cinema-press' ),
					'required' => true,
					'revision' => true,
					'std'      => intval( date( 'Y' ) ),
					'min'      => 0,
					'step'     => 1
				],
				[
					'id'       => 'running_time',
					'type'     => 'number',
					'name'     => __( 'Running Time', 'cinema-press' ),
					'desc'     => __( 'The movie\'s running time in minutes', 'cinema-press' ),
					'required' => true,
					'revision' => true,
					'min'      => 1,
					'step'     => 1
				],
				[
					'id'          => 'directors',
					'type'        => 'taxonomy',
					'name'        => __( 'Directors', 'cinema-press' ),
					'desc'        => __( 'Select the directors for the movie', 'cinema-press' ),
					'placeholder' => __( 'Select one or more directors', 'cinema-press' ),
					'required'    => true,
					'revision'    => true,
					'taxonomy'    => 'director',
					'query_args'  => array(
						'number' => 10,
					),
					'ajax'        => true,
					'add_new'     => true,
					'multiple'    => true,
				],
				[
					'id'         => 'actors',
					'type'       => 'taxonomy',
					'name'       => __( 'Starring Actors', 'cinema-press' ),
					'desc'       => __( 'Select up to 5 actors for the movie', 'cinema-press' ),
					'required'   => true,
					'revision'   => true,
					'taxonomy'   => 'actor',
					'query_args' => array(
						'number' => 10,
					),
					'js_options' => array(
						'maximumSelectionLength' => 5,
						'minimumInputLength'     => 1,
					),
					'ajax'       => true,
					'add_new'    => true,
					'multiple'   => true,
				],
				[
					'id'            => 'age_rating',
					'type'          => 'select',
					'name'          => __( 'Age Rating', 'cinema-press' ),
					'desc'          => __( 'Select the age rating for this movie. The official age ratings for movies are available with the <a href="https://fsk.de">FSK</a>', 'cinema-press' ),
					'required'      => true,
					'revision'      => true,
					'options'       => array(
						- 3 => __( 'rating pending', 'cinema-press' ),
						- 2 => __( 'unknown', 'cinema-press' ),
						- 1 => __( 'unrated', 'cinema-press' ),
						0   => __( 'FSK 0', 'cinema-press' ),
						6   => __( 'FSK 6', 'cinema-press' ),
						12  => __( 'FSK 12', 'cinema-press' ),
						16  => __( 'FSK 16', 'cinema-press' ),
						18  => __( 'FSK 18', 'cinema-press' ),
					),
					'std'           => - 2,
					'admin_columns' => true
				],
				[
					'id'       => 'age_rating_content_advices',
					'type'     => 'checkbox_list',
					'name'     => __( 'Content Advices by the FSK', 'cinema-press' ),
					'desc'     => __( 'Additional descriptors are published alongside the FSK age ratings for films and series.', 'cinema-press' ),
					'required' => true,
					'revision' => true,
					'options'  => array(
						'sexual_violence'           => __( 'Sexual Violence', 'cinema-press' ),
						'violence'                  => __( 'Violence', 'cinema-press' ),
						'risky_behaviour+self_harm' => __( 'Risky Behaviour and Self Harm', 'cinema-press' ),
						'drug_usage'                => __( 'Drug Usage', 'cinema-press' ),
						'exclusion+discrimination'  => __( 'Exclusion and Discrimination', 'cinema-press' ),
						'threats'                   => __( 'Threats', 'cinema-press' ),
						'sexuality'                 => __( 'Sexuality', 'cinema-press' ),
						'injury'                    => __( 'Injury', 'cinema-press' ),
						'difficult_topics'          => __( 'Difficult Topics', 'cinema-press' ),
						'disturbing_scenes'         => __( 'Disturbing Scenes', 'cinema-press' ),
						'language'                  => __( 'Language', 'cinema-press' ),
						'nudity'                    => __( 'Nudity', 'cinema-press' ),
					),
					'visible'  => [ 'age_rating', '>', 0 ]
				]
			)
		);

		return $meta_boxes;
	}
}