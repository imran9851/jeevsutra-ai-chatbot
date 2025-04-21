<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$keys = [
    'jeevsutra_bot_name',
    'jeevsutra_default_lang',
    'jeevsutra_chatbot_flow_json',
    'jeevsutra_chat_logs',
    'jeevsutra_fallback_logs',
    'jeevsutra_captured_leads',
];

foreach ($keys as $key) {
    delete_option($key);
}
