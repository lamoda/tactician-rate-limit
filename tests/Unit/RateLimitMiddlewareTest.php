<?php

namespace Lamoda\Tactician\RateLimit\Tests\Unit;

use Lamoda\Tactician\RateLimit\RateLimiter\RateLimit;
use Lamoda\Tactician\RateLimit\RateLimiter\RateLimiterInterface;
use Lamoda\Tactician\RateLimit\RateLimiter\RateLimitProviderInterface;
use Lamoda\Tactician\RateLimit\RateLimitMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RateLimitMiddlewareTest extends TestCase
{
    /**
     * @var RateLimitProviderInterface | MockObject
     */
    private $rateLimitProvider;
    /**
     * @var RateLimiterInterface | MockObject
     */
    private $rateLimiter;
    /**
     * @var RateLimitMiddleware
     */
    private $middleware;

    protected function setUp()
    {
        $this->rateLimitProvider = $this->createMock(RateLimitProviderInterface::class);
        $this->rateLimiter = $this->createMock(RateLimiterInterface::class);

        $this->middleware = new RateLimitMiddleware(
            $this->rateLimitProvider,
            $this->rateLimiter
        );
    }

    public function testExecuteWithRateLimiting(): void
    {
        $command = new \stdClass();

        $rateLimit = new RateLimit('a', 1, 1);

        $rateLimiterCalled = false;
        $nextCalled = false;
        $next = function ($command) use (&$nextCalled, &$rateLimiterCalled) {
            $this->assertTrue($rateLimiterCalled);
            $nextCalled = true;
        };

        $this->rateLimitProvider->expects($this->any())
            ->method('provide')
            ->with($command)
            ->willReturn($rateLimit);

        $this->rateLimiter->expects($this->once())
            ->method('throttle')
            ->with($rateLimit)
            ->willReturnCallback(function () use (&$rateLimiterCalled) {
                $rateLimiterCalled = true;
            });

        $this->middleware->execute($command, $next);

        $this->assertTrue($nextCalled);
    }

    public function testExecuteWithoutRateLimiting(): void
    {
        $command = new \stdClass();

        $nextCalled = false;
        $next = function ($command) use (&$nextCalled, &$rateLimiterCalled) {
            $nextCalled = true;
        };

        $this->rateLimitProvider->expects($this->any())
            ->method('provide')
            ->with($command)
            ->willReturn(null);

        $this->rateLimiter->expects($this->never())
            ->method('throttle');

        $this->middleware->execute($command, $next);

        $this->assertTrue($nextCalled);
    }
}
