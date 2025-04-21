<?php
if (!defined('ABSPATH')) exit;

// Add Dashboard Submenu
add_action('admin_menu', function () {
    add_submenu_page(
        'options-general.php',
        'Jeevsutra Chatbot Analytics',
        'Chatbot Analytics',
        'manage_options',
        'jeevsutra-chatbot-analytics',
        'jeevsutra_render_analytics_dashboard'
    );
});

// Dashboard UI
function jeevsutra_render_analytics_dashboard() {
    $logs = get_option('jeevsutra_chat_logs', []);
    $fallbacks = get_option('jeevsutra_fallback_logs', []);

    $intent_counts = [];
    foreach ($logs as $entry) {
        $key = $entry['matched'];
        if (!isset($intent_counts[$key])) $intent_counts[$key] = 0;
        $intent_counts[$key]++;
    }

    arsort($intent_counts);
    ?>
    <div class="wrap">
        <h1>📊 Jeevsutra Chatbot Analytics</h1>

        <h2>Top Matched Intents</h2>
        <ul>
            <?php foreach ($intent_counts as $intent => $count): ?>
                <li><strong><?= esc_html($intent) ?>:</strong> <?= esc_html($count) ?> messages</li>
            <?php endforeach; ?>
        </ul>

        <hr>

        <h2>Unmatched / Fallback Queries</h2>
        <ul style="max-height:200px; overflow:auto;">
            <?php foreach (array_reverse($fallbacks) as $entry): ?>
                <li><code>[<?= esc_html($entry['time']) ?>]</code> <?= esc_html($entry['query']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}
