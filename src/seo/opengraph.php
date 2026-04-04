<?php

/**
 * Override OpenGraph Image Url
 *
 * This filter exchanges the opengraph image for movies and events to the one also present on the website.
 * This helps with movies that we screen without an advertisement license like our special programs
 *
 * @param $url string Original URL that was set by YoastSEO
 *
 * @return string Updated URL if the post is a movie or event
 */
function ggl_cpt__change_opengraph_image_url( string $url ): string {
	if (!is_singular(["movie", "event"])) {
		return $url;
	}
	global $post;

	if ( $post->post_type !== 'event' && $post->post_type !== 'movie' ) {
		return $url;
	}

	return ggl_get_feature_image_url( $post->ID, "opengraph" );
}


/**
 * Override OpenGraph Height
 *
 * This filter overrides the returned height for the thumbnail of a movie or event
 *
 * @param $height string The input height
 *
 * @return string the new height
 */
function ggl_cpt__change_opengraph_image_height( string $height ): string {
	if (!is_singular(["movie", "event"])) {
		return $height;
	}
	global $post;
	if ( $post->post_type !== 'event' && $post->post_type !== 'movie' ) {
		return $height;
	}

	return 1200;
}


/**
 * Override OpenGraph Width
 *
 * This filter overrides the returned width for the thumbnail of a movie or event
 *
 * @param $width string The input width
 *
 * @return string the new width
 */
function ggl_cpt__change_opengraph_image_width( string $width ): string {
	if (!is_singular(["movie", "event"])) {
		return $width;
	}
	global $post;
	if ( $post->post_type !== 'event' && $post->post_type !== 'movie' ) {
		return $width;
	}

	return 675;
}