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

namespace SunNYCT\WP\Forms\Admin;

class Admin
{
    public function __construct()
    {
        add_filter('manage_' . SUNNYCT_WP_FORMS_PLUGIN_NAME . '_posts_columns', function($defaults){
            return $this->onAdminGridHead($defaults);
        });

        add_action('manage_' . SUNNYCT_WP_FORMS_PLUGIN_NAME . '_posts_custom_column', function($column_name, $post_ID){
            $this->onAdminGridRow($column_name, $post_ID);
        }, 10, 2);

        //add_action('admin_menu', function(){ $this->onAdminMenu(); }, 9);
        add_action('admin_menu', function(){
            add_submenu_page(
                'edit.php?post_type=' . SUNNYCT_WP_FORMS_PLUGIN_NAME,
                __('Readme', SUNNYCT_WP_FORMS_PLUGIN_NAME),
                __('Readme', SUNNYCT_WP_FORMS_PLUGIN_NAME),
                'level_0',
                'readme',
                function(){ include __DIR__ . '/../../templates/admin-readme.php'; }
            );
        });
    }

    private function onAdminGridHead($defaults)
    {
        $defaults['shortcode'] = __('Shortcode', SUNNYCT_WP_FORMS_PLUGIN_NAME);
        return $defaults;
    }

    private function onAdminGridRow($column_name, $post_ID)
    {
        if ($column_name !== 'shortcode') {
            return;
        }

        echo sprintf(
            '<span class="shortcode">
            <input type="text" onfocus="this.select();" readonly="readonly" value="%s" class="large-text code" />
            </span>',
            esc_attr(sprintf('[%s id="%s"]', SUNNYCT_WP_FORMS_PLUGIN_NAME, $post_ID))
        );
    }

    private function onAdminMenu()
    {
        global $submenu;

        add_menu_page(
            __('Forms', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            __('Forms', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'level_0',
            SUNNYCT_WP_FORMS_PLUGIN_NAME,
            '',
            'dashicons-email'
        );

        add_submenu_page(
            SUNNYCT_WP_FORMS_PLUGIN_NAME,
            __('Forms', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            __('Forms', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'level_0',
            SUNNYCT_WP_FORMS_PLUGIN_NAME,
            function(){ $this->handle(); }
        );

        $submenu[SUNNYCT_WP_FORMS_PLUGIN_NAME][] = [
            __('Add', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'level_0',
            'admin.php?page=' . SUNNYCT_WP_FORMS_PLUGIN_NAME . '&action=add'
        ];
    }

    private function handle()
    {
        if (empty($_GET['action'])) {
            include __DIR__ . '/../../templates/admin-grid.php';
        } else if ($_GET['action'] === 'add' || $_GET['action'] === 'edit') {
            $post  = !empty($_GET['id']) ? get_post($_GET['id']) : null;
            $title = $post ? $post->post_title : __('Add', SUNNYCT_WP_FORMS_PLUGIN_NAME);
            include __DIR__ . '/../../templates/admin-edit.php';
        }
    }
}