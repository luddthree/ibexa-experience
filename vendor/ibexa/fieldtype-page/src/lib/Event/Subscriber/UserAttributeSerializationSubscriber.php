<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use Ibexa\AdminUi\Form\Type\UserChoiceType;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\FieldTypePage\Event\AttributeSerializationEvent;
use Ibexa\FieldTypePage\Event\PageEvents;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserAttributeSerializationSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private UserService $userService;

    public function __construct(
        UserService $userService,
        ?LoggerInterface $logger = null
    ) {
        $this->userService = $userService;
        $this->logger = $logger ?? new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PageEvents::ATTRIBUTE_SERIALIZATION => ['onAttributeSerialization', 10],
            PageEvents::ATTRIBUTE_DESERIALIZATION => ['onAttributeDeserialization', 10],
        ];
    }

    public function onAttributeSerialization(AttributeSerializationEvent $event): void
    {
        $attributeDefinition = $event->getAttributeDefinition();
        if ($attributeDefinition === null || $attributeDefinition->getType() !== UserChoiceType::class) {
            return;
        }

        $deserializedValue = $event->getDeserializedValue();
        if ($deserializedValue === null) {
            return;
        }

        if (!is_array($deserializedValue)) {
            $deserializedValue = [$deserializedValue];
        }

        $event->setSerializedValue(
            implode(
                ',',
                array_map(static fn (User $value): int => $value->getUserId(), $deserializedValue),
            ),
        );
        $event->stopPropagation();
    }

    public function onAttributeDeserialization(AttributeSerializationEvent $event): void
    {
        $serializedValue = $event->getSerializedValue();
        if (!is_string($serializedValue)) {
            return;
        }

        $attributeDefinition = $event->getAttributeDefinition();
        if ($attributeDefinition === null || $attributeDefinition->getType() !== UserChoiceType::class) {
            return;
        }

        $isMultiple = $attributeDefinition->getOptions()['multiple'] ?? false;
        if ($isMultiple) {
            $this->handleMultipleUsers($serializedValue, $event);

            return;
        }

        $this->handleSingleUser($serializedValue, $event);
    }

    private function handleSingleUser(string $serializedValue, AttributeSerializationEvent $event): void
    {
        try {
            $user = $this->userService->loadUser((int)$serializedValue);
            $event->setDeserializedValue($user);
        } catch (NotFoundException $e) {
            $this->logger->info(sprintf(
                'User with ID %s was not found. Serialized value is discarded.',
                $serializedValue,
            ));
        }

        $event->stopPropagation();
    }

    private function handleMultipleUsers(string $serializedValue, AttributeSerializationEvent $event): void
    {
        $serializedValues = explode(',', $serializedValue);

        $users = [];
        foreach ($serializedValues as $serializedValue) {
            try {
                $users[] = $this->userService->loadUser((int)$serializedValue);
            } catch (NotFoundException $e) {
                $this->logger->info(sprintf(
                    'User with ID %s was not found. Serialized value is discarded.',
                    $serializedValue,
                ));
            }
        }

        $event->setDeserializedValue($users);
        $event->stopPropagation();
    }
}
