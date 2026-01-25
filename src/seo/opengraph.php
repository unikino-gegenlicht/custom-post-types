<?php

function ggl_cpt__change_opengraph_image_url($url): string {
	global $post;
	$url = ggl_cpt__get_thumbnail_url($post, "small");
	return $url;
}

function ggl_cpt__change_opengraph_image_height($_): string {
	global $post;
	$image_meta = wp_get_attachment_metadata(get_post_thumbnail_id($post));
	return $image_meta['height'];
}

function ggl_cpt__change_opengraph_image_width($_): string {
	global $post;
	$image_meta = wp_get_attachment_metadata(get_post_thumbnail_id($post));
	return $image_meta['width'];
}