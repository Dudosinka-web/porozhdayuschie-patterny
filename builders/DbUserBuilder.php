<?php

namespace app\builders;

class DbUserBuilder extends UserBuilder
{
    public function buildFromDatabase(array $data): static
    {
        $this->reset();

        $this->setId($data['id'] ?? null)
            ->setName($data['name'] ?? '')
            ->setEmail($data['email'] ?? '')
            ->setPassword($data['password'] ?? '')
            ->setSource('База данных');

        return $this;
    }
}