<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function sprintf;
use Webmozart\Assert\Assert;

final class ContentTypeGroupDeleteStepExecutor implements LoggerAwareInterface, StepExecutorInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(
        Repository $repository,
        ContentTypeService $contentTypeService,
        ?LoggerInterface $logger = null
    ) {
        $this->repository = $repository;
        $this->contentTypeService = $contentTypeService;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ValueObject\Step\ContentTypeGroupDeleteStep;
    }

    public function handle(StepInterface $step): void
    {
        Assert::isInstanceOf($step, ValueObject\Step\ContentTypeGroupDeleteStep::class);

        $matcher = $step->match;

        Assert::isInstanceOf($matcher, ValueObject\ContentTypeGroup\Matcher::class);
        Assert::eq(ValueObject\ContentTypeGroup\Matcher::CONTENT_TYPE_NAME_IDENTIFIER, $matcher->field);

        $this->repository->beginTransaction();

        try {
            $this->createFromValueObjects($matcher);

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    private function createFromValueObjects(
        ValueObject\ContentTypeGroup\Matcher $matcher
    ): void {
        $contentTypeGroup = $this->contentTypeService->loadContentTypeGroupByIdentifier($matcher->value);
        $this->contentTypeService->deleteContentTypeGroup($contentTypeGroup);

        $this->getLogger()->notice(sprintf(
            'Deleted content type group: "%s" (ID: %s)',
            $contentTypeGroup->identifier,
            $contentTypeGroup->id,
        ));
    }
}

class_alias(ContentTypeGroupDeleteStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ContentTypeGroupDeleteStepExecutor');
