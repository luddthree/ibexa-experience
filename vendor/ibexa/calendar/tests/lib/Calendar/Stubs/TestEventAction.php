<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar\Stubs;

use Ibexa\Contracts\Calendar\EventAction\EventActionContext;
use Ibexa\Contracts\Calendar\EventAction\EventActionInterface;

final class TestEventAction implements EventActionInterface
{
    /** @var string */
    private $identifier;

    public function __construct(string $identifier = 'test')
    {
        $this->identifier = $identifier;
    }

    public function getActionIdentifier(): string
    {
        return $this->identifier;
    }

    public function getActionLabel(): string
    {
        return $this->identifier;
    }

    public function supports(EventActionContext $context): bool
    {
        return false;
    }

    public function execute(EventActionContext $context): void
    {
    }
}

class_alias(TestEventAction::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\Stubs\TestEventAction');
