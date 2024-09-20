<?php

// =============================================================================
// CONSTANTS
// =============================================================================

define( 'CHILD_VERSION', '1.0.4' );
define( 'CHILD_NAME', 'Fictioneer Minimalist' );
define( 'CHILD_RELEASE_TAG', 'v1.0.4' );

// Boolean: Base theme switch in site settings
if ( ! defined( 'FICTIONEER_THEME_SWITCH' ) ) {
  define( 'FICTIONEER_THEME_SWITCH', false );
}

// Integer: Default site width
if ( ! defined( 'FICTIONEER_DEFAULT_SITE_WIDTH' ) ) {
  define( 'FICTIONEER_DEFAULT_SITE_WIDTH', 896 );
}

// Integer: Update check timeout
if ( ! defined( 'FCNMM_UPDATE_CHECK_TIMEOUT' ) ) {
  define( 'FCNMM_UPDATE_CHECK_TIMEOUT', 43200 ); // 12 hours
}

// =============================================================================
// CHILD THEME SETUP
// =============================================================================

function fcnmm_theme_setup() {
  // Load translations (if any)
  load_child_theme_textdomain( 'fcnmm', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'fcnmm_theme_setup' );

/**
 * Enqueue child theme styles and scripts
 */

function fcnmm_enqueue_styles_and_scripts() {
  // Setup
  $cache_bust = get_option( 'fictioneer_cache_bust' ) ?: CHILD_VERSION;
  $parenthandle = 'fictioneer-application';

  // Add child theme styles
  wp_enqueue_style(
    'fcnmm-style',
    get_stylesheet_directory_uri() . '/css/fcnmm-style.css',
    [ $parenthandle ],
    $cache_bust
  );
}
add_action( 'wp_enqueue_scripts', 'fcnmm_enqueue_styles_and_scripts', 99 );

/**
 * Add or remove parent filters and actions on the frontend
 */

function fcnmm_customize_parent() {
  // Remove unused parent partials
  remove_action( 'fictioneer_site', 'fictioneer_post_content_header', 20 );
  remove_action( 'fictioneer_site', 'fictioneer_text_center_header', 20 );
  remove_action( 'fictioneer_site', 'fictioneer_inner_header', 20 );
  remove_action( 'fictioneer_site', 'fictioneer_top_header', 9 );
  remove_action( 'fictioneer_site', 'fictioneer_inner_header', 20 );
  remove_action( 'fictioneer_site', 'fictioneer_navigation_bar' );
  remove_action( 'fictioneer_main', 'fictioneer_page_background' );
}
add_action( 'init', 'fcnmm_customize_parent' );

// =============================================================================
// CHECK FOR UPDATES
// =============================================================================

/**
 * Get basic child theme info
 *
 * @since 1.0.0
 *
 * @return array Associative array with theme info.
 */

function fcnmm_get_theme_info() {
  // Setup
  $info = get_option( 'fcnmm_theme_info' ) ?: [];

  // Set up if missing
  if ( ! $info || ! is_array( $info ) ) {
    $info = array(
      'last_update_check' => current_time( 'mysql', 1 ),
      'last_update_version' => '',
      'last_update_nag' => current_time( 'mysql', 1 ),
      'last_update_notes' => '',
      'last_version_download_url' => '',
      'setup' => 0,
      'version' => CHILD_VERSION
    );

    update_option( 'fcnmm_theme_info', $info, 'yes' );
  }

  // Merge with defaults (in case of incomplete data)
  $info = array_merge(
    array(
      'last_update_check' => current_time( 'mysql', 1 ),
      'last_update_version' => '',
      'last_update_nag' => '',
      'last_update_notes' => '',
      'last_version_download_url' => '',
      'setup' => 0,
      'version' => CHILD_VERSION
    ),
    $info
  );

  // Return info
  return $info;
}

/**
 * Check Github repository for a new release of the child theme
 *
 * 1.0.0
 *
 * @return boolean True if there is a newer version, false if not.
 */

function fcnmm_check_for_updates() {
  global $pagenow;

  // Setup
  $theme_info = fcnmm_get_theme_info();
  $last_check_timestamp = strtotime( $theme_info['last_update_check'] ?? 0 );
  $remote_version = $theme_info['last_update_version'];
  $is_updates_page = $pagenow === 'update-core.php';

  // Only call API every n seconds, otherwise check database
  if (
    ( ! $is_updates_page && current_time( 'timestamp', true ) < $last_check_timestamp + FCNMM_UPDATE_CHECK_TIMEOUT ) ||
    ( $_GET['action'] ?? 0 ) === 'do-plugin-upgrade'
  ) {
    if ( ! $remote_version ) {
      return false;
    }

    return version_compare( $remote_version, CHILD_RELEASE_TAG, '>' );
  }

  // Remember this check
  $theme_info['last_update_check'] = current_time( 'mysql', 1 );

  // Request to repository
  $response = wp_remote_get(
    'https://api.github.com/repos/Tetrakern/fictioneer-minimalist/releases/latest',
    array(
      'headers' => array(
        'User-Agent' => 'FICTIONEER-MINIMALIST',
        'Accept' => 'application/vnd.github+json',
        'X-GitHub-Api-Version' => '2022-11-28'
      )
    )
  );

  // Abort if request failed or is not a 2xx success status code
  if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) >= 300 ) {
    // Remember check to avoid continuous tries on each page load
    update_option( 'fcnmm_theme_info', $theme_info );

    return false;
  }

  // Decode JSON to array
  $release = json_decode( wp_remote_retrieve_body( $response ), true );
  $release_tag = sanitize_text_field( $release['tag_name'] ?? '' );

  // Abort if request did not return expected data
  if ( ! $release_tag ) {
    return false;
  }

  // Add to theme info
  $theme_info['last_update_version'] = $release_tag;
  $theme_info['last_update_notes'] = sanitize_textarea_field( $release['body'] ?? '' );
  $theme_info['last_update_nag'] = ''; // Reset

  if ( ( $release['assets'] ?? 0 ) && function_exists( 'fictioneer_sanitize_url' ) ) {
    $theme_info['last_version_download_url'] = fictioneer_sanitize_url( $release['assets'][0]['browser_download_url'] ?? '' );
  } else {
    $theme_info['last_version_download_url'] = '';
  }

  // Update info in database
  update_option( 'fcnmm_theme_info', $theme_info );

  // Compare with currently installed version
  return version_compare( $release_tag, CHILD_RELEASE_TAG, '>' );
}

/**
 * Show notice when a newer version of the child theme is available
 *
 * @since 1.0.0
 */

function fcnmm_admin_update_notice() {
  // Guard
  if (
    ! current_user_can( 'install_themes' ) ||
    ( $_GET['action'] ?? 0 ) === 'upload-theme' ||
    ! fcnmm_check_for_updates()
  ) {
    return;
  }

  global $pagenow;

  // Setup
  $theme_info = fcnmm_get_theme_info();
  $last_update_nag = strtotime( $theme_info['last_update_nag'] ?? 0 );
  $is_updates_page = $pagenow == 'update-core.php';

  // Show only once every n seconds
  if ( ! $is_updates_page && current_time( 'timestamp', true ) < $last_update_nag + 60 ) {
    return;
  }

  // Render notice
  if ( function_exists( 'fictioneer_prepare_release_notes' ) ) {
    $notes = fictioneer_prepare_release_notes( $theme_info['last_update_notes'] ?? '' );
  } else {
    $notes = sanitize_textarea_field( $theme_info['last_update_notes'] ?? '' );
  }

  wp_admin_notice(
    sprintf(
      __( '<strong>Fictioneer Minimalist %1$s</strong> is available. Please <a href="%2$s" target="_blank">download</a> and install the latest version at your next convenience.%3$s', 'fcnmm' ),
      $theme_info['last_update_version'],
      'https://github.com/Tetrakern/fictioneer-minimalist/releases',
      $notes ? '<br><details><summary>' . __( 'Release Notes', 'fcnmm' ) . '</summary>' . $notes . '</details>' : ''
    ),
    array(
      'type' => 'warning',
      'dismissible' => true,
      'additional_classes' => ['fictioneer-update-notice']
    )
  );

  // Remember notice
  $theme_info['last_update_nag'] = current_time( 'mysql', 1 );

  // Update info in database
  update_option( 'fcnmm_theme_info', $theme_info );
}
add_action( 'admin_notices', 'fcnmm_admin_update_notice' );

// =============================================================================
// CUSTOMIZER
// =============================================================================

/**
 * Modify the theme customizer
 *
 * @since 1.0.0
 *
 * @param WP_Customize_Manager $manager  The customizer instance.
 */

function fcnmm_modify_customizers( $manager ) {
  // Site width
  $manager->add_setting(
    'site_width',
    array(
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'absint',
      'default' => 896
    )
  );

  $manager->add_control(
    'site_width',
    array(
      'type' => 'number',
      'priority' => 2,
      'section' => 'layout',
      'label' => __( 'Site Width', 'fictioneer' ),
      'description' => __( 'Maximum site width in pixels, should not be less than 832. Default 896.', 'fcnmm' ),
      'input_attrs' => array(
        'placeholder' => '896',
        'min' => 832,
        'style' => 'width: 80px'
      )
    )
  );

  // Header tagline color
  fictioneer_add_color_theme_option(
    $manager,
    array(
      'section' => 'dark_mode_colors',
      'setting' => 'dark_header_tagline_color',
      'label' => __( 'Dark Header Tagline', 'fictioneer' ),
      'priority' => 7,
      'default' => '#9097a7'
    )
  );

  // Remove controls
  $manager->remove_control( 'inset_header_image' );
  $manager->remove_control( 'header_style' );
  $manager->remove_control( 'title_text_shadow' );
  $manager->remove_control( 'header_post_content_id' );
  $manager->remove_control( 'header_height_min' );
  $manager->remove_control( 'header_height_max' );
  $manager->remove_control( 'page_style' );
  $manager->remove_control( 'page_shadow' );
}
add_action( 'customize_register', 'fcnmm_modify_customizers', 99 );

/**
 * Always sets inset_header_image to true
 *
 * @since 1.0.0
 *
 * @param string $mod  Value of the theme mod.
 *
 * @return string Updated value of the theme mod.
 */

function fcnmm_modify_inset_header_image( $mod ) {
  return true;
}
add_filter( 'theme_mod_inset_header_image', 'fcnmm_modify_inset_header_image' );

/**
 * Overrides default cover position on story pages
 *
 * @since 1.0.0
 *
 * @param string $mod  Value of the theme mod.
 *
 * @return string Updated value of the theme mod.
 */

function fcnmm_modify_story_cover_position_fix_default( $mod ) {
  if ( ! $mod || $mod === 'top-left-overflow' ) {
    return 'float-top-left';
  }

  return $mod;
}
add_filter( 'theme_mod_story_cover_position', 'fcnmm_modify_story_cover_position_fix_default' );

/**
 * Changes default cover position in the Customizer
 *
 * @since 1.0.0
 *
 * @param string $default  Default value
 *
 * @return string Changed default value.
 */

function fcnmm_change_story_cover_position_default( $default ) {
  return 'float-top-left';
}
add_filter( 'fictioneer_filter_customizer_story_cover_position_default', 'fcnmm_change_story_cover_position_default' );

/**
 * Removes top-left-overflow story cover position option
 *
 * @since 1.0.0
 *
 * @param array $options  Customizer options for the story cover position.
 *
 * @return array Updated options.
 */

function fcnmm_modify_story_cover_position_options( $options ) {
  unset( $options['top-left-overflow'] );
  $options['float-top-left'] = _x( 'Floating Top-Left (Default)', 'Customizer story cover position option.', 'fcnmm' );

  return $options;
}
add_filter( 'fictioneer_filter_customizer_story_cover_position', 'fcnmm_modify_story_cover_position_options' );

// =============================================================================
// SITE HEADER & NAVIGATION
// =============================================================================

/**
 * Outputs the HTML for the child theme header
 *
 * @since 1.0.0
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fcnmm_header( $args ) {
  // Return early if...
  if ( $args['header_args']['blank'] ?? 0 ) {
    return;
  }

  // Setup
  $home_url = home_url();
  $title = get_bloginfo( 'name' );
  $tagline = get_bloginfo( 'description' );
  $title_tag = is_front_page() ? 'h1' : 'h3';
  $sub_tag = is_front_page() ? 'h2' : 'h4';
  $classes = [];

  // CSS classes
  if ( ! display_header_text() || empty( $title ) ) {
    $classes[] = '_no-title';
  }

  if ( empty( $tagline ) ) {
    $classes[] = '_no-tagline';
  }

  if ( ! has_custom_logo() ) {
    $classes[] = '_no-logo';
  }

  // Render Elementor or theme header
  if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
    // Start HTML ---> ?>
    <header class="fcnmm-header <?php echo implode( ' ', $classes ); ?>">
      <div class="fcnmm-header__content">
        <div class="fcnmm-header__identity"><?php
          if ( has_custom_logo() ) {
            echo get_custom_logo();
          }

          if ( display_header_text() ) {
            if ( $title ) {
              echo "<{$title_tag} class='fcnmm-header__title'><a href='{$home_url}'>{$title}</a></{$title_tag}>";
            }

            if ( $tagline ) {
              echo "<{$sub_tag} class='fcnmm-header__tagline'>{$tagline}</{$sub_tag}>";
            }
          }
        ?></div>
        <div class="fcnmm-header__spacer"></div>
        <div class="fcnmm-header__icon-menu"><?php
          get_template_part( 'partials/_icon-menu', null, array( 'location' => 'in-navigation' ) );
        ?></div>
      </div>
    </header>
    <?php // <--- End HTML
  }
}
add_action( 'fictioneer_site', 'fcnmm_header', 20 );

/**
 * Outputs the HTML for the child theme navigation bar
 *
 * @since 1.0.0
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fcnmm_navigation_bar( $args ) {
  // Return early if...
  if ( $args['header_args']['blank'] ?? 0 ) {
    return;
  }

  // Setup
  $mobile_nav_style = get_theme_mod( 'mobile_nav_style', 'overflow' );
  $classes = [];


if ( $mobile_nav_style === 'collapse' ) {
  $classes[] = '_collapse-on-mobile';
}

  // Start HTML ---> ?>
  <div id="full-navigation" class="main-navigation <?php echo implode( ' ', $classes ); ?>">

    <div id="nav-observer-sticky" class="observer nav-observer"></div>

    <div class="main-navigation__background"></div>

    <?php do_action( 'fictioneer_navigation_top', $args ); ?>

    <div class="main-navigation__wrapper">

      <?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'nav_bar' ) ) : ?>

        <?php do_action( 'fictioneer_navigation_wrapper_start', $args ); ?>

        <nav class="main-navigation__left" aria-label="<?php echo esc_attr__( 'Main Navigation', 'fictioneer' ); ?>">

          <label for="mobile-menu-toggle" class="mobile-menu-button follows-alert-number">
            <?php
              fictioneer_icon( 'fa-bars', 'off' );
              fictioneer_icon( 'fa-xmark', 'on' );
            ?>
            <span class="mobile-menu-button__label"><?php _ex( 'Menu' , 'Mobile menu label', 'fictioneer' ); ?></span>
          </label>

          <?php
            if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'nav_menu' ) ) {
              if ( has_nav_menu( 'nav_menu' ) ) {
                $menu = null;

                if ( FICTIONEER_ENABLE_MENU_TRANSIENTS ) {
                  $menu = get_transient( 'fictioneer_fcnmm_nav_menu_html' );
                }

                if ( empty( $menu ) ) {
                  $menu = wp_nav_menu(
                    array(
                      'theme_location' => 'nav_menu',
                      'menu_class' => 'main-navigation__list',
                      'container' => '',
                      'menu_id' => 'menu-navigation',
                      'items_wrap' => '<ul id="%1$s" data-menu-id="main" class="%2$s">%3$s</ul>',
                      'echo' => false
                    )
                  );

                  if ( $menu !== false ) {
                    $menu = str_replace( ['current_page_item', 'current-menu-item', 'aria-current="page"'], '', $menu );
                  }

                  if ( FICTIONEER_ENABLE_MENU_TRANSIENTS ) {
                    set_transient( 'fictioneer_fcnmm_nav_menu_html', $menu );
                  }
                }

                fictioneer_add_taxonomy_submenus( $menu );

                echo $menu;
              }
            }
          ?>
        </nav>

        <?php do_action( 'fictioneer_navigation_wrapper_end', $args ); ?>

      <?php endif; ?>

    </div>

    <?php do_action( 'fictioneer_navigation_bottom', $args ); ?>

  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_site', 'fcnmm_navigation_bar', 21 );

/**
 * Purges nav menu Transients on menu updates
 *
 * @since 1.0.0
 */

function fcnmm_purge_nav_menu_transients() {
  delete_transient( 'fictioneer_fcnmm_nav_menu_html' );
}
add_action( 'wp_update_nav_menu', 'fcnmm_purge_nav_menu_transients' );

/**
 * Outputs the HTML for the header image (if any)
 *
 * @since 1.0.0
 *
 * @param int|null       $args['post_id']           Optional. Current post ID.
 * @param int|null       $args['story_id']          Optional. Current story ID (if chapter).
 * @param string|boolean $args['header_image_url']  URL of the filtered header image or false.
 * @param array          $args['header_args']       Arguments passed to the header.php partial.
 */

function fcnmm_header_image( $args ) {
  // Abort if...
  if ( ! ( $args['header_image_url'] ?? 0 ) || ( $args['header_args']['no_header'] ?? 0 ) ) {
    return;
  }

  // Setup
  $header_image_style = get_theme_mod( 'header_image_style', 'default' );
  $extra_classes = [ "_style-{$header_image_style}" ];

  if ( absint( get_theme_mod( 'header_image_fading_start', 0 ) ) > 0 ) {
    $extra_classes[] = '_fading-bottom';
  }

  if ( get_theme_mod( 'header_image_shadow', true ) ) {
    $extra_classes[] = '_shadow';
  }

  // Start HTML ---> ?>
  <div class="header-background _fcnmm hide-on-fullscreen polygon <?php echo implode( ' ', $extra_classes ); ?>">
    <div class="header-background__wrapper _fcnmm polygon">
      <img src="<?php echo $args['header_image_url']; ?>" alt="Header Image" class="header-background__image _fcnmm">
    </div>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_site', 'fcnmm_header_image', 25 );
