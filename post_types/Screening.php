<?php
/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

namespace CinemaPress\post_types;

use CinemaPress\interfaces\Post_Type;
use WP_Query;
use function CinemaPress\cp_language_mapping;

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
			'supports'            => array( 'revisions', 'thumbnail', 'autosave', 'authors' ),
			'has_archive'         => 'archiv',
			'rewrite'             => array(
				'slug'       => 'screening',
				'with_front' => false,
				'feeds'      => false,
				'pages'      => false,
			),
			'query_var'           => 'screening',
			'delete_with_user'    => false,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-calendar'
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {

		add_filter( "rwmb_ajax_get_posts", function ( $items, $field, $request ) {
			/*var_dump( $items );
			var_dump($field);
			var_dump( $request );*/
			if ( $field["id"] !== 'movie' ) {
				return $items;
			}

			$args = $field["query_args"];
			unset( $args["s"] );
			$args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => 'original_title',
					'compare' => 'LIKE',
					'value'   => $field["query_args"]['s'],
				),
				array(
					'key'     => 'german_title',
					'compare' => 'LIKE',
					'value'   => $field["query_args"]['s'],
				),
				array(
					'key'     => 'english_title',
					'compare' => 'LIKE',
					'value'   => $field["query_args"]['s'],
				)
			);
			$query              = new WP_Query( $args );
			$posts              = $query->posts;

			$locale = get_user_locale( get_current_user_id() );

			$options = [];
			foreach ( $posts as $post ) {
				if ( ! current_user_can( 'read_post', $post->ID ) ) {
					continue;
				}

				$label = str_starts_with( $locale, "de" ) ? rwmb_get_value( 'german_title', post_id: $post->ID ) : rwmb_get_value( 'english_title', post_id: $post->ID );
				$label .= " (" . rwmb_get_value( 'release_year', post_id: $post->ID ) . ")";

				$option = [
					'value'  => $post->ID,
					'label'  => $label,
					'parent' => $post->post_parent,
				];

				$options[ $post->ID ] = $option;
			}

			return array_values( $options );
		}, 99, 3 );


		$available_proposer_types = [
			( get_option( 'cinema-press' )["enable_teamie_screening_assignment"] ?? 0 ) === 1 ? 'team_member' : null,
			( get_option( 'cinema-press' )["enable_cooperation_screening_assignment"] ?? 0 ) === 1 ? 'cooperation_partner' : null
		];
		$available_proposer_types = array_filter( $available_proposer_types, function ( $type ) {
			return $type !== null;
		} );

		$meta_boxes[] = array(
			'title'      => __( 'Screening Details', 'cinema-press' ),
			'id'         => 'cinema_press-screening_details',
			'context'    => 'normal',
			'style'      => 'seamless',
			'post_types' => array( 'screening' ),
			'autosave'   => true,
			'revision'   => true,
			'fields'     => array(
				[
					'id'                => 'movie',
					'type'              => 'post',
					'name'              => __( 'Movie', 'cinema-press' ),
					'label_description' => __( 'Select the movie that will be screened', 'cinema-press' ),
					'placeholder'       => __( 'Select a movie', 'cinema-press' ),
					'required'          => true,
					'revision'          => true,
					'post_type'         => 'movie',
					'query_args'        => array(
						'post_status'    => 'publish',
						'posts_per_page' => 10,
					),
					'js_options'        => [
						'minimumInputLength' => 1,
					],
					'ajax'              => true,
					'add_new'           => true,
					'admin_columns'     => array(
						'position' => 'replace title',
						'title'    => __( 'Movie', 'cinema-press' ),
					)
				],
				[
					'id'                => 'screening_start',
					'type'              => 'datetime',
					'name'              => __( 'Start Date and Time', 'cinema-press' ),
					'label_description' => __( 'The time the program for the screening starts', 'cinema-press' ),
					'required'          => true,
					'revision'          => true,
					'timestamp'         => false,
					'js_options'        => array(
						'dateFormat' => 'dd.mm.yy'
					),
					'save_format'       => 'Y-m-d H:i',
					'admin_columns'     => array(
						'position' => 'before movie',
						'sort'     => 'numeric',
						'title'    => __( 'Date and Time', 'cinema-press' ),
					)
				],
				[
					'id'                => 'program_series',
					'type'              => 'taxonomy',
					'name'              => __( 'Program Series', 'cinema-press' ),
					'label_description' => __( 'Select the program series the screening is accounted to. The default value is the main program series.', 'cinema-press' ),
					'placeholder'       => __( 'Select a program series', 'cinema-press' ),
					'required'          => true,
					'revision'          => true,
					'taxonomy'          => 'program_series',
					'std'               => get_term_by( 'slug', get_taxonomy('program_series')->default_term['slug'], 'program_series')->term_id,
				],
				[
					'id'                => 'semester',
					'type'              => 'taxonomy',
					'name'              => __( 'Semester', 'cinema-press' ),
					'label_description' => __( 'Choose the semester that the screening happens in', 'cinema-press' ),
					'placeholder'       => __( 'Select a semester', 'cinema-press' ),
					'required'          => true,
					'revision'          => true,
					'taxonomy'          => 'semester',
					'query_args'        => [
						'number'   => 5,
						'orderby'  => 'meta_value',
						'meta_key' => 'semester_start',
						'order'    => 'desc'
					],
					'std'               => count( get_terms( 'semester', [
						'number'   => 1,
						'orderby'  => 'meta_value',
						'meta_key' => 'semester_start',
						'order'    => 'desc'
					] ) ) > 0 ? get_terms( 'semester', [
						'number'   => 1,
						'orderby'  => 'meta_value',
						'meta_key' => 'semester_start',
						'order'    => 'desc'
					] )[0]->term_id : null,
				],
				[
					'id'                => 'location',
					'type'              => 'post',
					'name'              => __( 'Screening Location', 'cinema-press' ),
					'label_description' => __( 'The location the screening will happen in', 'cinema-press' ),
					'placeholder'       => __( 'Select a location', 'cinema-press' ),
					'required'          => true,
					'revision'          => true,
					'post_type'         => 'screening_location',
					'query_args'        => array(
						'post_status'    => 'publish',
						'posts_per_page' => 10
					),
					'ajax'              => true,
					'add_new'           => true,
					'admin_columns'     => array(
						'position'   => 'after date',
						'title'      => __( "Screening Location", 'cinema-press' ),
						'sort'       => false,
						'searchable' => false,
						'link'       => false
					)
				],
				[
					'id'                => 'admission_types',
					'type'              => 'post',
					'name'              => __( 'Admission Types', 'cinema-press' ),
					'label_description' => __( 'The admission types that are being offered for this screening', 'cinema-press' ),
					'placeholder'       => __( 'Select one or more admission types', 'cinema-press' ),
					'required'          => true,
					'revision'          => true,
					'post_type'         => 'admission_type',
					'query_args'        => array(
						'post_status'    => 'publish',
						'posts_per_page' => 10
					),
					'ajax'              => true,
					'multiple'          => true,
					'admin_columns'     => 'after screening_location'
				],
				( ! empty( $available_proposer_types ) ) ? [
					'id'                => 'proposed_by',
					'type'              => 'post',
					'name'              => __( 'Proposed By', 'cinema-press' ),
					'label_description' => __( 'Select the team member and/or cooperation partner that has proposed the movie', 'cinema-press' ),
					'post_type'         => $available_proposer_types,
					'field_type'        => 'select_advanced',
					'query_args'        => array(
						'post_status'    => 'publish',
						'posts_per_page' => 10
					),
					'ajax'              => true,
					'add_new'           => true,
					'multiple'          => true,
					'admin_columns'     => array(
						'position'   => 'after date',
						'title'      => __( "Proposed By", 'cinema-press' ),
						'sort'       => false,
						'searchable' => false,
						'link'       => false
					)
				] : [
					'type' => 'custom_html',
					'std'  => ""
				],
				[
					'id'      => 'audio_language',
					'type'    => 'select_advanced',
					'name'    => __( 'Audio Language', 'cinema-press' ),
					'desc'    => __( 'Select the audio language this screening will happen in', 'cinema-press' ),
					'options' => cp_language_mapping()
				],
				[
					'id'        => 'has_subtitles',
					'type'      => 'switch',
					'name'      => __( 'Screening with Subtitles', 'cinema-press' ),
					'on_label'  => __( 'Yes', 'cinema-press' ),
					'off_label' => __( 'No', 'cinema-press' )
				],
				[
					'id'      => 'subtitle_language',
					'type'    => 'select_advanced',
					'name'    => __( 'Subtitle Language', 'cinema-press' ),
					'options' => cp_language_mapping(),
					'visible' => [ 'has_subtitles' ]
				],
				[
					'type' => 'heading',
					'name' => __( "Summary", 'cinema-press' ),
					'desc' => __( 'Below this field you can now enter the movies summary for the screening', 'cinema-press' ),
				],
				[
					'id'                    => 'summary',
					'type'                  => 'wysiwyg',
					'required'              => true,
					'add_to_wpseo_analysis' => true,
					'options'               => CinemaPress_WYSIWYG_OPTIONS,
					'revision'              => true,
				],
				[
					'type' => 'heading',
					'name' => __( "Why is the movie worth to be seen?", 'cinema-press' ),
					'desc' => __( 'Below this field you can now enter why the movie is worth to be seen by your visitors', 'cinema-press' ),
				],
				[
					'id'                    => 'worth_to_be_seen',
					'type'                  => 'wysiwyg',
					'required'              => true,
					'add_to_wpseo_analysis' => true,
					'options'               => CinemaPress_WYSIWYG_OPTIONS,
					'revision'              => true,
				],
			)
		);

		return $meta_boxes;
	}
}