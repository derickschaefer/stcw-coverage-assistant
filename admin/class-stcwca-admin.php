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
        
        // Add AJAX handlers for crawler
        add_action('wp_ajax_stcwca_get_uncached_urls', [$this, 'ajax_get_uncached_urls']);
        add_action('wp_ajax_stcwca_process_url', [$this, 'ajax_process_url']);
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

        // Crawler JavaScript
        wp_enqueue_script(
            'stcwca-crawler',
            STCWCA_PLUGIN_URL . 'admin/js/crawler.js',
            ['jquery'],
            STCWCA_VERSION,
            true
        );

        wp_localize_script('stcwca-admin-script', 'stcwcaData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('copy_link_nonce'),
        ]);
        
        wp_localize_script('stcwca-crawler', 'stcwcaCrawler', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('stcwca_crawler_nonce'),
            'strings' => [
                'keepTabOpen' => __('Keep this tab open while caching is in progress.', 'stcw-coverage-assistant'),
                'confirmClose' => __('Caching is still in progress. Are you sure you want to leave?', 'stcw-coverage-assistant'),
                'complete' => __('Caching complete!', 'stcw-coverage-assistant'),
                'error' => __('An error occurred', 'stcw-coverage-assistant'),
            ],
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

    /**
     * AJAX: Get list of uncached URLs for crawler
     */
    public function ajax_get_uncached_urls() {
        check_ajax_referer('stcwca_crawler_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
        }
        
        // Check if GUI crawler is available
        if (!STCWCA_Core::is_gui_crawler_available()) {
            wp_send_json_error([
                'message' => 'GUI crawler not available yet',
                'threshold' => STCWCA_Core::get_gui_crawler_threshold(),
            ]);
        }
        
        // Get all uncached content
        $uncached = STCWCA_Core::get_uncached_content(0);
        
        if (empty($uncached)) {
            wp_send_json_success([
                'urls' => [],
                'count' => 0,
                'message' => 'All content is already cached!',
            ]);
        }
        
        // Extract just URLs
        $urls = array_map(function($item) {
            return $item['url'];
        }, $uncached);
        
        wp_send_json_success([
            'urls' => array_values($urls),
            'count' => count($urls),
            'estimate' => STCWCA_Crawler::estimate_crawl_time(count($urls)),
        ]);
    }

    /**
     * AJAX: Process a single URL
     */
    public function ajax_process_url() {
        check_ajax_referer('stcwca_crawler_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
        }
        
        $url = isset($_POST['url']) ? esc_url_raw(wp_unslash($_POST['url'])) : '';
        
        if (empty($url)) {
            wp_send_json_error(['message' => 'No URL provided']);
        }
        
        // Process the URL
        $result = STCWCA_Crawler::process_single_url($url);
        
        wp_send_json_success($result);
    }
}
