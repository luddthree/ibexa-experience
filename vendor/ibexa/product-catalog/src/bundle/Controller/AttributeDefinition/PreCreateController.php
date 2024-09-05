<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeDefinition;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionPreCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionPreCreateType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class PreCreateController extends Controller
{
    public function executeAction(Request $request): RedirectResponse
    {
        $form = $this->createPreCreateForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToCreateForm($form->getData());
        }

        return $this->redirectToRoute('ibexa.product_catalog.attribute_definition.list');
    }

    private function redirectToCreateForm(AttributeDefinitionPreCreateData $data): RedirectResponse
    {
        assert($data->getAttributeType() !== null);
        assert($data->getLanguage() !== null);

        $parameters = [
            'attributeType' => $data->getAttributeType()->getIdentifier(),
            'languageCode' => $data->getLanguage()->languageCode,
        ];

        if ($data->getAttributeGroup()) {
            $parameters['attributeGroupIdentifier'] = $data->getAttributeGroup()->getIdentifier();
        }

        return $this->redirectToRoute('ibexa.product_catalog.attribute_definition.create', $parameters);
    }

    private function createPreCreateForm(): FormInterface
    {
        return $this->createForm(AttributeDefinitionPreCreateType::class, null, [
            'action' => $this->generateUrl('ibexa.product_catalog.attribute_definition.pre_create'),
            'method' => Request::METHOD_POST,
        ]);
    }
}
