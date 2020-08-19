<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <nav>
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= admin_url('admin.php?page=woo_custom_attributes'); ?>">Релации</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="<?= admin_url('admin.php?page=woo_custom_attributes_create'); ?>">Добави</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= admin_url('admin.php?page=woo_custom_attributes_settings'); ?>">Настройки</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="col-12">
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
                <div class="form-row">
                    <?php
                    settings_fields('woo_custom_attributes_option_group');
                    do_settings_sections('woo_custom_attributes_settings');
                    submit_button();
                    ?>
                </div>
            </form>
        </div>
    </div>
</div>