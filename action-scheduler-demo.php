<?php
/**
 * Plugin Name: Action Scheduler Demos
 * Plugin URI: https://github.com/faithruth/action-scheduler-demo
 * Author: Imokol Faith Ruth
 * Author URI: https://github.com/faithruth
 * Description: Plugin to demonstrate the use of action scheduler plugin.
 * Version: 1.0.0
 * License: GPL2
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: action-scheduler-demo
 *
 *
 * @package ActionSchedulerDemo.
 */

// add basic plugin security.
defined( 'ABSPATH' ) || die;

if ( ! defined( 'AS_DEMO_DIR_PATH' ) ) {
	define( 'AS_DEMO_DIR_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'AS_DEMO_DIR_URL' ) ) {
	define( 'AS_DEMO_DIR_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}

if ( ! defined( 'AS_DEMO_PLUGIN' ) ) {
	define( 'AS_DEMO_PLUGIN', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'AS_DEMO_PLUGIN_FILE' ) ) {
	define( 'AS_DEMO_PLUGIN_FILE', __FILE__ );
}

register_activation_hook(__FILE__, 'plugin_dependancy_check');

function plugin_dependancy_check() {
    // Check if the required plugin is active.
    if (!is_plugin_active('action-scheduler/action-scheduler.php')) {
        // Deactivate the custom plugin.
        deactivate_plugins(plugin_basename(__FILE__));

        // Set the admin notice.
        add_action('admin_notices', 'broadcast_notification');
    }
}

function broadcast_notification() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e('Action Scheduler Demos requires Action Scheduler Plugin to be installed and activated. Please install and activate Action Scheduler Plugin.', 'action-scheduler-demo'); ?></p>
    </div>
    <?php
}

// Do the initial plugin stuff on activation.
require_once AS_DEMO_DIR_PATH. '/includes/class-asd-daily-jokes.php';
require_once AS_DEMO_DIR_PATH . '/includes/class-asd-daily-quotes.php';