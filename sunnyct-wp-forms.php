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
 * Plugin Name: [SunNY CT] WP symfony forms integration
 * Description: Integrate symfony form library for advanced form generation and submission
 * Version:     1.4.4
 * Author:      SunNY Creative Technologies
 * Author URI:  http://sunnyct.com
 */

if (!defined('ABSPATH')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else if (!file_exists(ABSPATH . 'vendor/autoload.php') && !file_exists(ABSPATH . '../vendor/autoload.php')) {
    throw new RuntimeException('Composer not installed');
}

define('SUNNYCT_WP_FORMS_PLUGIN_NAME', basename(__FILE__, '.php'));
define('SUNNYCT_WP_FORMS_PLUGIN_VERSION', get_file_data(__FILE__, ['Version' => 'Version'], 'plugin')['Version']);

if (!function_exists('Forms')) {
    /**
     * @return \SunNYCT\WP\Forms\Forms
     */
    function Forms()
    {
        return \SunNYCT\WP\Forms\Forms::getInstance();
    }
}

\SunNYCT\WP\Forms\Forms::getInstance();