<?php
/**
 * Admin Dashboard
 *
 * Handles admin interface for coverage assistant
 *
 * @package STCWCoverageAssistant
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

class STCWCA_Admin {

    /**
     * Initialize admin functionality
     */
    public function init() {
        add_action('admin_menu', [$this, 'add_submenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Add submenu page under Static Cache menu
     */
    public function add_submenu() {
        add_submenu_page(
            'static-cache-wrangler',
            __('Coverage Assistant', 'stcw-coverage-assistant'),
            __('Coverage Assistant', 'stcw-coverage-assistant'),
            'manage_options',
            'stcw-coverage-assistant',
            [$this, 'render_page']
        );
    }

    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook Current admin page hook
     */
    public function enqueue_assets($hook) {
        if ($hook !== 'static-cache_page_stcw-coverage-assistant') {
            return;
        }

        // Admin styles
        wp_enqueue_style(
            'stcwca-admin-style',
            STCWCA_PLUGIN_URL . 'admin/css/admin-style.css',
            [],
            STCWCA_VERSION
        );

        // JS for Copy‑Link functionality
        wp_enqueue_script(
            'stcwca-admin-script',
            STCWCA_PLUGIN_URL . 'admin/js/admin-script.js',
            ['jquery'],
            STCWCA_VERSION,
            true
        );

        wp_localize_script('stcwca-admin-script', 'stcwcaData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('copy_link_nonce'),
        ]);
    }

    /**
     * Render the admin page
     */
    public function render_page() {
        if (!current_user_can('manage_options')) {
            wp_die(
                esc_html__('You do not have sufficient permissions to access this page.', 'stcw-coverage-assistant')
            );
        }

        $coverage = STCWCA_Core::get_coverage_data();
        $uncached = STCWCA_Core::get_uncached_content(0); // Get ALL uncached content (no limit)
        $cached   = STCWCA_Core::get_cached_content(10);

        require_once STCWCA_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
}
