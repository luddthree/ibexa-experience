<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Connect\PageBuilder;

use Ibexa\Bundle\Connect\Event\PrePageBlockWebhookRequestEvent;
use Ibexa\Connect\PageBuilder\BlockClient;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class BlockClientTest extends TestCase
{
    private BlockClient $blockClient;

    /** @var \Symfony\Contracts\HttpClient\HttpClientInterface&\PHPUnit\Framework\MockObject\MockObject */
    private HttpClientInterface $httpClient;

    /** @var \Psr\Log\LoggerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private LoggerInterface $logger;

    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface&\PHPUnit\Framework\MockObject\MockObject */
    private EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->blockClient = new BlockClient(
            $this->httpClient,
            $this->eventDispatcher,
            $this->logger,
        );
    }

    public function testLogsIncompleteConfiguration(): void
    {
        $message = $this->getRenderingLogMessagePrefix(
            'Expected to have "url" attribute set. Block rendered without performing HTTP request'
        );
        $this->configureLoggerExpectations('warning', $message, [
            'block' => BlockValue::class,
        ]);
        $this->expectLoggerToNotHaveOtherMethodsCalled('warning');
        $this->expectEventDispatcherToNotBeCalled();

        $this->configureHttpClientMock(self::never());

        $blockValue = $this->buildBlockValue();
        $data = $this->blockClient->getExtraBlockData($blockValue);

        self::assertNull($data);
    }

    public function testLogsErrorWhenWebhookIsNotListening(): void
    {
        $message = $this->getRenderingLogMessagePrefix(
            'Webhook is not currently listening for requests in Ibexa Connect'
        );
        $this->configureLoggerExpectations('error', $message, [
            'block' => BlockValue::class,
        ]);
        $this->expectLoggerToNotHaveOtherMethodsCalled('error');
        $this->configureHttpClientMock(self::once(), 'Accepted');
        $this->expectEventDispatcherToEmitAnEvent();

        $blockValue = $this->buildBlockValue([
            'url' => 'http://some_url.local/',
        ]);
        $data = $this->blockClient->getExtraBlockData($blockValue);

        self::assertNull($data);
    }

    public function testThrowsExceptionWhenUrlAttributeIsNotAString(): void
    {
        $this->expectLoggerToNotBeCalled();
        $this->configureHttpClientMock(self::never());
        $this->expectEventDispatcherToNotBeCalled();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            $this->getRenderingLogMessagePrefix(
                '"url" block attribute is expected to be of type string, received array instead'
            ),
        );
        $blockValue = $this->buildBlockValue([
            'url' => [],
        ]);
        $this->blockClient->getExtraBlockData($blockValue);
    }

    public function testCorrectHttpClientResponse(): void
    {
        $this->expectLoggerToNotBeCalled();
        $this->configureHttpClientMock(self::once(), [
            'new_parameter' => [],
        ]);
        $this->expectEventDispatcherToEmitAnEvent();

        $blockValue = $this->buildBlockValue([
            'url' => 'http://some_url.local/',
        ]);
        $data = $this->blockClient->getExtraBlockData($blockValue);

        self::assertSame(
            [
                'new_parameter' => [],
            ],
            $data,
        );
    }

    public function testNonHttpExceptionsInClient(): void
    {
        $this->expectLoggerToNotBeCalled();
        $this->configureHttpClientMock(self::once(), new \LogicException('Foo'));
        $this->expectEventDispatcherToEmitAnEvent();

        $blockValue = $this->buildBlockValue([
            'url' => 'http://some_url.local/',
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Foo');
        $this->blockClient->getExtraBlockData($blockValue);
    }

    public function testHttpExceptionsInClient(): void
    {
        $message = $this->getRenderingLogMessagePrefix(
            'HTTP request processing ended with an error: Test exception message'
        );
        $this->configureLoggerExpectations('error', $message, [
            'block' => BlockValue::class,
            'exception' => ExceptionInterface::class,
        ]);
        $this->expectLoggerToNotHaveOtherMethodsCalled('error');
        $exception = new class ('Test exception message') extends \Exception implements ExceptionInterface {
        };
        $this->configureHttpClientMock(self::once(), $exception);
        $this->expectEventDispatcherToEmitAnEvent();

        $blockValue = $this->buildBlockValue([
            'url' => 'http://some_url.local/',
        ]);
        $data = $this->blockClient->getExtraBlockData($blockValue);

        self::assertNull($data);
    }

    public function testServerHttpExceptionsInClient(): void
    {
        $message = $this->getRenderingLogMessagePrefix(
            'Ibexa Connect server responded with an error: Scenario failed to complete. (HTTP code: 500)'
        );
        $this->configureLoggerExpectations('error', $message, [
            'block' => BlockValue::class,
            'exception' => ExceptionInterface::class,
        ]);
        $this->expectLoggerToNotHaveOtherMethodsCalled('error');
        $this->expectEventDispatcherToEmitAnEvent();

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn('Scenario failed to complete.');
        $exception = new class ('Test exception message', $response) extends \Exception implements ServerExceptionInterface {
            private ResponseInterface $response;

            public function __construct(string $message, ResponseInterface $response)
            {
                parent::__construct($message, 500);

                $this->response = $response;
            }

            public function getResponse(): ResponseInterface
            {
                return $this->response;
            }
        };
        $this->configureHttpClientMock(self::once(), $exception);

        $blockValue = $this->buildBlockValue([
            'url' => 'http://some_url.local/',
        ]);
        $data = $this->blockClient->getExtraBlockData($blockValue);

        self::assertNull($data);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function buildBlockValue(array $attributes = []): BlockValue
    {
        $mock = $this->createMock(BlockValue::class);

        $mock
            ->method('getId')
            ->willReturn('<BLOCK ID>');
        $mock
            ->method('getAttribute')
            ->willReturnCallback(static function (string $name) use ($attributes): ?Attribute {
                if (!isset($attributes[$name])) {
                    return null;
                }

                return new Attribute($name, $name, $attributes[$name]);
            });

        return $mock;
    }

    /**
     * @param string|array<mixed>|\Throwable $responseData
     */
    private function configureHttpClientMock(
        InvokedCount $invokedCount = null,
        $responseData = null
    ): void {
        $invokedCount ??= self::never();
        $invocationMocker = $this->httpClient
            ->expects($invokedCount)
            ->method('request');

        if (!$invokedCount->isNever()) {
            $responseMock = $this->createMock(ResponseInterface::class);
            if ($responseData instanceof \Throwable) {
                $responseMock
                    ->expects(self::once())
                    ->method('getContent')
                    ->willThrowException($responseData);
            } elseif (is_string($responseData)) {
                $responseMock
                    ->expects(self::once())
                    ->method('getContent')
                    ->willReturn($responseData);
            } else {
                $responseMock
                    ->expects(self::once())
                    ->method('toArray')
                    ->willReturn($responseData);
            }

            $invocationMocker->willReturn($responseMock);
        }
    }

    private function getRenderingLogMessagePrefix(string $message): string
    {
        return sprintf(
            'Rendering of Page Builder block "<BLOCK ID>" of type "ibexa_connect_block" failed. %s.',
            $message,
        );
    }

    /**
     * @phpstan-param 'warning'|'error' $logLevel
     *
     * @param array<string, class-string> $contextExpectations
     */
    private function configureLoggerExpectations(
        string $logLevel,
        string $message,
        array $contextExpectations = []
    ): void {
        $assertions = [];
        if ($contextExpectations) {
            $assertions = [
                self::callback(static function (array $context) use ($contextExpectations): bool {
                    foreach ($contextExpectations as $expectedKey => $expectedClass) {
                        self::assertArrayHasKey($expectedKey, $context);
                        self::assertInstanceOf($expectedClass, $context[$expectedKey]);
                    }

                    self::assertEqualsCanonicalizing(
                        array_keys($contextExpectations),
                        array_keys($context),
                        'More context is passed into logger than is tested.',
                    );

                    return true;
                }),
            ];
        }

        $this->logger
            ->expects(self::once())
            ->method($logLevel)
            ->with(
                self::identicalTo($message),
                ...$assertions,
            );
    }

    private function expectLoggerToNotBeCalled(): void
    {
        $this->logger->expects(self::never())->method(self::anything());
    }

    private function expectLoggerToNotHaveOtherMethodsCalled(string ...$methodNames): void
    {
        $constraints = array_map(static function (string $methodName): LogicalNot {
            return self::logicalNot(self::stringContains($methodName));
        }, $methodNames);

        $this->logger
            ->expects(self::never())
            ->method(self::logicalAnd(...$constraints));
    }

    private function expectEventDispatcherToNotBeCalled(): void
    {
        $this->eventDispatcher
            ->expects(self::never())
            ->method('dispatch');
    }

    private function expectEventDispatcherToEmitAnEvent(): void
    {
        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(
                self::isInstanceOf(PrePageBlockWebhookRequestEvent::class),
            );
    }
}
