<?php
/*
 * Plugin Name: PAUSATF Core
 * Description: Security hardening and widget visibility for pausatf.org
 * Version: 1.0.0
 * Requires PHP: 8.1
 * Author: Thomas Vincent
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Classic widget editor (block widget UI unused on this site)
add_filter('use_widgets_block_editor', '__return_false');

// --- Security ---

// Block REST API user enumeration
add_filter('rest_endpoints', function (array $endpoints): array {
    unset($endpoints['/wp/v2/users']);
    unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    return $endpoints;
});

// Strip xmlrpc discovery from <head>
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

// Audit trail: log widget option changes
add_action('updated_option', function (string $option, $old, $new): void {
    if (str_starts_with($option, 'widget_') || $option === 'sidebars_widgets') {
        $user = wp_get_current_user();
        $who = $user->ID ? $user->user_login : 'system';
        error_log(sprintf(
            'widget change: %s by %s (%d -> %d bytes)',
            $option,
            $who,
            strlen(maybe_serialize($old)),
            strlen(maybe_serialize($new))
        ));
    }
}, 10, 3);

// --- Widget Visibility ---
// Replaces widget-logic for widget-61 (Reno Indoor Track Schedule)
// which fails under widget-logic's custom parser due to a lifecycle
// race condition with is_page() on PHP-FPM + event MPM.
//
// All other widgets use widget-logic's editor-managed rules.

add_filter('widget_display_callback', function ($instance, $widget, $args) {
    if (is_admin()) {
        return $instance;
    }

    $id = $args['widget_id'] ?? '';

    // Code overrides for widgets that break under widget-logic
    $overrides = [
        'custom_html-61' => [70819, 71502, 71472, 70939, 73880], // indoor track pages
    ];

    if (isset($overrides[$id])) {
        return is_page($overrides[$id]) ? $instance : false;
    }

    return $instance;
}, 10, 3);

// --- WebP ---
// Serve .webp when the browser supports it and the file exists.
// For images loaded via wp_get_attachment_url only; hardcoded <img>
// tags in widget HTML need an Apache RewriteRule (see .htaccess).

add_filter('wp_get_attachment_url', function (string $url): string {
    if (empty($_SERVER['HTTP_ACCEPT']) || !str_contains($_SERVER['HTTP_ACCEPT'], 'image/webp')) {
        return $url;
    }
    if (!preg_match('/\.(jpe?g|png)$/i', $url)) {
        return $url;
    }
    $path = ABSPATH . str_replace(home_url('/'), '', $url) . '.webp';
    return file_exists($path) ? $url . '.webp' : $url;
});
