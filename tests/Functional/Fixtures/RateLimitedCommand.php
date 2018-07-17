<?php

declare(strict_types=1);

namespace Lamoda\Tactician\RateLimit\Tests\Functional\Fixtures;

final class RateLimitedCommand
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
