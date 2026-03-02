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
	global $post;

	if ( $post->post_type !== 'event' && $post->post_type !== 'movie' ) {
		return $url;
	}

	$url = ggl_cpt__get_thumbnail_url( $post, "opengraph" );

	return $url;
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
	global $post;
	if ( $post->post_type !== 'event' && $post->post_type !== 'movie' ) {
		return $height;
	}
	$image_meta = wp_get_attachment_metadata( ggl_cpt__get_thumbnail_id( $post , "opengraph") );
	return $image_meta['sizes']['opengraph']['height'] ?: $height;
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
	global $post;
	if ( $post->post_type !== 'event' && $post->post_type !== 'movie' ) {
		return $width;
	}
	$image_meta = wp_get_attachment_metadata( ggl_cpt__get_thumbnail_id( $post, "opengraph" ) );
	return $image_meta['sizes']['opengraph']['width'] ?: $width;
}