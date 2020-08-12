<?php

use WooCustomAttributes\Inc\Database;

function wag_activation_hook()
{
    flush_rewrite_rules();
    $db = new Database();
    $db->create_custom_attributes_templates_table();
    update_option("wag_auto_generate", false);
}

function wag_deactivation_hook()
{
    flush_rewrite_rules();
}

function wag_uninstallation_hook()
{
    flush_rewrite_rules();
    $db = new Database();
    $db->drop_custom_attributes_templates_table();
    delete_option("wag_auto_generate");
}

function wag_admin_menu()
{
    $main_page = add_menu_page('Woo Custom Attributes', 'Woo Custom Attributes', 'manage_options', 'woo_custom_attributes');
    $pages = [
        ['woo_custom_attributes', 'Woo Attributes Generator', 'Attributes', 'manage_options', 'woo_custom_attributes', 'wag_attributes_page_callback'],
        ['woo_custom_attributes', 'Woo Term Generator', 'Terms', 'manage_options', 'wag_terms', 'wag_terms_page_callback'],
        ['woo_custom_attributes', 'Woo Attributes Settings', 'Settings', 'manage_options', 'wag_settings', 'wag_settings_page_callback'],
    ];
    add_action('load-' . $main_page, 'wag_load_admin_scripts');
    foreach ($pages as $page) {
        $sub_page = add_submenu_page(...$page);
        add_action('load-' . $sub_page, 'wag_load_admin_scripts');
    }
}

function wag_attributes_page_callback()
{
    require_once plugin_dir_path(__FILE__) . 'template/attributes.php';
}

function wag_terms_page_callback()
{
    require_once plugin_dir_path(__FILE__) . 'template/terms.php';
}

function wag_settings_page_callback()
{
    require_once plugin_dir_path(__FILE__) . 'template/settings.php';
}

function wag_load_admin_scripts()
{
    add_action('admin_enqueue_scripts', 'wag_enqueue_bootstrap_scripts');
}

function wag_enqueue_bootstrap_scripts()
{
    wp_enqueue_style('st_bootstrap4_css', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css');
    wp_enqueue_script('st_jquery_slim_min', plugin_dir_path(__FILE__) . 'assets/css/jquery-3.5.1.slim.min.js', array('jquery'), '', true);
    wp_enqueue_script('st_popper_min', plugin_dir_path(__FILE__) . 'assets/css/popper.min.js', array('jquery'), '', true);
    wp_enqueue_script('st_bootstrap4_js', plugin_dir_path(__FILE__) . 'assets/css/bootstrap.min.js', array('jquery'), '', true);
}

function get_distinct_meta_attributes()
{
    $db = new Database();
    $distinct_meta_attributes = [];
    $meta_attributes = $db->select_all_product_attributes();
    foreach ($meta_attributes as $meta_attribute_array) {
        $attributes = unserialize($meta_attribute_array['meta_value']);
        foreach ($attributes as $attribute){
            if (!in_array($attribute['name'], $distinct_meta_attributes))
                $distinct_meta_attributes[] = $attribute['name'];
        }
    }
    return $distinct_meta_attributes;
}

function request_create_attribute_taxonomies(array $attribute_templates)
{
    $db = new Database();
    foreach ($attribute_templates as $attribute_template) {
        $db->insert_attribute_taxonomy($attribute_template);
    }
}

function hook_create_term_on_meta_update($meta_id, $post_id, $meta_key, $meta_value)
{
    if ($meta_key === '_product_attributes') {
        foreach ($meta_value as $attribute) {
            if ($taxonomy_id = wc_attribute_taxonomy_id_by_name($attribute['name'])) {
                $pa_name = wc_attribute_taxonomy_name($attribute['name']);
                $object_term = wp_set_object_terms($post_id, $attribute['value'], $pa_name, true);
                if (is_wp_error($object_term)) {
                    var_dump($object_term);
                    return;
                }
            }
        }
    }
}

function request_create_terms(array $attribute_taxonomies)
{
    $db = new Database();
    $product_meta_attributes = $db->select_all_product_attributes();
    foreach ($attribute_taxonomies as $taxonomy) {
        foreach ($product_meta_attributes as $product_meta_attribute) {
            $product_attributes = unserialize($product_meta_attribute['meta_value']);
            $taxonomy_name = array_column($product_attributes, 'name');
            $i = array_search($taxonomy, $taxonomy_name);
            $attribute = $product_attributes[$i];

            if ($attribute['name'] !== $taxonomy)
                continue;
            if ($taxonomy_id = wc_attribute_taxonomy_id_by_name($attribute['name'])) {
                $pa_name = wc_attribute_taxonomy_name($attribute['name']);
                $object_term = wp_set_object_terms($product_meta_attribute['post_id'], $attribute['value'], $pa_name, true);
                if (is_wp_error($object_term)) {
                    var_dump($object_term);
                    return;
                }
                $product_attributes_data[$pa_name] = [
                    'name' => $pa_name,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 1,
                ];
            }
        }
    }
}