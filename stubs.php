<?php

if ( ! defined( 'FICTIONEER_ENABLE_MENU_TRANSIENTS' ) ) {
  define( 'FICTIONEER_ENABLE_MENU_TRANSIENTS', true );
}

if ( ! defined( 'FICTIONEER_UPDATE_CHECK_TIMEOUT' ) ) {
  define( 'FICTIONEER_UPDATE_CHECK_TIMEOUT', 43200 ); // 12 hours
}

/**
 * PHP rand()
 *
 * @link https://www.php.net/manual/en/function.rand.php
 *
 * @param int $min  Optional minimum.
 * @param int $max  Optional maximum.
 *
 * @return int Random number.
 */

function rand( int $min = null, int $max = null ) {}

/**
 * Updates post author user caches for a list of post objects.
 *
 * @since WP 6.1.0
 *
 * @param WP_Post[] $posts Array of post objects.
 */

function update_post_author_caches( $posts ) {}

/**
 * PHP random_bytes()
 *
 * Generates a string containing uniformly selected
 * random bytes with the requested length.
 *
 * @link https://www.php.net/manual/en/function.random-bytes.php
 *
 * @param int $length  Length of the requested random string.
 *
 * @return string Random string with the requested length.
 */

function random_bytes( int $length ) {}

/**
 * Outputs an admin notice.
 *
 * @since 6.4.0
 *
 * @param string $message  The message to output.
 * @param array  $args {
 *   Optional. An array of arguments for the admin notice. Default empty array.
 *
 *   @type string   $type                Optional. The type of admin notice.
 *                                       For example, 'error', 'success', 'warning', 'info'.
 *                                       Default empty string.
 *   @type bool     $dismissible         Optional. Whether the admin notice is dismissible. Default false.
 *   @type string   $id                  Optional. The value of the admin notice's ID attribute. Default empty string.
 *   @type string[] $additional_classes  Optional. A string array of class names. Default empty array.
 *   @type string[] $attributes          Optional. Additional attributes for the notice div. Default empty array.
 *   @type bool     $paragraph_wrap      Optional. Whether to wrap the message in paragraph tags. Default true.
 * }
 */

function wp_admin_notice( string $message, array $args = array() ) {}

/**
 * Render Elementor template
 *
 * @param string $location  Template location.
 */

function elementor_theme_do_location( $location ) {}

/**
 * Replaces insecure HTTP URLs to the site in the given content, if configured to do so.
 *
 * @param string $content  Content to replace URLs in.
 *
 * @return string Filtered content.
 */

function wp_replace_insecure_home_url( $content ) {}

/**
 * Helper to add color theme option
 *
 * @since 5.12.0
 * @since 5.21.2 - Improved with theme colors helper function.
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 * @param array                $args     Arguments for the setting and controls.
 */

function fictioneer_add_color_theme_option( $manager, $args ) {}

/**
 * Adds actions to render taxonomy submenus as needed
 *
 * @since 5.22.1
 *
 * @param string $menu  The menu HTML to be rendered.
 */

function fictioneer_add_taxonomy_submenus( $menu ) {}

/**
 * Outputs the HTML for an inline svg icon
 *
 * @since 4.0.0
 *
 * @param string $icon     Name of the icon that matches the svg.
 * @param string $classes  Optional. String of CSS classes.
 * @param string $id       Optional. An element ID.
 * @param string $inserts  Optional. Additional attributes.
 */

function fictioneer_icon( $icon, $classes = '', $id = '', $inserts = '' ) {}

/**
 * Extracts the release notes from the update message
 *
 * @since 5.19.1
 *
 * @param string $message  Update message received.
 *
 * @return string The release notes or original message if not found.
 */

function fictioneer_prepare_release_notes( $message ) {}

/**
 * Sanitizes an URL
 *
 * @since 5.19.1
 *
 * @param string      $url         The URL entered.
 * @param string|null $match       Optional. URL must start with this string.
 * @param string|null $preg_match  Optional. String for a preg_match() test.
 *
 * @return string The sanitized URL or an empty string if invalid.
 */

function fictioneer_sanitize_url( $url, $match = null, $preg_match = null ) {}
