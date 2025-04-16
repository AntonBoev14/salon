<?php

/**
 * WordPress class extended for on-the-fly plugin installations.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      2.4.5
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class PirateForms_Migration_Install_Skin extends WP_Upgrader_Skin {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.4.5
	 *
	 * @param array $args Empty array of args (we will use defaults).
	 *
	 * @noinspection PhpUnusedParameterInspection*/
	public function __construct( $args = array() ) {

		parent::__construct();
	}

	/**
	 * Set the upgrader object and store it as a property in the parent class.
	 *
	 * @since 2.4.5
	 *
	 * @param object $upgrader The upgrader object (passed by reference).
	 */
	public function set_upgrader( &$upgrader ) {

		if ( is_object( $upgrader ) ) {
			$this->upgrader =& $upgrader;
		}
	}

	/**
	 * Empty out the header of its HTML content and only check to see if it has
	 * been performed or not.
	 *
	 * @since 2.4.5
	 */
	public function header() {
	}

	/**
	 * Empty out the footer of its HTML contents.
	 *
	 * @since 2.4.5
	 */
	public function footer() {
	}

	/**
	 * Instead of outputting HTML for errors, json_encode the errors and send them
	 * back to the Ajax script for processing.
	 *
	 * @since 2.4.5
	 *
	 * @param WP_Error $errors Array of errors with the installation process.
	 */
	public function error( $errors ) {

		if ( null !== $errors ) {
			foreach ( $errors->errors as $error ) {
				if ( ! empty( $error[0] ) ) {
					wp_send_json_error( $error[0] );
					break;
				}
			}
		}
	}

	/**
	 * Empty out the feedback method to prevent outputting HTML strings as the installation
	 * is progressing.
	 *
	 * @since 2.4.5
	 *
	 * @param string $feedback The feedback string.
	 * @param mixed  ...$args  Optional text replacements.
	 */
	public function feedback( $feedback, ...$args ) {
	}
}
