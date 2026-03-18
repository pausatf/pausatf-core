<?php
/*
 * Plugin Name: PAUSATF Core
 * Description: Widget visibility override for pausatf.org
 * Version: 1.1.0
 * Requires PHP: 8.1
 * Author: Thomas Vincent
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Widget-61 (Reno Indoor Track Schedule) fails under widget-logic
// due to a lifecycle race with is_page() on PHP-FPM + event MPM.
// This override shows it only on the indoor track pages.
add_filter('widget_display_callback', function ($instance, $widget, $args) {
    if (is_admin()) {
        return $instance;
    }

    $id = $args['widget_id'] ?? '';

    $overrides = [
        'custom_html-61' => [70819, 71502, 71472, 70939, 73880],
    ];

    if (isset($overrides[$id])) {
        return is_page($overrides[$id]) ? $instance : false;
    }

    return $instance;
}, 10, 3);
