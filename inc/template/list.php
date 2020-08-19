<?php

use WooCustomAttributes\Inc\Database;

if (isset($_POST['submit_term_action'])) {
    $requestApi = new \WooCustomAttributes\Inc\Request();
    if ($_POST['action'] === 'none')
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили действие</p></div>');

    if ($_POST['action'] === 'delete') {
        (!isset($_POST['relation_ids']) || empty($_POST['relation_ids'])) ?
            show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили релации за изтриване</p></div>') :
            $requestApi->delete_relation($_POST['relation_ids']);
    }
    if ($_POST['action'] === 'generate_terms') {
        (!isset($_POST['relation_ids']) || empty($_POST['relation_ids'])) ?
            show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили релации за генериране</p></div>') :
            $requestApi->generate_terms($_POST['relation_ids']);
    }
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
                    <a class="nav-link active"
                       href="<?= admin_url('admin.php?page=woo_custom_attributes'); ?>">Релации</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       href="<?= admin_url('admin.php?page=woo_custom_attributes_create'); ?>">Добави</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= admin_url('admin.php?page=woo_custom_attributes_settings'); ?>">Настройки</a>
                </li>
            </ul>
            <div class="card-header bg-dark text-light">
                Управление на взаимоотношенията м/у продуктови атрибути и продуктови мета атрибути
            </div>
            <div class="p-2">
                <form action="" method="post">
                    <table class="table table-hover table-sm">
                        <tr>
                            <th>
                                <label>
                                    <input id="checkbox_select_all" type="checkbox">
                                    Атрибути
                                </label>
                            </th>
                            <th>Мета атрибути</th>
                        </tr>
                        <?php foreach ($db->select_taxonomy_relation_labels() as $i => $taxonomy): ?>
                            <tr>
                                <td>
                                    <label for="taxonomy_<?= $i ?>">
                                        <input id="taxonomy_<?= $i ?>" class="checkbox-taxonomy" data-target="<?= $i ?>"
                                               type="checkbox">
                                        <?= $taxonomy ?>
                                    </label>
                                </td>
                                <td>
                                    <?php foreach ($db->select_taxonomy_relations(['attribute_label' => $taxonomy]) as $relation): ?>
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
                    <?php if (!empty($db->select_taxonomy_relation_labels())): ?>
                        <div class="form-group">
                            <label for="select_action">Изберете действие</label>
                            <div class="form-row mb-4">
                                <select class="custom-select form-control mr-1" id="select_action" name="action">
                                    <option value="none">Избор</option>
                                    <option value="delete">Изтриване</option>
                                    <option value="generate_terms">Генериране на Термини</option>
                                </select>
                                <input class="btn btn-primary btn-sm" name="submit_term_action" type="submit"
                                       value="Изпълни">
                            </div>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>