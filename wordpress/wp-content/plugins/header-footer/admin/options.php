<?php
defined('ABSPATH') || exit;

// Quick security patch, to be better integrated
if (!current_user_can('administrator')) {
    die();
}

load_plugin_textdomain('header-footer', false, 'header-footer/languages');

require_once __DIR__ . '/controls.php';

//update_option('hefo_dismissed', []);

$dismissed = get_option('hefo_dismissed', []);

if (isset($_REQUEST['dismiss']) && check_admin_referer('dismiss')) {
    $dismissed[$_REQUEST['dismiss']] = 1;
    update_option('hefo_dismissed', $dismissed);
    wp_redirect('?page=header-footer%2Fadmin%2Foptions.php');
    exit();
}

if (isset($_POST['save'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'save'))
        die('Page expired');
    $options = hefo_request('options');

    // Another thing to be improved...
    if (!isset($options['enable_php'])) {
        $options['enable_php'] = '0';
    }

    if (empty($options['mobile_user_agents'])) {
        $options['mobile_user_agents'] = "phone\niphone\nipod\nandroid.+mobile\nxoom";
    }
    $agents1 = explode("\n", $options['mobile_user_agents']);
    $agents2 = array();
    foreach ($agents1 as &$agent) {
        $agent = trim($agent);
        if (empty($agent))
            continue;
        $agents2[] = strtolower($agent);
    }
    $options['mobile_user_agents_parsed'] = implode('|', $agents2);

    update_option('hefo', $options);
} else {
    $options = get_option('hefo');
}
?>

<style>
    .notice {
        font-size: 1rem;
        padding: 1rem;
        line-height: 140%;
    }
</style>

<script>
    jQuery(function () {

        jQuery("textarea.hefo-cm").each(function () {
            wp.codeEditor.initialize(this);
        });
        jQuery("#hefo-tabs").tabs();
    });
</script>

<div class="wrap">
    <!--https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5PHGDGNHAYLJ8-->

    <h2>Head, Footer and Post Injections</h2>

    <?php if (!isset($dismissed['rate'])) { ?>
        <div class="notice notice-success">
                I never asked before and I'm curious: <a href="http://wordpress.org/extend/plugins/header-footer/" target="_blank"><strong>would you rate this plugin</strong></a>?
                (takes only few seconds required - account on WordPress.org, every blog owner should have one...). <strong>Really appreciated, Stefano</strong>.
                <a class="hefo-dismiss" href="<?php echo wp_nonce_url($_SERVER['REQUEST_URI'] . '&dismiss=rate&noheader=1', 'dismiss') ?>">&times;</a>
        </div>
    <?php } ?>

    <?php if (!isset($dismissed['newsletter'])) { ?>
        <div class="notice notice-success">
                If you want to be informed of important updated of this plugin, you may want to subscribe to my (rare) newsletter<br>
            <form action="http://www.satollo.net/?na=s" target="_blank" method="post">
                <input type="hidden" value="header-footer" name="nr">
                <input type="hidden" value="2" name="nl[]">
                <input type="email" name="ne" value="<?php echo esc_attr(get_option('admin_email')) ?>">
                <input type="submit" value="Subscribe">
            </form>
            <a class="hefo-dismiss" href="<?php echo wp_nonce_url($_SERVER['REQUEST_URI'] . '&dismiss=newsletter&noheader=1', 'dismiss') ?>">&times;</a>

        </div>
    <?php } ?>

    <?php if (!HEADER_FOOTER_ALLOW_PHP) { ?>
        <div class="notice notice-warning">
            PHP is disbaled by the constant <code>HEADER_FOOTER_ALLOW_PHP</code> in the
            <code>wp-config.php</code> file.
        </div>
    <?php } ?>

    <?php if (is_multisite() && !HEADER_FOOTER_MULTISITE_ALLOW_PHP) { ?>
        <div class="notice notice-warning">
            PHP is not allowed on multisite installations since a site admin can escalate to super-admin. If you're the only
            administrator of all the sites, you can enable PHP setting the constant
            <code>HEADER_FOOTER_MULTISITE_ALLOW_PHP</code> to <code>true</code> in the
            <code>wp-config.php</code> file.
        </div>
    <?php } ?>

    <div class="notice notice-success">
        Did this plugin save you lot of time and troubles?
        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5PHGDGNHAYLJ8" target="_blank"><img style="vertical-align: bottom" src="<?php echo plugins_url('header-footer') ?>/images/donate.png"></a>
        To help children. Even <b>2$</b> help. <a href="http://www.satollo.net/donations" target="_blank">Please read more</a>. Thank you.
        <br>
        Are you profitably using this free plugin for your customers? One more reason to consider a
        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5PHGDGNHAYLJ8" target="_blank">donation</a>. Thank you.
    </div>

    <div style="padding: 15px; background-color: #fff; border: 1px solid #eee; font-size: 16px; line-height: 22px">
        <?php
        if (apply_filters('hefo_php_exec', $options['enable_php'])) {
            esc_html_e('PHP is allowed in your code.', 'header-footer');
        } else {
            esc_html_e('PHP is NOT allowed in your code (disabled by your theme or a plugin)', 'header-footer');
        }
        ?>
        <br>

        <?php esc_html_e('Mobile configuration is now deprecated', 'header-footer'); ?>.
        <a href="https://www.satollo.net/plugins/header-footer" target="_blank" class="readmore"><?php esc_html_e('Read more', 'header-footer'); ?></a>

    </div>


    <form method="post" action="">
        <?php wp_nonce_field('save') ?>

        <p>
            <input type="submit" class="button-primary" name="save" value="<?php esc_attr_e('save', 'header-footer'); ?>">
        </p>

        <div id="hefo-tabs">
            <ul>
                <li><a href="#tabs-first"><?php esc_html_e('Head and footer', 'header-footer'); ?></a></li>
                <li><a href="#tabs-post"><?php esc_html_e('Posts', 'header-footer'); ?></a></li>
                <li><a href="#tabs-cpts"><?php esc_html_e('CPTs', 'header-footer'); ?></a></li>
                <li><a href="#tabs-post-inner"><?php esc_html_e('Inside posts', 'header-footer'); ?></a></li>
                <li><a href="#tabs-page"><?php esc_html_e('Pages', 'header-footer'); ?></a></li>
                <li><a href="#tabs-excerpt"><?php esc_html_e('Excerpts', 'header-footer'); ?></a></li>
                <li><a href="#tabs-5"><?php esc_html_e('Snippets', 'header-footer'); ?></a></li>
                <li><a href="#tabs-amp"><?php esc_html_e('AMP', 'header-footer'); ?></a></li>
                <li><a href="#tabs-generics"><?php esc_html_e('Generics', 'header-footer'); ?></a></li>
                <li><a href="#tabs-8"><?php esc_html_e('Advanced', 'header-footer'); ?></a></li>
                <li><a href="#tabs-7"><?php esc_html_e('Notes and...', 'header-footer'); ?></a></li>
            </ul>


            <div id="tabs-first">

                <h3><?php esc_html_e('<HEAD> page section injection', 'header-footer') ?></h3>
                <div class="row">

                    <div class="col-2">
                        <label><?php esc_html_e('On every page', 'header-footer') ?></label>
                        <?php hefo_base_textarea_cm('head'); ?>
                    </div>
                    <div class="col-2">
                        <label><?php esc_html_e('Only on the home page', 'header-footer') ?></label>
                        <?php hefo_base_textarea_cm('head_home'); ?>
                    </div>
                </div>

                <h3><?php esc_html_e('After the <BODY> tag', 'header-footer') ?></h3>
                <div class="row">

                    <div class="col-2">
                        <label><?php esc_html_e('Desktop', 'header-footer') ?>*</label>
                        <?php hefo_base_textarea_cm('body'); ?>
                    </div>
                    <div class="col-2">
                        <?php hefo_base_checkbox('mobile_body_enabled', __('Mobile', 'header-footer')); ?>
                        <?php hefo_base_textarea_cm('mobile_body'); ?>
                    </div>

                </div>
                <h3><?php esc_html_e('Before the &lt;/BODY&gt; closing tag (footer)', 'header-footer') ?></h3>
                <div class="row">
                    <div class="col-2">
                        <label><?php esc_html_e('Desktop', 'header-footer') ?>*</label>
                        <?php hefo_base_textarea_cm('footer'); ?>
                    </div>
                    <div class="col-2">
                        <?php hefo_base_checkbox('mobile_footer_enabled', __('Mobile', 'header-footer')); ?>
                        <?php hefo_base_textarea_cm('mobile_footer'); ?>
                    </div>
                </div>

                <div class="clearfix"></div>

            </div>

            <div id="tabs-generics">

                <?php for ($i = 1; $i <= 5; $i++) { ?>
                    <h3>Generic injection <?php echo $i; ?></h3>
                    <p>Inject before the <?php hefo_base_text('generic_tag_' . $i); ?> marker</p>
                    <div class="row">
                        <div class="col-2">
                            <label><?php esc_html_e('Desktop', 'header-footer') ?>*</label>
                            <?php hefo_base_textarea_cm('generic_' . $i); ?>
                        </div>
                        <div class="col-2">
                            <?php hefo_base_checkbox('mobile_generic_enabled_' . $i, __('Mobile', 'header-footer')); ?>
                            <?php hefo_base_textarea_cm('mobile_generic_' . $i); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                <?php } ?>
                <div class="clearfix"></div>
            </div>



            <div id="tabs-post">
                <p>
                    Please take the time to <a href="http://www.satollo.net/plugins/header-footer" target="_blank">read this page</a> to understand how the "mobile" configuration works.
                    See the "advanced tab" to configure the mobile device detection.
                </p>

                <h3><?php esc_html_e('Before the post content', 'header-footer'); ?></h3>
                <div class="row">

                    <div class="col-2">
                        <label><?php esc_html_e('Desktop', 'header-footer') ?>*</label>
                        <?php hefo_base_textarea_cm('before'); ?>
                    </div>
                    <div class="col-2">
                        <?php hefo_base_checkbox('mobile_before_enabled', __('Mobile', 'header-footer')); ?>
                        <?php hefo_base_textarea_cm('mobile_before'); ?>
                    </div>
                </div>

                <div class="clearfix"></div>

                <h3><?php esc_html_e('After the post content', 'header-footer'); ?></h3>
                <div class="row">

                    <div class="col-2">
                        <label><?php esc_html_e('Desktop', 'header-footer') ?>*</label>
                        <?php hefo_base_textarea_cm('after'); ?>
                    </div>
                    <div class="col-2">
                        <?php hefo_base_checkbox('mobile_after_enabled', __('Mobile', 'header-footer')); ?>
                        <?php hefo_base_textarea_cm('mobile_after'); ?>
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>

            <div id="tabs-cpts">
                <?php
                $post_types = get_post_types(['public' => true], 'objects', 'and');
                ?>

                <?php foreach ($post_types as $post_type) { ?>
                    <?php
                    if ($post_type->name === 'post' || $post_type->name === 'page' || $post_type->name === 'attachment') {
                        continue;
                    }
                    ?>
                    <h3><?php echo esc_html($post_type->label) ?></h3>
                    <p>
                        <?php
                        hefo_field_select($post_type->name . '_mode', ['' => __('Use the post configuration', 'header-footer'),
                            'enabled' => __('Enable injections below', 'header-footer'),
                            'disabled' => __('Do not inject', 'header-footer')]);
                        ?>
                    </p>
                    <div class="row">

                        <div class="col-2">
                            <label><?php esc_html_e('Before the post content', 'header-footer'); ?></label>
                            <?php hefo_base_textarea_cm($post_type->name . '_before'); ?>
                        </div>





                        <div class="col-2">
                            <label><?php esc_html_e('After the post content', 'header-footer'); ?></label>
                            <?php hefo_base_textarea_cm($post_type->name . '_after'); ?>
                        </div>

                    </div>

                    <div class="clearfix"></div>

                <?php } ?>
            </div>

            <div id="tabs-post-inner">

                <?php for ($i = 1; $i <= 5; $i++) { ?>
                    <h3>Inner post injection <?php echo $i; ?></h3>
                    <?php hefo_rule($i); ?>
                    <div class="row">
                        <div class="col-2">
                            <label><?php esc_html_e('Desktop', 'header-footer') ?>*</label>
                            <?php hefo_base_textarea_cm('inner_' . $i); ?>
                        </div>
                        <div class="col-2">
                            <?php hefo_base_checkbox('mobile_inner_enabled_' . $i, __('Mobile', 'header-footer')); ?>
                            <?php hefo_base_textarea_cm('mobile_inner_' . $i); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                <?php } ?>
            </div>


            <div id="tabs-page">

                <?php hefo_base_checkbox('page_use_post', __('Use the post configurations', 'header-footer')); ?><br>
                <?php hefo_base_checkbox('page_add_tags', __('Let pages to have tags', 'header-footer')); ?><br>
                <?php hefo_base_checkbox('page_add_categories', __('Let pages to have categories', 'header-footer')); ?>

                <h3><?php esc_html_e('Before the page content', 'header-footer') ?></h3>
                <div class="row">

                    <div class="col-2">
                        <label><?php _e('Desktop', 'header-footer') ?>*</label>
                        <?php hefo_base_textarea_cm('page_before'); ?>
                    </div>
                    <div class="col-2">
                        <?php hefo_base_checkbox('mobile_page_before_enabled', __('Mobile', 'header-footer')); ?><br>
                        <?php hefo_base_textarea_cm('mobile_page_before'); ?>
                    </div>
                </div>

                <div class="clearfix"></div>

                <h3><?php _e('After the page content', 'header-footer') ?></h3>
                <div class="row">

                    <div class="col-2">
                        <label><?php _e('Desktop', 'header-footer') ?>*</label>
                        <?php hefo_base_textarea_cm('page_after'); ?>
                    </div>
                    <div class="col-2">
                        <?php hefo_base_checkbox('mobile_page_after_enabled', __('Mobile', 'header-footer')); ?><br>
                        <?php hefo_base_textarea_cm('mobile_page_after'); ?>
                    </div>
                </div>

                <div class="clearfix"></div>

            </div>

            <div id="tabs-excerpt">

                <p><?php esc_html_e('It works only on category and tag pages.', 'header-footer'); ?></p>
                <table class="form-table">
                    <tr valign="top"><?php hefo_field_textarea('excerpt_before', __('Code to be inserted before each post excerpt', 'header-footer'), ''); ?></tr>
                    <tr valign="top"><?php hefo_field_textarea('excerpt_after', __('Code to be inserted after each post excerpt', 'header-footer'), ''); ?></tr>
                </table>
            </div>

            <!-- AMP -->

            <div id="tabs-amp">
                <p>
                    You need the <a href="https://it.wordpress.org/plugins/amp/" target="_blank">AMP</a> plugin. Other AMP plugins could be supported
                    in the near future.
                </p>

                <h3><?php esc_html_e('<HEAD> page section', 'header-footer') ?></h3>
                <div class="row">
                    <div class="col-1">
                        <?php hefo_base_textarea_cm('amp_head'); ?>
                    </div>
                </div>

                <div class="clearfix"></div>

                <h3><?php esc_html_e('Extra CSS', 'header-footer') ?></h3>
                <div class="row">
                    <div class="col-1">
                        <?php hefo_base_textarea_cm('amp_css'); ?>
                    </div>
                </div>

                <div class="clearfix"></div>

                <h3><?php esc_html_e('Just after the <BODY> tag', 'header-footer') ?></h3>
                <div class="row">
                    <div class="col-1">
                        <?php hefo_base_textarea_cm('amp_body'); ?>
                    </div>
                </div>


                <div class="clearfix"></div>

                <h3><?php esc_html_e('Before the post content', 'header-footer') ?></h3>
                <div class="row">
                    <div class="col-1">

                        <?php hefo_base_textarea_cm('amp_post_before'); ?>
                    </div>
                </div>

                <div class="clearfix"></div>

                <h3><?php esc_html_e('After the post content', 'header-footer') ?></h3>
                <div class="row">

                    <div class="col-1">

                        <?php hefo_base_textarea_cm('amp_post_after'); ?>

                    </div>
                </div>
                <div class="clearfix"></div>

                <h3><?php esc_html_e('Footer', 'header-footer') ?></h3>
                <div class="row">

                    <div class="col-1">

                        <?php hefo_base_textarea_cm('amp_footer'); ?>

                    </div>
                </div>
                <div class="clearfix"></div>

            </div>


            <div id="tabs-5">
                <p>
                    <?php esc_html_e('Common snippets that can be used in any header or footer area referring them as [snippet_N] where N is the snippet number
            from 1 to 5. Snippets are inserted before PHP evaluation.', 'header-footer'); ?><br />
                    <?php esc_html_e('Useful for social button to be placed before and after the post or in posts and pages.', 'header-footer'); ?>
                </p>
                <table class="form-table">
                    <?php for ($i = 1; $i <= 5; $i++) { ?>
                        <tr valign="top"><?php hefo_field_textarea('snippet_' . $i, __('Snippet ' . $i, 'header-footer'), ''); ?></tr>
                    <?php } ?>
                </table>
                <div class="clearfix"></div>
            </div>

            <div id="tabs-8">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">PHP</th>
                        <?php hefo_field_checkbox_only2('enable_php', __('Enable PHP execution', 'header-footer'), ''); ?>
                    </tr>
                </table>
                <table class="form-table">
                    <tr valign="top">
                        <?php
                        hefo_field_textarea('mobile_user_agents', __('Mobile user agent strings', 'header-footer'), 'For coders: a regular expression is built with those values and the resulting code will be<br>'
                                . '<code>preg_match(\'/' . $options['mobile_user_agents_parsed'] . '/\', ...);</code><br>' .
                                '<a href="http://www.satollo.net/plugins/header-footer" target="_blank">Read this page</a> for more.');
                        ?>

                    </tr>
                </table>

                <h3>Head meta links</h3>
                <p>
                    WordPress automatically add some meta link on the head of the page, for example the RSS links, the previous and next
                    post links and so on. Here you can disable those links if not of interest.
                </p>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Disable css link id</th>
                        <?php hefo_field_checkbox_only('disable_css_id', __('Disable the id attribute on css links generated by WordPress', 'header-footer'), '', 'http://www.satollo.net/plugins/header-footer#disable_css_id'); ?>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Disable css media</th>
                        <?php hefo_field_checkbox_only('disable_css_media', __('Disable the media attribute on css links generated by WordPress, id the option above is enabled.', 'header-footer'), '', 'http://www.satollo.net/plugins/header-footer#disable_css_media'); ?>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Extra feed links</th>
                        <?php hefo_field_checkbox_only('disable_feed_links_extra', __('Disable extra feed links like category feeds or single post comments feeds', 'header-footer')); ?>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Short link</th>
                        <?php hefo_field_checkbox_only('disable_wp_shortlink_wp_head', __('Disable the short link for posts', 'header-footer')); ?>
                    </tr>
                    <tr valign="top">
                        <th scope="row">WLW Manifest</th>
                        <?php hefo_field_checkbox_only('disable_wlwmanifest_link', __('Disable the Windows Live Writer manifest', 'header-footer')); ?>
                    </tr>
                    <tr valign="top">
                        <th scope="row">RSD link</th>
                        <?php hefo_field_checkbox_only('disable_rsd_link', __('Disable RSD link', 'header-footer')); ?>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Adjacent post links</th>
                        <?php hefo_field_checkbox_only('disable_adjacent_posts_rel_link_wp_head', __('Disable adjacent post links', 'header-footer')); ?>
                    </tr>
                </table>
                <div class="clearfix"></div>
            </div>


            <div id="tabs-7">
                <table class="form-table">
                    <tr valign="top"><?php hefo_field_textarea('notes', __('Notes and parked codes', 'header-footer'), ''); ?></tr>
                </table>
                <div class="clearfix"></div>
            </div>




        </div>
        <p>* if no mobile alternative is activated</p>
        <p class="submit"><input type="submit" class="button-primary" name="save" value="<?php esc_attr_e('save', 'header-footer'); ?>"></p>

    </form>
</div>


