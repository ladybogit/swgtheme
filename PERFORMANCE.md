# Performance Optimization Guide

## Overview

The SWG Theme includes a comprehensive performance optimization system designed to maximize page load speed, reduce server load, and improve Core Web Vitals scores. This guide covers all available optimizations and best practices.

## Quick Start

**Access:** `Appearance → ⚡ Performance`

**Recommended Settings for Most Sites:**
- ✅ Fragment Caching: Enabled (3600s)
- ✅ WebP Conversion: Enabled (85% quality)
- ✅ Enhanced Lazy Loading: Enabled
- ✅ Defer JavaScript: Enabled
- ✅ Remove Query Strings: Enabled
- ✅ Auto Database Cleanup: Enabled (30 days)
- ✅ Disable Emojis: Enabled
- ⚠️ Critical CSS: Enable after generating
- ⚠️ HTML Minification: Test before enabling
- ⚠️ GZIP: Enable if not handled by server

---

## 🗄️ Caching System

### Fragment Caching

**What it does:** Caches specific parts of your pages (widgets, template parts, heavy queries) to avoid regenerating them on every page load.

**How to use:**

```php
// In your template file
echo swg_cache_fragment( 'unique-key', function() {
    // Your expensive code here
    $popular_posts = new WP_Query( array(
        'posts_per_page' => 5,
        'meta_key' => 'post_views',
        'orderby' => 'meta_value_num',
    ) );
    
    while ( $popular_posts->have_posts() ) {
        $popular_posts->the_post();
        get_template_part( 'template-parts/content', 'popular' );
    }
    wp_reset_postdata();
}, 3600 ); // Cache for 1 hour
```

**Common Use Cases:**
- Widget output
- Popular posts lists
- Complex queries
- External API calls
- Generated menus
- Statistics/counters

**Cache Keys Best Practices:**
- Use descriptive names: `sidebar-popular-posts`
- Include context when needed: `homepage-slider-{$post_id}`
- Keep them unique per content variation

### Cache Management

**Statistics:**
- View total transients
- See fragment cache count
- Check object cache status

**Manual Actions:**
- Clear all caches via Tools tab
- Auto-clear on post publish/update (recommended plugin: WP Rocket)

**Expiration Times:**
| Content Type | Recommended Time |
|-------------|------------------|
| Popular posts | 1 hour (3600s) |
| Widgets | 6 hours (21600s) |
| Archives | 24 hours (86400s) |
| Static content | 1 week (604800s) |

---

## 🖼️ Image Optimization

### WebP Conversion

**What it does:** Automatically generates WebP versions of all uploaded JPG/PNG images. WebP provides 25-35% smaller file sizes with same visual quality.

**Requirements:**
- PHP GD library with WebP support
- Theme automatically checks and displays compatibility status

**How it works:**
1. Upload image via WordPress Media Library
2. Original JPG/PNG saved normally
3. WebP version generated automatically (85% quality default)
4. WebP served to supported browsers
5. Original served as fallback

**Generated Files:**
```
uploads/2026/01/
├── image.jpg          (original)
├── image.webp         (auto-generated)
├── image-300x300.jpg  (thumbnail)
├── image-300x300.webp (auto-generated)
└── ...
```

**Browser Support:**
- Chrome/Edge: ✅ Full support
- Firefox: ✅ Full support
- Safari 14+: ✅ Full support
- Older browsers: Falls back to JPG/PNG

**Quality Settings:**
- 85%: Default, best balance
- 75-80%: More compression, slight quality loss
- 90-95%: Better quality, less compression
- 100%: Lossless (not recommended, larger files)

### Enhanced Lazy Loading

**What it does:** Adds `loading="lazy"` and `decoding="async"` attributes to all images in post content and thumbnails.

**Benefits:**
- Images load only when scrolling near them
- Faster initial page load
- Reduced bandwidth for users who don't scroll
- Improved Largest Contentful Paint (LCP)

**Automatic Application:**
- Post content images
- Featured images
- Widget images
- Gallery images

**Exclusions:**
- Admin pages
- Feed content
- Preview mode
- Images already with loading attribute

**How it works:**
```html
<!-- Before -->
<img src="image.jpg" alt="Example">

<!-- After -->
<img src="image.jpg" alt="Example" loading="lazy" decoding="async">
```

**Best Practices:**
- Don't lazy load above-the-fold images
- Use with WebP for maximum savings
- Test on mobile devices

---

## ⚡ Asset Optimization

### JavaScript Optimization

**Defer JavaScript:**
- Delays script execution until HTML parsing completes
- Improves First Contentful Paint (FCP)
- Safe for most non-critical scripts

**Async JavaScript:**
- Scripts download in parallel
- Execute as soon as downloaded
- jQuery is excluded automatically
- Best for independent scripts

**When to Use:**
| Setting | Best For | Avoid If |
|---------|----------|----------|
| Defer | Analytics, widgets, social | Scripts modify HTML structure |
| Async | Ads, tracking, third-party | Scripts depend on each other |
| Both Off | Critical theme functionality | - |

### Critical CSS

**What it is:** Minimal CSS needed to render above-the-fold content, inlined in `<head>` for instant rendering.

**How to generate:**
1. Visit [Critical Path CSS Generator](https://www.sitelocity.com/critical-path-css-generator)
2. Enter your homepage URL
3. Copy the generated CSS
4. Paste into Performance → Assets → Critical CSS textarea
5. Enable "Inline critical CSS"

**Example Critical CSS:**
```css
body{margin:0;padding:0;font-family:sans-serif}
header{background:#000;color:#fff;padding:1rem}
.container{max-width:1200px;margin:0 auto}
h1{font-size:2rem;margin-bottom:1rem}
```

**Benefits:**
- Eliminates render-blocking CSS
- Faster First Contentful Paint
- Improved perceived performance

**Deferred Styles:**
- Non-critical CSS loads asynchronously
- Uses `media="print"` trick for instant swap
- No flash of unstyled content

### Resource Hints

**DNS Prefetch:**
Automatically added for:
- Google Fonts
- Font CDNs
- Your configured CDN

**Preconnect:**
Establishes early connections to critical domains

**Preload Fonts:**
```
https://yoursite.com/fonts/custom-font.woff2
https://yoursite.com/fonts/another-font.woff2
```
Enter one URL per line in Performance → Assets → Preload Fonts

**CDN Configuration:**
Enter your CDN URL (e.g., `https://cdn.example.com`) to add:
```html
<link rel="dns-prefetch" href="https://cdn.example.com">
<link rel="preconnect" href="https://cdn.example.com" crossorigin>
```

### Query String Removal

**What it does:** Removes `?ver=1.0.0` from CSS/JS URLs

**Before:**
```
/style.css?ver=1.0.0
```

**After:**
```
/style.css
```

**Benefits:**
- Better caching on some proxy servers
- Cleaner URLs
- May improve CDN caching

---

## 🗃️ Database Optimization

### Automatic Cleanup

**Schedule:** Runs daily via WordPress cron

**What gets cleaned:**

**Post Revisions:**
- Keeps latest revision
- Deletes older revisions beyond set days
- Typical savings: 20-40% database size

**Auto-Drafts:**
- Cleans abandoned auto-saves
- Removes drafts never published
- Frees orphaned autosave data

**Trash:**
- Permanently deletes trashed content
- Applies to posts, pages, comments
- Configurable retention period

**Orphaned Data:**
- Removes post meta without posts
- Cleans term relationships
- Deletes expired transients

**Table Optimization:**
- Defragments database tables
- Reclaims unused space
- Improves query performance

### Configuration

**Cleanup Age:**
- 30 days (default)
- 7 days (aggressive)
- 60-90 days (conservative)

**What to Enable:**

| Item | Recommended | Impact |
|------|-------------|--------|
| Revisions | ✅ Yes | High space savings |
| Auto-drafts | ✅ Yes | Medium savings |
| Trash | ⚠️ Careful | Permanent deletion |
| Orphaned data | ✅ Yes | Database integrity |
| Table optimization | ✅ Yes | Performance boost |

### Manual Cleanup

**When to use:**
- After major content changes
- Before backups
- Site is slow
- Database errors

**How to run:**
1. Go to Performance → Tools
2. Click "Cleanup Database"
3. Wait for completion (may take 30-60 seconds)
4. Review success message

**What gets cleaned:**
- All items enabled in settings
- Immediate execution
- Returns statistics

---

## 🔧 Advanced Optimizations

### HTML Minification

**What it does:**
- Removes whitespace between tags
- Strips HTML comments
- Reduces page size by 10-30%

**Safe to remove:**
- Spaces between tags
- Line breaks
- Non-IE conditional comments

**Preserved:**
- IE conditional comments (`<!--[if IE]>`)
- Critical formatting
- JavaScript/CSS within HTML

**When disabled:**
- WP_DEBUG is enabled
- Admin pages
- AJAX requests

**Testing:**
1. Enable minification
2. Test all pages
3. Check forms still work
4. Validate HTML (optional)
5. Test with browser DevTools

### GZIP Compression

**What it does:** Compresses HTML/CSS/JS before sending to browser

**Compression Ratio:**
- HTML: 60-70% smaller
- CSS: 70-80% smaller
- JavaScript: 50-60% smaller

**Requirements:**
- PHP Zlib extension (check: Performance → Advanced)
- Server allows `ob_gzhandler`
- Not already enabled by server

**Server vs Theme GZIP:**

| Method | Priority | Performance |
|--------|----------|-------------|
| Apache mod_deflate | Preferred | Best |
| Nginx gzip | Preferred | Best |
| Theme GZIP | Fallback | Good |

**Check if already enabled:**
1. Visit [GIDNetwork GZIP Test](https://www.gidnetwork.com/tools/gzip-test.php)
2. Enter your site URL
3. If already compressed, don't enable theme GZIP

### Disable Features

**WordPress Embeds:**
- Removes oEmbed functionality
- Saves ~15KB JavaScript
- Removes REST API embed endpoint
- **Disable if:** You don't use WordPress embeds (linking between WP sites)

**Emoji Scripts:**
- Removes emoji detection script
- Saves ~10KB JavaScript
- Uses native browser emojis instead
- **Safe to disable:** ✅ Recommended for all sites

---

## 📊 Performance Tools

### Cache Tools

**Clear All Caches:**
- Deletes all transients
- Clears fragment cache
- Flushes object cache
- Updates file timestamps

**When to clear:**
- After theme updates
- After changing settings
- Debugging issues
- Stale content

### Testing Tools

**Recommended Services:**

**Google PageSpeed Insights:**
- Core Web Vitals scoring
- Mobile/Desktop performance
- Specific recommendations
- https://pagespeed.web.dev/

**GTmetrix:**
- Detailed waterfall chart
- Performance history
- Video playback
- https://gtmetrix.com/

**Pingdom:**
- Multiple test locations
- Simple interface
- Historical data
- https://tools.pingdom.com/

**WebPageTest:**
- Advanced testing
- Connection throttling
- Film strip view
- https://webpagetest.org/

### Performance Metrics

**Target Scores:**
| Metric | Good | Needs Improvement | Poor |
|--------|------|-------------------|------|
| LCP | <2.5s | 2.5-4s | >4s |
| FID | <100ms | 100-300ms | >300ms |
| CLS | <0.1 | 0.1-0.25 | >0.25 |
| FCP | <1.8s | 1.8-3s | >3s |
| TTI | <3.8s | 3.8-7.3s | >7.3s |

**Optimization Impact:**

| Feature | LCP | FID | CLS | FCP | TTI |
|---------|-----|-----|-----|-----|-----|
| WebP | ✅✅✅ | - | - | ✅✅ | ✅ |
| Lazy Loading | ✅✅ | - | ⚠️ | ✅✅✅ | ✅✅ |
| Critical CSS | ✅✅✅ | - | - | ✅✅✅ | ✅✅ |
| Defer JS | ✅ | ✅✅✅ | - | ✅✅ | ✅✅✅ |
| Fragment Cache | ✅✅ | ✅ | - | ✅✅ | ✅✅ |
| GZIP | ✅ | - | - | ✅ | ✅ |

---

## 🚀 Optimization Strategies

### For High-Traffic Sites

**Priority Settings:**
1. ✅ Fragment caching (600s - 3600s)
2. ✅ WebP conversion
3. ✅ Database auto-cleanup (weekly)
4. ✅ GZIP compression
5. ✅ Object cache (Redis/Memcached recommended)
6. ⚠️ Full-page caching (use plugin like WP Rocket)

**Additional Recommendations:**
- CDN (Cloudflare, BunnyCDN)
- Dedicated server or VPS
- PHP 8.1+ with OPcache
- MariaDB 10.5+

### For Content-Heavy Sites

**Priority Settings:**
1. ✅ WebP conversion (75-80% quality)
2. ✅ Enhanced lazy loading
3. ✅ Database cleanup (revisions!)
4. ✅ Image lazy loading
5. ✅ Fragment caching for queries

**Additional Recommendations:**
- Image optimization before upload (Photoshop, TinyPNG)
- Limit post revisions in wp-config.php
- Regular database maintenance

### For Developer/Portfolio Sites

**Priority Settings:**
1. ✅ Critical CSS
2. ✅ Defer JavaScript
3. ✅ WebP conversion
4. ✅ Minify HTML
5. ✅ Remove query strings

**Additional Recommendations:**
- Showcase performance scores
- Document optimization process
- Use as case study

---

## 🔍 Troubleshooting

### Issue: WebP images not generating

**Check:**
- PHP GD extension installed? (`php -m | grep gd`)
- WebP support enabled? (Performance → Images shows status)
- File permissions on uploads folder
- Try re-uploading an image

**Solution:**
```bash
# Check PHP GD WebP support
php -r "var_dump(function_exists('imagewebp'));"
# Should output: bool(true)
```

### Issue: Critical CSS breaks layout

**Cause:** Critical CSS too minimal or incorrect

**Solution:**
1. Disable critical CSS temporarily
2. Regenerate using your actual homepage
3. Include more above-the-fold styles
4. Test incrementally

### Issue: JavaScript errors after deferring

**Cause:** Scripts loading out of order

**Solution:**
1. Disable "Defer JavaScript"
2. Keep "Async JavaScript" enabled instead
3. Or exclude specific scripts (requires custom code)

### Issue: Database cleanup deletes content

**Cause:** Trash cleanup with short retention

**Solution:**
1. Increase "Cleanup Age" to 60+ days
2. Disable "Delete trashed posts" option
3. Manually review trash before cleanup

### Issue: GZIP already enabled warning

**This is normal!** Your server handles GZIP.

**Action:** Leave theme GZIP disabled

### Issue: Caching causes stale content

**Solution:**
- Reduce cache expiration time
- Clear caches after updates
- Use cache-busting for critical changes

---

## 📖 Developer API

### Fragment Caching Function

```php
/**
 * Cache a template part or expensive operation
 *
 * @param string $key Unique cache key
 * @param callable $callback Function to generate cached content
 * @param int $expiration Cache duration in seconds
 * @return string Cached or fresh content
 */
swg_cache_fragment( $key, $callback, $expiration = 3600 );
```

**Example 1: Cache widget output**
```php
echo swg_cache_fragment( 'footer-instagram', function() {
    // Fetch Instagram feed (expensive API call)
    $instagram = fetch_instagram_feed();
    include( locate_template( 'widgets/instagram.php' ) );
}, 3600 );
```

**Example 2: Cache query results**
```php
$popular = swg_cache_fragment( 'popular-posts-sidebar', function() {
    $query = new WP_Query( array(
        'posts_per_page' => 5,
        'meta_key' => 'views',
        'orderby' => 'meta_value_num'
    ) );
    
    while ( $query->have_posts() ) {
        $query->the_post();
        get_template_part( 'template-parts/content', 'popular' );
    }
    
    wp_reset_postdata();
}, 7200 );
```

### Clear Specific Cache

```php
// Clear specific fragment
delete_transient( 'swg_fragment_your-key' );

// Clear all fragment caches
global $wpdb;
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_swg_fragment_%'" );
```

### Check Cache Stats Programmatically

```php
$stats = SWGTheme_Performance::get_cache_stats();
// Returns: array(
//     'total_transients' => 1234,
//     'fragment_cache' => 45,
//     'object_cache' => 'Active' or 'Not Active'
// )
```

### Bypass Cache for Specific Users

```php
add_filter( 'swg_enable_fragment_cache', function( $enabled ) {
    if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
        return false; // Disable cache for editors
    }
    return $enabled;
} );
```

---

## 🎯 Best Practices

### DO:
✅ Test performance before and after each optimization
✅ Enable features incrementally
✅ Monitor Core Web Vitals regularly
✅ Use multiple testing tools
✅ Optimize images before upload
✅ Clear cache after major changes
✅ Document your optimization settings
✅ Keep WordPress/PHP/MySQL updated

### DON'T:
❌ Enable all features at once without testing
❌ Set cache expiration too high (>24 hours for dynamic content)
❌ Minify without checking for errors
❌ Ignore mobile performance
❌ Forget to test forms after optimization
❌ Disable features needed for functionality
❌ Over-optimize at expense of features

---

## 📈 Optimization Checklist

**Initial Setup:**
- [ ] Enable WebP conversion
- [ ] Enable enhanced lazy loading
- [ ] Enable fragment caching
- [ ] Configure database auto-cleanup
- [ ] Disable emojis
- [ ] Remove query strings
- [ ] Test baseline performance

**Advanced Setup:**
- [ ] Generate and inline critical CSS
- [ ] Configure CDN URL
- [ ] Add preload hints for fonts
- [ ] Enable JavaScript deferral
- [ ] Enable GZIP (if not server-enabled)
- [ ] Test all optimizations

**Ongoing Maintenance:**
- [ ] Monitor cache statistics weekly
- [ ] Review PageSpeed scores monthly
- [ ] Clear caches after major updates
- [ ] Run database cleanup monthly (or auto)
- [ ] Check for new optimization features

---

## Support

For performance issues:
1. Check browser console for errors
2. Test with all optimizations disabled
3. Enable features one at a time
4. Review error logs
5. Test on different devices/browsers

Expected improvements:
- **Page load time:** 30-60% faster
- **Database queries:** 20-40% reduction
- **Page size:** 40-70% smaller
- **Server load:** Significant reduction
- **PageSpeed score:** +20 to +40 points

---

**Last Updated:** January 4, 2026
**Theme Version:** 1.0.0
