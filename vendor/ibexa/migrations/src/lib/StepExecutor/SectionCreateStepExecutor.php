<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class SectionCreateStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Persistence\TransactionHandler */
    private $transactionHandler;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    /** @var \Ibexa\Contracts\Core\Repository\SectionService */
    private $sectionService;

    public function __construct(
        TransactionHandler $transactionHandler,
        SectionService $sectionService,
        ActionExecutor\ExecutorInterface $actionExecutor,
        ?LoggerInterface $logger = null
    ) {
        $this->transactionHandler = $transactionHandler;
        $this->sectionService = $sectionService;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ValueObject\Step\SectionCreateStep;
    }

    protected function doHandle(StepInterface $step): Section
    {
        Assert::isInstanceOf($step, ValueObject\Step\SectionCreateStep::class);

        $sectionCreateStruct = $this->sectionService->newSectionCreateStruct();
        $sectionCreateStruct->identifier = $step->metadata->identifier;
        $sectionCreateStruct->name = $step->metadata->name;

        $section = $this->sectionService->createSection($sectionCreateStruct);

        $this->getLogger()->notice(sprintf(
            'Added section: "%s" (ID: %s)',
            $section->identifier,
            $section->id,
        ));

        return $section;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isInstanceOf($executionResult, Section::class);

        foreach ($step->getActions() as $action) {
            $this->actionExecutor->handle($action, $executionResult);
        }
    }
}

class_alias(SectionCreateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\SectionCreateStepExecutor');
