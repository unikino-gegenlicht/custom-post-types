<?php
/**
 * This file defines the custom post type `movie` and contains functions related
 * to the retrieval of meta information about the movies
 */

function ggl_post_type_movie(): void {
	register_post_type( 'movie', [
		'label'               => __( 'Movies', 'ggl-post-types' ),
		'labels'              => [
			'menu_name'          => __( 'Movies', 'ggl-post-types' ),
			'name_admin_bar'     => __( 'Movie', 'ggl-post-types' ),
			'singular_name'      => __( 'Movie', 'ggl-post-types' ),
			'add_new_item'       => __( 'Add Movie', 'ggl-post-types' ),
			'add_new'            => __( 'Add Movie', 'ggl-post-types' ),
			'edit_item'          => __( 'Edit Movie', 'ggl-post-types' ),
			'view_item'          => __( 'Show Movie', 'ggl-post-types' ),
			'search_items'       => __( 'Search Movies', 'ggl-post-types' ),
			'not_found'          => __( 'No Movies found', 'ggl-post-types' ),
			'not_found_in_trash' => __( 'No Movies found in Trash', 'ggl-post-types' ),
			'all_items'          => __( 'All Movies', 'ggl-post-types' ),
			'archives'           => __( 'Old Movies', 'ggl-post-types' ),
		],
		'public'              => true,
		'has_archive'         => 'archive',
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'can_export'          => true,
		'show_ui'             => true, // implies show_in_menu, show_in_nav_menus, show_in_admin_bar
		'show_in_rest'        => true,
		'delete_with_user'    => false,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-editor-video',
		'supports'            => [ 'thumbnail', 'revisions', 'autosave', 'author' ],
		'taxonomies'          => [ 'semester', 'special-program', 'director', 'actor' ],
		'rewrite'             => [
			'with_front' => true,
			'pages'      => false,
		]
	] );
}

add_filter( 'manage_movie_posts_columns', function ( $columns ) {
	$columns['title'] = __( "Original Title", "ggl-post-types" );

	return $columns;
} );

function ggl_cpt__add_movie_semester_filter( $post_type ): void {
	if ( $post_type !== 'movie' ) {
		return;
	}

	$semesters = get_terms( [
		"taxonomy"   => "semester",
		"hide_empty" => true,
	] );

	$sortedSemesters = [];
	foreach ( $semesters as $semester ) {
		$startDate              = date_parse_from_format( "d.m.Y", get_term_meta( $semester->term_id, "semester_start", true ) );
		$ts                     = mktime( 0, 0, 0, $startDate['month'], $startDate['day'], $startDate['year'] ) ?: 0;
		$sortedSemesters[ $ts ] = $semester;
	}
	krsort( $sortedSemesters )
	?>
    <select name="semester">
        <option value=""><?= esc_html__( "All Semesters", "ggl-post-types" ) ?></option>
		<?php foreach ( $sortedSemesters as $semester ): ?>
            <option value="<?= $semester->slug ?>" <?= selected( $semester->slug, @ $_GET["semester"], 0 ) ?>><?= $semester->name ?></option>
		<?php endforeach; ?>
    </select>
	<?php

}

function ggl_cpt__apply_movie_semester_filter( WP_Query $query ) {
	$cs = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	// make sure we are on the right admin page
	if ( ! is_admin() || empty( $cs->post_type ) || $cs->post_type != 'movie' || $cs->id != 'edit-movie' ) {
		return;
	}

	if ( @ $_GET['semester'] != - 1 && @ $_GET['semester'] !== null && @ $_GET['semester'] !== "" ) {
		$selected_id = @ $_GET['semester'] ?: null;
		$query->set( 'tax_query', array( [ 'taxonomy' => 'semester', 'terms' => $selected_id, 'field' => "slug" ] ) );
	}

}

function ensure_numerical_movie_link( $post_id ): void {
	$parent_id = wp_is_post_revision( $post_id );

	if ( false !== $parent_id ) {
		$post_id = $parent_id;
	}

	$post = get_post( $post_id );


	$post_title = $_POST['original_title'] ?: get_post_meta( $post->ID, 'original_title', true ) ?: null;

	if ( $post->post_name == $post_id && $post->post_title == $post_title ) {
		return;
	}

	remove_action( 'save_post_movie', 'ensure_numerical_movie_link', 1 );
	wp_update_post( array(
		'ID'         => $post_id,
		'post_name'  => $post_id,
		'post_title' => $post_title,
	) );
	add_action( 'save_post_movie', 'ensure_numerical_movie_link', 1 );
}


function movie_extended_info_meta_boxes( $meta_boxes ) {
	$meta_boxes[] = [
		'title'      => esc_html__( 'Movie Metaboxes', 'ggl-post-types' ),
		'id'         => 'movie_meta',
		'context'    => 'form_top',
		'style'      => 'seamless',
		'post_types' => [ 'movie' ],
		'autosave'   => true,
		'revision'   => true,
		'tabs'       => [
			'information'      => [
				"label" => esc_html__( 'Movie Information', "ggl-post-types" ),
				"icon"  => "dashicons-info-outline"
			],
			'production'       => [
				"label" => esc_html__( 'Production Members', "ggl-post-types" ),
				"icon"  => "dashicons-groups"
			],
			'sound'            => [
				"label" => esc_html__( 'Audio and Subtitles', "ggl-post-types" ),
				"icon"  => "dashicons-controls-volumeon"
			],
			'youth-protection' => [
				"label" => esc_html__( 'Youth Protection', "ggl-post-types" ),
				"icon"  => "dashicons-privacy"
			],
			'licensing'        => [
				"label" => esc_html__( "Licensing and Admissions", "ggl-post-types" ),
				"icon"  => "dashicons-awards"
			],
			'screening'        => [
				"label" => esc_html__( "Screening Details", "ggl-post-types" ),
				"icon"  => "dashicons-calendar"
			],
			'content-notice'   => [
				"label" => esc_html__( "Content Notice", "ggl-post-types" ),
				"icon"  => "dashicons-warning"
			],
			'short-movie'      => [
				"label" => esc_html__( "Short Movie", "ggl-post-types" ),
				"icon"  => "dashicons-video-alt"
			],
			'display'          => [
				"label" => esc_html__( "Display Options", "ggl-post-types" ),
				"icon"  => "dashicons-visibility"
			]
		],
		'fields'     => [
			[
				'type'          => 'text',
				'name'          => esc_html__( 'German Title', 'ggl-post-types' ),
				'id'            => 'german_title',
				'desc'          => esc_html__( 'Please enter the German title of the movie here', 'ggl-post-types' ),
				'required'      => true,
				'revision'      => true,
				'tab'           => 'information',
				'admin_columns' => str_starts_with( get_user_locale(), "de" ) ? [
					'position'   => 'after title',
					'link'       => 'none',
					'sort'       => true,
					'searchable' => true,
					'filterable' => false,
				] : false
			],
			[
				'type'          => 'text',
				'name'          => esc_html__( 'English Title', 'ggl-post-types' ),
				'id'            => 'english_title',
				'desc'          => esc_html__( 'Please enter the English title of the movie here', 'ggl-post-types' ),
				'required'      => true,
				'revision'      => true,
				'tab'           => 'information',
				'admin_columns' => str_starts_with( get_user_locale(), "en" ) ? [
					'position'   => 'after title',
					'link'       => 'none',
					'sort'       => true,
					'searchable' => true,
					'filterable' => false,
				] : false
			],
			[
				'type'     => 'text',
				'name'     => esc_html__( 'Original Title', 'ggl-post-types' ),
				'id'       => 'original_title',
				'desc'     => __( 'Please enter the original title here. For Japanese/Chinese/etc. titles, please input the logographics and the romanized versions seperated by an em dash (<code>—</code>) surrounded by spaces', 'ggl-post-types' ),
				'required' => true,
				'revision' => true,
				'tab'      => 'information',
			],
			[
				'type'     => 'select_advanced',
				'name'     => __( 'Country/Countries of Origin', 'ggl-post-types' ),
				'id'       => 'country',
				'options'  => generate_country_mapping(),
				'multiple' => true,
				'required' => true,
				'revision' => true,
				'tab'      => 'information',

			],
			[
				'type'       => 'date',
				'name'       => esc_html__( 'Release Date', 'ggl-post-types' ),
				'id'         => 'release_date',
				'required'   => true,
				'min'        => 0,
				'revision'   => true,
				'timestamp'  => true,
				'js_options' => [
					'dateFormat' => ( str_starts_with( get_locale(), "de" ) ? 'dd.mm.yy' : "mm/dd/yy" ),
				],
				'tab'        => 'information',

			],
			[
				'type'     => 'number',
				'name'     => esc_html__( 'Running Time', 'ggl-post-types' ),
				'id'       => 'running_time',
				'desc'     => esc_html__( 'The movie\'s running time in minutes', 'ggl-post-types' ),
				'std'      => 90,
				'step'     => 1,
				'min'      => 0,
				'required' => true,
				'revision' => true,
				'tab'      => 'information',

			],
			[
				'type'        => 'taxonomy',
				'name'        => esc_html__( 'Directed by', 'ggl-post-types' ),
				'id'          => 'director',
				'taxonomy'    => 'director',
				'required'    => true,
				'field_type'  => 'select_advanced',
				'placeholder' => __( 'Select Director', 'ggl-post-types' ),
				'add_new'     => true,
				'query_args'  => [
					'number' => 10,
				],
				'ajax'        => true,
				'revision'    => true,
				'tab'         => 'production'
			],
			[
				'type'        => 'taxonomy',
				'name'        => esc_html__( 'Starring', 'ggl-post-types' ),
				'id'          => 'actors',
				'taxonomy'    => 'actor',
				'required'    => true,
				'field_type'  => 'select_advanced',
				'add_new'     => true,
				'multiple'    => true,
				'placeholder' => __( 'Select Actors', 'ggl-post-types' ),
				'query_args'  => [
					'number' => 10,
				],
				'js_options'  => [
					'maximumSelectionLength' => 2
				],
				'ajax'        => true,
				'revision'    => true,
				'tab'         => 'production'
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Audio Type', 'ggl-post-types' ),
				'id'       => 'audio_type',
				'inline'   => true,
				'required' => true,
				'options'  => [
					'original'        => esc_html__( 'Original', 'ggl-post-types' ),
					'synchronization' => esc_html__( 'Synchronization', 'ggl-post-types' ),
				],
				'default'  => 'original',
				'revision' => true,
				'tab'      => 'sound',
			],
			[
				'type'          => 'select_advanced',
				'name'          => esc_html__( 'Audio Language', 'ggl-post-types' ),
				'id'            => 'audio_language',
				'std'           => 'eng',
				'options'       => generate_language_mapping(),
				'required'      => true,
				'revision'      => true,
				'tab'           => 'sound',
				'admin_columns' => true,
			],
			[
				'type'          => 'select_advanced',
				'name'          => esc_html__( 'Subtitle Language', 'ggl-post-types' ),
				'id'            => 'subtitle_language',
				'std'           => 'deu',
				'options'       => generate_language_mapping(),
				'required'      => true,
				'revision'      => true,
				'tab'           => 'sound',
				'admin_columns' => true,
			],
			[
				'type'          => 'radio',
				'name'          => esc_html__( 'License Type', 'ggl-post-types' ),
				'id'            => 'license_type',
				'inline'        => true,
				'required'      => true,
				'options'       => [
					'full' => esc_html__( 'Advertisement License', 'ggl-post-types' ),
					'pool' => esc_html__( 'Pool License', 'ggl-post-types' ),
					'none' => esc_html__( 'No License', 'ggl-post-types' ),
				],
				'std'           => 'full',
				'revision'      => true,
				'tab'           => 'licensing',
				'admin_columns' => [
					'position'   => 'after date',
					'link'       => 'none',
					'sort'       => false,
					'searchable' => false,
					'filterable' => false,
				]
			],
			[
				"type"    => "custom_html",
				"std"     => "<p class='notice notice-warning' style='display: block'>" . esc_html__( "You selected that we will screen this movie without an advertisement license. This means that you will have to input a second text which is a anonymized version of the text in the program booklet. Please check the additional input boxes below for further information!", "ggl-post-types" ) . "</p>",
				'visible' => [ 'license_type', '!=', 'full' ],
				'tab'     => 'licensing'
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Admission Type', 'ggl-post-types' ),
				'id'       => 'admission_type',
				'inline'   => true,
				'required' => true,
				'options'  => [
					'free'     => esc_html__( 'Free', 'ggl-post-types' ),
					'donation' => esc_html__( 'Donation', 'ggl-post-types' ),
					'paid'     => esc_html__( 'Paid', 'ggl-post-types' )
				],
				'std'      => 'paid',
				'revision' => true,
				'tab'      => 'licensing'
			],
			[
				'type'     => 'number',
				'name'     => esc_html__( 'Admission Fee', 'ggl-post-types' ),
				'id'       => 'admission_fee',
				'std'      => 3.00,
				'min'      => 0,
				'step'     => 0.01,
				'visible'  => [ 'admission_type', "=", "paid" ],
				'revision' => true,
				'tab'      => 'licensing'
			],
			[
				'type' => 'custom_html',
				'std'  => '<a target="_blank" href="https://www.fsk.de/freigabensuche/" style="display: flex; background-color: #e4df00; color: #033667; text-align: center; font-size: 25px; font-weight: bold; padding: 1ex 0.5em; text-decoration: none; flex-direction: row; align-items: center; justify-content: center "><img style="padding: 0.75ex" height="25px" src="https://www.fsk.de/wp-content/uploads/fsk_logo_pos_2c_XL@2-8_M.png"/><span>' . __( "To the FSK rating search", "ggl-post-types" ) . '</span></a>',
				'tab'  => 'youth-protection'
			],
			[
				'type'          => 'select',
				'name'          => esc_html__( 'Age Rating', 'ggl-post-types' ),
				'id'            => 'age_rating',
				'options'       => [
					- 2 => esc_html__( 'unknown', 'ggl-post-types' ),
					- 1 => esc_html__( 'Not rated', 'ggl-post-types' ),
					0   => esc_html__( 'FSK 0', 'ggl-post-types' ),
					6   => esc_html__( 'FSK 6', 'ggl-post-types' ),
					12  => esc_html__( 'FSK 12', 'ggl-post-types' ),
					16  => esc_html__( 'FSK 16', 'ggl-post-types' ),
					18  => esc_html__( 'FSK 18', 'ggl-post-types' ),
				],
				'std'           => - 2,
				'required'      => true,
				'revision'      => true,
				'tab'           => 'youth-protection',
				'admin_columns' => true
			],
			[
				'type'     => 'checkbox_list',
				'name'     => esc_html__( 'Descriptors', 'ggl-post-types' ),
				'id'       => 'descriptors',
				'options'  => [
					'sexualized_violence' => esc_html__( 'Sexualized Violence', 'ggl-post-types' ),
					'violence'            => esc_html__( 'Violence', 'ggl-post-types' ),
					'self_harm'           => esc_html__( 'Self Harm', 'ggl-post-types' ),
					'drug_usage'          => esc_html__( 'Drug Usage', 'ggl-post-types' ),
					'discrimination'      => esc_html__( 'Discrimination', 'ggl-post-types' ),
					'sexuality'           => esc_html__( 'Sexuality', 'ggl-post-types' ),
					'threat'              => esc_html__( 'Threat', 'ggl-post-types' ),
					'injury'              => esc_html__( 'Injury', 'ggl-post-types' ),
					'stressful_topics'    => esc_html__( 'Stressful Topics', 'ggl-post-types' ),
					'language'            => esc_html__( 'Language', 'ggl-post-types' ),
					'nudity'              => esc_html__( 'Nudity', 'ggl-post-types' ),
					'risky_behaviour'     => esc_html__( 'Risky Behaviour', 'ggl-post-types' ),
					'marginalization'     => esc_html__( 'Marginalization', 'ggl-post-types' ),
				],
				'revision' => true,
				'tab'      => 'youth-protection'
			],
			[
				'type'          => 'taxonomy',
				'name'          => esc_html__( 'Semester', 'ggl-post-type' ),
				'id'            => 'semester',
				'placeholder'   => esc_html__( 'Select a Semester', 'ggl-post-types' ),
				'taxonomy'      => 'semester',
				'required'      => false,
				'field_type'    => 'select_advanced',
				'query_args'    => [
					'number'   => 5,
					'orderby'  => 'meta_value_num',
					'meta_key' => 'semester_start',
					'order'    => 'desc'
				],
				'std'           => count( get_terms( 'semester', [
					'number'   => 1,
					'orderby'  => 'meta_value_num',
					'meta_key' => 'semester_start',
					'order'    => 'desc'
				] ) ) > 0 ? get_terms( 'semester', [
					'number'   => 1,
					'orderby'  => 'meta_value_num',
					'meta_key' => 'semester_start',
					'order'    => 'desc'
				] )[0]->term_id : null,
				'add_new'       => current_user_can( "edit_others_posts" ),
				'ajax'          => true,
				'revision'      => true,
				'tab'           => 'screening',
				'admin_columns' => [
					'position'   => 'before date',
					'link'       => 'none',
					'sort'       => false,
					'searchable' => false,
					'filterable' => false,
				]
			],
			[
				'type'          => 'datetime',
				'name'          => esc_html__( 'Date and Time', 'ggl-post-types' ),
				'id'            => 'screening_date',
				'timestamp'     => true,
				'js_options'    => [
					'dateFormat' => ( str_starts_with( get_locale(), "de" ) ? 'dd.mm.yy' : "mm/dd/yy" ),
				],
				'required'      => true,
				'revision'      => true,
				'tab'           => 'screening',
				'admin_columns' => [
					'position'   => 'replace date',
					'title'      => __( "Screening Date", 'ggl-post-types' ),
					'link'       => 'none',
					'sort'       => true,
					'searchable' => false,
					'filterable' => false,
				]
			],
			[
				'type'       => 'post',
				'name'       => esc_html__( 'Location', 'ggl-post-types' ),
				'id'         => 'screening_location',
				'desc'       => esc_html__( 'The location the screening will take place in', 'ggl-post-types' ),
				'required'   => true,
				'field_type' => 'select_advanced',
				'post_type'  => 'screening-location',
				'query_args' => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1
				],
				'std'        => get_theme_mod( "main_screening_location" ) ?: null,
				'ajax'       => true,
				'add_new'    => false,
				'revision'   => true,
				'tab'        => 'screening'
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Selected by', 'ggl-post-types' ),
				'id'       => 'selected_by',
				'inline'   => true,
				'required' => true,
				'options'  => [
					'member' => esc_html__( 'Team Member', 'ggl-post-types' ),
					'coop'   => esc_html__( 'Cooperation Partner', 'ggl-post-types' ),
					'hidden' => esc_html__( 'Don\'t show', 'ggl-post-types' )
				],
				'std'      => 'member',
				'revision' => true,
				'tab'      => 'screening'
			],
			[
				'type'        => 'post',
				'name'        => esc_html__( 'Team Member', 'ggl-post-types' ),
				'id'          => 'team_member_id',
				'post_type'   => 'team-member',
				'field_type'  => 'select_advanced',
				'add_new'     => false,
				'placeholder' => esc_html__( 'Select a Team Member', 'ggl-post-types' ),
				'query_args'  => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'meta_query'     => [
						[
							"key"     => "status",
							"value"   => [ "active", "hidden_active" ],
							'compare' => 'IN',
						]
					],
					'orderby'        => 'title',
					'order'          => 'ASC',
				],
				'visible'     => [ 'selected_by', '=', 'member' ],
				'ajax'        => true,
				'revision'    => true,
				'multiple'    => true,
				'tab'         => 'screening',
			],
			[
				'type'        => 'post',
				'name'        => esc_html__( 'Cooperation Partner', 'ggl-post-types' ),
				'id'          => 'cooperation_partner_id',
				'post_type'   => 'cooperation-partner',
				'field_type'  => 'select_advanced',
				'placeholder' => esc_html__( 'Select a Cooperation Partner', 'ggl-post-types' ),
				'add_new'     => false,
				'query_args'  => [
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'orderby'        => 'title',
					'order'          => 'ASC',
				],
				'visible'     => [ 'selected_by', '=', 'coop' ],
				'ajax'        => true,
				'revision'    => true,
				'multiple'    => true,
				'tab'         => 'screening'
			],
			[
				'type'          => 'radio',
				'name'          => esc_html__( 'Program Type', 'ggl-post-types' ),
				'id'            => 'program_type',
				'inline'        => true,
				'required'      => true,
				'options'       => [
					'main'            => esc_html__( 'Main Program', 'ggl-post-types' ),
					'special_program' => esc_html__( 'Special Program', 'ggl-post-types' ),
				],
				'std'           => 'main',
				'revision'      => true,
				'tab'           => 'screening',
				'admin_columns' => [
					'position'   => 'after semester',
					'title'      => __( "Program Type", 'ggl-post-types' ),
					'link'       => 'none',
					'sort'       => false,
					'searchable' => false,
					'filterable' => true,
				]
			],
			[
				'type'        => 'taxonomy',
				'name'        => esc_html__( 'Special Program', 'ggl-post-types' ),
				'placeholder' => esc_html__( 'Select a Special Program', 'ggl-post-types' ),
				'id'          => 'special_program',
				'taxonomy'    => 'special-program',
				'required'    => false,
				'field_type'  => 'select_advanced',
				'add_new'     => true,
				'query_args'  => [
					'number' => 10,
				],
				'visible'     => [ 'program_type', '=', 'special_program' ],
				'ajax'        => true,
				'revision'    => true,
				'tab'         => 'screening'
			],
			[
				'type'     => 'checkbox',
				'name'     => esc_html__( 'Show Content Notice', 'ggl-post-types' ),
				'id'       => 'show_content_notice',
				'std'      => 0,
				'revision' => true,
				'desc'     => esc_html__( 'If this box is ticked the below entered content notice will be displayed above the text for this movie.', 'ggl-post-types' ),
				'tab'      => 'content-notice'
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'content_notice',
				'required'              => false,
				'add_to_wpseo_analysis' => false,
				'dfw'                   => false,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS,
				'revision'              => true,
				'tab'                   => 'content-notice',
				'textarea_rows'         => 5,
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'Advertise Short Movie', 'ggl-post-types' ),
				'id'       => 'short_movie_screened',
				'required' => true,
				'options'  => [
					'yes' => esc_html__( 'Yes', 'ggl-post-types' ),
					'no'  => esc_html__( 'No', 'ggl-post-types' ),
				],
				'std'      => 'yes',
				'revision' => true,
				'tab'      => 'short-movie'
			],
			[
				'type'     => 'text',
				'name'     => esc_html__( 'Title', 'ggl-post-types' ),
				'id'       => 'short_movie_title',
				'revision' => true,
				'tab'      => 'short-movie'
			],
			[
				'type'     => 'text',
				'name'     => esc_html__( 'Directed by', 'ggl-post-types' ),
				'id'       => 'short_movie_directed_by',
				'revision' => true,
				'tab'      => 'short-movie'

			],
			[
				'type'     => 'select_advanced',
				'name'     => __( 'Country/Countries of Origin', 'ggl-post-types' ),
				'id'       => 'short_movie_country',
				'options'  => generate_country_mapping(),
				'multiple' => true,
				'tab'      => 'short-movie',
				'revision' => true
			],
			[
				'type'     => 'number',
				'name'     => esc_html__( 'Running Time', 'ggl-post-types' ),
				'id'       => 'short_movie_running_time',
				'desc'     => esc_html__( 'The short\'s running time in minutes', 'ggl-post-types' ),
				'std'      => 5,
				'step'     => 1,
				'min'      => 0,
				'tab'      => 'short-movie',
				'revision' => true
			],
			[
				'type'     => 'number',
				'name'     => esc_html__( 'Release Year', 'ggl-post-types' ),
				'id'       => 'short_movie_release_year',
				'std'      => 1970,
				'step'     => 1,
				'min'      => 0,
				'tab'      => 'short-movie',
				'revision' => true
			],
			[
				'type' => 'checkbox',
				'name' => esc_html__( 'Display Animated Image', 'ggl-post-types' ),
				'id'   => 'use_animated_feature_image',
				'std'  => 0,
				'desc' => esc_html__( "If enabled, the featured image will be handled as an animation. The browser settings of the visitors are still respected.", 'ggl-post-types' ),
				'tab'  => 'display'
			],
			[
				'type' => "single_image",
				'id'   => 'landscape_animated_feature_image',
				'name' => esc_html__( 'Animated Feature Image for Landscape', 'ggl-post-types' ),
				'tab'  => 'display',
				'desc' => esc_html__( "Please select an Animation with a aspect ratio of 16:9 and a resolution of 800x450 px", 'ggl-post-types' ),
			],
			[
				'type' => "single_image",
				'id'   => 'portrait_animated_feature_image',
				'name' => esc_html__( 'Animated Feature Image for Portrait Mode', 'ggl-post-types' ),
				'tab'  => 'display',
				'desc' => esc_html__( "Please select an Animation with a aspect ratio of 4:5 and a resolution of 800x1000 px", 'ggl-post-types' ),
			]
		],
	];

	return $meta_boxes;
}

function movie_text_boxes( $meta_boxes ) {
	$meta_boxes[] = [
		'title'      => esc_html__( 'Movie Texts', 'ggl-post-types' ),
		'id'         => 'movie_text_boxes',
		'context'    => 'after_title',
		'post_types' => [ 'movie' ],
		'style'      => 'seamless',
		'autosave'   => true,
		'revision'   => true,
		'tab_style'  => 'box',
		'tabs'       => [
			"summary"      => [ "label" => esc_html__( "Content Summary", 'ggl-post-types' ) ],
			"worth_to_see" => [ "label" => esc_html__( "Why it's worth seeing", 'ggl-post-types' ) ],
		],
		'fields'     => [
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'summary',
				'required'              => true,
				'add_to_wpseo_analysis' => true,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS,
				'revision'              => true,
				'tab'                   => 'summary'
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'worth_to_see',
				'required'              => true,
				'add_to_wpseo_analysis' => true,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS,
				'revision'              => true,
				'tab'                   => "worth_to_see",

			],
		]
	];
	$meta_boxes[] = [
		'title'      => esc_html__( 'Anonymized Texts', 'ggl-post-types' ),
		'id'         => 'movie_anon_text_boxes',
		'context'    => 'after_title',
		'post_types' => [ 'movie' ],
		'style'      => 'seamless',
		'autosave'   => true,
		'revision'   => true,
		'tab_style'  => 'box',
		'tabs'       => [
			"summary"      => [ "label" => esc_html__( "Content Summary", 'ggl-post-types' ) ],
			"worth_to_see" => [ "label" => esc_html__( "Why it's worth seeing", 'ggl-post-types' ) ],
		],
		'visible'    => [ 'license_type', '!=', 'full' ],
		'fields'     => [
			[
				'type' => "custom_html",
				"std"  => "<div style='padding-left: 10px'><h3>" . esc_html__( "Anonymized Texts", "ggl-post-types" ) . "</h3><p>" . esc_html__( "As you selected that we do not have advertising rights for this movie you need to provide an anonymized version of the texts you wrote above.", "ggl-post-types" ) . "</p><p>" . esc_html__( "The text may not contain the names of characters, names of actors, clearly identifiable names of plot locations and similar names that would make the movie identifiable by a online search engine.", "ggl-post-types" ) . "</p></div>"
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'anon_summary',
				'required'              => false,
				'add_to_wpseo_analysis' => false,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS,
				'visible'               => [ 'license_type', '!=', 'full' ],
				'revision'              => true,
				"tab"                   => "summary",
			],
			[
				'type'                  => 'wysiwyg',
				'id'                    => 'anon_worth_to_see',
				'required'              => false,
				'add_to_wpseo_analysis' => false,
				'options'               => GGL_CPT__WYSIWYG_OPTIONS,
				'visible'               => [ 'license_type', '!=', 'full' ],
				'revision'              => true,
				"tab"                   => "worth_to_see",
			],
		],
	];

	return $meta_boxes;
}

/**
 * Get the Admission Fee for the Movie or Event
 *
 * If an unsupported post type is provided the function will return an empty string
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *    Defaults to global `$post`
 *
 * @return string The admission fee for the movie or event.
 */
function ggl_get_admission_fee( int|WP_Post $post = 0 ): string {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( ! in_array( $post->post_type, [ "movie", "event" ] ) ) {
		return "";
	}

	$admission_type = get_post_meta( $post->ID, "admission_type", true );
	switch ( $admission_type ) {
		case "free":
			return __( "Free", "ggl-post-types" );
		case "donation":
			return __( "Donations Welcome", "ggl-post-types" );
		case "paid":
			$admission_fee = floatval( get_post_meta( $post->ID, "admission_fee", true ) );

			return number_format( $admission_fee, decimals: 2, decimal_separator: str_starts_with( get_user_locale(), "de" ) ? "," : "." ) . " €";
		default:
			return __( "Inquire at the Box Office", "ggl-post-types" );
	}
}

/**
 * Output the admission fee
 *
 * The function will output the result of `ggl_get_admission_fee()`
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *  Defaults to global `$post`
 *
 *
 * @see ggl_get_admission_fee() for the retrieval of the worth to see section
 * @since 3.9.0
 */
function ggl_the_admission_fee( int|WP_Post $post = 0 ): void {
	echo ggl_get_admission_fee( $post );
}

function ggl_get_numerical_admission_fee( int|WP_Post $post = 0 ): float {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( ! in_array( $post->post_type, [ "movie", "event" ] ) ) {
		return 0.00;
	}

	$admission_type = get_post_meta( $post->ID, "admission_type", true );
	switch ( $admission_type ) {
		case "donation":
		case "free":
			return 0.00;
		case "paid":
			return floatval( get_post_meta( $post->ID, "admission_fee", true ) );
		default:
			return 3.00;
	}
}


/**
 * Get a localized version of the starting time
 *
 * The starting time is formatted according to the
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return DateTimeImmutable|null The starting time of the screening/event
 *
 * @throws DateMalformedStringException
 * @see GGL_GERMAN_DATETIME_FORMAT German Format for screening start times
 * @see GGL_FALLBACK_DATETIME_FORMAT Fallback format in case neither English nor German is acceptable as a language
 * @see GGL_ENGLISH_DATETIME_FORMAT English Format for screening start times
 */
function ggl_get_starting_time( int|WP_Post $post = 0 ): null|DateTimeImmutable {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( ! in_array( $post->post_type, [ "movie", "event" ] ) ) {
		return null;
	}

	$starting_timestamp = get_post_meta( $post->ID, "screening_date", true );
	$server_tz          = new DateTimeZone( "Europe/Berlin" );

	return new DateTimeImmutable( date( "Y-m-d H:i:s", $starting_timestamp ), $server_tz );
}

/**
 * Output the formatted starting time
 *
 * The function will output the result of `ggl_get_starting_time()`
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *  Defaults to global `$post`
 *
 *
 * @throws DateMalformedStringException
 * @since 3.9.0
 * @see ggl_get_starting_time() for the retrieval of the worth to see section
 */
function ggl_the_starting_time( int|WP_Post $post = 0 ): void {
	$desired_language = substr( get_user_locale(), 0, 2 );
	$starting_time    = ggl_get_starting_time( $post );

	echo match ( $desired_language ) {
		"de" => $starting_time->format( GGL_GERMAN_DATETIME_FORMAT ),
		"en" => $starting_time->format( GGL_ENGLISH_DATETIME_FORMAT ),
		default => $starting_time->format( GGL_FALLBACK_DATETIME_FORMAT ),
	};
}

/**
 * Get the director of the movie
 *
 * This function automatically anonymizes the director name if required by the
 * filters
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return string (Anonymized) name of the director
 *
 */
function ggl_get_movie_director( int|WP_Post $post = 0 ): string {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( $post === null || $post->post_type != "movie" ) {
		return "";
	}

	$director_name = array_first( get_the_terms( $post, "director" ) )->name;
	$show_details  = apply_filters( "ggl__show_full_details", false, $post );

	return $show_details ? $director_name : ggl_cpt__anonymize_chars( $director_name );
}

/**
 * Output the movie director's name
 *
 * @param int|WP_Post $post $post Optional. Post ID or `WP_Post` object.
 *      Defaults to global `$post`
 *
 * @return void
 */
function ggl_the_movie_director( int|WP_Post $post = 0 ): void {
	echo ggl_get_movie_director( $post );
}

/**
 * Get the actors featured in the movie
 *
 * This function automatically anonymizes the actor names if required by the
 * filters
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return string[] (Anonymized) names of the actors
 *
 */
function ggl_get_movie_actors( int|WP_Post $post = 0 ): array {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( $post === null || $post->post_type != "movie" ) {
		return [];
	}

	$terms = get_the_terms( $post, "actor" );
	if ( ! $terms ) {
		return [];
	}

	$show_details = apply_filters( "ggl__show_full_details", false, $post );

	return array_map( function ( $term ) use ( $show_details ) {
		return $show_details ? $term->name : ggl_cpt__anonymize_chars( $term->name );
	}, $terms );
}

/**
 * Output the actors featured in the movie
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *      Defaults to global `$post`
 *
 * @return void
 */
function ggl_the_actors( int|WP_Post $post = 0 ): void {
	$actors = ggl_get_movie_actors( $post );
	if ( ! $actors || count( $actors ) < 1 ) {
		return;
	}

	$comma_joined_actors = array_slice( $actors, 0, count( $actors ) - 1 );
	$output              = esc_html__( "with", "ggl-post-types" ) . "&#x20;" . join( ", ", $comma_joined_actors );
	$output              .= "&#x20;" . esc_html__( "and", "ggl-post-types" ) . "&#x20;" . array_last( $actors );
	echo $output;
}

/**
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *       Defaults to global `$post`
 * @param string $field The field that should be returned from the country definition
 *
 * @return array List of countries
 *
 * @see Country for defined fields
 */
function ggl_get_countries_of_origin( int|WP_Post $post = 0, string $field = "alpha2" ): array {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return [];
	}

	$iso3166_numerical_codes = get_post_meta( $post->ID, "country", false );
	$countries               = generate_countries();
	$selected_countries      = [];
	foreach ( $iso3166_numerical_codes as $numerical_code ) {
		$country              = array_first( array_filter( $countries, function ( $country ) use ( $numerical_code ) {
			return $country->numerical == $numerical_code;
		} ) );
		$selected_countries[] = $country->$field;
	}

	return $selected_countries;
}

/**
 * Output the country/countries of origin separated by a slash
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *   Defaults to global `$post`
 *
 * @return void
 */
function ggl_the_countries_of_origin( int|WP_Post $post = 0 ): void {
	$countries = ggl_get_countries_of_origin( $post );
	echo join( "/", $countries );
}

/**
 * Get the release date of the movie
 *
 * @param int|WP_Post $post
 *
 * @return DateTimeImmutable|null
 */
function ggl_get_release_date( int|WP_Post $post = 0 ): DateTimeImmutable|null {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return null;
	}

	$release_date = get_post_meta( $post->ID, "release_date", true );
	if ( ! $release_date ) {
		return null;
	}

	return DateTimeImmutable::createFromTimestamp( $release_date );
}


/**
 * Display the release date of the movie
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *    Defaults to global `$post`
 * @param bool $year_only Optional. Show only the year and not the full date
 *    Defaults to `true`
 *
 * @return void
 */
function ggl_the_release_date( int|WP_Post $post = 0, bool $year_only = true ): void {
	$dt = ggl_get_release_date( $post );
	if ( $year_only ) {
		echo $dt->format( "Y" );

		return;
	}
	$desired_language = substr( get_user_locale(), 0, 2 );
	echo match ( $desired_language ) {
		"de" => $dt->format( GGL_GERMAN_DATE_FORMAT ),
		"en" => $dt->format( GGL_ENGLISH_DATE_FORMAT ),
		default => $dt->format( GGL_FALLBACK_DATE_FORMAT ),
	};
}


/**
 * Get the running time of the movie/event
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *    Defaults to global `$post`
 *
 * @return int The running time/duration
 */
function ggl_get_running_time( int|WP_Post $post = 0 ): int {
	$post = get_post( $post, filter: 'display' );
	if ( ! in_array( $post->post_type, [ "movie", "event" ] ) ) {
		return - 1;
	}

	return intval( get_post_meta( $post->ID, $post->post_type == "movie" ? "running_time" : "duration", true ) );
}

/**
 * Output the running time/duration
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *    Defaults to global `$post`
 *
 * @return void
 * @see ggl_get_running_time()
 */
function ggl_the_running_time( int|WP_Post $post = 0 ): void {
	echo ggl_get_running_time( $post ) . " " . __( "Minutes", "ggl-post-types" );
}

/**
 * Get the audio language for a movie
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 * Defaults to global `$post`
 *
 * @return string The ISO639 Alpha 3 identifier for the language
 */
function ggl_get_audio_language( int|WP_Post $post = 0 ): string {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return "";
	}

	return get_post_meta( $post->ID, "audio_language", true );
}

/**
 * Get/Output the audio language name instead of the ISO639 identifier
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object. Defaults to global `$post`
 * @param bool $output Optional. Control if the function should directly output the content or if it should be returned
 *
 * @return string|null The translated language name, if `$output` is `false`
 */
function ggl_the_audio_language( int|WP_Post $post = 0, bool $output = true ): null|string {
	if ( ! is_textdomain_loaded( "ggl-i18n" ) ) {
		load_plugin_textdomain( 'ggl-i18n', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	$audioLanguage = ggl_get_audio_language( $post );
	if ( $output ) {
		echo esc_html__( $audioLanguage, "ggl-i18n" );

		return null;
	} else {
		return esc_html__( $audioLanguage, "ggl-i18n" );
	}
}

/**
 * Get the subtitle language for a movie
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 * Defaults to global `$post`
 *
 * @return string The ISO639 Alpha 3 identifier for the language
 */
function ggl_get_subtitle_language( int|WP_Post $post = 0 ): string {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return "";
	}

	return get_post_meta( $post->ID, "subtitle_language", true );
}

/**
 * Get/Output the subtitle language name instead of the ISO639 identifier
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object. Defaults to global `$post`
 * @param bool $output Optional. Control if the function should directly output the content or if it should be returned
 *
 * @return string|null The translated language name, if `$output` is `false`
 */
function ggl_the_subtitle_language( int|WP_Post $post = 0, bool $output = true ): null|string {
	if ( ! is_textdomain_loaded( "ggl-i18n" ) ) {
		load_plugin_textdomain( 'ggl-i18n', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	$subtitle_language = ggl_get_subtitle_language( $post );
	if ( $output ) {
		echo esc_html__( $subtitle_language, "ggl-i18n" );

		return null;
	} else {
		return esc_html__( $subtitle_language, "ggl-i18n" );
	}
}

/**
 * Get the urls and media queries for the posts image generation
 *
 * @param int|WP_Post $post $post Optional . Post ID or `WP_Post` object.
 *    Defaults to global `$post`
 *
 * @return array
 */
function ggl_get_movie_thumbnail_urls( int|WP_Post $post = 0 ): array {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	if ( $post === null || $post->post_type != "movie" ) {
		return [];
	}

	$anonymous_image = rwmb_meta( "movie_anonymous_movie_image", [ "object_type" => "setting" ], "ggl_cpt__settings" );

	$show_details             = apply_filters( "ggl__show_full_details", false, $post );
	$is_in_special_program    = get_post_meta( $post->ID, "program_type", true ) === "special_program";
	$assigned_special_program = array_first( wp_get_post_terms( $post->ID, "special-program" ) );

	if ( ! $show_details ) {
		if ( $is_in_special_program && $assigned_special_program != null ) {
			return [
				[
					"url"         => ggl_get_special_program_anonymous_image_url( $assigned_special_program, "mobile" ) ?? $anonymous_image["sizes"]["mobile"]["url"] ?? $anonymous_image["url"],
					"media_query" => "(width <= 768px)"
				],

				[
					"url"         => ggl_get_special_program_anonymous_image_url( $assigned_special_program ) ?? $anonymous_image["sizes"]["desktop"]["url"] ?? $anonymous_image["url"],
					"media_query" => "(width > 768px)"
				]
			];
		}

		return [
			[
				"url"         => $anonymous_image["sizes"]["mobile"]["url"] ?? $anonymous_image["url"],
				"media_query" => "(width <= 768px)"
			],

			[
				"url"         => $anonymous_image["sizes"]["desktop"]["url"] ?? $anonymous_image["url"],
				"media_query" => "(width > 768px)"
			]
		];
	}


	$image_urls   = [];
	$image_urls[] = [
		"url"         => get_the_post_thumbnail_url( $post->ID, "mobile" ) ?? $anonymous_image["sizes"]["mobile"]["url"] ?? $anonymous_image["full_url"],
		"media_query" => "(prefers-reduced-motion: reduce) and (width <= 768px)"
	];
	$image_urls[] = [
		"url"         => get_the_post_thumbnail_url( $post->ID, "desktop" ) ?? $anonymous_image["sizes"]["desktop"]["url"] ?? $anonymous_image["full_url"],
		"media_query" => "(prefers-reduced-motion: reduce) and (width > 768px)"
	];

	$has_animated_image = boolval( get_post_meta( $post->ID, "use_animated_feature_image", true ) );
	if ( ! $has_animated_image ) {
		for ( $i = 0; $i < count( $image_urls ); $i ++ ) {
			$image_urls[ $i ]["media_query"] = trim( str_replace( "(prefers-reduced-motion: reduce) and", "", $image_urls[ $i ]["media_query"] ) );
		}
	} else {
		$mobile_animated_image = rwmb_meta( "portrait_animated_feature_image" );
		$image_urls[]          = [
			"url"         => $mobile_animated_image["full_url"],
			"media_query" => "(width <= 768px)"
		];

		$desktop_animated_image = rwmb_meta( "landscape_animated_feature_image" );
		$image_urls[]           = [
			"url"         => $desktop_animated_image["full_url"],
			"media_query" => "(width > 768px)"
		];
	}

	return $image_urls;
}

/**
 * Output the movie thumbnail
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *   Defaults to global `$post`
 * @param string $classes Optional. The classes for the `<picture>` element.
 *   Defaults to `image movie-image`
 *
 * @return void
 */
function ggl_the_movie_thumbnail( int|WP_Post $post = 0, string $classes = "image movie-image" ): void {
	$post = get_post( $post, filter: 'display' );

	if ( $post === null || $post->post_type != "movie" ) {
		return;
	}

	$images = ggl_get_movie_thumbnail_urls( $post );
	if ( empty( $images ) ) {
		return;
	}
	$anonymous_image = rwmb_meta( "movie_anonymous_movie_image", [ "object_type" => "setting" ], "ggl_cpt__settings" );
	?>
    <picture class="<?= $classes ?>">
		<?php foreach ( $images as $image ) : ?>
            <source media="<?= $image['media_query'] ?>" srcset="<?= $image['url'] ?>"/>
		<?php endforeach; ?>
        <img alt="" width="800" height="1000"
             src="<?= $anonymous_image["full_url"] ?>"/>
    </picture>
	<?php
}


/**
 * Check if a movie has a short movie that is shown before the start of the feature
 *
 * The function validates that the current visitor is allowed to see the detailed content via the
 * `ggl__show_full_details` filter, if the short movie is enabled on the movie and if a short title
 * is set.
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *    Defaults to global `$post`
 *
 * @return bool
 */
function ggl_movie_has_short( int|WP_Post $post = 0 ): bool {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return false;
	}

	$show_details        = apply_filters( "ggl__show_full_details", false, $post );
	$short_movie_enabled = filter_var( get_post_meta( $post->ID, "short_movie_screened", true ), FILTER_VALIDATE_BOOLEAN );

	return $show_details && $short_movie_enabled && ! empty( ggl_get_short_movie_title( $post ) );
}

/**
 * Get the title of the short movie that is shown
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return string
 */
function ggl_get_short_movie_title( int|WP_Post $post = 0 ): string {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return "";
	}

	return mb_trim( get_post_meta( $post->ID, "short_movie_title", true ) );
}

/**
 * Output the short movie title
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return void
 */
function ggl_the_short_movie_title( int|WP_Post $post = 0 ): void {
	echo ggl_get_short_movie_title( $post );
}

/**
 * Get the name of the director for the short movie
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *     Defaults to global `$post`
 *
 * @return string The director of the short movie
 */
function ggl_get_short_movie_director( int|WP_Post $post = 0 ): string {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return "";
	}

	return mb_trim( get_post_meta( $post->ID, "short_movie_directed_by", true ) );
}

/**
 * Output the director of the short movie
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *      Defaults to global `$post`
 *
 * @return void
 */
function ggl_the_short_movie_director( int|WP_Post $post = 0 ): void {
	echo ggl_get_short_movie_director( $post );
}

/**
 * Get the origin countries of the short movie
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *      Defaults to global `$post`
 *
 * @param string $field The field that should be returned from the country definition
 *
 * @return array List of Countries
 */
function ggl_get_short_movie_country_of_origin( int|WP_Post $post = 0, string $field = "alpha2" ): array {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return [];
	}

	$iso3166_numerical_codes = get_post_meta( $post->ID, "short_movie_country", false );
	$countries               = generate_countries();
	$selected_countries      = [];
	foreach ( $iso3166_numerical_codes as $numerical_code ) {
		$country              = array_first( array_filter( $countries, function ( $country ) use ( $numerical_code ) {
			return $country->numerical == $numerical_code;
		} ) );
		$selected_countries[] = $country->$field;
	}

	return $selected_countries;
}

/**
 * Output a slash separated list of the origin countries
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *      Defaults to global `$post`
 *
 * @return void
 */
function ggl_the_short_movie_countries( int|WP_Post $post = 0 ): void {
	$countries = ggl_get_short_movie_country_of_origin( $post );
	echo join( "/", $countries );
}

/**
 * Get the short movie release year
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *      Defaults to global `$post`
 *
 * @return int Release Year of the Short Movie
 */
function ggl_get_short_movie_release_year( int|WP_Post $post = 0 ): int {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return - 1;
	}

	return intval( get_post_meta( $post->ID, "short_movie_release_year", true ) );
}

/**
 * Output the release year of the short movie
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *      Defaults to global `$post`
 *
 * @return void
 */
function ggl_the_short_movie_release_year( int|WP_Post $post = 0 ): void {
	echo ggl_get_short_movie_release_year( $post );
}

/**
 * Get the short movies running time
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *      Defaults to global `$post`
 *
 * @return int The running time in minutes
 */
function ggl_get_short_movie_running_time( int|WP_Post $post = 0 ): int {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return - 1;
	}

	return intval( get_post_meta( $post->ID, "short_movie_running_time", true ) );
}

/**
 * Output the short movies running time
 *
 * @param int|WP_Post $post Optional . Post ID or `WP_Post` object.
 *      Defaults to global `$post`
 *
 * @return void
 */
function ggl_the_short_movie_running_time( int|WP_Post $post = 0 ): void {
	echo ggl_get_short_movie_running_time( $post ) . " " . esc_html__( "Minutes", "ggl-post-types" );
}

/**
 * @param int|WP_Post $post
 *
 * @return WP_Term|null
 */
function ggl_get_movie_semester( int|WP_Post $post = 0 ): WP_Term|null {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return null;
	}

	return array_first( wp_get_post_terms( $post->ID, 'semester' ) );
}

function ggl_the_movie_semester( int|WP_Post $post = 0 ): void {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type != "movie" ) {
		return;
	}

	$semester = ggl_get_movie_semester( $post );
	echo __( "screened in ", "ggl-post-types" ) . $semester->name;
}

function ggl_movie_is_special_feature( int|WP_Post $post = 0 ): bool {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || $post->post_type !== "movie" ) {
		return false;
	}

	return is_singular( "movie" ) && get_post_meta( $post->ID, "program_type", true ) == "special_program";
}

