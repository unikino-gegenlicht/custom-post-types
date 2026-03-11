<?php


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

function ggl_cpt__replace_movie_title_in_table() {
	$cs = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	// make sure we are on the right admin page
	if ( ! is_admin() || empty( $cs->post_type ) || $cs->post_type != 'movie' || $cs->id != 'edit-movie' ) {
		return;
	}

	add_filter( "the_title", function ( $title, $id ) {
		$post = get_post( $id );

		$germanTitle  = rwmb_get_value( "german_title", post_id: $post->ID );
		$englishTitle = rwmb_get_value( "english_title", post_id: $post->ID );

		return "{$germanTitle} // {$englishTitle}";

	}, 100, 2 );
}

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

function ggl_cpt__add_movie_program_filter( $post_type ): void {
	if ( $post_type !== 'movie' ) {
		return;
	}

	$special_programs = get_terms( [
		"taxonomy"   => "special-program",
		"hide_empty" => true,
		"orderby"    => "name",
		"order"      => "ASC",
	] );
	?>
    <select name="program">
        <option value=""><?= esc_html__( "All Programs", "ggl-post-types" ) ?></option>
        <option value="main" <?= selected( "main", @ $_GET["program"], 0 ) ?>><?= esc_html__( "Main Program", "ggl-post-types" ) ?></option>
		<?php foreach ( $special_programs as $special_program ): ?>
            <option value="<?= $special_program->slug ?>" <?= selected( $special_program->slug, @ $_GET["program"], 0 ) ?>><?= $special_program->name ?></option>
		<?php endforeach; ?>
    </select>
	<?php

}

function ggl_cpt__apply_movie_program_filter( WP_Query $query ) {
	$cs = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	// make sure we are on the right admin page
	if ( ! is_admin() || empty( $cs->post_type ) || $cs->post_type != 'movie' || $cs->id != 'edit-movie' ) {
		return;
	}

	if ( @ $_GET['program'] != - 1 && @ $_GET['program'] !== "" && @ $_GET['program'] !== null ) {
		$selected_program = @ $_GET['program'] ?: null;
		if ( $selected_program === "main" ) {
			$query->set( "meta_query", [
				[
					"key"   => "program_type",
					"value" => "main",
				]
			] );
		} else {
			$query->set( "meta_query", [
				"key"   => "program_type",
				"value" => "special_program",
			] );
			$query->set( "tax_query", [
				[
					"taxonomy" => "special-program",
					"terms"    => $selected_program,
					"field"    => "slug",
				]
			] );
		}
	}

}

function ensure_numerical_movie_link( $post_id ): void {
	$parent_id = wp_is_post_revision( $post_id );

	if ( false !== $parent_id ) {
		$post_id = $parent_id;
	}

    $german_title = $_POST['german_title'] ?: null;
    $english_title = $_POST['english_title'] ?: null;
    $post_title = "$german_title // $english_title";

	$post = get_post( $post_id );
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
				'type'     => 'text',
				'name'     => esc_html__( 'German Title', 'ggl-post-types' ),
				'id'       => 'german_title',
				'desc'     => esc_html__( 'Please enter the German title of the movie here', 'ggl-post-types' ),
				'required' => true,
				'revision' => true,
				'tab'      => 'information',
			],
			[
				'type'     => 'text',
				'name'     => esc_html__( 'English Title', 'ggl-post-types' ),
				'id'       => 'english_title',
				'desc'     => esc_html__( 'Please enter the English title of the movie here', 'ggl-post-types' ),
				'required' => true,
				'revision' => true,
				'tab'      => 'information',

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
				'tab'      => 'sound'
			],
			[
				'type'     => 'select_advanced',
				'name'     => esc_html__( 'Audio Language', 'ggl-post-types' ),
				'id'       => 'audio_language',
				'std'      => 'eng',
				'options'  => generate_language_mapping(),
				'required' => true,
				'revision' => true,
				'tab'      => 'sound'
			],
			[
				'type'     => 'select_advanced',
				'name'     => esc_html__( 'Subtitle Language', 'ggl-post-types' ),
				'id'       => 'subtitle_language',
				'std'      => 'deu',
				'options'  => generate_language_mapping(),
				'required' => true,
				'revision' => true,
				'tab'      => 'sound'
			],
			[
				'type'     => 'radio',
				'name'     => esc_html__( 'License Type', 'ggl-post-types' ),
				'id'       => 'license_type',
				'inline'   => true,
				'required' => true,
				'options'  => [
					'full' => esc_html__( 'Advertisement License', 'ggl-post-types' ),
					'pool' => esc_html__( 'Pool License', 'ggl-post-types' ),
					'none' => esc_html__( 'No License', 'ggl-post-types' ),
				],
				'std'      => 'full',
				'revision' => true,
				'tab'      => 'licensing'
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
				'type'     => 'select',
				'name'     => esc_html__( 'Age Rating', 'ggl-post-types' ),
				'id'       => 'age_rating',
				'options'  => [
					- 2 => esc_html__( 'unknown', 'ggl-post-types' ),
					- 1 => esc_html__( 'Not rated', 'ggl-post-types' ),
					0   => esc_html__( 'FSK 0', 'ggl-post-types' ),
					6   => esc_html__( 'FSK 6', 'ggl-post-types' ),
					12  => esc_html__( 'FSK 12', 'ggl-post-types' ),
					16  => esc_html__( 'FSK 16', 'ggl-post-types' ),
					18  => esc_html__( 'FSK 18', 'ggl-post-types' ),
				],
				'std'      => - 2,
				'required' => true,
				'revision' => true,
				'tab'      => 'youth-protection'
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
				'type'        => 'taxonomy',
				'name'        => esc_html__( 'Semester', 'ggl-post-type' ),
				'id'          => 'semester',
				'placeholder' => esc_html__( 'Select a Semester', 'ggl-post-types' ),
				'taxonomy'    => 'semester',
				'required'    => false,
				'field_type'  => 'select_advanced',
				'query_args'  => [
					'number'   => 5,
					'orderby'  => 'meta_value_num',
					'meta_key' => 'semester_start',
					'order'    => 'desc'
				],
				'std'         => count( get_terms( 'semester', [
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
				'add_new'     => current_user_can( "edit_others_posts" ),
				'ajax'        => true,
				'revision'    => true,
				'tab'         => 'screening'
			],
			[
				'type'       => 'datetime',
				'name'       => esc_html__( 'Date and Time', 'ggl-post-types' ),
				'id'         => 'screening_date',
				'timestamp'  => true,
				'js_options' => [
					'dateFormat' => ( str_starts_with( get_locale(), "de" ) ? 'dd.mm.yy' : "mm/dd/yy" ),
				],
				'required'   => true,
				'revision'   => true,
				'tab'        => 'screening'

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
							"key"   => "status",
							"value" => "active",
						]
					],
					'orderby'        => 'title',
					'order'          => 'ASC',
				],
				'visible'     => [ 'selected_by', '=', 'member' ],
				'ajax'        => true,
				'revision'    => true,
				'multiple'    => true,
				'tab'         => 'screening'
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
				'type'     => 'radio',
				'name'     => esc_html__( 'Program Type', 'ggl-post-types' ),
				'id'       => 'program_type',
				'inline'   => true,
				'required' => true,
				'options'  => [
					'main'            => esc_html__( 'Main Program', 'ggl-post-types' ),
					'special_program' => esc_html__( 'Special Program', 'ggl-post-types' ),
				],
				'std'      => 'main',
				'revision' => true,
				'tab'      => 'screening'
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