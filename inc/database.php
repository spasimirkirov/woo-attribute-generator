<?php

namespace WooCustomAttributes\Inc;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class Database
{
    public function wpdb()
    {
        global $wpdb;
        return $wpdb;
    }

    public function create_taxonomy_relations_table()
    {

        $charset_collate = $this->wpdb()->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->wpdb()->base_prefix}wca_taxonomy_relations` (
            `id` bigint(20) UNSIGNED NOT NULL,
            `taxonomy` nvarchar(50) NOT NULL,
            `meta_name` nvarchar(50) NOT NULL,
            PRIMARY KEY  (`id`)
            ) $charset_collate;";
        dbDelta($sql);
    }

    public function drop_taxonomy_relations_table()
    {
        $this->wpdb()->query("DROP TABLE IF EXISTS `{$this->wpdb()->base_prefix}wca_taxonomy_relations`");
    }

    public function select_taxonomy_relations()
    {
        return $this->wpdb()->get_results("SELECT * FROM `{$this->wpdb()->base_prefix}wca_taxonomy_relations`", 'ARRAY_A');
    }

    public function insert_taxonomy_relations($taxonomy, $meta_names)
    {
        foreach ($meta_names as $meta_name) {
            $this->wpdb()->insert("{$this->wpdb()->base_prefix}wca_taxonomy_relations", [
                'taxonomy' => $taxonomy,
                'meta_name' => $meta_name
            ]);
        }
    }

    public function delete_taxonomy_relations(array $relation_ids)
    {
        $sql = $this->wpdb("DELETE FROM `{$this->wpdb()->base_prefix}wca_taxonomy_relations`");
        $sql .= "WHERE `id` IN('" . implode(", '", $relation_ids) . "');";
        $this->wpdb()->query($sql);
    }

    public function select_all_product_attributes()
    {
        $prefix = $this->wpdb()->base_prefix;
        $sql = "SELECT `post_id`, `meta_value` as `_product_attributes` FROM `{$prefix}postmeta` WHERE `post_id` IN (SELECT ID FROM `{$prefix}posts` WHERE `post_type` = 'product') AND meta_key ='_product_attributes';";
        return $this->wpdb()->get_results($sql, 'ARRAY_A');
    }

    public function select_attribute_taxonomy(array $params = [])
    {
        $query = "SELECT * FROM `{$this->wpdb()->base_prefix}woocommerce_attribute_taxonomies` WHERE `attribute_id` > 0";
        if (isset($params['name']))
            $query .= $this->wpdb()->prepare(" AND `attribute_label` = '%s'", $params['name']);
        return $this->wpdb()->get_results($query, 'ARRAY_A');
    }

    public function insert_attribute_taxonomy($attribute_name)
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
        $sql = $this->wpdb()
            ->prepare("UPDATE `{$this->wpdb()->base_prefix}postmeta` SET `meta_value` = '%s' WHERE `post_id` = '%d' AND `meta_key` = '%s'",
                $serialized_value, $post_id, '_product_attributes');
        return $this->wpdb()->query($sql);
    }

}






