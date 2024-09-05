<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\OAuth2Client\Security\Authenticator;

use League\OAuth2\Client\Token\AccessTokenInterface;

final class OAuth2Credentials
{
    /** @var string */
    private $identifier;

    /** @var \League\OAuth2\Client\Token\AccessTokenInterface */
    private $accessToken;

    public function __construct(string $identifier, AccessTokenInterface $accessToken)
    {
        $this->identifier = $identifier;
        $this->accessToken = $accessToken;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getAccessToken(): AccessTokenInterface
    {
        return $this->accessToken;
    }
}

class_alias(OAuth2Credentials::class, 'Ibexa\Platform\Bundle\OAuth2Client\Security\Authenticator\OAuth2Credentials');
