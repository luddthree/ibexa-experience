<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeUpdateStruct;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;
use Ibexa\Rest\Server\Input\Parser\ContentTypeUpdate;

final class ProductTypeUpdate extends BaseParser
{
    private const CONTENT_TYPE_UPDATE_STRUCT_KEY = 'ContentTypeUpdateStruct';
    private const ASSIGNED_ATTRIBUTES_KEY = 'assigned_attributes';
    private const IDENTIFIER_KEY = 'identifier';
    private const IS_REQUIRED_KEY = 'is_required';
    private const IS_DISCRIMINATOR_KEY = 'is_discriminator';

    private const REQUIRED_OBJECT_KEYS = [
        self::CONTENT_TYPE_UPDATE_STRUCT_KEY,
    ];

    private const ALLOWED_OBJECT_KEYS = [
        self::CONTENT_TYPE_UPDATE_STRUCT_KEY,
        self::ASSIGNED_ATTRIBUTES_KEY,
    ];

    private ContentTypeUpdate $contentTypeUpdateParser;

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(
        ContentTypeUpdate $contentTypeUpdateParser,
        AttributeDefinitionServiceInterface $attributeDefinitionService
    ) {
        $this->contentTypeUpdateParser = $contentTypeUpdateParser;
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductTypeUpdateStruct
    {
        $inputKeys = array_keys($data);
        $missingKeys = array_diff(self::REQUIRED_OBJECT_KEYS, $inputKeys);
        $redundantKeys = array_diff($inputKeys, self::ALLOWED_OBJECT_KEYS);

        if (!empty($missingKeys)) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for Product Type.',
                    implode(', ', $missingKeys)
                )
            );
        }

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Product Type are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $contentTypeUpdateStruct = $this->contentTypeUpdateParser->parse(
            $data[self::CONTENT_TYPE_UPDATE_STRUCT_KEY],
            $parsingDispatcher
        );

        $attributesAssignments = $this->populateAttributeAssignments($data);

        return new ProductTypeUpdateStruct(
            $contentTypeUpdateStruct,
            $attributesAssignments
        );
    }

    /**
     * @phpstan-param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[]
     */
    private function populateAttributeAssignments(array $data): array
    {
        $assignedAttributes = $data[self::ASSIGNED_ATTRIBUTES_KEY] ?? [];
        $attributesAssignments = [];

        foreach ($assignedAttributes as $assignedAttribute) {
            $attributeDefinition = $this->attributeDefinitionService->getAttributeDefinition(
                $assignedAttribute[self::IDENTIFIER_KEY]
            );

            $attributesAssignments[] = new AssignAttributeDefinitionStruct(
                $attributeDefinition,
                $assignedAttribute[self::IS_REQUIRED_KEY],
                $assignedAttribute[self::IS_DISCRIMINATOR_KEY]
            );
        }

        return $attributesAssignments;
    }
}
