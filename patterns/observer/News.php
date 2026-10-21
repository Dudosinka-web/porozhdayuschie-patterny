<?php

namespace app\patterns\observer;

final class News
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
        public readonly string $publishedAt = ''
    ) {
    }
}
