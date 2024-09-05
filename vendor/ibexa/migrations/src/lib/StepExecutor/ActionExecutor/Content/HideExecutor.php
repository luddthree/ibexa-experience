<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\Content;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\Content\Hide;
use Webmozart\Assert\Assert;

final class HideExecutor implements ExecutorInterface
{
    private ContentService $contentService;

    public function __construct(
        ContentService $contentService
    ) {
        $this->contentService = $contentService;
    }

    public static function getExecutorKey(): string
    {
        return Hide::TYPE;
    }

    public function handle(Action $action, APIValueObject $valueObject): void
    {
        Assert::isInstanceOf($action, Hide::class);
        Assert::isInstanceOf($valueObject, Content::class);

        $this->contentService->hideContent(
            $valueObject->getVersionInfo()->getContentInfo(),
        );
    }
}
