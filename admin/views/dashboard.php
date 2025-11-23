<?php
/**
 * Coverage Assistant Dashboard Template
 *
 * @package STCWCoverageAssistant
 * @since 1.0.6
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get threshold data
$stcwca_threshold = STCWCA_Core::get_gui_crawler_threshold();
$stcwca_is_available = STCWCA_Core::is_gui_crawler_available();
$stcwca_pages_to_unlock = STCWCA_Core::get_pages_to_unlock();

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
		    /* translators: 1: Number of cached URLs, 2: Total number of URLs */
                    esc_html__('%1$s of %2$s URLs cached', 'stcw-coverage-assistant'),
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
                <?php esc_html_e('URLs pending cache', 'stcw-coverage-assistant'); ?>
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
            
            <?php if ($coverage['uncached_count'] > 0): ?>
            
            <!-- GUI Crawler Card (Moved Below Uncached Content) -->
            <div class="stcwca-card stcwca-crawler-card">
                <h2 class="stcwca-panel-title">
                    <?php esc_html_e('TRIGGER CACHING', 'stcw-coverage-assistant'); ?>
                </h2>
                
                <p style="margin: 15px 0; font-size: 14px;">
                    <strong>
                        <?php
                        printf(
                            /* translators: %s: Current coverage percentage */
                            esc_html__('Current Cached Content Coverage: %s%%', 'stcw-coverage-assistant'),
                            esc_html(number_format($coverage['coverage_percent'], 1))
                        );
                        ?>
                    </strong>
                </p>
                
                <?php if (!$stcwca_is_available): ?>
                    <!-- LOCKED STATE: Below Threshold -->
                    
                    <h3 style="font-size: 15px; font-weight: 600; margin: 20px 0 10px 0;">
                        <?php esc_html_e('HOW TO COMPLETE YOUR STATIC FILES:', 'stcw-coverage-assistant'); ?>
                    </h3>
                    
                    <div style="margin: 15px 0;">
                        <p style="margin-bottom: 8px;">
                            <strong><?php esc_html_e('Option 1 - Browse your site naturally (small sites)', 'stcw-coverage-assistant'); ?></strong>
                        </p>
                        <p style="color: #666; font-size: 13px; margin: 0 0 15px 0; line-height: 1.6;">
                            <?php esc_html_e('Visit URLs while logged out. Each visit generates a static cache file.', 'stcw-coverage-assistant'); ?>
                        </p>
                        
                        <p style="margin-bottom: 8px;">
                            <strong><?php esc_html_e('Option 2 - Copy specific URLs from Uncached Content Report', 'stcw-coverage-assistant'); ?></strong>
                        </p>
                        <p style="color: #666; font-size: 13px; margin: 0 0 15px 0; line-height: 1.6;">
                            <?php esc_html_e('Paste URLs one at a time into a browser to generate static cache for the page', 'stcw-coverage-assistant'); ?>
                        </p>
                        
                        <p style="margin-bottom: 8px;">
                            <strong>
                                <?php esc_html_e('Option 3 - Use WP-CLI for bulk caching', 'stcw-coverage-assistant'); ?>
                            </strong>
                            <span style="color: #2271b1; font-size: 13px;"><?php esc_html_e('(Recommended for Admins)', 'stcw-coverage-assistant'); ?></span>
                        </p>
                        <p style="margin: 0 0 8px 0;">
                            <code style="background: #f0f0f0; padding: 6px 10px; border-radius: 3px; font-size: 13px; display: inline-block;">
                                wp scw crawl-uncached --concurrency=4
                            </code>
                        </p>
                        
                        <p style="margin-bottom: 8px;">
                            <strong><?php esc_html_e('Option 4 - Trigger Automated Crawling from this Page', 'stcw-coverage-assistant'); ?></strong>
                        </p>
                    </div>
                    
                    <div class="notice notice-info inline" style="margin: 20px 0;">
                        <p style="line-height: 1.6;">
                            <?php
                            printf(
                                /* translators: %d: Threshold percentage */
                                esc_html__('GUI triggered crawling is designed to allow a user to finish up a complete static cache of their website by processing those less frequently visited URLs in batch. In doing so, it tries to be respectful of server resources. The GUI crawler becomes available at %d%% of the site has been processed naturally by Static Cache Wrangler OR one of the 3 options above. This is configurable via wp-config.php but 100%% crawls ARE NOT recommended. Your current cache progress is', 'stcw-coverage-assistant'),
                                absint($stcwca_threshold)
                            );
                            ?>
                        </p>
                    </div>
                    
                    <!-- Progress Bar to Threshold -->
                    <div class="stcwca-progress-to-threshold" style="margin: 20px 0;">
                        <div style="background: #f0f0f0; height: 32px; border-radius: 4px; overflow: hidden; position: relative; border: 1px solid #ddd;">
                            <?php $stcwca_progress_width = min(100, ($coverage['coverage_percent'] / $stcwca_threshold) * 100); ?>
                            <div style="background: linear-gradient(90deg, #2271b1 0%, #135e96 100%); 
                                        height: 100%; 
                                        width: <?php echo esc_attr($stcwca_progress_width); ?>%; 
                                        transition: width 0.4s ease;">
                            </div>
                            <span style="position: absolute; 
                                         left: 50%; 
                                         top: 50%; 
                                         transform: translate(-50%, -50%);
                                         font-weight: 600;
                                         font-size: 13px;
                                         color: <?php echo $coverage['coverage_percent'] > 30 ? '#fff' : '#333'; ?>;">
                                <?php echo esc_html(number_format($coverage['coverage_percent'], 1)); ?>% / <?php echo absint($stcwca_threshold); ?>%
                            </span>
                        </div>
                        <p style="text-align: center; margin-top: 10px; color: #666; font-size: 13px;">
                            <?php
                            if ($stcwca_pages_to_unlock > 0) {
                                printf(
                                    /* translators: %d: Number of pages needed */
                                    esc_html(_n(
                                        'Need %d more URL cached to unlock GUI crawler',
                                        'Need %d more URLs cached to unlock GUI crawler',
                                        $stcwca_pages_to_unlock,
                                        'stcw-coverage-assistant'
                                    )),
                                    absint($stcwca_pages_to_unlock)
                                );
                            } else {
                                esc_html_e('Almost there!', 'stcw-coverage-assistant');
                            }
                            ?>
                        </p>
                    </div>
                    
                <?php else: ?>
                    <!-- AVAILABLE STATE: Above Threshold -->
                    
                    <h3 style="font-size: 15px; font-weight: 600; margin: 20px 0 10px 0;">
                        <?php esc_html_e('HOW TO COMPLETE YOUR STATIC FILES:', 'stcw-coverage-assistant'); ?>
                    </h3>
                    
                    <div style="margin: 15px 0;">
                        <p style="margin-bottom: 8px;">
                            <strong><?php esc_html_e('Option 1 - Browse your site naturally (small sites)', 'stcw-coverage-assistant'); ?></strong>
                        </p>
                        <p style="color: #666; font-size: 13px; margin: 0 0 15px 0; line-height: 1.6;">
                            <?php esc_html_e('Visit URLs while logged out. Each visit generates a static cache file.', 'stcw-coverage-assistant'); ?>
                        </p>
                        
                        <p style="margin-bottom: 8px;">
                            <strong><?php esc_html_e('Option 2 - Copy specific URLs from Uncached Content Report', 'stcw-coverage-assistant'); ?></strong>
                        </p>
                        <p style="color: #666; font-size: 13px; margin: 0 0 15px 0; line-height: 1.6;">
                            <?php esc_html_e('Paste URLs one at a time into a browser to generate static cache for the page', 'stcw-coverage-assistant'); ?>
                        </p>
                        
                        <p style="margin-bottom: 8px;">
                            <strong>
                                <?php esc_html_e('Option 3 - Use WP-CLI for bulk caching', 'stcw-coverage-assistant'); ?>
                            </strong>
                            <span style="color: #2271b1; font-size: 13px;"><?php esc_html_e('(Recommended for Admins)', 'stcw-coverage-assistant'); ?></span>
                        </p>
                        <p style="margin: 0 0 8px 0;">
                            <code style="background: #f0f0f0; padding: 6px 10px; border-radius: 3px; font-size: 13px; display: inline-block;">
                                wp scw crawl-uncached --concurrency=4
                            </code>
                        </p>
                        
                        <p style="margin-bottom: 8px;">
                            <strong><?php esc_html_e('Option 4 - Trigger Automated Crawling from this Page', 'stcw-coverage-assistant'); ?></strong>
                        </p>
                    </div>
                    
                    <?php if ($coverage['uncached_count'] > 500): ?>
                        <!-- Warning for Large Batches -->
                        <div class="notice notice-warning inline" style="margin: 15px 0;">
                            <p>
                                <strong><?php esc_html_e('⚠️ Large Batch Detected', 'stcw-coverage-assistant'); ?></strong><br>
                                <?php
                                $stcwca_estimate_large = STCWCA_Crawler::estimate_crawl_time($coverage['uncached_count']);
                                printf(
                                    /* translators: 1: Number of uncached pages, 2: Estimated time */
                                    esc_html__('You have %1$s uncached pages (~%2$s in browser).', 'stcw-coverage-assistant'),
                                    esc_html(number_format_i18n($coverage['uncached_count'])),
                                    esc_html($stcwca_estimate_large['formatted'])
                                );
                                ?><br>
                                <strong><?php esc_html_e('Recommended:', 'stcw-coverage-assistant'); ?></strong> 
                                <?php esc_html_e('Use WP-CLI Option 3 instead for better performance.', 'stcw-coverage-assistant'); ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="notice notice-info inline" style="margin: 15px 0;">
                            <p style="line-height: 1.6;">
                                <?php
                                printf(
                                    /* translators: 1: Coverage percentage, 2: Threshold percentage */
                                    esc_html__('GUI triggered crawling is designed to allow a user to finish up a complete static cache of their website by processing those less frequently visited URLs in batch. In doing so, it tries to be respectful of server resources. The GUI crawler becomes available at %d%% of the site has been processed naturally by Static Cache Wrangler OR one of the 3 options above. This is configurable via wp-config.php but 100%% crawls ARE NOT recommended. Your current cache progress is', 'stcw-coverage-assistant'),
                                    absint($stcwca_threshold)
                                );
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Crawler UI -->
                    <div id="stcwca-crawl-control">
                        
                        <!-- Initial State: Ready to Start -->
                        <div id="stcwca-crawl-initial">
                            <p style="margin: 20px 0 15px 0; font-weight: 600;">
                                <?php
				printf(
				    /* translators: 1: Coverage percentage, 2: Threshold percentage */
				    esc_html__('%1$s%%%% / %2$d%%%%', 'stcw-coverage-assistant'),
                                    esc_html(number_format($coverage['coverage_percent'], 1)),
                                    absint($stcwca_threshold)
                                );
                                ?>
                            </p>
                            
                            <p style="margin-bottom: 15px; color: #666; font-size: 13px; line-height: 1.6;">
				<?php
					if ($stcwca_pages_to_unlock > 0) {
					    printf(
						/* translators: %d: Number of pages needed */
        					esc_html(_n(
            					'Need %d more URL cached to unlock GUI crawler',
            					'Need %d more URLs cached to unlock GUI crawler',
            				$stcwca_pages_to_unlock,
            				'stcw-coverage-assistant'
        			)),
        			absint($stcwca_pages_to_unlock)
    			);
		}
                                ?>
                            </p>
                            
                            <button class="button button-primary button-large" id="stcwca-start-crawl" style="margin-top: 10px;">
                                <?php
                                printf(
                                    /* translators: %d: Number of pages to cache */
                                    esc_html__('Start Caching %d URLs', 'stcw-coverage-assistant'),
                                    absint($coverage['uncached_count'])
                                );
                                ?>
                            </button>
                            
                            <p class="description" style="margin-top: 12px; line-height: 1.6;">
                                <?php
                                $stcwca_estimate = STCWCA_Crawler::estimate_crawl_time($coverage['uncached_count']);
                                printf(
                                    /* translators: %s: Estimated time */
                                    esc_html__('Estimated time: %s', 'stcw-coverage-assistant'),
                                    '<strong>' . esc_html($stcwca_estimate['formatted']) . '</strong>'
                                );
                                ?><br>
                                <span style="color: #d63638;">⚠️ <?php esc_html_e('Keep this browser tab open during caching.', 'stcw-coverage-assistant'); ?></span>
                            </p>
                        </div>
                        
                        <!-- Progress State: Crawling in Progress -->
                        <div id="stcwca-crawl-progress" style="display: none;">
                            <div class="stcwca-progress-bar-wrapper" style="margin: 20px 0;">
                                <div class="stcwca-progress-bar" style="background: #f0f0f0; height: 40px; border-radius: 6px; overflow: hidden; position: relative; border: 1px solid #ddd;">
                                    <div class="stcwca-progress-fill" style="background: linear-gradient(90deg, #2271b1 0%, #135e96 100%); height: 100%; width: 0%; transition: width 0.3s ease;"></div>
                                    <div style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); font-weight: 600; font-size: 14px; z-index: 1;">
                                        <span id="stcwca-progress-current">0</span> / 
                                        <span id="stcwca-progress-total">0</span> 
                                        (<span id="stcwca-progress-percent">0</span>%)
                                    </div>
                                </div>
                            </div>
                            
                            <div class="stcwca-crawl-stats" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 4px; border: 1px solid #ddd;">
                                <div>
                                    <div style="font-size: 12px; color: #666; margin-bottom: 4px;">⚡ <?php esc_html_e('Current Speed', 'stcw-coverage-assistant'); ?></div>
                                    <div style="font-size: 16px; font-weight: 600;"><span id="stcwca-speed">1000</span>ms <?php esc_html_e('delay', 'stcw-coverage-assistant'); ?></div>
                                    <div style="font-size: 11px; color: #666;"><?php esc_html_e('(adaptive)', 'stcw-coverage-assistant'); ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; color: #666; margin-bottom: 4px;">📊 <?php esc_html_e('Results', 'stcw-coverage-assistant'); ?></div>
                                    <div style="font-size: 14px;">
                                        ✅ <span id="stcwca-success" style="color: #46b450; font-weight: 600;">0</span> &nbsp;
                                        ❌ <span id="stcwca-errors" style="color: #dc3232; font-weight: 600;">0</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="margin: 20px 0;">
                                <button class="button" id="stcwca-stop-crawl">
                                    ⏹️ <?php esc_html_e('Stop Crawling', 'stcw-coverage-assistant'); ?>
                                </button>
                            </div>
                            
                            <div class="notice notice-info inline" style="margin-top: 15px;">
                                <p style="margin: 8px 0;">
                                    💡 <strong><?php esc_html_e('Keep this tab open', 'stcw-coverage-assistant'); ?></strong> 
                                    <?php esc_html_e('until crawling completes. Closing the tab will stop the process.', 'stcw-coverage-assistant'); ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Complete State: Success -->
                        <div id="stcwca-crawl-complete" style="display: none;">
                            <div class="notice notice-success inline">
                                <p style="font-size: 15px; margin: 10px 0;">
                                    🎉 <strong><?php esc_html_e('Crawl Complete!', 'stcw-coverage-assistant'); ?></strong><br>
                                    <?php esc_html_e('Successfully cached', 'stcw-coverage-assistant'); ?> 
                                    <span id="stcwca-final-success">0</span> 
                                    <?php esc_html_e('URLs.', 'stcw-coverage-assistant'); ?>
                                    <span id="stcwca-final-errors-wrapper" style="display: none;">
                                        <br>⚠️ <span id="stcwca-final-errors">0</span> 
                                        <?php esc_html_e('URLs failed.', 'stcw-coverage-assistant'); ?>
                                    </span>
                                </p>
                            </div>
                            <button class="button button-primary" onclick="location.reload()" style="margin-top: 10px;">
                                🔄 <?php esc_html_e('Refresh Dashboard', 'stcw-coverage-assistant'); ?>
                            </button>
                        </div>
                        
                    </div>
                    
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
                    <?php esc_html_e('This free companion plugin monitors which posts and pages have been cached by Static Cache Wrangler.', 'stcw-coverage-assistant'); ?>
                </p>
                <p style="font-size:13px;line-height:1.6;margin-top:10px;">
                    <?php esc_html_e('To increase coverage, copy uncached URLs and paste into an unauthenticated browser or use the CLI while static generation is enabled.', 'stcw-coverage-assistant'); ?>
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
                        <?php esc_html_e('List all uncached URLs', 'stcw-coverage-assistant'); ?>
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
                        <?php esc_html_e('Auto-cache all uncached URLs', 'stcw-coverage-assistant'); ?>
                    </p>
                </div>
                <p style="font-size:12px;color:#646970;line-height:1.6;margin:0;">
                    <?php esc_html_e('Use --help flag for full command options and examples.', 'stcw-coverage-assistant'); ?>
                </p>
            </div>

        </div>
    </div>
</div>
