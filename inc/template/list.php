<?php

use WooCustomAttributes\Inc\Api;
use WooCustomAttributes\Inc\Request;

if (isset($_POST['submit_term_action'])) {
    $requestApi = new Request();
    if ($_POST['action'] === 'none')
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили действие</p></div>');

    if ($_POST['action'] === 'delete') {
        (!isset($_POST['relation_ids']) || empty($_POST['relation_ids'])) ?
            show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили релации за изтриване</p></div>') :
            $requestApi->action_delete_relation($_POST['relation_ids']);
    }
    if ($_POST['action'] === 'generate_terms') {
        (!isset($_POST['relation_ids']) || empty($_POST['relation_ids'])) ?
            show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили релации за генериране</p></div>') :
            $requestApi->action_generate_terms($_POST['relation_ids']);
    }
    if ($_POST['action'] === 'create') {
        $input_taxonomy = intval($_POST['taxonomy_id']);
        $input_meta = $_POST['meta_name'] === 'none' ? null : $_POST['meta_name'];
        if ($input_taxonomy && $input_meta)
            $requestApi->action_create_relation($input_taxonomy, $input_meta);
        if ($input_taxonomy === 0)
            show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили таксономия</p></div>');
        if (!$input_meta)
            show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили мета атрибут</p></div>');
    }
}

$api = new Api();
$available_taxonomies = wc_get_attribute_taxonomies();
$available_meta_attributes = WooProductAttributes\Inc\Api::list_distinct_relation_metas();
$available_relations = $api->get_relations();
$available_relation_labels = $api->get_relation_taxonomy_labels();
sort($available_taxonomies);
sort($available_meta_attributes);
?>
<div class="container my-2">
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs p-0">
                <li class="nav-item">
                    <a class="nav-link active"
                       href="<?= admin_url('admin.php?page=woo_custom_attributes'); ?>">Релации</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= admin_url('admin.php?page=woo_custom_attributes_settings'); ?>">Настройки</a>
                </li>
            </ul>
            <div class="card-header bg-dark text-light">
                Управление на взаимоотношенията м/у продуктови атрибути и продуктови мета атрибути
            </div>
        </div>
        <div class="col-12">
            <form action="" method="post">
                <div class="form-group">
                    <table class="table table-hover table-bordered">
                        <tr>
                            <th>
                                <label>
                                    <input id="checkbox_select_all" type="checkbox">
                                    Атрибути
                                </label>
                            </th>
                            <th>Мета атрибути</th>
                        </tr>
                        <?php foreach ($api->get_relation_taxonomy_labels() as $i => $taxonomy_label): ?>
                            <tr>
                                <td>
                                    <label for="taxonomy_<?= $i ?>">
                                        <input id="taxonomy_<?= $i ?>" class="checkbox-taxonomy" data-target="<?= $i ?>"
                                               type="checkbox">
                                        <?= $taxonomy_label ?>
                                    </label>
                                </td>
                                <td>
                                    <?php foreach ($api->get_relation_by_label($taxonomy_label) as $relation): ?>
                                        <div class="form-row form-check">
                                            <label>
                                                <input class="checkbox-meta-<?= $i ?>" name="relation_ids[]"
                                                       type="checkbox" value=" <?php echo $relation['id'] ?>">
                                                <?php echo $relation['meta_name'] ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($available_relations)): ?>
                            <tr>
                                <td colspan="3">Не са намерени записи на релации</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="form-row">
                    <div class="form-group col-auto">
                        <label for="select-relation-category">Избор на таксономия на атрибут</label>
                        <select class="form-control" id="select-relation-category" name="taxonomy_id">
                            <option value="none">Избор</option>
                            <?php foreach ($available_taxonomies as $taxonomy): ?>
                                <option value="<?php echo $taxonomy->attribute_id; ?>">
                                    <?php echo $taxonomy->attribute_label ?> </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-secondary">
                            Изберете с коя таксономия да бъдат свързани мета атрибутите.<br>
                            За създаване на таксономии, вижте
                            <a href="<?= admin_url('edit.php?post_type=product&page=product_attributes') ?>">
                                Продукти->Атрибути
                            </a>
                        </p>
                    </div>
                    <div class="form-group col-auto">
                        <label for="select_meta">Избор на мета атрибути</label>
                        <select class="form-control" id="select_meta" name="meta_name">
                            <option value="0">Избор</option>
                            <?php foreach ($available_meta_attributes as $attribute): ?>
                                <option value="<?= $attribute ?>"><?= $attribute ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p>Задръжте "CTR" бутона повреме на избора си за селектиране на няколко опции. </p>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="select_action">Изберете действие</label><br>
                        <select class="form-control custom-select" id="select_action" name="action">
                            <option value="none">Избор</option>
                            <option value="create">Добавяне</option>
                            <option value="delete">Изтриване</option>
                            <option value="generate_terms">Генериране на Термини</option>
                        </select>
                        <input class="btn btn-primary" name="submit_term_action" type="submit" value="Изпълни">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>