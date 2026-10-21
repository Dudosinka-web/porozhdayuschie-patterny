<?php

namespace app\patterns\chain;

final class AuthorizationHandler extends OrderHandler
{
    protected function check(OrderContext $context): bool
    {
        if (!$context->isAuthenticated || $context->userId === null) {
            return $this->fail($context, 'Для оформления заказа необходимо войти на сайт.');
        }

        $context->trace[] = 'Пользователь авторизован.';
        return true;
    }
}
