<?php

namespace app\posts;

use InvalidArgumentException;

class SocialPostFactory
{
    public static function create(string $socialNetwork, string $text): SocialPost
    {
        return match (strtolower($socialNetwork)) {
            'vk' => new VkPost($text),
            'telegram' => new TelegramPost($text),
            default => throw new InvalidArgumentException(
                "Неизвестная социальная сеть: {$socialNetwork}"
            ),
        };
    }
}