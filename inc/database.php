<?php

function db_create_attribute_templates_table()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}wag_attribute_templates` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
        `active` TINYINT(1) NOT NULL DEFAULT '0',
        PRIMARY KEY  (id),
	    UNIQUE INDEX `UNIQUE KEY` (`name`)
        ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    update_option("woo_attribute_templates_db_version", 1.0);
    update_option("wag_auto_generate", false);
}

function db_drop_wag_attribute_templates_table()
{
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->base_prefix}wag_attribute_templates`");
    delete_option("woo_attribute_templates_db_version");
    delete_option("wag_auto_generate");
}

function db_select_product_attributes()
{
    global $wpdb;
    $sql = "SELECT `post_id`,`meta_value` FROM `wp_postmeta` WHERE `post_id` IN (SELECT ID FROM `wp_posts` WHERE `post_type` = 'product') AND meta_key ='_product_attributes';";
    return $wpdb->get_results($sql, 'ARRAY_A');
}


function db_select_attribute_template(array $params = [])
{
    global $wpdb;
    if (!isset($params['output']))
        $params['output'] = 'ARRAY';

    $query = "SELECT * FROM `{$wpdb->base_prefix}wag_attribute_templates`";
    $query .= isset($params['id']) ?
        $wpdb->prepare(" WHERE `id` = '%d'", $params['id']) :
        $wpdb->prepare(" WHERE `id` > '0'");

    if (isset($params['name']))
        $query .= $wpdb->prepare(" AND `name` = '%s'", $params['name']);

    if (isset($params['active']))
        $query .= $wpdb->prepare(" AND `active` = '%d'", $params['active']);

    return $params['output'] === 'ARRAY' ?
        $wpdb->get_results($query, 'ARRAY_A') :
        $wpdb->get_row($query, 'ARRAY_A');
}

function db_insert_attribute_template($name)
{
    global $wpdb;
    $sql = $wpdb->prepare("INSERT INTO `{$wpdb->base_prefix}wag_attribute_templates` (`name`, `active`) VALUES ('%s', 0);", $name);
    $wpdb->query($sql);
}

function db_select_attribute_taxonomies(array $params = [])
{
    global $wpdb;
    $query = "SELECT * FROM `{$wpdb->base_prefix}woocommerce_attribute_taxonomies` WHERE `attribute_id` > 0";
    if (isset($params['name']))
        $query .= $wpdb->prepare(" AND `attribute_label` = '%s'", $params['name']);
    return $wpdb->get_results($query, 'ARRAY_A');
}

function db_insert_attribute_taxonomy($attribute_name)
{
    return wc_create_attribute([
        'name' => $attribute_name,
        'type' => 'select',
        'order_by' => 'menu_order',
        'has_archives' => false,
    ]);
}