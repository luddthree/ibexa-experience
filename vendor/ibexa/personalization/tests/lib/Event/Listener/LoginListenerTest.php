<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Event\Listener;

use GuzzleHttp\Client;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Event\Listener\LoginListener;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @covers \Ibexa\Personalization\Event\Listener\LoginListener
 */
final class LoginListenerTest extends TestCase
{
    private const RECOMMENDATION_SESSION_KEY = 'recommendation-session-id';
    private const SESSION_ID = 'eZSESSID123456789';
    private const CUSTOMER_ID = 12345;
    private const ENDPOINT_URL = 'https://reco.engine.test';

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\Session\SessionInterface */
    private $session;

    /** @var \Ibexa\Personalization\Client\PersonalizationClientInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $client;

    /** @var \GuzzleHttp\Client|\PHPUnit\Framework\MockObject\MockObject */
    private $guzzleClient;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface */
    private $logger;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Security\Core\Authentication\Token\TokenInterface */
    private $token;

    /** @var \Ibexa\Personalization\Event\Listener\LoginListener */
    private $loginListener;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface */
    private $siteAccessService;

    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->session = $this->createMock(SessionInterface::class);
        $this->client = $this->createMock(PersonalizationClientInterface::class);
        $this->guzzleClient = $this->createMock(Client::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->token = $this->createMock(AbstractToken::class);
        $this->siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $this->loginListener = new LoginListener(
            $this->authorizationChecker,
            $this->session,
            $this->client,
            $this->configResolver,
            $this->logger,
            $this->siteAccessService,
            self::ENDPOINT_URL
        );
    }

    public function testDoNotStartSessionWhenRestRequest(): void
    {
        $this->assertSessionNotStarted(true);
    }

    public function testDoNotStartSessionWhenUserIsNotAuthenticated(): void
    {
        $this->configureAuthorizationCheckerToReturnIsUserAuthenticated(false, false);
        $this->assertSessionNotStarted(false);
    }

    public function testDoNotStartSessionWhenCustomerIdIsNotConfigured(): void
    {
        $this->configureAuthorizationCheckerToReturnIsUserAuthenticated(true, true);

        $this->siteAccessService
            ->expects(self::once())
            ->method('getCurrent')
            ->willReturn(new SiteAccess('ibexa_demo_site'));

        $this->configResolver
            ->expects(self::atLeastOnce())
            ->method('hasParameter')
            ->with('personalization.authentication.customer_id')
            ->willReturn(false);

        $this->assertSessionNotStarted(false);
    }

    public function testStartRecommendationSession(): void
    {
        $this->configureAuthorizationCheckerToReturnIsUserAuthenticated(true, true);
        $this->configureSession(self::SESSION_ID);
        $this->client
            ->expects(self::once())
            ->method('getHttpClient')
            ->willReturn($this->guzzleClient);

        $this->configureConfigResolverToReturnEndpointParameters();

        $this->guzzleClient
            ->expects(self::once())
            ->method('__call')
            ->with('get', [
                $this->getEndpointUri(self::ENDPOINT_URL, self::CUSTOMER_ID, self::SESSION_ID),
            ])
            ->willReturn($this->createMock(ResponseInterface::class));

        $event = $this->createTestEvent(false, true);
        $this->loginListener->onSecurityInteractiveLogin($event);
        $request = $event->getRequest();

        self::assertTrue($request->getSession()->isStarted());
        self::assertEquals(
            self::SESSION_ID,
            $request->cookies->get(self::RECOMMENDATION_SESSION_KEY)
        );
    }

    private function assertSessionNotStarted(bool $isRestRequest): void
    {
        $event = $this->createTestEvent($isRestRequest, false);
        $this->loginListener->onSecurityInteractiveLogin($event);
        $session = $event->getRequest()->getSession();

        self::assertFalse($session->isStarted());
    }

    private function createTestEvent(bool $isRestRequest, bool $isSessionStarted): InteractiveLoginEvent
    {
        return $this->createInteractiveLoginEvent(
            $this->createRequest($isRestRequest, $this->createSession($isSessionStarted)),
            $this->token
        );
    }

    private function createInteractiveLoginEvent(Request $request, TokenInterface $token): InteractiveLoginEvent
    {
        return new InteractiveLoginEvent($request, $token);
    }

    private function createRequest(bool $isRestRequest, SessionInterface $session): Request
    {
        $request = new Request();
        $request->attributes->set('is_rest_request', $isRestRequest);
        $request->setSession($session);

        return $request;
    }

    private function createSession(bool $isStarted): SessionInterface
    {
        $testSession = $this->createMock(SessionInterface::class);
        $testSession
            ->expects(self::once())
            ->method('isStarted')
            ->willReturn($isStarted);

        return $testSession;
    }

    private function configureAuthorizationCheckerToReturnIsUserAuthenticated(
        bool $isAuthenticatedFully,
        bool $isAuthenticatedRemembered
    ): void {
        $this->authorizationChecker
            ->expects(self::atLeastOnce())
            ->method('isGranted')
            ->withConsecutive(
                ['IS_AUTHENTICATED_FULLY'],
                ['IS_AUTHENTICATED_REMEMBERED'],
            )
            ->willReturnOnConsecutiveCalls(
                $isAuthenticatedFully,
                $isAuthenticatedRemembered
            );
    }

    private function configureSession(string $sessionId): void
    {
        $this->session
            ->expects(self::once())
            ->method('isStarted')
            ->willReturn(false);

        $this->session
            ->expects(self::once())
            ->method('start')
            ->willReturn(true);

        $this->session
            ->expects(self::once())
            ->method('getId')
            ->willReturn($sessionId);
    }

    private function configureConfigResolverToReturnEndpointParameters(): void
    {
        $this->siteAccessService
            ->expects(self::once())
            ->method('getCurrent')
            ->willReturn(new SiteAccess('ibexa_demo_site'));

        $this->configResolver
            ->expects(self::atLeastOnce())
            ->method('hasParameter')
            ->with('personalization.authentication.customer_id')
            ->willReturn(true);

        $this->configResolver
            ->expects(self::atLeastOnce())
            ->method('getParameter')
            ->with('personalization.authentication.customer_id')
            ->willReturn(self::CUSTOMER_ID);
    }

    private function getEndpointUri(
        string $endpointUrl,
        int $customerId,
        string $sessionId
    ): string {
        return sprintf($endpointUrl . '/api/%d/login/%s/', $customerId, $sessionId);
    }
}
