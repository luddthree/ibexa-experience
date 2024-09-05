<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroup;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupList;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException as RestNotFoundException;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class AttributeGroupController extends RestController
{
    private AttributeGroupServiceInterface $attributeGroupService;

    private LocalAttributeGroupServiceInterface $localAttributeGroupService;

    private LanguageService $languageService;

    public function __construct(
        AttributeGroupServiceInterface $attributeGroupService,
        LocalAttributeGroupServiceInterface $localAttributeGroupService,
        LanguageService $languageService
    ) {
        $this->attributeGroupService = $attributeGroupService;
        $this->localAttributeGroupService = $localAttributeGroupService;
        $this->languageService = $languageService;
    }

    /**
     * @deprecated since 4.3. Use ibexa.product_catalog.rest.attribute_groups.view route instead.
     */
    public function listAttributeGroupsAction(Request $request): Value
    {
        trigger_deprecation(
            'ibexa/product-catalog',
            '4.3',
            sprintf(
                '%s route is deprecated and will be removed in 5.0. Use %s route instead.',
                'ibexa.product_catalog.rest.attribute_groups',
                'ibexa.product_catalog.rest.attribute_groups.view'
            ),
        );

        $restAttributeGroups = [];
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupQueryStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $attributeGroupQuery = new AttributeGroupQuery(
            $input->getNamePrefix(),
            $input->getOffset(),
            $input->getLimit()
        );

        $attributeGroups = $this->attributeGroupService->findAttributeGroups($attributeGroupQuery);

        foreach ($attributeGroups as $attributeGroup) {
            $restAttributeGroups[] = new AttributeGroup($attributeGroup);
        }

        return new AttributeGroupList($restAttributeGroups);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getAttributeGroupAction(Request $request, string $identifier): Value
    {
        $languages = null;

        if (!empty($request->getContent())) {
            /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupLanguageStruct $input */
            $input = $this->inputDispatcher->parse(
                new Message(
                    ['Content-Type' => $request->headers->get('Content-Type')],
                    $request->getContent()
                )
            );

            $languages = $this->languageService->loadLanguageListByCode($input->getLanguages());
        }

        $attributeGroup = $this->attributeGroupService->getAttributeGroup($identifier, $languages);

        return new AttributeGroup($attributeGroup);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createAttributeGroupAction(Request $request): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupCreateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $attributeGroupCreateStruct = new AttributeGroupCreateStruct(
            $input->getIdentifier(),
            $input->getNames(),
            $input->getPosition(),
        );

        $attributeGroup = $this->localAttributeGroupService->createAttributeGroup($attributeGroupCreateStruct);

        return new AttributeGroup($attributeGroup);
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateAttributeGroupAction(Request $request, string $identifier): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupUpdateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        try {
            $languages = $this->languageService->loadLanguageListByCode(
                $input->getLanguages()
            );
            $attributeGroup = $this->attributeGroupService->getAttributeGroup(
                $identifier,
                $languages
            );

            $attributeGroupUpdateStruct = new AttributeGroupUpdateStruct(
                $input->getIdentifier(),
                $input->getNames(),
                $input->getPosition()
            );

            $attributeGroup = $this->localAttributeGroupService->updateAttributeGroup(
                $attributeGroup,
                $attributeGroupUpdateStruct
            );
        } catch (NotFoundException $e) {
            throw new RestNotFoundException($e->getMessage(), 0, $e);
        }

        return new AttributeGroup($attributeGroup);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteAttributeGroupAction(string $identifier): Value
    {
        $attributeGroup = $this->attributeGroupService->getAttributeGroup($identifier);
        $this->localAttributeGroupService->deleteAttributeGroup($attributeGroup);

        return new NoContent();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteAttributeGroupTranslationAction(string $identifier, string $languageCode): Value
    {
        $attributeGroup = $this->attributeGroupService->getAttributeGroup($identifier);
        $this->localAttributeGroupService->deleteAttributeGroupTranslation($attributeGroup, $languageCode);

        return new NoContent();
    }
}
