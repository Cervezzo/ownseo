<?php
/**
 * Plugin Name: OwnSEO
 * Description: Lightweight SEO plugin to manage Title and Meta Description from admin panel.
 * Version: 0.01
 * Author: crowbeer
 */

// === 1. Add admin menu page ===
function ownseo_add_admin_menu() {
    add_options_page(
        'OwnSEO Settings',          // Page title
        'OwnSEO',                   // Menu title
        'manage_options',           // Capability
        'ownseo',                   // Menu slug
        'ownseo_settings_page_html' // Callback function
    );
}
add_action('admin_menu', 'ownseo_add_admin_menu');

// === 2. Register settings ===
function ownseo_register_settings() {
    register_setting('ownseo_settings_group', 'ownseo_title');
    register_setting('ownseo_settings_group', 'ownseo_description');
}
add_action('admin_init', 'ownseo_register_settings');

// === 3. HTML for admin settings page ===
function ownseo_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>OwnSEO Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('ownseo_settings_group'); ?>
            <?php do_settings_sections('ownseo_settings_group'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="ownseo_title">Title</label></th>
                    <td>
                        <input type="text" id="ownseo_title" name="ownseo_title" 
                            value="<?php echo esc_attr(get_option('ownseo_title')); ?>" 
                            style="width: 400px;" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="ownseo_description">Meta Description</label></th>
                    <td>
                        <textarea id="ownseo_description" name="ownseo_description" rows="4" cols="50" style="width: 400px;"><?php 
                            echo esc_textarea(get_option('ownseo_description')); ?></textarea>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// === 4. Insert Meta Description into <head> ===
function ownseo_insert_meta() {
    $description = esc_attr(get_option('ownseo_description'));
    if ($description) {
        echo '<meta name="description" content="' . $description . '">' . "\n";
    }
}
add_action('wp_head', 'ownseo_insert_meta');

// === 5. Filter to customize <title> ===
function ownseo_custom_title($title) {
    $custom_title = get_option('ownseo_title');
    if ($custom_title) {
		// Sanitize title using native PHP
        return strip_tags($custom_title);
    }
    return $title;
}
add_filter('pre_get_document_title', 'ownseo_custom_title');
