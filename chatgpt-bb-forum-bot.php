<?php
/**
 * Plugin Name: ChatGPT WP/BB Forum Bot
 * Description: ChatGPT BB Forum Bot plugin for Wordpress/BuddyBoss site.
 * Author:      Sergey Chernyakov
 * Author URI:  https://github.com/sergeychernyakov
 * Version:     1.0.0
 * Text Domain: chatgpt-bb-forum-bot
 * Domain Path: /languages/
 * License:     GPLv3 or later (license.txt)
 */

/**
 * This file should always remain compatible with the minimum version of
 * PHP supported by WordPress.
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CHATGPT_BB_FORUM_BOT' ) ) {

    /**
     * Main CHATGPTBBFORUMBOT_BB_Platform_Addon Class
     *
     * @class CHATGPTBBFORUMBOT_BB_Platform_Addon
     * @version 1.0.0
     */
    final class CHATGPTBBFORUMBOT_BB_Platform_Addon {

        /**
         * @var CHATGPTBBFORUMBOT_BB_Platform_Addon The single instance of the class
         * @since 1.0.0
         */
        protected static $_instance = null;

        /**
         * Main CHATGPTBBFORUMBOT_BB_Platform_Addon Instance
         *
         * Ensures only one instance of CHATGPTBBFORUMBOT_BB_Platform_Addon is loaded or can be loaded.
         *
         * @since 1.0.0
         * @static
         * @see CHATGPTBBFORUMBOT_BB_Platform_Addon()
         * @return CHATGPTBBFORUMBOT_BB_Platform_Addon - Main instance
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Cloning is forbidden.
         * @since 1.0.0
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'chatgpt-bb-forum-bot' ), '1.0.0' );
        }

        /**
         * Unserializing instances of this class is forbidden.
         * @since 1.0.0
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'chatgpt-bb-forum-bot' ), '1.0.0' );
        }

        /**
         * CHATGPTBBFORUMBOT_BB_Platform_Addon Constructor.
         */
        public function __construct() {
            $this->define_constants();
            $this->includes();
            // Set up localisation.
            $this->load_plugin_textdomain();
        }

        /**
         * Define WCE Constants
         */
        private function define_constants() {
            $this->define( 'CHATGPTBBFORUMBOT_BB_ADDON_PLUGIN_FILE', __FILE__ );
            $this->define( 'CHATGPTBBFORUMBOT_BB_ADDON_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            $this->define( 'CHATGPTBBFORUMBOT_BB_ADDON_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
            $this->define( 'CHATGPTBBFORUMBOT_BB_ADDON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }

        /**
         * Define constant if not already set
         * @param string $name
         * @param string|bool $value
         */
        private function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes() {
            include_once( 'functions.php' );
        }

        /**
         * Get the plugin url.
         * @return string
         */
        public function plugin_url() {
            return untrailingslashit( plugins_url( '/', __FILE__ ) );
        }

        /**
         * Get the plugin path.
         * @return string
         */
        public function plugin_path() {
            return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }

        /**
         * Load Localisation files.
         *
         * Note: the first-loaded translation file overrides any following ones if the same translation is present.
         */
        public function load_plugin_textdomain() {
            $locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
            $locale = apply_filters( 'plugin_locale', $locale, 'chatgpt-bb-forum-bot' );

            unload_textdomain( 'chatgpt-bb-forum-bot' );
            load_textdomain( 'chatgpt-bb-forum-bot', WP_LANG_DIR . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' . plugin_basename( dirname( __FILE__ ) ) . '-' . $locale . '.mo' );
            load_plugin_textdomain( 'chatgpt-bb-forum-bot', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
        }

        /**
         * Add custom field to user profile
         */
        public function chatgpt_bb_add_prompt_field($user) {
            ?>
            <h3><?php _e("Custom ChatGPT Prompt", "chatgpt-bb-forum-bot"); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="chatgpt_prompt"><?php _e("ChatGPT Prompt"); ?></label></th>
                    <td>
                        <textarea name="chatgpt_prompt" id="chatgpt_prompt" rows="5" cols="30"><?php echo esc_textarea(get_the_author_meta('chatgpt_prompt', $user->ID)); ?></textarea><br />
                        <span class="description"><?php _e("Please enter your custom ChatGPT prompt."); ?></span>
                    </td>
                </tr>
            </table>
            <?php
        }

        /**
         * Save custom field value
         */
        public function chatgpt_bb_save_prompt_field($user_id) {
            if (!current_user_can('edit_user', $user_id)) {
                return false;
            }
            update_user_meta($user_id, 'chatgpt_prompt', $_POST['chatgpt_prompt']);
        }

    }

    /**
     * Returns the main instance of CHATGPTBBFORUMBOT_BB_Platform_Addon to prevent the need to use globals.
     *
     * @since 1.0.0
     * @return CHATGPTBBFORUMBOT_BB_Platform_Addon
     */
    function CHATGPTBBFORUMBOT_BB_Platform_Addon() {
        return CHATGPTBBFORUMBOT_BB_Platform_Addon::instance();
    }

    function CHATGPTBBFORUMBOT_BB_Platform_install_bb_platform_notice() {
        echo '<div class="error fade"><p>';
        _e('<strong>ChatGPT Forum Bot</strong></a> requires the BuddyBoss Platform plugin to work. Please <a href="https://buddyboss.com/platform/" target="_blank">install BuddyBoss Platform</a> first.', 'chatgpt-bb-forum-bot');
        echo '</p></div>';
    }

    function CHATGPTBBFORUMBOT_BB_Platform_update_bb_platform_notice() {
        echo '<div class="error fade"><p>';
        _e('<strong>ChatGPT WP Forum Bot</strong></a> requires BuddyBoss Platform plugin version 1.2.6 or higher to work. Please update BuddyBoss Platform.', 'chatgpt-bb-forum-bot');
        echo '</p></div>';
    }

    function CHATGPTBBFORUMBOT_BB_Platform_is_active() {
        if ( defined( 'BP_PLATFORM_VERSION' ) && version_compare( BP_PLATFORM_VERSION,'1.2.6', '>=' ) ) {
            return true;
        }
        return false;
    }

    function CHATGPTBBFORUMBOT_BB_Platform_init() {
        if ( ! defined( 'BP_PLATFORM_VERSION' ) ) {
            add_action( 'admin_notices', 'CHATGPTBBFORUMBOT_BB_Platform_install_bb_platform_notice' );
            add_action( 'network_admin_notices', 'CHATGPTBBFORUMBOT_BB_Platform_install_bb_platform_notice' );
            return;
        }

        if ( version_compare( BP_PLATFORM_VERSION,'1.2.6', '<' ) ) {
            add_action( 'admin_notices', 'CHATGPTBBFORUMBOT_BB_Platform_update_bb_platform_notice' );
            add_action( 'network_admin_notices', 'CHATGPTBBFORUMBOT_BB_Platform_update_bb_platform_notice' );
            return;
        }

        CHATGPTBBFORUMBOT_BB_Platform_Addon();
    }

    add_action( 'plugins_loaded', 'CHATGPTBBFORUMBOT_BB_Platform_init', 9 );

    // Hook to add custom field to user profile
    add_action('show_user_profile', array('CHATGPTBBFORUMBOT_BB_Platform_Addon', 'chatgpt_bb_add_prompt_field'));
    add_action('edit_user_profile', array('CHATGPTBBFORUMBOT_BB_Platform_Addon', 'chatgpt_bb_add_prompt_field'));
    add_action('personal_options_update', array('CHATGPTBBFORUMBOT_BB_Platform_Addon', 'chatgpt_bb_save_prompt_field'));
    add_action('edit_user_profile_update', array('CHATGPTBBFORUMBOT_BB_Platform_Addon', 'chatgpt_bb_save_prompt_field'));
}

// Function to retrieve OpenAI API key from the settings
function get_openai_api_key() {
    return get_option('CHATGPTBBFORUMBOT_openai_api_key', '');
}

// Function to retrieve OpenAI Assistant ID from the settings
function get_openai_assistant_id() {
    return get_option('CHATGPTBBFORUMBOT_openai_assistant_id', '');
}

// Function to generate a reply using ChatGPT
function chatgpt_generate_reply($prompt) {
    $api_key = get_openai_api_key();
    $assistant_id = get_openai_assistant_id();
    $url = 'https://api.openai.com/v1/engines/' . $assistant_id . '/completions';
    $data = array(
        'prompt' => $prompt,
        'max_tokens' => 150,
        'temperature' => 0.7,
    );
    $options = array(
        'http' => array(
            'header' => "Content-Type: application/json\r\n" .
                        "Authorization: Bearer $api_key\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
        ),
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { 
        return "Error in generating reply.";
    }
    $response = json_decode($result, true);
    return $response['choices'][0]['text'];
}

// Function to generate a reply using a user's ChatGPT Prompt
function generate_user_reply($content, $user_id) {
    $prompt = get_user_meta($user_id, 'chatgpt_prompt', true);
    if (!$prompt) {
        return "This user has not set a ChatGPT prompt.";
    }
    $prompt .= "\nDiscussion reply: " . $content;

    $reply = chatgpt_generate_reply($prompt);
    return $reply;
}

// Function to post the reply to the forum activity
function chatgpt_bb_post_reply($activity_id, $reply, $user_id) {
    if (function_exists('bp_activity_add')) {
        $reply_data = array(
            'content' => $reply,
            'component' => 'activity',
            'type' => 'activity_comment',
            'item_id' => $activity_id,
            'secondary_item_id' => false,
            'user_id' => $user_id,
            'hide_sitewide' => false,
        );

        bp_activity_add($reply_data);
    }
}

// Function to handle new discussion replies
function chatgpt_bb_handle_new_reply($reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author) {
    $reply = bbp_get_reply($reply_id);
    $content = $reply->post_content;
    $users = get_users(array('meta_key' => 'chatgpt_prompt', 'meta_compare' => 'EXISTS')); // Get users with a ChatGPT prompt

    foreach ($users as $user) {
        $reply_text = generate_user_reply($content, $user->ID);
        chatgpt_bb_post_reply($reply_id, $reply_text, $user->ID);
    }

    trigger_error("Hook: bbp_new_reply, Params: reply_id = $reply_id, topic_id = $topic_id, forum_id = $forum_id, reply_author = $reply_author, reply_content = $content", E_USER_ERROR);
}

// // Hook into BuddyBoss or WordPress to trigger the handle function on new discussion replies
// add_action('bp_activity_comment_posted', 'chatgpt_bb_handle_new_reply', 10, 2);

add_action('bbp_new_reply', 'chatgpt_bb_handle_new_reply', 10, 5);

// Hook testing functions

// function test_bbp_new_reply($reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author) {
//     $reply = bbp_get_reply($reply_id);
//     $reply_content = $reply->post_content;
//     trigger_error("Hook: bbp_new_reply, Params: reply_id = $reply_id, topic_id = $topic_id, forum_id = $forum_id, reply_author = $reply_author, reply_content = $reply_content", E_USER_ERROR);
// }

// function test_bbp_new_reply_pre_extras($reply_id, $reply) {
//     trigger_error("Hook: bbp_new_reply_pre_extras, Params: reply_id = $reply_id", E_USER_ERROR);
// }

// function test_bbp_new_forum_post_extras($post_id) {
//     trigger_error("Hook: bbp_new_forum_post_extras, Params: post_id = $post_id", E_USER_ERROR);
// }

// function test_bbp_new_reply_post_extras($reply_id, $reply) {
//     trigger_error("Hook: bbp_new_reply_post_extras, Params: reply_id = $reply_id", E_USER_ERROR);
// }

// function test_bbp_new_topic_pre_extras($topic_id) {
//     trigger_error("Hook: bbp_new_topic_pre_extras, Params: topic_id = $topic_id", E_USER_ERROR);
// }

// function test_bp_activity_add($activity) {
//     trigger_error("Hook: bp_activity_add, Params: activity = " . print_r($activity, true), E_USER_ERROR);
// }

// function test_bp_activity_posted_update($content, $user_id, $activity_id) {
//     trigger_error("Hook: bp_activity_posted_update, Params: content = $content, user_id = $user_id, activity_id = $activity_id", E_USER_ERROR);
// }

// function test_bp_activity_comment_posted($comment_id, $params) {
//     trigger_error("Hook: bp_activity_comment_posted, Params: comment_id = $comment_id, params = " . print_r($params, true), E_USER_ERROR);
// }

// function test_bp_blogs_new_blog_post($blog_id, $post_id) {
//     trigger_error("Hook: bp_blogs_new_blog_post, Params: blog_id = $blog_id, post_id = $post_id", E_USER_ERROR);
// }

// function test_bp_activity_post_type_published($activity_object) {
//     trigger_error("Hook: bp_activity_post_type_published, Params: activity_object = " . print_r($activity_object, true), E_USER_ERROR);
// }

// Add hooks for testing
// add_action('bbp_new_reply', 'test_bbp_new_reply', 10, 5);
# add_action('bbp_new_reply_pre_extras', 'test_bbp_new_reply_pre_extras', 10, 2);
// add_action('bbp_new_forum_post_extras', 'test_bbp_new_forum_post_extras', 10, 1);
// add_action('bbp_new_reply_post_extras', 'test_bbp_new_reply_post_extras', 10, 2);
// add_action('bbp_new_topic_pre_extras', 'test_bbp_new_topic_pre_extras', 10, 2);
# add_action('bp_activity_add', 'test_bp_activity_add', 10, 2);
// add_action('bp_activity_posted_update', 'test_bp_activity_posted_update', 10, 3);
// add_action('bp_activity_comment_posted', 'test_bp_activity_comment_posted', 10, 2);
// add_action('bp_blogs_new_blog_post', 'test_bp_blogs_new_blog_post', 10, 2);
// add_action('bp_activity_post_type_published', 'test_bp_activity_post_type_published', 10, 1);
