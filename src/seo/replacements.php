<?php
/**
 * Replacements for Yoast-SEO
 *
 * This file registers some replacements for Yoast-SEO to enhance the social media display for the custom post types
 */

function ggl_pt_get_title() {

	$post = get_post();

	$title        = rwmb_get_value( "german_title", post_id: $post->ID );
	$englishTitle = rwmb_get_value( "english_title", post_id: $post->ID );

	if ( $post->post_type === 'event' ) {
		if ( $title === $englishTitle ) {
			return $title;
		} else {
			return "{$title} // {$englishTitle}";
		}
	}

	$anonymize = rwmb_get_value( "license_type", post_id: $post->ID ) !== "full";
	if ( ! $anonymize ) {
		return "{$title} // {$englishTitle}";
	}

	$inSpecialProgram = rwmb_get_value( "program_type", post_id: $post->ID ) === "special_program";
	if ( $inSpecialProgram ) {
		$specialProgram = rwmb_get_value( "special_program", post_id: $post->ID );

		return $specialProgram->name;
	}

	if ( $post->post_type === "movie" ) {
		return "Ein ungenannter Film // A secret Movie";
	} else {
		return "Ein ungenanntes Event // A secret event";
	}
}

function ggl_pt_screening_date() {
	$post = get_post();
	$screeningStart = (int) rwmb_get_value( "screening_date", post_id: $post->ID );

	return date( "d.m.Y@H:i", $screeningStart );
}

function ggl_pt_text() {
	$anonymize = rwmb_get_value( "license_type" ) !== "full";
	$original_text = match ($anonymize) {
		false => rwmb_get_value("summary"),
		true => rwmb_get_value("anon_summary")
	};

	$return_string = "";
	$words = explode(" ", $original_text);
	foreach ($words as $word ) {
		if ((strlen($return_string) + strlen($word)) < 150) {
			$return_string .= " {$word}";
		}
	}
	$sentences = array_slice(explode(".", $return_string), 0, -1);
	return join(".", $sentences) . "…";
}

function ggl_pt_details() {
	$audioType        = rwmb_meta( 'audio_type' );
	$audioLanguage    = rwmb_meta( 'audio_language' );
	$subtitleLanguage = rwmb_meta( 'subtitle_language' );

	if ( $audioType == 'original' ) {
		$versionTag = match ( $subtitleLanguage ) {
			"eng" => "OmeU",
			"zxx" => "OV",
			default => "OmU"
		};
		$versionName = match ( $subtitleLanguage ) {
			"zxx" => "{$audioLanguage} Original",
			default => "{$audioLanguage}. Original mit {$subtitleLanguage}. Untertiteln"
		};
	} else {
		$versionTag = match ( $subtitleLanguage ) {
			"eng" => "SFmeU",
			"zxx" => "SF",
			default => "SFmU"
		};
		$versionName = match ( $subtitleLanguage ) {
			"zxx" => "{$audioLanguage} Synchronfassung",
			default => "{$audioLanguage}. Synchronfassung mit {$subtitleLanguage}. Untertiteln"
		};
	}

	$running_time = rwmb_get_value( 'running_time' );

	$ageRating = match ( (int) rwmb_get_value( "age_rating" ) ) {
		- 3, - 2, - 1 => "ohne/unbekannt",
		default => (int) rwmb_get_value( "age_rating" ),
	};

	$proposal_by = rwmb_get_value( "selected_by", get_post() );
	$proposer_id = match ( $proposal_by ) {
		"member" => rwmb_get_value( "team_member_id", get_post() ),
		"coop" => rwmb_get_value( "cooperation_partner_id", get_post() ),
		default => - 1,
	};

	$proposer = get_post($proposer_id)->post_title;

	$proposer_string = match ( $proposal_by ) {
		"member" => "| ausgesucht von: {$proposer}",
		"coop" => "| in Kooperation mit: {$proposer}",
		default => ""
	};

	$countries  = rwmb_get_value( "country" );
	$countryStr = join( "/", $countries );

	$releaseYear = date( 'Y', strtotime(rwmb_get_value( 'release_date' )) );

	return "{$versionTag} ($versionName) | {$countryStr} {$releaseYear} | Laufzeit: {$running_time} Minuten | FSK: {$ageRating} {$proposer_string}";
}