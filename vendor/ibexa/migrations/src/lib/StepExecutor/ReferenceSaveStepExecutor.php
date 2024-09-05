<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Reference\DumperInterface;
use Ibexa\Migration\ValueObject\Step\ReferenceSaveStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ReferenceSaveStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Migration\Reference\DumperInterface */
    private $dumper;

    public function __construct(
        DumperInterface $dumper,
        ?LoggerInterface $logger = null
    ) {
        $this->dumper = $dumper;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ReferenceSaveStep;
    }

    public function handle(StepInterface $step): void
    {
        /** @var \Ibexa\Migration\ValueObject\Step\ReferenceSaveStep $step */
        $this->dumper->dump($step->filename);

        $this->getLogger()->notice(sprintf(
            'Saved references to file: "%s"',
            $step->filename,
        ));
    }
}

class_alias(ReferenceSaveStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceSaveStepExecutor');
