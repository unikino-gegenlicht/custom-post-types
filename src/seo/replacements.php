<?php
/**
 * Replacements for Yoast-SEO
 *
 * This file registers some replacements for Yoast-SEO to enhance the social media display for the custom post types
 */


function ggl_cpt__seo_title() {
	global $post;

	return ggl_get_localized_title( $post );
}

function ggl_cpt__seo_date() {
	global $post;

	return ggl_get_starting_time( $post )->format( "d.m.Y \u\m H:i" );
}

function ggl_cpt__seo_summary() {
	global $post;

	$summary = mb_trim( strip_tags( ggl_get_summary( $post ) ) );

	return substr( $summary, 0, 147 ) . "…";


}

function ggl_cpt__seo_tagline() {
	global $post;

	$post             = get_post();
	$audioType        = rwmb_meta( 'audio_type' );
	$audioLanguage    = rwmb_meta( 'audio_language' );
	$subtitleLanguage = rwmb_meta( 'subtitle_language' );

	if ( $audioType == 'original' ) {
		$versionTag  = match ( $subtitleLanguage ) {
			"eng" => "OmeU",
			"zxx" => "OV",
			default => "OmU"
		};
		$versionName = match ( $subtitleLanguage ) {
			"zxx" => "{$audioLanguage} Original ohne Untertitel",
			default => "{$audioLanguage}. Original mit {$subtitleLanguage}. Untertiteln"
		};
	} else {
		$versionTag  = match ( $subtitleLanguage ) {
			"eng" => "SFmeU",
			"zxx" => "SF",
			default => "SFmU"
		};
		$versionName = match ( $subtitleLanguage ) {
			"zxx" => "{$audioLanguage} Synchronfassung ohne Untertitel",
			default => "{$audioLanguage}. Synchronfassung mit {$subtitleLanguage}. Untertiteln"
		};
	}

	$running_time = ggl_get_running_time( $post );

	$ageRating = match ( (int) rwmb_get_value( "age_rating" ) ) {
		- 3, - 2, - 1 => "ohne/unbekannt",
		default => (int) rwmb_get_value( "age_rating" ),
	};

	$proposal_by = rwmb_get_value( "selected_by", post_id: $post->ID );
	$proposer_id = match ( $proposal_by ) {
		"member" => rwmb_get_value( "team_member_id", post_id: $post->ID ),
		"coop" => rwmb_get_value( "cooperation_partner_id", post_id: $post->ID ),
		default => - 1,
	};

	$names = [];
	foreach ( $proposer_id as $id ) {
		$names[] = get_post( $id )->post_title;
	}
	$proposer_string = match ( $proposal_by ) {
		"member" => "| ausgesucht von: " . join( "+", $names ),
		"coop" => "| in Kooperation mit: " . join( "+", $names ),
		default => ""
	};

	$countries  = rwmb_get_value( "country" );
	$countryStr   = join( "/", ggl_resolve_country_list( $countries ) );

	$releaseYear = ggl_get_release_date( $post )->format( "Y" );

	return "{$versionTag} ($versionName) | {$countryStr} {$releaseYear} | Laufzeit: {$running_time} Minuten | FSK: {$ageRating} {$proposer_string}";
}