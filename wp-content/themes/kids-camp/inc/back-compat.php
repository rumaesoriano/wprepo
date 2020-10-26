<?php
/**
 * Kids Camp back compat functionality
 *
 * Prevents Kids Camp from running on WordPress versions prior to 4.4,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 4.4.
 *
 * @package Kids_Camp
 */

/**
 * Prevent switching to Kids Camp on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since Kids Camp 1.0
 */
function kids_camp_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );

	unset( $_GET['activated'] );

	add_action( 'admin_notices', 'kids_camp_upgrade_notice' );
}
add_action( 'after_switch_theme', 'kids_camp_switch_theme' );

/**
 * Adds a message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * Kids Camp on WordPress versions prior to 4.4.
 *
 * @since Kids Camp 1.0
 *
 * @global string $wp_version WordPress version.
 */
function kids_camp_upgrade_notice() {
	/* translators: %s: current WordPress version. */
	$message = sprintf( __( 'Kids Camp requires at least WordPress version 4.4. You are running version %s. Please upgrade and try again.', 'kids-camp' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );// WPCS: XSS ok.
}

/**
 * Prevents the Customizer from being loaded on WordPress versions prior to 4.4.
 *
 * @since Kids Camp 1.0
 *
 * @global string $wp_version WordPress version.
 */
function kids_camp_customize() {
	/* translators: %s: current WordPress version. */
	$message = sprintf( __( 'Kids Camp requires at least WordPress version 4.4. You are running version %s. Please upgrade and try again.', 'kids-camp' ), $GLOBALS['wp_version'] ); // WPCS: XSS ok.

	wp_die( $message, '', array(
		'back_link' => true,
	) );
}
add_action( 'load-customize.php', 'kids_camp_customize' );

/**
 * Prevents the Theme Preview from being loaded on WordPress versions prior to 4.4.
 *
 * @since Kids Camp 1.0
 *
 * @global string $wp_version WordPress version.
 */
function kids_camp_preview() {
	if ( isset( $_GET['preview'] ) ) {
		/* translators: %s: current WordPress version. */
		wp_die( sprintf( __( 'Kids Camp requires at least WordPress version 4.4. You are running version %s. Please upgrade and try again.', 'kids-camp' ), $GLOBALS['wp_version'] ) );// WPCS: XSS ok.
	}
}
add_action( 'template_redirect', 'kids_camp_preview' );
