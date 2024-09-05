<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class Attribute extends ValueObjectVisitor
{
    /**
     * @var iterable<\Ibexa\Contracts\ProductCatalog\REST\Output\AttributePostProcessorInterface>
     */
    private iterable $postProcessors;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\REST\Output\AttributePostProcessorInterface> $postProcessors
     */
    public function __construct(iterable $postProcessors)
    {
        $this->postProcessors = $postProcessors;
    }

    private const OBJECT_IDENTIFIER = 'Attribute';
    private const ATTRIBUTE_IDENTIFIER_NAME = 'name';
    private const ATTRIBUTE_IDENTIFIER_ID = 'identifier';
    private const ATTRIBUTE_IDENTIFIER_DESCRIPTION = 'description';
    private const ATTRIBUTE_IDENTIFIER_TYPE = 'type';
    private const ATTRIBUTE_IDENTIFIER_TYPE_IDENTIFIER = 'type_identifier';
    private const ATTRIBUTE_IDENTIFIER_VALUE = 'value';
    private const ATTRIBUTE_IDENTIFIER_GROUP = 'group';
    private const ATTRIBUTE_IDENTIFIER_POSITION = 'position';
    private const ATTRIBUTE_IDENTIFIER_OPTIONS = 'options';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Attribute $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_ID, $data->attributeDefinition->getIdentifier());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_NAME, $data->attributeDefinition->getName());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_DESCRIPTION, $data->attributeDefinition->getDescription());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_TYPE, $data->attributeDefinition->getType()->getName());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_TYPE_IDENTIFIER, $data->attributeDefinition->getType()->getIdentifier());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_GROUP, $data->attributeDefinition->getGroup()->getName());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_POSITION, $data->attributeDefinition->getPosition());
        $generator->generateFieldTypeHash(self::ATTRIBUTE_IDENTIFIER_OPTIONS, $data->attributeDefinition->getOptions()->all());
        $generator->generateFieldTypeHash(self::ATTRIBUTE_IDENTIFIER_VALUE, $data->value);

        $extraOptions = [];
        foreach ($this->postProcessors as $postProcessor) {
            if ($postProcessor->supports($data)) {
                $extraOptions = array_merge($postProcessor->process($data), $extraOptions);
            }
        }
        $generator->generateFieldTypeHash('extra_options', $extraOptions);

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
