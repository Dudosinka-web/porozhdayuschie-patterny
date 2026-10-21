<?php

namespace app\controllers;

use app\patterns\chain\AuthorizationHandler;
use app\patterns\chain\CreateOrderHandler;
use app\patterns\chain\DeliveryAddressHandler;
use app\patterns\chain\OrderContext;
use app\patterns\chain\StockCheckHandler;
use app\patterns\observer\EmailNewsSubscriber;
use app\patterns\observer\News;
use app\patterns\observer\NewsPublisher;
use Yii;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

final class BehaviorController extends Controller
{
    public function actionIndex(): string
    {
        $deliveryLog = [];
        $orderResult = null;

        if (Yii::$app->request->isPost) {
            $action = Yii::$app->request->post('demoAction');

            if ($action === 'publishNews') {
                $title = trim((string) Yii::$app->request->post('title', ''));
                $content = trim((string) Yii::$app->request->post('content', ''));

                if ($title === '' || $content === '') {
                    $deliveryLog[] = 'Укажите заголовок и текст новости.';
                } else {
                    $publisher = new NewsPublisher();
                    $subscriberEmails = (new Query())
                        ->select(['email'])
                        ->from('{{%news_subscribers}}')
                        ->column();

                    foreach ($subscriberEmails as $email) {
                        $publisher->attach(new EmailNewsSubscriber($email));
                    }

                    $publisher->publish(new News($title, $content, date('Y-m-d H:i:s')));
                    $deliveryLog = $publisher->getDeliveryLog();
                }
            }

            if ($action === 'placeOrder') {
                $productId = (int) Yii::$app->request->post('product_id', 0);
                $quantity = (int) Yii::$app->request->post('quantity', 1);
                $user = Yii::$app->user;
                $context = new OrderContext(
                    $user->isGuest ? null : (int) $user->id,
                    !$user->isGuest,
                    [['product_id' => $productId, 'quantity' => $quantity]]
                );

                $stockHandler = new StockCheckHandler();
                $stockHandler
                    ->setNext(new AuthorizationHandler())
                    ->setNext(new DeliveryAddressHandler())
                    ->setNext(new CreateOrderHandler());

                $stockHandler->handle($context);
                $orderResult = [
                    'order' => $context->order,
                    'error' => $context->error,
                    'trace' => $context->trace,
                ];
            }
        }

        $products = (new Query())
            ->select(['id', 'name', 'stock', 'price'])
            ->from('{{%product_stock}}')
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $address = null;
        if (!Yii::$app->user->isGuest) {
            $address = (new Query())
                ->select(['address'])
                ->from('{{%delivery_addresses}}')
                ->where(['user_id' => (int) Yii::$app->user->id])
                ->orderBy(['id' => SORT_DESC])
                ->scalar();
        }

        return $this->render('index', [
            'deliveryLog' => $deliveryLog,
            'orderResult' => $orderResult,
            'products' => $products,
            'address' => $address,
            'isGuest' => Yii::$app->user->isGuest,
        ]);
    }

    public function actionSubscribe(): Response
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Подписка оформляется только через POST-запрос.');
        }

        $email = trim((string) Yii::$app->request->post('email', ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Yii::$app->session->setFlash('error', 'Введите корректный адрес электронной почты.');
            return $this->redirect(['index']);
        }

        $exists = (new Query())
            ->from('{{%news_subscribers}}')
            ->where(['email' => $email])
            ->exists();

        if (!$exists) {
            Yii::$app->db->createCommand()->insert('{{%news_subscribers}}', [
                'email' => $email,
                'created_at' => date('Y-m-d H:i:s'),
            ])->execute();
            Yii::$app->session->setFlash('success', 'Подписка оформлена.');
        } else {
            Yii::$app->session->setFlash('info', 'Этот адрес уже подписан на новости.');
        }

        return $this->redirect(['index']);
    }

    public function actionSaveAddress(): Response
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Адрес можно сохранить только через POST-запрос.');
        }
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('error', 'Войдите на сайт, чтобы сохранить адрес доставки.');
            return $this->redirect(['index']);
        }

        $address = trim((string) Yii::$app->request->post('address', ''));
        if ($address === '') {
            Yii::$app->session->setFlash('error', 'Введите адрес доставки.');
            return $this->redirect(['index']);
        }

        $userId = (int) Yii::$app->user->id;
        $db = Yii::$app->db;
        $existingId = (new Query())
            ->select('id')
            ->from('{{%delivery_addresses}}')
            ->where(['user_id' => $userId])
            ->scalar();

        if ($existingId !== false && $existingId !== null) {
            $db->createCommand()->update(
                '{{%delivery_addresses}}',
                ['address' => $address],
                ['id' => (int) $existingId]
            )->execute();
        } else {
            $db->createCommand()->insert('{{%delivery_addresses}}', [
                'user_id' => $userId,
                'address' => $address,
            ])->execute();
        }

        Yii::$app->session->setFlash('success', 'Адрес доставки сохранён.');
        return $this->redirect(['index']);
    }
}
