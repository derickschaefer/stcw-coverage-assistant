<?php
/**
 * WP-CLI Commands for Coverage Assistant
 *
 * Provides 'wp scw-coverage' namespace for coverage-related commands
 *
 * @package STCWCoverageAssistant
 * @since 1.1.0
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_CLI')) {
    return;
}

/**
 * Coverage Assistant CLI Commands
 */
class STCWCA_CLI {

    /**
     * Display coverage statistics
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Output format (table, json, csv, yaml)
     * ---
     * default: table
     * options:
     *   - table
     *   - json
     *   - csv
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     # Show coverage statistics in table format
     *     $ wp scw-coverage stats
     *
     *     # Get coverage data as JSON for automation
     *     $ wp scw-coverage stats --format=json
     *
     * @when after_wp_load
     */
    public function stats($args, $assoc_args) {
        $format = isset($assoc_args['format']) ? $assoc_args['format'] : 'table';

        if (!class_exists('STCWCA_Core')) {
            WP_CLI::error('Coverage Assistant plugin is not active.');
        }

        $coverage = STCWCA_Core::get_coverage_data();

        if ($format === 'json') {
            WP_CLI::line(wp_json_encode($coverage, JSON_PRETTY_PRINT));
            return;
        }

        if ($format === 'yaml') {
            WP_CLI::line(WP_CLI\Utils\mustache_render(
                'coverage.yaml',
                $coverage
            ));
            return;
        }

        if ($format === 'csv') {
            $csv_data = [
                ['Metric', 'Value'],
                ['Coverage Percentage', $coverage['coverage_percent'] . '%'],
                ['Total Content', $coverage['total_content']],
                ['Total Posts', $coverage['total_posts']],
                ['Total Pages', $coverage['total_pages']],
                ['Cached Files', $coverage['cached_files']],
                ['Uncached Count', $coverage['uncached_count']],
                ['Cache Size', $coverage['formatted_size']],
            ];
            WP_CLI\Utils\write_csv(STDOUT, $csv_data);
            return;
        }

        // Default: table format with color-coded coverage
        WP_CLI::line('');
        WP_CLI::line(WP_CLI::colorize('%B=== Coverage Statistics ===%n'));
        WP_CLI::line('');
        
        // Build coverage line with color
        $coverage_color = $coverage['coverage_percent'] >= 80 ? '%G' : ($coverage['coverage_percent'] >= 50 ? '%Y' : '%R');
        $coverage_line = sprintf(
            'Coverage:       %s%s%%%% (%d of %d pages)%%n',
            $coverage_color,
            number_format($coverage['coverage_percent'], 1),
            $coverage['cached_files'],
            $coverage['total_content']
        );
        WP_CLI::line(WP_CLI::colorize($coverage_line));
        
        WP_CLI::line(sprintf('Total Content:  %d (%d posts, %d pages)', 
            $coverage['total_content'],
            $coverage['total_posts'],
            $coverage['total_pages']
        ));
        
        $cached_line = sprintf('Cached Files:   %%G%d%%n', $coverage['cached_files']);
        WP_CLI::line(WP_CLI::colorize($cached_line));
        
        $uncached_color = $coverage['uncached_count'] > 0 ? '%Y' : '%G';
        $uncached_line = sprintf('Uncached:       %s%d%%n', $uncached_color, $coverage['uncached_count']);
        WP_CLI::line(WP_CLI::colorize($uncached_line));
        
        WP_CLI::line(sprintf('Cache Size:     %s', $coverage['formatted_size']));
        WP_CLI::line('');

        if ($coverage['uncached_count'] > 0) {
            WP_CLI::warning(sprintf(
                '%d pages still need caching. Run: wp scw-coverage crawl',
                $coverage['uncached_count']
            ));
        } else {
            WP_CLI::success('100% coverage achieved! All content is cached.');
        }
    }

    /**
     * List uncached posts and pages
     *
     * ## OPTIONS
     *
     * [--limit=<number>]
     * : Maximum number of results to return (0 = all)
     * ---
     * default: 0
     * ---
     *
     * [--format=<format>]
     * : Output format
     * ---
     * default: table
     * options:
     *   - table
     *   - json
     *   - csv
     *   - yaml
     *   - ids
     *   - count
     * ---
     *
     * ## EXAMPLES
     *
     *     # List all uncached content
     *     $ wp scw-coverage list
     *
     *     # Get first 25 uncached pages as CSV
     *     $ wp scw-coverage list --limit=25 --format=csv
     *
     *     # Count uncached pages
     *     $ wp scw-coverage list --format=count
     *
     *     # Get just the IDs for scripting
     *     $ wp scw-coverage list --format=ids
     *
     * @when after_wp_load
     */
    public function list($args, $assoc_args) {
        $limit = isset($assoc_args['limit']) ? absint($assoc_args['limit']) : 0;
        $format = isset($assoc_args['format']) ? $assoc_args['format'] : 'table';

        if (!class_exists('STCWCA_Core')) {
            WP_CLI::error('Coverage Assistant plugin is not active.');
        }

        $uncached = STCWCA_Core::get_uncached_content($limit);

        if (empty($uncached)) {
            WP_CLI::success('All content is cached!');
            return;
        }

        // Count format
        if ($format === 'count') {
            WP_CLI::line(count($uncached));
            return;
        }

        // IDs format
        if ($format === 'ids') {
            $ids = array_column($uncached, 'id');
            WP_CLI::line(implode(' ', $ids));
            return;
        }

        // Other formats
        $formatter = new \WP_CLI\Formatter(
            $assoc_args,
            ['id', 'title', 'type', 'url', 'modified']
        );

        $formatter->display_items($uncached);

        if ($format === 'table') {
            WP_CLI::line('');
            WP_CLI::line(sprintf(
                'Found %s uncached pages. Run: %s',
                WP_CLI::colorize('%Y' . count($uncached) . '%n'),
                WP_CLI::colorize('%Gwp scw-coverage crawl%n')
            ));
        }
    }

    /**
     * Export URLs of uncached content (one per line)
     *
     * Perfect for piping to wget, curl, or other crawlers:
     *     wp scw-coverage urls | xargs -I {} curl -s {} > /dev/null
     *     wp scw-coverage urls | wget -i -
     *
     * ## OPTIONS
     *
     * [--limit=<number>]
     * : Maximum number of URLs to return (0 = all)
     * ---
     * default: 0
     * ---
     *
     * ## EXAMPLES
     *
     *     # Export all uncached URLs
     *     $ wp scw-coverage urls
     *
     *     # Save to file for batch processing
     *     $ wp scw-coverage urls > uncached-urls.txt
     *
     *     # Pipe to wget for immediate caching
     *     $ wp scw-coverage urls | wget -i -
     *
     *     # Pipe to curl with parallel processing
     *     $ wp scw-coverage urls | xargs -P 4 -I {} curl -s {} > /dev/null
     *
     * @when after_wp_load
     */
    public function urls($args, $assoc_args) {
        $limit = isset($assoc_args['limit']) ? absint($assoc_args['limit']) : 0;

        if (!class_exists('STCWCA_Core')) {
            WP_CLI::error('Coverage Assistant plugin is not active.');
        }

        $uncached = STCWCA_Core::get_uncached_content($limit);

        if (empty($uncached)) {
            WP_CLI::error('No uncached URLs found. All content is cached!');
        }

        foreach ($uncached as $item) {
            WP_CLI::line($item['url']);
        }
    }

    /**
     * Automatically crawl and cache all uncached content
     *
     * Visits each uncached URL to trigger static file generation.
     * This is the fastest way to achieve 100% cache coverage.
     *
     * ## OPTIONS
     *
     * [--concurrency=<number>]
     * : Number of concurrent requests (1-10)
     * ---
     * default: 1
     * ---
     *
     * [--delay=<ms>]
     * : Delay between requests in milliseconds
     * ---
     * default: 500
     * ---
     *
     * [--limit=<number>]
     * : Maximum number of pages to cache (0 = all)
     * ---
     * default: 0
     * ---
     *
     * [--verbose]
     * : Show detailed progress for each URL
     *
     * ## EXAMPLES
     *
     *     # Cache all uncached pages (sequential)
     *     $ wp scw-coverage crawl
     *
     *     # Cache with 4 concurrent requests
     *     $ wp scw-coverage crawl --concurrency=4
     *
     *     # Cache first 50 pages with verbose output
     *     $ wp scw-coverage crawl --limit=50 --verbose
     *
     *     # Faster crawl with minimal delay
     *     $ wp scw-coverage crawl --concurrency=4 --delay=100
     *
     * @when after_wp_load
     */
    public function crawl($args, $assoc_args) {
        $concurrency = isset($assoc_args['concurrency']) ? absint($assoc_args['concurrency']) : 1;
        $delay = isset($assoc_args['delay']) ? absint($assoc_args['delay']) : 500;
        $limit = isset($assoc_args['limit']) ? absint($assoc_args['limit']) : 0;
        $verbose = isset($assoc_args['verbose']);

        // Validate concurrency
        $concurrency = max(1, min(10, $concurrency));

        if (!class_exists('STCWCA_Core')) {
            WP_CLI::error('Coverage Assistant plugin is not active.');
        }

        // Check if static generation is enabled
        $is_enabled = false;
        if (defined('STCW_STATIC_ENABLED')) {
            $is_enabled = STCW_STATIC_ENABLED;
        } elseif (class_exists('STCW_Core')) {
            $is_enabled = get_option('stcw_enabled', false);
        }
        
        if (!$is_enabled) {
            WP_CLI::error('Static generation is not enabled. Enable it with: wp scw enable');
        }

        $uncached = STCWCA_Core::get_uncached_content($limit);

        if (empty($uncached)) {
            WP_CLI::success('All content is already cached!');
            return;
        }

        $total = count($uncached);
        WP_CLI::line('');
        WP_CLI::line(sprintf(
            'Found %s uncached pages to process...',
            WP_CLI::colorize('%Y' . $total . '%n')
        ));
        WP_CLI::line(sprintf('Concurrency: %d | Delay: %dms', $concurrency, $delay));
        WP_CLI::line('');

        $progress = \WP_CLI\Utils\make_progress_bar('Caching pages', $total);

        $success_count = 0;
        $error_count = 0;
        $errors = [];

        // Process URLs using shared crawler
        foreach (array_chunk($uncached, $concurrency) as $chunk) {
            foreach ($chunk as $item) {
                $url = $item['url'];
                $title = $item['title'];

                // Use shared crawler method
                $result = STCWCA_Crawler::process_single_url($url);

                if ($result['success']) {
                    $success_count++;
                    
                    if ($verbose) {
                        WP_CLI::log(sprintf(
                            'Cached: %s (%s)',
                            WP_CLI::colorize('%G' . $title . '%n'),
                            $url
                        ));
                    }
                } else {
                    $error_count++;
                    $errors[] = [
                        'url' => $url,
                        'title' => $title,
                        'error' => $result['error'],
                    ];
                    
                    if ($verbose) {
                        WP_CLI::warning(sprintf(
                            'Failed: %s - %s',
                            $title,
                            $result['error']
                        ));
                    }
                }

                $progress->tick();
            }

            // Delay between concurrent batches
            if ($delay > 0) {
                usleep($delay * 1000);
            }
        }

        $progress->finish();

        WP_CLI::line('');
        WP_CLI::line(WP_CLI::colorize('%B=== Crawl Complete ===%n'));
        WP_CLI::line('');
        WP_CLI::line(sprintf('Successfully cached: %s', WP_CLI::colorize('%G' . $success_count . '%n')));
        
        if ($error_count > 0) {
            WP_CLI::line(sprintf('Errors:              %s', WP_CLI::colorize('%R' . $error_count . '%n')));
        }

        WP_CLI::line('');

        // Show error details if any
        if (!empty($errors) && $error_count <= 10) {
            WP_CLI::line(WP_CLI::colorize('%RErrors:%n'));
            foreach ($errors as $error) {
                WP_CLI::line(sprintf(
                    '  • %s - %s',
                    $error['title'],
                    $error['error']
                ));
            }
            WP_CLI::line('');
        } elseif ($error_count > 10) {
            WP_CLI::line(sprintf(
                '%s errors occurred. Run with --verbose to see details.',
                $error_count
            ));
            WP_CLI::line('');
        }

        // Final coverage report
        $coverage = STCWCA_Core::get_coverage_data();
        WP_CLI::line(sprintf(
            'Current coverage: %s%%',
            WP_CLI::colorize(
                ($coverage['coverage_percent'] >= 80 ? '%G' : '%Y') . 
                number_format($coverage['coverage_percent'], 1) . 
                '%n'
            )
        ));

        if ($coverage['uncached_count'] > 0) {
            WP_CLI::line(sprintf(
                '%d pages still uncached. Run again to continue.',
                $coverage['uncached_count']
            ));
        } else {
            WP_CLI::success('🎉 100% coverage achieved! All content is now cached.');
        }
    }
}

// Register CLI commands under 'wp scw-coverage' namespace
WP_CLI::add_command('scw-coverage stats', ['STCWCA_CLI', 'stats']);
WP_CLI::add_command('scw-coverage list', ['STCWCA_CLI', 'list']);
WP_CLI::add_command('scw-coverage urls', ['STCWCA_CLI', 'urls']);
WP_CLI::add_command('scw-coverage crawl', ['STCWCA_CLI', 'crawl']);
