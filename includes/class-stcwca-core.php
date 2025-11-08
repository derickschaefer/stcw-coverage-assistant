<?php
/**
 * Coverage Assistant Core
 *
 * Calculates cache coverage statistics and identifies uncached content
 *
 * @package STCWCoverageAssistant
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

class STCWCA_Core {
    
    /**
     * Get coverage statistics
     *
     * @return array Coverage data including percentages and counts
     */
    public static function get_coverage_data() {
        // Get total published content
        $total_posts = wp_count_posts('post');
        $total_pages = wp_count_posts('page');
        
        $published_posts = isset($total_posts->publish) ? (int) $total_posts->publish : 0;
        $published_pages = isset($total_pages->publish) ? (int) $total_pages->publish : 0;
        $total_content = $published_posts + $published_pages;
        
        // Get ALL uncached content (no limit) to get accurate count
        $uncached = self::get_uncached_content(0); // 0 = no limit, get all
        $uncached_count = count($uncached);
        
        // Calculate cached count
        $cached_posts_pages = $total_content - $uncached_count;
        
        // Calculate coverage percentage
        $coverage = $total_content > 0 
            ? round(($cached_posts_pages / $total_content) * 100, 1) 
            : 0;
        
        // Get directory size
        $static_size = class_exists('STCW_Core') ? STCW_Core::get_directory_size() : 0;
        $formatted_size = class_exists('STCW_Core') ? STCW_Core::format_bytes($static_size) : '0 B';
        
        return [
            'total_posts' => $published_posts,
            'total_pages' => $published_pages,
            'total_content' => $total_content,
            'cached_files' => $cached_posts_pages,
            'coverage_percent' => $coverage,
            'uncached_count' => $uncached_count,
            'static_size' => $static_size,
            'formatted_size' => $formatted_size,
        ];
    }
    
    /**
     * Get list of uncached posts and pages
     *
     * @param int $limit Maximum number of results to return (0 = all)
     * @return array Array of uncached content with title, URL, and type
     */
    public static function get_uncached_content($limit = 0) {
        global $wpdb;
        
        // Sanitize limit
        $limit = absint($limit);
        
        // Add limit if specified
        if ($limit > 0) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $posts = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT ID, post_title, post_type, post_modified 
                     FROM {$wpdb->posts} 
                     WHERE post_status = %s 
                     AND post_type IN ('post', 'page')
                     ORDER BY post_modified DESC
                     LIMIT %d",
                    'publish',
                    $limit
                )
            );
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $posts = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT ID, post_title, post_type, post_modified 
                     FROM {$wpdb->posts} 
                     WHERE post_status = %s 
                     AND post_type IN ('post', 'page')
                     ORDER BY post_modified DESC",
                    'publish'
                )
            );
        }
        
        if (!$posts) {
            return [];
        }
        
        $uncached = [];
        foreach ($posts as $post) {
            $url = get_permalink($post->ID);
            $static_file = self::url_to_static_path($url);
            
            // Check if static file exists
            if (!file_exists($static_file)) {
                $uncached[] = [
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'url' => $url,
                    'type' => $post->post_type,
                    'modified' => $post->post_modified,
                ];
            }
        }
        
        return $uncached;
    }
    
    /**
     * Get list of cached content (opposite of uncached)
     *
     * @param int $limit Maximum number of results to return
     * @return array Array of cached content
     */
    public static function get_cached_content($limit = 50) {
        global $wpdb;
        
        // Sanitize limit
        $limit = absint($limit);
        $limit = min($limit, 500);
        
        // Query published posts and pages
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Simple analytics query, caching not beneficial
        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title, post_type, post_modified 
                 FROM {$wpdb->posts} 
                 WHERE post_status = %s 
                 AND post_type IN ('post', 'page')
                 ORDER BY post_modified DESC
                 LIMIT %d",
                'publish',
                $limit
            )
        );
        
        if (!$posts) {
            return [];
        }
        
        $cached = [];
        foreach ($posts as $post) {
            $url = get_permalink($post->ID);
            $static_file = self::url_to_static_path($url);
            
            // Check if static file exists
            if (file_exists($static_file)) {
                $file_time = filemtime($static_file);
                $cached[] = [
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'url' => $url,
                    'type' => $post->post_type,
                    'modified' => $post->post_modified,
                    'cached_time' => $file_time,
                ];
            }
        }
        
        return $cached;
    }
    
    /**
     * Convert URL to static file path
     *
     * @param string $url Page URL
     * @return string Path to static HTML file
     */
    private static function url_to_static_path($url) {
        if (!defined('STCW_STATIC_DIR')) {
            return '';
        }
        
        $parsed = wp_parse_url($url);
        $path = isset($parsed['path']) ? $parsed['path'] : '/';
        
        // Root or homepage
        if ($path === '/' || $path === '') {
            return STCW_STATIC_DIR . 'index.html';
        }
        
        // Remove leading/trailing slashes
        $path = trim($path, '/');
        
        // Build static file path
        return STCW_STATIC_DIR . $path . '/index.html';
    }
    
    /**
     * Check if a specific post/page is cached
     *
     * @param int $post_id Post or Page ID
     * @return bool True if cached
     */
    public static function is_post_cached($post_id) {
        $url = get_permalink($post_id);
        if (!$url) {
            return false;
        }
        
        $static_file = self::url_to_static_path($url);
        return file_exists($static_file);
    }
    
    /**
     * Get coverage trend data (last 30 days)
     *
     * Stores daily snapshots in options table
     *
     * @return array Array of daily coverage percentages
     */
    public static function get_coverage_trend() {
        $trend_data = get_option('stcwca_coverage_trend', []);
        
        // Clean old data (keep only last 30 days)
        $cutoff = strtotime('-30 days');
        $trend_data = array_filter($trend_data, function($item) use ($cutoff) {
            return isset($item['timestamp']) && $item['timestamp'] > $cutoff;
        });
        
        // Add today's data if not already recorded
        $today = gmdate('Y-m-d');
        $has_today = false;
        foreach ($trend_data as $item) {
            if (isset($item['date']) && $item['date'] === $today) {
                $has_today = true;
                break;
            }
        }
        
        if (!$has_today) {
            $coverage = self::get_coverage_data();
            $trend_data[] = [
                'date' => $today,
                'timestamp' => time(),
                'coverage' => $coverage['coverage_percent'],
                'cached_files' => $coverage['cached_files'],
                'total_content' => $coverage['total_content'],
            ];
            
            update_option('stcwca_coverage_trend', $trend_data, false);
        }
        
        // Sort by date
        usort($trend_data, function($a, $b) {
            return strcmp($a['date'] ?? '', $b['date'] ?? '');
        });
        
        return $trend_data;
    }
}
