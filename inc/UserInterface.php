<?php


namespace WooCustomAttributes\Inc;


class UserInterface
{
    private $pages;
    private $path;

    public function __construct()
    {
        $this->path = plugin_dir_url(__FILE__);
    }

    public function register_pages()
    {
        $this->pages['main'] = add_menu_page('Woo Custom Attributes', 'Woo Custom Attributes', 'manage_options', 'woo_custom_attributes');
        $this->pages['sub'] = [
            ['woo_custom_attributes', 'Woo Custom Attributes', 'Релации', 'manage_options', 'woo_custom_attributes', [$this, 'woo_custom_attributes_page']],
            ['woo_custom_attributes', 'Woo Custom Attributes Settings', 'Настройки', 'manage_options', 'woo_custom_attributes_settings', [$this, 'woo_custom_attributes_settings_page']],
        ];
    }

    public function woo_custom_attributes_page()
    {
        require_once plugin_dir_path(__FILE__) . 'template/list.php';
    }

    public function woo_custom_attributes_settings_page()
    {
        require_once plugin_dir_path(__FILE__) . 'template/settings.php';
    }

    public function load_scripts()
    {
        add_action('load-' . $this->pages['main'], [$this, 'enqueue_scripts']);
        foreach ($this->pages['sub'] as $page) {
            $sub_page = add_submenu_page(...$page);
            add_action('load-' . $sub_page, [$this, 'enqueue_scripts']);
        }
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('bootstrap4_css', $this->path . 'assets/css/bootstrap.min.css');
        wp_enqueue_script('jquery_slim_min', $this->path . 'assets/js/jquery-3.5.1.slim.min.js', array('jquery'), '', true);
        wp_enqueue_script('popper_min', $this->path . 'assets/js/popper.min.js', array('jquery'), '', true);
        wp_enqueue_script('bootstrap4_js', $this->path . 'assets/js/bootstrap.min.js', array('jquery'), '', true);
        wp_enqueue_script('plugin_main_js', $this->path . 'assets/js/main.js', '', '', true);
    }
}