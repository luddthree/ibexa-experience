<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Security\EditorialMode;

use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken as BasePostAuthenticationGuardToken;

class PostAuthenticationGuardToken extends BasePostAuthenticationGuardToken
{
}

class_alias(PostAuthenticationGuardToken::class, 'EzSystems\EzPlatformPageBuilder\Security\EditorialMode\PostAuthenticationGuardToken');
