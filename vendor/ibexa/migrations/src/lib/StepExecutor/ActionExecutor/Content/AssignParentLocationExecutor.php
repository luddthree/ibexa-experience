<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\Content;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\Content\AssignParentLocation;
use Webmozart\Assert\Assert;

final class AssignParentLocationExecutor implements ExecutorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(
        LocationService $locationService
    ) {
        $this->locationService = $locationService;
    }

    public static function getExecutorKey(): string
    {
        return AssignParentLocation::TYPE;
    }

    public function handle(Action $action, APIValueObject $valueObject): void
    {
        Assert::isInstanceOf($action, AssignParentLocation::class);
        Assert::isInstanceOf($valueObject, Content::class);

        $locationCreateStruct = $this->locationService->newLocationCreateStruct(
            $action->getValue()
        );

        $this->locationService->createLocation(
            $valueObject->getVersionInfo()->getContentInfo(),
            $locationCreateStruct
        );
    }
}

class_alias(AssignParentLocationExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\Content\AssignParentLocationExecutor');
