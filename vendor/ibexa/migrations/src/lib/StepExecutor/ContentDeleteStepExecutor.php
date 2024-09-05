<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use function get_class;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\ContentFilteringAdapter;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ContentDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Webmozart\Assert\Assert;

final class ContentDeleteStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    public function __construct(
        ContentService $contentService,
        ExecutorInterface $actionExecutor,
        ?LoggerInterface $logger = null
    ) {
        $this->contentService = $contentService;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ContentDeleteStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentDeleteStep $step
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content[]
     */
    protected function doHandle(StepInterface $step): array
    {
        $filter = new Filter();
        $filter->andWithCriterion($step->criterion);
        $filter->withSortClause(new Query\SortClause\ContentId());

        /** @var \Iterator|\Ibexa\Contracts\Core\Repository\Values\Content\Content[] $contentItems */
        $contentItems = new BatchIterator(new ContentFilteringAdapter($this->contentService, $filter));

        if (!$contentItems->valid()) {
            throw new RuntimeException(sprintf(
                '%s failed execution. No content found matching filters.',
                get_class($step)
            ));
        }

        $deletedContentItems = [];
        foreach ($contentItems as $content) {
            $contentType = $content->getContentType();

            try {
                $this->contentService->deleteContent($content->contentInfo);
            } catch (NotFoundException $e) {
                $this->getLogger()->notice(sprintf(
                    'Could not find content "%s" (ID: %s) of type "%s". Ignoring.',
                    $content->getName(),
                    $content->id,
                    $contentType->identifier,
                ));
            }

            $this->getLogger()->notice(sprintf(
                'Deleted content "%s" (ID: %s) of type "%s"',
                $content->getName(),
                $content->id,
                $contentType->identifier,
            ));

            $deletedContentItems[] = $content;
        }

        return $deletedContentItems;
    }

    protected function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isArray($executionResult);
        Assert::allIsInstanceOf($executionResult, Content::class);

        foreach ($executionResult as $valueObject) {
            foreach ($step->getActions() as $action) {
                $this->actionExecutor->handle($action, $valueObject);
            }
        }
    }
}

class_alias(ContentDeleteStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ContentDeleteStepExecutor');
