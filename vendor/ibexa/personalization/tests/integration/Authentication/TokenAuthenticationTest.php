<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization\Authentication;

use Ibexa\Contracts\Core\Repository\TokenService;
use Ibexa\Contracts\Core\Repository\Values\Token\Token as RepositoryTokenValue;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use Ibexa\Personalization\Authentication\AuthenticationInterface;
use Ibexa\Personalization\Value\Token\Token;
use Ibexa\Rest\Server\Exceptions\AuthenticationFailedException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Ibexa\Personalization\Authentication\TokenAuthentication
 */
final class TokenAuthenticationTest extends IbexaKernelTestCase
{
    private const BEARER_TOKEN_PREFIX = 'Bearer ';

    private AuthenticationInterface $authentication;

    private TokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authentication = self::getServiceByClassName(AuthenticationInterface::class);
        $this->tokenService = self::getServiceByClassName(TokenService::class);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testAuthenticate(): void
    {
        $updateToken = $this->createToken(1000, Token::IDENTIFIER_UPDATE);

        self::assertTrue(
            $this->authentication->authenticate(
                $this->createRequest(self::BEARER_TOKEN_PREFIX . $updateToken->getToken()),
                Token::IDENTIFIER_UPDATE
            )
        );

        $exportToken = $this->createToken(1000, Token::IDENTIFIER_EXPORT);

        self::assertTrue(
            $this->authentication->authenticate(
                $this->createRequest(self::BEARER_TOKEN_PREFIX . $exportToken->getToken()),
                Token::IDENTIFIER_EXPORT
            )
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testAuthenticateWithInvalidToken(): void
    {
        $updateToken = $this->createToken(1000, Token::IDENTIFIER_UPDATE);

        self::assertFalse(
            $this->authentication->authenticate(
                $this->createRequest(self::BEARER_TOKEN_PREFIX . $updateToken->getToken()),
                Token::IDENTIFIER_EXPORT
            )
        );

        $exportToken = $this->createToken(1000, Token::IDENTIFIER_EXPORT);

        self::assertFalse(
            $this->authentication->authenticate(
                $this->createRequest(self::BEARER_TOKEN_PREFIX . $exportToken->getToken()),
                Token::IDENTIFIER_UPDATE
            )
        );

        $tokenToBeDeleted = $this->createToken(1000, Token::IDENTIFIER_EXPORT);
        $this->tokenService->deleteToken($tokenToBeDeleted);

        self::assertFalse(
            $this->authentication->authenticate(
                $this->createRequest(self::BEARER_TOKEN_PREFIX . $tokenToBeDeleted->getToken()),
                Token::IDENTIFIER_UPDATE
            )
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testAuthenticateWithExpiredToken(): void
    {
        $expiredToken = $this->createToken(
            0,
            Token::IDENTIFIER_UPDATE
        );

        sleep(1);

        self::assertFalse(
            $this->authentication->authenticate(
                $this->createRequest(self::BEARER_TOKEN_PREFIX . $expiredToken->getToken()),
                Token::IDENTIFIER_UPDATE
            )
        );
    }

    /**
     * @dataProvider provideDataForTestAuthenticateThrowAuthenticationFailedException
     */
    public function testAuthenticateThrowAuthenticationFailedException(Request $request): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage('Missing Authorization header with Bearer token');

        $this->authentication->authenticate($request, Token::IDENTIFIER_UPDATE);
    }

    /**
     * @return iterable<array{
     *     \Symfony\Component\HttpFoundation\Request
     * }>
     */
    public function provideDataForTestAuthenticateThrowAuthenticationFailedException(): iterable
    {
        yield 'Request with no authorization header' => [
            $this->createRequest(),
        ];

        yield 'Request with Basic authorization header' => [
            $this->createRequest('Basic 1YHd8%NBVCD3edc4radfv^wadMidwadzz'),
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function createToken(
        int $ttl,
        string $identifier
    ): RepositoryTokenValue {
        return $this->tokenService->generateToken(
            Token::TYPE,
            $ttl,
            $identifier
        );
    }

    private function createRequest(
        ?string $authorizationType = null
    ): Request {
        $request = new Request();

        if (null === $authorizationType) {
            return $request;
        }

        $request->server->set('HTTP_AUTHORIZATION', $authorizationType);

        return $request;
    }
}
