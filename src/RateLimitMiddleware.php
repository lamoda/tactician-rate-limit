<?php

declare(strict_types=1);

namespace Lamoda\Tactician\RateLimit;

use Lamoda\Tactician\RateLimit\RateLimiter\RateLimitProviderInterface;
use Lamoda\Tactician\RateLimit\RateLimiter\RateLimiterInterface;
use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class RateLimitMiddleware implements Middleware
{
    /**
     * @var RateLimitProviderInterface
     */
    private $rateLimitProvider;
    /**
     * @var RateLimiterInterface
     */
    private $rateLimiter;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        RateLimitProviderInterface $rateLimitProvider,
        RateLimiterInterface $rateLimiter,
        LoggerInterface $logger = null
    ) {
        $this->rateLimitProvider = $rateLimitProvider;
        $this->rateLimiter = $rateLimiter;
        $this->logger = $logger ?: new NullLogger();
    }

    public function execute($command, callable $next)
    {
        $rateLimit = $this->rateLimitProvider->provide($command);

        if (null !== $rateLimit) {
            $this->logger->info('Throttling command with key {key} for {limit} on {time} ms', [
                'key' => $rateLimit->getKey(),
                'limit' => $rateLimit->getLimit(),
                'time' => $rateLimit->getMilliseconds(),
            ]);
            $this->rateLimiter->throttle($rateLimit);
        }

        return $next($command);
    }
}
