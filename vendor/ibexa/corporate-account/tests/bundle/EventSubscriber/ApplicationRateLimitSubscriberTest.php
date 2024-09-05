<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\Bundle\CorporateAccount\EventSubscriber\ApplicationRateLimitSubscriber;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeCreateContentEvent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Exception\ApplicationRateLimitExceededException;
use Ibexa\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

final class ApplicationRateLimitSubscriberTest extends TestCase
{
    private const TEST_CLIENT_IP = '123.456.78.90';

    public function testOnBeforeCreateContentHasHighEnoughPriority(): void
    {
        /** @var array<string, array{string, int}> $events */
        $events = ApplicationRateLimitSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(BeforeCreateContentEvent::class, $events);
        self::assertCount(2, $events[BeforeCreateContentEvent::class]);
        self::assertGreaterThanOrEqual(100, $events[BeforeCreateContentEvent::class][1]);
    }

    public function testRateLimitOK(): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(
            new ApplicationRateLimitSubscriber(
                $this->createRequestStackMock(self::TEST_CLIENT_IP),
                $this->createPermissionResolverMock(10),
                $this->createConfigResolverMock(),
                $this->createCorporateAccountConfigurationMock(),
                $this->getRateLimiterFactory(5)
            )
        );

        try {
            $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
            $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
            $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
            $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
            $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
        } catch (ApplicationRateLimitExceededException $exception) {
            self::fail('Rate limit exceeded');
        }

        self::assertTrue(true);
    }

    public function testRateLimitExceeded(): void
    {
        self::expectException(ApplicationRateLimitExceededException::class);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(
            new ApplicationRateLimitSubscriber(
                $this->createRequestStackMock(self::TEST_CLIENT_IP),
                $this->createPermissionResolverMock(10),
                $this->createConfigResolverMock(),
                $this->createCorporateAccountConfigurationMock(),
                $this->getRateLimiterFactory(1)
            )
        );

        $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
        $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
    }

    public function testNonAnonymousUserContextIgnored(): void
    {
        $rateLimiter = $this->getRateLimiterFactory(1);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(
            new ApplicationRateLimitSubscriber(
                $this->createRequestStackMock(self::TEST_CLIENT_IP),
                $this->createPermissionResolverMock(1234),
                $this->createConfigResolverMock(),
                $this->createCorporateAccountConfigurationMock(),
                $rateLimiter
            )
        );

        $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
        $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
        $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());

        $limiter = $rateLimiter->create(
            sprintf(
                'user_%d_ip_%s',
                1234,
                self::TEST_CLIENT_IP
            )
        );

        self::assertTrue($limiter->consume(0)->isAccepted());
    }

    public function testNonRequestContextIgnored(): void
    {
        $rateLimiter = $this->getRateLimiterFactory(1);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(
            new ApplicationRateLimitSubscriber(
                $this->createRequestStackMock(),
                $this->createPermissionResolverMock(10),
                $this->createConfigResolverMock(),
                $this->createCorporateAccountConfigurationMock(),
                $rateLimiter
            )
        );

        $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
        $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());
        $eventDispatcher->dispatch($this->createBeforeCreateContentEvent());

        $limiter = $rateLimiter->create(
            sprintf(
                'user_%d_ip_%s',
                1234,
                self::TEST_CLIENT_IP
            )
        );

        self::assertTrue($limiter->consume(0)->isAccepted());
    }

    private function createRequestStackMock(?string $ip = null): RequestStack
    {
        $requestMock = self::createMock(Request::class);
        $requestMock
            ->method('getClientIp')
            ->willReturn($ip);

        $requestStackMock = self::createMock(RequestStack::class);
        $requestStackMock
            ->method('getMainRequest')
            ->willReturn($ip !== null ? $requestMock : null);

        return $requestStackMock;
    }

    private function createPermissionResolverMock(int $userId): PermissionResolver
    {
        $userReferenceMock = self::createMock(UserReference::class);
        $userReferenceMock
            ->method('getUserId')
            ->willReturn($userId);

        $permissionResolverMock = self::createMock(PermissionResolver::class);
        $permissionResolverMock
            ->method('getCurrentUserReference')
            ->willReturn($userReferenceMock);

        return $permissionResolverMock;
    }

    private function createCorporateAccountConfigurationMock(): CorporateAccountConfiguration
    {
        $corporateAccountConfigurationMock = self::createMock(CorporateAccountConfiguration::class);
        $corporateAccountConfigurationMock
            ->method('getApplicationContentTypeIdentifier')
            ->willReturn('corporate_account_application');

        return $corporateAccountConfigurationMock;
    }

    private function createConfigResolverMock(): ConfigResolverInterface
    {
        $configResolverMock = self::createMock(ConfigResolverInterface::class);
        $configResolverMock
            ->method('getParameter')
            ->with('anonymous_user_id')
            ->willReturn(10);

        return $configResolverMock;
    }

    private function createBeforeCreateContentEvent(): BeforeCreateContentEvent
    {
        return new BeforeCreateContentEvent(
            new ContentCreateStruct([
                'contentType' => new ContentType([
                    'identifier' => 'corporate_account_application',
                ]),
            ]),
            []
        );
    }

    private function getRateLimiterFactory(int $limit): RateLimiterFactory
    {
        return new RateLimiterFactory([
            'id' => 'corporate_account_application',
            'limit' => $limit,
            'interval' => '5 minutes',
            'policy' => 'fixed_window',
        ], new InMemoryStorage());
    }
}
