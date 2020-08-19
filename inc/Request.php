<?php


namespace WooCustomAttributes\Inc;


class Request
{
    private $db;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    public function create_attribute_taxonomies(array $attribute_templates)
    {
        foreach ($attribute_templates as $attribute_template) {
            $this->db->insert_attribute_taxonomy($attribute_template);
        }
    }

    public function create_relation(int $taxonomy_id, array $meta_names)
    {
        foreach ($meta_names as $meta_name) {
            $relation = Api::is_taxonomy_meta_related($taxonomy_id, $meta_name);
            if ($relation) {
                show_message('<div class="error notice notice-error is-dismissible"><p>' . $relation['attribute_label'] . ' и ' . $relation['meta_name'] . ' вече са релативни</p></div>');
                return;
            }
            $rows = $this->db->insert_taxonomy_relations($taxonomy_id, $meta_name);
            if ($rows && $rows > 0)
                show_message('<div class="updated notice notice-success is-dismissible"><p>Успешно добавяне на ' . $meta_name . ' към релации</p></div>');
        }
    }

    public function delete_relation(array $relation_ids)
    {
        $rows = $this->db->delete_taxonomy_relations($relation_ids);
        if ($rows && $rows > 0)
            show_message('<div class="updated notice notice-success is-dismissible"><p> Успешно изтриване на ' . $rows . ' релации</p></div>');
    }

    public function create_terms(string $taxonomy, array $attribute_labels)
    {
        wp_raise_memory_limit();
        $products = array_map(function ($arr) {
            $arr['_product_attributes'] = unserialize($arr['_product_attributes']);
            return $arr;
        }, $this->db->select_all_product_attributes());

        foreach ($products as $product) {
            if (!is_array($product['_product_attributes']))
                continue;
            foreach ($product['_product_attributes'] as $product_attribute) {
                if (!in_array($product_attribute['name'], $attribute_labels))
                    continue;
                if ($taxonomy_id = wc_attribute_taxonomy_id_by_name($taxonomy)) {
                    var_dump($product_attribute, $taxonomy);
                    $pa_attribute = Api::attach_post_term_meta($product['post_id'], $product_attribute['value'], $taxonomy);
                    if (!is_wp_error($pa_attribute)) {
                        $_product_attributes[$pa_attribute['name']] = $pa_attribute;
                        $_product_attributes_serialized = serialize($_product_attributes);
                        $this->db->update_post_meta_attributes($product['post_id'], $_product_attributes_serialized);
                    }
                }
                break;
            }
        }
    }

    public function generate_terms($relation_ids)
    {
        foreach ($relation_ids as $relation_id) {
            $relation = $this->db->select_taxonomy_relations(['id' => $relation_id]);
            var_dump($relation);

        }
    }
}