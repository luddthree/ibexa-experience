<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\AdminUi\View\ContentTypeEditView;
use Ibexa\Bundle\ProductCatalog\EventSubscriber\ContentTypeEditViewSubscriber;
use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\ToolbarFactoryInterface;
use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\Toolbar;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

final class ContentTypeEditViewSubscriberTest extends TestCase
{
    private const EXAMPLE_PRODUCT_TYPE = 'dress';

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                MVCEvents::PRE_CONTENT_VIEW => 'onPreContentView',
            ],
            ContentTypeEditViewSubscriber::getSubscribedEvents()
        );
    }

    public function testOnPreContentViewSkipNonContentTypeEditViews(): void
    {
        $view = $this->createMock(View::class);
        $view->expects(self::never())->method('setTemplateIdentifier');
        $view->expects(self::never())->method('addParameters');

        $subscriber = new ContentTypeEditViewSubscriber(
            $this->createMock(ProductTypeServiceInterface::class),
            $this->createMock(ToolbarFactoryInterface::class)
        );
        $subscriber->onPreContentView(new PreContentViewEvent($view));
    }

    public function testOnPreContentViewSkipContentTypeEditViewWithoutProductSpecification(): void
    {
        $view = new ContentTypeEditView(
            'original.html.twig',
            $this->createMock(ContentTypeGroup::class),
            $this->createContentTypeDraft(false),
            $this->createMock(Language::class),
            $this->createMock(FormInterface::class)
        );

        $subscriber = new ContentTypeEditViewSubscriber(
            $this->createMock(ProductTypeServiceInterface::class),
            $this->createMock(ToolbarFactoryInterface::class)
        );
        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertEquals('original.html.twig', $view->getTemplateIdentifier());
        self::assertArrayNotHasKey('attributes_definitions_toolbar', $view->getParameters());
    }

    public function testOnPreContentViewHandleContentTypeEditViewWithProductSpecification(): void
    {
        $exceptedToolbar = new Toolbar([]);

        $productType = $this->createMock(ProductTypeInterface::class);

        $productTypeService = $this->createMock(ProductTypeServiceInterface::class);
        $productTypeService->method('getProductType')->with(self::EXAMPLE_PRODUCT_TYPE)->willReturn($productType);

        $toolbarFactory = $this->createMock(ToolbarFactoryInterface::class);
        $toolbarFactory->method('create')->willReturn($exceptedToolbar);

        $view = new ContentTypeEditView(
            'original.html.twig',
            $this->createMock(ContentTypeGroup::class),
            $this->createContentTypeDraft(
                true,
                self::EXAMPLE_PRODUCT_TYPE,
                $this->createProductSpecificationFieldDefinition()
            ),
            $this->createMock(Language::class),
            $this->createMock(FormInterface::class)
        );

        $subscriber = new ContentTypeEditViewSubscriber($productTypeService, $toolbarFactory);
        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertEquals(
            '@ibexadesign/product_catalog/product_type/edit.html.twig',
            $view->getTemplateIdentifier()
        );

        self::assertArrayHasKey('attributes_definitions_toolbar', $view->getParameters());

        self::assertEquals(
            $exceptedToolbar,
            $view->getParameter('attributes_definitions_toolbar')
        );
    }

    private function createContentTypeDraft(
        bool $withProductSpecification,
        ?string $identifier = null,
        ?FieldDefinition $fieldDefinition = null
    ): ContentTypeDraft {
        $contentTypeDraft = $this->createMock(ContentTypeDraft::class);
        $contentTypeDraft
            ->method('hasFieldDefinitionOfType')
            ->with(ProductSpecificationType::FIELD_TYPE_IDENTIFIER)
            ->willReturn($withProductSpecification);

        if (
            $withProductSpecification
            && null !== $fieldDefinition
        ) {
            $contentTypeDraft
                ->method('getFirstFieldDefinitionOfType')
                ->with(ProductSpecificationType::FIELD_TYPE_IDENTIFIER)
                ->willReturn($fieldDefinition);
        }

        if ($identifier !== null) {
            $contentTypeDraft->method('__get')->with('identifier')->willReturn($identifier);
        }

        return $contentTypeDraft;
    }

    /**
     * @param array<mixed> $attributes
     * @param array<mixed> $regions
     */
    private function createProductSpecificationFieldDefinition(
        array $attributes = [],
        array $regions = [],
        bool $isVirtual = false
    ): FieldDefinition {
        $fieldDefinition = $this->createMock(FieldDefinition::class);
        $fieldDefinition
            ->expects(self::once())
            ->method('getFieldSettings')
            ->willReturn(
                [
                    'attributes_definitions' => $attributes,
                    'regions' => $regions,
                    'is_virtual' => $isVirtual,
                ]
            );

        return $fieldDefinition;
    }
}
