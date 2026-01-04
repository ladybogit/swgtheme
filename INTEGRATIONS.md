# Third-Party Integrations

## Overview

The SWG Theme includes comprehensive integration support for popular third-party services and WordPress plugins. This document covers setup, configuration, and usage of all available integrations.

## Table of Contents

1. [Newsletter Integration (Mailchimp)](#newsletter-integration)
2. [Discord Webhook Notifications](#discord-notifications)
3. [Social Media Feeds](#social-media-feeds)
4. [Contact Form 7 Compatibility](#contact-form-7)
5. [Gravity Forms Compatibility](#gravity-forms)
6. [bbPress Forum Integration](#bbpress-integration)

---

## Newsletter Integration

### Mailchimp API Setup

1. **Get API Key:**
   - Log in to [Mailchimp](https://mailchimp.com)
   - Navigate to Account → Extras → API keys
   - Generate a new API key
   - Copy the key (format: `abc123...xyz-us1`)

2. **Get List/Audience ID:**
   - Go to Audience → Settings
   - Click "Audience name and defaults"
   - Copy the Audience ID (format: `a1b2c3d4e5`)

3. **Configure in WordPress:**
   - Go to `Appearance → Integrations → Newsletter`
   - Paste your API Key
   - Paste your List ID
   - Click "Save Integration Settings"

### Usage

**Widget:**
1. Go to `Appearance → Widgets`
2. Add "SWG Newsletter Signup" widget to any sidebar
3. Configure:
   - Title: "Subscribe to Newsletter"
   - Description: Optional text
   - Button Text: "Subscribe"
   - Show name field: Optional checkbox

**Shortcode:**
```
[swg_newsletter title="Subscribe" description="Get updates" button_text="Sign Up" show_name="1"]
```

**Parameters:**
- `title` - Form heading
- `description` - Optional description text
- `button_text` - Submit button label
- `show_name` - Include name field (1=yes, 0=no)

### Features

- ✅ AJAX form submission (no page reload)
- ✅ Email validation
- ✅ Double opt-in support
- ✅ Success/error messaging
- ✅ Responsive design
- ✅ Optional name field

---

## Discord Notifications

### Webhook Setup

1. **Create Discord Webhook:**
   - Open Discord server settings
   - Go to Integrations → Webhooks
   - Click "New Webhook"
   - Name it (e.g., "WordPress Updates")
   - Select the channel
   - Copy the Webhook URL

2. **Configure in WordPress:**
   - Go to `Appearance → Integrations → Discord`
   - Paste Webhook URL
   - Enable desired notifications:
     - ☐ Notify when new posts are published
     - ☐ Notify when new comments are posted
   - Click "Save Integration Settings"

### Notification Types

**New Post Notification:**
- Post title with link
- Featured image (if available)
- Author name
- Excerpt
- Rich embed formatting

**New Comment Notification:**
- Comment author
- Post title with link
- Comment text
- Direct link to comment

### Customization

Edit `includes/integrations.php` to customize:
- Embed colors
- Message formatting
- Included fields
- Footer text

---

## Social Media Feeds

### Twitch Stream Status

**API Setup:**
1. Create app at [Twitch Developer Console](https://dev.twitch.tv/console)
2. Get Client ID and Access Token
3. Configure in `Appearance → Integrations → Social Media`

**Widget Usage:**
1. Add "SWG Twitch Stream Status" widget
2. Configure:
   - Title: "Live Stream"
   - Twitch Username: `yourusername`
   - Show preview image: ☑

**Features:**
- Live/Offline status indicator
- Stream title and game
- Viewer count (when live)
- Preview thumbnail (optional)
- 5-minute caching

### YouTube Videos

**API Setup:**
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Enable YouTube Data API v3
3. Create API key
4. Configure in `Appearance → Integrations → Social Media`

**Widget Usage:**
1. Add "SWG YouTube Videos" widget
2. Configure:
   - Title: "Latest Videos"
   - Channel ID: `UC...` (find in YouTube Studio)
   - Number of Videos: 3
   - Show thumbnails: ☑

**Features:**
- Latest videos from channel
- Thumbnails with play overlay
- Video titles and descriptions
- Relative timestamps ("2 days ago")
- 1-hour caching

---

## Contact Form 7

### Setup

1. Install [Contact Form 7](https://wordpress.org/plugins/contact-form-7/)
2. Go to `Appearance → Integrations → Plugin Compatibility`
3. Enable "Use custom theme styles for Contact Form 7"
4. Click "Save Integration Settings"

### Features

The theme automatically applies custom styling to CF7 forms:

- ✅ Theme-consistent form fields
- ✅ Custom button styles
- ✅ Validation message styling
- ✅ Responsive layout
- ✅ Focus states
- ✅ Error highlighting

### CSS Classes

The integration adds these classes automatically:
- `.wpcf7-form` - Main form wrapper
- `.wpcf7-form-control` - Input fields
- `.wpcf7-submit` - Submit button
- `.wpcf7-response-output` - Messages

### Customization

To override styles, add CSS to `style.css`:
```css
.wpcf7-form input[type="text"] {
    border-color: #your-color;
}
```

---

## Gravity Forms

### Setup

1. Install [Gravity Forms](https://www.gravityforms.com/) (premium)
2. Integration is automatic - no configuration needed
3. Optional: Disable in `Appearance → Integrations → Plugin Compatibility`

### Features

- ✅ Custom submit button styling
- ✅ Theme color integration
- ✅ Responsive form layouts
- ✅ Validation styling
- ✅ Progress bar styling (multi-page forms)

### Customization

The theme hooks into:
- `gform_submit_button` - Button HTML customization
- Custom CSS in `css/integrations.css`

To modify button text or classes:
```php
add_filter('gform_submit_button', function($button, $form) {
    return '<button class="btn btn-custom">Send</button>';
}, 10, 2);
```

---

## bbPress Integration

### Setup

1. Install [bbPress](https://wordpress.org/plugins/bbpress/)
2. Go to `Appearance → Integrations → Plugin Compatibility`
3. Enable "Use custom theme styles for bbPress forums"
4. Click "Save Integration Settings"

### Features

The theme integrates bbPress forum styling:

- ✅ Forum list styling
- ✅ Topic and reply formatting
- ✅ User profile integration
- ✅ Subscribe/favorite buttons
- ✅ Breadcrumb navigation
- ✅ Search form styling

### Forum Elements Styled

- Forum containers
- Topic lists
- Reply threads
- User badges
- Meta information
- Pagination
- Subscription buttons
- Search forms

### Customization

Override bbPress styles in `style.css`:
```css
#bbpress-forums li.bbp-header {
    background: #your-color;
}
```

---

## API Rate Limits & Caching

### Caching Strategy

All API integrations use WordPress transients for caching:

| Integration | Cache Duration | Transient Key |
|------------|----------------|---------------|
| Twitch     | 5 minutes      | `swg_twitch_stream_{username}` |
| YouTube    | 1 hour         | `swg_youtube_videos_{channel_id}` |
| Mailchimp  | No cache       | - |
| Discord    | No cache       | - |

### Clear Cache

To manually clear caches:

**Via WordPress:**
```php
delete_transient('swg_twitch_stream_username');
delete_transient('swg_youtube_videos_channelid');
```

**Via Database:**
```sql
DELETE FROM wp_options 
WHERE option_name LIKE '%transient%swg_%';
```

### Rate Limits

| Service   | Limit | Notes |
|-----------|-------|-------|
| Mailchimp | 10/sec | Per API key |
| Twitch    | 800/min | Per client ID |
| YouTube   | 10,000/day | Quota units |
| Discord   | 30/min | Per webhook URL |

---

## Troubleshooting

### Mailchimp

**Issue:** "Invalid API Key"
- Verify API key format: `abc-us1`
- Check datacenter suffix matches (`us1`, `us2`, etc.)
- Regenerate API key if needed

**Issue:** "List not found"
- Verify Audience ID is correct
- Check account permissions

### Discord

**Issue:** Webhook not working
- Verify webhook URL is complete
- Check channel permissions
- Test webhook with curl:
  ```bash
  curl -X POST webhook-url -H "Content-Type: application/json" -d '{"content":"test"}'
  ```

### Twitch

**Issue:** "Stream not found"
- Verify username is correct (case-sensitive)
- Check API credentials
- Ensure access token is valid

### YouTube

**Issue:** "Quota exceeded"
- YouTube has daily quotas
- Wait until quota resets (midnight Pacific Time)
- Increase cache duration

### Contact Form 7

**Issue:** Styles not applying
- Ensure CF7 is activated
- Check setting in Integrations panel
- Clear browser cache
- Verify `integrations.css` is loading

---

## Security Notes

### API Key Storage

All API keys are stored in WordPress options:
- Keys are sanitized on save
- Access requires `manage_options` capability
- Database is WordPress-secured

### Best Practices

1. **Rotate API keys regularly**
2. **Use environment-specific keys** (dev/staging/prod)
3. **Restrict webhook permissions** (Discord)
4. **Monitor API usage** (quota tracking)
5. **Use SSL/HTTPS** for all API calls

### GDPR Compliance

When using newsletter integration:
- Inform users about data collection
- Provide privacy policy link
- Use double opt-in
- Respect unsubscribe requests
- Document data processing

---

## Developer Reference

### Filter Hooks

```php
// Modify Discord post notification
add_filter('swg_discord_post_embed', function($embed, $post_id) {
    $embed['color'] = 0xFF0000; // Red color
    return $embed;
}, 10, 2);

// Customize Mailchimp merge fields
add_filter('swg_mailchimp_merge_fields', function($fields, $email, $name) {
    $fields['FNAME'] = $name;
    $fields['SOURCE'] = 'Website';
    return $fields;
}, 10, 3);
```

### Action Hooks

```php
// After successful newsletter signup
add_action('swg_newsletter_subscribed', function($email, $name) {
    error_log("New subscriber: $email");
}, 10, 2);

// Before Discord notification
add_action('swg_before_discord_notify', function($post_id) {
    // Custom logic
}, 10, 1);
```

### Direct API Usage

```php
// Get Twitch stream status
$integrations = new SWGTheme_Integrations();
$stream = $integrations->get_twitch_stream_status('username');

if ($stream && $stream['is_live']) {
    echo "Live with {$stream['viewer_count']} viewers";
}

// Subscribe to Mailchimp
$result = $integrations->subscribe_to_mailchimp('email@example.com', 'John Doe');
```

---

## Support

For integration issues:
1. Check API credentials
2. Review error logs (`wp-content/debug.log`)
3. Test API endpoints directly
4. Consult service documentation:
   - [Mailchimp API](https://mailchimp.com/developer/)
   - [Discord Webhooks](https://discord.com/developers/docs/resources/webhook)
   - [Twitch API](https://dev.twitch.tv/docs/api/)
   - [YouTube Data API](https://developers.google.com/youtube/v3)

---

## Changelog

### Version 1.0.0
- Initial integration system
- Mailchimp newsletter support
- Discord webhook notifications
- Twitch stream status
- YouTube video feeds
- Contact Form 7 styling
- Gravity Forms support
- bbPress forum integration
