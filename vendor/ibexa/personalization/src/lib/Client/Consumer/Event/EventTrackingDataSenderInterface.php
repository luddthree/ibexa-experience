<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Event;

use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
interface EventTrackingDataSenderInterface
{
    public function sendEvent(string $userIdentifier, string $outputContentTypeId): ResponseInterface;
}
