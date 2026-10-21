<?php

namespace app\patterns\chain;

abstract class OrderHandler
{
    protected ?OrderHandler $next = null;

    public function setNext(OrderHandler $handler): OrderHandler
    {
        $this->next = $handler;
        return $handler;
    }

    public function handle(OrderContext $context): bool
    {
        if (!$this->check($context)) {
            return false;
        }

        if ($this->next !== null) {
            return $this->next->handle($context);
        }

        return true;
    }

    abstract protected function check(OrderContext $context): bool;

    protected function fail(OrderContext $context, string $message): bool
    {
        $context->error = $message;
        $context->trace[] = 'Ошибка: ' . $message;
        return false;
    }
}
