<?php

namespace WooCustomAttributes\Inc;

class Hooks
{

    function activate()
    {
        flush_rewrite_rules();
        $db = new Database();
        $db->create_taxonomy_relations_table();
        update_option("woo_custom_attributes_auto_generate", false);
    }

    function deactivate()
    {
        flush_rewrite_rules();
    }

    function uninstall()
    {
        flush_rewrite_rules();
        $db = new Database();
        $db->drop_taxonomy_relations_table();
        delete_option("woo_custom_attributes_auto_generate");
    }

    function create_term_on_meta_update($meta_id, $post_id, $meta_key, $meta_value)
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
}