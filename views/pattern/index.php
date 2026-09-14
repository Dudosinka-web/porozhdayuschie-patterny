<?php

use yii\helpers\Html;

/** @var app\models\User $dbUser */
/** @var app\models\User $formUser */
/** @var app\models\User $vkUser */
/** @var app\posts\SocialPost $vkPost */
/** @var app\posts\SocialPost $telegramPost */

$this->title = 'Порождающие паттерны';
?>

<div class="site-patterns">

    <h1><?= Html::encode($this->title) ?></h1>

    <hr>

    <h2>Паттерн «Строитель»</h2>

    <h3>1. Пользователь из базы данных</h3>
    <p><?= Html::encode($dbUser->getDescription()) ?></p>

    <h3>2. Пользователь из формы</h3>
    <p><?= Html::encode($formUser->getDescription()) ?></p>

    <h3>3. Пользователь после авторизации через VK</h3>
    <p><?= Html::encode($vkUser->getDescription()) ?></p>

    <hr>

    <h2>Паттерн «Фабрика»</h2>

    <h3>Пост для VK</h3>
    <p><?= Html::encode($vkPost->publish()) ?></p>

    <h3>Пост для Telegram</h3>
    <p><?= Html::encode($telegramPost->publish()) ?></p>

</div>