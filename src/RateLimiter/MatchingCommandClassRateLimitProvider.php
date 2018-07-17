<?php

declare(strict_types=1);

namespace Lamoda\Tactician\RateLimit\RateLimiter;

final class MatchingCommandClassRateLimitProvider implements RateLimitProviderInterface
{
    /**
     * @var string
     */
    private $className;
    /**
     * @var int
     */
    private $limit;
    /**
     * @var int
     */
    private $milliseconds;

    public function __construct(string $className, int $limit, int $milliseconds)
    {
        $this->className = $className;
        $this->limit = $limit;
        $this->milliseconds = $milliseconds;
    }

    public function provide($command): ?RateLimit
    {
        if (!$command instanceof $this->className) {
            return null;
        }

        return new RateLimit($this->className, $this->limit, $this->milliseconds);
    }
}
