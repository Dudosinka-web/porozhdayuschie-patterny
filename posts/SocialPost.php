<?php

namespace app\posts;

interface SocialPost
{
    public function publish(): string;
}