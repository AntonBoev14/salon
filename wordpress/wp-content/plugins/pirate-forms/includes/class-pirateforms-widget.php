<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

// phpcs:disable PEAR.NamingConventions.ValidClassName.Invalid, PEAR.NamingConventions.ValidClassName.StartWithCapital
// phpcs:ignore Generic.Commenting.DocComment.MissingShort
/** @noinspection AutoloadingIssuesInspection */

/**
 * Template for new WordPress widget
 *
 * @see WP_Widget::widget()
 */
class pirate_forms_contact_widget extends WP_Widget {

	/**
	 *  Widget constructor
	 */
	public function __construct() {
		parent::__construct(
			'pirate_forms_contact_widget',
			__( 'Pirate Forms', 'pirate-forms' ),
			[
				'classname'   => __FUNCTION__,
				'description' => __( 'Pirate Forms', 'pirate-forms' ),
			]
		);
	}

	/**
	 *  Register the widget
	 */
	public static function register_widget() {
		register_widget( 'pirate_forms_contact_widget' );
	}

	/**
	 * Widget logic and display
	 *
	 * @param array $args     Args.
	 * @param array $instance Instance.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		// Pulling out all settings.
		$args     = wp_parse_args(
			$args,
			[
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '',
				'after_title'   => '',
			]
		);
		$instance = wp_parse_args(
			$instance,
			[
				'pirate_forms_widget_title'   => 'Pirate Forms',
				'pirate_forms_widget_subtext' => 'Pirate Forms',
			]
		);
		// Output all wrappers.
		echo wp_kses_post( $args['before_widget'] ) . '<div class="pirate-forms-contact-widget">';
		if ( ! empty( $instance['pirate_forms_widget_title'] ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $instance['pirate_forms_widget_title'] ) . wp_kses_post( $args['after_title'] );
		}
		if ( ! empty( $instance['pirate_forms_widget_subtext'] ) ) {
			echo wp_kses_post( wpautop( stripslashes( $instance['pirate_forms_widget_subtext'] ) ) );
		}

		$attributes = [ 'from' => 'widget' ];
		if ( isset( $instance['pirate_forms_widget_ajax'] ) && $instance['pirate_forms_widget_ajax'] ) {
			$attributes['ajax'] = 'yes';
		}

		$attributes = apply_filters( 'pirate_forms_widget_attributes', $attributes, $instance );

		$shortcode = '[pirate_forms';
		foreach ( $attributes as $k => $v ) {
			$shortcode .= " $k='$v'";
		}
		$shortcode .= ']';
		echo do_shortcode( $shortcode );
		echo '<div class="pirate_forms_clearfix"></div>';
		echo '</div>' . wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Used to update widget settings
	 *
	 * @param array $new_instance New instance.
	 * @param array $old_instance Old instance.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		// Storing widget title as inputted option or category name.
		$instance['pirate_forms_widget_title']   = apply_filters( 'widget_title', sanitize_text_field( $new_instance['pirate_forms_widget_title'] ) );
		$instance['pirate_forms_widget_subtext'] = wp_kses_post( $new_instance['pirate_forms_widget_subtext'] );
		$instance['pirate_forms_widget_ajax']    = isset( $new_instance['pirate_forms_widget_ajax'] ) && $new_instance['pirate_forms_widget_ajax'];

		return apply_filters( 'pirate_forms_widget_update', $instance, $new_instance );
	}

	/**
	 * Used to generate the widget admin view
	 *
	 * @param array $instance Instance.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$pirate_forms_widget_title   = ! empty( $instance['pirate_forms_widget_title'] ) ? $instance['pirate_forms_widget_title'] : __( 'Title', 'pirate-forms' );
		$pirate_forms_widget_subtext = ! empty( $instance['pirate_forms_widget_subtext'] ) ? $instance['pirate_forms_widget_subtext'] : __( 'Text above form', 'pirate-forms' );
		$pirate_forms_widget_ajax    = ! empty( $instance['pirate_forms_widget_ajax'] ) && (int) $instance['pirate_forms_widget_ajax'] === 1 ? 'checked' : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pirate_forms_widget_title' ) ); ?>"><?php esc_html_e( 'Title:', 'pirate-forms' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pirate_forms_widget_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pirate_forms_widget_title' ) ); ?>" type="text" value="<?php echo esc_attr( $pirate_forms_widget_title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pirate_forms_widget_subtext' ) ); ?>"><?php esc_html_e( 'Subtext:', 'pirate-forms' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pirate_forms_widget_subtext' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pirate_forms_widget_subtext' ) ); ?>"><?php echo esc_attr( $pirate_forms_widget_subtext ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pirate_forms_widget_ajax' ) ); ?>"><?php esc_html_e( 'Submit form using Ajax:', 'pirate-forms' ); ?></label>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'pirate_forms_widget_ajax' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pirate_forms_widget_ajax' ) ); ?>" value="1" <?php echo esc_attr( $pirate_forms_widget_ajax ); ?>>
		</p>
		<?php
	}
}
