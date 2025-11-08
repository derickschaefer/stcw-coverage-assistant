<?php
/**
 * Coverage Assistant Dashboard Template (with CLI Commands)
 *
 * @package STCWCoverageAssistant
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Optional success messages
$stcwca_messages = [
    'trend-refreshed' => __('Coverage trend data refreshed.', 'stcw-coverage-assistant'),
];

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display of success message, no data processing
$stcwca_message_key = isset($_GET['message']) ? sanitize_key(wp_unslash($_GET['message'])) : '';
?>
<div class="wrap">
    <h1><?php esc_html_e('Coverage Assistant', 'stcw-coverage-assistant'); ?></h1>

    <?php if ($stcwca_message_key && isset($stcwca_messages[$stcwca_message_key])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html($stcwca_messages[$stcwca_message_key]); ?></p>
        </div>
    <?php endif; ?>

    <!-- Coverage Summary Cards -->
    <div class="stcwca-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-top:20px;">

        <div class="stcwca-card">
            <h3><?php esc_html_e('Coverage', 'stcw-coverage-assistant'); ?></h3>
            <div class="stcwca-value <?php echo ($coverage['coverage_percent'] >= 80) ? 'stcwca-good' : (($coverage['coverage_percent'] >= 50) ? 'stcwca-warning' : 'stcwca-bad'); ?>">
                <?php echo esc_html(number_format_i18n($coverage['coverage_percent'], 1)); ?>%
            </div>
            <div class="stcwca-label">
		<?php
		printf(
		    /* translators: 1: Number of cached pages, 2: Total number of pages */
                    esc_html__('%1$s of %2$s pages', 'stcw-coverage-assistant'),
                    esc_html(number_format_i18n($coverage['cached_files'])),
                    esc_html(number_format_i18n($coverage['total_content']))
                );
                ?>
            </div>
        </div>

        <div class="stcwca-card">
            <h3><?php esc_html_e('Total Content', 'stcw-coverage-assistant'); ?></h3>
            <div class="stcwca-value">
                <?php echo esc_html(number_format_i18n($coverage['total_content'])); ?>
            </div>
            <div class="stcwca-label">
		<?php
		printf(
		    /* translators: 1: Number of posts, 2: Number of pages */
                    esc_html__('%1$s posts, %2$s pages', 'stcw-coverage-assistant'),
                    esc_html(number_format_i18n($coverage['total_posts'])),
                    esc_html(number_format_i18n($coverage['total_pages']))
                );
                ?>
            </div>
        </div>

        <div class="stcwca-card">
            <h3><?php esc_html_e('Uncached', 'stcw-coverage-assistant'); ?></h3>
            <div class="stcwca-value <?php echo ($coverage['uncached_count'] > 0) ? 'stcwca-warning' : 'stcwca-good'; ?>">
                <?php echo esc_html(number_format_i18n($coverage['uncached_count'])); ?>
            </div>
            <div class="stcwca-label">
                <?php esc_html_e('Pages need caching', 'stcw-coverage-assistant'); ?>
            </div>
        </div>

        <div class="stcwca-card">
            <h3><?php esc_html_e('Cache Size', 'stcw-coverage-assistant'); ?></h3>
            <div class="stcwca-value">
                <?php echo esc_html($coverage['formatted_size']); ?>
            </div>
            <div class="stcwca-label">
                <?php esc_html_e('Static files footprint', 'stcw-coverage-assistant'); ?>
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
                    <?php esc_html_e('Uncached Content', 'stcw-coverage-assistant'); ?>
                    <span class="stcwca-count-badge"><?php echo esc_html(number_format_i18n(count($uncached))); ?></span>
                </h2>
                <p><?php esc_html_e('These posts and pages have not been cached yet. Copy their links for manual caching or automation.', 'stcw-coverage-assistant'); ?></p>

                <table class="widefat" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Title', 'stcw-coverage-assistant'); ?></th>
                            <th><?php esc_html_e('Type', 'stcw-coverage-assistant'); ?></th>
                            <th><?php esc_html_e('Last Modified', 'stcw-coverage-assistant'); ?></th>
                            <th style="width: 120px;"><?php esc_html_e('Actions', 'stcw-coverage-assistant'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($uncached as $stcwca_item): ?>
                        <tr>
                            <td><strong><?php echo esc_html($stcwca_item['title']); ?></strong></td>
                            <td>
                                <span class="stcwca-type-badge stcwca-type-<?php echo esc_attr($stcwca_item['type']); ?>">
                                    <?php echo esc_html(ucfirst($stcwca_item['type'])); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(gmdate('Y-m-d H:i', strtotime($stcwca_item['modified']))); ?></td>
                            <td>
                                <button class="button button-small copy-link-button" data-url="<?php echo esc_url($stcwca_item['url']); ?>" title="<?php esc_attr_e('Copy URL to clipboard', 'stcw-coverage-assistant'); ?>">
                                    <span class="dashicons dashicons-admin-links"></span>
                                    <?php esc_html_e('Copy Link', 'stcw-coverage-assistant'); ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (count($uncached) >= 25): ?>
                <p style="margin-top:10px;color:#666;">
                    <?php esc_html_e('Showing first 25 uncached pages. More may exist.', 'stcw-coverage-assistant'); ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($cached)): ?>
            <!-- Recently Cached Panel -->
            <div class="stcwca-panel stcwca-card">
                <h2 class="stcwca-panel-title"><?php esc_html_e('Recently Cached Content', 'stcw-coverage-assistant'); ?></h2>
                <table class="widefat" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Title', 'stcw-coverage-assistant'); ?></th>
                            <th><?php esc_html_e('Type', 'stcw-coverage-assistant'); ?></th>
                            <th><?php esc_html_e('Cached', 'stcw-coverage-assistant'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cached as $stcwca_item): ?>
                        <tr>
                            <td><strong><?php echo esc_html($stcwca_item['title']); ?></strong></td>
                            <td>
                                <span class="stcwca-type-badge stcwca-type-<?php echo esc_attr($stcwca_item['type']); ?>">
                                    <?php echo esc_html(ucfirst($stcwca_item['type'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo esc_html(human_time_diff($stcwca_item['cached_time'], time())); ?>
                                <?php esc_html_e('ago', 'stcw-coverage-assistant'); ?>
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
                <h2 class="stcwca-panel-title"><?php esc_html_e('Quick Actions', 'stcw-coverage-assistant'); ?></h2>
                <a href="<?php echo esc_url(admin_url('admin.php?page=static-cache-wrangler')); ?>"
                   class="button button-primary button-large"
                   style="width:100%;text-align:center;margin-bottom:10px;">
                    <span class="dashicons dashicons-admin-settings" style="margin-top:3px;"></span>
                    <?php esc_html_e('Static Cache Settings', 'stcw-coverage-assistant'); ?>
                </a>
            </div>

            <div class="stcwca-card">
                <h2 class="stcwca-panel-title"><?php esc_html_e('About Coverage Assistant', 'stcw-coverage-assistant'); ?></h2>
                <p style="font-size:13px;line-height:1.6;">
                    <?php esc_html_e('This plugin monitors which posts and pages have been cached by Static Cache Wrangler.', 'stcw-coverage-assistant'); ?>
                </p>
                <p style="font-size:13px;line-height:1.6;margin-top:10px;">
                    <?php esc_html_e('To increase coverage, copy and crawl the uncached links while static generation is enabled.', 'stcw-coverage-assistant'); ?>
                </p>
            </div>

            <div class="stcwca-card">
                <h2 class="stcwca-panel-title"><?php esc_html_e('WP-CLI Commands', 'stcw-coverage-assistant'); ?></h2>
                <p style="font-size:13px;line-height:1.6;margin-bottom:12px;">
                    <?php esc_html_e('Automate cache monitoring and generation via command line:', 'stcw-coverage-assistant'); ?>
                </p>
                <div style="background:#f6f7f7;border-left:3px solid #2271b1;padding:12px;margin-bottom:12px;border-radius:3px;">
                    <code style="font-size:12px;display:block;margin-bottom:6px;color:#1d2327;">
                        <strong>wp scw coverage</strong>
                    </code>
                    <p style="font-size:12px;color:#646970;margin:0 0 8px 0;line-height:1.4;">
                        <?php esc_html_e('Show coverage statistics', 'stcw-coverage-assistant'); ?>
                    </p>
                    
                    <code style="font-size:12px;display:block;margin-bottom:6px;color:#1d2327;">
                        <strong>wp scw uncached</strong>
                    </code>
                    <p style="font-size:12px;color:#646970;margin:0 0 8px 0;line-height:1.4;">
                        <?php esc_html_e('List all uncached pages', 'stcw-coverage-assistant'); ?>
                    </p>
                    
                    <code style="font-size:12px;display:block;margin-bottom:6px;color:#1d2327;">
                        <strong>wp scw uncached-urls</strong>
                    </code>
                    <p style="font-size:12px;color:#646970;margin:0 0 8px 0;line-height:1.4;">
                        <?php esc_html_e('Export URLs for automation', 'stcw-coverage-assistant'); ?>
                    </p>
                    
                    <code style="font-size:12px;display:block;margin-bottom:6px;color:#1d2327;">
                        <strong>wp scw crawl-uncached</strong>
                    </code>
                    <p style="font-size:12px;color:#646970;margin:0;line-height:1.4;">
                        <?php esc_html_e('Auto-cache all uncached pages', 'stcw-coverage-assistant'); ?>
                    </p>
                </div>
                <p style="font-size:12px;color:#646970;line-height:1.6;margin:0;">
                    <?php esc_html_e('Use --help flag for full command options and examples.', 'stcw-coverage-assistant'); ?>
                </p>
            </div>

        </div>
    </div>
</div>
