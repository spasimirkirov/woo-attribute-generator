<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('wag_option_group');
                do_settings_sections('wag_settings');
                submit_button();
                ?>
            </form>
        </div>
    </div>
</div>