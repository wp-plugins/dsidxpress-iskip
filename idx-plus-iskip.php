<?php
/*
* Plugin Name: IDX+ iSkip
* Description: Enable iPhoto-like slideshow when hovering over your dsIDXpress listing photo
* Version: 1.2.1
* Author: Katz Web Services, Inc.
* Author URI: http://www.idxplus.net
* Text Domain: idx-plus
*/

add_action('wp_enqueue_scripts', 'idx_plus_iskip_enqueue');
function idx_plus_iskip_enqueue() {
	if(idx_plus_show_iskip()) {
		wp_enqueue_script('iskip', plugins_url( 'iSkip/jquery.iskip.js', __FILE__), array('jquery'));
	}
}

function idx_plus_show_iskip() {
	global $wp_query;

	// If this is a listing, return. We only want this for search results.
	return (isset($wp_query->query_vars['idx-action']) && $wp_query->query_vars['idx-action'] !== 'details');
}

add_action('wp_footer', 'idx_plus_iskip_js');

/**
 * Add iSkip JS magic to dsIDXpress search results footer
 */
function idx_plus_iskip_js() {

	if(!idx_plus_show_iskip()) { return; }
?>
<script>
jQuery(document).ready(function($) {
	if(dsidx.dataSets['results']) {

		dsidx.dataSets['results'].forEach(function(result, index, results) {

			// There are no photos
			if(result.PhotoCount <= 1) { return; }

			var photos = [];

			for(var i=0; i < result.PhotoCount; i++) {
				photos[i] = result.PhotoUriBase + i+'-medium.jpg';
			}

			// Replace the title to hide the tooltip
			$('#dsidx-listings li[class*="dsidx-listing"]')
			.eq(index)
			.find('.dsidx-photo img')
				.addClass('iskip')
				.attr('data-height', $(this).parents('.dsidx-photo').height() )
				.hover(function() {
					$(this).height( $(this).attr('data-height') );
					$(this).attr('_t', $(this).attr('title')).attr('title', null);
				}, function() {
					$(this).height(null);
					$(this).attr('title', $(this).attr('_t')).attr('_t', null);
				}
				).off('iskip').iskip({
					images: photos,
					method:'mousemove'
				});
		});
	}
});
</script>
<?php
}