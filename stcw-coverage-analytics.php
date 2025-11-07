<?php
/**
 * Plugin Name: Static Cache Wrangler - Coverage Analytics
 * Plugin URI: https://moderncli.dev/code/static-cache-wrangler/
 * Description: Monitor cache coverage and identify uncached content for Static Cache Wrangler
 * Version: 1.0.0
 * Author: Derick Schaefer
 * Author URI: https://moderncli.dev/author/
 * Text Domain: stcw-coverage-analytics
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires Plugins: static-cache-wrangler
 */

if (!defined('ABSPATH')) exit;

define('STCWCA_VERSION', '1.0.0');
define('STCWCA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('STCWCA_PLUGIN_URL', plugin_dir_url(__FILE__));

spl_autoload_register(function($class) {
    if (strpos($class, 'STCWCA_') !== 0) return;
    $class_file = strtolower(str_replace('_', '-', $class));
    $paths = [STCWCA_PLUGIN_DIR . 'includes/', STCWCA_PLUGIN_DIR . 'admin/'];
    foreach ($paths as $path) {
        $file = $path . 'class-' . $class_file . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

function stcwca_is_stcw_active() { return class_exists('STCW_Core'); }

function stcwca_admin_notice_missing_stcw() {
    if (!stcwca_is_stcw_active()) {
        echo '<div class="notice notice-error"><p><strong>' . esc_html__('Static Cache Wrangler - Coverage Analytics', 'stcw-coverage-analytics') . '</strong> ' . esc_html__('requires', 'stcw-coverage-analytics') . ' <strong>' . esc_html__('Static Cache Wrangler', 'stcw-coverage-analytics') . '</strong> ' . esc_html__('to be installed and activated.', 'stcw-coverage-analytics') . '</p></div>';
    }
}
add_action('admin_notices', 'stcwca_admin_notice_missing_stcw');

function stcwca_init() {
    if (!stcwca_is_stcw_active()) return;
    if (is_admin()) {
        $admin = new STCWCA_Admin();
        $admin->init();
    }
}
add_action('plugins_loaded', 'stcwca_init');

register_activation_hook(__FILE__, function() {
    if (!stcwca_is_stcw_active()) {
        wp_die(
            esc_html__('This plugin requires Static Cache Wrangler to be installed and activated.', 'stcw-coverage-analytics'),
            esc_html__('Plugin Activation Error', 'stcw-coverage-analytics'),
            ['back_link' => true]
        );
    }
});
