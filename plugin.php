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

class WooAttributePlugin
{
    private $plugin_path;
    private $includes_path;

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->includes_path = $this->plugin_path . 'inc';
    }

    function init()
    {
        $this->includes();
        if (get_option('wag_auto_generate', false))
            add_action('added_post_meta', 'hook_create_term_on_meta_update', 10, 4);
        add_action('admin_menu', 'wag_admin_menu');
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

register_activation_hook(__FILE__, 'wca_activation_hook');
register_deactivation_hook(__FILE__, 'wca_deactivation_hook');
register_uninstall_hook(__FILE__, 'wca_uninstallation_hook');

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
