<?php
/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

namespace CinemaPress\post_types;

use CinemaPress\interfaces\Post_Type;

class Team_Member implements Post_Type {

	public const string SLUG = 'team_member';

	/**
	 * @inheritDoc
	 */
	public static function register_data(): array {
		return array(
			'label'               => __( "Team Members", "cine-press" ),
			'labels'              => array(
				'name'                     => __( "Team Members", "cine-press" ),
				'singular_name'            => __( "Team Member", "cine-press" ),
				'add_new'                  => __( "Add Team Member", "cine-press" ),
				'add_new_item'             => __( "Add Team Member", "cine-press" ),
				'edit_item'                => __( "Edit Team Member", "cine-press" ),
				'new_item'                 => __( "New Team Member", "cine-press" ),
				'view_item'                => __( "View Team Member", "cine-press" ),
				'view_items'               => __( "View Team Members", "cine-press" ),
				'search_items'             => __( "Search Team Members", "cine-press" ),
				'not_found'                => __( "No Team Members found", "cine-press" ),
				'not_found_in_trash'       => __( "No Team Members found in Trash", "cine-press" ),
				'all_items'                => __( "All Team Members", "cine-press" ),
				'archives'                 => __( 'The Team', 'cine-press' ),
				'attributes'               => __( 'Member attributes', 'cine-press' ),
				'insert_into_item'         => __( 'Insert into team members', 'cine-press' ),
				'uploaded_to_this_item'    => __( 'Uploaded to this team member', 'cine-press' ),
				'set_featured_image'       => __( 'Set Teamie image', 'cine-press' ),
				'remove_featured_image'    => __( 'Remove Teamie image', 'cine-press' ),
				'use_featured_image'       => __( 'Use Teamie image', 'cine-press' ),
				'filter_items_list'        => __( 'Filter team members list', 'cine-press' ),
				'items_list_navigation'    => __( 'Team members list navigation', 'cine-press' ),
				'items_list'               => __( 'Team members list', 'cine-press' ),
				'item_published'           => __( 'Team member published', 'cine-press' ),
				'item_published_privately' => __( 'Team member published privately', 'cine-press' ),
				'item_reverted_to_draft'   => __( 'Team member reverted to draft', 'cine-press' ),
				'item_trashed'             => __( 'Team member trashed', 'cine-press' ),
				'item_scheduled'           => __( 'Team member scheduled', 'cine-press' ),
				'item_updated'             => __( 'Team member updated', 'cine-press' ),
				'item_link'              => __( 'Team member linked', 'cine-press' ),
				'item_link_description'    => __( 'Link to a team member', 'cine-press' ),
			),
			'public'              => true,
			'hierarchical'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => true,
			'show_in_rest'        => true,
			'rest_namespace'      => 'cinema-press',
			'supports'            => array( 'title', 'thumbnail', 'revisions', 'autosave' ),
			'has_archive'         => 'team',
			'rewrite'             => array(
				'slug'       => 'teamie',
				'with_front' => false,
				'feeds'      => false,
				'pages'      => false,
			),
			'menu_position'       => - 10,
			'menu_icon'           => 'dashicons-buddicons-buddypress-logo',
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function meta_boxes( array $meta_boxes ): array {
		$meta_boxes[] = array(
			'title'       => __( 'Membership Status', "cine-press" ),
			'id'          => 'cinema_press-membership_status',
			'context'     => 'normal',
			'style'       => 'seamless',
			'post_types'  => array( 'team_member' ),
			'autosave'    => true,
			'revision'    => true,
			'translation' => 'advanced',
			'fields'      => array(
				[
					'type' => 'heading',
					'name' => __( 'Membership Status', "cine-press" ),
				],
				[
					'id'      => 'status',
					'type'    => 'radio',
					'label'   => __( 'Status', "cine-press" ),
					'options' => array(
						'active'   => __( 'Active', "cine-press" ),
						'former'   => __( 'Former', "cine-press" ),
						'honorary' => __( 'Honorary', "cine-press" ),
					),
					'translation' => 'copy',
				],
				[
					'type' => 'heading',
					'name' => __( 'Membership Details', "cine-press" ),
				],
				[
					'id'   => 'joined_in',
					'type' => 'number',
					'name' => __( 'Joined in', "cine-press" ),
					'std'  => intval( date( 'Y' ) ),
					'step' => 1,
					'min'  => 0,
					'translation' => 'copy',
				],
				[
					'id'      => 'left_in',
					'type'    => 'number',
					'name'    => __( 'Left in', "cine-press" ),
					'step'    => 1,
					'min'     => 0,
					'visible' => [ 'status', 'in', [ 'former', 'honorary' ] ],
					'translation' => 'copy',
				],
				[
					'id'      => 'honorary_since',
					'type'    => 'number',
					'name'    => __( 'Honorary Member since', "cine-press" ),
					'step'    => 1,
					'min'     => 0,
					'visible' => [ 'status', 'honorary' ],
					'translation' => 'copy',
				],
				[
					'type' => 'heading',
					'name' => __( 'Social Media Accounts', "cine-press" ),
					'desc' => __( 'If you enter a handle to a social media account the account might be linked in themes and schema data. The social media profiles listed here are the most commonly used ones. If you are missing one, just contact <a href="mailto:it@gegenlicht.net">it@gegenlicht.net</a> and the social media profile might be added in a future release', 'cine-press' ),
				],
				[
					'id'      => 'letterboxd_username',
					'type'    => 'text',
					'name'    => __( 'Letterboxd Username', "cine-press" ),
					'prepend' => "https://letterboxd.com/",
					'translation' => 'copy',
				],
				[
					'id'      => 'instagram_username',
					'type'    => 'text',
					'name'    => __( 'Instagram Username', "cine-press" ),
					'prepend' => "https://instagram.com/",
					'translation' => 'copy',
				],
				[
					'id'      => 'trakt.tv_username',
					'type'    => 'text',
					'name'    => __( 'Trakt TV Username', "cine-press" ),
					'prepend' => "https://app.trakt.tv/profile/",
					'translation' => 'copy',
				],
				[
					'type' => 'heading',
					'name' => __( 'Personalized Teamie Descriptions', "cine-press" ),
					'desc' => __( 'The description that has been entered here will override the default text set in the plugin settings', 'cine-press' ),
				],
				[
					'id'                    => 'description',
					'type'                  => 'wysiwyg',
					'add_to_wpseo_analysis' => true,
					'dfw'                   => false,
					'options'               => CinemaPress_WYSIWYG_OPTIONS,
					'textarea_rows'         => 20,
					'translation'           => 'translate'
				]
			)
		);

		return $meta_boxes;
	}
}