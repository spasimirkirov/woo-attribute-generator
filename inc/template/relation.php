<?php

use WooCustomAttributes\Inc\Database;

$db = new Database();
$available_taxonomies = $db->select_attribute_taxonomy();
$available_meta_attributes = get_distinct_meta_attributes();
$available_relations = $db->select_taxonomy_relations();
sort($available_taxonomies);
sort($available_meta_attributes);

if (isset($_POST['wag_create_terms_submit'])) {
    if (!isset($_POST['attributes']) || empty($_POST['attributes'])) {
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили атрибути за чиито термини да бъдат генерирани</p></div>');
    } else {
        request_link_attributes($_POST['taxonomy'], $_POST['attributes']);
        show_message('<div class="updated notice notice-success is-dismissible"><p>Успешно генериране на термини за атрибути ' . implode(', ', $_POST['attributes']) . '</p></div>');
    }
}

?>
<div class="container-fluid row col-12 card mt-3">
    <div class="card-header bg-dark text-light">
        Управление на взаймотношенията м/у продуктови атрибути и продуктови мета атрибути
    </div>
    <div class="card-body">
        <form action="" method="post">
            <div class="row">
                <div class="col-12">
                    <label>
                        <select name="action">
                            <option value="delete">Изтриване</option>
                            <option value="generate">Генериране на Термини</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Потвърди</button>
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <table class="table table-stripped table-hover table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Атрибут</th>
                            <th>Мета атрибут</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($available_relations as $relation): ?>
                            <tr>
                                <td class="col-md-1">
                                    <div class="form-check">
                                        <label>
                                            <input type="checkbox" name="relations[]"
                                                   value=" <?php echo $relation['id'] ?>">
                                            <?php echo $relation['id'] ?>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <?php echo $relation['taxonomy'] ?>
                                </td>
                                <td>
                                    <?php echo $relation['meta_name'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <label>
                <select name="action">
                    <option value="delete">Изтриване</option>
                    <option value="generate">Генериране на Термини</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Потвърди</button>
            </label>
        </form>
    </div>

    <div class="card-body">
        <details>
            <form action="" method="post">
                <div class="form-row mb-4">
                    <label>
                        <select name="taxonomy">
                            <?php foreach ($available_taxonomies as $taxonomy): ?>
                                <option value="<?php echo $taxonomy['attribute_label']; ?>">
                                    <?php echo $taxonomy['attribute_label'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
                <div class="form-row mb-4">
                    <?php foreach ($available_meta_attributes as $attribute): ?>
                        <div class="form-check">
                            <label>
                                <input type="checkbox"
                                       value="<?php echo $attribute; ?>"
                                       name="attributes[]"
                                >
                                <?php echo $attribute; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary">Свържи</button>
                </div>
            </form>
        </details>
    </div>
</div>