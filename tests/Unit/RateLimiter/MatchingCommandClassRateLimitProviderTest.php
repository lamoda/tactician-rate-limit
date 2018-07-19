<?php

namespace Lamoda\Tactician\RateLimit\Tests\Unit\RateLimiter;

use Lamoda\Tactician\RateLimit\RateLimiter\FirstMatchedRateLimitProvider;
use Lamoda\Tactician\RateLimit\RateLimiter\MatchingCommandClassRateLimitProvider;
use Lamoda\Tactician\RateLimit\RateLimiter\RateLimit;
use PHPUnit\Framework\TestCase;

class MatchingCommandClassRateLimitProviderTest extends TestCase
{
    /**
     * @var int
     */
    private const LIMIT = 10;
    /**
     * @var int
     */
    private const MILLISECONDS = 1;

    /**
     * @var FirstMatchedRateLimitProvider
     */
    private $provider;

    protected function setUp()
    {
        parent::setUp();

        $this->provider = new MatchingCommandClassRateLimitProvider(
            \stdClass::class,
            self::LIMIT,
            self::MILLISECONDS
        );
    }

    /**
     * @dataProvider dataProvide
     */
    public function testProvide($command, $expectedResult): void
    {
        $result = $this->provider->provide($command);

        $this->assertEquals($expectedResult, $result);
    }

    public function dataProvide(): array
    {
        return [
            [
                new \stdClass(),
                new RateLimit(\stdClass::class, self::LIMIT, self::MILLISECONDS),
            ],
            [
                new class() {
                },
                null,
            ],
        ];
    }
}
