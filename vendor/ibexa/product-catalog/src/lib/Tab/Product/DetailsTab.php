<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Product;

use Ibexa\AdminUi\Util\FieldDefinitionGroupsUtil;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductLanguageSwitchType;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Ibexa\ProductCatalog\Tab\AbstractDetailsTab;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class DetailsTab extends AbstractDetailsTab implements ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-product-details';

    private LocalProductServiceInterface $localProductService;

    private FormFactoryInterface $formFactory;

    private RequestStack $requestStack;

    private FieldDefinitionGroupsUtil $fieldDefinitionGroupsUtil;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        LocalProductServiceInterface $localProductService,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        FieldDefinitionGroupsUtil $fieldDefinitionGroupsUtil
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->localProductService = $localProductService;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->fieldDefinitionGroupsUtil = $fieldDefinitionGroupsUtil;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/product/tab/details.html.twig';
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['product'] instanceof Product;
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $product */
        $product = $contextParameters['product'];

        $languageSwitchForm = $this->createLanguageSwitchForm($product);
        $languageSwitchForm->handleRequest($this->requestStack->getCurrentRequest());
        if ($languageSwitchForm->isSubmitted() && $languageSwitchForm->isValid()) {
            /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\ProductLanguageSwitchData $data */
            $data = $languageSwitchForm->getData();

            // Reload product with forced languages settings
            $product = $this->localProductService->getProduct(
                $product->getCode(),
                new LanguageSettings([$data->getLanguage()->languageCode], true)
            );
        }

        $viewParameters = [
            'field_definitions_by_group' => $this->getFieldDefinitionByGroup($product),
            'language_switch_form' => $languageSwitchForm->createView(),
            'product' => $product,
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getFieldDefinitionByGroup(ContentAwareProductInterface $product): array
    {
        return $this->fieldDefinitionGroupsUtil->groupFieldDefinitions(
            $product->getContent()->getContentType()->getFieldDefinitions()
        );
    }

    private function createLanguageSwitchForm(ContentAwareProductInterface $product): FormInterface
    {
        return $this->formFactory->createNamed(
            '',
            ProductLanguageSwitchType::class,
            null,
            [
                'product' => $product,
            ]
        );
    }
}
