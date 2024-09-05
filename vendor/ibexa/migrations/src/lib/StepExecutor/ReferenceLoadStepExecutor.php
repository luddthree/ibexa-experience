<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Reference\LoaderInterface;
use Ibexa\Migration\ValueObject\Step\ReferenceLoadStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ReferenceLoadStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Migration\Reference\LoaderInterface */
    private $loader;

    public function __construct(
        LoaderInterface $loader,
        ?LoggerInterface $logger = null
    ) {
        $this->loader = $loader;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ReferenceLoadStep;
    }

    public function handle(StepInterface $step): void
    {
        /** @var \Ibexa\Migration\ValueObject\Step\ReferenceLoadStep $step */
        $this->loader->load($step->filename);

        $this->getLogger()->notice(sprintf(
            'Loaded references from file: "%s"',
            $step->filename,
        ));
    }
}

class_alias(ReferenceLoadStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceLoadStepExecutor');
