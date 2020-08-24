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

use WooCustomAttributes\Inc\Hooks;
use WooCustomAttributes\Inc\WooAttributePlugin;

$dependencies = [
    'woocommerce/woocommerce.php' => 'WooCommerce',
    'woo-product-attributes/plugin.php' => 'Woo Product Attributes',
];

$errors = 0;
foreach ($dependencies as $plugin_file => $plugin_name) {
    $is_plugin_active = in_array($plugin_file, apply_filters('active_plugins', get_option('active_plugins')));
    if (!$is_plugin_active) {
        add_action('admin_notices', function () use ($plugin_name) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><?php _e('' . $plugin_name . ' е неактивен, моля активирайте го преди да използвате Woo Custom Attributes'); ?></p>
            </div>
            <?php
        });
        $errors++;
    }
}
if ($errors === 0) {
    // include the Composer autoload file
    require plugin_dir_path(__FILE__) . 'vendor/autoload.php';
    $hooks = new Hooks();
    register_activation_hook(__FILE__, [$hooks, 'activate']);
    register_deactivation_hook(__FILE__, [$hooks, 'deactivate']);
    register_uninstall_hook(__FILE__, [$hooks, 'uninstall']);
    $WooAttributePlugin = new WooAttributePlugin();
    $WooAttributePlugin->init();
}