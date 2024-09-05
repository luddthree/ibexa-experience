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

final class ObjectStateCreateStepExecutor implements StepExecutorInterface, LoggerAwareInterface
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
        return $step instanceof ValueObject\Step\ObjectStateCreateStep;
    }

    public function handle(StepInterface $step): void
    {
        Assert::isInstanceOf($step, ValueObject\Step\ObjectStateCreateStep::class);

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
        ValueObject\ObjectState\CreateMetadata $metadata
    ): void {
        $objectStateCreateStruct = $this->objectStateService->newObjectStateCreateStruct($metadata->identifier);
        $objectStateCreateStruct->defaultLanguageCode = $metadata->mainTranslation;

        [
            $objectStateCreateStruct->names,
            $objectStateCreateStruct->descriptions
        ] = $this->transformTranslations($metadata->translations);

        $objectStateCreateStruct->priority = $metadata->priority;

        if (is_numeric($metadata->objectStateGroup)) {
            $objectStateGroup = $this->objectStateService->loadObjectStateGroup($metadata->objectStateGroup);
        } else {
            $objectStateGroup = $this->objectStateService->loadObjectStateGroupByIdentifier($metadata->objectStateGroup);
        }

        $objectState = $this->objectStateService->createObjectState(
            $objectStateGroup,
            $objectStateCreateStruct
        );

        $this->getLogger()->notice(sprintf(
            'Added object state: "%s" (ID: %s)',
            $objectState->identifier,
            $objectState->id,
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

class_alias(ObjectStateCreateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ObjectStateCreateStepExecutor');
