<?php
/**
 * Crawler Engine
 *
 * Shared crawling logic for CLI and GUI
 *
 * @package STCWCoverageAssistant
 * @since 1.0.6
 */

if (!defined('ABSPATH')) exit;

class STCWCA_Crawler {
    
    /**
     * Process a single URL (shared by CLI and AJAX)
     *
     * @param string $url URL to crawl
     * @return array Result with success, response_time, error
     */
    public static function process_single_url($url) {
        $start_time = microtime(true);
        
        $response = wp_remote_get($url, [
            'timeout' => 30,
            'sslverify' => false,
            'headers' => [
                'User-Agent' => 'WP-CLI/Coverage-Assistant-Crawler',
            ],
        ]);
        
        $response_time = round((microtime(true) - $start_time) * 1000); // ms
        
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'response_time' => $response_time,
                'error' => $response->get_error_message(),
                'status_code' => 0,
            ];
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        
        return [
            'success' => $status_code === 200,
            'response_time' => $response_time,
            'error' => $status_code !== 200 ? "HTTP {$status_code}" : null,
            'status_code' => $status_code,
        ];
    }
    
    /**
     * Calculate adaptive delay based on performance stats
     *
     * @param array $stats Performance statistics
     * @return int Delay in milliseconds
     */
    public static function calculate_adaptive_delay($stats) {
        $min_delay = 500;    // Never faster than 500ms
        $max_delay = 3000;   // Never slower than 3s
        $current_delay = isset($stats['current_delay']) ? $stats['current_delay'] : 1000;
        
        // Get average response time (last 10 requests)
        $avg_response_time = isset($stats['avg_response_time']) ? $stats['avg_response_time'] : 500;
        
        // Get error rate
        $total_requests = $stats['successful'] + $stats['failed'];
        $error_rate = $total_requests > 0 ? $stats['failed'] / $total_requests : 0;
        
        // High error rate (>15%) - slow down significantly
        if ($error_rate > 0.15) {
            $current_delay = min($max_delay, $current_delay * 1.5);
        }
        // Slow server (>1.5s response) - increase delay
        elseif ($avg_response_time > 1500) {
            $current_delay = min($max_delay, $current_delay * 1.2);
        }
        // Fast server (<400ms response) and low errors (<5%) - speed up
        elseif ($avg_response_time < 400 && $error_rate < 0.05) {
            $current_delay = max($min_delay, $current_delay * 0.85);
        }
        
        return round($current_delay);
    }
    
    /**
     * Estimate crawl time for a number of URLs
     *
     * @param int $url_count Number of URLs to crawl
     * @param int $avg_delay Average delay between requests (ms)
     * @return array Human-readable time estimate
     */
    public static function estimate_crawl_time($url_count, $avg_delay = 1500) {
        // Estimate: avg_delay per URL (includes response time + throttle)
        $total_seconds = ($url_count * $avg_delay) / 1000;
        
        if ($total_seconds < 60) {
            return [
                'seconds' => ceil($total_seconds),
                'formatted' => '~' . ceil($total_seconds) . ' seconds',
            ];
        } else {
            $minutes = ceil($total_seconds / 60);
            return [
                'minutes' => $minutes,
                'formatted' => '~' . $minutes . ' minute' . ($minutes > 1 ? 's' : ''),
            ];
        }
    }
}
