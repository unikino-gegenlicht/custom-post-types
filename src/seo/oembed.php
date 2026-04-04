<?php

function ggl_cpt__update_oembed_data( array $data, WP_Post $_post, int $width, int $height ): array {
	global $post;
	$post = $_post;
	if ( $post->post_type !== 'event' && $post->post_type !== 'movie' ) {
		return $data;
	}

	// remove unused data
	unset( $data['html'] );
	unset( $data['width'] );
	unset( $data['height'] );

	// switch over to the link oembed type and set the fullsize thumbnail
	$data['type']          = "link";
	$data['thumbnail_url'] = ggl_get_thumbnail_url( $post, "opengraph" );

	$image_meta = wp_get_attachment_metadata( ggl_cpt__get_thumbnail_id( $post ) );
	$data['thumbnail_width']  = $image_meta['sizes']['opengraph']['width'];
	$data['thumbnail_height'] = $image_meta['sizes']['opengraph']['height'];

	// change the author data
	$proposal_by = rwmb_get_value( "selected_by", post_id: $post->ID );
	$proposer_id = match ( $proposal_by ) {
		"member" => rwmb_get_value( "team_member_id", post_id: $post->ID ),
		"coop" => rwmb_get_value( "cooperation_partner_id", post_id: $post->ID ),
		default => - 1,
	};

	$names = [];
	foreach ( $proposer_id as $id ) {
		$names[] = get_post( $id )->post_title;
	}
	$data["author_name"] = join( " + ", $names );
	$data["author_url"]  = get_permalink( get_post( $proposer_id[0] ) );


	return $data;
}