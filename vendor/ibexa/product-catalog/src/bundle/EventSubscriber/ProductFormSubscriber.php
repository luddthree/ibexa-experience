<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Exception;
use Ibexa\Bundle\ProductCatalog\Form\Data\AbstractProductData;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\Event\ProductFormEvents;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value as ProductSpecificationValue;
use Ibexa\ProductCatalog\Tab\Product\TranslationsTab;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductFormSubscriber implements EventSubscriberInterface
{
    private Repository $repository;

    private LocalProductServiceInterface $localProductService;

    private UrlGeneratorInterface $urlGenerator;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        Repository $repository,
        LocalProductServiceInterface $localProductService,
        UrlGeneratorInterface $urlGenerator,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->repository = $repository;
        $this->localProductService = $localProductService;
        $this->urlGenerator = $urlGenerator;
        $this->notificationHandler = $notificationHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductFormEvents::PRODUCT_CREATE => ['onProductCreate', -255],
            ProductFormEvents::PRODUCT_UPDATE => ['onProductUpdate', -255],
        ];
    }

    public function onProductCreate(FormActionEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!$data instanceof ProductCreateData) {
            return;
        }

        $languageCode = $form->getConfig()->getOption('languageCode');

        $createStruct = $this->localProductService->newProductCreateStruct(
            $data->getProductType(),
            $languageCode
        );

        $this->mapDataToStruct($createStruct, $data, $languageCode);

        $this->repository->beginTransaction();
        try {
            $product = $this->localProductService->createProduct($createStruct);

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $this->notificationHandler->success(
            /** @Desc("Product '%name%' created.") */
            'product.create.success',
            ['%name%' => $product->getName()],
            'ibexa_product_catalog'
        );

        $event->setResponse($this->createRedirectToProductView($product));
    }

    public function onProductUpdate(FormActionEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!$data instanceof ProductUpdateData) {
            return;
        }

        $languageCode = $form->getConfig()->getOption('languageCode');
        $mainLanguageCode = $form->getConfig()->getOption('mainLanguageCode');
        $isTranslation = $languageCode !== $mainLanguageCode;

        $updateStruct = $this->localProductService->newProductUpdateStruct($data->getProduct());
        $updateStruct->getContentUpdateStruct()->initialLanguageCode = $languageCode;
        $this->mapDataToStruct($updateStruct, $data, $languageCode, $isTranslation);

        $this->repository->beginTransaction();
        try {
            $product = $this->localProductService->updateProduct($updateStruct);

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $this->notificationHandler->success(
            /** @Desc("Product '%name%' updated.") */
            'product.update.success',
            ['%name%' => $product->getName()],
            'ibexa_product_catalog'
        );

        $response = $this->createRedirectToProductView(
            $product,
            $isTranslation ? TranslationsTab::URI_FRAGMENT : null
        );

        $event->setResponse($response);
    }

    private function createRedirectToProductView(ProductInterface $product, ?string $fragment = null): RedirectResponse
    {
        $parameters = [
            'productCode' => $product->getCode(),
            '_fragment' => $fragment,
        ];

        if ($product instanceof ContentAwareProductInterface) {
            $parameters['updatedContentId'] = $product->getContent()->id;
        }

        $redirectionUrl = $this->urlGenerator->generate(
            'ibexa.product_catalog.product.view',
            $parameters,
        );

        return new RedirectResponse($redirectionUrl);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct|\Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct  $struct
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\AbstractProductData $data
     */
    private function mapDataToStruct(
        $struct,
        AbstractProductData $data,
        string $languageCode,
        bool $isTranslation = false
    ): void {
        $struct->setCode($data->getCode());

        foreach ($data->getFieldsData() as $fieldDefIdentifier => $fieldData) {
            if ($fieldData->value instanceof ProductSpecificationValue) {
                continue;
            }

            if ($isTranslation && !$fieldData->fieldDefinition->isTranslatable) {
                continue;
            }

            $struct->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        if (!$isTranslation) {
            foreach ($data->getAttributes() as $attributeData) {
                $struct->setAttribute(
                    $attributeData->getAttributeDefinition()->getIdentifier(),
                    $attributeData->getValue()
                );
            }
        }
    }
}
