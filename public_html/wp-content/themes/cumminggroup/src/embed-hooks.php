<?php

/**
 * Modify YouTube Embeds to Disable Cookies.
 */
add_filter( 'embed_oembed_html', function( $html ) {
	if ( str_contains( $html, 'youtube.com' ) ) {
		$html = str_replace( 'youtube.com', 'youtube-nocookie.com', $html );
	}
	return $html;
}, 10 );