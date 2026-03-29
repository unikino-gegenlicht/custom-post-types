<?php
/*
 * Copyright (c) 2024. Philip Kaufmann, Jan Eike Suchard, Benjamin Witte
 * This file is copyright under the latest version of the EUPL.
 * Please see the LICENSE file for your rights.
 */

function ggl_post_type_team_member(): void {
	register_post_type( 'team-member', [
		'label'               => __( 'Team Members', 'ggl-post-types' ),
		'labels'              => [
			'menu_name'             => __( 'Team Members', 'ggl-post-types' ),
			'name_admin_bar'        => __( 'Team Member', 'ggl-post-types' ),
			'singular_name'         => __( 'Team Member', 'ggl-post-types' ),
			'add_new_item'          => __( 'Add Team Member', 'ggl-post-types' ),
			'add_new'               => __( 'Add Team Member', 'ggl-post-types' ),
			'edit_item'             => __( 'Edit Team Member', 'ggl-post-types' ),
			'view_item'             => __( 'View Team Member', 'ggl-post-types' ),
			'all_items'             => __( 'All Team Members', 'ggl-post-types' ),
			'featured_image'        => __( 'Photo', 'ggl-post-types' ),
			'set_featured_image'    => __( 'Set Photo', 'ggl-post-types' ),
			'remove_featured_image' => __( 'Remove Photo', 'ggl-post-types' ),
			'upload_featured_image' => __( 'Upload Photo', 'ggl-post-types' ),
		],
		'public'              => true,
		'has_archive'         => 'team',
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'can_export'          => true,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'menu_position'       => 7,
		'menu_icon'           => 'dashicons-groups',
		'supports'            => [ 'title', 'thumbnail', 'revisions', 'autosave', 'author' ],
		'rewrite'             => [
			'slug'       => 'team',
			'with_front' => true,
			'pages'      => false,
		],
	] );
}

function ggl_cpt__add_team_member_status_filter( $post_type ): void {
	if ( $post_type !== "team-member" ) {
		return;
	}

	?>
    <select name="member_status">
        <option value="" <?= selected( "all", @ $_GET["member_status"] ) ?>><?= esc_html__( "Active and Former", "ggl-post-types" ) ?></option>
        <option value="active" <?= selected( "active", @ $_GET["member_status"] ) ?>><?= esc_html__( "Active only", "ggl-post-types" ) ?></option>
        <option value="former" <?= selected( "former", @ $_GET["member_status"] ) ?>><?= esc_html__( "Former only", "ggl-post-types" ) ?></option>
    </select>
	<?php
}

function ggl_cpt__apply_team_member_status_filter( WP_Query $query ) {
	$cs = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	// make sure we are on the right admin page
	if ( ! is_admin() || empty( $cs->post_type ) || $cs->post_type != 'team-member' || $cs->id != 'edit-team-member' ) {
		return;
	}

	if ( @ $_GET['member_status'] != - 1 && @ $_GET['member_status'] !== null && @ $_GET['member_status'] !== "" ) {
		$status = @ $_GET['member_status'] ?: null;
		$query->set( 'meta_query', array( [ 'key' => 'status', 'value' => @ $status ] ) );
	}

}

function team_member_register_meta_boxes( $meta_boxes ) {
	$prefix = 'team-member_';

	$meta_boxes[] = [
		'title'      => esc_html__( 'Membership Type', 'ggl-post-types' ),
		'id'         => 'membership_information',
		'context'    => 'before_permalink',
		'style'      => 'seamless',
		'revision'   => true,
		'post_types' => [ 'team-member' ],
		'fields'     => [
			[
				'type' => 'heading',
				'name' => esc_html__( 'Membership Type', 'ggl-post-types' ),
			],
			[
				'type'     => 'radio',
				'id'       => 'status',
				'inline'   => true,
				'options'  => [
					'active'        => esc_html__( 'Active', 'ggl-post-types' ),
					'former'        => esc_html__( 'Former', 'ggl-post-types' ),
					'hidden_active' => esc_html__( 'Active (Hidden)', 'ggl-post-types' ),
					'hidden_former' => esc_html__( 'Former (Hidden)', 'ggl-post-types' ),
				],
				'revision' => true,
				'desc'     => __( "If a member status is set to hidden it will not be shown on the team member page, but is still selectable in the backend", "ggl-post-types" ),
			],
			[
				'type' => 'heading',
				'name' => esc_html__( 'Joined In', 'ggl-post-types' ),
			],
			[
				'type'     => 'number',
				'id'       => 'joined_in',
				'inline'   => true,
				'std'      => (int) date( 'Y' ),
				'step'     => 1,
				'min'      => 0,
				'revision' => true
			],
			[
				'type'    => 'heading',
				'name'    => esc_html__( 'Left In', 'ggl-post-types' ),
				'visible' => [ 'status', 'in', [ 'former', 'hidden_former' ] ],
			],
			[
				'type'     => 'number',
				'id'       => 'left_in',
				'inline'   => true,
				'step'     => 1,
				'min'      => 0,
				'visible'  => [ 'status', 'in', [ 'former', 'hidden_former' ] ],
				'revision' => true
			]
		],
	];

	$meta_boxes[] = [
		'title'      => esc_html__( "Teamie Description", 'ggl-post-types' ),
		'id'         => 'team-description',
		'context'    => 'before_permalink',
		'style'      => 'seamless',
		'revision'   => true,
		'post_types' => [ 'team-member' ],
		'tabs'       => [
			'german'  => [
				"label" => __( "German" ),
			],
			'english' => [
				"label" => __( "English" ),
			]
		],
		'fields'     => [
			[
				'type' => 'heading',
				'name' => esc_html__( 'Description', 'ggl-post-types' ),
				"desc" => esc_html__( "The following description will be displayed after the generic text which is displayed for everyone. Use this text to introduce yourself or what drove you to us. This text will usually be filled after the first semester you are with us.", "ggl-post-types" ),
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'description',
				'required'              => false,
				'add_to_wpseo_analysis' => true,
				'dfw'                   => false,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS,
				'revision'              => true,
				'textarea_rows'         => 5,
				'tab'                   => "german"
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'description_en',
				'required'              => false,
				'add_to_wpseo_analysis' => true,
				'dfw'                   => false,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS,
				'revision'              => true,
				'textarea_rows'         => 5,
				'tab'                   => "english"
			]
		]
	];

	$meta_boxes[] = [
		'title'      => esc_html__( 'Archival Data', 'ggl-post-types' ),
		'id'         => 'manual-archive',
		'context'    => 'before_permalink',
		'style'      => 'seamless',
		'revision'   => true,
		'post_types' => [ 'team-member' ],
		'fields'     => [
			[
				'type' => 'heading',
				'name' => esc_html__( 'Archival Data', 'ggl-post-types' ),
			],
			[
				'type'        => 'key_value',
				'id'          => $prefix . 'shown_movies',
				'desc'        => esc_html__( 'When filling in this archival list the given values are added to the automatically read values', 'ggl-post-types' ),
				'placeholder' => [
					'key'   => 'Year',
					'value' => 'Movie/Event Name',
				],
				'revision'    => true
			],
		],
	];

	return $meta_boxes;
}


add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', 'ggl_cpt__remove_hidden_members_from_sitemap' );
function ggl_cpt__remove_hidden_members_from_sitemap(): array {
	$hidden_teamies = new WP_Query( [
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'meta_query'     => [
			[
				"key"     => "status",
				"value"   => [ "hidden_active", "hidden_former" ],
				'compare' => 'IN',
			]
		],
		'orderby'        => 'title',
		'order'          => 'ASC',
	] );

	return array_map( function ( $x ) {
		return $x->ID;
	}, $hidden_teamies->posts );
}


/**
 * Get the name of the team member
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *   Defaults to global `$post`
 *
 * @return string
 */
function ggl_get_teamie_name( int|WP_Post $post = 0 ): string {
	$post = get_post( $post, filter: 'display' );
	if ( $post->post_type != "team-member" ) {
		return "";
	}

	return $post->post_title;
}

/**
 * Output the team member's name
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *    Defaults to global `$post`
 *
 * @return void
 */
function ggl_the_teamie_name( int|WP_Post $post = 0 ): void {
	echo ggl_get_teamie_name( $post );
}

function ggl_is_teamie_active( int|WP_Post $post = 0 ): bool {
	$post = get_post( $post, filter: 'display' );
	if ( $post->post_type != "team-member" ) {
		return false;
	}

	return str_ends_with( get_post_meta( $post->ID, "status", true ), "active" );
}


/**
 * Get the URL for the teamie picture or get the fallback variant
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return string The URL pointing to the picture
 */
function ggl_get_teamie_image_url( int|WP_Post $post = 0 ): string {
	$post = get_post( $post, filter: 'display' );
	if ( $post->post_type != "team-member" ) {
		return "";
	}

	$anonymous_image = rwmb_meta( "teamie_anonymous_image", [ "object_type" => "setting" ], "ggl_cpt__settings" );

	return get_the_post_thumbnail_url( $post, "member-crop" ) ?: $anonymous_image["sizes"]["member-crop"]["url"] ?? $anonymous_image["full_url"];
}

/**
 * Output the markup for the team members image
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 * @param string $classes Optional. The CSS classes the printed <picture> element should have
 *     Defaults to `image is-3by4 member-picture`
 * @param string $min_height Optional. The minimum heigt of the image. Per default empty
 *
 * @return void
 */
function ggl_the_teamie_image( int|WP_Post $post = 0, string $classes = "image is-3by4 member-picture", string $min_height = "" ): void {
	$url         = ggl_get_teamie_image_url( $post );
	$teamie_name = ggl_get_teamie_name( $post );
	$title       = sprintf( __( "This beautiful person is %s", "ggl-post-types" ), $teamie_name );
	echo "<picture class='$classes' title='$title'" . ( ! empty( trim( $min_height ) ) ? ' style="min-height: ' . trim( $min_height ) . ' !important;">' : ">" );
	echo "<img src='$url' alt=''/>";
	echo "</picture>";
}

/**
 * Get the year in which the teamie joined the GEGENLICHT
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return int Year in which the teamie joined
 */
function ggl_get_teamie_joined_in( int|WP_Post $post = 0 ): int {
	$post = get_post( $post, filter: 'display' );
	if ( $post->post_type != "team-member" ) {
		return - 1;
	}

	return intval( get_post_meta( $post->ID, "joined_in", true ) );
}

/**
 * Output the year since the teamie is a member
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return void
 */
function ggl_teamie_joined_in( int|WP_Post $post = 0 ): void {
	/* translators: %d year in which the teamie joined */
	echo sprintf( __( "since %d", "ggl-post-types" ), ggl_get_teamie_joined_in( $post ) );
}


/**
 * Get the year in which the teamie left the GEGENLICHT
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return int Year
 */
function ggl_get_teamie_left_in( int|WP_Post $post = 0 ): int {
	$post = get_post( $post, filter: 'display' );
	if ( $post->post_type != "team-member" ) {
		return - 1;
	}

	return intval( get_post_meta( $post->ID, "left_in", true ) );
}

function ggl_the_teamie_membership_duration( int|WP_Post $post = 0 ): void {
	$post      = get_post( $post, filter: 'display' );
	$joined_in = ggl_get_teamie_joined_in( $post );
	$left_in   = ggl_get_teamie_left_in( $post );

	echo $joined_in == $left_in ? $joined_in : "$joined_in &ndash; $left_in";
}

/**
 * Get a localized variant of the Teamie Description
 *
 * The function will automatically return the description for a team member.
 * As the description supports a German and an English version for the
 * description, the function will automatically determine the correct language
 * required for the returned content.
 *
 * If the provided post object is not of the type `team-member` the function
 * will return an empty string.
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *  Defaults to global `$post`
 *
 * @return string The description of the teamie
 *
 * @since 3.9.0
 */
function ggl_get_teamie_description( int|WP_Post $post = 0 ): string {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( $post->post_type !== "team-member" ) {
		return "";
	}

	$desired_language = substr( get_user_locale(), 0, 2 );
	$meta_key         = match ( $desired_language ) {
		"de" => "description",
		default => "description_en",
	};


	$customized_description = get_post_meta( $post->ID, $meta_key, true );
	if ( mb_trim( $customized_description ) === "" ) {
		$replacement_data = [
			"{name}"        => ggl_get_title( $post ),
			"{joined-in}"   => ggl_get_teamie_joined_in( $post ),
			"{left-in}"     => ggl_get_teamie_left_in( $post ),
			"{movie-count}" => sprintf( _n( "%s movie", "%s movies", ggl_get_teamie_movie_count( $post ), "ggl-post-types" ), number_format_i18n( ggl_get_teamie_movie_count( $post ) ) ),
		];

		$teamie_status    = get_post_meta( $post->ID, "status", true );
		$is_former_member = str_ends_with( $teamie_status, "former" );
		if ( $is_former_member ) {
			$template = match ( $desired_language ) {
				"de" => rwmb_meta( "former_teamie_generic_description_de", [ "object_type" => "setting" ], "ggl_cpt__settings" ),
				default => rwmb_meta( "former_teamie_generic_description_en", [ "object_type" => "setting" ], "ggl_cpt__settings" )
			};
		} else {
			$template = match ( $desired_language ) {
				"de" => rwmb_meta( "teamie_generic_description_de", [ "object_type" => "setting" ], "ggl_cpt__settings" ),
				default => rwmb_meta( "teamie_generic_description_en", [ "object_type" => "setting" ], "ggl_cpt__settings" )
			};
		}

		return str_replace( array_keys( $replacement_data ), array_values( $replacement_data ), $template );
	}

	return get_post_meta( $post->ID, $meta_key, true );
}

/**
 * Output the localized version of the teamie description
 *
 * @param int|WP_Post $post
 *
 * @return void
 *
 * @see ggl_get_teamie_description()
 */
function ggl_the_teamie_description( int|WP_Post $post = 0 ): void {
	echo apply_filters( "the_content", ggl_get_teamie_description( $post ) );
}

/**
 * Get the movies that are associated to the teamie
 *
 * The list will contain any movie that has the team member listed as a proposing party and that is displayed as
 * selected by a teamie
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return array
 */
function ggl_get_teamie_movies( int|WP_Post $post = 0 ): array {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( $post->post_type !== "team-member" ) {
		return [];
	}

	$query = new WP_Query( [
		"posts_per_page" => - 1,
		"post_type"      => "movie",
		"meta_query" => [
			[
				"key" => "selected_by",
                "value" => "member"
			],
            [
                "key" => "team_member_id",
                "value" => [$post->ID],
                "compare" => "IN"
            ]
		]
	] );
    return $query->posts;
}


