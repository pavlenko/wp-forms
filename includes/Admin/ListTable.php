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

if (!class_exists('WP_List_Table') ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class ListTable extends \WP_List_Table {

    public static function define_columns()
    {
        return [
            'cb'        => '<input type="checkbox" />',
            'title'     => __('Title', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'shortcode' => __('Shortcode', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'author'    => __('Author', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'date'      => __('Date', SUNNYCT_WP_FORMS_PLUGIN_NAME),
        ];
    }

    public function __construct()
    {
        parent::__construct([
            'singular' => 'post',
            'plural'   => 'posts',
            'ajax'     => false,
        ]);
    }

    public function prepare_items()
    {
        get_current_screen();
        $per_page = $this->get_items_per_page(SUNNYCT_WP_FORMS_PLUGIN_NAME . '-per-page');

        $this->_column_headers = array($this->get_columns(), [], $this->get_sortable_columns());

        $args = array(
            'posts_per_page' => $per_page,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'offset'         => ($this->get_pagenum() - 1) * $per_page,
        );

        if (!empty($_REQUEST['s'])) {
            $args['s'] = $_REQUEST['s'];
        }

        if (!empty($_REQUEST['orderby'])) {
            if ('title' === $_REQUEST['orderby']) {
                $args['orderby'] = 'title';
            } else if ( 'author' === $_REQUEST['orderby']) {
                $args['orderby'] = 'author';
            } else if ( 'date' === $_REQUEST['orderby']) {
                $args['orderby'] = 'date';
            }
        }

        if (!empty($_REQUEST['order'])) {
            if ('asc' === strtolower($_REQUEST['order'])) {
                $args['order'] = 'ASC';
            } else if ('desc' === strtolower($_REQUEST['order'])) {
                $args['order'] = 'DESC';
            }
        }

        $args = wp_parse_args($args, [
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'offset'         => 0,
            'orderby'        => 'ID',
            'order'          => 'ASC',
        ]);

        $args['post_type'] = SUNNYCT_WP_FORMS_PLUGIN_NAME;

        $query = new \WP_Query();
        $this->items = $query->query($args);

        $args['posts_per_page'] = -1;

        $query->query($args);

        $total_items = $query->found_posts;
        $total_pages = ceil($total_items / $per_page);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'per_page'    => $per_page,
        ]);
    }

    /**
     * @return array
     */
    public function get_columns()
    {
        return [
            'cb'        => '<input type="checkbox" />',
            'title'     => __('Title', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'shortcode' => __('Shortcode', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'author'    => __('Author', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'date'      => __('Date', SUNNYCT_WP_FORMS_PLUGIN_NAME),
        ];
    }

    /**
     * @return array
     */
    public function get_sortable_columns()
    {
        $columns = array(
            'title'  => array('title', true),
            'author' => array('author', false),
            'date'   => array('date', false),
        );

        return $columns;
    }

    /**
     * @return array
     */
    public function get_bulk_actions()
    {
        return ['delete' => __('Delete', SUNNYCT_WP_FORMS_PLUGIN_NAME)];
    }

    /**
     * @param \WP_Post $item
     * @param string   $column_name
     *
     * @return string
     */
    public function column_default($item, $column_name)
    {
        return '';
    }

    /**
     * @param \WP_Post $item
     *
     * @return string
     */
    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item->ID
        );
    }

    /**
     * @param \WP_Post $item
     *
     * @return string
     */
    public function column_title($item)
    {
        $edit_link = sprintf(
            'admin.php?page=%s&id=%s&action=edit',
            SUNNYCT_WP_FORMS_PLUGIN_NAME,
            absint($item->ID)
        );

        $output = sprintf(
            '<strong><a class="row-title" href="%s">%s</a></strong>',
            esc_url($edit_link),
            esc_html($item->post_title)
        );

        $output .= $this->row_actions([
            'edit' => sprintf(
                '<a href="%s">%s</a>',
                esc_url($edit_link),
                esc_html(__('Edit', SUNNYCT_WP_FORMS_PLUGIN_NAME))
            )
        ]);

        return $output;
    }

    /**
     * @param \WP_Post $item
     *
     * @return string
     */
    public function column_author($item)
    {
        $author = get_userdata($item->post_author);

        if (false === $author) {
            return '';
        }

        return esc_html($author->display_name);
    }

    /**
     * @param \WP_Post $item
     *
     * @return string
     */
    public function column_shortcode($item)
    {
        return sprintf(
            '<span class="shortcode">
            <input type="text" onfocus="this.select();" readonly="readonly" value="%s" class="large-text code" />
            </span>',
            esc_attr(sprintf('[sunnyct-wp-forms id="%s"]', absint($item->ID)))
        );
    }

    /**
     * @param \WP_Post $item
     *
     * @return string
     */
    public function column_date($item)
    {
        $t_time = mysql2date(
            __('Y/m/d g:i:s A', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            $item->post_date
        );

        $m_time = $item->post_date;

        $time = mysql2date('G', $item->post_date) - get_option( 'gmt_offset' ) * 3600;

        $time_diff = time() - $time;

        if ($time_diff > 0 && $time_diff < 86400) {
            $h_time = sprintf(
                __('%s ago', SUNNYCT_WP_FORMS_PLUGIN_NAME),
                human_time_diff($time)
            );
        } else {
            $h_time = mysql2date(
                __('Y/m/d', SUNNYCT_WP_FORMS_PLUGIN_NAME),
                $m_time
            );
        }

        return '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
    }
}