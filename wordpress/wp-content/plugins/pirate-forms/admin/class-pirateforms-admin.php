<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    PirateForms
 * @subpackage PirateForms/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PirateForms
 * @subpackage PirateForms/admin
 */
class PirateForms_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles_and_scripts() {
		$current_screen = get_current_screen();

		if ( ! $current_screen || empty( $current_screen->id ) ) {
			return;
		}

		if (
			in_array(
				$current_screen->id,
				[ 'edit-pf_contact', 'edit-pf_form', 'pf_form', 'toplevel_page_pirateforms-admin' ],
				true
			)
		) {
			wp_enqueue_style( 'pirateforms_admin_styles', PIRATEFORMS_URL . 'admin/css/wp-admin.css', [], $this->version );
			wp_enqueue_script(
				'pirateforms_scripts_admin',
				PIRATEFORMS_URL . 'admin/js/scripts-admin.js',
				[ 'jquery', 'jquery-ui-tooltip' ],
				$this->version,
				true
			);
			wp_localize_script(
				'pirateforms_scripts_admin',
				'cwp_top_ajaxload',
				[
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( PIRATEFORMS_SLUG ),
					'slug'    => PIRATEFORMS_SLUG,
					'i10n'    => [
						'recaptcha' => __( 'Please specify the Site Key and Secret Key.', 'pirate-forms' ),
					],
				]
			);
		}

		if ( $current_screen->id === 'dashboard' ) {
			wp_enqueue_style( 'pirateforms_farewell_styles', PIRATEFORMS_URL . 'admin/css/farewell.css', [], $this->version );
		}

		if ( $current_screen->id === 'pirate-forms_page_pirateforms-admin-migration' ) {
			wp_enqueue_style( 'pirateforms_farewell_migration', PIRATEFORMS_URL . 'admin/css/migration.css', [], $this->version );
			wp_enqueue_script( 'pirateforms_farewell_migration', PIRATEFORMS_URL . 'admin/js/migration.js', [ 'jquery' ], $this->version, true );
		}
	}

	/**
	 * Add the settings link in the plugin page
	 *
	 * @since 1.0.0
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=pirateforms-admin">' . __( 'Settings', 'pirate-forms' ) . '</a>';
		if ( function_exists( 'array_unshift' ) ) :
			array_unshift( $links, $settings_link );
		else :
			$links[] = $settings_link;
		endif;

		return $links;
	}

	/**
	 * Add page to the dashboard menu.
	 *
	 * @since 1.0.0
	 */
	public function add_to_admin() {
		add_menu_page(
			PIRATEFORMS_NAME,
			PIRATEFORMS_NAME,
			'manage_options',
			'pirateforms-admin',
			[
				$this,
				'settings',
			],
			'dashicons-feedback'
		);

		add_submenu_page(
			'pirateforms-admin',
			PIRATEFORMS_NAME,
			esc_html__( 'Settings', 'pirate-forms' ),
			'manage_options',
			'pirateforms-admin',
			[
				$this,
				'settings',
			]
		);

		add_submenu_page(
			'pirateforms-admin',
			PIRATEFORMS_NAME,
			'<span style="color:#f18500">' . esc_html__( 'Migration', 'pirate-forms' ) . '</span>',
			'manage_options',
			'pirateforms-admin-migration',
			[
				$this,
				'migration',
			]
		);
	}

	/**
	 *  Admin area setting page for the plugin
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnusedLocalVariableInspection
	 */
	public function settings() {
		// $current_user is used in the template.
		global $current_user;

		$pirate_forms_options = PirateForms_Util::get_option();
		$plugin_options       = $this->get_plugin_options();
		include_once PIRATEFORMS_DIR . 'admin/partials/settings.php';
	}

	/**
	 * Display a migration page content.
	 *
	 * @since 2.4.5
	 */
	public function migration() {

		$lite = 'wpforms-lite/wpforms.php';
		$pro  = 'wpforms/wpforms.php';

		$class_import = 'disabled';

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();

		// If exists and active.
		if (
			is_plugin_active( $lite ) ||
			is_plugin_active( $pro )
		) {
			$class        = 'disabled';
			$label        = esc_html__( 'WPForms is Ready', 'pirate-forms' );
			$class_import = '';
		} elseif (
			array_key_exists( $lite, $all_plugins ) ||
			array_key_exists( $pro, $all_plugins )
		) {
			// Plugin exists but not active.
			$class = 'js-pf-migration-activate';
			$label = esc_html__( 'Activate WPForms', 'pirate-forms' );
		} else {
			// Plugin doesn't even exist.
			$class = 'js-pf-migration-install';
			$label = esc_html__( 'Install WPForms', 'pirate-forms' );
		}

		include_once PIRATEFORMS_DIR . 'admin/partials/migration.php';
	}

	/**
	 *  Get any options that might be configured through the theme.
	 */
	public function get_theme_options() {
		$recaptcha_show = '';
		$button_label   = __( 'Send Message', 'pirate-forms' );
		$email          = get_bloginfo( 'admin_email' );

		$theme = strtolower( wp_get_theme()->__get( 'name' ) );

		// Default values from Zerif Lite.
		if ( strpos( $theme, 'zerif' ) === 0 ) {
			$zerif_contactus_recaptcha_show = get_theme_mod( 'zerif_contactus_recaptcha_show' );
			if ( ! isset( $zerif_contactus_recaptcha_show ) || (int) $zerif_contactus_recaptcha_show !== 1 ) {
				$recaptcha_show = 'custom';
			}

			$zerif_contactus_button_label = get_theme_mod( 'zerif_contactus_button_label', __( 'Send Message', 'pirate-forms' ) );
			if ( ! empty( $zerif_contactus_button_label ) ) {
				$button_label = $zerif_contactus_button_label;
			}

			$zerif_contactus_email = get_theme_mod( 'zerif_contactus_email' );
			$zerif_email           = get_theme_mod( 'zerif_email' );
			if ( ! empty( $zerif_contactus_email ) ) {
				$email = $zerif_contactus_email;
			} elseif ( ! empty( $zerif_email ) ) {
				$email = $zerif_email;
			}
		}

		return [
			$recaptcha_show,
			$button_label,
			$email,
		];
	}

	/**
	 *
	 * OPTIONS
	 *
	 * @since 1.0.0
	 * name; id; desc; type; default; options
	 */
	public function get_plugin_options() {
		list(
			$pirate_forms_contactus_recaptcha_show,
			$pirate_forms_contactus_button_label,
			$pirate_forms_contactus_email
			) = $this->get_theme_options();

		// Check if akismet is installed.
		$akismet_status = false;
		if ( is_plugin_active( 'akismet/akismet.php' ) ) {
			$akismet_key = get_option( 'wordpress_api_key' );
			if ( ! empty( $akismet_key ) ) {
				$akismet_status = true;
			}
		}

		$akismet_msg = '';
		if ( ! $akismet_status ) {
			$akismet_msg = __( 'To use this option, please ensure Akismet is activated with a valid key.', 'pirate-forms' );
		}

		// The key(s) will be added to the div as class names to enable tooltip popup add 'pirate_tooltip'.
		return apply_filters(
			'pirate_forms_admin_controls',
			[
				'pirate_options pirate_tooltip' => [
					'heading'  => __( 'Form processing options', 'pirate-forms' ),
					'controls' => apply_filters(
						'pirate_forms_admin_controls_for_options',
						[
							[
								'id'      => 'pirateformsopt_email',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Contact notification email address', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => '<strong>' . __( "Insert [email] to use the contact form submitter's email.", 'pirate-forms' ) . '</strong><br>' . __( "The notification email will be sent from this address both to the recipients below and the contact form submitter (if this is activated below in email confirmation, in which case the domain for this email address should match your site's domain).", 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'default' => PirateForms_Util::get_from_email(),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_email' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'class'   => 'widefat',
							],
							[
								'id'      => 'pirateformsopt_email_recipients',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Contact submission recipients', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => __( 'Email address(es) to receive contact submission notifications. You can separate multiple emails with a comma.', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'default' => $pirate_forms_contactus_email,
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_email_recipients' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'class'   => 'widefat',
							],
							[
								'id'      => 'pirateformsopt_store',
								'type'    => 'checkbox',
								'label'   => [
									'value' => __( 'Store submissions in the database', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => sprintf( '%s<br>%s', __( 'Should the submissions be stored in the admin area? If chosen, contact form submissions will be saved under "All Entries" on the left (appears after this option is activated).', 'pirate-forms' ), __( 'According to GDPR we recommend you to ask for consent in order to store user data', 'pirate-forms' ) ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'default' => 'no',
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_store' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [ 'yes' => __( 'Yes', 'pirate-forms' ) ],
								'title'   => __( 'According to GDPR, we recommend you to ask for consent in order to store user data.', 'pirate-forms' ),
							],
							[
								'id'      => 'pirateformsopt_store_ip',
								'type'    => 'checkbox',
								'label'   => [
									'value' => __( 'Track and store IP of user', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => sprintf( '%s<br>%s<br>%s', __( 'Should the IP of the customer be tracked, stored and displayed in the email content?', 'pirate-forms' ), __( 'According to GDPR we recommend you to ask for consent in order to store user data', 'pirate-forms' ), __( 'If this option is not selected, we may not be able to determine whether this is a spam message.', 'pirate-forms' ) ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'default' => 'no',
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_store_ip' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [ 'yes' => __( 'Yes', 'pirate-forms' ) ],
								'title'   => __( 'According to GDPR, we recommend you to ask for consent in order to store user data.', 'pirate-forms' ),
							],
							[
								'id'      => 'pirateformsopt_nonce',
								'type'    => 'checkbox',
								'label'   => [
									'value' => __( 'Add a nonce to the contact form', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => __( 'Should the form use a WordPress nonce? This helps reduce spam by ensuring that the form submittor is on the site when submitting the form rather than submitting remotely. This could, however, cause problems with sites using a page caching plugin. Turn this off if you are getting complaints about forms not being able to be submitted with an error of "Nonce failed!"', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'default' => 'yes',
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_nonce' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [ 'yes' => __( 'Yes', 'pirate-forms' ) ],
							],
							[
								'id'    => 'pirateformsopt_confirm_email',
								'type'  => 'textarea',
								'label' => [
									'value' => __( 'Send email confirmation to form submitter', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => __( 'Adding text here will send an email to the form submitter. The email uses the "Successful form submission text" field from the "Alert Messages" tab as the subject line. Plain text only here, no HTML.', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'value' => stripslashes( PirateForms_Util::get_option( 'pirateformsopt_confirm_email' ) ),
								'wrap'  => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'cols'  => 70,
								'rows'  => 5,
							],
							[
								'id'      => 'pirateformsopt_copy_email',
								'type'    => 'checkbox',
								'label'   => [
									'value' => __( 'Add copy of mail to confirmation email', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => __( 'Should a copy of the email be appended to the confirmation email? Only the fields that are being displayed will be sent to the sender. Please note that this will only be appended if confirmation email text is provided above.', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'default' => '',
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_copy_email' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [ 'yes' => __( 'Yes', 'pirate-forms' ) ],
							],
							[
								'id'      => 'pirateformsopt_save_attachment',
								'type'    => 'checkbox',
								'label'   => [
									'value' => __( 'Save Attachment?', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => __( 'Enabling this option will save the attachment(s) otherwise attachments can only be found in the email that is received.', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'default' => '',
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_save_attachment' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [ 'yes' => __( 'Yes', 'pirate-forms' ) ],
							],
							[
								'id'      => 'pirateformsopt_thank_you_url',
								'type'    => 'select',
								'label'   => [
									'value' => __( 'Success Page', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => __( 'Select the page that displays after a successful form submission. The page will be displayed without pausing on the email form, so please be sure to configure a relevant thank you message in this page.', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_thank_you_url' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => PirateForms_Util::get_thank_you_pages(),
							],
							[
								'id'       => 'pirateformsopt_akismet',
								'type'     => 'checkbox',
								'label'    => [
									'value' => __( 'Integrate with Akismet?', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => sprintf(
											/* translators: %s: Akismet message */
											__( 'Checking this option will verify the content of the message with Akismet to check if it\'s spam. If it is determined to be spam, the message will be blocked. %s', 'pirate-forms' ),
											$akismet_msg
										),
										'class' => 'pirate_forms_option_description',
									],
								],
								'value'    => PirateForms_Util::get_option( 'pirateformsopt_akismet' ),
								'wrap'     => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options'  => [
									'yes' => __( 'Yes', 'pirate-forms' ),
								],
								'disabled' => ! empty( $akismet_msg ),
							],
						]
					),
				],
				'pirate_fields pirate_tooltip'  => [
					'heading'  => __( 'Fields Settings', 'pirate-forms' ),
					'controls' => apply_filters(
						'pirate_forms_admin_controls_for_fields',
						[
							/* Name */
							[
								'id'      => 'pirateformsopt_name_field',
								'type'    => 'select',
								'label'   => [
									'value' => __( 'Name', 'pirate-forms' ),
								],
								'default' => 'req',
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_name_field' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [
									''    => __( 'Do not display', 'pirate-forms' ),
									'yes' => __( 'Display but not required', 'pirate-forms' ),
									'req' => __( 'Required', 'pirate-forms' ),
								],
							],
							/* Email */
							[
								'id'      => 'pirateformsopt_email_field',
								'type'    => 'select',
								'label'   => [
									'value' => __( 'Email address', 'pirate-forms' ),
								],
								'default' => 'req',
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_email_field' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [
									''    => __( 'Do not display', 'pirate-forms' ),
									'yes' => __( 'Display but not required', 'pirate-forms' ),
									'req' => __( 'Required', 'pirate-forms' ),
								],
							],
							/* Subject */
							[
								'id'      => 'pirateformsopt_subject_field',
								'type'    => 'select',
								'label'   => [
									'value' => __( 'Subject', 'pirate-forms' ),
								],
								'default' => 'req',
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_subject_field' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [
									''    => __( 'Do not display', 'pirate-forms' ),
									'yes' => __( 'Display but not required', 'pirate-forms' ),
									'req' => __( 'Required', 'pirate-forms' ),
								],
							],
							/* Message */
							[
								'id'      => 'pirateformsopt_message_field',
								'type'    => 'select',
								'label'   => [
									'value' => __( 'Message', 'pirate-forms' ),
								],
								'default' => 'req',
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_message_field' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [
									''    => __( 'Do not display', 'pirate-forms' ),
									'yes' => __( 'Display but not required', 'pirate-forms' ),
									'req' => __( 'Required', 'pirate-forms' ),
								],
							],
							/* Attachment */
							[
								'id'      => 'pirateformsopt_attachment_field',
								'type'    => 'select',
								'label'   => [
									'value' => __( 'Attachment', 'pirate-forms' ),
								],
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_attachment_field' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [
									''    => __( 'Do not display', 'pirate-forms' ),
									'yes' => __( 'Display but not required', 'pirate-forms' ),
									'req' => __( 'Required', 'pirate-forms' ),
								],
							],
							[
								'id'      => 'pirateformsopt_checkbox_field',
								'type'    => 'select',
								'label'   => [
									'value' => __( 'Checkbox', 'pirate-forms' ),
								],
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_checkbox_field' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped pirateformsopt_checkbox',
								],
								'options' => [
									''    => __( 'Do not display', 'pirate-forms' ),
									'yes' => __( 'Display but not required', 'pirate-forms' ),
									'req' => __( 'Required', 'pirate-forms' ),
								],
							],
							/* Recaptcha */
							[
								'id'      => 'pirateformsopt_recaptcha_field',
								'type'    => 'radio',
								'label'   => [
									'value' => __( 'Add a spam trap', 'pirate-forms' ),
								],
								'default' => $pirate_forms_contactus_recaptcha_show,
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_recaptcha_field' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [
									''       => __( 'No', 'pirate-forms' ),
									'custom' => __( 'Custom', 'pirate-forms' ),
									'yes'    => __( 'Google reCAPTCHA', 'pirate-forms' ),
								],
							],
							/* Site key */
							[
								'id'    => 'pirateformsopt_recaptcha_sitekey',
								'type'  => 'text',
								'label' => [
									'value' => __( 'Site key', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => '<a href="https://www.google.com/recaptcha/admin#list" target="_blank">' . __( 'Create an account here ', 'pirate-forms' ) . '</a>' . __( 'to get the Site key and the Secret key for the reCaptcha.', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'value' => PirateForms_Util::get_option( 'pirateformsopt_recaptcha_sitekey' ),
								'wrap'  => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped pirateformsopt_recaptcha',
								],
							],
							/* Secret key */
							[
								'id'    => 'pirateformsopt_recaptcha_secretkey',
								'type'  => 'password',
								'label' => [
									'value' => __( 'Secret key', 'pirate-forms' ),
								],
								'value' => PirateForms_Util::get_option( 'pirateformsopt_recaptcha_secretkey' ),
								'wrap'  => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped pirateformsopt_recaptcha pirate-forms-password-toggle',
								],
							],
						]
					),
				],
				'pirate_labels pirate_tooltip'  => [
					'heading'  => __( 'Fields Labels', 'pirate-forms' ),
					'controls' => apply_filters(
						'pirate_forms_admin_controls_for_field_labels',
						[
							[
								'id'      => 'pirateformsopt_label_name',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Name', 'pirate-forms' ),
								],
								'default' => __( 'Your Name', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_name' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_email',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Email', 'pirate-forms' ),
								],
								'default' => __( 'Your Email', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_email' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_subject',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Subject', 'pirate-forms' ),
								],
								'default' => __( 'Subject', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_subject' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_message',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Message', 'pirate-forms' ),
								],
								'default' => __( 'Your message', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_message' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_submit_btn',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Submit button', 'pirate-forms' ),
								],
								'default' => $pirate_forms_contactus_button_label,
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_submit_btn' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_checkbox',
								'type'    => 'wysiwyg',
								'label'   => [
									'value' => __( 'Checkbox', 'pirate-forms' ),
								],
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_checkbox' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'wysiwyg' => [
									'editor_class'  => 'pirate-forms-wysiwyg',
									'quicktags'     => false,
									'teeny'         => true,
									'media_buttons' => false,
								],
							],
							[
								'id'      => 'pirateformsopt_email_content',
								'type'    => 'wysiwyg',
								'label'   => [
									'value' => __( 'Email content', 'pirate-forms' ),
									'html'  => '<br/><br/>' . esc_attr( __( 'You can use the following magic tags:', 'pirate-forms' ) ) . '<br/>' . PirateForms_Util::get_magic_tags(),
								],
								'default' => PirateForms_Util::get_default_email_content( true, null, true ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_email_content' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'wysiwyg' => [
									'editor_class'  => 'pirate-forms-wysiwyg',
									'editor_height' => 500,
								],
							],
						]
					),
				],
				'pirate_alerts pirate_tooltip'  => [
					'heading'  => __( 'Alert Messages', 'pirate-forms' ),
					'controls' => apply_filters(
						'pirate_forms_admin_controls_for_alerts',
						[
							[
								'id'      => 'pirateformsopt_label_err_name',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Name required and missing', 'pirate-forms' ),
								],
								'default' => __( 'Enter your name', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_err_name' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_err_email',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'E-mail required and missing', 'pirate-forms' ),
								],
								'default' => __( 'Enter valid email', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_err_email' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_err_subject',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Subject required and missing', 'pirate-forms' ),
								],
								'default' => __( 'Please enter a subject', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_err_subject' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_err_no_content',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Question/comment is missing', 'pirate-forms' ),
								],
								'default' => __( 'Enter your question or comment', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_err_no_content' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_err_no_attachment',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Attachment is missing', 'pirate-forms' ),
								],
								'default' => __( 'Please add an attachment', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_err_no_attachment' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_err_no_checkbox',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Checkbox is not checked', 'pirate-forms' ),
								],
								'default' => __( 'Please select the checkbox', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_err_no_checkbox' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_label_submit',
								'type'    => 'text',
								'label'   => [
									'value' => __( 'Successful form submission text', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => __( 'This text is used on the page if no Success Page is chosen above. This is also used as the confirmation email title, if one is set to send out.', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'default' => __( 'Thanks, your email was sent successfully!', 'pirate-forms' ),
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_label_submit' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
						]
					),
				],
				'pirate_smtp pirate_tooltip'    => [
					'heading'  => __( 'SMTP Options', 'pirate-forms' ),
					'controls' => apply_filters(
						'pirate_forms_admin_controls_for_smtp',
						[
							[
								'id'      => 'pirateformsopt_use_smtp',
								'type'    => 'checkbox',
								'label'   => [
									'value' => __( 'Use SMTP to send emails?', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => __( 'Instead of PHP mail function', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_use_smtp' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [ 'yes' => __( 'Yes', 'pirate-forms' ) ],
							],
							[
								'id'    => 'pirateformsopt_smtp_host',
								'type'  => 'text',
								'label' => [
									'value' => __( 'SMTP Host', 'pirate-forms' ),
								],
								'value' => PirateForms_Util::get_option( 'pirateformsopt_smtp_host' ),
								'wrap'  => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'    => 'pirateformsopt_smtp_port',
								'type'  => 'text',
								'label' => [
									'value' => __( 'SMTP Port', 'pirate-forms' ),
								],
								'value' => PirateForms_Util::get_option( 'pirateformsopt_smtp_port' ),
								'wrap'  => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'      => 'pirateformsopt_use_smtp_authentication',
								'type'    => 'checkbox',
								'label'   => [
									'value' => __( 'Use SMTP Authentication?', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => __( 'If you check this box, make sure the SMTP Username and SMTP Password are completed.', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'default' => 'yes',
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_use_smtp_authentication' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [ 'yes' => __( 'Yes', 'pirate-forms' ) ],
							],
							[
								'id'      => 'pirateformsopt_use_secure',
								'type'    => 'radio',
								'label'   => [
									'value' => __( 'Security?', 'pirate-forms' ),
									'html'  => '<span class="dashicons dashicons-editor-help"></span>',
									'desc'  => [
										'value' => __( 'If you check this box, make sure the SMTP Username and SMTP Password are completed.', 'pirate-forms' ),
										'class' => 'pirate_forms_option_description',
									],
								],
								'value'   => PirateForms_Util::get_option( 'pirateformsopt_use_secure' ),
								'wrap'    => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
								'options' => [
									''    => __( 'No', 'pirate-forms' ),
									'ssl' => __( 'SSL', 'pirate-forms' ),
									'tls' => __( 'TLS', 'pirate-forms' ),
								],
							],
							[
								'id'    => 'pirateformsopt_smtp_username',
								'type'  => 'text',
								'label' => [
									'value' => __( 'SMTP Username', 'pirate-forms' ),
								],
								'value' => PirateForms_Util::get_option( 'pirateformsopt_smtp_username' ),
								'wrap'  => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped',
								],
							],
							[
								'id'    => 'pirateformsopt_smtp_password',
								'type'  => 'password',
								'label' => [
									'value' => __( 'SMTP Password', 'pirate-forms' ),
								],
								'value' => PirateForms_Util::get_option( 'pirateformsopt_smtp_password' ),
								'wrap'  => [
									'type'  => 'div',
									'class' => 'pirate-forms-grouped pirate-forms-password-toggle',
								],
							],
						]
					),
				],
			]
		);
	}

	/**
	 * ******** Save default options if none exist ***********/
	public function settings_init() {
		if ( ! PirateForms_Util::get_option() ) {
			$new_opt = [];
			foreach ( $this->get_plugin_options() as $array ) {
				foreach ( $array['controls'] as $controls ) {
					$new_opt[ $controls['id'] ] = isset( $controls['default'] ) ? $controls['default'] : '';
				}
			}
			PirateForms_Util::set_option( $new_opt );
		}
	}

	/**
	 * Sanitize the options
	 *
	 * @since 2.6.0
	 */
	private function sanitize_options( $params ) {

		foreach ( $params as $key => $value ) {
			$params[ $key ] = wp_kses_post( $value );
		}

		return $params;
	}

	/**
	 * Save the data
	 *
	 * @since    1.0.0
	 */
	public function save_callback() {
		check_ajax_referer( PIRATEFORMS_SLUG, 'security' );

		if ( isset( $_POST['dataSent'] ) ) :
			$dataSent = wp_unslash( $_POST['dataSent'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$params   = [];
			if ( ! empty( $dataSent ) ) :
				parse_str( $dataSent, $params );
			endif;
			if ( ! empty( $params ) ) :
				$params = $this->sanitize_options( $params );

				/**
				 ****** Important fix for saving inputs of type checkbox */
				if ( ! isset( $params['pirateformsopt_store'] ) ) {
					$params['pirateformsopt_store'] = '';
				}
				if ( ! isset( $params['pirateformsopt_recaptcha_field'] ) ) {
					$params['pirateformsopt_recaptcha_field'] = '';
				}
				if ( ! isset( $params['pirateformsopt_nonce'] ) ) {
					$params['pirateformsopt_nonce'] = '';
				}
				if ( ! isset( $params['pirateformsopt_attachment_field'] ) ) {
					$params['pirateformsopt_attachment_field'] = '';
				}
				if ( ! isset( $params['pirateformsopt_use_smtp'] ) ) {
					$params['pirateformsopt_use_smtp'] = '';
				}
				if ( ! isset( $params['pirateformsopt_use_smtp_authentication'] ) ) {
					$params['pirateformsopt_use_smtp_authentication'] = '';
				}
				PirateForms_Util::set_option( $params );
				$pirate_forms_zerif_lite_mods = get_option( 'theme_mods_zerif-lite' );
				if ( empty( $pirate_forms_zerif_lite_mods ) ) :
					$pirate_forms_zerif_lite_mods = [];
				endif;
				if ( isset( $params['pirateformsopt_label_submit_btn'] ) ) :
					$pirate_forms_zerif_lite_mods['zerif_contactus_button_label'] = sanitize_text_field( $params['pirateformsopt_label_submit_btn'] );
				endif;
				if ( isset( $params['pirateformsopt_email'] ) ) :
					$pirate_forms_zerif_lite_mods['zerif_contactus_email'] = $params['pirateformsopt_email'];
				endif;
				if ( isset( $params['pirateformsopt_email_recipients'] ) ) :
					$pirate_forms_zerif_lite_mods['zerif_contactus_email'] = $params['pirateformsopt_email_recipients'];
				endif;
				if ( isset( $params['pirateformsopt_recaptcha_field'] ) && ( $params['pirateformsopt_recaptcha_field'] === 'custom' ) ) :
					$pirate_forms_zerif_lite_mods['zerif_contactus_recaptcha_show'] = 0;
				else :
					$pirate_forms_zerif_lite_mods['zerif_contactus_recaptcha_show'] = 1;
				endif;
				if ( isset( $params['pirateformsopt_recaptcha_sitekey'] ) ) :
					$pirate_forms_zerif_lite_mods['zerif_contactus_sitekey'] = $params['pirateformsopt_recaptcha_sitekey'];
				endif;
				if ( isset( $params['pirateformsopt_recaptcha_secretkey'] ) ) :
					$pirate_forms_zerif_lite_mods['zerif_contactus_secretkey'] = $params['pirateformsopt_recaptcha_secretkey'];
				endif;
				update_option( 'theme_mods_zerif-lite', $pirate_forms_zerif_lite_mods );
			endif;
		endif;
		die();
	}

	/**
	 * Add the columns for contacts listing.
	 *
	 * @since    1.0.0
	 *
	 * @param array $columns array of columns.
	 *
	 * @return array
	 */
	public function manage_contact_posts_columns( $columns ) {
		$tmp     = $columns;
		$columns = [];
		/**
		 * Remove redundant columns.
		 */
		$allowed_keys = [ 'cb', 'title', 'pf_mailstatus', 'pf_form', 'date' ];

		foreach ( $tmp as $key => $val ) {
			if ( 'date' === $key ) {
				// ensure our columns are added before the date.
				$columns['pf_mailstatus'] = __( 'Mail Status', 'pirate-forms' );
			}
			if ( in_array( $key, $allowed_keys, true ) ) {
				$columns[ $key ] = $val;
			}
		}

		return $columns;
	}

	/**
	 * Show the additional columns for contact listing
	 *
	 * @since    1.0.0
	 *
	 * @param string $column the column name.
	 * @param int    $id     The post id.
	 */
	public function manage_contact_posts_custom_column( $column, $id ) {
		if ( $column === 'pf_mailstatus' ) {
			$response = get_post_meta( $id, PIRATEFORMS_SLUG . 'mail-status', true );
			$failed   = $response === 'false';
			echo empty( $response ) ? esc_html__( 'Status not captured', 'pirate-forms' ) : ( $failed ? esc_html__( 'Mail sending failed!', 'pirate-forms' ) : esc_html__( 'Mail sent successfully!', 'pirate-forms' ) );

			if ( $failed ) {
				$reason = get_post_meta( $id, PIRATEFORMS_SLUG . 'mail-status-reason', true );
				if ( ! empty( $reason ) ) {
					echo esc_html( ' (' . $reason . ')' );
				}
			}
		}

		do_action( 'pirate_forms_listing_display', $column, $id );
	}

	/**
	 * Test sending the email.
	 */
	public function test_email() {
		check_ajax_referer( PIRATEFORMS_SLUG, 'security' );
		add_filter( 'pirateformpro_get_form_attributes', [ $this, 'test_configuration' ], 999, 2 );
		add_action( 'pirate_forms_after_processing', [ $this, 'test_result' ], 10, 1 );
		add_filter( 'pirate_forms_validate_request', [ $this, 'test_alter_session' ], 10, 3 );
		$_POST = [
			'honeypot'                     => '',
			'pirate_forms_form_id'         => isset( $_POST['pirate_forms_form_id'] )
				? sanitize_text_field( wp_unslash( $_POST['pirate_forms_form_id'] ) )
				: '',
			'pirate-forms-contact-name'    => 'Test Name',
			'pirate-forms-contact-email'   => get_bloginfo( 'admin_email' ),
			'pirate-forms-contact-subject' => 'Test Email',
			'pirate-forms-contact-message' => 'This is a test.',
		];
		do_action( 'pirate_forms_send_email', true );
	}

	/**
	 * Change the options for testing.
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function test_configuration( $options, $id ) {
		// Disable captcha.
		$options['pirateformsopt_recaptcha_field'] = 'no';
		// Disable attachments.
		$options['pirateformsopt_attachment_field'] = 'no';

		return $options;
	}

	/**
	 * Show admin notices.
	 */
	public function admin_notices() {
		$screen = get_current_screen();
		if ( null === $screen ) {
			return;
		}
		if ( ! isset( $screen->base ) ) {
			return;
		}

		if ( 'toplevel_page_pirateforms-admin' !== $screen->id ) {
			return;
		}

		$options = PirateForms_Util::get_option();

		// check if store submissions is enabled without having a checkbox.
		if ( 'yes' !== $options['pirateformsopt_store'] ) {
			return;
		}

		if ( empty( $options['pirateformsopt_checkbox_field'] ) && false === get_transient( 'pirate_forms_gdpr_notice0' ) ) {
			echo sprintf( '<div data-dismissible="0" class="notice notice-warning pirateforms-notice pirateforms-notice-checkbox pirateforms-notice-gdpr is-dismissible"><p><strong>%s</strong></p></div>', esc_html__( 'According to GDPR we recommend you to ask for consent in order to store user data', 'pirate-forms' ) );
		}
	}

	/**
	 * Generic ajax handler.
	 */
	public function ajax() {
		check_ajax_referer( PIRATEFORMS_SLUG, 'security' );

		if ( isset( $_POST['_action'], $_POST['id'] ) && sanitize_text_field( wp_unslash( $_POST['_action'] ) ) === 'dismiss-notice' ) {
			$id = sanitize_text_field( wp_unslash( $_POST['id'] ) );
			set_transient( 'pirate_forms_gdpr_notice' . $id, 'yes' );
		}

		wp_die();
	}

	/**
	 * Hook into the sent result.
	 */
	public function test_result( $response ) {
		wp_send_json_success( [ 'message' => $response ? __( 'Sent email successfully!', 'pirate-forms' ) : __( 'Sent email failed!', 'pirate-forms' ) ] );
	}

	/**
	 * Clear the session of any errors.
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function test_alter_session( $body, $error_key, $pirate_forms_options ) {
		$_SESSION[ $error_key ] = '';

		return $body;
	}

	/**
	 * Register the private data exporter.
	 */
	public function register_private_data_exporter( $exporters ) {
		$exporters[ PIRATEFORMS_SLUG ] = [
			'exporter_friendly_name' => PIRATEFORMS_NAME,
			'callback'               => [ $this, 'private_data_exporter' ],
		];

		return $exporters;
	}

	/**
	 * Export the private data.
	 */
	public function private_data_exporter( $email_address, $page = 1 ) {
		$export_items = [];

		$query = new WP_Query(
			[
				'post_type'   => 'pf_contact',
				'numberposts' => 300,
				'post_status' => [ 'publish', 'private' ],
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'meta_query'  => [
					[
						'key'   => 'Contact email',
						'value' => $email_address,
					],
				],
			]
		);

		$pirate_forms_options = PirateForms_Util::get_option();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$data   = [];
				$data[] = [
					'name'  => __( 'Email content', 'pirate-forms' ),
					'value' => nl2br( wp_strip_all_tags( $query->post->post_content ) ),
				];

				$data = apply_filters( 'pirate_forms_private_data_exporter', $data, $query->post->ID, $email_address, $page, $pirate_forms_options );

				$export_items[] = [
					'group_id'    => 'pf_contact',
					'group_label' => PIRATEFORMS_NAME,
					'item_id'     => "pf_contact-{$query->post->ID}",
					'data'        => $data,
				];
			}
		}

		return [
			'data' => $export_items,
			'done' => true,
		];
	}

	/**
	 * Register the private data eraser.
	 */
	public function register_private_data_eraser( $erasers ) {
		$erasers[ PIRATEFORMS_SLUG ] = [
			'eraser_friendly_name' => PIRATEFORMS_NAME,
			'callback'             => [ $this, 'private_data_eraser' ],
		];

		return $erasers;
	}

	/**
	 * Erase the private data.
	 */
	public function private_data_eraser( $email_address, $page = 1 ) {
		$query = new WP_Query(
			[
				'post_type'   => 'pf_contact',
				'numberposts' => 300,
				'post_status' => [ 'publish', 'private' ],
				'fields'      => 'ids',
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'meta_query'  => [
					[
						'key'   => 'Contact email',
						'value' => $email_address,
					],
				],
			]
		);

		$retained             = [];
		$removed              = 0;
		$pirate_forms_options = PirateForms_Util::get_option();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				list( $retained, $removed ) = apply_filters(
					'pirate_forms_private_data_eraser',
					[ $retained, $removed ],
					$query->post,
					$email_address,
					$page,
					$pirate_forms_options
				);

				// delete the post last so that all dependent operations are complete.
				if ( false !== wp_delete_post( $query->post, true ) ) {
					++$removed;
				} else {
					$retained[] = $query->post;
				}
			}
		}

		return [
			'items_removed'  => $removed,
			'items_retained' => ! empty( $retained ) ? count( $retained ) : false,
			'messages'       => ! empty( $retained )
				? [
					sprintf(
						/* translators: %d: number of entries retained */
						__( 'Unable to delete %d entries', 'pirate-forms' ),
						count( $retained )
					),
				]
				: [],
			'done'           => true,
		];
	}
}
