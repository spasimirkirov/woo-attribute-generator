<?php

use WooCustomAttributes\Inc\Database;

$db = new Database();
$available_taxonomies = $db->select_attribute_taxonomy();
$available_meta_attributes = get_distinct_meta_attributes();
sort($available_meta_attributes);

if (isset($_POST['wag_create_terms_submit'])) {
    if (!isset($_POST['attributes']) || empty($_POST['attributes'])) {
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили атрибути за чиито термини да бъдат генерирани</p></div>');
    } else {
        request_create_terms($_POST['taxonomy'], $_POST['attributes']);
        show_message('<div class="updated notice notice-success is-dismissible"><p>Успешно генериране на термини за атрибути ' . implode(', ', $_POST['attributes']) . '</p></div>');
    }
}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 card p-0">
            <div class="card-header bg-dark text-light">
                Създаване на продуктови атрибути от чиито термини да бъдат създадени
            </div>
            <div class="card-body">
                <div class="card-body">
                    <p class="card-text">
                    </p>
                    <h5 class="card-title">Изберете атрибути които да бъдат създадени</h5>
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
                            <input type="hidden" name="wag_create_terms_submit" value="true"/>
                            <button type="submit" class="btn btn-primary">Създаване</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
