<?php
/**
 * Provide a public-facing view for the CAPTCHA field.
 *
 * This file provides a public-facing view for the CAPTCHA field.
 *
 * @package    PirateForms
 * @subpackage PirateForms/public/partials
 */

?>

<?php
if ( is_null( $wrap_classes ) ) {
	$wrap_classes = [
		'col-xs-12 col-sm-6 form_field_wrap',
		"form_{$name}_wrap",
	];
}

$attributes = '';
if ( isset( $args['custom'] ) ) {
	foreach ( $args['custom'] as $key => $val ) {
		$attributes .= ' ' . $key . '="' . esc_attr( $val ) . '"';
	}
}

?>

<div class="<?php echo esc_attr( implode( ' ', apply_filters( "pirateform_wrap_classes_{$name}", $wrap_classes, $name, $args['type'] ) ) ); ?>">
	<div
			id="<?php echo esc_attr( $args['id'] ); ?>"
			class="<?php echo esc_attr( apply_filters( "pirateform_field_classes_{$name}", 'g-recaptcha pirate-forms-google-recaptcha', $name, $args['type'] ) ); ?>" <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	</div>
</div>
