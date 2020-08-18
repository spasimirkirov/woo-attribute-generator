<?php

use WooCustomAttributes\Inc\Database;

function wca_activation_hook()
{
    flush_rewrite_rules();
    $db = new Database();
    $db->create_taxonomy_relations_table();
    update_option("wag_auto_generate", false);
}

function wca_deactivation_hook()
{
    flush_rewrite_rules();
}

function wca_uninstallation_hook()
{
    flush_rewrite_rules();
    $db = new Database();
    $db->drop_taxonomy_relations_table();
    delete_option("wag_auto_generate");
}

function wag_admin_menu()
{
    $main_page = add_menu_page('Woo Custom Attributes', 'Woo Custom Attributes', 'manage_options', 'woo_custom_attributes');
    $pages = [
        ['woo_custom_attributes', 'Woo Attributes Relation', 'Relations', 'manage_options', 'woo_custom_attributes', 'wag_relation_page_callback'],
        ['woo_custom_attributes', 'Woo Attributes Settings', 'Settings', 'manage_options', 'settings', 'wag_settings_page_callback'],
    ];
    add_action('load-' . $main_page, 'wag_load_admin_scripts');
    foreach ($pages as $page) {
        $sub_page = add_submenu_page(...$page);
        add_action('load-' . $sub_page, 'wag_load_admin_scripts');
    }
}

function wag_relation_page_callback()
{
    require_once plugin_dir_path(__FILE__) . 'template/relation.php';
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
        $attributes = unserialize($meta_attribute_array['_product_attributes']);
        foreach ($attributes as $attribute) {
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
//    $db = new Database();
//    if ($meta_key === '_product_attributes') {
//        foreach ($meta_value as $attribute) {
//            if ($taxonomy_id = wc_attribute_taxonomy_id_by_name($attribute['name'])) {
//                $pa_attribute = attach_post_term_meta($post_id, $attribute['value'], $attribute['name']);
//                if (!is_wp_error($pa_attribute)) {
//                    $meta_value[$pa_attribute['name']] = $pa_attribute;
//                    $db->update_post_meta_attributes($post_id, serialize($meta_value));
//                }
//            }
//        }
//    }
}

function attach_post_term_meta($post_id, $term, $taxonomy)
{
    $pa_name = wc_attribute_taxonomy_name($taxonomy);
    $object_term = wp_set_object_terms($post_id, $term, $pa_name, true);
    if (is_wp_error($object_term)) {
        var_dump($object_term);
        return $object_term;
    }

    return array([
        'name' => $pa_name,
        'is_visible' => 1,
        'is_variation' => 0,
        'is_taxonomy' => 1,
    ]);
}

function request_create_relation(int $taxonomy_id, array $meta_names)
{
    $db = new Database();
    foreach ($meta_names as $meta_name) {
        $relation = is_taxonomy_meta_related($taxonomy_id, $meta_name);
        if ($relation) {
            show_message('<div class="error notice notice-error is-dismissible"><p>' . $relation['attribute_label'] . ' и ' . $relation['meta_name'] . ' вече са релативни</p></div>');
            return;
        }
        $rows = $db->insert_taxonomy_relations($taxonomy_id, $meta_name);
        if ($rows && $rows > 0)
            show_message('<div class="updated notice notice-success is-dismissible"><p>Успешно добавяне на ' . $meta_name . ' към релации</p></div>');
    }

}

function request_delete_relation(array $relation_ids)
{
    $db = new Database();
    $rows = $db->delete_taxonomy_relations($relation_ids);
    if ($rows && $rows > 0)
        show_message('<div class="updated notice notice-success is-dismissible"><p> Успешно изтриване на ' . $rows . ' релации</p></div>');
}

function is_taxonomy_meta_related($taxonomy_id, $meta_name)
{
    $db = new Database();
    return $db->select_taxonomy_relations(['taxonomy_id' => $taxonomy_id, 'meta_name' => $meta_name, 'row' => 0]);
}

function request_create_terms(string $taxonomy, array $attribute_labels)
{
    wp_raise_memory_limit();
    $db = new Database();
    $products = array_map(function ($arr) {
        $arr['_product_attributes'] = unserialize($arr['_product_attributes']);
        return $arr;
    }, $db->select_all_product_attributes());
    foreach ($products as $product) {
        if (!is_array($product['_product_attributes']))
            continue;
        foreach ($product['_product_attributes'] as $product_attribute) {
            if (!in_array($product_attribute['name'], $attribute_labels))
                continue;
            if ($taxonomy_id = wc_attribute_taxonomy_id_by_name($taxonomy)) {
                var_dump($product_attribute, $taxonomy);
                $pa_attribute = attach_post_term_meta($product['post_id'], $product_attribute['value'], $taxonomy);
                if (!is_wp_error($pa_attribute)) {
                    $_product_attributes[$pa_attribute['name']] = $pa_attribute;
                    $_product_attributes_serialized = serialize($_product_attributes);
                    $db->update_post_meta_attributes($product['post_id'], $_product_attributes_serialized);
                }
            }
            break;
        }
    }
}