<?php

namespace WooCustomAttributes\Inc;

class Hooks
{
    private $api;

    /**
     * Hooks constructor.
     */
    public function __construct()
    {
        $this->api = new Api();
    }

    public static function activate()
    {
        flush_rewrite_rules();
        $db = new Database();
        $db->create_taxonomy_relations_table();
        update_option("woo_custom_attributes_auto_generate", false);
    }

    public static function deactivate()
    {
        flush_rewrite_rules();
    }

    public static function uninstall()
    {
        flush_rewrite_rules();
        $db = new Database();
        $db->drop_taxonomy_relations_table();
        delete_option("woo_custom_attributes_auto_generate");
    }

    public function create_term_on_meta_update($meta_id, $post_id, $meta_key, $meta_value)
    {
        if ($meta_key !== '_product_attributes')
            return;
        foreach ($this->api->get_relations() as $relation)
            $this->api->handle_generate_terms($relation, $post_id, $meta_value);
    }
}