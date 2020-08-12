<?php

use WooCustomAttributes\Inc\Database;

$db = new Database();
$available_taxonomies = $db->select_attribute_taxonomy();

if (isset($_POST['wag_create_terms_submit'])) {
    if (!isset($_POST['attributes']) || empty($_POST['attributes'])) {
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили атрибути за чиито термини да бъдат генерирани</p></div>');
    } else {
        request_create_terms($_POST['attributes']);
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
                            <?php foreach ($available_taxonomies as $taxonomy): ?>
                                <div class="form-check">
                                    <label>
                                        <input type="checkbox"
                                               value="<?php echo $taxonomy['attribute_label']; ?>"
                                               name="attributes[]"
                                        >
                                        <?php echo $taxonomy['attribute_label']; ?>
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
