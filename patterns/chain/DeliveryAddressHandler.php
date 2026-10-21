<?php

namespace app\patterns\chain;

use yii\db\Query;

final class DeliveryAddressHandler extends OrderHandler
{
    protected function check(OrderContext $context): bool
    {
        $address = (new Query())
            ->select(['id', 'address'])
            ->from('{{%delivery_addresses}}')
            ->where(['user_id' => $context->userId])
            ->one();

        if (!$address) {
            return $this->fail($context, 'В базе нет адреса доставки для этого пользователя.');
        }

        $context->trace[] = 'Адрес доставки найден.';
        return true;
    }
}
