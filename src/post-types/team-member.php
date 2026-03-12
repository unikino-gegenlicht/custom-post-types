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
				'visible' => [ 'status', 'in', ['former', 'hidden_former'] ],
			],
			[
				'type'     => 'number',
				'id'       => 'left_in',
				'inline'   => true,
				'step'     => 1,
				'min'      => 0,
				'visible' => [ 'status', 'in', ['former', 'hidden_former'] ],
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
			],
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

	return array_map(function($x) {return $x->ID;}, $hidden_teamies->posts);
}
