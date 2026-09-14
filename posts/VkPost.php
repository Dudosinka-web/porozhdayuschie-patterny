<?php

namespace app\posts;

class VkPost implements SocialPost
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function publish(): string
    {
        return '[VK] ' . $this->text;
    }
}