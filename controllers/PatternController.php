<?php

namespace app\controllers;

use app\builders\DbUserBuilder;
use app\builders\FormUserBuilder;
use app\builders\VkUserBuilder;
use app\posts\SocialPostFactory;
use yii\web\Controller;

class PatternController extends Controller
{
    public function actionIndex()
    {
        // ПОЛЬЗОВАТЕЛЬ ИЗ БД
        $dbUser = (new DbUserBuilder())
            ->buildFromDatabase([
                'id' => 1,
                'name' => 'Иван Петров',
                'email' => 'ivan@example.com',
                'password' => '123456'
            ])
            ->getUser();

        // ПОЛЬЗОВАТЕЛЬ ИЗ ФОРМЫ
        $formUser = (new FormUserBuilder())
            ->buildFromForm([
                'name' => 'Анна Сидорова',
                'email' => 'anna@example.com',
                'password' => 'qwerty'
            ])
            ->getUser();

        // ПОЛЬЗОВАТЕЛЬ ИЗ VK
        $vkUser = (new VkUserBuilder())
            ->buildFromVk([
                'id' => 12345,
                'name' => 'Алексей Иванов',
                'email' => 'alex@example.com'
            ])
            ->getUser();

        // ПОСТ ДЛЯ VK
        $vkPost = SocialPostFactory::create(
            'vk',
            'Новая публикация в социальной сети!'
        );

        // ПОСТ ДЛЯ TELEGRAM
        $telegramPost = SocialPostFactory::create(
            'telegram',
            'Новое сообщение в Telegram!'
        );

        return $this->render('index', [
            'dbUser' => $dbUser,
            'formUser' => $formUser,
            'vkUser' => $vkUser,
            'vkPost' => $vkPost,
            'telegramPost' => $telegramPost,
        ]);
    }
}