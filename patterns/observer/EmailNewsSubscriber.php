<?php

namespace app\patterns\observer;

use RuntimeException;
use Yii;

final class EmailNewsSubscriber implements NewsObserverInterface
{
    public function __construct(private readonly string $email)
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function update(News $news): void
    {
        $sent = Yii::$app->mailer->compose()
            ->setTo($this->email)
            ->setFrom([
                Yii::$app->params['senderEmail'] ?? 'noreply@example.com' =>
                    Yii::$app->params['senderName'] ?? 'Новости сайта',
            ])
            ->setSubject($news->title)
            ->setTextBody($news->content)
            ->send();

        if (!$sent) {
            throw new RuntimeException('Не удалось сформировать письмо для ' . $this->email);
        }
    }
}
