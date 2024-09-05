<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\EventSubscriber\PageBuilder;

use Ibexa\Bundle\ActivityLog\Form\Type\ActionChoiceType;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActionInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @extends \Ibexa\Bundle\ActivityLog\EventSubscriber\PageBuilder\AbstractChoiceAttributeSerializationSubscriber<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActionInterface>
 */
final class ActionAttributeSerializationSubscriber extends AbstractChoiceAttributeSerializationSubscriber implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const SERIALIZATION_SEPARATOR = ',';

    private ActivityLogServiceInterface $activityLogService;

    public function __construct(
        ActivityLogServiceInterface $activityLogService,
        ?LoggerInterface $logger = null
    ) {
        $this->activityLogService = $activityLogService;
        $this->logger = $logger ?? new NullLogger();
    }

    protected function supportsAttribute(BlockAttributeDefinition $attributeDefinition): bool
    {
        return $attributeDefinition->getType() === ActionChoiceType::class;
    }

    protected function deserializeSingleItem(string $serializedValue): ?object
    {
        try {
            return $this->activityLogService->getAction((int)$serializedValue);
        } catch (NotFoundException $e) {
            $this->logger->info(
                sprintf(
                    '%s not found. Discarding value. %s.',
                    ActionInterface::class,
                    $e->getMessage(),
                ),
                ['exception' => $e],
            );
        }

        return null;
    }

    protected function serializeSingleItem(object $value): string
    {
        return (string)$value->getId();
    }
}
