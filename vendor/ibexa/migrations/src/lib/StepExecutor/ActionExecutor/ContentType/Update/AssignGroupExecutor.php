<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\ContentType\Update;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\ContentType\AssignContentTypeGroup;
use Webmozart\Assert\Assert;

final class AssignGroupExecutor implements ExecutorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(
        ContentTypeService $contentTypeService
    ) {
        $this->contentTypeService = $contentTypeService;
    }

    public static function getExecutorKey(): string
    {
        return AssignContentTypeGroup::TYPE;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action\ContentType\AssignContentTypeGroup $action
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $valueObject
     */
    public function handle(Action $action, APIValueObject $valueObject): void
    {
        Assert::isInstanceOf($action, AssignContentTypeGroup::class);
        Assert::isInstanceOf($valueObject, ContentType::class);

        $group = $this->contentTypeService->loadContentTypeGroupByIdentifier($action->getValue());
        $this->contentTypeService->assignContentTypeGroup($valueObject, $group);
    }
}

class_alias(AssignGroupExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\ContentType\Update\AssignGroupExecutor');
