<?php

use yii\db\Migration;

/** Creates demo tables for the Observer and Chain of Responsibility lab. */
class m261021_100001_create_behavioral_pattern_tables extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%news_subscribers}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(190)->notNull()->unique(),
            'created_at' => $this->dateTime()->notNull(),
        ]);

        $this->createTable('{{%product_stock}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(120)->notNull(),
            'stock' => $this->integer()->notNull()->defaultValue(0),
            'price' => $this->decimal(10, 2)->notNull()->defaultValue(0),
        ]);

        $this->batchInsert('{{%product_stock}}', ['id', 'name', 'stock', 'price'], [
            [1, 'Ноутбук', 5, 45000.00],
            [2, 'Мышь', 20, 2500.00],
            [3, 'Клавиатура', 8, 5000.00],
        ]);

        $this->createTable('{{%delivery_addresses}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'address' => $this->string(255)->notNull(),
        ]);
        $this->createIndex('idx-delivery_addresses-user_id', '{{%delivery_addresses}}', 'user_id');

        $this->createTable('{{%shop_orders}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'items_json' => $this->text()->notNull(),
            'total' => $this->decimal(10, 2)->notNull(),
            'status' => $this->string(30)->notNull()->defaultValue('created'),
            'created_at' => $this->dateTime()->notNull(),
        ]);
        $this->createIndex('idx-shop_orders-user_id', '{{%shop_orders}}', 'user_id');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%shop_orders}}');
        $this->dropTable('{{%delivery_addresses}}');
        $this->dropTable('{{%product_stock}}');
        $this->dropTable('{{%news_subscribers}}');
    }
}
