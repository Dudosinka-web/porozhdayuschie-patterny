<?php

namespace app\builders;

class FormUserBuilder extends UserBuilder
{
    public function buildFromForm(array $data): static
    {
        $this->reset();

        $this->setName($data['name'] ?? '')
            ->setEmail($data['email'] ?? '')
            ->setPassword($data['password'] ?? '')
            ->setSource('Форма');

        return $this;
    }
}