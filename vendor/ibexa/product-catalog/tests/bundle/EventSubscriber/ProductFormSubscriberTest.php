<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\ProductFormSubscriber;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductUpdateData;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductFormSubscriberTest extends TestCase
{
    public const EXAMPLE_LANGUAGE_CODE = 'pol-PL';
    public const EXAMPLE_MAIN_LANGUAGE_CODE = 'eng-GB';
    public const EXAMPLE_PRODUCT_CODE = '0001';

    /** @var \Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private LocalProductServiceInterface $productService;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private UrlGeneratorInterface $urlGenerator;

    /** @var \Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private ProductFormSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->productService = $this->createMock(LocalProductServiceInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->notificationHandler = $this->createMock(TranslatableNotificationHandlerInterface::class);

        $this->subscriber = new ProductFormSubscriber(
            $this->createMock(Repository::class),
            $this->productService,
            $this->urlGenerator,
            $this->notificationHandler
        );
    }

    public function testOnProductCreate(): void
    {
        $form = $this->createFormWithLanguageCode(self::EXAMPLE_LANGUAGE_CODE);

        $data = new ProductCreateData($this->createMock(ProductTypeInterface::class));
        $data->setCode(self::EXAMPLE_PRODUCT_CODE);
        $data->addFieldData($this->createFieldData('foo', 'foo'));
        $data->addFieldData($this->createFieldData('bar', 'bar'));
        $data->addFieldData($this->createFieldData('baz', 'baz'));
        $data->setAttributes([
            $this->createAttributeData('width', 10),
            $this->createAttributeData('height', 15),
            $this->createAttributeData('length', 20),
        ]);

        $createStructAssertion = function (ProductCreateStruct $createStruct): bool {
            self::assertEquals(self::EXAMPLE_PRODUCT_CODE, $createStruct->getCode());

            self::assertEquals([
                $this->createField('foo', 'foo', self::EXAMPLE_LANGUAGE_CODE),
                $this->createField('bar', 'bar', self::EXAMPLE_LANGUAGE_CODE),
                $this->createField('baz', 'baz', self::EXAMPLE_LANGUAGE_CODE),
            ], $createStruct->getContentCreateStruct()->fields);

            self::assertEquals([
                'width' => 10,
                'height' => 15,
                'length' => 20,
            ], $createStruct->getAttributes());

            return true;
        };

        $this->urlGenerator->method('generate')->with('ibexa.product_catalog.product.view')->willReturn('/product-view');

        $this->productService
            ->expects(self::once())
            ->method('newProductCreateStruct')
            ->willReturn(new ProductCreateStruct(
                $this->createMock(ProductTypeInterface::class),
                new ContentCreateStruct()
            ));

        $this->productService
            ->expects(self::once())
            ->method('createProduct')
            ->with(self::callback($createStructAssertion));

        $this->notificationHandler
            ->expects(self::once())
            ->method('success')
            ->with('product.create.success');

        $event = new FormActionEvent($form, $data, 'create');

        $this->subscriber->onProductCreate($event);

        $this->assertRedirectionToProductView($event);
    }

    public function testOnProductUpdateWithMainTranslation(): void
    {
        $form = $this->createFormWithLanguageCode(self::EXAMPLE_LANGUAGE_CODE);

        $product = $this->createMock(ContentAwareProductInterface::class);

        $data = new ProductUpdateData($product);
        $data->setCode(self::EXAMPLE_PRODUCT_CODE);
        $data->addFieldData($this->createFieldData('foo', 'foo'));
        $data->addFieldData($this->createFieldData('bar', 'bar'));
        $data->addFieldData($this->createFieldData('baz', 'baz'));
        $data->setAttributes([
            $this->createAttributeData('width', 10),
            $this->createAttributeData('height', 15),
            $this->createAttributeData('length', 20),
        ]);

        $updateStructAssertion = function (ProductUpdateStruct $productUpdateStruct): bool {
            self::assertEquals(self::EXAMPLE_PRODUCT_CODE, $productUpdateStruct->getCode());

            self::assertEquals([
                $this->createField('foo', 'foo', self::EXAMPLE_LANGUAGE_CODE),
                $this->createField('bar', 'bar', self::EXAMPLE_LANGUAGE_CODE),
                $this->createField('baz', 'baz', self::EXAMPLE_LANGUAGE_CODE),
            ], $productUpdateStruct->getContentUpdateStruct()->fields);

            self::assertEquals([
                'width' => 10,
                'height' => 15,
                'length' => 20,
            ], $productUpdateStruct->getAttributes());

            return true;
        };

        $this->urlGenerator->method('generate')->with('ibexa.product_catalog.product.view')->willReturn('/product-view');

        $this->productService
            ->expects(self::once())
            ->method('newProductUpdateStruct')
            ->with($product)
            ->willReturn(new ProductUpdateStruct($product, new ContentUpdateStruct()));

        $this->productService
            ->expects(self::once())
            ->method('updateProduct')
            ->with(self::callback($updateStructAssertion));

        $this->notificationHandler
            ->expects(self::once())
            ->method('success')
            ->with('product.update.success');

        $event = new FormActionEvent($form, $data, 'update');

        $this->subscriber->onProductUpdate($event);

        $this->assertRedirectionToProductView($event);
    }

    public function testOnProductUpdateWithSecondaryTranslation(): void
    {
        $form = $this->createFormWithLanguageCode(
            self::EXAMPLE_LANGUAGE_CODE,
            self::EXAMPLE_MAIN_LANGUAGE_CODE
        );

        $product = $this->createMock(ContentAwareProductInterface::class);

        $data = new ProductUpdateData($product);
        $data->setCode(self::EXAMPLE_PRODUCT_CODE);
        $data->addFieldData($this->createFieldData('foo', 'foo'));
        $data->addFieldData($this->createFieldData('bar', 'bar'));
        $data->addFieldData($this->createFieldData('baz', 'baz', false));
        $data->setAttributes([
            $this->createAttributeData('width', 10),
            $this->createAttributeData('height', 15),
            $this->createAttributeData('length', 20),
        ]);

        $updateStructAssertion = function (ProductUpdateStruct $productUpdateStruct): bool {
            self::assertEquals(self::EXAMPLE_PRODUCT_CODE, $productUpdateStruct->getCode());
            self::assertEquals([
                $this->createField('foo', 'foo', self::EXAMPLE_LANGUAGE_CODE),
                $this->createField('bar', 'bar', self::EXAMPLE_LANGUAGE_CODE),
            ], $productUpdateStruct->getContentUpdateStruct()->fields);
            self::assertEmpty($productUpdateStruct->getAttributes());

            return true;
        };

        $this->urlGenerator
            ->method('generate')
            ->with(self::equalTo('ibexa.product_catalog.product.view'))
            ->willReturn('/product-view');

        $this->productService
            ->expects(self::once())
            ->method('newProductUpdateStruct')
            ->with($product)
            ->willReturn(new ProductUpdateStruct($product, new ContentUpdateStruct()));

        $this->productService
            ->expects(self::once())
            ->method('updateProduct')
            ->with(self::callback($updateStructAssertion));

        $this->notificationHandler
            ->expects(self::once())
            ->method('success')
            ->with('product.update.success');

        $event = new FormActionEvent($form, $data, 'update');

        $this->subscriber->onProductUpdate($event);

        $this->assertRedirectionToProductView($event);
    }

    private function assertRedirectionToProductView(FormActionEvent $event): void
    {
        self::assertInstanceOf(RedirectResponse::class, $event->getResponse());
        self::assertEquals('/product-view', $event->getResponse()->getTargetUrl());
    }

    /**
     * @param mixed $value
     */
    private function createField(string $fieldDefIdentifier, $value, string $languageCode): Field
    {
        return new Field([
            'fieldDefIdentifier' => $fieldDefIdentifier,
            'value' => $value,
            'languageCode' => $languageCode,
        ]);
    }

    /**
     * @param mixed $value
     */
    private function createAttributeData(string $attributeIdentifier, $value): AttributeData
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $attributeDefinition->method('getIdentifier')->willReturn($attributeIdentifier);

        return new AttributeData($attributeDefinition, $value);
    }

    /**
     * @param mixed $value
     */
    private function createFieldData(string $fieldDefIdentifier, $value, bool $isTranslatable = true): FieldData
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);
        $fieldDefinition->method('__get')->willReturnMap([
            ['identifier', $fieldDefIdentifier],
            ['isTranslatable', $isTranslatable],
        ]);

        return new FieldData([
            'fieldDefinition' => $fieldDefinition,
            'value' => $value,
        ]);
    }

    private function createFormWithLanguageCode(string $languageCode, ?string $mainLanguageCode = null): FormInterface
    {
        $formConfig = $this->createFormConfigWithLanguageCode($languageCode, $mainLanguageCode);

        $form = $this->createMock(FormInterface::class);
        $form->method('getConfig')->willReturn($formConfig);

        return $form;
    }

    private function createFormConfigWithLanguageCode(
        string $languageCode,
        ?string $mainLanguageCode = null
    ): FormConfigInterface {
        $formConfig = $this->createMock(FormConfigInterface::class);
        $formConfig->method('getOption')->willReturnMap([
            ['mainLanguageCode', null, $mainLanguageCode ?? $languageCode],
            ['languageCode', null, $languageCode],
        ]);

        return $formConfig;
    }
}
