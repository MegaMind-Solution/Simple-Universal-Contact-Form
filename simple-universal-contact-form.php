<?php
/*
Plugin Name: Simple Universal Contact Form
Plugin URI:  https://github.com/MegaMind-Solution/Simple-Universal-Contact-Form/wiki
Description: One universal contact form plugin with centralized backend, admin auto-detect, and full editor styling.
Version:     4.2.1
Author:      M Ramzan Ch
Author URI:  http://mramzanch.blogspot.com/
License:     GPL2
Text Domain: sucf
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/* ================================
   PLUGIN PATHS
================================ */
define('SUCF_PATH', plugin_dir_path(__FILE__));
define('SUCF_URL', plugin_dir_url(__FILE__));

/* ================================
   INCLUDE FILES
================================ */
// Core engine (frontend handling, email, validation)
require_once SUCF_PATH . 'frontend.php';

// Editor integration (Elementor / Gutenberg)
require_once SUCF_PATH . 'editor.php';

// Admin dashboard
require_once SUCF_PATH . 'dashboard.php';

/* ================================
   ENQUEUE ASSETS
================================ */
function sucf_enqueue_assets() {
    // Admin CSS
    if (is_admin()) {
        wp_enqueue_style('sucf-admin-css', SUCF_URL . 'assets/admin.css');
    }

    // Frontend CSS
    wp_enqueue_style('sucf-frontend-css', SUCF_URL . 'assets/frontend.css');

    // Frontend JS
    wp_enqueue_script('sucf-script', SUCF_URL . 'assets/script.js', ['jquery'], null, true);
}
add_action('admin_enqueue_scripts', 'sucf_enqueue_assets');
add_action('wp_enqueue_scripts', 'sucf_enqueue_assets');

/* ================================
   PLUGIN ACTIVATION / DEACTIVATION
================================ */
register_activation_hook(__FILE__, 'sucf_activate');
register_deactivation_hook(__FILE__, 'sucf_deactivate');

function sucf_activate() {
    if (!get_option('sucf_settings')) {
        $defaults = [
            'recipient_user' => '',
            'admin_phone' => '',
            'from_name' => get_bloginfo('name'),
            'reply_to' => get_bloginfo('admin_email'),
            'success_message' => 'Thank you! Your message has been sent.',
            'error_message' => 'Something went wrong. Please try again.',
            'enable_honeypot' => 1,
            'enable_logging' => 0,
        ];
        add_option('sucf_settings', $defaults);
    }
}

function sucf_deactivate() {
    // Optional: remove logs
    // delete_option('sucf_logs');
}
