<?php

use WooCustomAttributes\Inc\Database;

if (isset($_POST['submit_term_link'])) {
    $input_taxonomy = intval($_POST['taxonomy_id']);
    $input_meta = $_POST['meta_name'] === 'none' ? null : $_POST['meta_name'];

    if ($input_taxonomy && $input_meta)
        request_create_relation($input_taxonomy, $input_meta);

    if ($input_taxonomy === 0)
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили таксономия</p></div>');

    if (!$input_meta)
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили мета атрибут</p></div>');
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
        <div class="card col-12 col-md-4">
            <div class="card-body">
                <form action="" method="post">
                    <div class="row row-cols-1">
                        <div class="col-12">
                            <label for="select_taxonomy">Избор на таксономия на атрибут</label>
                            <div class="form-row">
                                <select id="select_taxonomy" name="taxonomy_id">
                                    <option value="none">Избор</option>
                                    <?php foreach ($available_taxonomies as $taxonomy): ?>
                                        <option value="<?php echo $taxonomy->attribute_id; ?>">
                                            <?php echo $taxonomy->attribute_label ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <p class="text-secondary"> Изберете таксономия за която да бъде закачен продуктовия атрибут
                                (мета атрибут) </p>
                            <p class="text-secondary"> За да създаване на още таксономии отидете на <a
                                        href="<?= admin_url('edit.php?post_type=product&page=product_attributes') ?>">Продукти->Атрибути</a>
                            </p>
                        </div>
                        <div class="col-12">
                            <label for="select_meta">Избор на мета атрибут</label>
                            <div class="form-row">
                                <select id="select_meta" name="meta_name[]" class="custom-select custom-select-sm"
                                        multiple="multiple" size="20">
                                    <option value="0">Избор</option>
                                    <?php foreach (array_filter($available_meta_attributes) as $attribute): ?>
                                        <option value="<?php echo $attribute; ?>">
                                            <?php echo $attribute ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <p class="text-secondary">За селектиране на няколко мета атрибута задръжте CTR повреме на
                                избора си. </p>
                        </div>
                        <div class="col-12">
                            <input type="submit" name="submit_term_link" class="btn btn-primary" value="Свързване">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card col-12 col-md-8">
            <div class="card-body">
                <form action="" method="post">
                    <?php foreach ($db->select_taxonomy_relation_labels() as $taxonomy): ?>
                        <h4><?php echo $taxonomy ?></h4>
                        <hr>
                        <div class="form-row mb-4">
                            <?php foreach ($db->select_taxonomy_relations(['attribute_label' => $taxonomy]) as $relation): ?>
                                <div class="form-check">
                                    <label>
                                        <input type="checkbox" name="relation_ids[]"
                                               value=" <?php echo $relation['id'] ?>">
                                        <?php echo $relation['meta_name'] ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($available_relations)): ?>
                        <h4>Не са намерени записи на релации</h4>
                    <?php endif; ?>
                    <?php if (!empty($db->select_taxonomy_relation_labels())): ?>
                        <label for="select_action"> Изберете действие </label>
                        <div class="form-row mb-4">
                            <select id="select_action" name="action">
                                <option value="none">Избор</option>
                                <option value="delete">Изтриване</option>
                                `
                                <option value="generate">Генериране на Термини</option>
                            </select>
                            <input type="submit" name="submit_term_action" class="btn btn-primary btn-sm"
                                   value="Изпълни">
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>