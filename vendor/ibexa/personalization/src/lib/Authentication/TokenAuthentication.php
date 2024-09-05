<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Authentication;

use Ibexa\Contracts\Core\Repository\TokenService;
use Ibexa\Personalization\Value\Token\Token;
use Ibexa\Rest\Server\Exceptions\AuthenticationFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class TokenAuthentication implements AuthenticationInterface
{
    private const TYPE_BEARER = 'Bearer';

    private TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function authenticate(Request $request, string $action): bool
    {
        $server = $request->server;
        $headers = $server->getHeaders();

        if (
            !array_key_exists('AUTHORIZATION', $headers)
            || !str_starts_with($headers['AUTHORIZATION'], self::TYPE_BEARER)
        ) {
            throw new AuthenticationFailedException(
                'Missing Authorization header with ' . self::TYPE_BEARER . ' token',
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = str_replace(
            self::TYPE_BEARER,
            '',
            $headers['AUTHORIZATION']
        );

        return $this->tokenService->checkToken(
            Token::TYPE,
            ltrim($token),
            $action
        );
    }
}
