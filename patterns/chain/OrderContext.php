<?php

namespace app\patterns\chain;

final class OrderContext
{
    public ?string $error = null;
    public array $trace = [];
    public ?array $order = null;

    /**
     * @param array<int, array{product_id:int, quantity:int}> $items
     */
    public function __construct(
        public readonly ?int $userId,
        public readonly bool $isAuthenticated,
        public readonly array $items
    ) {
    }
}
