<?php
/**
 * Provide a public-facing view for the input file field.
 *
 * This file provides a public-facing view for the input file field.
 *
 * @package    PirateForms
 * @subpackage PirateForms/public/partials
 */

?>

<?php
if ( is_null( $wrap_classes ) ) {
	$wrap_classes = [
		'col-xs-12 form_field_wrap',
		"contact_{$name}_wrap",
		isset( $args['wrap_class'] ) ? $args['wrap_class'] : '',
	];
}

// Since the file field is going to be non-focusable, let's put the required attributes (if available) on the text field.
$text_args = [
	'id'    => $args['id'],
	'name'  => $args['name'],
	'class' => 'pirate-forms-file-upload-input',
];
if ( isset( $args['required'], $args['required_msg'] ) && $args['required'] ) {
	$text_args['required']     = $args['required'];
	$text_args['required_msg'] = $args['required_msg'];
	unset( $args['required'], $args['required_msg'] );
}
?>

<div class="<?php echo esc_attr( implode( ' ', apply_filters( "pirateform_wrap_classes_{$name}", $wrap_classes, $name, $args['type'] ) ) ); ?>">
	<div class="pirate-forms-file-upload-wrapper">
		<div class="pirate-forms-file-upload-wrapper">
			<input
					type="file"
					class="<?php echo esc_attr( apply_filters( "pirateform_field_classes_{$name}", 'form-control', $name, $args['type'] ) ); ?>" <?php echo $this->get_common( $text_args, [ 'value' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					tabindex="-1"></div>
	</div>
</div>
