=== Static Cache Wrangler - Coverage Assistant ===
Contributors: derickschaefer
Donate link: https://moderncli.dev/
Tags: static cache wrangler, static cache, offline, html, static html
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires Plugins: static-cache-wrangler

Monitor cache coverage and identify uncached content for Static Cache Wrangler.

== Description ==

**Coverage Assistant** is a companion plugin for [Static Cache Wrangler](https://wordpress.org/plugins/static-cache-wrangler/) that helps you monitor which posts and pages have been cached as static HTML files.

Get instant visibility into your static cache coverage with a modern, card-based dashboard that shows:

* **Coverage Percentage** - See at a glance what % of your content is cached
* **Uncached Content List** - Identify exactly which pages need caching
* **One-Click Copy Links** - Copy uncached URLs to clipboard for manual caching
* **GUI Crawler** - Interactive browser-based crawler unlocks at 65% coverage
* **Cache Statistics** - View total files, cache size, and more

Perfect for site owners who want to ensure complete static site generation before deploying to CDN, Amazon S3®, or creating offline copies.

= Key Features =

* **Visual Dashboard** - Modern card-based UI with 4 key metrics
* **Coverage Cards** - Color-coded indicators (green/yellow/red) for quick status checks
* **Uncached Content Table** - Complete list with page titles, types, and last modified dates
* **One-Click Copy Links** - Copy uncached URLs to clipboard for manual caching
* **GUI Crawler** - Interactive browser-based crawler with progress tracking (unlocks at 65% coverage)
* **Adaptive Throttling** - Smart crawling speed adjusts to your server's performance
* **Recently Cached** - See the last 10 pages that were successfully cached
* **Auto-Cache Command** - Use `wp scw crawl-uncached` to automatically cache all remaining pages
* **WP-CLI Integration** - Full command-line interface for coverage monitoring and automation
* **Batch Processing** - Concurrent crawling with `--concurrency` flag for faster cache generation
* **Export URLs** - `wp scw uncached-urls` outputs URLs for piping to external tools
* **Zero Configuration** - Works immediately after activation
* **Clean Uninstall** - Removes all data when plugin is deleted

= Requirements =

* WordPress 5.0 or higher
* PHP 7.4 or higher
* **Static Cache Wrangler 2.0.5+** (parent plugin must be active)

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
5. Use the uncached content list to identify pages that need caching
6. Click "Copy Link" buttons to copy URLs for manual visiting
7. **NEW:** At 65% coverage, unlock the GUI crawler to batch-process remaining pages
8. **Alternative:** Use `wp scw crawl-uncached` CLI command for fastest bulk caching


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

== Frequently Asked Questions ==

= Does this work without Static Cache Wrangler? =

No, this is a companion plugin that requires Static Cache Wrangler 2.0.5 or higher to be installed and activated. It will show an admin notice if the parent plugin is missing.

= How often is coverage data updated? =

Coverage statistics are calculated in real-time when you visit the dashboard.

= What data does this plugin store? =

This plugin does not store any persistent data in the WordPress database. All coverage statistics are calculated in real-time when you view the dashboard. No options, settings, or historical data are saved.

= Does this plugin cache anything itself? =

No, this plugin only monitors caching done by Static Cache Wrangler. It does not perform any caching operations itself.

= How do I know which pages need caching? =

The dashboard shows an "Uncached Content" table listing all posts and pages that haven't been cached yet. Each row includes a "Copy Link" button to copy the URL to your clipboard for easy visiting.

= What is the GUI crawler and how do I unlock it? =

The GUI crawler is an interactive, browser-based tool that automatically visits and caches your uncached pages. It unlocks when you reach 65% cache coverage. This threshold ensures you've naturally cached the most important pages first, and the crawler is used only to finish the remaining less-visited pages.

You can adjust this threshold in wp-config.php:
```php
define('STCWCA_GUI_CRAWLER_THRESHOLD', 70); // Default is 65
```

= How fast is the GUI crawler? =

The GUI crawler uses adaptive throttling to respect your server's capabilities. It automatically speeds up on fast servers and slows down if errors occur. Typical processing speed is 1-2 pages per second, adjusting in real-time based on server response times.

= Can I exclude certain pages from the uncached list? =

Not currently, but this is planned for a future version. The plugin currently shows all published posts and pages.

= Does this work with custom post types? =

Currently, the plugin only monitors standard posts and pages. Custom post type support is planned for a future release.

= How can I get 100% coverage quickly? =

1. Enable static generation in Static Cache Wrangler
2. Visit the Coverage Assistant dashboard
3. Choose your preferred method:
   * **Fastest (CLI):** Run `wp scw crawl-uncached --concurrency=4` to auto-cache everything
   * **Browser-based:** Once you reach 65% coverage, use the GUI crawler to finish remaining pages
   * **Manual:** Click "Copy Link" on each uncached page and visit them in a browser
4. Check progress with `wp scw coverage` or refresh the dashboard

The `crawl-uncached` CLI command is the fastest way to achieve 100% coverage - it automatically visits all uncached URLs to trigger static file generation.

= What WP-CLI commands are available? =

Coverage Assistant extends the `wp scw` namespace with these commands:

* `wp scw coverage` - Display coverage statistics (supports --format=json, csv, yaml)
* `wp scw uncached` - List all uncached content with filtering options
* `wp scw uncached-urls` - Export URLs for piping to external crawlers
* `wp scw crawl-uncached` - Automatically visit and cache all uncached pages

Example usage:
```
# Show current coverage
wp scw coverage

# Auto-cache everything with 4 concurrent requests
wp scw crawl-uncached --concurrency=4

# Export uncached URLs to file
wp scw uncached-urls > uncached.txt

# Pipe to wget for caching
wp scw uncached-urls | wget -i -
```

See CLI.md in the plugin directory for complete documentation.

= What happens if I uninstall the plugin? =

The plugin stores no persistent data, so uninstalling simply removes the plugin files. Your static cached files created by Static Cache Wrangler remain completely untouched and continue to function normally.

= Is this compatible with multisite? =

Not tested yet. Multisite compatibility is planned for a future release.

= Where can I get support? =

For issues, feature requests, and general support:
* GitHub Issues: https://github.com/derickschaefer/stcw-assistant/issues
* WordPress.org Support Forum: https://wordpress.org/support/plugin/stcw-coverage-assistant/

== Screenshots ==

1. Coverage dashboard showing 28.6% coverage with color-coded metric cards
2. Instructions and available CLI commands for automation and batch processing
3. Cached content passes 65% threshold enabling batch processing in GUI
4. GUI crawler interface with adaptive throttling and progress tracking

== Changelog ==

= 1.0.6 - 2025-11-09 =

**New: GUI Crawler + Fixes**

* Introduced interactive GUI crawler for browser-based batch caching
* GUI crawler unlocks at 65% coverage (configurable via wp-config.php)
* Adaptive throttling automatically adjusts crawl speed to server performance
* Real-time progress tracking with success/error counts
* Smart delay calculation prevents server overload
* Copy clarifications on multiple UI cards
* Readme updates and corrections
* Removed legacy trend tracking feature (not actively used)

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
* Removed legacy helper functions and optimized data handling
* Improved compatibility with Static Cache Wrangler 2.0.5

= 1.0.2 - 2025-06-21 =

**Improvements: Accuracy & Performance**

* Refined coverage percentage calculation logic for mixed post statuses
* Added logic to exclude drafts, private posts, and trashed items from stats
* Reduced database queries on the dashboard by ~30% for faster load

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
* Uncached content identification and listing
* Recently cached content display
* Modern card-based dashboard UI
* Color-coded status indicators (green/yellow/red)
* One-click "Copy Link" buttons for uncached pages
* Quick actions sidebar
* WordPress.org coding standards compliance
* Clean uninstall support
* Full i18n/translation readiness

== Upgrade Notice ==

= 1.0.6 =
Major update: New GUI crawler for browser-based batch caching! Unlocks at 65% coverage with adaptive throttling. Also includes WP-CLI commands for automation.

= 1.0.5 =
New WP-CLI commands added! Use `wp scw crawl-uncached` for fastest bulk caching. See CLI.md for full documentation.

= 1.0.0 =
Initial release of Coverage Assistant companion plugin. Requires Static Cache Wrangler 2.0.5 or higher.

== Additional Information ==

= About the Author =

Created by **Derick Schaefer** - Developer, writer, and WordPress enthusiast.

* Website: [ModernCLI.dev](https://moderncli.dev)
* GitHub: [@derickschaefer](https://github.com/derickschaefer)

= Related Plugins =

* [Static Cache Wrangler](https://wordpress.org/plugins/static-cache-wrangler/) - Parent plugin (required)

= Planned Features =

* Custom post type support
* Multisite compatibility
* Configurable exclusion rules
* Advanced filtering options
* Enhanced reporting and analytics

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

The plugin does not store any data in the WordPress database. All coverage statistics are calculated in real-time from your existing WordPress posts and pages. No historical data, user information, or statistics are stored.

This data is:
* Stored locally in your WordPress database
* Not shared with any third parties

**GDPR Compliance**

This plugin is GDPR compliant as it:
* Does not process personal data
* Does not track users
* Does not use cookies
* Stores only aggregate statistics locally

== Technical Details ==

**Database Usage**

* No WordPress options created or stored
* No custom database tables created
* No database schema modifications
* Queries existing WordPress posts table only (read-only)
* Clean uninstall (nothing to remove)

**Performance**

* Lightweight - minimal resource usage
* Queries optimized with prepared statements
* No frontend performance impact
* Dashboard-only calculations (not run on frontend)
* GUI crawler uses adaptive throttling to prevent server overload

**Security**

* All database queries use `$wpdb->prepare()`
* All output escaped (`esc_html`, `esc_url`, `esc_attr`)
* All input sanitized (`sanitize_text_field`, `sanitize_key`, `absint`)
* Capability checks on all admin functions
* Nonce verification on all form submissions
* AJAX endpoints protected with nonce validation

**Compatibility**

* WordPress 5.0+
* PHP 7.4, 8.0, 8.1, 8.2, 8.3
* Compatible with all major themes
* Compatible with all major page builders
* Compatible with Static Cache Wrangler 2.0.5+

**File Structure**

```
stcw-coverage-assistant/
├── stcw-coverage-assistant.php    Main plugin file
├── LICENSE                        GPL v2+ license
├── readme.txt                     This file
├── CLI.md                         WP-CLI documentation
├── uninstall.php                  Clean removal script
├── includes/
│   ├── class-stcwca-core.php     Coverage calculation engine
│   ├── class-stcwca-cli.php      WP-CLI commands
│   └── class-stcwca-crawler.php  Shared crawler logic
└── admin/
    ├── class-stcwca-admin.php    Admin dashboard controller
    ├── css/
    │   └── admin-style.css       Modern UI styling
    ├── js/
    │   ├── admin-script.js       Copy Link functionality
    │   └── crawler.js            GUI crawler with adaptive throttling
    └── views/
        └── dashboard.php         Dashboard template
```

**Code Quality**

* Follows WordPress Coding Standards
* PHPCS compliant
* Plugin Check compliant
* Proper PHPDoc comments throughout
* Meaningful variable and function names
* DRY principles applied

**Interested in learning more about command-line interfaces and WP-CLI?**
Check out [ModernCLI.dev](https://moderncli.dev) — a practical guide to mastering modern CLI workflows.
