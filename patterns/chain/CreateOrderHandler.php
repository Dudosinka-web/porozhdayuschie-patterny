<?php

namespace app\patterns\chain;

use Throwable;
use Yii;
use yii\db\Expression;
use yii\db\Query;

final class CreateOrderHandler extends OrderHandler
{
    protected function check(OrderContext $context): bool
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $total = 0.0;
            $orderItems = [];

            foreach ($context->items as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];
                $product = (new Query())
                    ->select(['id', 'name', 'stock', 'price'])
                    ->from('{{%product_stock}}')
                    ->where(['id' => $productId])
                    ->one($db);

                if (!$product || (int) $product['stock'] < $quantity) {
                    throw new \RuntimeException('Остаток товара изменился. Обновите корзину и повторите попытку.');
                }

                $changed = $db->createCommand()->update(
                    '{{%product_stock}}',
                    ['stock' => new Expression('[[stock]] - ' . $quantity)],
                    ['and', ['id' => $productId], ['>=', 'stock', $quantity]]
                )->execute();

                if ($changed !== 1) {
                    throw new \RuntimeException('Не удалось зарезервировать товар на складе.');
                }

                $lineTotal = (float) $product['price'] * $quantity;
                $total += $lineTotal;
                $orderItems[] = [
                    'product_id' => $productId,
                    'name' => $product['name'],
                    'quantity' => $quantity,
                    'price' => (float) $product['price'],
                    'line_total' => $lineTotal,
                ];
            }

            $db->createCommand()->insert('{{%shop_orders}}', [
                'user_id' => $context->userId,
                'items_json' => json_encode($orderItems, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'total' => number_format($total, 2, '.', ''),
                'status' => 'created',
                'created_at' => date('Y-m-d H:i:s'),
            ])->execute();

            $context->order = [
                'id' => (int) $db->getLastInsertID(),
                'items' => $orderItems,
                'total' => $total,
                'status' => 'created',
            ];
            $context->trace[] = 'Заказ успешно оформлен. Номер заказа: ' . $context->order['id'] . '.';
            $transaction->commit();
            return true;
        } catch (Throwable $exception) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }
            return $this->fail($context, 'Не удалось оформить заказ: ' . $exception->getMessage());
        }
    }
}
