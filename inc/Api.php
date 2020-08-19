<?php


namespace WooCustomAttributes\Inc;


class Api
{

    public static function list_distinct_meta_attributes()
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

    static function attach_post_term_meta($post_id, $term, $taxonomy)
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
    static function is_taxonomy_meta_related($taxonomy_id, $meta_name)
    {
        $db = new Database();
        return $db->select_taxonomy_relations(['taxonomy_id' => $taxonomy_id, 'meta_name' => $meta_name, 'row' => 0]);
    }
}