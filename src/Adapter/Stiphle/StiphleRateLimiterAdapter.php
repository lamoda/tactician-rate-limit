<?php

declare(strict_types=1);

namespace Lamoda\Tactician\RateLimit\Adapter\Stiphle;

use Lamoda\Tactician\RateLimit\RateLimiter\RateLimit;
use Lamoda\Tactician\RateLimit\RateLimiter\RateLimiterInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Stiphle\Throttle\ThrottleInterface;

final class StiphleRateLimiterAdapter implements RateLimiterInterface
{
    /**
     * @var ThrottleInterface
     */
    private $throttle;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ThrottleInterface $throttle, LoggerInterface $logger = null)
    {
        $this->throttle = $throttle;
        $this->logger = $logger ?: new NullLogger();
    }

    public function throttle(RateLimit $rateLimit): void
    {
        $estimation = $this->throttle->getEstimate(
            $rateLimit->getKey(),
            $rateLimit->getLimit(),
            $rateLimit->getMilliseconds()
        );

        $this->throttle->throttle(
            $rateLimit->getKey(),
            $rateLimit->getLimit(),
            $rateLimit->getMilliseconds()
        );

        if ($estimation > 0) {
            $this->logger->info('Rate limiter throttled execution for {time} ms', [
                'time' => $estimation,
            ]);
        }
    }
}
