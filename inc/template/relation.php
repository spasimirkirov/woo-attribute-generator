<?php

use WooCustomAttributes\Inc\Database;

if (isset($_POST['submit_term_link'])) {
    if ($_POST['taxonomy_id'] !== '0' & $_POST['meta_name'] !== 'none') {
        request_create_relation($_POST['taxonomy_id'], $_POST['meta_name']);
    }
    if ($_POST['taxonomy_id'] === '0') {
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили таксономия</p></div>');
    }
    if ($_POST['meta_name'] === 'none') {
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили таксономия</p></div>');
    }
}

if (isset($_POST['submit_term_action'])) {
    if ($_POST['action'] === 'none') {
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили действие</p></div>');
    }
    if ($_POST['action'] === 'delete') {
        (!isset($_POST['relation_ids']) || empty($_POST['relation_ids'])) ?
            show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили релации за изтриване</p></div>') :
            request_delete_relation($_POST['relation_ids']);
    }
}

$db = new Database();
$available_taxonomies = wc_get_attribute_taxonomies();
$available_meta_attributes = get_distinct_meta_attributes();
$available_relations = $db->select_taxonomy_relations();
sort($available_taxonomies);
sort($available_meta_attributes);
?>

<div class="container-fluid my-2">

    <div class="row">
        <div class="col-12 card-header bg-dark text-light">
            Управление на взаймотношенията м/у продуктови атрибути и продуктови мета атрибути
        </div>
    </div>
    <div class="row row-cols-1 row-cols-md-2">
        <div class="card col-auto col-md-4">
            <div class="card-body">
                <form action="" method="post">
                    <label for="select_taxonomy">Таксономия (Атрибут)</label>
                    <div class="form-row mb-4">
                        <select id="select_taxonomy" name="taxonomy_id">
                            <option value="none">Избор</option>
                            <?php foreach ($available_taxonomies as $taxonomy): ?>
                                <option value="<?php echo $taxonomy->attribute_id; ?>">
                                    <?php echo $taxonomy->attribute_label ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <label for="select_meta">Мета атрибут</label>
                    <div class="form-row mb-4">
                        <select id="select_meta" name="meta_name">
                            <option value="0">Избор</option>
                            <?php foreach (array_filter($available_meta_attributes) as $attribute): ?>
                                <option value="<?php echo $attribute; ?>">
                                    <?php echo $attribute ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row mb-4">
                        <input type="submit" name="submit_term_link" class="btn btn-primary btn-sm" value="Свързване">
                    </div>
                </form>
            </div>
        </div>

        <div class="card col col-md-8">
            <div class="card-body">
                <form action="" method="post">
                    <div class="form-row mb-4">
                        <table class="table table-stripped table-hover table-bordered table-sm">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Атрибут</th>
                                <th scope="col">Мета атрибут</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($available_relations as $relation): ?>
                                <tr>
                                    <th scope="row">
                                        <label>
                                            <input type="checkbox" name="relation_ids[]"
                                                   value=" <?php echo $relation['id'] ?>">
                                        </label>
                                    </th>
                                    <td>
                                        <?php echo $relation['attribute_label'] ?>
                                    </td>
                                    <td>
                                        <?php echo $relation['meta_name'] ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <label for="select_action"> Изберете действие </label>
                    <div class="form-row mb-4">
                        <select id="select_action" name="action">
                            <option value="none">Избор</option>
                            <option value="delete">Изтриване</option>
                            <option value="generate">Генериране на Термини</option>
                        </select>
                        <input type="submit" name="submit_term_action" class="btn btn-primary btn-sm"
                               value="Изпълни">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>