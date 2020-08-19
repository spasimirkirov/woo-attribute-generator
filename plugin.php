<?php
/*
Plugin Name: Woo Custom Attributes
Plugin URI: https://github.com/spasimirkirov/woo-custom-attributes
Description: Generates WooCommerce Attributes and Terms from product's attributes
Version: 1.0.0
Author: Spasimir Kirov
Author URI: https://www.vonchronos.com/
License: GPLv2 or later
Text Domain: woo-attribute-generator
 */

if (!defined('ABSPATH')) {
    die;
}
define('BASE_PATH', plugin_dir_path(__FILE__));
define('BASE_URL', plugin_dir_url(__FILE__));

// include the Composer autoload file
require BASE_PATH . 'vendor/autoload.php';

use WooCustomAttributes\Inc\Hooks;
use WooCustomAttributes\Inc\WooAttributePlugin;

$hooks = new Hooks();
register_activation_hook(__FILE__, [$hooks, 'activate']);
register_deactivation_hook(__FILE__, [$hooks, 'deactivate']);
register_uninstall_hook(__FILE__, [$hooks, 'uninstall']);

$is_woo_active = in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
if (!$is_woo_active) {
    add_action('admin_notices', function () {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('WooCommerce е неактивен, моля активирайте го преди да използвате Woo Attribute Generator'); ?></p>
        </div>
        <?php
    });
} else {
    $WooAttributePlugin = new WooAttributePlugin();
    $WooAttributePlugin->init();
}
