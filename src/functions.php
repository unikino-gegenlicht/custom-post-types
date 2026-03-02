<?php

use DateTimeZone as PhpDateTimeZone;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\Entity\TimeZone;
use Eluceo\iCal\Domain\ValueObject\Alarm;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\EmailAddress;
use Eluceo\iCal\Domain\ValueObject\GeographicPosition;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\Organizer;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\UniqueIdentifier;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

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

function refresh_permalinks() {
	flush_rewrite_rules( false );
}

function unregister_taxonomies() {
	unregister_taxonomy( 'semester' );
	unregister_taxonomy( 'special-program' );
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

function generate_country_mapping(): array {
	$locale = get_locale();
	$output_prepared = false;
	$output = wp_cache_get("ggl__cc_mapping_" . $locale, "ggl", found: $output_prepared);
	if ( $output_prepared ) {
		return $output;
	}

	$country_source_file_path = dirname(__FILE__) . "/../assets/iso3166.csv";
	$rows = array_map('str_getcsv', file($country_source_file_path));
	$header = array_shift($rows);
	$countries = array();
	foreach ($rows as $row) {
		$countries[] = array_combine($header, $row);
	}

	foreach ($countries as $country) {
		$output[$country["num_id"]] = str_replace(" // ⚠️ )",")", ((str_starts_with($locale, "de") ? $country["german_name"] : $country["english_name"] ). "\n(". (str_starts_with($locale, "de") ? $country["official_german_name"] . " // ⚠️ " . $country["german_comment"] : $country["official_english_name"] . " // ⚠️ " . $country["english_comment"] ). ")"));
	}

	$sorted = $output;

	$collator = new Collator($locale);
	usort($sorted, fn($a,$b) => $collator->compare($a,$b));

	$mapping = [];

	foreach ($sorted as $key => $value) {
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