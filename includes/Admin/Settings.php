<?php
/**
 * SunNY Creative Technologies
 *
 *   #####                                ##     ##    ##      ##
 * ##     ##                              ###    ##    ##      ##
 * ##                                     ####   ##     ##    ##
 * ##           ##     ##    ## #####     ## ##  ##      ##  ##
 *   #####      ##     ##    ###    ##    ##  ## ##       ####
 *        ##    ##     ##    ##     ##    ##   ####        ##
 *        ##    ##     ##    ##     ##    ##    ###        ##
 * ##     ##    ##     ##    ##     ##    ##     ##        ##
 *   #####        #######    ##     ##    ##     ##        ##
 *
 * C  R  E  A  T  I  V  E     T  E  C  H  N  O  L  O  G  I  E  S
 */

namespace PE\WP\Forms\Admin;

class Settings
{
    /**
     * Settings constructor.
     */
    public function __construct()
    {
        add_action('admin_init', function(){
            add_settings_section(
                SUNNYCT_WP_FORMS_PLUGIN_NAME,
                __('Google reCaptcha', SUNNYCT_WP_FORMS_PLUGIN_NAME),
                '__return_false',
                SUNNYCT_WP_FORMS_PLUGIN_NAME
            );

            register_setting(SUNNYCT_WP_FORMS_PLUGIN_NAME, 'google_recaptcha_site_key');
            register_setting(SUNNYCT_WP_FORMS_PLUGIN_NAME, 'google_recaptcha_secret_key');
        });

        add_action('admin_menu', function(){
            add_submenu_page(
                'edit.php?post_type=' . SUNNYCT_WP_FORMS_PLUGIN_NAME,
                __('Settings', SUNNYCT_WP_FORMS_PLUGIN_NAME),
                __('Settings', SUNNYCT_WP_FORMS_PLUGIN_NAME),
                'level_0',
                'settings',
                function(){ include __DIR__ . '/../../templates/admin-settings.php'; }
            );
        });
    }

    /**
     * Render text field helper
     *
     * @param string $name
     * @param string $label
     * @param string $description
     */
    public function text($name, $label, $description = null)
    {
        ?>
        <tr>
            <th scope="row">
                <label for="<?php echo $name ?>">
                    <?php echo $label ?>
                </label>
            </th>
            <td>
                <input id="<?php echo $name ?>"
                       name="<?php echo $name ?>"
                       type="text"
                       <?php if ($description) { ?>aria-describedby="<?php echo $name ?>__description"<?php } ?>
                       value="<?php echo esc_attr(get_option($name)); ?>"
                       class="regular-text">
                <?php if ($description) { ?>
                    <p class="description" id="<?php echo $name ?>__description">
                        <?php echo $description ?>
                    </p>
                <?php } ?>
            </td>
        </tr>
        <?php
    }
}