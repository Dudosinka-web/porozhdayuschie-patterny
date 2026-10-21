<?php

namespace app\patterns\chain;

use yii\db\Query;

final class StockCheckHandler extends OrderHandler
{
    protected function check(OrderContext $context): bool
    {
        if ($context->items === []) {
            return $this->fail($context, 'Корзина пуста.');
        }

        foreach ($context->items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($productId < 1 || $quantity < 1) {
                return $this->fail($context, 'Некорректные данные товара.');
            }

            $product = (new Query())
                ->select(['id', 'name', 'stock'])
                ->from('{{%product_stock}}')
                ->where(['id' => $productId])
                ->one();

            if (!$product) {
                return $this->fail($context, 'Товар с ID ' . $productId . ' не найден на складе.');
            }

            if ((int) $product['stock'] < $quantity) {
                return $this->fail(
                    $context,
                    'Недостаточно товара «' . $product['name'] . '» на складе. Остаток: ' . $product['stock'] . '.'
                );
            }
        }

        $context->trace[] = 'Проверка склада пройдена.';
        return true;
    }
}
