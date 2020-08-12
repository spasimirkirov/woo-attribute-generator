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
        $sql = "SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id` IN (SELECT ID FROM `wp_posts` WHERE `post_type` = 'product') AND meta_key ='_product_attributes';";
        return $this->wpdb()->get_results($sql, 'ARRAY_A');
    }

    function insert_custom_attribute_template($name)
    {
        $sql = $this->wpdb()->prepare("INSERT INTO `{$this->wpdb()->base_prefix}custom_attributes_templates` (`name`, `active`) VALUES ('%s', 0);", $name);
        $this->wpdb()->query($sql);
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
}






