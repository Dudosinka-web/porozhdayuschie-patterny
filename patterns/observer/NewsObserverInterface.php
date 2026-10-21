<?php

namespace app\patterns\observer;

interface NewsObserverInterface
{
    public function update(News $news): void;
}
