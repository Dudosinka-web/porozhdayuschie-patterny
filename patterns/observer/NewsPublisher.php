<?php

namespace app\patterns\observer;

use Throwable;

final class NewsPublisher
{
    /** @var NewsObserverInterface[] */
    private array $subscribers = [];

    /** @var string[] */
    private array $deliveryLog = [];

    public function attach(NewsObserverInterface $subscriber): void
    {
        if (!in_array($subscriber, $this->subscribers, true)) {
            $this->subscribers[] = $subscriber;
        }
    }

    public function detach(NewsObserverInterface $subscriber): void
    {
        $this->subscribers = array_values(array_filter(
            $this->subscribers,
            static fn (NewsObserverInterface $item): bool => $item !== $subscriber
        ));
    }

    public function publish(News $news): void
    {
        $this->deliveryLog = [];

        foreach ($this->subscribers as $subscriber) {
            try {
                $subscriber->update($news);
                $email = $subscriber instanceof EmailNewsSubscriber
                    ? $subscriber->getEmail()
                    : get_class($subscriber);
                $this->deliveryLog[] = 'Письмо сформировано для ' . $email;
            } catch (Throwable $exception) {
                $this->deliveryLog[] = 'Ошибка уведомления: ' . $exception->getMessage();
            }
        }

        if ($this->subscribers === []) {
            $this->deliveryLog[] = 'У новости нет подписчиков.';
        }
    }

    /** @return string[] */
    public function getDeliveryLog(): array
    {
        return $this->deliveryLog;
    }

    public function getSubscriberCount(): int
    {
        return count($this->subscribers);
    }
}
