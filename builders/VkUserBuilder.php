<?php

namespace app\builders;

class VkUserBuilder extends UserBuilder
{
    public function buildFromVk(array $data): static
    {
        $this->reset();

        $this->setId($data['id'] ?? null)
            ->setName($data['name'] ?? '')
            ->setEmail($data['email'] ?? '')
            ->setSource('VK');

        return $this;
    }
}