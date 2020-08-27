<?php


namespace WooCustomAttributes\Inc;


class Request
{
    private $api;
    private $db;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->api = new Api();
    }

    public function action_create_relation(int $taxonomy_id, string $meta_name)
    {
        $relation = Api::is_taxonomy_meta_related($taxonomy_id, $meta_name);
        if ($relation) {
            show_message('<div class="error notice notice-error is-dismissible"><p>' . $relation['attribute_label'] . ' и ' . $relation['meta_name'] . ' вече са релативни</p></div>');
            return;
        }
        $rows = $this->api->create_relation($taxonomy_id, $meta_name);
        if ($rows && $rows > 0)
            show_message('<div class="updated notice notice-success is-dismissible"><p>Успешно добавяне на ' . $meta_name . ' към релации</p></div>');

    }

    public function action_delete_relation(array $relation_ids)
    {
        $rows = $this->api->remove_relations($relation_ids);
        if ($rows && $rows > 0)
            show_message('<div class="updated notice notice-success is-dismissible"><p> Успешно изтриване на ' . $rows . ' релации</p></div>');
    }

    public function action_generate_terms($relation_ids)
    {
        wp_raise_memory_limit();
        foreach ($relation_ids as $relation_id) {
            $relation = $this->api->get_relation_by_id($relation_id);
            if(!$relation)
                return;
            $posts_metas = $this->api->get_postmeta_by_meta($relation['meta_name']);
            foreach ($posts_metas as $post_meta) {
                $this->api->handle_generate_terms($relation, $post_meta['post_id'], $post_meta['meta_value']);
            }
        }
    }

}