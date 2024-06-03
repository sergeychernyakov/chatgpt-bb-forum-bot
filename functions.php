<?php
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

if ( ! function_exists( 'CHATGPTBBFORUMBOT_get_settings_fields' ) ) {
	function CHATGPTBBFORUMBOT_get_settings_fields() {

		$fields = array();

		$fields['CHATGPTBBFORUMBOT_settings_section'] = array(

			'CHATGPTBBFORUMBOT_openai_assistant_id' => array(
				'title'             => __( 'OpenAI Assistant ID', 'chatgpt-bb-forum-bot' ),
				'callback'          => 'CHATGPTBBFORUMBOT_settings_callback_openai_assistant_id',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),

			'CHATGPTBBFORUMBOT_openai_api_key' => array(
				'title'             => __( 'OpenAI API Key', 'chatgpt-bb-forum-bot' ),
				'callback'          => 'CHATGPTBBFORUMBOT_settings_callback_openai_api_key',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),

		);

		return (array) apply_filters( 'CHATGPTBBFORUMBOT_get_settings_fields', $fields );
	}
}

if ( ! function_exists( 'CHATGPTBBFORUMBOT_settings_callback_openai_assistant_id' ) ) {
	function CHATGPTBBFORUMBOT_settings_callback_openai_assistant_id() {
		$option = get_option( 'CHATGPTBBFORUMBOT_openai_assistant_id', '' );
		?>
        <div style="position: relative; display: inline-block;">
            <input name="CHATGPTBBFORUMBOT_openai_assistant_id"
                   id="CHATGPTBBFORUMBOT_openai_assistant_id"
                   type="password"
                   value="<?php echo esc_attr( $option ); ?>"
                   style="padding-right: 30px;"
            />
            <span class="dashicons dashicons-visibility" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('CHATGPTBBFORUMBOT_openai_assistant_id')"></span>
        </div>
        <label for="CHATGPTBBFORUMBOT_openai_assistant_id">
			<?php _e( 'Enter your OpenAI Assistant ID', 'chatgpt-bb-forum-bot' ); ?>
        </label>
		<?php
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
// if ( ! function_exists( 'CHATGPTBBFORUMBOT_bp_admin_setting_general_register_fields' ) ) {
//     function CHATGPTBBFORUMBOT_bp_admin_setting_general_register_fields( $setting ) {
// 	    // Main General Settings Section
// 	    $setting->add_section( 'CHATGPTBBFORUMBOT_addon', __( 'ChatGPT BB Forum Bot Settings', 'chatgpt-bb-forum-bot' ) );

// 	    $args          = array();
// 	    $setting->add_field( 'bp-enable-my-addon', __( 'My Field', 'chatgpt-bb-forum-bot' ), 'CHATGPTBBFORUMBOT_admin_general_setting_callback_my_addon', 'intval', $args );
//     }

// 	add_action( 'bp_admin_setting_general_register_fields', 'CHATGPTBBFORUMBOT_bp_admin_setting_general_register_fields' );
// }

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
