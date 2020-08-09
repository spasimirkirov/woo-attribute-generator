<?php

function create_attributes_templates_table()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}attribute_templates` (
	    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        category_id mediumint(9) NOT NULL,
        name varchar (28)NOT NULL,
        active bool NOT NULL DEFAULT false,
        PRIMARY KEY  (id)
        ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    update_option("woo_attribute_templates_db_version", 1.0);
}

function drop_attributes_templates_table()
{
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->base_prefix}attribute_templates`");
    delete_option("woo_attribute_templates_db_version");
}

function fetch_product_attributes()
{
    global $wpdb;
    $sql = "SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id` IN (SELECT ID FROM `wp_posts` WHERE `post_type` = 'product') AND meta_key ='_product_attributes';";
    return $wpdb->get_col($sql, 0);
}