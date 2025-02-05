<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event;

use Ibexa\Personalization\SPI\UserAPIRequest;
use Symfony\Contracts\EventDispatcher\Event;

abstract class UserAPIEvent extends Event
{
    /** @var \Ibexa\Personalization\SPI\UserAPIRequest */
    private $request;

    public function getUserAPIRequest(): ?UserAPIRequest
    {
        return $this->request;
    }

    public function setUserAPIRequest(UserAPIRequest $request): void
    {
        $this->request = $request;
    }
}

class_alias(UserAPIEvent::class, 'EzSystems\EzRecommendationClient\Event\UserAPIEvent');
