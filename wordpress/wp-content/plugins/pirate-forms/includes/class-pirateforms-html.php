<?php

/**
 * HTML elements helper
 *
 * @since    1.2.6
 */
class PirateForms_HTML {

	/**
	 * Add the HTML element - the single entry point for this class
	 *
	 * @since 1.2.6
	 */
	public function add( $args, $do_echo = true ) {
		if ( isset( $args['front_end'] ) && $args['front_end'] ) {
			$html = $this->front_end( $args );

			if ( ! $do_echo ) {
				return $html;
			}

			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			return '';
		}

		$type = $args['type'];

		if ( method_exists( $this, $type ) ) {
			if ( isset( $args['id'] ) && ! isset( $args['name'] ) ) {
				$args['name'] = $args['id'];
			}

			if ( isset( $args['class'] ) && is_array( $args['class'] ) ) {
				$args['class'] = implode( ' ', $args['class'] );
			}

			$html = $this->$type( $args );
		} else {
			$html = sprintf( 'Field type "%s" not defined. Have you upgraded to the latest version of %s?', $type, PIRATEFORMS_NAME );

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $html );
		}

		if ( ! $do_echo ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		return '';
	}

	/**
	 * Add the wrapper around the HTML element
	 *
	 * @since    1.2.6
	 */
	private function get_wrap( $args, $inside ) {

		$html = '';
		if ( isset( $args['wrap'] ) ) {
			$html .= '<' . $args['wrap']['type'];
			if ( isset( $args['wrap']['class'] ) ) {
				$html .= ' class="' . esc_attr( $args['wrap']['class'] ) . '"';
			}
			if ( isset( $args['wrap']['style'] ) ) {
				$html .= ' style="' . $args['wrap']['style'] . '"';
			}
			$html .= '>';
		}
		$html .= $inside;

		if ( isset( $args['wrap'] ) ) {
			$html .= '</' . $args['wrap']['type'] . '>';
		}

		return $html;
	}

	/**
	 * Add the label etc. for the HTML element
	 *
	 * @since    1.2.6
	 */
	private function get_label( $args ) {
		$html = '';
		if ( isset( $args['label'] ) ) {
			$html .= '<label for="' . esc_attr( $args['id'] ) . '"';

			if ( isset( $args['label']['class'] ) ) {
				$html .= 'class="' . esc_attr( $args['label']['class'] ) . '"';
			}
			$html .= '>';
			if ( isset( $args['label']['value'] ) ) {
				$html .= esc_html( $args['label']['value'] );
			}
			if ( isset( $args['label']['html'] ) ) {
				$span = $args['label']['html'];
				if ( isset( $args['label']['desc']['value'] ) && strpos( $span, 'dashicons-editor-help' ) !== false ) {
					$class = isset( $args['label']['desc']['class'] ) ? $args['label']['desc']['class'] : '';
					$span  = str_replace( '></', '><div style="display: none" class="' . $class . '">' . $args['label']['desc']['value'] . '</div></', $span );
					unset( $args['label']['desc'], $args['label']['desc']['value'] );
				}
				$html .= $span;
			}
			if ( isset( $args['label']['desc'] ) ) {
				$html .= '<div';
				if ( isset( $args['label']['desc']['class'] ) ) {
					$html .= ' class="' . esc_attr( $args['label']['desc']['class'] ) . '"';
				}
				$html .= '>' . $args['label']['desc']['value'] . '</div>';
			}
			$html .= '</label>';
		}

		return $html;
	}

	/**
	 * Add the common attributes for the HTML element
	 *
	 * @since    1.2.6
	 */
	private function get_common( $args, $additional = [] ) {
		$html = 'id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '" class="' . ( isset( $args['class'] ) ? esc_attr( $args['class'] ) : '' ) . '" placeholder="' . ( isset( $args['placeholder'] ) ? esc_attr( $args['placeholder'] ) : '' ) . '" ' . ( isset( $args['required'] ) && $args['required'] ? 'required' : '' );

		if ( isset( $args['required'], $args['required_msg'] ) && $args['required'] ) {
			$html .= ' oninvalid="this.setCustomValidity(\'' . esc_attr( $args['required_msg'] ) . '\')" onchange="this.setCustomValidity(\'\')"';
		}

		if ( in_array( 'value', $additional, true ) ) {
			$html .= ' value="' . ( isset( $args['value'] ) ? esc_attr( $args['value'] ) : '' ) . '"';
		}

		if ( isset( $args['disabled'] ) && $args['disabled'] ) {
			$html .= ' disabled';
		}

		if ( ! empty( $args['title'] ) ) {
			$html .= ' title="' . esc_attr( $args['title'] ) . '"';
		}

		return $html;
	}

	/**
	 * The H3 element
	 *
	 * @since    1.2.6
	 */
	private function h3( $args ) {
		$html = '<h3';

		if ( isset( $args['class'] ) ) {
			$html .= ' class="' . esc_attr( $args['class'] ) . '"';
		}
		$html .= '>' . esc_html( $args['value'] ) . '</h3>';
		if ( isset( $args['hr'] ) && $args['hr'] ) {
			$html .= '<hr />';
		}

		return $html;
	}

	/**
	 * The DIV element
	 *
	 * @since    1.2.6
	 */
	private function div( $args ) {
		$html = '<div';

		if ( isset( $args['id'] ) ) {
			$html .= ' id="' . esc_attr( $args['id'] ) . '"';
		}

		if ( isset( $args['class'] ) ) {
			$html .= ' class="' . esc_attr( $args['class'] ) . '"';
		}

		if ( isset( $args['custom'] ) ) {
			foreach ( $args['custom'] as $key => $val ) {
				$html .= ' ' . $key . '="' . esc_attr( $val ) . '"';
			}
		}

		$html .= '>';

		if ( isset( $args['value'] ) ) {
			$html .= esc_html( $args['value'] );
		}

		$html .= '</div>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The input type="file" element
	 *
	 * @since    1.2.6
	 */
	private function file( $args ) {
		$class = 'pirate-forms-file-upload-hidden';
		if ( isset( $args['class'] ) ) {
			$class .= ' ' . $args['class'];
		}
		$args['class'] = $class;

		// label for the upload button.
		$label = isset( $args['label']['value'] ) ? $args['label']['value'] : ( isset( $args['placeholder'] ) ? $args['placeholder'] : '' );
		if ( empty( $label ) ) {
			$label = __( 'Upload file', 'pirate-forms' );
		}
		$args['label']['value'] = $label;

		// since the file field is going to be non-focusable, let's put the required attributes (if available) on the text field.
		if ( isset( $args['required'], $args['required_msg'] ) && $args['required'] ) {
			unset( $args['required'], $args['required_msg'] );
		}

		$html = '<div class="pirate-forms-file-upload-wrapper"><input type="file" ' . $this->get_common( $args, [ 'value' ] ) . ' tabindex="-1"></div>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The input type="email" element
	 *
	 * @since    1.2.6
	 */
	private function email( $args ) {
		$html = $this->get_label( $args );

		$html .= '<input type="email" ' . $this->get_common( $args, [ 'value' ] ) . '>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The input type="text" element
	 *
	 * @since    1.2.6
	 */
	private function text( $args ) {
		$html = $this->get_label( $args );

		$html .= '<input type="text" ' . $this->get_common( $args, [ 'value' ] ) . '>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The input type="number" element
	 *
	 * @since    1.2.6
	 */
	private function number( $args ) {
		$html = $this->get_label( $args );

		$html .= '<input type="number" ' . $this->get_common( $args, [ 'value' ] ) . ' min=0>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The input type="tel" element
	 *
	 * @since    1.2.6
	 */
	private function tel( $args ) {
		$html = $this->get_label( $args );

		$html .= '<input type="tel" ' . $this->get_common( $args, [ 'value' ] ) . '>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The input type="hidden" element
	 *
	 * @since    1.2.6
	 */
	private function hidden( $args ) {
		return '<input type="hidden" ' . $this->get_common( $args, [ 'value' ] ) . '>';
	}

	/**
	 * The input type="password" element
	 *
	 * @since    1.2.6
	 */
	private function password( $args ) {
		$html = $this->get_label( $args );

		$html .= '<input type="password" ' . $this->get_common( $args, [ 'value' ] ) . '>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The textarea element
	 *
	 * @since    1.2.6
	 */
	private function textarea( $args ) {
		$html = $this->get_label( $args );

		$rows = isset( $args['rows'] ) ? $args['rows'] : 5;
		$cols = isset( $args['cols'] ) ? $args['cols'] : 30;

		$html .= '<textarea rows=' . $rows . ' cols=' . $cols . ' ' . $this->get_common( $args ) . '>' . ( isset( $args['value'] ) ? esc_attr( $args['value'] ) : '' ) . '</textarea>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The dropdown element
	 *
	 * @since    1.2.6
	 */
	private function select( $args ) {
		$html = $this->get_label( $args );

		$extra = ' ';
		if ( isset( $args['sub_type'] ) ) {
			$extra .= $args['sub_type'] . ' ';
		}
		if ( isset( $args['required'] ) && $args['required'] ) {
			$extra .= 'required ';
		}
		if ( isset( $args['required'], $args['required_msg'] ) && $args['required'] ) {
			$extra .= 'oninvalid="this.setCustomValidity(\'' . esc_attr( $args['required_msg'] ) . '\')" onchange="this.setCustomValidity(\'\')" ';
		}

		$html .= '<select id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '" class="' . ( isset( $args['class'] ) ? esc_attr( $args['class'] ) : '' ) . '" ' . $extra . '>';
		if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			foreach ( $args['options'] as $key => $val ) {
				// phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
				$extra = isset( $args['value'] ) && $key == $args['value'] ? 'selected' : '';

				$html .= '<option value="' . esc_attr( $key ) . '" ' . $extra . '>' . esc_html( $val ) . '</option>';
			}
		}
		$html .= '</select>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The input type="radio" element
	 *
	 * @since    1.2.6
	 */
	private function radio( $args ) {
		$html = $this->get_label( $args );

		if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			$index_radio = 0;
			foreach ( $args['options'] as $key => $val ) {
				// phpcs:disable Universal.Operators.StrictComparisons.LooseEqual
				$extra = $key == $args['value'] ? 'checked' : '';
				if ( $index_radio++ == 0 ) {
					$extra = 'checked';
				}
				// phpcs:enable Universal.Operators.StrictComparisons.LooseEqual
				$html .= '<input type="radio" value="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] . $key ) . '" name="' . esc_attr( $args['id'] ) . '" class="' . ( isset( $args['class'] ) ? esc_attr( $args['class'] ) : '' ) . '" ' . $extra . '>' . $val;
			}
		}

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The input type="checkbox" element
	 *
	 * @since    1.2.6
	 */
	private function checkbox( $args ) {
		$html = $this->get_label( $args );

		if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			foreach ( $args['options'] as $key => $val ) {
				// phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
				$extra = isset( $args['value'] ) && $key == $args['value'] ? 'checked' : '';
				// DO NOT escape $val because it can also have HTML markup.
				$html .= '<input type="checkbox" ' . $extra . ' ' . $this->get_common( $args ) . ' value="' . esc_attr( $key ) . '"><label for="' . esc_attr( $args['id'] ) . '" class="pf-checkbox-label"><span>' . $val . '</span></label>';
			}
		}

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The input type="submit" element
	 *
	 * @since    1.2.6
	 */
	private function submit( $args ) {
		$html = '<input type="submit" ' . $this->get_common( $args, [ 'value' ] ) . '>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The button element
	 *
	 * @since    1.2.6
	 */
	private function button( $args ) {
		$html = '<button type="submit" ' . $this->get_common( $args ) . '>' . ( isset( $args['value'] ) ? $args['value'] : '' ) . '</button>';

		return $this->get_wrap( $args, $html );
	}

	/**
	 * The WYSIWYG element.
	 */
	private function wysiwyg( $args ) {
		$html    = $this->get_label( $args );
		$content = ! empty( $args['value'] ) ? $args['value'] : ( isset( $args['default'] ) ? $args['default'] : '' );
		ob_start();
		wp_editor( $content, $args['id'], $args['wysiwyg'] );
		$html .= ob_get_clean();

		return $this->get_wrap( $args, $html );
	}

	/**
	 * Elements on the front end.
	 */
	private function front_end( $args ) {
		global $wp_filesystem;

		$type = $args['type'];

		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		$plugin_path = str_replace( ABSPATH, $wp_filesystem->abspath(), PIRATEFORMS_DIR );
		$template    = trailingslashit( $plugin_path ) . "/public/partials/fields/{$type}.php";

		if ( ! $wp_filesystem->is_readable( $template ) ) {
			return '';
		}

		if ( isset( $args['id'] ) && ! isset( $args['name'] ) ) {
			$args['name'] = $args['id'];
		}

		$name = str_replace( [ 'pirate-forms-contact-', 'pirate-forms-' ], '', $args['name'] );

		$args = apply_filters( "pirate_forms_front_end_{$type}_args", $args, $name );

		// Themes might have overridden some attributes, so we need to extract them in a backward-compatible way.
		$wrap_classes = null;
		if ( isset( $args['wrap']['class'] ) ) {
			$wrap_classes = [ $args['wrap']['class'] ];
		}
		$label = null;
		if ( isset( $args['label'] ) ) {
			$label = $this->get_label( $args );
		}

		ob_start();
		include $template;

		return ob_get_clean();
	}

	/**
	 * The label element.
	 */
	private function label( $args ) {
		$html = $args['placeholder'];

		return $this->get_wrap( $args, $html );
	}
}
