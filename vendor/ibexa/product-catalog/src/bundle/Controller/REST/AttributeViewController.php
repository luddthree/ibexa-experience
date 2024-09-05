<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeView;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class AttributeViewController extends RestController
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(AttributeDefinitionServiceInterface $attributeDefinitionService)
    {
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    public function createView(Request $request): Value
    {
        $viewInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        return new AttributeView(
            $viewInput->identifier,
            $this->attributeDefinitionService->findAttributesDefinitions(
                $viewInput->query
            )
        );
    }
}
