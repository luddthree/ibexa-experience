<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Contracts\Core\Event\NameSchema\ResolveContentNameSchemaEvent;
use Ibexa\Contracts\Core\Event\NameSchema\ResolveNameSchemaEvent;
use Ibexa\Contracts\Core\Event\NameSchema\ResolveUrlAliasSchemaEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\NameSchema\NameSchemaStrategyInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\ProductCatalog\Event\NameSchemaSubscriber;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class NameSchemaSubscriberTest extends TestCase
{
    private NameSchemaSubscriber $subscrber;

    /**
     * @return iterable<array{\Ibexa\Contracts\Core\Event\NameSchema\ResolveUrlAliasSchemaEvent, array<string,array<string>>}>
     */
    public function dataProviderOnResolveUrlAliasSchema(): iterable
    {
        yield [
            $this->createResolveUrlAliasSchemaEvent(
                ['field' => []],
                $this->createContent(
                    $this->createContentType(),
                )
            ),
            [],
        ];

        yield [
            $this->createResolveUrlAliasSchemaEvent(
                ['attribute' => []],
                $this->createContent(
                    $this->createContentType(),
                )
            ),
            [],
        ];

        yield [
            $this->createResolveUrlAliasSchemaEvent(
                ['attributes' => []],
                $this->createContent(
                    $this->createContentType(
                        $this->createProductSpecificationFieldDefinition()
                    ),
                    new Value(1, 'code', [], false)
                )
            ),
            [],
        ];

        yield [
            $this->createResolveUrlAliasSchemaEvent(
                ['attribute' => ['attr1']],
                $this->createContent(
                    $this->createContentType(
                        $this->createProductSpecificationFieldDefinition()
                    ),
                    new Value(1, 'code', ['foo'], false)
                )
            ),
            ['eng-GB' => ['attribute:attr1' => 'Name']],
        ];
    }

    protected function setUp(): void
    {
        $attributeDefinitionServiceMock = $this->createMock(AttributeDefinitionServiceInterface::class);
        $attributeDefinitionServiceMock
            ->method('getAttributeDefinition')
            ->willReturnCallback(function () {
                $mock = $this->createMock(AttributeDefinitionInterface::class);
                $mock->method('getId')->willReturn(0);
                $mock->method('getName')->willReturn('Name');

                return $mock;
            });
        $nameSchemaStrategyMock = $this->createMock(NameSchemaStrategyInterface::class);
        $nameSchemaStrategyMock->method('supports')->willReturn(true);
        $nameSchemaStrategyMock->method('resolve')->willReturnCallback(static function (AttributeDefinitionInterface $attributeDefinition) {
            return $attributeDefinition->getName();
        });

        $loggerMock = $this->createMock(LoggerInterface::class);
        $this->subscrber = new NameSchemaSubscriber(
            $attributeDefinitionServiceMock,
            $nameSchemaStrategyMock,
            $loggerMock
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals([
            ResolveNameSchemaEvent::class => [
                'onResolveNameSchema',
            ],
            ResolveContentNameSchemaEvent::class => [
                'onResolveContentNameSchema',
            ],
            ResolveUrlAliasSchemaEvent::class => [
                'onResolveUrlAliasSchema',
            ],
        ], NameSchemaSubscriber::getSubscribedEvents());
    }

    /**
     * @dataProvider dataProviderOnResolveUrlAliasSchema
     *
     * @param array<string, string> $result
     */
    public function testOnResolveUrlAliasSchema(ResolveUrlAliasSchemaEvent $event, array $result): void
    {
        $this->subscrber->onResolveUrlAliasSchema($event);

        self::assertEquals($result, $event->getTokenValues());
    }

    /**
     * @param array<string,array<string>> $schemaIdentifiers
     */
    private function createResolveUrlAliasSchemaEvent(array $schemaIdentifiers, Content $content): ResolveUrlAliasSchemaEvent
    {
        return new ResolveUrlAliasSchemaEvent($schemaIdentifiers, $content);
    }

    private function createContent(ContentType $contentType, Value $field = null): Content
    {
        $content = $this->createMock(Content::class);

        $content->method('getContentType')->willReturn(
            $contentType
        );

        $content->method('getFieldValue')->willReturn(
            $field
        );

        $content->method('getVersionInfo')->willReturn($this->createVersionInfo());

        return $content;
    }

    private function createContentType(FieldDefinition $fieldDefinition = null): ContentType
    {
        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->method('getFirstFieldDefinitionOfType')
            ->with(Type::FIELD_TYPE_IDENTIFIER)
            ->willReturn($fieldDefinition);

        return $contentType;
    }

    private function createVersionInfo(): VersionInfo
    {
        $versionInfo = $this->createMock(VersionInfo::class);
        $versionInfo->method('getLanguages')->willReturn([new Language(['languageCode' => 'eng-GB'])]);

        return $versionInfo;
    }

    private function createProductSpecificationFieldDefinition(): FieldDefinition
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);
        $fieldDefinition
            ->method('__get')
            ->with(self::equalTo('identifier'))
            ->willReturn(Type::FIELD_TYPE_IDENTIFIER);

        return $fieldDefinition;
    }
}
