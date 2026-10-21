<?php

use yii\helpers\Html;

/** @var string[] $deliveryLog */
/** @var array|null $orderResult */
/** @var array $products */
/** @var string|null $address */
/** @var bool $isGuest */

$this->title = 'Поведенческие шаблоны';
?>
<div class="container" style="max-width: 1000px; margin: 30px auto;">
    <h1>Поведенческие шаблоны проектирования</h1>
    <p>Демонстрация паттернов «Наблюдатель» и «Цепочка обязанностей».</p>

    <hr>
    <section>
        <h2>1. Наблюдатель — рассылка новостей</h2>
        <p>Издатель новости уведомляет всех подписчиков из таблицы базы данных через общий интерфейс наблюдателя. В стандартной конфигурации Yii2 письма формируются в файловом режиме и сохраняются в <code>runtime/mail</code>; реальные письма внешним адресатам не отправляются.</p>

        <h3>Подписка на новости</h3>
        <?= Html::beginForm(['behavior/subscribe'], 'post', ['style' => 'margin-bottom: 20px;']) ?>
            <div class="form-group" style="margin-bottom: 10px;">
                <?= Html::label('Email подписчика', 'subscriber-email') ?>
                <?= Html::input('email', 'email', '', [
                    'id' => 'subscriber-email', 'class' => 'form-control', 'required' => true,
                    'placeholder' => 'name@example.com',
                ]) ?>
            </div>
            <?= Html::submitButton('Подписаться', ['class' => 'btn btn-secondary']) ?>
        <?= Html::endForm() ?>

        <h3>Публикация новости</h3>
        <?= Html::beginForm(['behavior/index'], 'post') ?>
            <input type="hidden" name="demoAction" value="publishNews">
            <div class="form-group" style="margin-bottom: 12px;">
                <?= Html::label('Заголовок новости', 'news-title') ?>
                <?= Html::textInput('title', 'Новая новость сайта', [
                    'id' => 'news-title', 'class' => 'form-control', 'required' => true,
                ]) ?>
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <?= Html::label('Текст новости', 'news-content') ?>
                <?= Html::textarea('content', 'На сайте опубликована новая новость.', [
                    'id' => 'news-content', 'class' => 'form-control', 'rows' => 3, 'required' => true,
                ]) ?>
            </div>
            <?= Html::submitButton('Опубликовать и уведомить подписчиков', ['class' => 'btn btn-primary']) ?>
        <?= Html::endForm() ?>

        <?php if ($deliveryLog !== []): ?>
            <h3>Результат рассылки</h3>
            <ul>
                <?php foreach ($deliveryLog as $line): ?>
                    <li><?= Html::encode($line) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <hr>
    <section>
        <h2>2. Цепочка обязанностей — оформление заказа</h2>
        <p>Проверки выполняются по очереди: наличие товара на складе → авторизация пользователя → наличие адреса доставки → создание заказа. При первой ошибке дальнейшая обработка прекращается.</p>

        <?php if ($isGuest): ?>
            <div class="alert alert-warning">Вы не авторизованы. Для успешного оформления заказа сначала войдите на сайт.</div>
        <?php else: ?>
            <p><strong>Адрес доставки:</strong> <?= $address ? Html::encode($address) : 'не сохранён' ?></p>
            <?= Html::beginForm(['behavior/save-address'], 'post', ['style' => 'margin-bottom: 20px;']) ?>
                <div class="form-group" style="margin-bottom: 10px;">
                    <?= Html::label('Адрес доставки', 'delivery-address') ?>
                    <?= Html::textInput('address', $address ?? '', [
                        'id' => 'delivery-address', 'class' => 'form-control',
                        'placeholder' => 'Город, улица, дом, квартира', 'required' => true,
                    ]) ?>
                </div>
                <?= Html::submitButton('Сохранить адрес', ['class' => 'btn btn-secondary']) ?>
            <?= Html::endForm() ?>
        <?php endif; ?>

        <?= Html::beginForm(['behavior/index'], 'post') ?>
            <input type="hidden" name="demoAction" value="placeOrder">
            <div class="form-group" style="margin-bottom: 12px;">
                <?= Html::label('Товар', 'product-id') ?>
                <select id="product-id" name="product_id" class="form-control" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= (int) $product['id'] ?>">
                            <?= Html::encode($product['name']) ?> — <?= number_format((float) $product['price'], 2, ',', ' ') ?> ₽; остаток: <?= (int) $product['stock'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <?= Html::label('Количество', 'product-quantity') ?>
                <?= Html::input('number', 'quantity', 1, [
                    'id' => 'product-quantity', 'class' => 'form-control', 'min' => 1, 'required' => true,
                ]) ?>
            </div>
            <?= Html::submitButton('Проверить и оформить заказ', ['class' => 'btn btn-success']) ?>
        <?= Html::endForm() ?>

        <?php if ($orderResult !== null): ?>
            <h3>Результат проверки</h3>
            <ol>
                <?php foreach ($orderResult['trace'] as $line): ?>
                    <li><?= Html::encode($line) ?></li>
                <?php endforeach; ?>
            </ol>
            <?php if ($orderResult['order'] !== null): ?>
                <div class="alert alert-success">
                    Заказ №<?= (int) $orderResult['order']['id'] ?> оформлен на сумму
                    <?= number_format((float) $orderResult['order']['total'], 2, ',', ' ') ?> ₽.
                </div>
            <?php elseif ($orderResult['error'] !== null): ?>
                <div class="alert alert-danger"><?= Html::encode($orderResult['error']) ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</div>
