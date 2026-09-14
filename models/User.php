<?php

namespace app\models;

use yii\base\Model;

class User extends Model
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $source;

    public function getDescription(): string
    {
        return "Имя: {$this->name}, Email: {$this->email}, Источник: {$this->source}";
    }
}