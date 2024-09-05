<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Security\EditorialMode;

use DateTime;
use Psr\Cache\CacheItemPoolInterface;

class TokenRevoker implements TokenRevokerInterface
{
    private const KEY_PREFIX = 'ez-revoked-jwt-token-';

    /** @var \Psr\Cache\CacheItemPoolInterface */
    private $cache;

    /**
     * @param \Psr\Cache\CacheItemPoolInterface $cache
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function isValid(array $token): bool
    {
        return !$this->cache->hasItem($this->getKey($token));
    }

    public function revoke(array $token): void
    {
        $item = $this->cache->getItem($this->getKey($token));
        $item->set(true);

        if (isset($token['exp'])) {
            $expiresAt = new DateTime();
            $expiresAt->setTimestamp($token['exp']);

            $item->expiresAt($expiresAt);
        }

        $this->cache->save($item);
    }

    /**
     * Generates cache item key.
     */
    private function getKey(array $token): string
    {
        return self::KEY_PREFIX . $this->getTokenId($token);
    }

    /**
     * Extract token UUID from payload.
     */
    private function getTokenId(array $token): string
    {
        return $token['jti'];
    }
}

class_alias(TokenRevoker::class, 'EzSystems\EzPlatformPageBuilder\Security\EditorialMode\TokenRevoker');
