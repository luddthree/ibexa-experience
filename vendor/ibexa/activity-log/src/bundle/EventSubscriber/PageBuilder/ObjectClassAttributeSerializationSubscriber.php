<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\EventSubscriber\PageBuilder;

use Ibexa\Bundle\ActivityLog\Form\Type\ObjectClassChoiceType;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * @extends \Ibexa\Bundle\ActivityLog\EventSubscriber\PageBuilder\AbstractChoiceAttributeSerializationSubscriber<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface<object>>
 */
final class ObjectClassAttributeSerializationSubscriber extends AbstractChoiceAttributeSerializationSubscriber implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ActivityLogServiceInterface $activityLogService;

    public function __construct(
        ActivityLogServiceInterface $activityLogService
    ) {
        $this->activityLogService = $activityLogService;
    }

    protected function deserializeSingleItem(string $serializedValue): ?object
    {
        try {
            return $this->activityLogService->getObjectClass((int)$serializedValue);
        } catch (NotFoundException $e) {
            $this->logger->info(
                sprintf(
                    '%s not found. Discarding value. %s.',
                    ObjectClassInterface::class,
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

    protected function supportsAttribute(BlockAttributeDefinition $attributeDefinition): bool
    {
        return $attributeDefinition->getType() === ObjectClassChoiceType::class;
    }
}
