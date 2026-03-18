<?php
add_action("init", function () {
	add_filter("ggl__show_full_details", "ggl_cpt__default_show_full_details", accepted_args: 2);
});
function ggl_cpt__default_show_full_details(bool $display_details, WP_Post $post) : bool {
	return ( rwmb_get_value( "license_type", post_id: $post->ID ) == "full" || is_user_logged_in());
}