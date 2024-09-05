<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\Attribute;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeList;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeType as RestAttributeType;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeTypeList as RestAttributeTypeList;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\AttributeGroupIdentifierCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\NameCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException as RestNotFoundException;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeType;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class AttributeController extends RestController
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private AttributeGroupServiceInterface $attributeGroupService;

    private AttributeTypeServiceInterface $attributeTypeService;

    private LocalAttributeDefinitionServiceInterface $localAttributeDefinitionService;

    public function __construct(
        AttributeDefinitionServiceInterface $attributeDefinitionService,
        AttributeGroupServiceInterface $attributeGroupService,
        AttributeTypeServiceInterface $attributeTypeService,
        LocalAttributeDefinitionServiceInterface $localAttributeDefinitionService
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->attributeGroupService = $attributeGroupService;
        $this->attributeTypeService = $attributeTypeService;
        $this->localAttributeDefinitionService = $localAttributeDefinitionService;
    }

    /**
     * @deprecated since 4.3. Use ibexa.product_catalog.rest.attributes.view route instead.
     */
    public function listAttributesAction(Request $request): Value
    {
        trigger_deprecation(
            'ibexa/product-catalog',
            '4.3',
            sprintf(
                '%s route is deprecated and will be removed in 5.0. Use %s route instead.',
                'ibexa.product_catalog.rest.attributes',
                'ibexa.product_catalog.rest.attributes.view'
            ),
        );

        $restAttributes = [];
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeQueryStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $attributeGroupQuery = new AttributeGroupQuery($input->getGroupNamePrefix());
        $groups = $this->attributeGroupService
            ->findAttributeGroups($attributeGroupQuery)
            ->getAttributeGroups();

        $attributeQuery = new AttributeDefinitionQuery(
            null,
            null,
            $input->getOffset(),
            $input->getLimit()
        );
        $attributeQuery->and(
            new AttributeGroupIdentifierCriterion(
                array_map(
                    static fn (AttributeGroupInterface $attributeGroup): string => $attributeGroup->getIdentifier(),
                    $groups,
                ),
            ),
            new NameCriterion($input->getNamePrefix(), 'STARTS_WITH'),
        );
        $attributes = $this->attributeDefinitionService->findAttributesDefinitions($attributeQuery);

        foreach ($attributes as $attribute) {
            $restAttributes[] = new Attribute($attribute);
        }

        return new AttributeList($restAttributes);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getAttributeAction(Request $request, string $identifier): Value
    {
        $languages = null;

        if (!empty($request->getContent())) {
            /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeLanguageStruct $input */
            $input = $this->inputDispatcher->parse(
                new Message(
                    ['Content-Type' => $request->headers->get('Content-Type')],
                    $request->getContent()
                )
            );

            $languages = $input->getLanguages();
        }

        $attribute = $this->attributeDefinitionService->getAttributeDefinition($identifier, $languages);

        return new Attribute($attribute);
    }

    public function listAttributeTypesAction(): Value
    {
        $restAttributeTypes = [];
        $attributeTypes = $this->attributeTypeService->getAttributeTypes();

        foreach ($attributeTypes as $attributeType) {
            $restAttributeTypes[] = new RestAttributeType($attributeType);
        }

        return new RestAttributeTypeList($restAttributeTypes);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getAttributeTypeAction(string $identifier): Value
    {
        $attributeType = $this->attributeTypeService->getAttributeType($identifier);

        return new RestAttributeType($attributeType);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function createAttributeAction(Request $request): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeCreateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $attributeCreateStruct = new AttributeDefinitionCreateStruct($input->getIdentifier());

        $attributeCreateStruct = $this->setAttributeDefinitionNames($attributeCreateStruct, $input->getNames());
        $attributeCreateStruct = $this->setAttributeDefinitionDescriptions($attributeCreateStruct, $input->getDescriptions());
        $attributeCreateStruct->setPosition($input->getPosition());

        $attributeTypeDefinition = new AttributeType($input->getType());
        $attributeCreateStruct->setType($attributeTypeDefinition);

        $attributeGroup = $this->attributeGroupService->getAttributeGroup($input->getGroup());
        $attributeCreateStruct->setGroup($attributeGroup);

        $attributeCreateStruct->setOptions($input->getOptions());

        $attribute = $this->localAttributeDefinitionService->createAttributeDefinition($attributeCreateStruct);

        return new Attribute($attribute);
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateAttributeAction(Request $request, string $identifier, string $groupIdentifier): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeUpdateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        try {
            $attribute = $this->attributeDefinitionService->getAttributeDefinition($identifier);
            $attributeGroup = $this->attributeGroupService->getAttributeGroup($groupIdentifier);
            $attributeUpdateStruct = new AttributeDefinitionUpdateStruct();

            $attributeUpdateStruct->setIdentifier($input->getIdentifier());
            $attributeUpdateStruct->setNames($input->getNames());
            $attributeUpdateStruct->setDescriptions($input->getDescriptions());
            $attributeUpdateStruct->setGroup($attributeGroup);
            $attributeUpdateStruct->setPosition($input->getPosition());
            $attributeUpdateStruct->setOptions($input->getOptions());

            $attribute = $this->localAttributeDefinitionService->updateAttributeDefinition(
                $attribute,
                $attributeUpdateStruct
            );
        } catch (NotFoundException $e) {
            throw new RestNotFoundException($e->getMessage(), 0, $e);
        }

        return new Attribute($attribute);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteAttributeAction(string $identifier): Value
    {
        $attribute = $this->attributeDefinitionService->getAttributeDefinition($identifier);
        $this->localAttributeDefinitionService->deleteAttributeDefinition($attribute);

        return new NoContent();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteAttributeTranslationAction(string $identifier, string $languageCode): Value
    {
        $attribute = $this->localAttributeDefinitionService->getAttributeDefinition($identifier);
        $this->localAttributeDefinitionService->deleteAttributeDefinitionTranslation($attribute, $languageCode);

        return new NoContent();
    }

    /**
     * @param array<string, string> $names
     */
    private function setAttributeDefinitionNames(
        AttributeDefinitionCreateStruct $attributeStruct,
        array $names
    ): AttributeDefinitionCreateStruct {
        foreach ($names as $language => $name) {
            $attributeStruct->setName($language, $name);
        }

        return $attributeStruct;
    }

    /**
     * @param array<string, string> $descriptions
     */
    private function setAttributeDefinitionDescriptions(
        AttributeDefinitionCreateStruct $attributeStruct,
        array $descriptions
    ): AttributeDefinitionCreateStruct {
        foreach ($descriptions as $language => $description) {
            $attributeStruct->setDescription($language, $description);
        }

        return $attributeStruct;
    }
}
