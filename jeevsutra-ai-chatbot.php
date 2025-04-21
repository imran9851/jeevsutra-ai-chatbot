<?php
/*
Plugin Name: Jeevsutra AI Chatbot
Description: AI-powered chatbot plugin for Jeevsutra (Vastu, Astrology, Healing).
Version: 1.0.0
Author: Acharya Imran (Jeevsutra Pvt. Ltd.)
Author URI: https://www.jeevsutra.com
License: GPLv2 or later
GitHub Plugin URI: https://github.com/imran9851/jeevsutra-ai-chatbot
GitHub Branch: main

*/

defined('ABSPATH') || exit;

// Load plugin files
require_once plugin_dir_path(__FILE__) . 'includes/flow-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/analytics-dashboard.php';

// Load assets
function jeevsutra_chatbot_assets() {
    wp_enqueue_style('jeevsutra-chatbot-css', plugin_dir_url(__FILE__) . 'assets/css/chatbot-style.css');
    wp_enqueue_script('jeevsutra-chatbot-js', plugin_dir_url(__FILE__) . 'assets/js/chatbot-ui.js', [], null, true);

    wp_localize_script('jeevsutra-chatbot-js', 'JeevsutraBot', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'bot_name' => get_option('jeevsutra_bot_name', 'Jeevsutra AI'),
        'lang_default' => get_option('jeevsutra_default_lang', 'bn'),
    ]);
}
add_action('wp_enqueue_scripts', 'jeevsutra_chatbot_assets');

// Frontend Output
function jeevsutra_chatbot_output() {
    echo '<div id="jeevsutra-chatbot-container"></div>';
}
add_action('wp_footer', 'jeevsutra_chatbot_output');

// Ajax Endpoint
add_action('wp_ajax_jeevsutra_chatbot_ask', 'jeevsutra_chatbot_ask');
add_action('wp_ajax_nopriv_jeevsutra_chatbot_ask', 'jeevsutra_chatbot_ask');

function jeevsutra_chatbot_ask() {
    $message = sanitize_text_field($_POST['message'] ?? '');
    $lang = sanitize_text_field($_POST['lang'] ?? 'bn');

    $response = '🔮 This is a sample AI response to: ' . $message;
    wp_send_json_success(['reply' => $response]);
}
