<?php

use WooCustomAttributes\Inc\Database;

if (isset($_POST['submit_term_link'])) {
    $requestApi = new \WooCustomAttributes\Inc\Request();
    $input_taxonomy = intval($_POST['taxonomy_id']);
    $input_meta = $_POST['meta_name'] === 'none' ? null : $_POST['meta_name'];

    if ($input_taxonomy && $input_meta)
        $requestApi->action_create_relation($input_taxonomy, $input_meta);

    if ($input_taxonomy === 0)
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили таксономия</p></div>');

    if (!$input_meta)
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили мета атрибут</p></div>');
}

$db = new Database();
$available_taxonomies = wc_get_attribute_taxonomies();
$available_meta_attributes = WooCustomAttributes\Inc\Api::list_distinct_meta_attributes();
$available_relations = $db->select_taxonomy_relations();
sort($available_taxonomies);
sort($available_meta_attributes);
?>
<div class="container my-2">
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs p-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= admin_url('admin.php?page=woo_custom_attributes'); ?>">Релации</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active"
                       href="<?= admin_url('admin.php?page=woo_custom_attributes_create'); ?>">Добави</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= admin_url('admin.php?page=woo_custom_attributes_settings'); ?>">Настройки</a>
                </li>
            </ul>
            <div class="card-header bg-dark text-light">
                Създаване на релация меджу Атрибут и Мета атрибут
            </div>
            <div class="p-2">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="select_meta">Избор на мета атрибути</label>
                        <div class="form-row">
                            <select class="form-control" id="select_meta" multiple name="meta_name[]" size="20">
                                <option value="0">Избор</option>
                                <?php foreach (array_filter($available_meta_attributes) as $attribute): ?>
                                    <option value="<?php echo $attribute; ?>">
                                        <?php echo $attribute ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <p>Задръжте "CTR" бутона повреме на избора си за селектиране на няколко опции. </p>
                    </div>

                    <label for="select_taxonomy">Избор на таксономия на атрибут</label>
                    <p class="text-secondary">
                        Изберете с коя таксономия да бъдат свързани мета атрибутите.<br>
                        За създаване на таксономии, вижте
                        <a href="<?= admin_url('edit.php?post_type=product&page=product_attributes') ?>">
                            Продукти->Атрибути
                        </a>
                    </p>
                    <select class="custom-select" id="select_taxonomy" name="taxonomy_id">
                        <option value="none">Избор</option>
                        <?php foreach ($available_taxonomies as $taxonomy): ?>
                            <option value="<?php echo $taxonomy->attribute_id; ?>">
                                <?php echo $taxonomy->attribute_label ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <input class="btn btn-primary btn-sm ml-2" name="submit_term_link" type="submit"
                           value="Свързване">
                </form>
            </div>
        </div>
    </div>
</div>