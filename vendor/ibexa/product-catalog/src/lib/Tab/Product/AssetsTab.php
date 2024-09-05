<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Product;

use Ibexa\Bundle\ProductCatalog\Form\Type\AssetBulkDeleteType;
use Ibexa\Bundle\ProductCatalog\Form\Type\AssetCreateType;
use Ibexa\Bundle\ProductCatalog\Form\Type\AssetTagType;
use Ibexa\Bundle\ProductCatalog\UI\AssetGroup\AssetGroupCollectionFactoryInterface;
use Ibexa\ContentForms\ConfigResolver\MaxUploadSize;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class AssetsTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-product-assets';

    private AssetServiceInterface $assetService;

    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    private AssetGroupCollectionFactoryInterface $assetGroupCollectionFactory;

    private MaxUploadSize $maxUploadSize;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        AssetServiceInterface $assetService,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        AssetGroupCollectionFactoryInterface $assetGroupCollectionFactory,
        MaxUploadSize $maxUploadSize
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->assetService = $assetService;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->assetGroupCollectionFactory = $assetGroupCollectionFactory;
        $this->maxUploadSize = $maxUploadSize;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/product/tab/assets.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface $product */
        $product = $contextParameters['product'];

        return [
            'product' => $product,
            'assets' => $this->assetGroupCollectionFactory->createFromAssetCollection(
                $this->assetService->findAssets($product)
            ),
            'create_asset_form' => $this->createAssetCreateForm($product)->createView(),
            'delete_asset_form' => $this->createAssetDeleteForm($product)->createView(),
            'tag_asset_form' => $this->createTagAssetForm($product)->createView(),
            'max_upload_size' => $this->maxUploadSize->get(MaxUploadSize::MEGABYTES),
        ];
    }

    private function createAssetCreateForm(ProductInterface $product): FormInterface
    {
        return $this->formFactory->create(
            AssetCreateType::class,
            null,
            [
                'action' => $this->urlGenerator->generate(
                    'ibexa.product_catalog.product.asset.create',
                    [
                        'productCode' => $product->getCode(),
                    ]
                ),
            ]
        );
    }

    private function createAssetDeleteForm(ProductInterface $product): FormInterface
    {
        return $this->formFactory->create(
            AssetBulkDeleteType::class,
            null,
            [
                'action' => $this->urlGenerator->generate(
                    'ibexa.product_catalog.product.asset.delete',
                    [
                        'productCode' => $product->getCode(),
                    ]
                ),
                'product' => $product,
            ]
        );
    }

    private function createTagAssetForm(ProductInterface $product): FormInterface
    {
        $actionUrl = $this->urlGenerator->generate(
            'ibexa.product_catalog.product.asset.tag',
            [
                'productCode' => $product->getCode(),
            ]
        );

        return $this->formFactory->create(
            AssetTagType::class,
            null,
            [
                'action' => $actionUrl,
                'method' => Request::METHOD_POST,
                'product' => $product,
            ]
        );
    }

    public function getOrder(): int
    {
        return 250;
    }

    public function getIdentifier(): string
    {
        return 'assets';
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Desc("Assets") */ 'tab.name.assets', [], 'ibexa_product_catalog');
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['product'] instanceof ContentAwareProductInterface;
    }
}
