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

	// Check if the function shall return the real titles for the movie/event
	$show_details = apply_filters( "ggl__show_full_details", false, $post );

	if ( $post->post_type === "event" ) {
		return $show_details ? ggl_get_localized_title( $post ) : __( "An unnamed event", "ggl-post-types" );
	}

	if ( $show_details ) {
		return get_post_meta( $post->ID, "original_title", true );
	}

	$is_in_special_program = get_post_meta( $post->ID, "program_type", true ) === "special_program";
	if ( ! $is_in_special_program ) {
		return get_post_meta( $post->ID, "original_title", true );
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
	if ( $show_details ) {
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

	if ( $post->post_type === "event" ) {
		return __( "An unnamed event", "ggl-post-types" );
	}


	$is_in_special_program = get_post_meta( $post->ID, "program_type", true ) === "special_program";
	if ( ! $is_in_special_program ) {
		return get_post_meta( $post->ID, "original_title", true );
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