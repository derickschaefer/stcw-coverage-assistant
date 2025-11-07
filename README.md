# Static Cache Wrangler - Coverage Analytics

Monitor cache coverage and identify uncached content for Static Cache Wrangler.

## Features
- Coverage percentage with 30-day trend chart
- Uncached content list with "Visit Now" buttons
- Modern card-based dashboard UI
- WordPress.org compliant (escaped, sanitized, prepared queries)
- Requires Static Cache Wrangler 2.0.4+

## Installation
1. Install and activate Static Cache Wrangler first
2. Upload this plugin to `wp-content/plugins/stcw-coverage-analytics/`
3. Activate the plugin
4. Navigate to **Static Cache > Coverage Analytics**

## Directory Structure
```
stcw-coverage-analytics/
├── stcw-coverage-analytics.php    Main plugin file
├── LICENSE                        GPL v2+
├── README.md                      This file
├── uninstall.php                  Clean removal
├── includes/
│   └── class-stcwca-core.php     Coverage calculation engine
└── admin/
    ├── class-stcwca-admin.php    Admin controller
    ├── css/admin-style.css       Modern UI styling
    ├── js/admin-script.js        Chart.js integration
    └── views/dashboard.php       Dashboard template
```

## License
GPL v2 or later - Copyright © 2025 Derick Schaefer
```

---

**That's all 9 files. Create these directories and files in your wp-content/plugins/ directory:**
```
stcw-coverage-analytics/
├── stcw-coverage-analytics.php
├── LICENSE
├── README.md
├── uninstall.php
├── includes/
│   └── class-stcwca-core.php
└── admin/
    ├── class-stcwca-admin.php
    ├── css/
    │   └── admin-style.css
    ├── js/
    │   └── admin-script.js
    └── views/
        └── dashboard.php
