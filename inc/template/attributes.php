<?php

$available_templates = db_select_attribute_template(['output' => 'ARRAY']);

if (isset($_POST['wag_create_attributes_submit'])) {
    if (!isset($_POST['attributes']) || empty($_POST['attributes'])) {
        show_message('<div class="error notice notice-error is-dismissible"><p>Не сте посочили атрибути за създаване</p></div>');
    } else {
        request_create_attributes($_POST['attributes']);
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
                            <?php foreach ($available_templates as $attribute): ?>
                                <div class="form-check">
                                    <label>
                                        <input type="checkbox"
                                               value="<?php echo $attribute['name']; ?>"
                                               name="attributes[]"
                                            <?php echo db_select_attribute_taxonomies(['name' => $attribute['name']]) ? 'disabled checked' : null; ?>
                                        >
                                        <?php echo $attribute['name']; ?>
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
