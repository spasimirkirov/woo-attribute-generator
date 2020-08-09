<?php

function wag_activation_hook()
{
    flush_rewrite_rules();
    db_create_attribute_templates_table();
}

function wag_deactivation_hook()
{
    flush_rewrite_rules();
}

function wag_uninstallation_hook()
{
    flush_rewrite_rules();
    db_drop_wag_attribute_templates_table();
}

function wag_admin_menu_option()
{
    add_menu_page('Woo Attributes Generator', 'Woo Attributes', 'manage_options', 'wag_attributes');
    add_submenu_page('wag_attributes', 'Woo Attributes Generator', 'Attributes', 'manage_options', 'wag_attributes',
        'wag_attributes_page_callback');
    add_submenu_page('wag_attributes', 'Woo Term Generator', 'Terms', 'manage_options', 'wag_terms',
        'wag_terms_page_callback');
    add_submenu_page('wag_attributes', 'Woo Attributes Settings', 'Settings', 'manage_options', 'wag_settings',
        'wag_settings_page_callback');
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

function wag_on_post_meta_update_hook($meta_id, $post_id, $meta_key, $meta_value)
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

function request_create_attributes(array $attribute_templates)
{
    foreach ($attribute_templates as $attribute_template) {
        db_insert_attribute_taxonomy($attribute_template);
    }
}

function request_create_terms(array $attribute_taxonomies)
{
    $product_metas = db_select_product_attributes();
    foreach ($attribute_taxonomies as $taxonomy) {
        foreach ($product_metas as $product_meta) {
            $product_attributes = unserialize($product_meta['meta_value']);
            $taxonomy_name = array_column($product_attributes, 'name');
            $i = array_search($taxonomy, $taxonomy_name);
            $attribute = $product_attributes[$i];

            if ($attribute['name'] !== $taxonomy)
                continue;
            if ($taxonomy_id = wc_attribute_taxonomy_id_by_name($attribute['name'])) {
                $pa_name = wc_attribute_taxonomy_name($attribute['name']);
                $object_term = wp_set_object_terms($product_meta['post_id'], $attribute['value'], $pa_name, true);
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