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
 * @var \WP_Post|null $post
 */
?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $post ? __('Edit form', SUNNYCT_WP_FORMS_PLUGIN_NAME) : __('Add form', SUNNYCT_WP_FORMS_PLUGIN_NAME); ?>
    </h1>
    <a href="admin.php?page=<?php echo SUNNYCT_WP_FORMS_PLUGIN_NAME ?>&action=add" class="page-title-action">
        <?php echo __('Add new', SUNNYCT_WP_FORMS_PLUGIN_NAME); ?>
    </a>
    <hr class="wp-header-end">
    <?php if (isset($_GET['added'])) : ?>
        <div id="message" class="updated notice is-dismissible"><p><?php _e('Link added.'); ?></p></div>
    <?php endif; ?>
    <form name="<?php echo esc_attr( $form_name ); ?>"
          id="<?php echo esc_attr( $form_name ); ?>"
          method="post"
          action="link.php">
        <?php
        if ( ! empty( $link_added ) ) {
            echo $link_added;
        }
        ?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-1">
                <div id="post-body-content">
                    <div class="stuffbox">
                        <h2>
                            <label for="name">
                                <?php echo __('Form name', SUNNYCT_WP_FORMS_PLUGIN_NAME) ?>
                            </label>
                        </h2>
                        <div class="inside">
                            <input type="text"
                                   id="name"
                                   name="name"
                                   style="width: 100%"
                                   maxlength="255"
                                   value="<?php echo esc_attr($link->link_name); ?>" />
                        </div>
                    </div>
                    <div class="stuffbox">
                        <h2>
                            <label for="content">
                                <?php echo __('Form shortcodes', SUNNYCT_WP_FORMS_PLUGIN_NAME) ?>
                            </label>
                        </h2>
                        <div class="inside">
                            <?php wp_editor($content, 'content' ); ?>
                            <p><?php
                                _e('This will be shown when someone hovers over the link in the blogroll, or optionally below the link.');
                            ?></p>
                        </div>
                    </div>
                </div>
                <?php if (!empty($link->link_id)) { ?>
                    <input name="save"
                           type="submit"
                           class="button button-primary button-large"
                           id="publish"
                           value="<?php echo esc_attr('Update', SUNNYCT_WP_FORMS_PLUGIN_NAME) ?>" />
                <?php } else { ?>
                    <input name="save"
                           type="submit"
                           class="button button-primary button-large"
                           id="publish"
                           value="<?php echo esc_attr('Add', SUNNYCT_WP_FORMS_PLUGIN_NAME) ?>" />
                <?php } ?>
                <?php

                if ( $link_id ) : ?>
                    <input type="hidden" name="action" value="save" />
                    <input type="hidden" name="link_id" value="<?php echo (int) $link_id; ?>" />
                    <input type="hidden" name="cat_id" value="<?php echo (int) $cat_id ?>" />
                <?php else: ?>
                    <input type="hidden" name="action" value="add" />
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
