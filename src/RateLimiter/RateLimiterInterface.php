<?php

namespace Lamoda\Tactician\RateLimit\RateLimiter;

interface RateLimiterInterface
{
    public function throttle(RateLimit $rateLimit): void;
}
