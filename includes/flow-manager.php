<?php
if (!defined('ABSPATH')) exit;

// Admin menu: Flow Builder page
add_action('admin_menu', function () {
    add_submenu_page(
        'options-general.php',
        'Jeevsutra Chatbot Flow',
        'Chatbot Flow Builder',
        'manage_options',
        'jeevsutra-chatbot-flow',
        'jeevsutra_render_flow_ui'
    );
});

// Register option to store chatbot flow JSON
add_action('admin_init', function () {
    register_setting('jeevsutra_flow_group', 'jeevsutra_chatbot_flow_json');
});

// Render Flow Builder UI
function jeevsutra_render_flow_ui() {
    $flow_json = get_option('jeevsutra_chatbot_flow_json', '[]');
    ?>
    <div class="wrap">
        <h1>🧠 Jeevsutra Chatbot Flow Builder</h1>

        <form method="post" action="options.php">
            <?php settings_fields('jeevsutra_flow_group'); ?>
            <textarea name="jeevsutra_chatbot_flow_json" rows="25" cols="120"><?php echo esc_textarea($flow_json); ?></textarea>
            <br><small>⚠️ Paste flow JSON or build using external visual tool (future).</small>
            <?php submit_button('💾 Save Flow'); ?>
        </form>

        <hr>
        <h2>📤 Export Flow</h2>
        <form method="post">
            <input type="hidden" name="jeevsutra_export_flow" value="1">
            <?php submit_button('⬇️ Download Flow JSON'); ?>
        </form>

        <hr>
        <h2>📥 Import Flow</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="jeevsutra_flow_file" accept=".json" required>
            <input type="hidden" name="jeevsutra_import_flow" value="1">
            <?php submit_button('📂 Upload & Import Flow'); ?>
        </form>

        <?php
        if (isset($_POST['jeevsutra_export_flow'])) {
            jeevsutra_export_flow_now();
        } elseif (isset($_POST['jeevsutra_import_flow'])) {
            jeevsutra_import_flow_now();
        }
        ?>

        <hr>
        <p><b>💡 Sample JSON Step:</b></p>
        <pre>{
  "id": "step_booking",
  "trigger": ["book", "appointment"],
  "reply": "You can book your consultation here 👉 https://calendly.com/jeevsutra",
  "fallback": "step_fallback",
  "next": null
}</pre>
    </div>
    <?php
}

// JSON Export
function jeevsutra_export_flow_now() {
    $flow = get_option('jeevsutra_chatbot_flow_json', '[]');
    header("Content-Type: application/json");
    header("Content-Disposition: attachment; filename=jeevsutra-flow-export.json");
    echo $flow;
    exit;
}

// JSON Import
function jeevsutra_import_flow_now() {
    if (!empty($_FILES['jeevsutra_flow_file']['tmp_name'])) {
        $file = $_FILES['jeevsutra_flow_file']['tmp_name'];
        $json = file_get_contents($file);
        json_decode($json); // Validate JSON
        if (json_last_error() === JSON_ERROR_NONE) {
            update_option('jeevsutra_chatbot_flow_json', $json);
            echo "<div class='updated'><p>✅ Flow imported successfully!</p></div>";
        } else {
            echo "<div class='error'><p>❌ Invalid JSON file.</p></div>";
        }
    }
}

// Chat match helper
function jeevsutra_match_chatbot_step($message) {
    $flow_json = get_option('jeevsutra_chatbot_flow_json', '[]');
    $steps = json_decode($flow_json, true);
    foreach ($steps as $step) {
        foreach ($step['trigger'] ?? [] as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return $step;
            }
        }
    }
    return null;
}

// Log matched & unmatched
function jeevsutra_log_chat_usage($message, $matched_step = null) {
    $log = get_option('jeevsutra_chat_logs', []);
    $log[] = [
        'time' => current_time('mysql'),
        'matched' => $matched_step ? $matched_step['id'] : 'unmatched',
        'message' => $message,
    ];
    update_option('jeevsutra_chat_logs', array_slice($log, -200));
}

function jeevsutra_log_fallback($message) {
    $fallbacks = get_option('jeevsutra_fallback_logs', []);
    $fallbacks[] = ['time' => current_time('mysql'), 'query' => $message];
    update_option('jeevsutra_fallback_logs', array_slice($fallbacks, -100));
}
