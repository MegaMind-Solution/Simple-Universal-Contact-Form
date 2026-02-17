<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add menu page
 */
function sucf_add_admin_menu() {
    add_menu_page(
        'Simple Contact Form',
        'Contact Form',
        'manage_options',
        'sucf-settings',
        'sucf_render_settings_page',
        'dashicons-email',
        58
    );
}
add_action('admin_menu', 'sucf_add_admin_menu');

/**
 * Render settings page
 */
function sucf_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Simple Contact Form Settings</h1>
        <div class="sucf-shortcode-note" style="padding: 15px; background: #f1f5f9; border-left: 4px solid #0073aa; margin-bottom: 20px; border-radius: 3px;">
    <strong>Shortcode:</strong> <code>[sucf_form]</code><br>
    You can use this shortcode on any page, post, or widget to display the contact form.  
    Optional attributes: <code>show_name, show_email, show_phone, show_message, button_text, layout, columns, gap</code>
</div>

        <form method="post" action="options.php">
            <?php
            settings_fields('sucf_settings_group');
            do_settings_sections('sucf_settings_page');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register settings
 */
function sucf_register_settings() {

    register_setting(
        'sucf_settings_group',
        'sucf_settings',
        'sucf_sanitize_settings'
    );

    /* ---------------- Email Section ---------------- */

    add_settings_section(
        'sucf_email_section',
        'Email & Recipient',
        '__return_false',
        'sucf_settings_page'
    );

    add_settings_field(
        'recipient_user',
        'Send To',
        'sucf_recipient_user_field',
        'sucf_settings_page',
        'sucf_email_section'
    );

    add_settings_field(
        'admin_phone',
        'Admin Phone Number',
        'sucf_admin_phone_field',
        'sucf_settings_page',
        'sucf_email_section'
    );

    add_settings_field(
        'from_name',
        'From Name',
        'sucf_from_name_field',
        'sucf_settings_page',
        'sucf_email_section'
    );

    add_settings_field(
        'reply_to',
        'Reply-To Email',
        'sucf_reply_to_field',
        'sucf_settings_page',
        'sucf_email_section'
    );

    /* ---------------- Messages Section ---------------- */

    add_settings_section(
        'sucf_messages_section',
        'Messages',
        '__return_false',
        'sucf_settings_page'
    );

    add_settings_field(
        'success_message',
        'Success Message',
        'sucf_success_message_field',
        'sucf_settings_page',
        'sucf_messages_section'
    );

    add_settings_field(
        'error_message',
        'Error Message',
        'sucf_error_message_field',
        'sucf_settings_page',
        'sucf_messages_section'
    );

    /* ---------------- Spam Section ---------------- */

    add_settings_section(
        'sucf_spam_section',
        'Spam Protection',
        '__return_false',
        'sucf_settings_page'
    );

    add_settings_field(
        'enable_honeypot',
        'Enable Honeypot',
        'sucf_honeypot_field',
        'sucf_settings_page',
        'sucf_spam_section'
    );

    /* ---------------- Logging ---------------- */

    add_settings_section(
        'sucf_logging_section',
        'Logging',
        '__return_false',
        'sucf_settings_page'
    );

    add_settings_field(
        'enable_logging',
        'Enable Submission Logging',
        'sucf_logging_field',
        'sucf_settings_page',
        'sucf_logging_section'
    );
}
add_action('admin_init', 'sucf_register_settings');

/* =====================================================
   FIELD RENDERERS
===================================================== */

function sucf_recipient_user_field() {
    $options = get_option('sucf_settings', []);
    $admins = get_users(['role' => 'administrator']);
    ?>
    <select name="sucf_settings[recipient_user]">
        <option value="">Default Site Admin Email</option>
        <?php foreach ($admins as $admin): ?>
            <option value="<?php echo esc_attr($admin->ID); ?>"
                <?php selected($options['recipient_user'] ?? '', $admin->ID); ?>>
                <?php echo esc_html($admin->display_name . ' (' . $admin->user_email . ')'); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description">Select which admin receives all form messages.</p>
    <?php
}

function sucf_admin_phone_field() {
    $options = get_option('sucf_settings', []);
    ?>
    <input type="text"
           name="sucf_settings[admin_phone]"
           value="<?php echo esc_attr($options['admin_phone'] ?? ''); ?>"
           class="regular-text">
    <p class="description">Optional phone number for internal reference.</p>
    <?php
}

function sucf_from_name_field() {
    $options = get_option('sucf_settings', []);
    ?>
    <input type="text"
           name="sucf_settings[from_name]"
           value="<?php echo esc_attr($options['from_name'] ?? get_bloginfo('name')); ?>"
           class="regular-text">
    <?php
}

function sucf_reply_to_field() {
    $options = get_option('sucf_settings', []);
    ?>
    <input type="email"
           name="sucf_settings[reply_to]"
           value="<?php echo esc_attr($options['reply_to'] ?? ''); ?>"
           class="regular-text">
    <?php
}

function sucf_success_message_field() {
    $options = get_option('sucf_settings', []);
    ?>
    <input type="text"
           name="sucf_settings[success_message]"
           value="<?php echo esc_attr($options['success_message'] ?? 'Thank you! Your message has been sent.'); ?>"
           class="regular-text">
    <?php
}

function sucf_error_message_field() {
    $options = get_option('sucf_settings', []);
    ?>
    <input type="text"
           name="sucf_settings[error_message]"
           value="<?php echo esc_attr($options['error_message'] ?? 'Something went wrong. Please try again.'); ?>"
           class="regular-text">
    <?php
}

function sucf_honeypot_field() {
    $options = get_option('sucf_settings', []);
    ?>
    <label>
        <input type="checkbox"
               name="sucf_settings[enable_honeypot]"
               value="1"
            <?php checked(1, $options['enable_honeypot'] ?? 0); ?>>
        Enable invisible honeypot protection
    </label>
    <?php
}

function sucf_logging_field() {
    $options = get_option('sucf_settings', []);
    ?>
    <label>
        <input type="checkbox"
               name="sucf_settings[enable_logging]"
               value="1"
            <?php checked(1, $options['enable_logging'] ?? 0); ?>>
        Save submissions in database
    </label>
    <?php
}

/* =====================================================
   SANITIZATION
===================================================== */

function sucf_sanitize_settings($input) {
    return [
        'recipient_user'  => absint($input['recipient_user'] ?? 0),
        'admin_phone'     => sanitize_text_field($input['admin_phone'] ?? ''),
        'from_name'       => sanitize_text_field($input['from_name'] ?? ''),
        'reply_to'        => sanitize_email($input['reply_to'] ?? ''),
        'success_message' => sanitize_text_field($input['success_message'] ?? ''),
        'error_message'   => sanitize_text_field($input['error_message'] ?? ''),
        'enable_honeypot' => !empty($input['enable_honeypot']) ? 1 : 0,
        'enable_logging'  => !empty($input['enable_logging']) ? 1 : 0,
    ];
}
