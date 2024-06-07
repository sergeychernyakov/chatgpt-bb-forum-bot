<?php
/**
 * Plugin Name: ChatGPT WP/BB Forum Bot
 * Description: ChatGPT BB Forum Bot plugin for WordPress/BuddyBoss site.
 * Author:      Sergey Chernyakov
 * Author URI:  https://github.com/sergeychernyakov
 * Version:     1.0.0
 * Text Domain: chatgpt-bb-forum-bot
 * Domain Path: /languages/
 * License:     GPLv3 or later (license.txt)
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CHATGPTBBFORUMBOT_BB_Platform_Addon' ) ) {

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
            $this->define( 'CHATGPT_REPLY_INTERVAL_MIN_HOURS', 1); // 1 hour
            $this->define( 'CHATGPT_REPLY_INTERVAL_MAX_HOURS', 72); // 72 hours
            $this->define( 'CHATGPT_MODEL', 'gpt-4' );
            $this->define( 'CHATGPT_MAX_TOKENS', 150 );
            $this->define( 'CHATGPT_TEMPERATURE', 1 );
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
         *
         * @param WP_User $user The user object.
         */
        public static function chatgpt_bb_add_prompt_field($user) {
            if (!current_user_can('administrator')) {
                return;
            }
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
         *
         * @param int $user_id The user ID.
         */
        public static function chatgpt_bb_save_prompt_field($user_id) {
            if (!current_user_can('edit_user', $user_id)) {
                return false;
            }
            update_user_meta($user_id, 'chatgpt_prompt', sanitize_textarea_field($_POST['chatgpt_prompt']));
        }

        /**
         * Retrieve OpenAI API key from the settings
         *
         * @return string The OpenAI API key.
         */
        public static function get_openai_api_key() {
            return get_option('CHATGPTBBFORUMBOT_openai_api_key', '');
        }

        /**
         * Generate a reply using ChatGPT
         *
         * @param string $prompt The prompt to send to ChatGPT.
         * @param string $content The content to include in the reply.
         * @return string The generated reply or empty string on failure.
         */
        public static function chatgpt_generate_reply($prompt, $content) {
            $api_key = self::get_openai_api_key();
            
            if (empty($api_key)) {
                return '';
            }

            $model = CHATGPT_MODEL;
            $url = 'https://api.openai.com/v1/chat/completions';

            $data = array(
                'model' => $model,
                'messages' => array(
                    array(
                        'role' => 'system',
                        'content' => $prompt
                    ),
                    array(
                        'role' => 'user',
                        'content' => $content
                    )
                ),
                'max_tokens' => CHATGPT_MAX_TOKENS,
                'temperature' => CHATGPT_TEMPERATURE,
            );

            $options = array(
                'http' => array(
                    'header' => "Content-Type: application/json\r\n" .
                                "Authorization: Bearer $api_key\r\n",
                    'method' => 'POST',
                    'content' => json_encode($data),
                ),
            );

            try {
                $context = stream_context_create($options);
                $result = file_get_contents($url, false, $context);
                if ($result === FALSE) { 
                    return '';
                }
                $response = json_decode($result, true);
                return $response['choices'][0]['message']['content'];
            } catch (Exception $e) {
                return '';
            }
        }

        /**
         * Post the reply at the scheduled time
         *
         * @param array $args The arguments for posting the reply.
         */
        public static function chatgpt_bb_post_reply($args) {
            $reply_id = $args['reply_id'];
            $reply_text = $args['reply_text'];
            $user_id = $args['user_id'];

            // Check if reply_text is valid
            if (empty($reply_text) || strpos($reply_text, 'Error') !== false || strpos($reply_text, 'No valid reply generated') !== false) {
                return;
            }

            // Get the reply details
            $reply = bbp_get_reply($reply_id);
            $topic_id = $reply->post_parent; // The parent topic of the original reply
            $forum_id = bbp_get_reply_forum_id($reply_id); // The forum ID of the original reply

            // Prepare the data for the new reply
            $reply_data = array(
                'post_parent'    => $topic_id, // Set the parent to the topic ID
                'post_status'    => 'publish',
                'post_type'      => bbp_get_reply_post_type(),
                'post_author'    => $user_id,
                'post_content'   => $reply_text,
            );

            $reply_meta = array(
                'author_ip' => bbp_current_author_ip(),
                'forum_id'  => $forum_id,
                'topic_id'  => $topic_id,
                'reply_to'  => $reply_id, // Set the reply_to to the original reply ID
            );

            // Insert the reply
            $new_reply_id = bbp_insert_reply($reply_data, $reply_meta);

            // If the reply was successfully created, update the topic and forum
            if ($new_reply_id) {
                bbp_update_topic($topic_id);
                bbp_update_forum($forum_id);
            }
        }

        /**
         * Get parent replies
         *
         * @param int $reply_id The ID of the reply.
         * @return string The concatenated parent replies.
         */
        public static function chatgpt_bb_get_parent_replies($reply_id) {
            $parent_replies = [];
            $current_reply = bbp_get_reply($reply_id);

            while ($current_reply && $current_reply->post_parent != 0) {
                $parent_reply_id = get_post_meta($current_reply->ID, '_bbp_reply_to', true);
                $parent_reply = bbp_get_reply($parent_reply_id);
                if ($parent_reply) {
                    $parent_replies[] = $parent_reply->post_content;
                    $current_reply = $parent_reply;
                } else {
                    break;
                }
            }

            return implode("\n---\n", array_reverse($parent_replies));
        }

        /**
         * Generate and post the reply using ChatGPT
         *
         * @param array $args The arguments for generating the reply.
         */
        public static function chatgpt_bb_generate_reply($args) {
            $reply_id = $args['reply_id'];
            $topic_id = $args['topic_id'];
            $forum_id = $args['forum_id'];
            $parent_reply_author_id = $args['parent_reply_author_id'];
            $chatgpt_prompt = $args['chatgpt_prompt'];

            // Exclude replies from ChatGPT user and user doesn't answer on his own replies
            $reply = bbp_get_reply($reply_id);
            $content = $reply->post_content;

            $forum_name = bbp_get_forum_title($forum_id);
            $topic_name = bbp_get_topic_title($topic_id);
            $topic_description = bbp_get_topic_content($topic_id);
            $parent_replies = self::chatgpt_bb_get_parent_replies($reply_id);

            $full_prompt = "Yor character: $chatgpt_prompt\n\nForum name: $forum_name\nTopic: $topic_name\nnTopic description: $topic_description\nParent Replies: $parent_replies";

            $reply_text = self::chatgpt_generate_reply($full_prompt, $content);

            if (!empty($reply_text)) {
                self::chatgpt_bb_post_reply(array(
                    'reply_id' => $reply_id,
                    'reply_text' => $reply_text,
                    'user_id' => $parent_reply_author_id
                ));
            }
        }

        /**
         * Schedule the reply generation
         *
         * @param int $reply_id The ID of the original reply.
         * @param int $topic_id The ID of the topic.
         * @param int $forum_id The ID of the forum.
         * @param array $anonymous_data The anonymous data.
         * @param int $reply_author The ID of the reply author.
         */
        public static function chatgpt_bb_schedule_reply_generation($reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author) {
            // Get the parent reply
            $parent_reply_id = bbp_get_reply_to($reply_id);

            // If there isn't a parent reply, return
            if (empty($parent_reply_id)) {
                return;
            }

            // Get the parent reply author ID
            $parent_reply = bbp_get_reply($parent_reply_id);
            if (!$parent_reply) {
                return;
            }

            $parent_reply_author_id = $parent_reply->post_author;

            // Check if the parent reply author has a chatgpt_prompt
            $chatgpt_prompt = get_user_meta($parent_reply_author_id, 'chatgpt_prompt', true);

            if (empty($chatgpt_prompt)) {
                // Do nothing if the parent reply author does not have a chatgpt_prompt
                return;
            }

            // Schedule the event to handle the reply generation
            $interval = rand(get_option('CHATGPTBBFORUMBOT_reply_interval_min_hours', CHATGPT_REPLY_INTERVAL_MIN_HOURS) * 3600, get_option('CHATGPTBBFORUMBOT_reply_interval_max_hours', CHATGPT_REPLY_INTERVAL_MAX_HOURS) * 3600); // Random interval between defined min and max in seconds
            $timestamp = time() + $interval;

            $args = array(
                'reply_id' => $reply_id,
                'topic_id' => $topic_id,
                'forum_id' => $forum_id,
                'parent_reply_author_id' => $parent_reply_author_id,
                'chatgpt_prompt' => $chatgpt_prompt
            );

            wp_schedule_single_event($timestamp, 'chatgpt_bb_generate_reply_event', array($args));
        }

        /**
         * Generate and post the reply using ChatGPT
         *
         * @param array $args The arguments for generating the reply to new topic.
         */
        public static function chatgpt_bb_generate_reply_to_new_topic($args) {
            $topic_id = $args['topic_id'];
            $forum_id = $args['forum_id'];

            // Get the topic details
            $topic = bbp_get_topic($topic_id);
            if (!$topic) {
                return;
            }

            $content = $topic->post_content;
            $topic_author_id = $topic->post_author;

            // Custom query to get users with a non-empty chatgpt_prompt, excluding the topic author
            $user_query = new WP_User_Query(array(
                'meta_key' => 'chatgpt_prompt',
                'meta_value' => '',
                'meta_compare' => '!=',
                'orderby' => 'rand',
                'exclude' => array($topic_author_id) // Exclude the topic author
            ));

            $users = $user_query->get_results(); // Get users with a non-empty chatgpt_prompt

            if (!empty($users)) {
                foreach ($users as $user) {
                    $chatgpt_prompt = get_user_meta($user->ID, 'chatgpt_prompt', true);

                    if (!empty($chatgpt_prompt)) {
                        $forum_name = bbp_get_forum_title($forum_id);
                        $topic_name = bbp_get_topic_title($topic_id);
                        $topic_description = bbp_get_topic_content($topic_id);

                        $full_prompt = "Yor character: $chatgpt_prompt\n\nForum name: $forum_name\nTopic: $topic_name\nnTopic description: $topic_description";

                        $reply_text = self::chatgpt_generate_reply($full_prompt, $content);

                        if (!empty($reply_text)) {
                            $interval = rand(get_option('CHATGPTBBFORUMBOT_reply_interval_min_hours', CHATGPT_REPLY_INTERVAL_MIN_HOURS) * 3600, get_option('CHATGPTBBFORUMBOT_reply_interval_max_hours', CHATGPT_REPLY_INTERVAL_MAX_HOURS) * 3600); // Random interval between defined min and max in seconds
                            $timestamp = time() + $interval;

                            $args = array(
                                'topic_id' => $topic_id,
                                'reply_text' => $reply_text,
                                'user_id' => $user->ID
                            );

                            wp_schedule_single_event($timestamp, 'chatgpt_bb_post_reply_to_new_topic_event', array($args));
                        }
                    }
                }
            }
        }

        /**
         * Post the reply to a new topic
         *
         * @param array $args The arguments for posting the reply.
         */
        public static function chatgpt_bb_post_reply_to_new_topic($args) {
            $topic_id = $args['topic_id'];
            $reply_text = $args['reply_text'];
            $user_id = $args['user_id'];

            // Check if reply_text is valid
            if (empty($reply_text) || strpos($reply_text, 'Error') !== false || strpos($reply_text, 'No valid reply generated') !== false) {
                return;
            }

            // Prepare the data for the new reply
            $reply_data = array(
                'post_parent'    => $topic_id, // Set the parent to the topic ID
                'post_status'    => 'publish',
                'post_type'      => bbp_get_reply_post_type(),
                'post_author'    => $user_id,
                'post_content'   => $reply_text,
            );

            $reply_meta = array(
                'author_ip' => bbp_current_author_ip(),
                'forum_id'  => bbp_get_topic_forum_id($topic_id),
                'topic_id'  => $topic_id,
            );

            // Insert the reply
            $new_reply_id = bbp_insert_reply($reply_data, $reply_meta);

            // If the reply was successfully created, update the topic and forum
            if ($new_reply_id) {
                bbp_update_topic($topic_id);
                bbp_update_forum($reply_meta['forum_id']);
            }
        }

        /**
         * Schedule the reply generation
         *
         * @param int $topic_id The ID of the topic.
         * @param int $forum_id The ID of the forum.
         */
        public static function chatgpt_bb_schedule_reply_to_new_topic_generation($topic_id, $forum_id) {
            // Get the selected forum from the settings
            $selected_forum = get_option('CHATGPTBBFORUMBOT_selected_forum');

            // Check if the new topic is in the selected forum
            if ($forum_id != $selected_forum) {
                return;
            }

            $args = array(
                'topic_id' => $topic_id,
                'forum_id' => $forum_id
            );

            wp_schedule_single_event(time(), 'chatgpt_bb_generate_reply_to_new_topic_event', array($args));
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
        _e('<strong>ChatGPT Forum Bot</strong> requires the BuddyBoss Platform plugin to work. Please <a href="https://buddyboss.com/platform/" target="_blank">install BuddyBoss Platform</a> first.', 'chatgpt-bb-forum-bot');
        echo '</p></div>';
    }

    function CHATGPTBBFORUMBOT_BB_Platform_update_bb_platform_notice() {
        echo '<div class="error fade"><p>';
        _e('<strong>ChatGPT WP Forum Bot</strong> requires BuddyBoss Platform plugin version 1.2.6 or higher to work. Please update BuddyBoss Platform.', 'chatgpt-bb-forum-bot');
        echo '</p></div>';
    }

    function CHATGPTBBFORUMBOT_BB_Platform_is_active() {
        return defined( 'BP_PLATFORM_VERSION' ) && version_compare( BP_PLATFORM_VERSION, '1.2.6', '>=' );
    }

    function CHATGPTBBFORUMBOT_BB_Platform_init() {
        if ( ! defined( 'BP_PLATFORM_VERSION' ) ) {
            add_action( 'admin_notices', 'CHATGPTBBFORUMBOT_BB_Platform_install_bb_platform_notice' );
            add_action( 'network_admin_notices', 'CHATGPTBBFORUMBOT_BB_Platform_install_bb_platform_notice' );
            return;
        }

        if ( version_compare( BP_PLATFORM_VERSION, '1.2.6', '<' ) ) {
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

    // Hook to schedule reply generation
    if (get_option('CHATGPTBBFORUMBOT_reply_to_direct_replies', '1') === '1') {
        add_action('bbp_new_reply', array('CHATGPTBBFORUMBOT_BB_Platform_Addon', 'chatgpt_bb_schedule_reply_generation'), 10, 5);
        add_action('chatgpt_bb_generate_reply_event', array('CHATGPTBBFORUMBOT_BB_Platform_Addon', 'chatgpt_bb_generate_reply'));
    }

    // Hook to schedule reply to new topic
    if (get_option('CHATGPTBBFORUMBOT_reply_to_new_introductions', '1' ) === '1') {
        add_action('bbp_new_topic', array('CHATGPTBBFORUMBOT_BB_Platform_Addon', 'chatgpt_bb_schedule_reply_to_new_topic_generation'), 10, 2);
        add_action('chatgpt_bb_generate_reply_to_new_topic_event', array('CHATGPTBBFORUMBOT_BB_Platform_Addon', 'chatgpt_bb_generate_reply_to_new_topic'));
        add_action('chatgpt_bb_post_reply_to_new_topic_event', array('CHATGPTBBFORUMBOT_BB_Platform_Addon', 'chatgpt_bb_post_reply_to_new_topic'));
    }
}
