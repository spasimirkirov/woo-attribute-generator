<div class="container">
    <div class="row">
        <div class="col-12 card p-0">
            <div class="card-header bg-dark text-light">
                Импорт на продуктови атрибути от категории
            </div>
            <div class="card-body">
                <?php if ($categories = $attributesTemplateRepo->getCategories()): ?>
                    <div class="card-body">
                        <p class="card-text">Тук можете да видите списък със синхронизираните от Солитрон продуктови
                            категории и да изберете на кои от тях, продуктовите атрибути да бъдат синхронизирани.
                        </p>
                        <h5 class="card-title">Изберете категориите, чиито продуктови атрибути да бъдат импортирани</h5>
                        <form action="" method="post">
                            <?php foreach ($categories as $categoryDTO): ?>
                                <hr>
                                <h4><?php echo $categoryDTO->name; ?></h4>
                                <div class="form-row mb-4">
                                    <?php foreach ($categoryDTO->attributes() as $attribute): ?>
                                        <div class="form-check">
                                            <label>
                                                <input type="checkbox"
                                                       value="<?php echo $attribute['id']; ?>"
                                                       name="attributes[]"
                                                    <?php echo $attribute['active'] ? 'checked' : null; ?>
                                                >
                                                <?php echo $attribute['name']; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                            <div class="form-row">
                                <input type="hidden" name="solytron_import_submit" value="true"/>
                                <button type="submit" class="btn btn-primary">Обнови</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
