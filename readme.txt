=== Simple Universal Contact Form ===
Contributors: ramzanch
Tags: contact form, elementor widget, gutenberg block, email form, simple form
Requires at least: 5.8
Tested up to: 6.7
Stable tag: 4.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A lightweight, universal contact form with auto-detect admin routing, Elementor & Gutenberg integration, and database logging.

== Description ==

Simple Universal Contact Form is designed to be the easiest way to add a contact form to your WordPress site without bloated settings. It features a centralized dashboard that automatically detects Administrator users for easy email routing.

Whether you prefer using Shortcodes, the Gutenberg Block Editor, or Elementor Page Builder, this plugin adapts to your workflow.

**Key Features:**

*   **Universal Integration:** Works seamlessly with **Elementor**, **Gutenberg (Block Editor)**, and via **Shortcode**.
*   **Smart Admin Routing:** Automatically lists all Administrator users in the settings. simply select who receives the emails from a dropdown.
*   **Layout Engine:** Choose between Block, Flex, or Grid layouts with customizable columns and gaps.
*   **Spam Protection:** Built-in invisible Honeypot protection to stop bots without annoying captchas.
*   **Submission Logging:** Optional feature to save form submissions directly to your database for backup.
*   **Styling Controls:** Full control over colors, borders, and radius directly inside Elementor.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/simple-universal-contact-form` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to **Contact Form** in the admin menu to configure your email settings.
4. Add the form to a page using the `[sucf_form]` shortcode, the Gutenberg block, or the Elementor widget.

== Frequently Asked Questions ==

= How do I change who receives the emails? =
Go to the **Contact Form** menu in your dashboard. Under "Email & Recipient", use the "Send To" dropdown. The plugin automatically detects all Administrator users on your site.

= Can I use this with Elementor? =
Yes! Search for the **Simple Contact Form** widget in the Elementor editor. It includes controls for hiding specific fields (Phone, Name, etc.), changing the layout to Grid/Flex, and styling colors and borders.

= Where are the form submissions saved? =
If you enable "Submission Logging" in the settings, submissions are saved to your database. Currently, these are stored in the WordPress options table for lightweight retrieval.

= What are the shortcode attributes? =
You can customize the form using the following attributes:
`[sucf_form show_name="1" show_phone="1" layout="grid" columns="2" gap="20" button_text="Contact Us"]`

*   `show_name`: 1 or 0 (default: 1)
*   `show_email`: 1 or 0 (default: 1)
*   `show_phone`: 1 or 0 (default: 1)
*   `show_message`: 1 or 0 (default: 1)
*   `layout`: block, flex, or grid (default: block)
*   `columns`: Number of columns for grid layout (default: 1)
*   `gap`: Gap in pixels (default: 15)
*   `button_text`: Text for the submit button

== Screenshots ==

1. **Admin Dashboard** - Easy configuration of recipients and messages.
2. **Elementor Widget** - Drag and drop widget with styling controls.
3. **Frontend Form** - Clean, responsive form output.

== Changelog ==

= 4.2.1 =
*   Added full Elementor Widget support with style controls.
*   Added Gutenberg Block support.
*   Implemented Grid and Flex layouts.
*   Added database logging for submissions.
*   Improved admin UI with user auto-detection.

= 1.0.0 =
*   Initial release.