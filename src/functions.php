<?php

require_once 'Country.php';

function add_admin_menu_separator( int $atPosition ) {
	global $menu;

	$seperator = [ '', 'read', '', '', 'wp-menu-separator' ];

	// check if a entry already exists at this position
	$menuIdxUsed = isset( $menu[ $atPosition ] );

	if ( ! $menuIdxUsed ) {
		$menu[ $atPosition ] = $seperator;

		return;
	}

	// as the menu position is already used, save the menu entry
	$previousMenuEntry = $menu[ $atPosition ];

	// now calculate the offset for splitting the menu
	$offset = 0;
	for ( $i = 0; $i < $atPosition; $i ++ ) {
		if ( isset( $menu[ $i ] ) ) {
			$offset ++;
		}
	}

	$beforeSeperator = array_slice( $menu, 0, $offset, true );
	$behindSeperator = array_slice( $menu, $offset, null, true );

	$menu                    = $beforeSeperator + $behindSeperator;
	$menu[ $atPosition ]     = $seperator;
	$menu[ $atPosition + 1 ] = $previousMenuEntry;

}

function ggl_cpt__register_settings( $settings_pages ): array {
	$settings_pages[] = [
		"id"          => "ggl_cpt__settings",
		"option_name" => "ggl_cpt__settings",
		"menu_title"  => esc_html__( 'Anonymization', 'ggl-post-types' ),
		"page_title"  => esc_html__( 'Anonymization Settings', 'ggl-post-types' ),
		"capability"  => "manage_options",
		"icon_url"    => "dashicons-privacy",
		'customizer'  => true,
		"position"    => 11,
		"style"       => "no-boxes",
		"tabs"        => [
			"movies"       => [
				"label" => esc_html__( 'Movies', 'ggl-post-types' ),
				"icon"  => "dashicons-editor-video",
			],
			"events"       => [
				"label" => esc_html__( 'Events', 'ggl-post-types' ),
				"icon"  => "dashicons-schedule",
			],
			"team-members" => [
				"label" => esc_html__( 'Team Members', 'ggl-post-types' ),
				"icon"  => "dashicons-businessperson",
			]
		]
	];

	return $settings_pages;
}

function ggl_cpt__settings_meta_boxes( $meta_boxes ): array {
	$meta_boxes[] = [
		"id"             => "movie_anonymization_settings",
		"title"          => esc_html__( 'Movies', 'ggl-post-types' ),
		"context"        => "normal",
		"settings_pages" => "ggl_cpt__settings",
		"tab"            => "movies",
		"fields"         => [
			[
				"name" => esc_html__( 'Anonymization Image', 'ggl-post-types' ),
				"type" => "single_image",
				"desc" => esc_html__( "This image is used if the movie has no advertisement license and the user is not permitted to see the full details or no image is selected for a movie", "ggl-post-types" ),
				"id"   => "movie_anonymous_movie_image",
			],
			[
				"name" => esc_html__( "Replacement Character", "ggl-post-types" ),
				"type" => "text",
				"desc" => esc_html__( "This character is used to replace the letters in director and actor names if the movie is to be anonymized", "ggl-post-types" ),
				"id"   => "replacement_character",
				"std"  => "█"
			]
		]
	];

	$meta_boxes[] = [
		"id"             => "event_anonymization_settings",
		"title"          => esc_html__( 'Events', 'ggl-post-types' ),
		"context"        => "normal",
		"settings_pages" => "ggl_cpt__settings",
		"tab"            => "events",
		"fields"         => [
			[
				"name" => esc_html__( 'Anonymization Image', 'ggl-post-types' ),
				"type" => "single_image",
				"desc" => esc_html__( "This image is used if the event has no advertisement license and the user is not permitted to see the full details or no image is selected for a event", "ggl-post-types" ),
				"id"   => "event_anonymous_movie_image",
			]
		]
	];

	$meta_boxes[] = [
		"id"             => "team_member_anonymization_settings",
		"title"          => esc_html__( 'Team Member', 'ggl-post-types' ),
		"context"        => "normal",
		"settings_pages" => "ggl_cpt__settings",
		"tab"            => "team-members",
		"tabs"           => [
			"german"         => [ "label" => __( "German" ) ],
			"german_former"  => [ "label" => __( "German" ) . " (" . __( "Former", "ggl-post-types" ) . ")" ],
			"english"        => [ "label" => __( "English" ) ],
			"english_former" => [ "label" => __( "English" ) . " (" . __( "Former", "ggl-post-types" ) . ")" ],
		],
		"fields"         => [
			[
				"name" => esc_html__( "Allow Access to hidden Teamies", "ggl-post-types" ),
				"type" => "checkbox",
				"id"   => "teamie_allow_hidden_display",
				"desc" => esc_html__( "If this checkbox is ticked, the plugin will not redirect users away from hidden teamie entries", "ggl-post-types" ),
				"std"  => false,
			],
			[
				"name" => esc_html__( 'Fallback Teamie Image', 'ggl-post-types' ),
				"type" => "single_image",
				"id"   => "teamie_anonymous_image",
				"desc" => esc_html__( "This image is displayed instead of a team members image if the team member post has no associated image", "ggl-post-types" ),
			],
			[
				"type" => "custom_html",
				"std"  => "<h3 style='margin: -20px 0'>" . __( "Generic Team Member Description", "ggl-post-types" ) . "</h3>",
			],
			[
				"type" => "custom_html",
				"std"  => '<div class="rwmb-field" style="padding: 0"><div class="rwmb-label">
                                <label>' . esc_html__( "Available Placeholders", "ggl-post-types" ) . '</label>
                               <p class="description">' . esc_html__( "The placeholders defined here can be used to dynamically change the content of the teamie descriptions", "ggl-post-types" ) . '</p>
                           </div>
                           <div class="rwmb-input">
                                <dl>
                                    <dt><code>{joined-in}</code></dt>
                                    <dd>' . esc_html__( "The year the teamie joined the GEGENLICHT", "ggl-post-types" ) . '</dd>
                                    <dt><code>{left-in}</code></dt>
                                    <dd>' . esc_html__( "The year the teamie left the GEGENLICHT (only used for former members)", "ggl-post-types" ) . '</dd>
                                    <dt><code>{name}</code></dt>
                                    <dd>' . esc_html__( "The teamie's name", "ggl-post-types" ) . '</dd>
                                    <dt><code>{movie-count}</code></dt>
                                    <dd>' . esc_html__( "The number of movies the teamie screened", "ggl-post-types" ) . '</dd>
                                </dl>
                           </div></div>'

			],
			[
				"type"    => "wysiwyg",
				'options' => GGL_CPT__WYSIWYG_OPTIONS,
				'tab'     => "german",
				'id'      => "teamie_generic_description_de",
			],
			[
				"type"    => "wysiwyg",
				'options' => GGL_CPT__WYSIWYG_OPTIONS,
				'tab'     => "german_former",
				'id'      => "former_teamie_generic_description_de",
			],
			[
				"type"    => "wysiwyg",
				'options' => GGL_CPT__WYSIWYG_OPTIONS,
				'tab'     => "english",
				'id'      => "teamie_generic_description_en",
			],
			[
				"type"    => "wysiwyg",
				'options' => GGL_CPT__WYSIWYG_OPTIONS,
				'tab'     => "english_former",
				'id'      => "former_teamie_generic_description_en",
			]
		]
	];

	return $meta_boxes;
}

function ggl_post_types_load_textdomain() {
	load_plugin_textdomain( 'ggl-post-types', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	load_plugin_textdomain( 'ggl-i18n', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

function ggl_cpt__spaceout_admin_menu() {
	remove_menu_page( 'edit.php' ); // Posts
	remove_menu_page( 'edit-comments.php' ); // Comments
	global $menu;

	$menu[5] = $menu[6];
	$menu[6] = $menu[7];
	$menu[7] = $menu[11];
	unset( $menu[11] );

	add_admin_menu_separator( 10 );
	add_admin_menu_separator( 20 );
}

function generate_language_mapping(): array {
	$output = array();
	foreach ( GGL_LANGUAGES as $languageKey ) {
		$translatedLanguage     = __( $languageKey, 'ggl-i18n' );
		$output[ $languageKey ] = $translatedLanguage;
	}

	return $output;
}

function generate_countries(): array {
	$output_prepared = false;
	$output = wp_cache_get("ggl_countries", "ggl", found: $output_prepared);
	if ( $output_prepared ) {
		return $output;
	}

	$country_source_file_path = dirname(__FILE__) . "/../assets/iso3166.csv";
	$rows = array_map(function ($in) {
		return str_getcsv($in, ",", escape: "");
	}, file($country_source_file_path));
	$header = array_shift($rows);
	$definitions = array();
	foreach ($rows as $row) {
		$definitions[] = array_combine( $header, $row );
	}

	$countries = [];

	foreach ($definitions as $definition) {
		$countries[] = new Country($definition["num_id"], $definition["letter_code"], $definition["german_comment"], $definition["english_comment"], $definition["german_name"], $definition["official_german_name"], $definition["english_name"], $definition["official_english_name"]);
	}

	wp_cache_add("ggl_countries", $countries, "ggl");
	return $countries;
}

function generate_country_mapping(): array {

	$countries = generate_countries();

	$locale = get_user_locale();
	$output_prepared = false;
	$output = wp_cache_get("ggl__cc_mapping_" . $locale, "ggl", found: $output_prepared);
	if ( $output_prepared ) {
		return $output;
	}

	$output = array();

	foreach ($countries as $country) {
		$output[$country->numerical] = str_replace(" // ⚠️ )",")", ((str_starts_with($locale, "de") ? $country->german_name : $country->english_name ). "\n(". (str_starts_with($locale, "de") ? $country->official_german_name . " // ⚠️ " . $country->german_comment : $country->official_english_name . " // ⚠️ " . $country->english_comment ). ")"));
	}

	$sorted = $output;

	$collator = new Collator($locale);
	usort($sorted, fn($a,$b) => $collator->compare($a,$b));

	$mapping = [];

	foreach ($sorted as $value) {
		$num_id = array_search($value, $output);

		$mapping[$num_id] = $value;
	}

	wp_cache_set("ggl__cc_mapping_" . $locale, $mapping,"ggl", 0);
	return $mapping;
}

function ggl_menu_order( array $order ) {
	$newOrder = array(
		"index.php",
		"separator1",
		"edit.php?post_type=movie",
		"edit.php?post_type=event",
		"edit.php?post_type=team-member",
		"edit.php?post_type=cooperation-partner",
		"edit.php?post_type=supporter",
		"edit.php?post_type=screening-location",
		"separator2",
		"edit.php?post_type=page"
	);

	return $newOrder;

}

function ggl_cpt__anonymize_chars( string $chars ): string {
	return preg_replace( '/\S/u', '█', $chars );
}