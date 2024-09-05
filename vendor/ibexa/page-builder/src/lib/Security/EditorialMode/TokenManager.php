<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Security\EditorialMode;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * JSON Web Token manager.
 *
 * In comparison to the default implementation delivered as part of JWTAuthenticationBundle it doesn't dispatch events
 * and decorates token payload (overrides TTL, adds client ip).
 */
final class TokenManager implements JWTTokenManagerInterface
{
    /** @var \Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface */
    private $jwtEncoder;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var string */
    private $userIdentityField;

    /** @var string */
    private $userIdClaim;

    /** @var int */
    private $ttl;

    /** @var bool */
    private $verifyIp;

    /**
     * @param \Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface $jwtEncoder
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param string $userIdClaim
     * @param int $ttl
     * @param bool $verifyIp
     */
    public function __construct(
        JWTEncoderInterface $jwtEncoder,
        RequestStack $requestStack,
        string $userIdClaim,
        int $ttl,
        bool $verifyIp = true
    ) {
        $this->jwtEncoder = $jwtEncoder;
        $this->requestStack = $requestStack;
        $this->userIdentityField = 'username';
        $this->userIdClaim = $userIdClaim;
        $this->ttl = $ttl;
        $this->verifyIp = $verifyIp;
    }

    /**
     * {@inheritdoc}
     */
    public function create(UserInterface $user)
    {
        return $this->jwtEncoder->encode($this->createPayload($user));
    }

    /**
     * {@inheritdoc}
     */
    public function decode(TokenInterface $token)
    {
        return $this->decodeFromString($token->getCredentials());
    }

    /**
     * Decodes JWT from string.
     *
     * @param string $token
     *
     * @return array|bool
     *
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     */
    public function decodeFromString(string $token)
    {
        if (!($payload = $this->jwtEncoder->decode($token))) {
            return false;
        }

        if (!$this->isValidClientIp($payload['ip'] ?? null)) {
            return false;
        }

        return $payload;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserIdentityField($field)
    {
        $this->userIdentityField = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserIdentityField()
    {
        return $this->userIdentityField;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserIdClaim()
    {
        return $this->userIdClaim;
    }

    /**
     * Validates client ip if enabled.
     *
     * @param string|null $ip
     *
     * @return bool
     */
    private function isValidClientIp(?string $ip): bool
    {
        if ($this->verifyIp) {
            $request = $this->requestStack->getCurrentRequest();
            if ($ip === null || $ip !== $request->getClientIp()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Creates token payload.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     *
     * @return array
     */
    private function createPayload(UserInterface $user): array
    {
        $expiresAt = time() + $this->ttl;
        $clientIp = $this->requestStack->getCurrentRequest()->getClientIp();

        return [
            'exp' => $expiresAt,
            'jti' => (string)Uuid::uuid4(),
            'ip' => $clientIp,
            'mt' => microtime(),
            $this->getUserIdClaim() => $user->getUsername(),
        ];
    }
}

class_alias(TokenManager::class, 'EzSystems\EzPlatformPageBuilder\Security\EditorialMode\TokenManager');
