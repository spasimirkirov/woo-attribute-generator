<?php

function wag_activation_hook()
{
    flush_rewrite_rules();
    create_attributes_templates_table();
}

function wag_deactivation_hook()
{
    flush_rewrite_rules();
}

function wag_uninstallation_hook()
{
    flush_rewrite_rules();
    drop_attributes_templates_table();
}

function wag_admin_menu_option()
{
    add_menu_page('Woo Attributes Generator', 'Woo Attributes', 'manage_options', 'wag_attributes_menu');
    add_submenu_page('wag_attributes_menu', 'Woo Attributes', 'Woo Attributes', 'manage_options', 'wag_attributes_menu',
        'wag_attributes_page_callback');
    add_submenu_page('wag_attributes_menu', 'Woo Terms', 'Woo Terms', 'manage_options', 'wag_terms_menu',
        'wag_terms_page_callback');
}

function wag_attributes_page_callback(){
    echo "Hello World";
}
function wag_terms_page_callback(){
    echo "Hello World 2";
}

function create_attribute_template($meta_id, $post_id, $meta_key, $meta_value)
{
    \SolytronImporter\Services\HooksServiceProvider::create_attributes_templates_table();
    if ($meta_key === '_product_attributes') {
        var_dump($meta_key);
        var_dump($meta_value);
    }
}