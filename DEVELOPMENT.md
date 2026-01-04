# SWG Theme Development Guide

## Quick Start

### Prerequisites
- PHP 8.0+
- Node.js 16+
- WordPress 5.0+
- Composer (optional)

### Setup

1. **Clone/Download the theme:**
   ```bash
   cd wp-content/themes/swgtheme
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Enable development mode:**
   Add to `wp-config.php`:
   ```php
   define( 'WP_DEBUG', true );
   define( 'WP_DEBUG_LOG', true );
   define( 'WP_DEBUG_DISPLAY', false );
   define( 'SAVEQUERIES', true );
   define( 'SCRIPT_DEBUG', true );
   define( 'SWGTHEME_DEV_MODE', true );
   ```

### Development Workflow

**Watch mode (recommended):**
```bash
npm run dev
```

**Build for production:**
```bash
npm run build
```

**Lint code:**
```bash
npm run lint
```

**Format code:**
```bash
npm run format
```

**Create release zip:**
```bash
npm run zip
```

## Debug Tools

### PHP Debug Helpers

#### Available Functions:
```php
// Dump and die (only in dev mode)
swg_dd( $variable, $another_var );

// Dump without dying
swg_dump( $variable );

// Debug log with context
swg_log( 'Message', $data, 'Context' );

// Performance timing
swg_timer_start( 'query-time' );
// ... code to time
$elapsed = swg_timer_end( 'query-time' ); // Logs automatically

// Check if dev mode
if ( swg_is_dev() ) {
    // Dev-only code
}
```

#### Developer Tools Class:
```php
// System information
SWGTheme_Dev_Tools::system_info();

// Query analysis (requires SAVEQUERIES)
SWGTheme_Dev_Tools::analyze_queries();

// Display registered hooks
SWGTheme_Dev_Tools::display_hooks( 'wp_enqueue_scripts' );

// Show current template
SWGTheme_Dev_Tools::show_template_hierarchy();

// Memory usage
echo SWGTheme_Dev_Tools::get_memory_usage();
echo SWGTheme_Dev_Tools::get_memory_usage( true ); // Peak memory
```

### JavaScript Debug Toolbar

Press **Ctrl+Shift+D** to toggle the debug toolbar.

**Features:**
- Console log viewer
- Network request monitor
- Performance metrics
- System information
- Memory usage tracking

**Console Methods:**
```javascript
console.log('Message');   // Captured in toolbar
console.error('Error');   // Highlighted in red
console.warn('Warning');  // Highlighted in yellow
```

**Access Metrics:**
```javascript
// Get all web vitals
const metrics = window.getWebVitalsMetrics();

// Check if debug mode
if (window.debugToolbar) {
    window.debugToolbar.show();
}
```

## Code Quality

### ESLint (JavaScript)
```bash
npm run lint:js
```

**Auto-fix:**
```bash
npx eslint js/**/*.js --fix
```

### StyleLint (CSS)
```bash
npm run lint:css
```

**Auto-fix:**
```bash
npx stylelint css/**/*.css --fix
```

### Prettier (Formatting)
```bash
npm run format
```

## VS Code Integration

### Recommended Extensions
Install all recommended extensions:
- PHP Intelephense
- ESLint
- StyleLint
- Prettier
- WordPress Toolbox
- PHP Debug (XDebug)

### Code Snippets

Type these prefixes and press Tab:

**PHP:**
- `wpfunc` - WordPress function with PHPDoc
- `wpaction` - Add action hook
- `wpfilter` - Add filter hook
- `wpajax` - Complete AJAX handler
- `wprest` - REST API route
- `wpcpt` - Custom post type
- `wptax` - Custom taxonomy
- `wpquery` - WP_Query loop
- `swglog` - Debug log
- `swgdd` - Dump and die
- `swgtimer` - Performance timer

**JavaScript:**
(Similar patterns available for JS development)

### Debugging with XDebug

1. Install XDebug for PHP
2. Configure in `php.ini`:
   ```ini
   zend_extension=xdebug
   xdebug.mode=debug
   xdebug.start_with_request=yes
   xdebug.client_port=9003
   ```
3. Press F5 in VS Code to start debugging
4. Set breakpoints by clicking line numbers

## File Structure

```
swgtheme/
├── .vscode/               # VS Code configuration
├── css/                   # Stylesheets
├── js/                    # JavaScript files
├── includes/              # PHP includes
│   ├── dev-helpers.php   # Development utilities
│   └── modern-helpers.php # Modern PHP helpers
├── blocks/                # Gutenberg blocks
├── patterns/              # Block patterns
├── dist/                  # Build output (gitignored)
├── node_modules/          # NPM packages (gitignored)
├── .eslintrc.json        # ESLint config
├── .stylelintrc.json     # StyleLint config
├── .prettierrc.json      # Prettier config
├── webpack.config.js     # Webpack build config
├── package.json          # NPM dependencies
├── functions.php         # Theme functions
└── style.css             # Theme info
```

## Best Practices

### PHP

1. **Always sanitize input:**
   ```php
   $value = isset( $_POST['key'] ) ? sanitize_text_field( $_POST['key'] ) : '';
   ```

2. **Always escape output:**
   ```php
   echo esc_html( $text );
   echo esc_url( $url );
   echo esc_attr( $attribute );
   ```

3. **Use nonces for security:**
   ```php
   wp_nonce_field( 'action_name', 'nonce_name' );
   check_ajax_referer( 'action_name', 'nonce_name' );
   ```

4. **Check capabilities:**
   ```php
   if ( ! current_user_can( 'edit_posts' ) ) {
       wp_die( 'Unauthorized' );
   }
   ```

5. **Use WordPress functions:**
   ```php
   // Good
   wp_safe_redirect( home_url() );
   
   // Bad
   header( 'Location: /' );
   ```

### JavaScript

1. **Use strict mode:**
   ```javascript
   'use strict';
   ```

2. **Avoid global pollution:**
   ```javascript
   (function() {
       // Your code in IIFE
   })();
   ```

3. **Check dependencies:**
   ```javascript
   if (typeof jQuery === 'undefined') {
       console.error('jQuery not loaded');
       return;
   }
   ```

4. **Handle errors:**
   ```javascript
   try {
       // Code
   } catch (error) {
       console.error('Error:', error);
   }
   ```

### CSS

1. **Use CSS custom properties:**
   ```css
   :root {
       --primary-color: #dc3545;
   }
   ```

2. **Mobile-first approach:**
   ```css
   /* Base styles */
   .element { }
   
   /* Tablet and up */
   @media (min-width: 768px) { }
   ```

3. **Use BEM methodology:**
   ```css
   .block { }
   .block__element { }
   .block--modifier { }
   ```

## Performance Tips

1. **Minimize database queries:**
   ```php
   // Cache results
   $result = wp_cache_get( 'key' );
   if ( false === $result ) {
       $result = expensive_query();
       wp_cache_set( 'key', $result, '', 3600 );
   }
   ```

2. **Use transients for temporary data:**
   ```php
   set_transient( 'key', $data, DAY_IN_SECONDS );
   $data = get_transient( 'key' );
   ```

3. **Lazy load images:**
   ```html
   <img src="placeholder.jpg" data-src="actual.jpg" loading="lazy">
   ```

4. **Defer non-critical JavaScript:**
   ```php
   wp_enqueue_script( 'handle', $url, array(), $ver, true );
   ```

## Testing

### Manual Testing Checklist

- [ ] Test in development mode
- [ ] Test in production mode
- [ ] Test with WP_DEBUG enabled
- [ ] Test all AJAX endpoints
- [ ] Test responsive design
- [ ] Test accessibility (WCAG 2.1 AA)
- [ ] Test browser compatibility
- [ ] Test PWA functionality
- [ ] Run Lighthouse audit
- [ ] Check Web Vitals scores

### Browser Testing

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile Safari (iOS)
- Chrome Mobile (Android)

## Troubleshooting

### Common Issues

**White screen of death:**
1. Check error logs: `wp-content/debug.log`
2. Disable plugins temporarily
3. Check for PHP syntax errors

**JavaScript not working:**
1. Check browser console for errors
2. Verify jQuery is loaded
3. Check script enqueue order

**Styles not applying:**
1. Hard refresh (Ctrl+Shift+R)
2. Clear browser cache
3. Check CSS specificity

**Performance issues:**
```php
// Enable query monitoring
define( 'SAVEQUERIES', true );
SWGTheme_Dev_Tools::analyze_queries();
```

## Additional Resources

- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [Theme Developer Handbook](https://developer.wordpress.org/themes/)
- [REST API Handbook](https://developer.wordpress.org/rest-api/)
- [Block Editor Handbook](https://developer.wordpress.org/block-editor/)

## Support

For development questions:
1. Check this guide
2. Review code comments
3. Use debug tools
4. Check error logs

---

**Happy Coding!** 🚀
