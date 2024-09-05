<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Exception;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class ObjectStateGroupCreateStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Persistence\TransactionHandler */
    private $transactionHandler;

    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    public function __construct(
        TransactionHandler $transactionHandler,
        ObjectStateService $objectStateService,
        ?LoggerInterface $logger = null
    ) {
        $this->transactionHandler = $transactionHandler;
        $this->objectStateService = $objectStateService;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ValueObject\Step\ObjectStateGroupCreateStep;
    }

    public function handle(StepInterface $step): void
    {
        Assert::isInstanceOf($step, ValueObject\Step\ObjectStateGroupCreateStep::class);

        $this->transactionHandler->beginTransaction();

        try {
            $this->createFromValueObjects($step->metadata);
            $this->transactionHandler->commit();
        } catch (Exception $e) {
            $this->transactionHandler->rollback();
            throw $e;
        }
    }

    private function createFromValueObjects(
        ValueObject\ObjectStateGroup\CreateMetadata $metadata
    ): void {
        $objectStateGroupCreateStruct = $this->objectStateService->newObjectStateGroupCreateStruct($metadata->identifier);
        $objectStateGroupCreateStruct->defaultLanguageCode = $metadata->mainTranslation;

        [
            $objectStateGroupCreateStruct->names,
            $objectStateGroupCreateStruct->descriptions
        ] = $this->transformTranslations($metadata->translations);

        $objectStateGroup = $this->objectStateService->createObjectStateGroup(
            $objectStateGroupCreateStruct
        );

        $this->getLogger()->notice(sprintf(
            'Added object state group: "%s" (ID: %s)',
            $objectStateGroup->identifier,
            $objectStateGroup->id,
        ));
    }

    /**
     * @param array<array<string>> $translations
     *
     * @return array<int, array<string>>
     */
    private function transformTranslations(array $translations): array
    {
        $names = $descriptions = [];

        foreach ($translations as $lang => $translation) {
            if (array_key_exists('name', $translation)) {
                $names[$lang] = $translation['name'];
            }

            if (array_key_exists('description', $translation)) {
                $descriptions[$lang] = $translation['description'];
            }
        }

        return [$names, $descriptions];
    }
}

class_alias(ObjectStateGroupCreateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ObjectStateGroupCreateStepExecutor');
