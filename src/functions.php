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

function ggl_cpt__get_ical_download_url( int $post = 0 ): string|false {
	$post = get_post( $post );

	return content_url( "ics/{$post->post_type}-{$post->ID}.ics" );
}

function ggl_cpt__get_title( WP_Post|int $post = 0 ): string {
	$post = get_post( $post );

	if ( $post->post_type !== "movie" && $post->post_type !== "event" ) {
		return get_the_title( $post );
	}

	$licensingType = rwmb_get_value( "license_type" ) ?: "full";
	$anonymize     = ( $licensingType != "full" && ! is_user_logged_in() );

	if ( ! $anonymize ) {
		if ( rwmb_get_value( 'german_title' ) == rwmb_get_value( 'english_title' ) ) {
			return rwmb_get_value( 'german_title' );
		}

		return rwmb_get_value( 'german_title' ) . ' // ' . rwmb_get_value( 'english_title' );
	}

	$inSpecialProgram = rwmb_get_value( 'program_type' ) == 'special_program';
	if ( $inSpecialProgram ) {
		return rwmb_get_value( 'special_program' )->name;
	}

	if ( $post->post_type === "event" ) {
		return "Ein geheimes Event // A secret Event";
	}

	return "Ein ungenannter Film // A unnamed Movie";
}

function ggl_cpt__get_summary( WP_Post|int $post = 0, bool $plain = false ): string {
	$post = get_post( $post );
	$val  = "";
	if ( $post->post_type !== "movie" && $post->post_type !== "event" ) {
		return $val;
	}

	$licensingType = rwmb_get_value( "license_type" ) ?: "full";
	$anonymize     = ( $licensingType != "full" && ! is_user_logged_in() );

	if ( $anonymize ) {
		$val = rwmb_get_value( "anon_summary" );
	} else {
		$val = rwmb_get_value( "summary" );
	}

	return strip_tags( $val );
}

function ggl_cpt__get_worth_to_see( WP_Post|int $post = 0, bool $plain = false ): string {
	$post = get_post( $post );
	$val  = "";
	if ( $post->post_type !== "movie" && $post->post_type !== "event" ) {
		return $val;
	}

	$licensingType = rwmb_get_value( "license_type" ) ?: "full";
	$anonymize     = ( $licensingType != "full" && ! is_user_logged_in() );

	if ( $anonymize ) {
		$val = rwmb_get_value( "anon_worth_to_see" );
	} else {
		$val = rwmb_get_value( "worth_to_see" );
	}

	return strip_tags( $val );
}

function ggl_cpt__admission( $lang = "de" ) {
	$admissionType = rwmb_get_value( "admission_type" );
	switch ( $admissionType ) {
		case "paid":
			return number_format( (float) rwmb_get_value( "admission_fee" ), 2, decimal_separator: ",", thousands_separator: "." ) . " €";
		case "free":
			return $lang == "de" ? "kostenlos" : "free";
		case "donation":
			return $lang == "de" ? "gegen Spende" : "against Donation";
		default:
			return $lang == "de" ? "an Abendkasse angegeben" : "posted at box office";
	}
}

function ggl_cpt__generate_single_ical( WP_Post $post ): Event|null {
	if ( ! in_array( $post->post_type, [ "event", "movie" ] ) ) {
		return null;
	}

	$uniqueID = new UniqueIdentifier( get_post_permalink( $post->ID ) );
	$event    = new Event( $uniqueID );

	$summary        = ( $post->post_type == "movie" ? "🎬 " : "🔮 " ) . ggl_cpt__get_title( $post );
	$admission_de   = ggl_cpt__admission();
	$admission_en   = ggl_cpt__admission( "en" );
	$screeningStart = new DateTimeImmutable( date( "Y-m-d\TH:i:s", rwmb_get_value( "screening_date", post_id: $post->ID ) ) . " Europe/Berlin" );
	if ( $post->post_type === "event" ) {
		$duration = (int) rwmb_get_value( "duration", post_id: $post->ID );
	} else {
		$duration = (int) rwmb_get_value( "running_time", post_id: $post->ID );
	}
	$shortDuration = (int) rwmb_get_value( "short_movie_running_time", post_id: $post->ID );
	$totalDuration = $duration + $shortDuration + 10;
	$age_rating    = rwmb_get_value( "age_rating", post_id: $post->ID );

	/**
	 * Build the description which is displayed in the events details
	 */
	$description = "― English Version Below ―" . PHP_EOL;
	$description .= "Eintritt: {$admission_de}" . PHP_EOL;
	switch ( $post->post_type ) {
		case "movie":
			$description .= "FSK: " . match ( true ) {
					$age_rating < 0 => "ohne Freigabe/unbekannt",
					$age_rating >= 0 => "Ab " . rwmb_get_value( 'age_rating' ) . " freigegeben",
				} . PHP_EOL;
			$description .= "Laufzeit des Films: {$duration} Minuten" . PHP_EOL;
			break;
		case "event":
			$description .= "Start des Events: {$screeningStart->format("H:i")} Uhr" . PHP_EOL;
			if ( rwmb_get_value( "age_restricted", post_id: $post->ID ) ) {
				$description .= "Mindestalter: " . rwmb_get_value( "minimal_age" ) . PHP_EOL;
			}
			break;
	}
	$description .= PHP_EOL . PHP_EOL;


	$description .= "― English Version ―" . PHP_EOL;
	$description .= "Admission: {$admission_en}" . PHP_EOL;
	switch ( $post->post_type ) {
		case "movie":
			$description .= "Admission starts: {$screeningStart->sub(new DateInterval("PT45M"))->format("H.i")}h" . PHP_EOL;
			$description .= "Age Rating: " . match ( true ) {
					$age_rating < 0 => "unrated/unknown rating",
					$age_rating >= 0 => "For ages " . rwmb_get_value( 'age_rating' ) . "+",
				} . PHP_EOL;
			$description .= "Running Time: {$duration} minutes" . PHP_EOL;
			$description .= PHP_EOL . PHP_EOL;
			break;
		case "event":
			$description .= "Event starts at: {$screeningStart->format("H.i")}h" . PHP_EOL;
			if ( rwmb_get_value( "age_restricted", post_id: $post->ID ) ) {
				$description .= "Minimal Attendee Age: " . rwmb_get_value( "minimal_age" ) . PHP_EOL;
			}
			$description .= PHP_EOL . PHP_EOL;
			break;
	}


	/**
	 * Create the Organizer Entity
	 */
	$organizer = new Organizer( new EmailAddress( "info@gegenlicht.net" ), "Unikino GEGENLICHT", null, new EmailAddress( "noreply@gegenlicht.net" ) );

	/**
	 * Get the information for the screening location as event location and build the location entry
	 */
	$location = get_post( rwmb_get_value( "screening_location", post_id: $post->ID ) );
	if ( $location ):
		$street      = rwmb_get_value( "street", post_id: $location->ID );
		$postal_code = rwmb_get_value( "postal_code", post_id: $location->ID );
		$city        = rwmb_get_value( "city", post_id: $location->ID );
		$latitude    = rwmb_get_value( "lat", post_id: $location->ID );
		$longitude   = rwmb_get_value( "long", post_id: $location->ID );

		$eventLocation = new Location( "{$street}, {$postal_code} {$city}", $location->post_title );
		$eventLocation = $eventLocation->withGeographicPosition( new GeographicPosition( $latitude, $longitude ) );

		$event = $event->setLocation( $eventLocation );
	endif;

	/**
	 * Build Reminders
	 */

	$initial         = new DateInterval( "PT6H" );
	$initial->invert = 1;

	$reminder1 = new Alarm( new Alarm\DisplayAction( "Heute ist Kinotag!" ), new Alarm\RelativeTrigger( $initial ), );

	$first         = new DateInterval( "PT60M" );
	$first->invert = 1;

	$reminder2 = new Alarm( new Alarm\DisplayAction( "In einer Stunde geht's los. So langsam solltest du dich auf den Weg machen!" ), new Alarm\RelativeTrigger( $first ), );

	$second         = new DateInterval( "PT30M" );
	$second->invert = 1;

	$reminder3 = new Alarm( new Alarm\DisplayAction( "In dreißig Minuten geht's los. Der Einlass beginnt schon" ), new Alarm\RelativeTrigger( $second ), );

	$thrid         = new DateInterval( "PT10M" );
	$thrid->invert = 1;

	$reminder4 = new Alarm( new Alarm\DisplayAction( "In 10 Minuten geht's los. Jetzt aber schnell ins Kino" ), new Alarm\RelativeTrigger( $thrid ), );

	/**
	 * Now calculate the times for the event
	 */

	$eventStart = new DateTime( $screeningStart, true );
	$eventEnd   = new DateTime( $screeningStart->add( new DateInterval( "PT{$totalDuration}M" ) ), true );

	/**
	 * Now add the event data
	 */

	$event = $event->setUrl( new Uri( get_post_permalink( $post->ID ) ) );
	$event = $event->setSummary( $summary );
	$event = $event->setDescription( $description );
	$event = $event->setOrganizer( $organizer );
	$event = $event->setOccurrence( new TimeSpan( $eventStart, $eventEnd ) );
	$event = $event->addAlarm( $reminder1 );
	$event = $event->addAlarm( $reminder2 );
	$event = $event->addAlarm( $reminder3 );
	$event = $event->addAlarm( $reminder4 );

	return $event;
}

/**
 * Serialize Events into a calendar file
 *
 * @param Event[] $events
 *
 * @return string The events in once ics file as blob
 */
function ggl_cpt__serialize_icals( array $events, $asBlob = true ): string {
	$start = null;
	$end = null;

	foreach ( $events as $event ) {
		$occurrence = $event->getOccurrence();
		if (!$occurrence instanceof TimeSpan) {
			continue;
		}

		if ($start > $occurrence->getBegin()->getDateTime() || $start == null) {
			$start = $occurrence->getBegin()->getDateTime();
		}

		if ($end < $occurrence->getEnd()->getDateTime() || $end == null) {
			$end = $occurrence->getEnd()->getDateTime();
		}
	}

	$calendar = new Calendar( $events );
	$calendar = $calendar->setPublishedTTL( new DateInterval( "P7D" ) );
	$calendar = $calendar->addTimeZone(TimeZone::createFromPhpDateTimeZone(new DateTimeZone("Europe/Berlin"), $start, $end) );
	$factory  = new CalendarFactory();

	$preparedCalendar = $factory->createCalendar( $calendar );
	ob_start();
	echo $preparedCalendar;
	$data = ob_get_clean();

	if ( $asBlob ) {
		return "data:text/calendar;base64," . base64_encode( $data );
	}

	return $data;
}

function ggl_cpt__get_thumbnail_url( WP_Post|int $post = 0, string $size = "full" ): false|string {
	$post = get_post( $post );
	if ( $post->post_type !== "movie" && $post->post_type !== "event" ) {
		return get_the_post_thumbnail_url( $post, $size );
	}

	$fallbackImageUrl = wp_get_attachment_image_url( get_theme_mod( 'anonymous_image' ), size: $size );

	$licensingType = rwmb_get_value( "license_type", post_id: $post->ID ) ?: "full";
	$anonymize     = ( $licensingType != "full" && ! is_user_logged_in() );

	if ( ! $anonymize ) {
		return get_the_post_thumbnail_url( $post, $size ) ?: $fallbackImageUrl;
	}

	$inSpecialProgram = rwmb_get_value( 'program_type', post_id: $post->ID ) == 'special_program';
	if ( $inSpecialProgram ) {
		$specialProgram = rwmb_get_value( 'special_program', post_id: $post->ID );

		return wp_get_attachment_image_url( get_term_meta( $specialProgram->term_id, "anonymous_image", single: true ), size: $size ) ?: $fallbackImageUrl;
	}

	return $fallbackImageUrl;
}

function ggl_cpt__get_thumbnail_id(WP_Post|int $post = 0, string $size = "full"): int {
	return attachment_url_to_postid(ggl_cpt__get_thumbnail_url($post, $size));

}

function ggl_cpt__anonymize_chars( string $chars ): string {
	return preg_replace( '/\S/u', '█', $chars );
}