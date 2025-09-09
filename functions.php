<?php

use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\EmailAddress;
use Eluceo\iCal\Domain\ValueObject\GeographicPosition;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\Organizer;
use Eluceo\iCal\Domain\ValueObject\Alarm;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\DateTime;
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
	$output = array();
	foreach ( GGL_COUNTRIES as $code ) {
		$translatedName  = __( $code, 'ggl-i18n' );
		$output[ $code ] = $translatedName;
	}

	return $output;
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

function ggl_cpt__generate_single_ical( WP_Post $post ): Event | null {
	if ( ! in_array( $post->post_type, [ "event", "movie" ] ) ) {
		return null;
	}

	$uniqueID = new UniqueIdentifier(get_post_permalink($post->ID));
	$event = new Event($uniqueID);

	$summary        = ($post->post_type == "movie" ? "🎬 " : "🔮 ").ggl_cpt__get_title( $post );
	$admission_de   = ggl_cpt__admission();
	$admission_en   = ggl_cpt__admission( "en" );
	$screeningStart = new DateTimeImmutable( date( "Y-m-d\TH:i:s", rwmb_get_value( "screening_date", post_id: $post->ID ) ) . " Europe/Berlin" );
	$runningTime    = (int) rwmb_get_value( "running_time", post_id: $post->ID );
	$shortDuration  = (int) rwmb_get_value( "short_movie_running_time", post_id: $post->ID );
	$totalDuration  = $runningTime + $shortDuration + 10;
	$age_rating     = rwmb_get_value( "age_rating", post_id: $post->ID );

	/**
	 * Build the description which is displayed in the events details
	 */
	$description = "― English Version Below ―" . PHP_EOL;
	$description .= "Eintritt: {$admission_de}" . PHP_EOL;
	switch ( $post->post_type ) {
		case "movie":
			$description .= "Start der Vorstellung: {$screeningStart->sub(new DateInterval("PT45M"))->format("H.i")} Uhr" . PHP_EOL;
			$description .= "FSK: " . match ( true ) {
					$age_rating < 0 => "ohne Freigabe/unbekannt",
					$age_rating >= 0 => "Ab " . rwmb_get_value( 'age_rating' ) . " freigegeben",
				} . PHP_EOL;
			$description .= "Laufzeit des Films: {$runningTime} Minuten". PHP_EOL;
			break;
		case "event":
			$description .= "Start des Events: {$screeningStart->format("H:i")} Uhr". PHP_EOL;
			if ( rwmb_get_value( "age_restricted", post_id: $post->ID ) ) {
				$description .= "Mindestalter: " . rwmb_get_value( "minimal_age" ). PHP_EOL;
			}
			break;
	}
	$description .= PHP_EOL . PHP_EOL;


	$description .= "― English Version ―" . PHP_EOL;
	$description .= "Admission: {$admission_en}". PHP_EOL;
	switch ( $post->post_type ) {
		case "movie":
			$description .= "Admission starts: {$screeningStart->sub(new DateInterval("PT45M"))->format("H.i")}h". PHP_EOL;
			$description .= "Age Rating: " . match ( true ) {
					$age_rating < 0 => "unrated/unknown rating",
					$age_rating >= 0 => "For ages " . rwmb_get_value( 'age_rating' ) . "+",
				}. PHP_EOL;
			$description .= "Running Time: {$runningTime} minutes". PHP_EOL;
			$description .= PHP_EOL . PHP_EOL;
			break;
		case "event":
			$description .= "Event starts at: {$screeningStart->format("H.i")}h". PHP_EOL;
			if ( rwmb_get_value( "age_restricted", post_id: $post->ID ) ) {
				$description .= "Minimal Attendee Age: " . rwmb_get_value( "minimal_age" ). PHP_EOL;
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
	if ($location):
	$street        = rwmb_get_value( "street", post_id: $location->ID );
	$postal_code   = rwmb_get_value( "postal_code", post_id: $location->ID );
	$city          = rwmb_get_value( "city", post_id: $location->ID );
	$latitude      = rwmb_get_value( "lat", post_id: $location->ID );
	$longitude     = rwmb_get_value( "long", post_id: $location->ID );

	$eventLocation = new Location("{$street}, {$postal_code} {$city}", $location->post_title);
	$eventLocation = $eventLocation->withGeographicPosition(new GeographicPosition($latitude, $longitude));

	$event = $event->setLocation($eventLocation);
	endif;

	/**
	 * Build Reminders
	 */

	$initial = new DateInterval("PT6H");
	$initial->invert = 1;

	$reminder1 = new Alarm(
		new Alarm\DisplayAction("Heute ist Kinotag!"),
		new Alarm\RelativeTrigger($initial),
	);

	$first = new DateInterval("PT60M");
	$first->invert = 1;

	$reminder2 = new Alarm(
		new Alarm\DisplayAction("In einer Stunde geht's los. So langsam solltest du dich auf den Weg machen!"),
		new Alarm\RelativeTrigger($first),
	);

	$second = new DateInterval("PT30M");
	$second->invert = 1;

	$reminder3 = new Alarm(
		new Alarm\DisplayAction("In dreißig Minuten geht's los. Der Einlass beginnt schon"),
		new Alarm\RelativeTrigger($second),
	);

	$thrid = new DateInterval("PT10M");
	$thrid->invert = 1;

	$reminder4 = new Alarm(
		new Alarm\DisplayAction("In 10 Minuten geht's los. Jetzt aber schnell ins Kino"),
		new Alarm\RelativeTrigger($thrid),
	);

	/**
	 * Now calculate the times for the event
	 */

	$eventStart = new DateTime($screeningStart, true);
	$eventEnd = new DateTime($screeningStart->add(new DateInterval("PT{$totalDuration}M")), true);

	/**
	 * Now add the event data
	 */

	$event = $event->setUrl(new Uri(get_post_permalink($post->ID)));
	$event = $event->setSummary($summary);
	$event = $event->setDescription($description);
	$event = $event->setOrganizer($organizer);
	$event = $event->setOccurrence(new TimeSpan($eventStart, $eventEnd));
	$event = $event->addAlarm($reminder1);
	$event = $event->addAlarm($reminder2);
	$event = $event->addAlarm($reminder3);
	$event = $event->addAlarm($reminder4);

	return $event;
}

/**
 * Serialize Events into a calendar file
 *
 * @param Event[] $events
 *
 * @return string The events in once ics file as blob
 */
function ggl_cpt__serialize_icals(array $events, $asBlob = true): string {
	$calendar = new Calendar($events);
	$calendar->setPublishedTTL(new DateInterval("P7D"));
	$factory = new CalendarFactory();

	$preparedCalendar = $factory->createCalendar($calendar);
	ob_start();
	echo $preparedCalendar;
	$data = ob_get_clean();

	if ($asBlob) return "data:text/calendar;base64," . base64_encode($data);
	return $data;
}