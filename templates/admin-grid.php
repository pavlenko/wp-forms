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
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo __('Forms', SUNNYCT_WP_FORMS_PLUGIN_NAME) ?></h1>
    <a href="admin.php?page=<?php echo SUNNYCT_WP_FORMS_PLUGIN_NAME ?>&action=add" class="page-title-action">
        <?php echo __('Add', SUNNYCT_WP_FORMS_PLUGIN_NAME); ?>
    </a>
    <?php
    $list_table = new \SunNYCT\WP\Forms\Admin\ListTable();
    $list_table->prepare_items();
    $list_table->display();
    ?>
</div>