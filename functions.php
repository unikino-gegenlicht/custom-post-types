<?php

namespace CinemaPress;

/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

function cp_county_mapping(): array {
	$iso_codes = iso_codes();
	$map = array();
	foreach ($iso_codes->getCountries() as $country) {
		$map[$country->getNumericCode()] = $country->getLocalName();
	}

	$collator = collator_create(get_user_locale());
	collator_asort($collator, $map);
	return $map;
}


