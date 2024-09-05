<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Ibexa\Migration\ValueObject\Step\ReferenceSetStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ReferenceSetStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Migration\Reference\CollectorInterface */
    private $collector;

    public function __construct(
        CollectorInterface $collector,
        ?LoggerInterface $logger = null
    ) {
        $this->collector = $collector;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ReferenceSetStep;
    }

    public function handle(StepInterface $step): void
    {
        /** @var \Ibexa\Migration\ValueObject\Step\ReferenceSetStep $step */
        $this->collector->collect(
            Reference::create(
                $step->name,
                $step->value
            )
        );

        $this->getLogger()->notice(sprintf(
            'Set reference named: "%s" with value: %s',
            $step->name,
            is_string($step->value) ? "\"{$step->value}\"" : $step->value,
        ));
    }
}

class_alias(ReferenceSetStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceSetStepExecutor');
