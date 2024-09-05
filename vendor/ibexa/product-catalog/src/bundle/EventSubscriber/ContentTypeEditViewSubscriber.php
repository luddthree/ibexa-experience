<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\AdminUi\View\ContentTypeEditView;
use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\ToolbarFactoryInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentTypeEditViewSubscriber implements EventSubscriberInterface, TranslationContainerInterface
{
    private const PHYSICAL_PRODUCT_TYPE_ID = 'physical_product_type.edit.action_name';
    private const VIRTUAL_PRODUCT_TYPE_ID = 'virtual_product_type.edit.action_name';

    private ProductTypeServiceInterface $productTypeService;

    private ToolbarFactoryInterface $toolbarFactory;

    public function __construct(
        ProductTypeServiceInterface $productTypeService,
        ToolbarFactoryInterface $toolbarFactory
    ) {
        $this->productTypeService = $productTypeService;
        $this->toolbarFactory = $toolbarFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => 'onPreContentView',
        ];
    }

    public function onPreContentView(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();
        if (!$view instanceof ContentTypeEditView) {
            return;
        }

        $contentType = $view->getContentTypeDraft();
        if (!$this->hasProductSpecification($contentType)) {
            return;
        }

        try {
            $productType = $this->productTypeService->getProductType($contentType->identifier);
        } catch (NotFoundException $e) {
            $productType = null;
        }

        $fieldDefinition = $contentType->getFirstFieldDefinitionOfType(
            ProductSpecificationType::FIELD_TYPE_IDENTIFIER
        );

        $isVirtual = $fieldDefinition->getFieldSettings()['is_virtual'];
        $view->setTemplateIdentifier('@ibexadesign/product_catalog/product_type/edit.html.twig');
        $view->addParameters([
            'attributes_definitions_toolbar' => $this->toolbarFactory->create($productType),
            'action_name' => $this->getActionName($isVirtual),
        ]);
    }

    private function hasProductSpecification(ContentType $contentType): bool
    {
        return $contentType->hasFieldDefinitionOfType(ProductSpecificationType::FIELD_TYPE_IDENTIFIER);
    }

    private function getActionName(bool $isVirtual): string
    {
        return $isVirtual ? self::VIRTUAL_PRODUCT_TYPE_ID : self::PHYSICAL_PRODUCT_TYPE_ID;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                self::PHYSICAL_PRODUCT_TYPE_ID,
                'ibexa_product_catalog'
            )->setDesc('Editing Physical Product type'),
            Message::create(
                self::VIRTUAL_PRODUCT_TYPE_ID,
                'ibexa_product_catalog'
            )->setDesc('Editing Virtual Product type'),
        ];
    }
}
