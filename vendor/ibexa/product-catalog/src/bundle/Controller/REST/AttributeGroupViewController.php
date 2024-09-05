<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupView;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class AttributeGroupViewController extends RestController
{
    private AttributeGroupServiceInterface $attributeGroupService;

    public function __construct(AttributeGroupServiceInterface $attributeGroupService)
    {
        $this->attributeGroupService = $attributeGroupService;
    }

    public function createView(Request $request): Value
    {
        $viewInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        return new AttributeGroupView(
            $viewInput->identifier,
            $this->attributeGroupService->findAttributeGroups(
                $viewInput->query
            )
        );
    }
}
