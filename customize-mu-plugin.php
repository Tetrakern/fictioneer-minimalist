<?php
/**
 * Plugin Name: Fictioneer Customization
 * Description: Scripts to customize the theme or child theme.
 * Version: 1.0.0
 * Author: YOUR NAME
 * License: GNU General Public License v3.0 or later
 * License URI: http://www.gnu.org/licenses/gpl.html
 */


/**
 * Adds actions and filters after the theme has loaded
 * and all hooks have been registered
 */

function custom_initialize() {
  // add_filter( 'fictioneer_filter_post_meta_items', 'custom_modify_post_meta_items' );
}
add_action( 'after_setup_theme', 'custom_initialize', 99 );

/**
 * Removes the icons from the post meta row and separates the items with a "|"
 *
 * Note: Add ".post__meta { gap: .5rem; }" under Appearance > Customize > Custom CSS.
 *
 * @param array $output  The HTML of the post meta items to be rendered.
 *
 * @return array The updated items.
 */

function custom_modify_post_meta_items( $output ) {
  // Remove icons
  $output = array_map( function( $item ) { return preg_replace( '/<i[^>]*>.*?<\/i>/', '', $item ); }, $output );
  $count = 0;
  $new_output = [];

  // Add slashes as divider
  foreach ( $output as $key => $value ) {
    if ( $count > 0 ) {
      $new_output[ $key . '_slash' ] = '<span class="divider">|</span>';
    }

    $new_output[ $key ] = $value;

    $count++;
  }

  // Continue filter
  return $new_output;
}
