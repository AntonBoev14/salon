<?php

/**
 * Builds the form
 *
 * @since    1.0.0
 */
class PirateForms_PhpFormBuilder {

	/**
	 * The array of options for the form.
	 *
	 * @access   public
	 * @var      array $pirate_forms_options The array of options for the form.
	 */
	public $pirate_forms_options;

	/**
	 * Container class for the form.
	 *
	 * @var string
	 */
	private $container_class;

	/**
	 * Form classes.
	 *
	 * @var array|null
	 */
	private $form_classes;

	/**
	 * Form attributes.
	 *
	 * @var array|null
	 */
	private $form_attributes;

	/**
	 * Form hidden fields.
	 *
	 * @var string
	 */
	private $form_hidden;

	/**
	 * Build the HTML for the form based on the input queue
	 *
	 * @param array $elements             The array of HTML elements.
	 * @param array $pirate_forms_options The array of options for the form.
	 * @param bool  $from_widget          Is the form in the widget.
	 *
	 * @return string
	 */
	public function build_form( $elements, $pirate_forms_options, $from_widget ) {
		$this->pirate_forms_options = $pirate_forms_options;
		$this->container_class      = (string) apply_filters( 'pirate_forms_container_class', $from_widget ? 'widget-yes' : 'widget-no', $this->pirate_forms_options );

		$classes   = [];
		$classes[] = $from_widget ? 'widget-on' : '';

		// we will add an id to the form so that we can scroll to it.
		$id         = wp_create_nonce( sprintf( 'pf-%s-%s', $from_widget ? 1 : 0, isset( $pirate_forms_options['id'] ) ? $pirate_forms_options['id'] : 0 ) );
		$elements[] = [
			'type'  => 'hidden',
			'id'    => 'pirate_forms_from_form',
			'value' => $id,
		];

		$html_helper   = new PirateForms_HTML();
		$hidden        = '';
		$custom_fields = '';

		foreach ( $elements as $val ) {
			if (
				array_key_exists( 'class', $val ) &&
				'form_honeypot' !== $val['id'] &&
				! in_array( $val['type'], [ 'hidden', 'div' ], true )
			) {
				$val['class'] = apply_filters( 'pirate_forms_field_class', $val['class'], $val['id'] );
			}

			if ( isset( $val['is_custom'] ) && $val['is_custom'] ) {
				// we will combine the HTML for all the custom fields and save it under one element name.
				$custom_fields .= $html_helper->add( $val, false );

				$classes[] = $val['id'] . '-on';
			} else {
				$element = $html_helper->add( $val, false );
				if (
					$val['id'] !== 'pirate-forms-maps-custom' &&
					( 'form_honeypot' === $val['id'] || in_array( $val['type'], [ 'hidden', 'div' ], true ) )
				) {
					$hidden .= $element;
				}

				if ( $val['id'] === 'pirate-forms-maps-custom' ) {
					$this->set_element( 'captcha', $element );
				}

				$this->set_element( $val['id'], $element );

				if ( 'hidden' === $val['type'] ) {
					if ( ! empty( $val['value'] ) ) {
						$classes[] = $val['id'] . '-on';
					}
				} else {
					$classes[] = $val['id'] . '-on';
				}
			}
		}

		$this->set_element( 'custom_fields', $custom_fields );

		$form_attributes = array_filter( apply_filters( 'pirate_forms_form_attributes', [ 'action' => '' ] ) );

		if ( $form_attributes ) {
			// If additional classes are provided, add them to our classes.
			if ( array_key_exists( 'class', $form_attributes ) ) {
				$form_classes = explode( ' ', $form_attributes['class'] );
				$classes      = array_merge( $classes, $form_classes );
				unset( $form_attributes['class'] );
			}

			// Don't allow overriding of method or enctype.
			if ( array_key_exists( 'method', $form_attributes ) ) {
				unset( $form_attributes['method'] );
			}

			if ( array_key_exists( 'enctype', $form_attributes ) ) {
				unset( $form_attributes['enctype'] );
			}
		}

		$this->form_classes    = apply_filters( 'pirate_forms_form_classes', $classes, $this );
		$this->form_attributes = $form_attributes;
		$this->form_hidden     = $hidden;

		return $this->load_theme();
	}

	/**
	 * Sets the element as a variable that can be used in the templates
	 *
	 * @since    1.2.6
	 */
	public function set_element( $element_name, $output ) {
		$name  = str_replace( [ 'pirate-forms-', '-' ], [ '', '_' ], $element_name );
		$final = apply_filters( "pirate_forms_before_{$name}", '', $this->pirate_forms_options );

		$final .= $output;
		$final .= apply_filters( "pirate_forms_after_{$name}", '', $this->pirate_forms_options );

		// Suppress deprecation errors in PHP 8.2+.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@$this->$name = $final;
	}

	/**
	 * Load the correct template
	 *
	 * @since    1.2.6
	 */
	private function load_theme() {
		$default = PIRATEFORMS_DIR . 'public/partials/pirateforms-form.php';
		$custom  = trailingslashit( get_template_directory() ) . 'pirate-forms/form.php';
		$file    = $default;
		if ( is_readable( $custom ) ) {
			$file = $custom;
		} elseif ( file_exists( $custom ) ) {
			do_action( 'themeisle_log_event', PIRATEFORMS_NAME, sprintf( 'cannot access theme = %s', $custom ), 'error', __FILE__, __LINE__ );
		}
		ob_start();
		include $file;

		return ob_get_clean();
	}
}
