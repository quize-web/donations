<?php
require_once ("./_common.php");

$shopId = '25634';
$scid = '33560';
//$form_action = 'https://money.yandex.ru/eshop.xml';
$form_action = '/handler.php';

$types = [
    (object)[
        'id' => 'ozdravii',
        'title' => 'О здравии',
        'count' => 10,
        'active' => true,
        'cost' => 180,
        'multiplies' => false,
    ],
    (object)[
        'id' => 'oypokoenii',
        'title' => 'О Упокоении',
        'count' => 10,
        'active' => false,
        'cost' => 180,
        'multiplies' => false,
    ],
    (object)[
        'id' => 'sorokoyst',
        'title' => 'Сорокоуст',
        'count' => 10,
        'active' => false,
        'cost' => 400,
        'multiplies' => true,
    ],
    (object)[
        'id' => 'pojertvovat',
        'title' => 'Пожертвовать',
        'count' => 0,
        'active' => false,
        'cost' => 0,
        'multiplies' => false,
    ]
];


?>
<link href="css/font.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/style.css">

<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery-3.4.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>


<div class="d-flex flex-row mt-2" style="max-width: 99%;">

    <ul class="nav nav-tabs nav-tabs--vertical nav-tabs--left" role="navigation">
        <?php foreach ($types as $form): ?>
            <li clas
                s="nav-item">
                <a href="#<?= $form->id ?>" class="nav-link <?= $form->active ? 'active' : '' ?>" data-toggle="tab" role="tab" aria-controls="lorem"><?= $form->title ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content">
        <?php foreach ($types as $id => $form): ?>
            <div class="tab-pane fade show <?= $form->active ? 'active' : '' ?>" id="<?= $form->id ?>" role="tabpanel">
                <form action="<?= $form_action ?>" method="post" target="_blank">

                    <input name="shopId" value="<?= $shopId ?>" type="hidden" required/>
                    <input name="scid" value="<?= $scid ?>" type="hidden" required/>

                    <? if ($form->id === 'pojertvovat'): ?>
                        <label>
                            Сумма пожертвования
                            <input name="sum" class="customerSum" value="" min="1" type="number" required>
                        </label>
                    <? else:?>
                        <input name="sum" value="<?= $form->cost ?>" min="1" id="customerSum<?= $id ?>" type="hidden" required>
                    <? endif;?>

                    <?php if ($form->id !== 'pojertvovat'): ?>

                        <div class="order-info">
                            <div class="order-info-title"><?= $form->title ?></div>
                            <div class="order-info-body">
                                <div class="order-info-body-list-wrapp">
                                    <ol>
                                        <?php for ($i = 0; $i < $form->count; $i++): ?>
                                            <li><input name="names[<?= $id ?>][]"></li>
                                        <?php endfor; ?>
                                    </ol>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>

                    <div style="margin-top: 10px">
                        <label>Как к вам обращаться?
                            <input required name="customer-name" class="customerName customerName<?= $id ?>" value="" size="64"/>
                        </label>
                    </div>

                    <script>
                        $('.customerName<?= $id ?>').on('input', function () {
                            var disabled = $(this).val().length === 0;
                            $('.btn-submit<?= $id ?>').prop('disabled', disabled);
                        })
                    </script>

                    <div>
                        <input type="submit" value="Отправить" class="btn-submit btn-submit<?= $id ?>" disabled>
                    </div>

                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

