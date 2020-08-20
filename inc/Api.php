<?php


namespace WooCustomAttributes\Inc;


class Api
{
    protected $db;

    /**
     * Api constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * @return array
     */
    public static function list_distinct_meta_attributes()
    {
        $db = new Database();
        $distinct_meta_attributes = [];
        $meta_attributes = $db->select_all_product_attributes();
        foreach ($meta_attributes as $meta_attribute_array) {
            $attributes = unserialize($meta_attribute_array['_product_attributes']);
            foreach ($attributes as $attribute) {
                if (!in_array($attribute['name'], $distinct_meta_attributes) && !$attribute['is_taxonomy'])
                    $distinct_meta_attributes[] = $attribute['name'];
            }
        }
        return $distinct_meta_attributes;
    }

    /** Checks if product catalog attribute and meta attribute are related
     * @param $taxonomy_id
     * @param $meta_name
     * @return array|object|void|null
     */
    static function is_taxonomy_meta_related($taxonomy_id, $meta_name)
    {
        $db = new Database();
        return $db->select_taxonomy_relations(['taxonomy_id' => $taxonomy_id, 'meta_name' => $meta_name, 'row' => 0]);
    }

    public function create_relation(int $taxonomy_id, $meta_name)
    {
        return $this->db->insert_taxonomy_relations($taxonomy_id, $meta_name);;
    }

    public function remove_relations(array $relation_ids)
    {
        return $this->db->delete_taxonomy_relations($relation_ids);
    }

    public function get_relations()
    {
        return $this->db->select_taxonomy_relations();
    }

    public function get_relation_taxonomy_ids()
    {
        return $this->db->select_taxonomy_relations(['col' => 1]);
    }

    public function get_relation_taxonomy_labels()
    {
        return array_unique($this->db->select_taxonomy_relations(['col' => 2]));
    }

    public function get_relation_by_id(int $relation_id)
    {
        return $this->db->select_taxonomy_relations(['id' => $relation_id, 'row' => 0]);
    }

    public function get_relation_by_label($taxonomy_label)
    {
        return $this->db->select_taxonomy_relations(['attribute_label' => $taxonomy_label]);
    }

    public function get_relation_meta_names()
    {
        return array_filter($this->db->select_taxonomy_relations(['col' => 3]));
    }

    public function get_postmeta_by_meta($meta_name)
    {
        return $this->db->select_postmeta_by_metaname($meta_name);
    }

    /**
     * Searches in array for a specific field and value
     * @param $array
     * @param $field
     * @param $value
     * @return mixed|null
     */
    public function array_search_attribute_by_name($array, $value)
    {
        foreach ($array as $key => $row) {
            if ($row['name'] === $value)
                return $row;
        }
        return null;
    }

    public function handle_generate_terms($relation, $post_id, $post_meta)
    {
        $_product_meta_attributes = is_serialized($post_meta) ? unserialize($post_meta) : $post_meta;
        $_product_attribute = $this->array_search_attribute_by_name($_product_meta_attributes, $relation['meta_name']);
        if (!$_product_attribute)
            return;
        var_dump($relation, $post_id, $_product_attribute);
        var_dump('-----');
        $pa_name = wc_attribute_taxonomy_name($relation['attribute_label']);
        $tern_attribute = [
            $pa_name => [
                'name' => $pa_name,
                'value' => $_product_attribute['value'],
                'is_taxonomy' => 1,
                'is_visible' => 1,
                'is_variation' => 0,
            ]
        ];
        $this->db->update_postmeta($post_id, maybe_serialize(array_merge($_product_meta_attributes, $tern_attribute)));
        $object_term = wp_set_object_terms($post_id, $_product_attribute['value'], $pa_name, true);
        var_dump([$pa_name => [
            'name' => $pa_name,
            'value' => $_product_attribute['value'],
            'is_taxonomy' => 1,
            'is_visible' => 1,
            'is_variation' => 0,
        ]]);
        if (is_wp_error($object_term)) {
            var_dump($object_term);
            return;
        }
    }
}