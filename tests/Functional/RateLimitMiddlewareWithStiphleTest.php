<?php

namespace Lamoda\Tactician\RateLimit\Tests\Functional;

use Lamoda\Tactician\RateLimit\Adapter\Stiphle\StiphleRateLimiterAdapter;
use Lamoda\Tactician\RateLimit\RateLimiter\MatchingCommandClassRateLimitProvider;
use Lamoda\Tactician\RateLimit\RateLimitMiddleware;
use Lamoda\Tactician\RateLimit\Tests\Functional\Fixtures\RateLimitedCommand;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use PHPUnit\Framework\TestCase;
use Stiphle\Throttle\LeakyBucket;

class RateLimitMiddlewareWithStiphleTest extends TestCase
{
    private const COMMANDS_LIMIT = 3;
    private const MILLISECONDS = 1000;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var float
     */
    private $lastHandledAt;

    protected function setUp()
    {
        $rateLimitProvider = new MatchingCommandClassRateLimitProvider(
            RateLimitedCommand::class,
            self::COMMANDS_LIMIT,
            self::MILLISECONDS
        );

        $throttle = new LeakyBucket();

        $rateLimiter = new StiphleRateLimiterAdapter($throttle);

        $rateLimitMiddleware = new RateLimitMiddleware(
            $rateLimitProvider,
            $rateLimiter
        );

        $handlerMiddleware = new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            new InMemoryLocator([
                RateLimitedCommand::class => new class($this) {
                    /**
                     * @var RateLimitMiddlewareWithStiphleTest
                     */
                    private $test;

                    public function __construct(RateLimitMiddlewareWithStiphleTest $test)
                    {
                        $this->test = $test;
                    }

                    public function handle(RateLimitedCommand $command): void
                    {
                        $this->test->updateHandleTime();
                    }
                },
            ]),
            new HandleInflector()
        );

        $this->commandBus = new CommandBus([
            $rateLimitMiddleware,
            $handlerMiddleware,
        ]);
    }

    public function testRateLimiting(): void
    {
        $command = new RateLimitedCommand(1);

        $initialTime = microtime(true) * 1000;

        $this->commandBus->handle($command);
        $this->assertTimeDiffIsLessThan($initialTime, 100);

        $this->commandBus->handle($command);
        $this->assertTimeDiffIsLessThan($initialTime, 100);

        $this->commandBus->handle($command);
        $this->assertTimeDiffIsLessThan($initialTime, 100);

        // Rate limiting should happen here

        $this->commandBus->handle($command);
        $this->assertTimeDiffIsGreaterThan($initialTime, 300);

        $this->commandBus->handle($command);
        $this->assertTimeDiffIsGreaterThan($initialTime, 600);
    }

    /**
     * Public just for internal class.
     */
    public function updateHandleTime(): void
    {
        $this->lastHandledAt = microtime(true) * 1000;
    }

    private function assertTimeDiffIsLessThan(float $initial, float $milliseconds): void
    {
        $this->assertNotNull($this->lastHandledAt);
        $this->assertLessThan($milliseconds, $this->lastHandledAt - $initial);
    }

    private function assertTimeDiffIsGreaterThan(float $initial, float $milliseconds): void
    {
        $this->assertNotNull($this->lastHandledAt);
        $this->assertGreaterThan($milliseconds, $this->lastHandledAt - $initial);
    }
}
