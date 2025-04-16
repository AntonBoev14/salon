<?php
/**
 * Provide a public-facing view for the button field.
 *
 * This file provides a public-facing view for the button field.
 *
 * @package    PirateForms
 * @subpackage PirateForms/public/partials
 */

?>

<?php

$name = 'submit';

if ( is_null( $wrap_classes ) ) {
	$wrap_classes = [
		'col-xs-12 col-sm-6 form_field_wrap',
		"contact_{$name}_wrap",
	];
}
?>

<div class="<?php echo esc_attr( implode( ' ', apply_filters( "pirateform_wrap_classes_{$name}", $wrap_classes, $name, $args['type'] ) ) ); ?>">
	<button type="submit" class="<?php echo esc_attr( apply_filters( "pirateform_field_classes_{$name}", $args['class'], $name, $args['type'] ) ); ?>" <?php echo $this->get_common( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo isset( $args['value'] ) ? esc_html( $args['value'] ) : ''; ?></button>
</div>
