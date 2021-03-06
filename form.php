<?php
require_once ("." . DIRECTORY_SEPARATOR . "_common.php");
use app\Services\Names;
?>

<!-- ASSETS -->

<link href="css/font.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/style.css">

<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery-3.4.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- ASSETS [END] -->

<div class="d-flex flex-row mt-2" style="max-width: 99%;">

    <!---->

    <ul class="nav nav-tabs nav-tabs--vertical nav-tabs--left" role="navigation">
        <?php foreach (Names::$TYPES as $slug => $typeData): ?>
            <li class="nav-item">
                <a href="#<?= $slug ?>" class="nav-link<?= (Names::ACTIVE_TAB === $slug) ? " active" : "" ?>" data-toggle="tab" role="tab" aria-controls="lorem"><?= $typeData["title"] ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!---->

    <div class="tab-content">
        <?php $n = 0; ?>
        <?php foreach (Names::$TYPES as $slug => $typeData): ?>
            <div class="tab-pane fade show<?= (Names::ACTIVE_TAB === $slug) ? " active" : "" ?>" id="<?= $slug?>" role="tabpanel">
                <form action="<?= env('HOST') . "/handler.php" ?>" method="post" target="_blank">

                    <? if ($slug === 'donation'): ?>
                        <label>
                            Сумма пожертвования
                            <input name="sum" class="customerSum" value="" min="1" type="number" required>
                        </label>
                    <? else:?>
                        <input name="sum" value="<?= $typeData["cost"] ?>" min="1" id="customerSum<?= $n ?>" type="hidden" required>
                    <? endif;?>

                    <!-- поля для имен -->
                    <?php if ($slug !== "donation"): ?>
                        <div class="order-info">
                            <div class="order-info-title"><?= $typeData["title"] ?></div>
                            <div class="order-info-body">
                                <div class="order-info-body-list-wrapp">
                                    <ol>
                                        <?php for ($i = 0; $i < $typeData["count"]; $i++): ?>
                                            <li>
                                                <label>
                                                    <input name="names[<?= $slug ?>][]"
                                                           data-cost="<?= $typeData["cost"] ?>"
                                                           type="text"<?= ($typeData["foreach"]) ? " data-foreach='{$slug}'" : "" ?>>
                                                </label>
                                            </li>
                                        <?php endfor; ?>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!-- поля для имен -->

                    <div style="margin-top: 10px">
                        <label>Как к вам обращаться?
                            <input required name="customer-name" class="customerName customerName<?= $n ?>" value="" size="64"/>
                        </label>
                    </div>

                    <script>
                        $('.customerName<?= $n ?>').on('input', function () {
                            var disabled = $(this).val().length === 0;
                            $('.btn-submit<?= $n ?>').prop('disabled', disabled);
                        });
                    </script>

                    <div>
                        <?php $value = (($slug === 'donation') ? "Пожертвовать" : "Оплатить {$typeData["cost"]}"); ?>
                        <input type="submit" value="<?= $value ?>" class="btn-submit btn-submit<?= $n ?>" disabled>
                    </div>

                </form>
            </div>
        <?php $n++; ?>
        <?php endforeach; ?>
    </div>
</div>

<script>
    $("[data-foreach]").on('change', function () {
        var slug = $(this).data('foreach');
        var cost = $(this).data('cost');
        var count = $("[data-foreach='" + slug + "']").filter(function () {
            return !!$(this).val();
        }).length;
        var total = ((count <= 1) ? cost : (cost * count));
        $(this).parents("form").eq(0).find("input[type='submit']").val("Оплатить " + total)
    });
</script>
