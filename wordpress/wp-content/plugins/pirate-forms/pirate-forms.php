<?php
/**
 * Plugin Pirate Forms
 *
 * @package              pirate-forms
 * @author               wpforms
 * @license              GPL-2.0-or-later
 * @wordpress-plugin
 *
 * Plugin Name:       Contact Form & SMTP Plugin for WordPress by PirateForms
 * Plugin URI:        http://themeisle.com/plugins/pirate-forms/
 * Description:       Easily creates a nice looking, simple contact form on your WP site.
 * Version:           2.6.1
 * Requires at least: 5.5
 * Requires PHP:      5.6
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Text Domain:       pirate-forms
 * Domain Path:       /languages/
 * License:           GPL v2 or later
 * Pro Slug:          pirate-forms-pro
 * Requires License:  no
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PIRATEFORMS_NAME', 'Pirate Forms' );
define( 'PIRATEFORMS_SLUG', 'pirate-forms' );
define( 'PIRATEFORMS_API_VERSION', '1' );
define( 'PIRATE_FORMS_VERSION', '2.6.1' );
define( 'PIRATEFORMS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'PIRATEFORMS_URL', plugin_dir_url( __FILE__ ) );
define( 'PIRATEFORMS_BASENAME', plugin_basename( __FILE__ ) );
define( 'PIRATEFORMS_BASEFILE', __FILE__ );
define( 'PIRATEFORMS_ROOT', trailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'PIRATEFORMS_DEBUG', false );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 *
 * @since    3.0.0
 *
 * @param string $class_name The class name to load.
 *
 * @return bool Either was loaded or not.
 */
function pirate_forms_autoload( $class_name ) {
	$namespaces = array( 'PirateForms' );
	$class1     = str_replace( '_', '-', strtolower( 'class-' . $class_name ) );

	foreach ( $namespaces as $namespace ) {
		if ( strpos( $class_name, $namespace ) === 0 ) {
			$filename = PIRATEFORMS_DIR . 'includes/' . $class1 . '.php';

			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}

			$filename = PIRATEFORMS_DIR . 'admin/' . $class1 . '.php';

			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}

			$filename = PIRATEFORMS_DIR . 'public/' . $class1 . '.php';

			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}

			$filename = PIRATEFORMS_DIR . 'public/partials/' . $class1 . '.php';

			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}

			$filename = PIRATEFORMS_DIR . 'gutenberg/' . $class1 . '.php';

			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
		}

		$filename = PIRATEFORMS_DIR . 'includes/class-pirateforms-widget.php';

		if ( is_readable( $filename ) ) {
			require_once $filename;

			return true;
		}

		$filename = PIRATEFORMS_DIR . 'includes/class-pirateforms-farewell.php';

		if ( is_readable( $filename ) ) {
			require_once $filename;

			return true;
		}
	}// End foreach().

	return false;
}

/**
 * Get the main class of PirateForms.
 *
 * @since 2.6.1
 *
 * @return PirateForms
 */
function pirate_forms() {
	static $plugin;

	if ( ! $plugin ) {
		$plugin = new PirateForms();

		$plugin->run();
	}

	return $plugin;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pirate_forms() {
	pirate_forms();
}

spl_autoload_register( 'pirate_forms_autoload' );
run_pirate_forms();
