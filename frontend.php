<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main form renderer
 */
function sucf_render_form($atts = []) {

    $settings = get_option('sucf_settings', []);

    $defaults = [
        'show_name'    => true,
        'show_email'   => true,
        'show_phone'   => true,
        'show_message' => true,
        'button_text'  => 'Send Message',
        'layout'       => 'block', // block | flex | grid
        'columns'      => 1,
        'gap'          => 15,
        'form_id'      => 'sucf-form'
    ];

    $atts = wp_parse_args($atts, $defaults);

    $errors = [];
    $success = false;

    /* =========================
       HANDLE SUBMISSION
    ========================= */

    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['sucf_nonce']) &&
        wp_verify_nonce($_POST['sucf_nonce'], 'sucf_submit')
    ) {

        $name    = sanitize_text_field($_POST['sucf_name'] ?? '');
        $email   = sanitize_email($_POST['sucf_email'] ?? '');
        $phone   = sanitize_text_field($_POST['sucf_phone'] ?? '');
        $message = sanitize_textarea_field($_POST['sucf_message'] ?? '');
        $hp      = sanitize_text_field($_POST['sucf_hp'] ?? '');

        // Honeypot
        if (!empty($settings['enable_honeypot']) && !empty($hp)) {
            $errors[] = 'Spam detected.';
        }

        // Validation
        if ($atts['show_name'] && empty($name)) {
            $errors[] = 'Name is required.';
        }

        if ($atts['show_email'] && (empty($email) || !is_email($email))) {
            $errors[] = 'Valid email is required.';
        }

        if (!empty($phone) && !preg_match('/^[0-9\-\+\s\(\)]+$/', $phone)) {
            $errors[] = 'Invalid phone number.';
        }

        if ($atts['show_message'] && empty($message)) {
            $errors[] = 'Message is required.';
        }

        /* =========================
           SEND EMAIL
        ========================= */

        if (empty($errors)) {

            $to = get_option('admin_email');

            if (!empty($settings['recipient_user'])) {
                $user = get_user_by('id', $settings['recipient_user']);
                if ($user) {
                    $to = $user->user_email;
                }
            }

            $subject = 'New Contact Form Message';

            $headers = [];
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';

            if (!empty($settings['from_name'])) {
                $headers[] = 'From: ' . esc_html($settings['from_name']) . ' <' . $to . '>';
            }

            if (!empty($settings['reply_to'])) {
                $headers[] = 'Reply-To: ' . sanitize_email($settings['reply_to']);
            }

            $body  = "New contact form submission:\n\n";
            if ($atts['show_name'])    $body .= "Name: $name\n";
            if ($atts['show_email'])   $body .= "Email: $email\n";
            if ($atts['show_phone'])   $body .= "Phone: $phone\n";
            if ($atts['show_message']) $body .= "Message:\n$message\n";

            if (!empty($settings['admin_phone'])) {
                $body .= "\nAdmin Phone: " . $settings['admin_phone'];
            }

            $success = wp_mail($to, $subject, $body, $headers);

            // Logging
            if ($success && !empty($settings['enable_logging'])) {
                sucf_log_submission([
                    'name'    => $name,
                    'email'   => $email,
                    'phone'   => $phone,
                    'message' => $message,
                    'date'    => current_time('mysql')
                ]);
            }

            if (!$success) {
                $errors[] = 'Email could not be sent.';
            }
        }
    }

    /* =========================
       LAYOUT STYLE
    ========================= */

    $wrapper_style = '';

    if ($atts['layout'] === 'grid') {
        $wrapper_style = sprintf(
            'display:grid;grid-template-columns:repeat(%d,1fr);gap:%dpx;',
            intval($atts['columns']),
            intval($atts['gap'])
        );
    }

    if ($atts['layout'] === 'flex') {
        $wrapper_style = sprintf(
            'display:flex;flex-wrap:wrap;gap:%dpx;',
            intval($atts['gap'])
        );
    }

    /* =========================
       OUTPUT
    ========================= */
    ?>

    <div class="sucf-wrapper">

        <?php if ($success): ?>
            <div class="sucf-success">
                <?php echo esc_html($settings['success_message'] ?? 'Message sent successfully.'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="sucf-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo esc_html($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post"
              class="sucf-form"
              id="<?php echo esc_attr($atts['form_id']); ?>"
              style="<?php echo esc_attr($wrapper_style); ?>">

            <?php wp_nonce_field('sucf_submit', 'sucf_nonce'); ?>

            <input type="text" name="sucf_hp" class="sucf-hp" autocomplete="off">

            <?php if ($atts['show_name']): ?>
                <p>
                    <label>Name *</label>
                    <input type="text" name="sucf_name" placeholder="Elon Musk"  required>
                </p>
            <?php endif; ?>

            <?php if ($atts['show_email']): ?>
                <p>
                    <label>Email *</label>
                    <input type="email" name="sucf_email" placeholder="elonmusk@tesla.com" required>
                </p>
            <?php endif; ?>

            <?php if ($atts['show_phone']): ?>
                <p>
                    <label>Phone</label>
                    <input type="text" placeholder="+12574774777(Optional)" name="sucf_phone">
                </p>
            <?php endif; ?>

            <?php if ($atts['show_message']): ?>
                <p style="<?php echo esc_attr($atts['layout'] === 'grid' ? 'grid-column:1/-1;' : ''); ?>">
                    <label>Message *</label>
                    <textarea name="sucf_message" placeholder="Your massage here" rows="5" required></textarea>
                </p>
            <?php endif; ?>

            <p style="<?php echo esc_attr($atts['layout'] === 'grid' ? 'grid-column:1/-1;' : ''); ?>">
                <button type="submit">
                    <?php echo esc_html($atts['button_text']); ?>
                </button>
            </p>

        </form>
    </div>
    <?php
}

/**
 * Log submissions
 */
function sucf_log_submission($data) {
    $logs = get_option('sucf_logs', []);
    $logs[] = $data;
    update_option('sucf_logs', $logs);
}

/**
 * Shortcode
 */
function sucf_register_shortcode() {
    add_shortcode('sucf_form', 'sucf_render_form');
}
add_action('init', 'sucf_register_shortcode');
