<?php
/**
 * Get the title of the Custom Post Type entry
 *
 * The function will return the title of the custom post type entry.
 * For `movie` and `event` posts a determination is made if the actual titles
 * may be displayed.
 * For movies this function will always output the original title.
 * For events this function will always return a localized title.
 *
 * If an unsupported post type is supplied to the function, the WordPress
 * function `get_the_title` is called instead.
 *
 * To help with the sanitization process, the function automatically uses the
 * `display` filter while getting the post.
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *  Defaults to global `$post`
 *
 * @return string The title of the custom post type.
 *
 *
 * @see get_the_title() for the handling of unsupported post types
 * @see ggl_get_localized_title() for the determination of the localized titles
 * @see GGL_COMPATIBLE_POST_TYPES for compatible post types
 * @since 3.9.0
 */
function ggl_get_title( int|WP_Post $post = 0 ): string {

	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( ! in_array( $post->post_type, GGL_COMPATIBLE_POST_TYPES ) ) {
		return get_the_title( $post );
	}

	// For team members and supporters return the post title directly
	if ( $post->post_type === "team-member" || $post->post_type === "supporter" ) {
		return $post->post_title;
	}

	// Allow events to always display the title
	if ( $post->post_type === "event" ) {
		return ggl_get_localized_title( $post );
	}

	// Check if the function shall return the real titles for the movie/event
	$show_details = apply_filters( "ggl__show_full_details", false, $post );

	if ( $show_details ) {
		return get_post_meta( $post->ID, "original_title", true );
	}

	$is_in_special_program = get_post_meta( $post->ID, "program_type", true ) === "special_program";
	if ( ! $is_in_special_program ) {
		return __( "An unnamed movie", "ggl-post-types" );
	}

	$assigned_special_program = array_first( wp_get_post_terms( $post->ID, "special-program" ) );
	if ( $assigned_special_program === null ) {
		return __( "An unnamed special", "ggl-post-types" );
	} else {
		return $assigned_special_program->name;
	}
}

/**
 * Output the Title of the Custom Post Type
 *
 * The function will output the result of `ggl_get_title()`
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *  Defaults to global `$post`
 *
 *
 * @see ggl_get_title() for the retrieval of the title
 * @since 3.9.0
 */
function ggl_the_title( int|WP_Post $post = 0 ): void {
	echo ggl_get_title( $post );
}

/**
 * Get the localized title of the Custom Post Type entry
 *
 * The function will return the localized title of the custom post type entry.
 * For `movie` and `event` posts a determination is made if the actual titles
 * may be displayed.
 *
 * If an unsupported post type is supplied to the function, the WordPress
 * function `get_the_title` is called instead.
 *
 * To help with the sanitization process, the function automatically uses the
 * `display` filter while getting the post.
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *  Defaults to global `$post`
 *
 * @return string The title of the custom post type.
 *
 *
 * @see get_the_title() for the handling of unsupported post types
 * @see GGL_COMPATIBLE_POST_TYPES for compatible post types
 * @since 3.9.0
 */
function ggl_get_localized_title( int|WP_Post $post = 0 ): string {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( ! in_array( $post->post_type, [ "movie", "event" ] ) ) {
		return ggl_get_title( $post );
	}

	// Check if the function shall return the real titles for the movie/event
	$show_details = apply_filters( "ggl__show_full_details", false, $post );
	if ( $show_details || $post->post_type === "event" ) {
		// as we only want to get the language for the localization we take a
		// substring of the first two characters here as those are the
		// language part of the BCP 47 tag that are returned by get_user_locale
		$desired_language = substr( get_user_locale(), 0, 2 );
		$meta_prefix      = match ( $desired_language ) {
			"de" => "german",
			default => "english"
		};

		// now return the appropriate post metadata
		return get_post_meta( $post->ID, $meta_prefix . "_title", true );
	}


	$is_in_special_program = get_post_meta( $post->ID, "program_type", true ) === "special_program";
	if ( ! $is_in_special_program ) {
		return __( "An unnamed movie", "ggl-post-types" );
	}

	$assigned_special_program = array_first( wp_get_post_terms( $post->ID, "special-program" ) );
	if ( $assigned_special_program === null ) {
		return __( "An unnamed special", "ggl-post-types" );
	} else {
		return $assigned_special_program->name;
	}


}

/**
 * Output the localized title of the custom post type
 *
 * The function will output the result of `ggl_get_localized_title()`
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *  Defaults to global `$post`
 *
 *
 * @see ggl_get_localized_title() for the retrieval of the title
 * @since 3.9.0
 */
function ggl_the_localized_title( int|WP_Post $post = 0 ): void {
	echo ggl_get_localized_title( $post );
}

/**
 * Get the summary for movies and events
 *
 * This function will automatically return the summary for a movie or an event.
 * If the function determines, that the summary needs to the anonymized it will
 * automatically return the anonymized summary.
 *
 * If the supplied post is neither an event nor a movie the function will return
 * an empty string.
 *
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *   Defaults to global `$post`
 *
 * @return string The summary for the entry.
 */
function ggl_get_summary( int|WP_Post $post = 0 ): string {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( ! in_array( $post->post_type, [ "movie", "event" ] ) ) {
		return "";
	}

	$show_details = apply_filters( "ggl__show_full_details", false, $post );
	$meta_key     = $show_details || $post->post_type === "event" ? "summary" : "anon_summary";

	return get_post_meta( $post->ID, $meta_key, true );
}

/**
 * Output the summary of the custom post type
 *
 * The function will output the result of `ggl_get_summary()`
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *  Defaults to global `$post`
 *
 *
 * @see ggl_get_summary() for the retrieval of the summary
 * @since 3.9.0
 */
function ggl_the_summary( int|WP_Post $post = 0 ): void {
	echo apply_filters( "the_content", ggl_get_summary( $post ) );
}

/**
 * Get the wroth to see/attend content for movies and events
 *
 * This function will automatically return the worth to see/attend text for a
 * movie or an event.
 * If the function determines, that the summary needs to the anonymized it will
 * automatically return the anonymized summary.
 *
 * If the supplied post is neither an event nor a movie the function will return
 * an empty string.
 *
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *   Defaults to global `$post`
 *
 * @return string The summary for the entry.
 */
function ggl_get_worth_to_see( int|WP_Post $post = 0 ): string {
	// Resolve the provided post or fall back to the global post
	$post = get_post( $post, filter: 'display' );

	// Return early if the post type is not supported by the function
	if ( ! in_array( $post->post_type, [ "movie", "event" ] ) ) {
		return "";
	}

	$show_details = apply_filters( "ggl__show_full_details", false, $post );
	$meta_key     = $show_details || $post->post_type === "event" ? "worth_to_see" : "anon_worth_to_see";

	return get_post_meta( $post->ID, $meta_key, true );
}

/**
 * Output the worth to see section of the custom post type
 *
 * The function will output the result of `ggl_get_worth_to_see()`
 *
 * @param int|WP_Post $post Optional. Post ID or `WP_Post` object.
 *  Defaults to global `$post`
 *
 *
 * @see ggl_get_worth_to_see() for the retrieval of the worth to see section
 * @since 3.9.0
 */
function ggl_the_worth_to_see_section( int|WP_Post $post = 0 ): void {
	echo apply_filters( "the_content", ggl_get_worth_to_see( $post ) );
}

function ggl_get_feature_image_url( int|WP_Post $post = 0, $size = "full" ): string {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || ! in_array( $post->post_type, [ "movie", "event" ] ) ) {
		return '';
	}
	$mov_anonymous_image   = rwmb_meta( "movie_anonymous_movie_image", [ "object_type" => "setting" ], "ggl_cpt__settings" );
	$event_anonymous_image = rwmb_meta( "event_anonymous_movie_image", [ "object_type" => "setting" ], "ggl_cpt__settings" );

	$show_details             = apply_filters( "ggl__show_full_details", false, $post );
	$is_in_special_program    = get_post_meta( $post->ID, "program_type", true ) === "special_program";
	$assigned_special_program = array_first( wp_get_post_terms( $post->ID, "special-program" ) );

	if ( ! $show_details && $post->post_type !== "event" ) {
		if ( $is_in_special_program && $assigned_special_program != null ) {
			return ggl_get_special_program_anonymous_image_url( $assigned_special_program, $size );
		}

		return $size == "full" ? $mov_anonymous_image["full_url"] : $mov_anonymous_image["sizes"][ $size ]["url"];
	}

	$anon_movie_image_url = $size == "full" ? $mov_anonymous_image["full_url"] : $mov_anonymous_image["sizes"][ $size ]["url"];
	$anon_event_image_url = $size == "full" ? $event_anonymous_image["full_url"] : $event_anonymous_image["sizes"][ $size ]["url"];

	return get_the_post_thumbnail_url( $post->ID, $size ) ?: ( $post->post_type === "movie" ? $anon_movie_image_url : $anon_event_image_url );

}


function ggl_get_assigned_location( int|WP_Post $post = 0 ): WP_Post|null {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || ! in_array( $post->post_type, [ "movie", "event" ], true ) ) {
		return null;
	}

	return get_post( get_post_meta( $post->ID, "screening_location", true ) );
}

function ggl_get_proposed_by( int|WP_Post $post = 0 ): string {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || ! in_array( $post->post_type, [ "movie", "event" ], true ) ) {
		return "";
	}

	return get_post_meta( $post->ID, "selected_by", true );
}

function ggl_get_proposers( int|WP_Post $post = 0 ): array {
	$post = get_post( $post, filter: 'display' );
	if ( $post === null || ! in_array( $post->post_type, [ "movie", "event" ], true ) ) {
		return [];
	}

	return match ( ggl_get_proposed_by( $post ) ) {
		"coop" => array_map( function ( $id ) {
			return get_post( $id, filter: 'display' );
		}, get_post_meta( $post->ID, "cooperation_partner_id" ) ),
		"member" => array_map( function ( $id ) {
			return get_post( $id, filter: 'display' );
		}, get_post_meta( $post->ID, "team_member_id" ) ),
		default => []
	};
}