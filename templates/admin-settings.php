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
 *
 * @var $this \SunNYCT\WP\Forms\Admin\Settings
 */
?>
<div class="wrap">
    <h1><?php echo __('Settings', SUNNYCT_WP_FORMS_PLUGIN_NAME) ?></h1>
    <form method="post" action="options.php" novalidate="novalidate">
        <?php settings_fields(SUNNYCT_WP_FORMS_PLUGIN_NAME); ?>
        <?php do_settings_sections(SUNNYCT_WP_FORMS_PLUGIN_NAME); ?>
        <table class="form-table">
            <tbody>
            <?php $this->text('google_recaptcha_site_key', __('Site key', SUNNYCT_WP_FORMS_PLUGIN_NAME)) ?>
            <?php $this->text('google_recaptcha_secret_key', __('Secret key', SUNNYCT_WP_FORMS_PLUGIN_NAME)) ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit"
                   name="submit"
                   id="submit"
                   class="button button-primary"
                   value="<?php echo __('Save', SUNNYCT_WP_FORMS_PLUGIN_NAME) ?>">
        </p>
    </form>
</div>
