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
	public function __construct( $context ) {
		$this->context = $context;
	}

	public function is_needed(): bool {
		return is_singular( [ "movie", "event" ] );
	}

	public function generate() {
		global $post;
		$meta = YoastSEO()->meta->for_post( $post->ID );


		$starting_time         = ggl_get_starting_time( $post );
		$duration              = ggl_get_running_time( $post );
		$running_time_interval = new DateInterval( "PT{$duration}M" );
		$ending_time           = $starting_time->add( $running_time_interval );
		$offer_start_interval  = new DateInterval( "P1M" );
		$offer_start_time      = $starting_time->sub( $offer_start_interval );

		$proposers = ggl_get_proposers( $post );

		if ( count( $proposers ) > 1 ) {
			$performer = [];
			foreach ( $proposers as $proposer ) {
				$performer[] = [
					"type" => ggl_get_proposed_by( $post ) === "coop" ? "Organization" : "Person",
					"name" => $proposer->post_title,
					"url"  => get_post_permalink( $proposer )
				];
			}
		} else {
			$performer = [
				"type" => ggl_get_proposed_by( $post ) === "coop" ? "Organization" : "Person",
				"name" => $proposers[0]->post_title,
				"url"  => get_post_permalink( $proposers[0] )
			];
		}


		$data = [
			"@type"                           => "Event",
			"@id"                             => $meta->canonical . '#/screening',
			"name"                            => ggl_get_localized_title(),
			"mainEntityOfPage"                => [ '@id' => $meta->canonical ],
			"description"                     => mb_trim( strip_tags( ggl_get_summary() ) ),
			"eventStatus"                     => "https://schema.org/EventScheduled",
			"image"                           => ggl_get_feature_image_url( $post ),
			"inLanguage"                      => substr( ggl_get_audio_language( $post ), 0, 2 ),
			"maximumAttendeeCapacity"         => 80,
			"maximumPhysicalAttendeeCapacity" => 80,
			"startDate"                       => $starting_time->format( DATE_ATOM ),
			"endDate"                         => $ending_time->format( DATE_ATOM ),
			"offers"                          => [
				"@type"         => "Offer",
				"price"         => ggl_get_numerical_admission_fee( $post ),
				"priceCurrency" => "EUR",
				"url"           => ggl_get_event_booking_url( $post ),
				"availability"  => "https://schema.org/InStock",
				"validFrom"     => $offer_start_time->format( DATE_ATOM ),
			],
			"location"                        => ggl_get_location_schema_markup_data( ggl_get_assigned_location( $post ) ),
			"organizer"                       => [
				"@type" => "Organization",
				"name"  => "Unikino GEGENLICHT",
				"url"   => get_home_url( scheme: "https" )
			],
			"performer"                       => $performer
		];

		return $data;
	}
}