<?php
/**
 * Automatic Variants for GlotPress
 *
 * Heavily inspired by the Serbian Latin solution for transliteration of Serbian Cyrillic locale from translate.wordpress.org.
 * https://meta.trac.wordpress.org/ticket/5471
 * https://meta.trac.wordpress.org/browser/sites/trunk/wordpress.org/public_html/wp-content/plugins/wporg-gp-customizations/inc/locales/class-serbian-latin.php?rev=10360
 * https://wordpress.slack.com/archives/C02RP4R9F/p1637139808076000
 *
 * @package           GP_Automatic_Variants
 * @link              https://github.com/pedro-mendonca/GP_Automatic_Variants
 * @author            Pedro Mendonça
 * @copyright         2021 Pedro Mendonça
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Automatic Variants for GlotPress
 * Plugin URI:        https://wordpress.org/plugins/gp-automatic-variants/
 * GitHub Plugin URI: https://github.com/pedro-mendonca/GP-Automatic-Variants
 * Primary Branch:    main
 * Description:       Plugin for GlotPress to manage automatically converted variants.
 * Version:           1.0.0
 * Requires at least: 5.3
 * Tested up to:      5.8
 * Requires PHP:      7.2
 * Author:            Pedro Mendonça
 * Author URI:        https://profiles.wordpress.org/pedromendonca/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gp-automatic-variants
 * Domain Path:       /languages
 */

namespace GP_Automatic_Variants;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Check if get_plugin_data() function exists.
if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Get plugin headers data.
$gp_automatic_variants_data = get_plugin_data( __FILE__, false, false );


// Set Automatic Variants for GlotPress plugin version.
if ( ! defined( 'GP_AUTOMATIC_VARIANTS_VERSION' ) ) {
	define( 'GP_AUTOMATIC_VARIANTS_VERSION', $gp_automatic_variants_data['Version'] );
}

// Set Automatic Variants for GlotPress required PHP version. Needed for PHP compatibility check for WordPress < 5.1.
if ( ! defined( 'GP_AUTOMATIC_VARIANTS_REQUIRED_PHP' ) ) {
	define( 'GP_AUTOMATIC_VARIANTS_REQUIRED_PHP', $gp_automatic_variants_data['RequiresPHP'] );
}

// Set Automatic Variants for GlotPress required GlotPress version.
if ( ! defined( 'GP_AUTOMATIC_VARIANTS_REQUIRED_GLOTPRESS' ) ) {
	define( 'GP_AUTOMATIC_VARIANTS_REQUIRED_GLOTPRESS', '3.0.0-alpha' );
}

// Set Automatic Variants for GlotPress tested GlotPress version.
if ( ! defined( 'GP_AUTOMATIC_VARIANTS_TESTED_GLOTPRESS' ) ) {
	define( 'GP_AUTOMATIC_VARIANTS_TESTED_GLOTPRESS', '3.0.0-alpha.4' );
}

// Set Automatic Variants for GlotPress plugin URL.
define( 'GP_AUTOMATIC_VARIANTS_DIR_URL', plugin_dir_url( __FILE__ ) );

// Set Automatic Variants for GlotPress plugin filesystem path.
define( 'GP_AUTOMATIC_VARIANTS_DIR_PATH', plugin_dir_path( __FILE__ ) );

// Set Automatic Variants for GlotPress file path.
define( 'GP_AUTOMATIC_VARIANTS_FILE', plugin_basename( __FILE__ ) );


/**
 * Register classes autoloader function.
 *
 * @since 1.0.0
 *
 * @param callable(string): void
 */
spl_autoload_register( __NAMESPACE__ . '\gp_automatic_variants_class_autoload' );


/**
 * Class autoloader.
 *
 * @since 1.0.0
 *
 * @param string $class_name  Classe name.
 *
 * @return void
 */
function gp_automatic_variants_class_autoload( $class_name ) {

	$project_namespace = __NAMESPACE__ . '\\';

	// Check if class is in the project namespace.
	if ( 0 !== strncmp( $project_namespace, $class_name, strlen( $project_namespace ) ) ) {
		return;
	}

	// Set class file full path.
	$class = sprintf(
		'%sincludes/class-%s.php',
		GP_AUTOMATIC_VARIANTS_DIR_PATH,
		str_replace( '_', '-', strtolower( str_replace( $project_namespace, '', $class_name ) ) )
	);

	if ( ! is_file( $class ) ) {
		return;
	}

	require_once $class;
}


// Initialize the plugin.
Automatic_Variants::init();

/**
 * TODO:
 *  - Notice to identify that the Locale is read-only and automatically managed.
 *  - Mark Locale (Locales list and Translation Set list) as read-only and automatically converted.
 *  - Set variant as read-only.
 *  - Use GlotPress stubs.
 */
