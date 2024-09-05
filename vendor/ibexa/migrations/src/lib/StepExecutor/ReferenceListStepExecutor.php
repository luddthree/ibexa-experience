<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\ValueObject\Step\ReferenceListStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ReferenceListStepExecutor implements StepExecutorInterface, LoggerAwareInterface
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
        return $step instanceof ReferenceListStep;
    }

    public function handle(StepInterface $step): void
    {
        $collection = $this->collector->getCollection()->getAll();

        foreach ($collection as $reference) {
            $value = $reference->getValue();
            $this->getLogger()->debug(sprintf(
                'Reference - name: "%s", value (%s): %s',
                $reference->getName(),
                gettype($value),
                is_string($value) ? "\"$value\"" : $value,
            ));
        }
    }
}

class_alias(ReferenceListStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceListStepExecutor');
