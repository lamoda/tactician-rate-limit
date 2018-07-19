<?php

namespace Lamoda\Tactician\RateLimit\Tests\Unit\RateLimiter;

use Lamoda\Tactician\RateLimit\RateLimiter\FirstMatchedRateLimitProvider;
use Lamoda\Tactician\RateLimit\RateLimiter\RateLimit;
use Lamoda\Tactician\RateLimit\RateLimiter\RateLimitProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FirstMatchedRateLimitProviderTest extends TestCase
{
    /**
     * @var RateLimitProviderInterface | MockObject
     */
    private $mockedProvider1;
    /**
     * @var RateLimitProviderInterface | MockObject
     */
    private $mockedProvider2;
    /**
     * @var FirstMatchedRateLimitProvider
     */
    private $provider;

    protected function setUp()
    {
        parent::setUp();

        $this->mockedProvider1 = $this->createMock(RateLimitProviderInterface::class);
        $this->mockedProvider2 = $this->createMock(RateLimitProviderInterface::class);

        $this->provider = new FirstMatchedRateLimitProvider(
            $this->mockedProvider1,
            $this->mockedProvider2
        );
    }

    /**
     * @dataProvider dataProvide
     */
    public function testProvide($result1, $result2, $expectedResult): void
    {
        $command = new \stdClass();

        $this->mockedProvider1->expects($this->any())
            ->method('provide')
            ->with($command)
            ->willReturn($result1);

        $this->mockedProvider2->expects($this->any())
            ->method('provide')
            ->with($command)
            ->willReturn($result2);

        $result = $this->provider->provide($command);

        $this->assertSame($expectedResult, $result);
    }

    public function dataProvide(): array
    {
        $result1 = new RateLimit('a', 1, 2);
        $result2 = new RateLimit('b', 3, 4);

        return [
            [
                null,
                null,
                null,
            ],
            [
                $result1,
                null,
                $result1,
            ],
            [
                null,
                $result2,
                $result2,
            ],
            [
                $result1,
                $result2,
                $result1,
            ],
        ];
    }
}
