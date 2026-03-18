<?php

/**
 * Event class for adding Event Schema to the SchemaGraph output of WPSEO
 */
class Event {
	/**
	 * Context variables for WPSEO schema
	 *
	 * @var WPSEO_Schema_Context
	 */
	public WPSEO_Schema_Context $context;

	/**
	 * Create a new Event Schema object
	 *
	 * @param $context
	 */
	public function __construct($context) {
		$this->context = $context;
	}

	public function is_needed(): bool {
		return is_singular(["movie", "event"]);
	}

	public function generate() {
		$canonical_url = YoastSEO()->meta->for_current_page()->canonical;

		$data = [
			"@type" => "Event",
			"@id" => $canonical_url,
			"title" => ggl_cpt__get_title()
		];
	}
}