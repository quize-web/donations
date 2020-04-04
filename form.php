<?php
$shopId = '25634';
$scid = '33560';
$form_action = 'https://money.yandex.ru/eshop.xml';

$froms = [
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
        <? foreach ($froms as $form): ?>
            <li class="nav-item">
                <a href="#<?= $form->id ?>" class="nav-link <?= $form->active ? 'active' : '' ?>" data-toggle="tab" role="tab" aria-controls="lorem"><?= $form->title ?></a>
            </li>
        <? endforeach; ?>
    </ul>
    <div class="tab-content">
        <? foreach ($froms as $id => $form): ?>
            <div class="tab-pane fade show <?= $form->active ? 'active' : '' ?>" id="<?= $form->id ?>" role="tabpanel">
                <form action="<?= $form_action ?>" method="post" target="_blank">
                    <input name="shopId" value="<?= $shopId ?>" type="hidden" required/>
                    <input name="scid" value="<?= $scid ?>" type="hidden" required/>
                    <? if ($form->id === 'pojertvovat'): ?>
                    <label>
                        Сумма пожертвования
                        <input name="sum" class='customerSum' value="" min="1" type="number" required>
                    </label>
                    <? else:?>
                        <input name="sum" value="<?= $form->cost ?>" min="1" id='customerSum<?=$id?>' type="hidden" required>
                    <? endif;?>
                    <textarea name="orderDetails" id="orderDetails<?=$id?>" wrap="soft" style="display: none"></textarea>

                    <? if ($form->id !== 'pojertvovat'): ?>
                    <div class="order-info">
                        <div class="order-info-title"><?= $form->title ?></div>
                        <div class="order-info-body">
                            <div class="order-info-body-list-wrapp">
                                <ol>
                                    <? for ($i = 0; $i < $form->count; $i++): ?>
                                    <li><input class="name<?= $id ?>"></li>
                                    <? endfor; ?>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <script>
                        $('.name<?= $id ?>').on('change', function () {
                            var names = [];
                            var sum = 0;
                            $('.name<?= $id ?>').each(function() {
                                if ($(this).val()) {
                                    names.push($(this).val())
                                    sum += <?= $form->cost ?>
                                }
                            })
                            $('#orderDetails<?= $id ?>').val('<?= $form->title?>: ' + names.join(', '));
                            <? if ($form->multiplies): ?>
                            $('#customerSum<?=$id?>').val(sum);
                            <? endif; ?>
                        })
                    </script>

                    <? endif; ?>

                    <div style="margin-top: 10px">
                        <label>Как к вам обращаться?
                            <input required name="customerNumber" class="customerNumber customerNumber<?= $id ?>" value="" size="64"/>
                        </label>
                    </div>

                    <script>
                        $('.customerNumber<?= $id ?>').on('change', function () {
                            $('.btn-submit<?= $id ?>').prop('disabled', !$(this).val());
                        })
                    </script>

                    <div>
                        <input type="submit" value="Отправить" class="btn-submit btn-submit<?= $id ?>" disabled>
                    </div>


                </form>
            </div>
        <? endforeach; ?>
    </div>
</div>

