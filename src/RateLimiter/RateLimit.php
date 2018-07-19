<?php

declare(strict_types=1);

namespace Lamoda\Tactician\RateLimit\RateLimiter;

final class RateLimit
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var int
     */
    private $limit;
    /**
     * @var int
     */
    private $milliseconds;

    public function __construct(string $key, int $limit, int $milliseconds)
    {
        $this->key = $key;
        $this->limit = $limit;
        $this->milliseconds = $milliseconds;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getMilliseconds(): int
    {
        return $this->milliseconds;
    }
}
