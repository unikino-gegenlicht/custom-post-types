<?php
/**
 * Replacements for Yoast-SEO
 *
 * This file registers some replacements for Yoast-SEO to enhance the social media display for the custom post types
 */

function ggl_pt_get_title() {
	$title = rwmb_get_value("german_title");
	$englishTitle =  rwmb_get_value("english_title");
	$anonymize = rwmb_get_value("license_type") !== "full";
	if (!$anonymize) {
		return $title . " // " .  $englishTitle;
	}

	$inSpecialProgram = rwmb_get_value("program_type") === "special_program";
	if ($inSpecialProgram) {
		$specialProgram = rwmb_get_value("special_program");
		return $specialProgram->name;
	}

	if (get_post()->post_type === "movie") {
		return "Ein ungenannter Film";
	} else {
		return "Ein ungenanntes Event";
	}
}

function ggl_pt_screening_date() {
	$screeningStart = (int)rwmb_get_value("screening_date");
	return date("d.m.Y | H:i", $screeningStart);
}

function ggl_pt_text() {
	$anonymize = rwmb_get_value("license_type") !== "full";
	if (!$anonymize) {
		return rwmb_get_value("summary");
	} else {
		return rwmb_get_value("anon_summary");
	}
}

function ggl_pt_admission() {
	$admissionType = rwmb_get_value("admission_type");
	switch ($admissionType) {
		case "paid":
			return number_format((float)rwmb_get_value("admission_fee"), 2, decimal_separator: ",", thousands_separator: ".") . " €";
		case "free":
			return "kostenlos";
		case "donation":
			return "spenden Erbeten";
		default:
			return "an Abendkasse angegeben";
	}
}

function  ggl_pt_age_rating() {
	$ageRating = (int) rwmb_get_value("age_rating");
	switch ($ageRating) {
		case -3:
		case -2:
		case -1:
			return "ohne";
		default:
			return $ageRating;
	}
}

function ggl_pt_linebreak() {
	return PHP_EOL.PHP_EOL;
}