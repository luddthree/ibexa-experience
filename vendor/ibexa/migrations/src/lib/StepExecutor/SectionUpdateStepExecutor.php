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
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Section\Matcher;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\SectionUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class SectionUpdateStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
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
        ActionExecutor\ExecutorInterface $actionExecutor
    ) {
        $this->transactionHandler = $transactionHandler;
        $this->sectionService = $sectionService;
        $this->actionExecutor = $actionExecutor;
        $this->logger = new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof SectionUpdateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\SectionUpdateStep $step
     */
    public function doHandle(StepInterface $step): Section
    {
        $sectionUpdateStruct = $this->sectionService->newSectionUpdateStruct();
        $sectionUpdateStruct->identifier = $step->metadata->identifier;
        $sectionUpdateStruct->name = $step->metadata->name;

        if ($step->match->field === Matcher::IDENTIFIER) {
            Assert::string($step->match->value);
            $section = $this->sectionService->loadSectionByIdentifier($step->match->value);
        } elseif ($step->match->field === Matcher::ID) {
            Assert::integer($step->match->value);
            $section = $this->sectionService->loadSection($step->match->value);
        } else {
            throw new UnknownMatchPropertyException($step->match->field, [Matcher::IDENTIFIER]);
        }

        $section = $this->sectionService->updateSection($section, $sectionUpdateStruct);

        $this->getLogger()->notice(sprintf(
            'Updated section: "%s" (ID: %s)',
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

class_alias(SectionUpdateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\SectionUpdateStepExecutor');
