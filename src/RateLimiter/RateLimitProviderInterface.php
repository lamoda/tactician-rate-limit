<?php

declare(strict_types=1);

namespace Lamoda\Tactician\RateLimit\RateLimiter;

interface RateLimitProviderInterface
{
    public function provide($command): ?RateLimit;
}
