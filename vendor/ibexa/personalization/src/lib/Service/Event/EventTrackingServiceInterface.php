<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Event;

/**
 * @internal
 */
interface EventTrackingServiceInterface
{
    public function handleEvent(string $userIdentifier, string $outputContentTypeId): void;
}
