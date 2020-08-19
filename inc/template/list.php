<?php

use WooCustomAttributes\Inc\Database;

if (isset($_POST['submit_term_link'])) {
    $requestApi = new \WooCustomAttributes\Inc\Request();
    $input_taxonomy = intval($_POST['taxonomy_id']);
    $input_meta = $_POST['meta_name'] === 'none' ? null : $_POST['meta_name'];

    if ($input_taxonomy && $input_meta)
        $requestApi->create_relation($input_taxonomy, $input_meta);

    if ($input_taxonomy === 0)
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили таксономия</p></div>');

    if (!$input_meta)
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили мета атрибут</p></div>');
}

if (isset($_POST['submit_term_action'])) {
    $requestApi = new \WooCustomAttributes\Inc\Request();
    if ($_POST['action'] === 'none') {
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили действие</p></div>');
    }
    if ($_POST['action'] === 'delete') {
        (!isset($_POST['relation_ids']) || empty($_POST['relation_ids'])) ?
            show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили релации за изтриване</p></div>') :
            $requestApi->delete_relation($_POST['relation_ids']);
    }
}

$db = new Database();
$available_taxonomies = wc_get_attribute_taxonomies();
$available_meta_attributes = WooCustomAttributes\Inc\Api::list_distinct_meta_attributes();
$available_relations = $db->select_taxonomy_relations();
sort($available_taxonomies);
sort($available_meta_attributes);
?>
<div class="container-fluid my-2">
    <div class="row">
        <div class="col-12">
            <nav>
                <ul class="nav nav-tabs">
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
            </nav>
        </div>
        <div class="col-12 card-header bg-dark text-light">
            Управление на взаимоотношенията м/у продуктови атрибути и продуктови мета атрибути
        </div>
        <div class="col border rounded">
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
                    <label for="select_action"> Изберете действие </label>
                    <div class="form-row mb-4">
                        <select id="select_action" name="action">
                            <option value="none">Избор</option>
                            <option value="delete">Изтриване</option>
                            <option value="generate">Генериране на Термини</option>
                        </select>
                        <input class="btn btn-primary btn-sm" name="submit_term_action" type="submit" value="Изпълни">
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>