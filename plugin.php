<?php
/*
Plugin Name: Woo Attribute Generator
Plugin URI: https://github.com/spasimirkirov/woo-attribute-generator
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

class WooAttributePlugin
{
    private string $plugin_path;
    private string $includes_path;

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->includes_path = $this->plugin_path . '/inc';
        $this->register();
    }

    function register()
    {
        $this->includes();
        add_action('admin_menu', 'wag_admin_menu_option');
        add_action('admin_init', [$this, 'settings']);
    }

    function includes()
    {
        require_once $this->includes_path . '/database.php';
        require_once $this->includes_path . '/callbacks.php';
    }

    function settings()
    {

        register_setting('wag_option_group', 'wag_auto_generate', [
            'type' => 'boolean',
            'description' => 'Enable/Disable terms auto generate upon product import',
            'default' => false
        ]);

        add_settings_section('wag_plugin_configuration', 'Settings', '', 'wag_settings');

        add_settings_field(
            'wag_auto_generate',
            'Генериране на термини при вкарване на продукт?',
            function () {
                echo '<input type="checkbox" name="wag_auto_generate" value="1" ' . checked('1', get_option('wag_auto_generate'), false) . '/>';
            },
            'wag_settings',
            'wag_plugin_configuration',
            ['label_for' => 'wag_auto_generate'],
        );
    }
}

register_activation_hook(__FILE__, 'wag_activation_hook');
register_deactivation_hook(__FILE__, 'wag_deactivation_hook');
register_uninstall_hook(__FILE__, 'wag_uninstallation_hook');

if (get_option('wag_auto_generate', false))
    add_action('added_post_meta', 'wag_on_post_meta_update_hook', 10, 4);

$wooAttributePlugin = new WooAttributePlugin();