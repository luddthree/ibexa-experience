<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\OAuth2Client\Exception;

/**
 * Marker interface for all exception thrown by this library.
 */
interface OAuth2ClientException
{
}

class_alias(OAuth2ClientException::class, 'Ibexa\Platform\Contracts\OAuth2Client\Exception\OAuth2ClientException');
