<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\ProductSpecificationTranslatabilitySubscriber;
use Ibexa\Contracts\Core\Repository\Events\ContentType\AddFieldDefinitionEvent;
use Ibexa\Contracts\Core\Repository\Events\ContentType\BeforeCreateContentTypeEvent;
use Ibexa\Contracts\Core\Repository\Events\ContentType\BeforeUpdateContentTypeDraftEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft as ApiContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Ibexa\ProductCatalog\Exception\BadStateException;
use PHPUnit\Framework\TestCase;

final class ProductSpecificationTranslatabilitySubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                BeforeCreateContentTypeEvent::class => 'onBeforeCreateContentType',
                BeforeUpdateContentTypeDraftEvent::class => 'onBeforeUpdateContentTypeDraft',
                AddFieldDefinitionEvent::class => 'onAddProductSpecificationFieldDefinition',
            ],
            ProductSpecificationTranslatabilitySubscriber::getSubscribedEvents()
        );
    }

    /**
     * @dataProvider dataProviderForTestEnforceNoTranslatabilityOnProductTypeCreation
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct> $fieldDefinitionCreateStructs
     */
    public function testEnforceNoTranslatibilityOnProductTypeCreation(
        array $fieldDefinitionCreateStructs,
        bool $shouldThrowException
    ): void {
        $subscriber = new ProductSpecificationTranslatabilitySubscriber();
        $event = new BeforeCreateContentTypeEvent(
            $this->getContentTypeCreateStruct($fieldDefinitionCreateStructs),
            []
        );

        if ($shouldThrowException) {
            $this->expectException(BadStateException::class);
        }

        $subscriber->onBeforeCreateContentType($event);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @dataProvider dataProviderForTestEnforceNoTranslatabilityOnProductTypeUpdate
     *
     * @param array<\Ibexa\Core\Repository\Values\ContentType\FieldDefinition> $fieldDefinitions
     */
    public function testEnforceNoTranslatibilityOnProductTypeUpdate(
        array $fieldDefinitions,
        bool $shouldThrowException
    ): void {
        $subscriber = new ProductSpecificationTranslatabilitySubscriber();
        $event = new BeforeUpdateContentTypeDraftEvent(
            $this->getContentTypeDraft($fieldDefinitions),
            new ContentTypeUpdateStruct(),
        );

        if ($shouldThrowException) {
            $this->expectException(BadStateException::class);
        }

        $subscriber->onBeforeUpdateContentTypeDraft($event);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @dataProvider dataProviderForTestEnforceNoTranslatabilityOnProductSpecificationFieldAdding
     *
     * @param array<\Ibexa\Core\Repository\Values\ContentType\FieldDefinition> $fieldDefinitions
     */
    public function testEnforceNoTranslatibilityOnProductSpecificationFieldAdding(
        array $fieldDefinitions,
        FieldDefinitionCreateStruct $createStruct,
        bool $shouldThrowException
    ): void {
        $subscriber = new ProductSpecificationTranslatabilitySubscriber();
        $event = new AddFieldDefinitionEvent(
            $this->getContentTypeDraft($fieldDefinitions),
            $createStruct
        );

        if ($shouldThrowException) {
            $this->expectException(BadStateException::class);
        }

        $subscriber->onAddProductSpecificationFieldDefinition($event);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @return iterable<string, array{array{\Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct}, boolean}>
     */
    public function dataProviderForTestEnforceNoTranslatabilityOnProductTypeCreation(): iterable
    {
        yield 'does not contain product specification fieldtype' => [
            [
                new FieldDefinitionCreateStruct(['fieldTypeIdentifier' => 'foo']),
                new FieldDefinitionCreateStruct(['fieldTypeIdentifier' => 'bar']),
            ],
            false,
        ];

        yield 'contains product specification fieldtype marked as non-translatable' => [
            [
                new FieldDefinitionCreateStruct(['identifier' => 'foo']),
                new FieldDefinitionCreateStruct([
                    'fieldTypeIdentifier' => 'ibexa_product_specification',
                    'isTranslatable' => false,
                ]),
            ],
            false,
        ];

        yield 'contains product specification fieldtype marked as translatable' => [
            [
                new FieldDefinitionCreateStruct(['identifier' => 'foo']),
                new FieldDefinitionCreateStruct([
                    'fieldTypeIdentifier' => 'ibexa_product_specification',
                    'isTranslatable' => true,
                ]),
            ],
            true,
        ];
    }

    /**
     * @return iterable<string, array{array{\Ibexa\Core\Repository\Values\ContentType\FieldDefinition}, boolean}>
     */
    public function dataProviderForTestEnforceNoTranslatabilityOnProductTypeUpdate(): iterable
    {
        yield 'does not contain product specification fieldtype' => [
            [
                new FieldDefinition(['identifier' => 'field_1', 'fieldTypeIdentifier' => 'foo']),
                new FieldDefinition(['identifier' => 'field_2', 'fieldTypeIdentifier' => 'bar']),
            ],
            false,
        ];

        yield 'contains product specification fieldtype marked as non-translatable' => [
            [
                new FieldDefinition(['identifier' => 'foo']),
                new FieldDefinition([
                    'identifier' => 'field_1',
                    'fieldTypeIdentifier' => 'ibexa_product_specification',
                    'isTranslatable' => false,
                ]),
            ],
            false,
        ];

        yield 'contains product specification fieldtype marked as translatable' => [
            [
                new FieldDefinition(['identifier' => 'foo']),
                new FieldDefinition([
                    'identifier' => 'field_1',
                    'fieldTypeIdentifier' => 'ibexa_product_specification',
                    'isTranslatable' => true,
                ]),
            ],
            true,
        ];
    }

    /**
     * @return iterable<
     *     string,
     *     array{
     *         array{\Ibexa\Core\Repository\Values\ContentType\FieldDefinition},
     *         \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct,
     *         boolean,
     *     }
     * >
     */
    public function dataProviderForTestEnforceNoTranslatabilityOnProductSpecificationFieldAdding(): iterable
    {
        yield 'does not contain product specification fieldtype' => [
            [
                new FieldDefinition(['identifier' => 'field_1', 'fieldTypeIdentifier' => 'foo']),
                new FieldDefinition(['identifier' => 'field_2', 'fieldTypeIdentifier' => 'bar']),
            ],
            new FieldDefinitionCreateStruct([
                'fieldTypeIdentifier' => 'ezmatrix',
                'isTranslatable' => true,
            ]),
            false,
        ];

        yield 'contains product specification fieldtype marked as non-translatable' => [
            [
                new FieldDefinition(['identifier' => 'foo']),
                new FieldDefinition([
                    'identifier' => 'field_1',
                    'fieldTypeIdentifier' => 'ibexa_product_specification',
                    'isTranslatable' => false,
                ]),
            ],
            new FieldDefinitionCreateStruct([
                'fieldTypeIdentifier' => 'ezmatrix',
                'isTranslatable' => true,
            ]),
            false,
        ];

        yield 'contains product specification fieldtype marked as translatable' => [
            [
                new FieldDefinition(['identifier' => 'foo']),
                new FieldDefinition([
                    'identifier' => 'field_1',
                    'fieldTypeIdentifier' => 'ezmatrix',
                    'isTranslatable' => false,
                ]),
            ],
            new FieldDefinitionCreateStruct([
                'fieldTypeIdentifier' => 'ibexa_product_specification',
                'isTranslatable' => true,
            ]),
            true,
        ];
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct> $fieldDefinitions
     */
    private function getContentTypeCreateStruct(array $fieldDefinitions): ContentTypeCreateStruct
    {
        $struct = new ContentTypeCreateStruct();
        $struct->fieldDefinitions = $fieldDefinitions;

        return $struct;
    }

    /**
     * @param array<\Ibexa\Core\Repository\Values\ContentType\FieldDefinition> $fieldDefinitions
     */
    private function getContentTypeDraft(array $fieldDefinitions): ApiContentTypeDraft
    {
        return new ContentTypeDraft([
            'innerContentType' => new ContentType([
                'fieldDefinitions' => new FieldDefinitionCollection($fieldDefinitions),
                'identifier' => 'foobar',
            ]),
        ]);
    }
}
