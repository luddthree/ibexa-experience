<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar\EventAction;

/**
 * Represents event action.
 */
interface EventActionInterface
{
    public function getActionIdentifier(): string;

    /**
     * Returns human readable label for event action.
     */
    public function getActionLabel(): string;

    /**
     * Returns true if action is supports given $context.
     */
    public function supports(EventActionContext $context): bool;

    public function execute(EventActionContext $context): void;
}

class_alias(EventActionInterface::class, 'EzSystems\EzPlatformCalendar\Calendar\EventAction\EventActionInterface');
