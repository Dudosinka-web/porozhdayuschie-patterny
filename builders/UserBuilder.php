<?php

namespace app\builders;

use app\models\User;

abstract class UserBuilder
{
    protected User $user;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->user = new User();
    }

    public function setId($id): static
    {
        $this->user->id = $id;
        return $this;
    }

    public function setName(string $name): static
    {
        $this->user->name = $name;
        return $this;
    }

    public function setEmail(string $email): static
    {
        $this->user->email = $email;
        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->user->password = $password;
        return $this;
    }

    public function setSource(string $source): static
    {
        $this->user->source = $source;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}