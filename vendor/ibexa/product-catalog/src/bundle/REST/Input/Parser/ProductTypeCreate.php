<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;
use Ibexa\Rest\Server\Input\Parser\ContentTypeCreate;

final class ProductTypeCreate extends BaseParser
{
    private const CONTENT_TYPE_CREATE_STRUCT_KEY = 'ContentTypeCreateStruct';
    private const MAIN_LANGUAGE_CODE_KEY = 'main_language_code';
    private const ASSIGNED_ATTRIBUTES_KEY = 'assigned_attributes';
    private const IDENTIFIER_KEY = 'identifier';
    private const IS_REQUIRED_KEY = 'is_required';
    private const IS_DISCRIMINATOR_KEY = 'is_discriminator';

    private const REQUIRED_OBJECT_KEYS = [
        self::CONTENT_TYPE_CREATE_STRUCT_KEY,
        self::MAIN_LANGUAGE_CODE_KEY,
    ];

    private const ALLOWED_OBJECT_KEYS = [
        self::CONTENT_TYPE_CREATE_STRUCT_KEY,
        self::MAIN_LANGUAGE_CODE_KEY,
        self::ASSIGNED_ATTRIBUTES_KEY,
    ];

    private ContentTypeCreate $contentTypeCreateParser;

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(
        ContentTypeCreate $contentTypeCreateParser,
        AttributeDefinitionServiceInterface $attributeDefinitionService
    ) {
        $this->contentTypeCreateParser = $contentTypeCreateParser;
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductTypeCreateStruct
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

        $assignedAttributes = $data[self::ASSIGNED_ATTRIBUTES_KEY] ?? [];
        if (!is_array($assignedAttributes)) {
            throw new Parser('The "assigned_attributes" parameter must be an array.');
        }

        $contentTypeCreateStruct = $this->contentTypeCreateParser->parse(
            $data[self::CONTENT_TYPE_CREATE_STRUCT_KEY],
            $parsingDispatcher
        );

        $attributesAssignments = $this->populateAttributeAssignments($assignedAttributes);

        return new ProductTypeCreateStruct(
            $contentTypeCreateStruct,
            $data[self::MAIN_LANGUAGE_CODE_KEY],
            $attributesAssignments
        );
    }

    /**
     * @phpstan-param array<mixed> $assignedAttributes
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[]
     */
    private function populateAttributeAssignments(array $assignedAttributes): array
    {
        $attributesAssignments = [];

        foreach ($assignedAttributes as $assignedAttribute) {
            $attributeDefinition = $this->attributeDefinitionService->getAttributeDefinition(
                $assignedAttribute[self::IDENTIFIER_KEY]
            );

            $attributesAssignments[] = new AssignAttributeDefinitionStruct(
                $attributeDefinition,
                (bool)$assignedAttribute[self::IS_REQUIRED_KEY],
                (bool)$assignedAttribute[self::IS_DISCRIMINATOR_KEY]
            );
        }

        return $attributesAssignments;
    }
}
