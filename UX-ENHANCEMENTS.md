# User Experience Enhancements

## Overview
Complete User Experience enhancement package for the swgtheme WordPress theme including PWA support, performance monitoring, mobile optimizations, and modern UI/UX features.

## Features Implemented

### 1. Progressive Web App (PWA) Support
- **manifest.json**: Complete PWA manifest with icons, theme colors, and shortcuts
- **Service Worker (sw.js)**: Offline functionality with caching strategies
  - Static asset caching
  - Dynamic content caching
  - Image optimization caching
  - Network-first strategy for HTML
  - Cache-first strategy for assets
  - Background sync support
  - Push notification support
- **PWA Initialization (pwa-init.js)**: Service worker registration and install prompts
- **offline.html**: Dedicated offline fallback page with connection monitoring

**Files Created:**
- `/wp-content/themes/swgtheme/manifest.json`
- `/wp-content/themes/swgtheme/sw.js`
- `/wp-content/themes/swgtheme/js/pwa-init.js`
- `/offline.html`

### 2. Web Vitals Monitoring
- **Core Web Vitals Tracking**:
  - LCP (Largest Contentful Paint)
  - FID (First Input Delay)
  - CLS (Cumulative Layout Shift)
  - TTFB (Time to First Byte)
  - FCP (First Contentful Paint)
  - INP (Interaction to Next Paint)
- Google Analytics integration
- WordPress AJAX tracking
- Visual indicators in development mode
- Performance Observer for long tasks and layout shifts

**Files Created:**
- `/wp-content/themes/swgtheme/js/web-vitals.js`

### 3. Enhanced Mobile Responsiveness
- **Touch Gesture Support**:
  - Swipe left/right for menu control
  - Swipe up/down navigation
  - Pull-to-refresh functionality
- **Mobile Optimizations**:
  - Touch target size optimization (min 44x44px)
  - Fast-click implementation
  - Orientation change handling
  - Mobile menu improvements
- **Responsive Breakpoint Indicators**: Visual debugging tool

**Files Created:**
- `/wp-content/themes/swgtheme/js/mobile-enhancements.js`

### 4. Toast Notification System
- **Features**:
  - Success, error, warning, and info notifications
  - Auto-dismiss with configurable duration
  - Queue management (max 5 concurrent)
  - Progress bar indicators
  - Dark mode support
  - ARIA accessibility
  - Mobile responsive
- **Global API**: `window.toast.success()`, `toast.error()`, `toast.warning()`, `toast.info()`

**Files Created:**
- `/wp-content/themes/swgtheme/js/toast.js`

### 5. Loading States and Skeleton Screens
- **Skeleton Screens**: Pre-rendered placeholders for content
  - Post skeletons
  - Card skeletons
  - List skeletons
  - Text skeletons
- **Loading Indicators**:
  - Button loading states
  - Image loading with fade-in
  - Form submission states
  - Global progress bar for AJAX requests
- **Loading Overlays**: Full or partial page loading states

**Files Created:**
- `/wp-content/themes/swgtheme/js/loading-states.js`

### 6. Smooth Scrolling and Animations
- **Scroll Animations**:
  - Fade in
  - Slide up/left/right
  - Scale animations
  - Stagger delays for sequential animations
- **Parallax Effects**: Data-attribute driven parallax scrolling
- **Counter Animations**: Number count-up effects
- **Micro-Interactions**:
  - Button ripple effects
  - Hover lift/scale/glow effects
  - Smooth anchor scrolling
- **Scroll Progress Indicator**: Visual page scroll progress
- **Scroll-to-Top Button**: Appears after 300px scroll

**Files Created:**
- `/wp-content/themes/swgtheme/js/smooth-animations.js`

## WordPress Integration

### Functions Added to functions.php

#### PWA Functions
- `swgtheme_pwa_support()`: Adds manifest and meta tags
- `swgtheme_ensure_offline_page()`: Validates offline page existence

#### Performance Functions
- `swgtheme_enqueue_ux_scripts()`: Enqueues all UX enhancement scripts
- `swgtheme_track_web_vitals()`: AJAX handler for web vitals tracking
- `swgtheme_get_web_vitals_data()`: Retrieves stored vitals data
- `swgtheme_resource_hints()`: Adds preconnect/DNS-prefetch hints
- `swgtheme_disable_emojis()`: Removes WordPress emoji scripts
- `swgtheme_critical_css()`: Inlines critical CSS for front page
- `swgtheme_lazy_load_videos()`: Adds lazy loading to iframes

#### UX Functions
- `swgtheme_viewport_meta()`: Optimized viewport settings
- `swgtheme_add_animation_classes()`: Auto-adds animation classes to posts
- `swgtheme_search_autocomplete_data()`: Provides search suggestions
- `swgtheme_ux_body_classes()`: Adds device/browser detection classes

## Usage Examples

### Toast Notifications
```javascript
// Success notification
window.toast.success('Post saved successfully!');

// Error with custom title
window.toast.error('Failed to save post', 'Save Error');

// Custom notification
window.toast.show({
	type: 'info',
	title: 'New Message',
	message: 'You have 3 new messages',
	duration: 5000,
	closable: true
});
```

### Loading States
```javascript
// Show spinner
const spinner = window.loadingStates.showSpinner(element);

// Show overlay
const overlay = window.loadingStates.showOverlay(container);

// Hide when done
window.loadingStates.hideSpinner(spinner);
window.loadingStates.hideOverlay(overlay);
```

### Animations
```javascript
// Animate element
window.smoothAnimations.animateElement(element, 'slide-up');

// Observe element for scroll animation
window.smoothAnimations.observeElement(element);
```

### HTML Animation Classes
```html
<!-- Fade in on scroll -->
<div class="animate-fade-in">Content</div>

<!-- Slide up with delay -->
<div class="animate-slide-up animate-stagger-1">Content</div>

<!-- Parallax background -->
<div data-parallax="0.5">Background</div>

<!-- Counter animation -->
<span data-counter="1000" data-duration="2000">0</span>

<!-- Skeleton screen -->
<div data-skeleton="post"></div>
```

## Performance Impact

### Optimizations Included
1. **Lazy Loading**: Images and iframes load on demand
2. **Caching**: Service worker caches static assets
3. **Minification**: Scripts loaded in footer
4. **Preconnect**: DNS prefetch for external resources
5. **Critical CSS**: Inline critical styles
6. **Reduced Motion**: Respects user preferences

### Web Vitals Targets
- LCP: < 2.5s (Good)
- FID: < 100ms (Good)
- CLS: < 0.1 (Good)
- TTFB: < 800ms (Good)

## Browser Support
- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support (iOS 11.3+)
- Mobile browsers: Optimized experience

## Accessibility Features
- ARIA labels and landmarks
- Keyboard navigation support
- Screen reader announcements
- Reduced motion support
- High contrast mode compatible
- Touch target minimum sizes (44x44px)

## Development Tools

### Debug Mode Features
- Web Vitals visual indicators (localhost only)
- Breakpoint indicators (enable with `body.show-breakpoints`)
- Console logging for performance metrics
- Layout shift warnings
- Long task detection

### Testing PWA
1. Open DevTools → Application → Service Workers
2. Check "Offline" mode
3. Reload page - should show offline.html
4. Lighthouse audit for PWA score

## Future Enhancements
- Push notification subscription UI
- Background sync for form submissions
- App shortcuts customization
- Share API integration
- Install prompt customization
- A/B testing framework

## Requirements
- WordPress 5.0+
- PHP 8.0+
- Modern browser with Service Worker support
- HTTPS (required for PWA features)

## Installation Notes
1. All scripts auto-enqueue via `swgtheme_enqueue_ux_scripts()`
2. Service worker registered at `/wp-content/themes/swgtheme/sw.js`
3. Manifest accessible at `/wp-content/themes/swgtheme/manifest.json`
4. Icons required in `/wp-content/themes/swgtheme/images/` (various sizes)
5. Offline page at `/offline.html` in WordPress root

## Icon Requirements for PWA
Create these icon sizes in `/wp-content/themes/swgtheme/images/`:
- icon-72x72.png
- icon-96x96.png
- icon-128x128.png
- icon-144x144.png
- icon-152x152.png
- icon-192x192.png
- icon-384x384.png
- icon-512x512.png

## Maintenance
- Service worker cache version updates in `sw.js` (CACHE_VERSION)
- Monitor web vitals data via `swgtheme_get_web_vitals_data()`
- Clear transients periodically for vitals data
- Update manifest.json shortcuts as needed
