<?php
/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

namespace CinemaPress\interfaces;

interface Taxonomy {

	/**
	 * The taxonomy's slug
	 */
	const string SLUG = 'slug';

	/**
	 * Get the data required for registering a taxonomy
	 *
	 * @return array
	 */
	public static function register_data(): array;

	/**
	 * Register the meta boxes for the taxonomies.
	 *
	 * @param array $meta_boxes Already existing metaboxes (automatically provided by MetaBox)
	 *
	 * @return array The registered meta boxes
	 */
	public static function meta_boxes(array $meta_boxes): array;
}