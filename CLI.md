# WP-CLI Commands for Coverage Analytics

This plugin extends the `wp scw` namespace with coverage-related commands.

## Command Reference

### 1. `wp scw coverage` - Display Coverage Statistics

Show current cache coverage with color-coded output.

**Usage:**
```bash
# Show coverage in table format (default)
wp scw coverage

# Get coverage data as JSON
wp scw coverage --format=json

# Export as CSV
wp scw coverage --format=csv
```

**Output formats:** `table`, `json`, `csv`, `yaml`

**Example output:**
```
=== Coverage Statistics ===

Coverage:       78.3% (23 of 30 pages)
Total Content:  30 (18 posts, 12 pages)
Cached Files:   23
Uncached:       7
Cache Size:     1.2 MB

⚠ Warning: 7 pages still need caching. Run: wp scw crawl-uncached
```

---

### 2. `wp scw uncached` - List Uncached Content

Display all posts and pages that haven't been cached yet.

**Usage:**
```bash
# List all uncached content
wp scw uncached

# Limit to first 25 results
wp scw uncached --limit=25

# Get as CSV for processing
wp scw uncached --format=csv

# Just count uncached pages
wp scw uncached --format=count

# Get only post IDs (for scripting)
wp scw uncached --format=ids
```

**Options:**
- `--limit=<number>` - Maximum results (0 = all)
- `--format=<format>` - Output format: `table`, `json`, `csv`, `yaml`, `ids`, `count`

**Example output:**
```
+----+---------------------------+------+--------------------------------+---------------------+
| id | title                     | type | url                            | modified            |
+----+---------------------------+------+--------------------------------+---------------------+
| 42 | About Us                  | page | https://example.com/about/     | 2025-01-10 14:23:00 |
| 89 | Contact                   | page | https://example.com/contact/   | 2025-01-09 11:45:00 |
| 12 | Getting Started with AI   | post | https://example.com/ai-guide/  | 2025-01-08 09:12:00 |
+----+---------------------------+------+--------------------------------+---------------------+

Found 3 uncached pages. Run: wp scw crawl-uncached
```

---

### 3. `wp scw uncached-urls` - Export Uncached URLs

Output just the URLs (one per line) for piping to external crawlers.

**Usage:**
```bash
# Print all uncached URLs
wp scw uncached-urls

# Save to file
wp scw uncached-urls > uncached.txt

# Pipe to wget
wp scw uncached-urls | wget -i -

# Pipe to curl with parallel processing
wp scw uncached-urls | xargs -P 4 -I {} curl -s {} > /dev/null

# Process with GNU parallel
wp scw uncached-urls | parallel -j 8 curl -s {} > /dev/null
```

**Options:**
- `--limit=<number>` - Maximum URLs to return (0 = all)

**Example output:**
```
https://example.com/about/
https://example.com/contact/
https://example.com/ai-guide/
https://example.com/services/
https://example.com/portfolio/
```

---

### 4. `wp scw crawl-uncached` - Auto-Cache Everything

**⭐ The most powerful command** - automatically visits all uncached URLs to generate static files.

**Usage:**
```bash
# Cache all uncached pages (sequential)
wp scw crawl-uncached

# Use 4 concurrent requests for faster processing
wp scw crawl-uncached --concurrency=4

# Cache first 50 pages only
wp scw crawl-uncached --limit=50

# Show detailed progress for each URL
wp scw crawl-uncached --verbose

# Fast crawl with minimal delay
wp scw crawl-uncached --concurrency=4 --delay=100

# Maximum speed (use with caution)
wp scw crawl-uncached --concurrency=8 --delay=0
```

**Options:**
- `--concurrency=<number>` - Concurrent requests (1-10, default: 1)
- `--delay=<ms>` - Delay between requests in milliseconds (default: 500)
- `--limit=<number>` - Max pages to cache (0 = all)
- `--verbose` - Show detailed output for each URL

**Example output:**
```
Found 47 uncached pages to process...
Concurrency: 4 | Delay: 500ms

Caching pages  100% [===================] 47/47 (0:02:15)

=== Crawl Complete ===

Successfully cached: 45
Errors:              2

Errors:
  • Legacy Page - HTTP 404
  • Draft Post - HTTP 403

Current coverage: 95.7%
2 pages still uncached. Run again to continue.
```

---

## Real-World Usage Examples

### Quick Coverage Check
```bash
# Just want to see the current status
wp scw coverage
```

### Export for External Crawling
```bash
# Save uncached URLs for overnight batch processing
wp scw uncached-urls > /tmp/uncached-urls.txt

# Process with wget
wget -i /tmp/uncached-urls.txt -O /dev/null

# Process with curl (parallel)
cat /tmp/uncached-urls.txt | xargs -P 8 -I {} curl -s {} > /dev/null
```

### Automated Caching Pipeline
```bash
# Cache everything with optimal settings
wp scw crawl-uncached --concurrency=4 --delay=250

# Check final coverage
wp scw coverage --format=json
```

### CI/CD Integration
```bash
#!/bin/bash
# Pre-deployment cache generation script

echo "Checking cache coverage..."
COVERAGE=$(wp scw coverage --format=json | jq -r '.coverage_percent')

if (( $(echo "$COVERAGE < 95" | bc -l) )); then
    echo "Coverage below 95%, caching uncached content..."
    wp scw crawl-uncached --concurrency=4
fi

echo "Final coverage: $(wp scw coverage --format=json | jq -r '.coverage_percent')%"
```

### Monitoring Script
```bash
#!/bin/bash
# Add to cron for daily coverage reports

UNCACHED_COUNT=$(wp scw uncached --format=count)

if [ "$UNCACHED_COUNT" -gt 0 ]; then
    echo "⚠️  $UNCACHED_COUNT pages need caching"
    wp scw uncached --limit=10
else
    echo "✅ 100% coverage maintained"
fi
```

---

## Integration with Parent Plugin

These commands work alongside the existing `wp scw` commands:

```bash
wp scw
usage: wp scw clear 
   or: wp scw disable 
   or: wp scw enable 
   or: wp scw process 
   or: wp scw status 
   or: wp scw zip [--output=<path>]
   or: wp scw coverage [--format=<format>]
   or: wp scw uncached [--limit=<number>] [--format=<format>]
   or: wp scw uncached-urls [--limit=<number>]
   or: wp scw crawl-uncached [--concurrency=<number>] [--delay=<ms>]
```

---

## Tips & Best Practices

1. **Start slow:** Begin with `--concurrency=1` to ensure stability
2. **Monitor server load:** Use `--concurrency=4` for most servers
3. **Respect your server:** Don't use `--delay=0` on shared hosting
4. **Check coverage first:** Run `wp scw coverage` before caching
5. **Use verbose for debugging:** Add `--verbose` when troubleshooting
6. **Automate it:** Add `wp scw crawl-uncached` to your deployment scripts

---

## Troubleshooting

**Q: Command not found**  
A: Make sure Coverage Analytics plugin is activated and WP-CLI is installed

**Q: "Static generation is not enabled" error**  
A: Enable static generation in Static Cache settings first: `wp scw enable`

**Q: Crawl is too slow**  
A: Increase `--concurrency` and decrease `--delay`

**Q: Server timeouts during crawl**  
A: Decrease `--concurrency` or increase `--delay`

**Q: Want to cache specific pages only**  
A: Use `wp scw uncached --format=ids` and process manually with `wp scw process`

---

## Performance Recommendations

| Server Type | Concurrency | Delay |
|-------------|-------------|-------|
| Shared Hosting | 1-2 | 1000ms |
| VPS/Cloud | 4 | 500ms |
| Dedicated Server | 8 | 250ms |
| Local Development | 8 | 0ms |
