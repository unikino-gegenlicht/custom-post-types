<?php

namespace CinemaPress;

/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

use Sokil\IsoCodes\IsoCodesFactory;
use Sokil\IsoCodes\TranslationDriver\SymfonyTranslationDriver;

function cp_county_mapping(): array {
	$locale = get_user_locale();
	$translation_driver = new SymfonyTranslationDriver();
	$translation_driver->setLocale($locale);
	$iso_codes = new IsoCodesFactory(null, $translation_driver);

	$map = array();
	foreach ($iso_codes->getCountries() as $country) {
		$map[$country->getNumericCode()] = $country->getLocalName();
	}

	return $map;
}


