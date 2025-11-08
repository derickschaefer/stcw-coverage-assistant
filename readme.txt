=== Static Cache Wrangler - Coverage Assistant ===
Contributors: derickschaefer
Donate link: https://moderncli.dev/
Tags: static site, cache, assistant, coverage, monitoring
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires Plugins: static-cache-wrangler

Monitor cache coverage and identify uncached content for Static Cache Wrangler.

== Description ==

**Coverage Assistant** is a companion plugin for [Static Cache Wrangler](https://wordpress.org/plugins/static-cache-wrangler/) that helps you monitor which posts and pages have been cached as static HTML files.

Get instant visibility into your static cache coverage with a modern, card-based dashboard that shows:

* **Coverage Percentage** - See at a glance what % of your content is cached
* **Uncached Content List** - Identify exactly which pages need caching
* **One-Click Actions** - Visit uncached pages directly from the dashboard
* **Cache Statistics** - View total files, cache size, and more

Perfect for site owners who want to ensure complete static site generation before deploying to CDN, Amazon S3®, or creating offline copies.

= Key Features =

* **Visual Dashboard** - Modern card-based UI with 4 key metrics
* **Coverage Cards** - Color-coded indicators (green/yellow/red) for quick status checks
* **WP-CLI Support** - Command-line interface support including cache completion command
* **Uncached Content Table** - Complete list with page titles, types, and last modified dates
* **Recently Cached** - See the last 10 pages that were successfully cached
* **Quick Actions** - Direct links to Static Cache settings and trend data refresh
* **Zero Configuration** - Works immediately after activation
* **Clean Uninstall** - Removes all data when plugin is deleted

= Requirements =

* WordPress 5.0 or higher
* PHP 7.4 or higher
* **Static Cache Wrangler 2.0.4+** (parent plugin must be active)
* **Chart.js** (for trend visualization - see FAQ for setup)

= Perfect For =

* Site owners deploying to CDN or Amazon S3®
* Developers creating offline-ready static sites
* Agencies managing multiple static WordPress installations
* Anyone who wants to monitor cache generation progress
* Teams ensuring 100% coverage before launch

= How It Works =

1. Install and activate both Static Cache Wrangler and Coverage Assistant
2. Enable static site generation in Static Cache settings
3. Browse your site normally - pages are cached as you visit them
4. Check the Coverage Assistant dashboard to see progress
5. Use the uncached content list to identify pages that need visiting
6. Click "Visit Now" buttons to generate static files immediately
7. Monitor the 30-day trend to track coverage over time

= Chart.js Setup =

To display the trend chart, you must add Chart.js to your WordPress site separately. This plugin does not bundle Chart.js to comply with WordPress.org guidelines.

**Option 1: Add via Theme**
Add this to your theme's `functions.php`:

`
function my_enqueue_chartjs() {
    if (is_admin()) {
        wp_enqueue_script('chartjs', get_template_directory_uri() . '/js/chart.min.js', [], '4.4.0', true);
    }
}
add_action('admin_enqueue_scripts', 'my_enqueue_chartjs');
`

**Option 2: Download Chart.js**
Download from: https://www.chartjs.org/docs/latest/getting-started/installation.html

Place `chart.min.js` in your theme's `js` folder and use the code above.

= WordPress.org Compliance =

This plugin follows all WordPress.org Plugin Directory guidelines:

* All output properly escaped
* All input sanitized
* Database queries use `$wpdb->prepare()`
* Scripts and styles properly enqueued
* No external resources loaded
* Clean uninstall removes all data
* Proper text domain for translations

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to **Plugins > Add New**
3. Search for "Static Cache Wrangler - Coverage Assistant"
4. Click **Install Now** then **Activate**
5. Ensure Static Cache Wrangler is also installed and activated
6. Navigate to **Static Cache > Coverage Assistant**

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to **Plugins > Add New > Upload Plugin**
4. Choose the downloaded ZIP file
5. Click **Install Now** then **Activate**
6. Ensure Static Cache Wrangler is also installed and activated
7. Navigate to **Static Cache > Coverage Assistant**

= After Installation =

1. Go to **Settings > Static Cache** and enable static generation
2. Browse your site to generate some cached pages
3. Go to **Static Cache > Coverage Assistant** to view your dashboard
4. (Optional) Add Chart.js to enable the trend chart visualization

== Frequently Asked Questions ==

= Does this work without Static Cache Wrangler? =

No, this is a companion plugin that requires Static Cache Wrangler 2.0.4 or higher to be installed and activated. It will show an admin notice if the parent plugin is missing.

= Why isn't the trend chart displaying? =

The trend chart requires Chart.js to be added to your WordPress site. This plugin does not bundle Chart.js to comply with WordPress.org guidelines. See the Installation section for instructions on adding Chart.js via your theme.

= How do I add Chart.js? =

Download Chart.js from https://www.chartjs.org/ and add it to your theme's `js` folder. Then enqueue it in your theme's `functions.php`:

`
function my_enqueue_chartjs() {
    if (is_admin()) {
        wp_enqueue_script('chartjs', get_template_directory_uri() . '/js/chart.min.js', [], '4.4.0', true);
    }
}
add_action('admin_enqueue_scripts', 'my_enqueue_chartjs');
`

= Can I use the plugin without the trend chart? =

Yes! All other features work perfectly without Chart.js. You'll still see:
* Coverage percentage cards
* Uncached content list
* Recently cached pages
* All statistics and metrics

The trend chart is optional but recommended for tracking progress over time.

= How often is coverage data updated? =

Coverage statistics are calculated in real-time when you visit the dashboard. Trend data snapshots are stored once per day automatically. You can manually refresh trend data using the "Refresh Trend Data" button.

= What data does this plugin store? =

The plugin stores one WordPress option: `stcwca_coverage_trend` - an array containing up to 30 days of daily coverage snapshots. This data is automatically cleaned (keeps last 30 days) and completely removed when you uninstall the plugin.

= Does this plugin cache anything itself? =

No, this plugin only monitors caching done by Static Cache Wrangler. It does not perform any caching operations itself.

= How do I know which pages need caching? =

The dashboard shows an "Uncached Content" table listing all posts and pages that haven't been cached yet. Each row includes a "Visit Now" button to generate the static file immediately.

= Can I exclude certain pages from the uncached list? =

Not currently, but this is planned for a future version. The plugin currently shows all published posts and pages.

= Does this work with custom post types? =

Currently, the plugin only monitors standard posts and pages. Custom post type support is planned for a future release.

= How can I get 100% coverage quickly? =

1. Enable static generation in Static Cache Wrangler
2. Visit the Coverage Assistant dashboard
3. Click "Visit Now" on each uncached page
4. Or use WP-CLI: `wp scw process` (if parent plugin supports it)

= What happens if I uninstall the plugin? =

All plugin data is automatically removed, including the `stcwca_coverage_trend` option. Your static cached files created by Static Cache Wrangler remain untouched.

= Is this compatible with multisite? =

Not tested yet. Multisite compatibility is planned for a future release.

= Where can I get support? =

For issues, feature requests, and general support:
* GitHub Issues: https://github.com/derickschaefer/stcw-assistant/issues
* WordPress.org Support Forum: https://wordpress.org/support/plugin/stcw-coverage-assistant/

== Screenshots ==

1. Coverage dashboard showing 78.3% coverage with color-coded metric cards
2. Available CLI commands
3. Uncached content table with "Copy Link" action buttons
4. Recently cached content showing last 10 successfully cached pages
5. Quick actions sidebar with links to parent plugin settings
== Changelog ==

= 1.0.5 - 2025-11-07 =

**New: WP-CLI Support**

* Introduced `wp scw` CLI commands for coverage management:
  * `wp scw coverage` – Display current cache stats (table, JSON, CSV, YAML)
  * `wp scw uncached` – List uncached pages/posts with filtering and formats
  * `wp scw uncached-urls` – Export uncached URLs for external crawlers
  * `wp scw crawl-uncached` – Automatically crawl and cache all missing pages
* CLI output includes warning indicators, progress bars, and real-time status
* Ideal for CI/CD pipelines, cron jobs, and batch caching automation
* Extensive format and concurrency options for advanced scripting

= 1.0.4 - 2025-09-29 =

**Enhancement: File Counting & Cache Stats**

* Added display of **total cached file count** and **total content items**
* New metric for **cache size in MB** added to dashboard
* Improved support for custom file paths during coverage analysis
* Admin UI: tooltip hints added to clarify coverage formula

= 1.0.3 - 2025-08-10 =

**Fixes + Compliance**

* Fixed bug with incorrect uncached post count in rare conditions
* Escaped all admin output strings for WordPress.org security compliance
* Removed legacy helper functions and optimized trend chart data handling
* Improved compatibility with Static Cache Wrangler 2.0.5

= 1.0.2 - 2025-06-21 =

**Improvements: Accuracy & Performance**

* Refined coverage percentage calculation logic for mixed post statuses
* Added logic to exclude drafts, private posts, and trashed items from stats
* Reduced database queries on the dashboard by ~30% for faster load
* Trend data update now debounced to avoid duplicate entries on rapid refresh

= 1.0.1 - 2025-04-18 =

**Initial Fixes and Minor Enhancements**

* Fixed issue where some pages were misidentified as uncached
* Added "Last Cached" timestamp to recently cached list
* Adjusted color thresholds for coverage indicators (green/yellow/red)
* Internal refactoring of coverage engine for extensibility
* Minor styling tweaks for better mobile admin view

= 1.0.0 - 2025-01-15 =

**Initial Release**

* Coverage percentage calculation and display
* 30-day trend tracking with Chart.js visualization
* Uncached content identification and listing
* Recently cached content display
* Modern card-based dashboard UI
* Color-coded status indicators (green/yellow/red)
* One-click "Visit Now" buttons for uncached pages
* Quick actions sidebar
* Manual trend data refresh
* WordPress.org coding standards compliance
* Clean uninstall support
* Full i18n/translation readiness

== Upgrade Notice ==

= 1.0.0 =
Initial release of Coverage Assistant companion plugin. Requires Static Cache Wrangler 2.0.5 and WP-CLI (separate install from https://wp-cli.org) for full functionality.

== Additional Information ==

= About the Author =

Created by **Derick Schaefer** - Developer, writer, and WordPress enthusiast.

* Website: [ModernCLI.dev](https://moderncli.dev)
* GitHub: [@derickschaefer](https://github.com/derickschaefer)

= Related Plugins =

* [Static Cache Wrangler](https://wordpress.org/plugins/static-cache-wrangler/) - Parent plugin (required)

= Planned Features =

* TBD based on use cases and community feedback

= Contributing =

This is an open-source project. Contributions are welcome!

* GitHub Repository: https://github.com/derickschaefer/stcw-assistant
* Submit Issues: https://github.com/derickschaefer/stcw-assistant/issues
* Pull Requests: https://github.com/derickschaefer/stcw-assistant/pulls

= License =

This plugin is licensed under the GNU General Public License v2.0 or later.

Copyright (C) 2025 Derick Schaefer

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

= Trademark Recognition =

Amazon S3® is a registered trademark of Amazon Technologies, Inc.

This plugin is not endorsed by, affiliated with, or sponsored by Amazon Technologies, Inc. or any trademark owners mentioned in this documentation.

== Privacy Policy ==

**Data Collection**

This plugin does not:
* Collect any personal data
* Send data to external servers
* Use cookies or tracking
* Store user information

**Data Storage**

The plugin stores one WordPress option (`stcwca_coverage_trend`) containing:
* Daily coverage percentages (last 30 days)
* Number of cached files per day
* Total content count per day
* Timestamps (server time, not user time)

This data is:
* Stored locally in your WordPress database
* Automatically cleaned (keeps 30 days)
* Completely removed on plugin uninstall
* Not shared with any third parties

**GDPR Compliance**

This plugin is GDPR compliant as it:
* Does not process personal data
* Does not track users
* Does not use cookies
* Stores only aggregate statistics locally

== Technical Details ==

**Database Usage**

* Uses one WordPress option: `stcwca_coverage_trend`
* No custom database tables created
* No database schema modifications
* Clean uninstall removes all data

**Performance**

* Lightweight - minimal resource usage
* Queries optimized with prepared statements
* No frontend performance impact
* Dashboard-only calculations (not run on frontend)

**Security**

* All database queries use `$wpdb->prepare()`
* All output escaped (`esc_html`, `esc_url`, `esc_attr`)
* All input sanitized (`sanitize_text_field`, `sanitize_key`, `absint`)
* Capability checks on all admin functions
* Nonce verification on all form submissions

**Compatibility**

* WordPress 5.0+
* PHP 7.4, 8.0, 8.1, 8.2, 8.3
* Compatible with all major themes
* Compatible with all major page builders
* Compatible with Static Cache Wrangler 2.0.4+

**File Structure**

`
stcw-coverage-assistant/
├── stcw-coverage-assistant.php    Main plugin file
├── LICENSE                        GPL v2+ license
├── readme.txt                     This file
├── uninstall.php                  Clean removal script
├── includes/
│   └── class-stcwca-core.php     Coverage calculation engine
└── admin/
    ├── class-stcwca-admin.php    Admin dashboard controller
    ├── css/
    │   └── admin-style.css       Modern UI styling
    ├── js/
    │   └── admin-script.js       Chart.js integration
    └── views/
        └── dashboard.php         Dashboard template
`

**Code Quality**

* Follows WordPress Coding Standards
* PHPCS compliant
* Plugin Check compliant
* Proper PHPDoc comments throughout
* Meaningful variable and function names
* DRY principles applied
