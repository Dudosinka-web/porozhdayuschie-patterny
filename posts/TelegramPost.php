<?php

namespace app\posts;

class TelegramPost implements SocialPost
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function publish(): string
    {
        return '[Telegram] ' . $this->text;
    }
}