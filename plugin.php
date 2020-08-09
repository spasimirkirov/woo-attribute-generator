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
    private $db;
    private string $plugin_path;
    private string $template_path;
    private string $includes_path;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->template_path = plugin_dir_path(__FILE__) . '/template';
        $this->includes_path = plugin_dir_path(__FILE__) . '/inc';
        $this->register();
    }

    function register()
    {
        $this->includes();
        add_action('admin_menu', 'wag_admin_menu_option');
        add_action('updated_post_meta', 'create_attribute_template', 10, 4);
        add_action('added_post_meta', 'create_attribute_template', 10, 4);
    }

    function includes()
    {
        require_once $this->includes_path . '/callbacks.php';
        require_once $this->includes_path . '/database.php';
    }

    function template($name)
    {
        return require_once $this->template_path . '/' . $name . '.php';
    }
}

register_activation_hook(__FILE__, 'wag_activation_hook');
register_deactivation_hook(__FILE__, 'wag_deactivation_hook');
register_uninstall_hook(__FILE__, 'wag_uninstallation_hook');


$wooAttributePlugin = new WooAttributePlugin();