<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Serializer\Handler;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\LandingPage\Type;
use Ibexa\FieldTypePage\Serializer\AttributeSerializationDispatcher;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class AttributeHandler implements SubscribingHandlerInterface
{
    /** @var \Ibexa\FieldTypePage\Serializer\AttributeSerializationDispatcher */
    private $attributeSerializationDispatcher;

    /**
     * @param \Ibexa\FieldTypePage\Serializer\AttributeSerializationDispatcher $attributeSerializationDispatcher
     */
    public function __construct(
        AttributeSerializationDispatcher $attributeSerializationDispatcher
    ) {
        $this->attributeSerializationDispatcher = $attributeSerializationDispatcher;
    }

    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => Attribute::class,
                'method' => 'serializeAttribute',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => Attribute::class,
                'method' => 'deserializeAttribute',
            ],
        ];
    }

    /**
     * @param \JMS\Serializer\JsonSerializationVisitor $visitor
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute $attribute
     * @param array $type
     * @param \JMS\Serializer\Context $context
     *
     * @return array
     */
    public function serializeAttribute(
        JsonSerializationVisitor $visitor,
        Attribute $attribute,
        array $type,
        Context $context
    ): array {
        $contextGroups = $context->hasAttribute('groups') ? $context->getAttribute('groups') : [];

        /** @var \SplStack $visitingStack */
        $visitingStack = $context->getVisitingStack();
        $visitedAttribute = $visitingStack->pop();

        if (!$visitedAttribute instanceof Attribute) {
            throw new \RuntimeException(sprintf('Expected to visit %s object.', Attribute::class));
        }

        $blockValue = $visitingStack->pop();

        if (!$blockValue instanceof BlockValue) {
            throw new \RuntimeException(sprintf('Expected to visit %s object.', BlockValue::class));
        }

        // unfortunately it's needed to revert stack to previous state
        $visitingStack->push($blockValue);
        $visitingStack->push($visitedAttribute);
        $visitingStack->rewind();

        $serializedValue = $this->attributeSerializationDispatcher->serialize($blockValue, $visitedAttribute);
        $serializedAttribute = [
            'id' => null,
            'name' => $attribute->getName(),
            'value' => $serializedValue,
        ];

        if (!in_array(Type::COMPARABLE_SERIALIZATION_CONTEXT_GROUP, $contextGroups, true)) {
            $serializedAttribute['id'] = $attribute->getId();
        }

        return $serializedAttribute;
    }

    /**
     * @param \JMS\Serializer\JsonDeserializationVisitor $visitor
     * @param array $data
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute
     */
    public function deserializeAttribute(
        JsonDeserializationVisitor $visitor,
        array $data
    ): Attribute {
        $blockValue = $visitor->getCurrentObject();

        if (!$blockValue instanceof BlockValue) {
            throw new \RuntimeException(sprintf('Expected to visit %s object.', BlockValue::class));
        }

        $deserializedValue = $this->attributeSerializationDispatcher->deserialize($blockValue, $data['name'], $data['value']);

        return new Attribute((string)$data['id'], $data['name'], $deserializedValue);
    }
}

class_alias(AttributeHandler::class, 'EzSystems\EzPlatformPageFieldType\Serializer\Handler\AttributeHandler');
