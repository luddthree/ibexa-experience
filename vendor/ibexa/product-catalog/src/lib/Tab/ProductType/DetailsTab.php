<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\ProductType;

use Ibexa\AdminUi\Util\FieldDefinitionGroupsUtil;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeLanguageSwitchType;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\ProductCatalog\Tab\AbstractDetailsTab;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class DetailsTab extends AbstractDetailsTab implements ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-product-type-details';

    private FormFactoryInterface $formFactory;

    private ProductTypeServiceInterface $productTypeService;

    private RequestStack $requestStack;

    private FieldDefinitionGroupsUtil $fieldDefinitionGroupsUtil;

    private RegionServiceInterface $regionService;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        ProductTypeServiceInterface $productTypeService,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        FieldDefinitionGroupsUtil $fieldDefinitionGroupsUtil,
        RegionServiceInterface $regionService
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->productTypeService = $productTypeService;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->fieldDefinitionGroupsUtil = $fieldDefinitionGroupsUtil;
        $this->regionService = $regionService;
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['product_type'] instanceof ContentTypeAwareProductTypeInterface;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/product_type/tab/details.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        $productType = $contextParameters['product_type'];

        $languageSwitchForm = $this->createLanguageSwitchForm($productType);
        $languageSwitchForm->handleRequest($this->requestStack->getCurrentRequest());
        if ($languageSwitchForm->isSubmitted() && $languageSwitchForm->isValid()) {
            /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\ProductTypeLanguageSwitchData $data */
            $data = $languageSwitchForm->getData();

            // Reload product type with forced languages settings
            $productType = $this->productTypeService->getProductType(
                $productType->getIdentifier(),
                new LanguageSettings([$data->getLanguage()->languageCode])
            );
        }

        return [
            'field_definitions_by_group' => $this->getFieldDefinitionByGroup($productType),
            'language_switch_form' => $languageSwitchForm->createView(),
            'product_type' => $productType,
            'vat_categories_by_region' => $this->getVatCategoriesByRegion($productType),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getFieldDefinitionByGroup(ContentTypeAwareProductTypeInterface $productType): array
    {
        return $this->fieldDefinitionGroupsUtil->groupFieldDefinitions(
            $productType->getContentType()->getFieldDefinitions()
        );
    }

    private function createLanguageSwitchForm(ContentTypeAwareProductTypeInterface $productType): FormInterface
    {
        return $this->formFactory->createNamed(
            '',
            ProductTypeLanguageSwitchType::class,
            null,
            [
                'product_type' => $productType,
            ]
        );
    }

    /**
     * @return array<string, \Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface|null>
     */
    private function getVatCategoriesByRegion(ContentTypeAwareProductTypeInterface $productType): array
    {
        $vatCategoriesByRegion = [];
        foreach ($this->regionService->findRegions() as $region) {
            $vatCategory = $productType->getVatCategory($region);
            $vatCategoriesByRegion[$region->getIdentifier()] = $vatCategory;
        }

        return $vatCategoriesByRegion;
    }
}
