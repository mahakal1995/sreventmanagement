<?php
function gmap_embed_no_of_post() {
	$count_posts = wp_count_posts( 'wpgmapembed' );

	return isset( $count_posts->draft ) ? $count_posts->draft : 0;

}

function gmap_embed_is_using_premium_version() {
	return gmap_embed_no_of_post() == 0 or strlen( trim( get_option( 'wpgmapembed_license' ) ) ) == 32;
}