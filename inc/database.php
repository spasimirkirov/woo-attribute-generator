<?php

namespace WooCustomAttributes\Inc;

class Database
{
    public function wpdb()
    {
        global $wpdb;
        return $wpdb;
    }

    function select_all_product_attributes()
    {
        $prefix = $this->wpdb()->base_prefix;
        $sql = "SELECT `post_id`, `meta_value` as `_product_attributes` FROM `{$prefix}postmeta` WHERE `post_id` IN (SELECT ID FROM `{$prefix}posts` WHERE `post_type` = 'product') AND meta_key ='_product_attributes';";
        return $this->wpdb()->get_results($sql, 'ARRAY_A');
    }

    function select_attribute_taxonomy(array $params = [])
    {
        $query = "SELECT * FROM `{$this->wpdb()->base_prefix}woocommerce_attribute_taxonomies` WHERE `attribute_id` > 0";
        if (isset($params['name']))
            $query .= $this->wpdb()->prepare(" AND `attribute_label` = '%s'", $params['name']);
        return $this->wpdb()->get_results($query, 'ARRAY_A');
    }

    function insert_attribute_taxonomy($attribute_name)
    {
        return wc_create_attribute([
            'name' => $attribute_name,
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => false,
        ]);
    }

    public function update_post_meta_attributes($post_id, string $serialized_value)
    {
        $prefix = $this->wpdb()->base_prefix;
        $sql = $this->wpdb()
            ->prepare("UPDATE `{$prefix}postmeta` SET `meta_value` = '%s' WHERE `post_id` = '%d' AND `meta_key` = '%s'",
                $serialized_value, $post_id, '_product_attributes');
        return $this->wpdb()->query($sql);
    }
}






