<?php

declare(strict_types=1);

namespace Lamoda\Tactician\RateLimit\RateLimiter;

final class FirstMatchedRateLimitProvider implements RateLimitProviderInterface
{
    /**
     * @var RateLimitProviderInterface[]
     */
    private $rateLimitProviders;

    public function __construct(RateLimitProviderInterface ...$rateLimitProviders)
    {
        $this->rateLimitProviders = $rateLimitProviders;
    }

    public function provide($command): ?RateLimit
    {
        $result = null;

        foreach ($this->rateLimitProviders as $rateLimitProvider) {
            $result = $rateLimitProvider->provide($command);
            if (null !== $result) {
                break;
            }
        }

        return $result;
    }
}
