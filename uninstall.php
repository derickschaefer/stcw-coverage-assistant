<?php
/**
 * Uninstall Script for Static Cache Wrangler - Coverage Assistant
 *
 * Fired when the plugin is uninstalled. Removes all plugin data
 * and options from the database.
 *
 * @package STCWCoverageAssistant
 * @since 1.0.0
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all plugin options from database
delete_option('stcwca_coverage_trend');
