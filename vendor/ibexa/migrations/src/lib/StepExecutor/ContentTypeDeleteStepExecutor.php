<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ContentTypeDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class ContentTypeDeleteStepExecutor extends AbstractContentTypeStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    /** @var \Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface */
    private $contentTypeFinderRegistry;

    public function __construct(
        ExecutorInterface $actionExecutor,
        ContentTypeService $contentTypeService,
        FieldTypeServiceInterface $fieldTypeService,
        ContentTypeFinderRegistryInterface $contentTypeFinderRegistry,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($fieldTypeService);

        $this->contentTypeService = $contentTypeService;
        $this->logger = $logger ?? new NullLogger();
        $this->actionExecutor = $actionExecutor;
        $this->contentTypeFinderRegistry = $contentTypeFinderRegistry;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ContentTypeDeleteStep;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function doHandle(StepInterface $step): ContentType
    {
        /** @var \Ibexa\Migration\ValueObject\Step\ContentTypeDeleteStep $step */
        Assert::isInstanceOf($step, ContentTypeDeleteStep::class);

        $matcher = $step->getMatch();

        $finder = $this->contentTypeFinderRegistry->getFinder($matcher->getField());
        $contentType = $finder->find($matcher);
        $this->contentTypeService->deleteContentType($contentType);

        $this->getLogger()->notice(sprintf(
            'Deleted content type: "%s" (ID: %s)',
            $contentType->identifier,
            $contentType->id,
        ));

        return $contentType;
    }

    public function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isInstanceOf($executionResult, ContentType::class);

        foreach ($step->getActions() as $action) {
            $this->actionExecutor->handle($action, $executionResult);
        }
    }
}

class_alias(ContentTypeDeleteStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ContentTypeDeleteStepExecutor');
