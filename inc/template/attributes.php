<?php

use WooCustomAttributes\Inc\Database;

$db = new Database();
$available_meta_attributes = get_distinct_meta_attributes();

if (isset($_POST['wag_create_attributes_submit'])) {
    if (!isset($_POST['attributes']) || empty($_POST['attributes'])) {
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили атрибути за създаване</p></div>');
    } else {
        request_create_attribute_taxonomies($_POST['attributes']);
        show_message('<div class="updated notice notice-success is-dismissible"><p>Успешно създаване на атрибути ' . implode(', ', $_POST['attributes']) . '</p></div>');
    }
}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 card p-0">
            <div class="card-header bg-dark text-light">
                Създаване на продуктови атрибути от продуктите
            </div>
            <div class="card-body">
                <div class="card-body">
                    <p class="card-text">
                    </p>
                    <h5 class="card-title">Изберете атрибути които да бъдат създадени</h5>
                    <form action="" method="post">
                        <div class="form-row mb-4">
                            <?php foreach ($available_meta_attributes as $meta_attribute): ?>
                                <div class="form-check">
                                    <label>
                                        <input type="checkbox"
                                               value="<?php echo $meta_attribute; ?>"
                                               name="attributes[]"
                                            <?php echo $db->select_attribute_taxonomy(['name' => $meta_attribute]) ? 'disabled checked' : null; ?>
                                        >
                                        <?php echo $meta_attribute; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-row">
                            <input type="hidden" name="wag_create_attributes_submit" value="true"/>
                            <button type="submit" class="btn btn-primary">Създаване</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
