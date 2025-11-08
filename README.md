# Static Cache Wrangler - Coverage Assistant

Monitor cache coverage and identify uncached content for Static Cache Wrangler.

## Features
- Coverage percentage with 30-day trend chart
- Uncached content list with "Copy Link" buttons
- Modern card-based dashboard UI
- **WP-CLI commands** for automation and batch processing
- WordPress.org compliant (escaped, sanitized, prepared queries)
- Requires Static Cache Wrangler 2.0.4+

## WP-CLI Commands

This plugin extends the `wp scw` namespace with powerful coverage commands:

```bash
# Check coverage statistics
wp scw coverage [--format=json]

# List uncached content
wp scw uncached [--limit=25] [--format=csv]

# Export uncached URLs for automation
wp scw uncached-urls

# Auto-cache all uncached pages
wp scw crawl-uncached [--concurrency=4] [--delay=500]
```

See [CLI-COMMANDS.md](CLI-COMMANDS.md) for complete documentation and examples.

## Installation
1. Install and activate Static Cache Wrangler first
2. Upload this plugin to `wp-content/plugins/stcw-coverage-assistant/`
3. Activate the plugin
4. Navigate to **Static Cache > Coverage Assistant**

## Directory Structure
```
stcw-coverage-assistant/
├── stcw-coverage-assistant.php    Main plugin file
├── LICENSE                        GPL v2+
├── README.md                      This file
├── CLI-COMMANDS.md                WP-CLI documentation
├── uninstall.php                  Clean removal
├── includes/
│   ├── class-stcwca-core.php     Coverage calculation engine
│   └── class-stcwca-cli.php      WP-CLI commands
└── admin/
    ├── class-stcwca-admin.php    Admin controller
    ├── css/admin-style.css       Modern UI styling
    ├── js/admin-script.js        Copy Link functionality
    └── views/dashboard.php       Dashboard template
```

## License
GPL v2 or later - Copyright © 2025 Derick Schaefer
