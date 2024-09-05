<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\Content\Update;

use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\Content\AssignSection as AssignSectionAction;
use Webmozart\Assert\Assert;

final class AssignSectionExecutor implements ExecutorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\SectionService */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public static function getExecutorKey(): string
    {
        return AssignSectionAction::TYPE;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handle(Action $action, APIValueObject $content): void
    {
        Assert::isInstanceOf($action, AssignSectionAction::class);

        $section = $this->getSection($action);
        $this->sectionService->assignSection($content->contentInfo, $section);
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getSection(AssignSectionAction $action): Section
    {
        $id = $action->getId();
        if ($id !== null) {
            return $this->sectionService->loadSection($id);
        }

        $identifier = $action->getIdentifier();
        if ($identifier !== null) {
            return $this->sectionService->loadSectionByIdentifier($identifier);
        }

        throw new InvalidArgumentException(
            '$action',
            'Action object does not contain ID nor section identifier',
        );
    }
}
