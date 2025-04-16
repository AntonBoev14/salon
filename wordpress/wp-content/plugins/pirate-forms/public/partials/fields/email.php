<?php
/**
 * Provide a public-facing view for the input text field.
 *
 * This file provides a public-facing view for the input text field.
 *
 * @package    PirateForms
 * @subpackage PirateForms/public/partials
 */

?>

<?php
if ( is_null( $wrap_classes ) ) {
	$wrap_classes = [
		'col-xs-12',
		'pirate_forms_three_inputs form_field_wrap',
		"contact_{$name}_wrap",
		isset( $args['wrap_class'] ) ? $args['wrap_class'] : '',
	];
}
?>

<div class="<?php echo esc_attr( implode( ' ', apply_filters( "pirateform_wrap_classes_{$name}", $wrap_classes, $name, $args['type'] ) ) ); ?>">
	<?php echo esc_html( $label ); ?>
	<input type="email" class="<?php echo esc_attr( apply_filters( "pirateform_field_classes_{$name}", 'form-control', $name, $args['type'] ) ); ?>" <?php echo $this->get_common( $args, [ 'value' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
</div>
