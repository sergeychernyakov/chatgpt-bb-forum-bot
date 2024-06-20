<?php
// functions.php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'CHATGPTBBFORUMBOT_admin_enqueue_script' ) ) {
    function CHATGPTBBFORUMBOT_admin_enqueue_script() {
        wp_enqueue_style( 'buddyboss-addon-admin-css', plugin_dir_url( __FILE__ ) . 'style.css' );
        wp_enqueue_style( 'dashicons' ); // Enqueue Dashicons
        wp_enqueue_script( 'buddyboss-addon-admin-js', plugin_dir_url( __FILE__ ) . 'script.js', array('jquery'), false, true );
    }

    add_action( 'admin_enqueue_scripts', 'CHATGPTBBFORUMBOT_admin_enqueue_script' );
}

if (!function_exists('CHATGPTBBFORUMBOT_check_wp_ulike')) {
    function CHATGPTBBFORUMBOT_check_wp_ulike() {
        if (!is_plugin_active('wp-ulike/wp-ulike.php')) {
            add_action('admin_notices', 'CHATGPTBBFORUMBOT_wp_ulike_admin_notice');
            return false;
        }
        return true;
    }

    function CHATGPTBBFORUMBOT_wp_ulike_admin_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('ChatGPT BB Forum Bot requires the WP ULike plugin to be installed and activated.', 'chatgpt-bb-forum-bot'); ?></p>
        </div>
        <?php
    }
    add_action('admin_init', 'CHATGPTBBFORUMBOT_check_wp_ulike');
}

if (!CHATGPTBBFORUMBOT_check_wp_ulike()) {
    return;
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_get_settings_sections' ) ) {
    function CHATGPTBBFORUMBOT_get_settings_sections() {
        $settings = array(
            'CHATGPTBBFORUMBOT_settings_section' => array(
                'page'  => 'addon',
                'title' => __( 'ChatGPT BB Forum Bot Settings', 'chatgpt-bb-forum-bot' ),
            ),
        );

        return (array) apply_filters( 'CHATGPTBBFORUMBOT_get_settings_sections', $settings );
    }
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_get_settings_fields_for_section' ) ) {
    function CHATGPTBBFORUMBOT_get_settings_fields_for_section( $section_id = '' ) {
        // Bail if section is empty
        if ( empty( $section_id ) ) {
            return false;
        }

        $fields = CHATGPTBBFORUMBOT_get_settings_fields();
        $retval = isset( $fields[ $section_id ] ) ? $fields[ $section_id ] : false;

        return (array) apply_filters( 'CHATGPTBBFORUMBOT_get_settings_fields_for_section', $retval, $section_id );
    }
}
if (!function_exists('CHATGPTBBFORUMBOT_get_settings_fields')) {
    function CHATGPTBBFORUMBOT_get_settings_fields() {
        $fields = array();

        $fields['CHATGPTBBFORUMBOT_settings_section'] = array(
            'CHATGPTBBFORUMBOT_openai_api_key' => array(
                'title'             => __('OpenAI API Key', 'chatgpt-bb-forum-bot'),
                'callback'          => 'CHATGPTBBFORUMBOT_settings_callback_openai_api_key',
                'sanitize_callback' => 'sanitize_text_field',
                'args'              => array(),
            ),
            'CHATGPTBBFORUMBOT_selected_forum' => array(
                'title'             => __('Select Introduction Forum', 'chatgpt-bb-forum-bot'),
                'callback'          => 'CHATGPTBBFORUMBOT_settings_callback_selected_forum',
                'sanitize_callback' => 'sanitize_text_field',
                'args'              => array(),
            ),
            'CHATGPTBBFORUMBOT_reply_interval_min_hours' => array(
                'title'             => __('Reply Interval Min (hours)', 'chatgpt-bb-forum-bot'),
                'callback'          => 'CHATGPTBBFORUMBOT_settings_callback_reply_interval_min_hours',
                'sanitize_callback' => 'floatval',
                'args'              => array(),
            ),
            'CHATGPTBBFORUMBOT_reply_interval_max_hours' => array(
                'title'             => __('Reply Interval Max (hours)', 'chatgpt-bb-forum-bot'),
                'callback'          => 'CHATGPTBBFORUMBOT_settings_callback_reply_interval_max_hours',
                'sanitize_callback' => 'floatval',
                'args'              => array(),
            ),
            'CHATGPTBBFORUMBOT_reply_to_direct_replies' => array(
                'title'             => __('Reply to Direct Replies', 'chatgpt-bb-forum-bot'),
                'callback'          => 'CHATGPTBBFORUMBOT_settings_callback_reply_to_direct_replies',
                'sanitize_callback' => 'sanitize_text_field',
                'args'              => array(),
            ),
            'CHATGPTBBFORUMBOT_reply_to_new_introductions' => array(
                'title'             => __('Reply to New Introductions', 'chatgpt-bb-forum-bot'),
                'callback'          => 'CHATGPTBBFORUMBOT_settings_callback_reply_to_new_introductions',
                'sanitize_callback' => 'sanitize_text_field',
                'args'              => array(),
            ),
            'CHATGPTBBFORUMBOT_like_replies' => array(
                'title'             => __('Like Replies', 'chatgpt-bb-forum-bot'),
                'callback'          => 'CHATGPTBBFORUMBOT_settings_callback_like_replies',
                'sanitize_callback' => 'sanitize_text_field',
                'args'              => array(),
            ),
            'CHATGPTBBFORUMBOT_face_of_brand_account' => array(
                'title'             => __('Face of the Brand Account', 'chatgpt-bb-forum-bot'),
                'callback'          => 'CHATGPTBBFORUMBOT_settings_callback_face_of_brand_account',
                'sanitize_callback' => 'sanitize_text_field',
                'args'              => array(),
            ),
        );

        return (array) apply_filters('CHATGPTBBFORUMBOT_get_settings_fields', $fields);
    }
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_settings_callback_openai_api_key' ) ) {
    function CHATGPTBBFORUMBOT_settings_callback_openai_api_key() {
        $option = get_option( 'CHATGPTBBFORUMBOT_openai_api_key', '' );
        ?>
        <div style="position: relative; display: inline-block;">
            <input name="CHATGPTBBFORUMBOT_openai_api_key"
                   id="CHATGPTBBFORUMBOT_openai_api_key"
                   type="password"
                   value="<?php echo esc_attr( $option ); ?>"
                   style="padding-right: 30px;"
            />
            <span class="dashicons dashicons-visibility" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('CHATGPTBBFORUMBOT_openai_api_key')"></span>
        </div>
        <label for="CHATGPTBBFORUMBOT_openai_api_key">
            <?php _e( 'Enter your OpenAI API Key', 'chatgpt-bb-forum-bot' ); ?>
        </label>
        <?php
    }
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_settings_callback_selected_forum' ) ) {
    function CHATGPTBBFORUMBOT_settings_callback_selected_forum() {
        $selected_forum = get_option( 'CHATGPTBBFORUMBOT_selected_forum', '' );
        $forums = get_posts(array(
            'post_type' => 'forum',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ));
        ?>
        <select name="CHATGPTBBFORUMBOT_selected_forum">
            <?php foreach ( $forums as $forum ) : ?>
                <option value="<?php echo esc_attr( $forum->ID ); ?>" <?php selected( $selected_forum, $forum->ID ); ?>>
                    <?php echo esc_html( $forum->post_title ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="CHATGPTBBFORUMBOT_selected_forum">
            <?php _e( 'Select the forum for ChatGPT responses', 'chatgpt-bb-forum-bot' ); ?>
        </label>
        <?php
    }
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_settings_callback_reply_interval_min_hours' ) ) {
    function CHATGPTBBFORUMBOT_settings_callback_reply_interval_min_hours() {
        $option = get_option( 'CHATGPTBBFORUMBOT_reply_interval_min_hours', 1 );
        ?>
        <input name="CHATGPTBBFORUMBOT_reply_interval_min_hours"
               id="CHATGPTBBFORUMBOT_reply_interval_min_hours"
               type="number"
               step="0.01"
               value="<?php echo esc_attr( $option ); ?>"
        />
        <label for="CHATGPTBBFORUMBOT_reply_interval_min_hours">
            <?php _e( 'Set the minimum interval for replies in hours', 'chatgpt-bb-forum-bot' ); ?>
        </label>
        <?php
    }
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_settings_callback_reply_interval_max_hours' ) ) {
    function CHATGPTBBFORUMBOT_settings_callback_reply_interval_max_hours() {
        $option = get_option( 'CHATGPTBBFORUMBOT_reply_interval_max_hours', CHATGPT_REPLY_INTERVAL_MAX_HOURS );
        ?>
        <input name="CHATGPTBBFORUMBOT_reply_interval_max_hours"
               id="CHATGPTBBFORUMBOT_reply_interval_max_hours"
               type="number"
               step="0.01"
               value="<?php echo esc_attr( $option ); ?>"
        />
        <label for="CHATGPTBBFORUMBOT_reply_interval_max_hours">
            <?php _e( 'Set the maximum interval for replies in hours', 'chatgpt-bb-forum-bot' ); ?>
        </label>
        <?php
    }
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_settings_callback_reply_to_direct_replies' ) ) {
    function CHATGPTBBFORUMBOT_settings_callback_reply_to_direct_replies() {
        $option = get_option( 'CHATGPTBBFORUMBOT_reply_to_direct_replies', '1' );
        ?>
        <input name="CHATGPTBBFORUMBOT_reply_to_direct_replies"
               id="CHATGPTBBFORUMBOT_reply_to_direct_replies"
               type="checkbox"
               value="1"
               <?php checked( '1', $option ); ?>
        />
        <label for="CHATGPTBBFORUMBOT_reply_to_direct_replies">
            <?php _e( 'Reply to Direct Replies', 'chatgpt-bb-forum-bot' ); ?>
        </label>
        <?php
    }
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_settings_callback_reply_to_new_introductions' ) ) {
    function CHATGPTBBFORUMBOT_settings_callback_reply_to_new_introductions() {
        $option = get_option( 'CHATGPTBBFORUMBOT_reply_to_new_introductions', '1' );
        ?>
        <input name="CHATGPTBBFORUMBOT_reply_to_new_introductions"
               id="CHATGPTBBFORUMBOT_reply_to_new_introductions"
               type="checkbox"
               value="1"
               <?php checked( '1', $option ); ?>
        />
        <label for="CHATGPTBBFORUMBOT_reply_to_new_introductions">
            <?php _e( 'Reply to New Introductions', 'chatgpt-bb-forum-bot' ); ?>
        </label>
        <?php
    }
}

if (!function_exists('CHATGPTBBFORUMBOT_settings_callback_like_replies')) {
    function CHATGPTBBFORUMBOT_settings_callback_like_replies() {
        $option = get_option('CHATGPTBBFORUMBOT_like_replies', '1');
        ?>
        <input name="CHATGPTBBFORUMBOT_like_replies"
               id="CHATGPTBBFORUMBOT_like_replies"
               type="checkbox"
               value="1"
               <?php checked('1', $option); ?>
        />
        <label for="CHATGPTBBFORUMBOT_like_replies">
            <?php _e('Allow the bot to like replies', 'chatgpt-bb-forum-bot'); ?>
        </label>
        <?php
    }
}

if (!function_exists('CHATGPTBBFORUMBOT_settings_callback_face_of_brand_account')) {
    function CHATGPTBBFORUMBOT_settings_callback_face_of_brand_account() {
        $face_of_brand_account = get_option('CHATGPTBBFORUMBOT_face_of_brand_account', '');
        $users = get_users(array(
            'meta_key'     => 'chatgpt_prompt',
            'meta_value'   => '',
            'meta_compare' => '!=',
        ));
        ?>
        <select name="CHATGPTBBFORUMBOT_face_of_brand_account">
            <?php foreach ($users as $user) : ?>
                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($face_of_brand_account, $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="CHATGPTBBFORUMBOT_face_of_brand_account">
            <?php _e('Select the face of the brand account', 'chatgpt-bb-forum-bot'); ?>
        </label>
        <?php
    }
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_is_addon_field_enabled' ) ) {
    function CHATGPTBBFORUMBOT_is_addon_field_enabled( $default = 1 ) {
        return (bool) apply_filters( 'CHATGPTBBFORUMBOT_is_addon_field_enabled', (bool) get_option( 'CHATGPTBBFORUMBOT_field', $default ) );
    }
}

/***************************** Add section in current settings ***************************************/

/**
 * Register fields for settings hooks
 * bp_admin_setting_general_register_fields
 * bp_admin_setting_xprofile_register_fields
 * bp_admin_setting_groups_register_fields
 * bp_admin_setting_forums_register_fields
 * bp_admin_setting_activity_register_fields
 * bp_admin_setting_media_register_fields
 * bp_admin_setting_friends_register_fields
 * bp_admin_setting_invites_register_fields
 * bp_admin_setting_search_register_fields
 */

if ( ! function_exists( 'CHATGPTBBFORUMBOT_admin_general_setting_callback_my_addon' ) ) {
    function CHATGPTBBFORUMBOT_admin_general_setting_callback_my_addon() {
        ?>
        <input id="bp-enable-my-addon" name="bp-enable-my-addon" type="checkbox"
               value="1" <?php checked( CHATGPTBBFORUMBOT_enable_my_addon() ); ?> />
        <label for="bp-enable-my-addon"><?php _e( 'Enable my option', 'chatgpt-bb-forum-bot' ); ?></label>
        <?php
    }
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_enable_my_addon' ) ) {
    function CHATGPTBBFORUMBOT_enable_my_addon( $default = false ) {
        return (bool) apply_filters( 'CHATGPTBBFORUMBOT_enable_my_addon', (bool) bp_get_option( 'bp-enable-my-addon', $default ) );
    }
}

/**************************************** MY PLUGIN INTEGRATION ************************************/

/**
 * Set up the my plugin integration.
 */
function CHATGPTBBFORUMBOT_register_integration() {
    require_once dirname( __FILE__ ) . '/integration/buddyboss-integration.php';
    buddypress()->integrations['addon'] = new CHATGPTBBFORUMBOT_BuddyBoss_Integration();
}
add_action( 'bp_setup_integrations', 'CHATGPTBBFORUMBOT_register_integration' );

if (!function_exists('CHATGPTBBFORUMBOT_create_vote')) {
    function CHATGPTBBFORUMBOT_create_vote($topic_id, $user_id, $user_ip, $status) {
        global $wpdb;

        // Check if WP ULike function exists
        if (!function_exists('wp_ulike_get_option')) {
            trigger_error("WP ULike functions not found", E_USER_NOTICE);
            return false;
        }

        // Prepare necessary variables
        $table_name = $wpdb->prefix . 'ulike_forums';
        $meta_table = $wpdb->prefix . 'ulike_meta';
        $current_time = current_time('mysql');

        // Check if the user already liked this topic
        $existing_like = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE topic_id = %d AND user_id = %d",
            $topic_id, $user_id
        ));

        if ($existing_like > 0) {
            return array('success' => false, 'message' => 'User has already liked this topic');
        }

        // Insert like into database
        $result = $wpdb->insert(
            $table_name,
            array(
                'topic_id'  => $topic_id,
                'user_id'   => $user_id,
                'ip'        => $user_ip,
                'status'    => $status,
                'date_time' => $current_time
            ),
            array(
                '%d', '%d', '%s', '%d', '%s'
            )
        );

        if ($result) {
            // Update the like count in post meta
            $meta_key = '_liked';
            $like_count = get_post_meta($topic_id, $meta_key, true);
            $like_count = empty($like_count) ? 0 : (int) $like_count;
            $like_count++;
            update_post_meta($topic_id, $meta_key, $like_count);

            // Update the count_distinct_like in wp_ulike_meta
            $meta_key = 'count_distinct_like';
            $current_count = $wpdb->get_var($wpdb->prepare(
                "SELECT meta_value FROM $meta_table WHERE item_id = %d AND meta_key = %s",
                $topic_id, $meta_key
            ));
            $new_count = empty($current_count) ? 1 : (int) $current_count + 1;

            // Update or insert the new count
            if ($current_count !== null) {
                $wpdb->update(
                    $meta_table,
                    array('meta_value' => $new_count),
                    array('item_id' => $topic_id, 'meta_key' => $meta_key),
                    array('%d'),
                    array('%d', '%s')
                );
            } else {
                $wpdb->insert(
                    $meta_table,
                    array(
                        'item_id'   => $topic_id,
                        'meta_key'  => $meta_key,
                        'meta_value' => $new_count,
                        'meta_group' => 'topic'
                    ),
                    array('%d', '%s', '%d', '%s')
                );
            }

            return array('success' => true, 'message' => 'Vote registered successfully');
        } else {
            return array('success' => false, 'message' => 'Failed to register vote');
        }
    }
}
