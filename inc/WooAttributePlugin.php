<?php


namespace WooCustomAttributes\Inc;

class WooAttributePlugin
{
    private $plugin_path;
    private $includes_path;

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->includes_path = $this->plugin_path . 'inc';
    }

    public function init()
    {
        if (get_option('woo_custom_attributes_auto_generate', false))
            add_action('added_post_meta', [new Hooks(), 'create_term_on_meta_update'], 10, 4);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'register_interface']);
    }

    public function register_settings()
    {
        register_setting('woo_custom_attributes_option_group', 'woo_custom_attributes_auto_generate', [
            'type' => 'boolean',
            'description' => 'Enable/Disable terms auto generate upon product import',
            'default' => false
        ]);
        add_settings_section('woo_custom_attributes_plugin_configuration', 'Настройки', '', 'woo_custom_attributes_settings');
        add_settings_field(
            'woo_custom_attributes_auto_generate',
            'Генериране на термини при вкарване на продукт?',
            function () {
                echo '<input type="checkbox" name="woo_custom_attributes_auto_generate" value="1" ' . checked('1', get_option('woo_custom_attributes_auto_generate'), false) . '/>';
            },
            'woo_custom_attributes_settings',
            'woo_custom_attributes_plugin_configuration',
            ['label_for' => 'woo_custom_attributes_auto_generate']
        );
    }

    public function register_interface(){
        $interface = new UserInterface();
        $interface->register_pages();
        $interface->load_scripts();
    }
}