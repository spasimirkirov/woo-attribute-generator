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
	        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	        `taxonomy_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `meta_name` nvarchar(50) NOT NULL,
            PRIMARY KEY  (`id`)
            ) $charset_collate;";
        dbDelta($sql);
    }

    public function drop_taxonomy_relations_table()
    {
        $this->wpdb()->query("DROP TABLE IF EXISTS `{$this->wpdb()->base_prefix}wca_taxonomy_relations`");
    }


    public function select_taxonomy_relations($params = [])
    {
        $sql = "SELECT a.`id` , a.`taxonomy_id`, b.`attribute_label`, a.`meta_name` FROM `{$this->wpdb()->base_prefix}wca_taxonomy_relations` AS a ";
        $sql .= " INNER JOIN `{$this->wpdb()->base_prefix}woocommerce_attribute_taxonomies` as b ON a.`taxonomy_id` = b.`attribute_id`";

        $sql .= isset($params['id']) ?
            $this->wpdb()->prepare(" WHERE a.`id` = '%d'", $params['id']) :
            " WHERE a.`id` > '0'";

        if (isset($params['taxonomy_id']))
            $sql .= $this->wpdb()->prepare(" AND a.`taxonomy_id` = '%d'", $params['taxonomy_id']);

        if (isset($params['meta_name']))
            $sql .= $this->wpdb()->prepare(" AND a.`meta_name` = '%s'", $params['meta_name']);

        if (isset($params['attribute_label']))
            $sql .= $this->wpdb()->prepare(" AND b.`attribute_label` = '%s'", $params['attribute_label']);

        $sql .= " ORDER BY b.`attribute_label` ASC";

        if (isset($params['col']))
            return $this->wpdb()->get_col($sql, $params['col']);

        if (isset($params['row']))
            return $this->wpdb()->get_row($sql, 'ARRAY_A', $params['row']);

        return $this->wpdb()->get_results($sql, 'ARRAY_A');
    }

    public function select_taxonomy_relation_labels()
    {
        $results = $this->select_taxonomy_relations(['col' => 2]);
        return array_unique($results);
    }

    public function insert_taxonomy_relations($taxonomy, $meta)
    {
        return $this->wpdb()->insert("{$this->wpdb()->base_prefix}wca_taxonomy_relations", [
            'taxonomy_id' => $taxonomy,
            'meta_name' => $meta
        ]);
    }

    public function delete_taxonomy_relations(array $relation_ids)
    {
        $sql = "DELETE FROM `{$this->wpdb()->base_prefix}wca_taxonomy_relations`";
        $sql .= " WHERE `id` IN(" . implode(",", $relation_ids) . ");";
        return $this->wpdb()->query($sql);
    }

    public function select_all_product_attributes()
    {
        $prefix = $this->wpdb()->base_prefix;
        $sql = "SELECT `post_id`, `meta_value` as `_product_attributes` FROM `{$prefix}postmeta` WHERE `post_id` IN (SELECT ID FROM `{$prefix}posts` WHERE `post_type` = 'product') AND meta_key ='_product_attributes';";
        return $this->wpdb()->get_results($sql, 'ARRAY_A');
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





