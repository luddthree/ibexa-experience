<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\Content\Create;

use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\Content\AssignObjectState;
use Webmozart\Assert\Assert;

final class AssignObjectStateExecutor implements ExecutorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    public function __construct(
        ObjectStateService $objectStateService
    ) {
        $this->objectStateService = $objectStateService;
    }

    public static function getExecutorKey(): string
    {
        return AssignObjectState::TYPE;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action\Content\AssignObjectState $action
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $valueObject
     */
    public function handle(Action $action, APIValueObject $valueObject): void
    {
        Assert::isInstanceOf($action, AssignObjectState::class);
        Assert::isInstanceOf($valueObject, Content::class);

        $objectStateGroup = $this->objectStateService->loadObjectStateGroupByIdentifier(
            $action->getGroupIdentifier()
        );

        $objectState = $this->objectStateService->loadObjectStateByIdentifier(
            $objectStateGroup,
            $action->getIdentifier()
        );

        $this->objectStateService->setContentState($valueObject->contentInfo, $objectStateGroup, $objectState);
    }
}

class_alias(AssignObjectStateExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\Content\Create\AssignObjectStateExecutor');
