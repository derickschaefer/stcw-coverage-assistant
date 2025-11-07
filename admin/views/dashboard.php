<?php
/**
 * Coverage Analytics Dashboard Template (No Chart.js)
 *
 * @package STCWCoverageAnalytics
 * @since 1.1.0
 */

if (!defined('ABSPATH')) exit;

// Optional success messages
$messages = [
    'trend-refreshed' => __('Coverage trend data refreshed.', 'stcw-coverage-analytics'),
];

$message_key = isset($_GET['message']) ? sanitize_key(wp_unslash($_GET['message'])) : '';
?>
<div class="wrap">
    <h1><?php esc_html_e('Coverage Analytics', 'stcw-coverage-analytics'); ?></h1>

    <?php if ($message_key && isset($messages[$message_key])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html($messages[$message_key]); ?></p>
        </div>
    <?php endif; ?>

    <!-- Coverage Summary Cards -->
    <div class="stcwca-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-top:20px;">

        <div class="stcwca-card">
            <h3><?php esc_html_e('Coverage', 'stcw-coverage-analytics'); ?></h3>
            <div class="stcwca-value <?php echo ($coverage['coverage_percent'] >= 80) ? 'stcwca-good' : (($coverage['coverage_percent'] >= 50) ? 'stcwca-warning' : 'stcwca-bad'); ?>">
                <?php echo esc_html(number_format_i18n($coverage['coverage_percent'], 1)); ?>%
            </div>
            <div class="stcwca-label">
                <?php
                printf(
                    esc_html__('%1$s of %2$s pages', 'stcw-coverage-analytics'),
                    esc_html(number_format_i18n($coverage['cached_files'])),
                    esc_html(number_format_i18n($coverage['total_content']))
                );
                ?>
            </div>
        </div>

        <div class="stcwca-card">
            <h3><?php esc_html_e('Total Content', 'stcw-coverage-analytics'); ?></h3>
            <div class="stcwca-value">
                <?php echo esc_html(number_format_i18n($coverage['total_content'])); ?>
            </div>
            <div class="stcwca-label">
                <?php
                printf(
                    esc_html__('%1$s posts, %2$s pages', 'stcw-coverage-analytics'),
                    esc_html(number_format_i18n($coverage['total_posts'])),
                    esc_html(number_format_i18n($coverage['total_pages']))
                );
                ?>
            </div>
        </div>

        <div class="stcwca-card">
            <h3><?php esc_html_e('Uncached', 'stcw-coverage-analytics'); ?></h3>
            <div class="stcwca-value <?php echo ($coverage['uncached_count'] > 0) ? 'stcwca-warning' : 'stcwca-good'; ?>">
                <?php echo esc_html(number_format_i18n($coverage['uncached_count'])); ?>
            </div>
            <div class="stcwca-label">
                <?php esc_html_e('Pages need caching', 'stcw-coverage-analytics'); ?>
            </div>
        </div>

        <div class="stcwca-card">
            <h3><?php esc_html_e('Cache Size', 'stcw-coverage-analytics'); ?></h3>
            <div class="stcwca-value">
                <?php echo esc_html($coverage['formatted_size']); ?>
            </div>
            <div class="stcwca-label">
                <?php esc_html_e('Static files footprint', 'stcw-coverage-analytics'); ?>
            </div>
        </div>
    </div>

    <div class="stcwca-layout" style="display:flex;gap:20px;align-items:flex-start;margin-top:20px;">

        <!-- Main Column -->
        <div style="flex:1;min-width:0;">

            <?php if (!empty($uncached)): ?>
            <!-- Uncached Content Panel -->
            <div class="stcwca-panel stcwca-card">
                <h2 class="stcwca-panel-title">
                    <?php esc_html_e('Uncached Content', 'stcw-coverage-analytics'); ?>
                    <span class="stcwca-count-badge"><?php echo esc_html(number_format_i18n(count($uncached))); ?></span>
                </h2>
                <p><?php esc_html_e('These posts and pages have not been cached yet. Copy their links for manual caching or automation.', 'stcw-coverage-analytics'); ?></p>

                <table class="widefat" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Title', 'stcw-coverage-analytics'); ?></th>
                            <th><?php esc_html_e('Type', 'stcw-coverage-analytics'); ?></th>
                            <th><?php esc_html_e('Last Modified', 'stcw-coverage-analytics'); ?></th>
                            <th><?php esc_html_e('Actions', 'stcw-coverage-analytics'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($uncached as $item): ?>
                        <tr>
                            <td><strong><?php echo esc_html($item['title']); ?></strong></td>
                            <td>
                                <span class="stcwca-type-badge stcwca-type-<?php echo esc_attr($item['type']); ?>">
                                    <?php echo esc_html(ucfirst($item['type'])); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(gmdate('Y-m-d H:i', strtotime($item['modified']))); ?></td>
                            <td>
                                <button class="button button-small copy-link-button" data-url="<?php echo esc_url($item['url']); ?>">
                                    <?php esc_html_e('Copy Link', 'stcw-coverage-analytics'); ?>
                                </button>
                                <a href="<?php echo esc_url(get_edit_post_link($item['id'])); ?>" class="button button-small">
                                    <?php esc_html_e('Edit', 'stcw-coverage-analytics'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (count($uncached) >= 25): ?>
                <p style="margin-top:10px;color:#666;">
                    <?php esc_html_e('Showing first 25 uncached pages. More may exist.', 'stcw-coverage-analytics'); ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($cached)): ?>
            <!-- Recently Cached Panel -->
            <div class="stcwca-panel stcwca-card">
                <h2 class="stcwca-panel-title"><?php esc_html_e('Recently Cached Content', 'stcw-coverage-analytics'); ?></h2>
                <table class="widefat" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Title', 'stcw-coverage-analytics'); ?></th>
                            <th><?php esc_html_e('Type', 'stcw-coverage-analytics'); ?></th>
                            <th><?php esc_html_e('Cached', 'stcw-coverage-analytics'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cached as $item): ?>
                        <tr>
                            <td><strong><?php echo esc_html($item['title']); ?></strong></td>
                            <td>
                                <span class="stcwca-type-badge stcwca-type-<?php echo esc_attr($item['type']); ?>">
                                    <?php echo esc_html(ucfirst($item['type'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo esc_html(human_time_diff($item['cached_time'], time())); ?>
                                <?php esc_html_e('ago', 'stcw-coverage-analytics'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div style="width:320px;flex:0 0 320px;">

            <div class="stcwca-card">
                <h2 class="stcwca-panel-title"><?php esc_html_e('Quick Actions', 'stcw-coverage-analytics'); ?></h2>
                <a href="<?php echo esc_url(admin_url('admin.php?page=static-cache-wrangler')); ?>"
                   class="button button-primary button-large"
                   style="width:100%;text-align:center;margin-bottom:10px;">
                    <span class="dashicons dashicons-admin-settings" style="margin-top:3px;"></span>
                    <?php esc_html_e('Static Cache Settings', 'stcw-coverage-analytics'); ?>
                </a>
            </div>

            <div class="stcwca-card">
                <h2 class="stcwca-panel-title"><?php esc_html_e('About Coverage Analytics', 'stcw-coverage-analytics'); ?></h2>
                <p style="font-size:13px;line-height:1.6;">
                    <?php esc_html_e('This plugin monitors which posts and pages have been cached by Static Cache Wrangler.', 'stcw-coverage-analytics'); ?>
                </p>
                <p style="font-size:13px;line-height:1.6;margin-top:10px;">
                    <?php esc_html_e('To increase coverage, copy and crawl the uncached links while static generation is enabled.', 'stcw-coverage-analytics'); ?>
                </p>
            </div>

            <div class="stcwca-card">
                <h2 class="stcwca-panel-title"><?php esc_html_e('Tips', 'stcw-coverage-analytics'); ?></h2>
                <ul style="font-size:13px;line-height:1.8;margin-left:20px;">
                    <li><?php esc_html_e('Copy uncached links for automated crawling', 'stcw-coverage-analytics'); ?></li>
                    <li><?php esc_html_e('Visit uncached pages to generate static files', 'stcw-coverage-analytics'); ?></li>
                    <li><?php esc_html_e('Check this dashboard regularly to track progress', 'stcw-coverage-analytics'); ?></li>
                    <li><?php esc_html_e('100% coverage ensures complete offline functionality', 'stcw-coverage-analytics'); ?></li>
                </ul>
            </div>

        </div>
    </div>
</div>
