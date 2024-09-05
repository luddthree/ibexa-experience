<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Cache\AttributeDefinition;

use Ibexa\Contracts\Core\Persistence\Content\Type;
use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as ContentTypeHandlerInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\AttributeGroupIdentifierCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\NameCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd;
use Ibexa\ProductCatalog\Local\Persistence\Cache\AttributeDefinition\Handler as CacheHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionUpdateStruct;
use Ibexa\Tests\ProductCatalog\Local\Persistence\Cache\AbstractCacheHandlerTest;
use InvalidArgumentException;

final class HandlerTest extends AbstractCacheHandlerTest
{
    private const ATTRIBUTE_DEFINITION_ID = 1;
    private const ATTRIBUTE_DEFINITION_IDENTIFIER = 'foo';
    private const ATTRIBUTE_GROUP_IDENTIFIER = 'bar';

    /**
     * @return \Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getInnerHandler(): HandlerInterface
    {
        return $this->createMock(HandlerInterface::class);
    }

    /**
     * @param object|\Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface $innerHandler
     */
    public function getPersistenceCacheHandler(object $innerHandler): CacheHandler
    {
        if (!$innerHandler instanceof HandlerInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s to be passed to %s, received %s',
                    HandlerInterface::class,
                    __METHOD__,
                    get_class($innerHandler),
                )
            );
        }

        return new CacheHandler(
            $innerHandler,
            $this->createContentTypeHandlerMock(),
            $this->cacheMock,
            $this->cacheIdentifierGeneratorMock,
            $this->loggerMock,
            $this->cacheIdentifierSanitizer
        );
    }

    public function testUpdate(): void
    {
        $updateStruct = new AttributeDefinitionUpdateStruct();
        $updateStruct->id = self::ATTRIBUTE_DEFINITION_ID;
        $updateStruct->identifier = 'new-foo';

        $attributeDefinition = new AttributeDefinition();
        $attributeDefinition->id = self::ATTRIBUTE_DEFINITION_ID;
        $attributeDefinition->identifier = self::ATTRIBUTE_DEFINITION_IDENTIFIER;

        $this->loggerMock->expects(self::once())->method('logCall');
        $this->loggerMock->expects(self::never())->method('logCacheHit');
        $this->loggerMock->expects(self::never())->method('logCacheMiss');

        $innerHandler = $this->getInnerHandler();
        $innerHandler
            ->expects(self::once())
            ->method('load')
            ->with($updateStruct->id)
            ->willReturn($attributeDefinition);
        $innerHandler
            ->expects(self::once())
            ->method('update')
            ->with($updateStruct);

        $this->cacheIdentifierGeneratorMock
            ->expects(self::exactly(6))
            ->method('generateTag')
            ->will(
                self::returnValueMap([
                    ['content_fields_type', [1], false, 'cft-1'],
                    ['type', [1], false, 't-1'],
                    ['content_fields_type', [2], false, 'cft-2'],
                    ['type', [2], false, 't-2'],
                    ['attribute_definition', [self::ATTRIBUTE_DEFINITION_ID], false, 'ad-1'],
                    ['type_map', [], false, 'tm'],
                ])
            );

        $tags = ['cft-1', 't-1', 'cft-2', 't-2', 'ad-1', 'tm'];
        $this->cacheMock
            ->expects(self::once())
            ->method('invalidateTags')
            ->with($tags);

        $this->getPersistenceCacheHandler($innerHandler)->update($updateStruct);
    }

    public function providerForUnCachedMethods(): iterable
    {
        $attributeGroupMock = $this->createMock(AttributeGroupInterface::class);
        $attributeGroupMock->method('getIdentifier')
            ->willReturn(self::ATTRIBUTE_GROUP_IDENTIFIER);

        $query = new AttributeDefinitionQuery(
            new LogicalAnd(
                new AttributeGroupIdentifierCriterion(self::ATTRIBUTE_GROUP_IDENTIFIER),
                new NameCriterion('namePrefix', 'STARTS_WITH'),
            ),
            null,
            0,
            1,
        );

        yield [
            'findMatching',
            [
                $query,
            ],
            null,
            null,
            null,
            null,
            [],
        ];

        $query = new AttributeDefinitionQuery(
            new LogicalAnd(
                new AttributeGroupIdentifierCriterion(self::ATTRIBUTE_GROUP_IDENTIFIER),
                new NameCriterion('namePrefix', 'STARTS_WITH'),
            ),
        );

        yield [
            'countMatching',
            [
                $query,
            ],
            null,
            null,
            null,
            null,
            0,
        ];
        yield [
            'create',
            [
                new AttributeDefinitionCreateStruct(),
            ],
            null,
            null,
            null,
            null,
            null,
        ];
        yield [
            'deleteByIdentifier',
            [
                self::ATTRIBUTE_DEFINITION_IDENTIFIER,
            ],
            [
                ['attribute_definition', [self::ATTRIBUTE_DEFINITION_IDENTIFIER], false],
            ],
            null,
            ['ad-1'],
        ];
    }

    public function providerForCachedLoadMethodsHit(): iterable
    {
        $attributeDefinition = new AttributeDefinition();
        $attributeDefinition->id = self::ATTRIBUTE_DEFINITION_ID;
        $attributeDefinition->identifier = self::ATTRIBUTE_DEFINITION_IDENTIFIER;

        yield [
            'load',
            [
                self::ATTRIBUTE_DEFINITION_ID,
            ],
            'ibx-ad-' . self::ATTRIBUTE_DEFINITION_ID,
            null,
            null,
            [['attribute_definition', [self::ATTRIBUTE_DEFINITION_ID], true]],
            ['ibx-ad-' . self::ATTRIBUTE_DEFINITION_ID],
            $attributeDefinition,
        ];
        yield [
            'loadByIdentifier',
            [
                self::ATTRIBUTE_DEFINITION_IDENTIFIER,
            ],
            'ibx-ad-' . self::ATTRIBUTE_DEFINITION_IDENTIFIER,
            null,
            null,
            [['attribute_definition', [self::ATTRIBUTE_DEFINITION_IDENTIFIER], true]],
            ['ibx-ad-' . self::ATTRIBUTE_DEFINITION_IDENTIFIER],
            $attributeDefinition,
        ];
    }

    public function providerForCachedLoadMethodsMiss(): iterable
    {
        $attributeDefinition = new AttributeDefinition();
        $attributeDefinition->id = self::ATTRIBUTE_DEFINITION_ID;
        $attributeDefinition->identifier = self::ATTRIBUTE_DEFINITION_IDENTIFIER;

        yield [
            'load',
            [
                self::ATTRIBUTE_DEFINITION_ID,
            ],
            'ibx-ad-' . self::ATTRIBUTE_DEFINITION_ID,
            [
                ['attribute_definition', [self::ATTRIBUTE_DEFINITION_ID], false],
                ['attribute_definition', [self::ATTRIBUTE_DEFINITION_IDENTIFIER], false],
            ],
            ['ad-1', 'ad-foo'],
            [['attribute_definition', [self::ATTRIBUTE_DEFINITION_ID], true]],
            ['ibx-ad-' . self::ATTRIBUTE_DEFINITION_ID],
            $attributeDefinition,
        ];
        yield [
            'loadByIdentifier',
            [
                self::ATTRIBUTE_DEFINITION_IDENTIFIER,
            ],
            'ibx-ad-' . self::ATTRIBUTE_DEFINITION_IDENTIFIER,
            [
                ['attribute_definition', [self::ATTRIBUTE_DEFINITION_ID], false],
                ['attribute_definition', [self::ATTRIBUTE_DEFINITION_IDENTIFIER], false],
            ],
            ['ad-1', 'ad-foo'],
            [['attribute_definition', [self::ATTRIBUTE_DEFINITION_IDENTIFIER], true]],
            ['ibx-ad-' . self::ATTRIBUTE_DEFINITION_IDENTIFIER],
            $attributeDefinition,
        ];
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Type\Handler|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createContentTypeHandlerMock(): ContentTypeHandlerInterface
    {
        $handlerMock = $this->createMock(ContentTypeHandlerInterface::class);
        $handlerMock
            ->method('loadContentTypesByFieldDefinitionIdentifier')
            ->with('ibexa_product_specification')
            ->willReturnCallback(static function (string $identifier): array {
                $contentType1 = new Type();
                $contentType1->id = 1;

                $contentType2 = new Type();
                $contentType2->id = 2;

                $fieldDefinition = new Type\FieldDefinition();
                $fieldDefinition->identifier = $identifier;
                $fieldDefinition->fieldTypeConstraints->fieldSettings['attributes_definitions'] = [
                    [
                        ['attributeDefinition' => self::ATTRIBUTE_DEFINITION_IDENTIFIER],
                    ],
                ];
                $contentType1->fieldDefinitions = [$fieldDefinition];
                $contentType2->fieldDefinitions = [$fieldDefinition];

                return [$contentType1, $contentType2];
            });

        return $handlerMock;
    }
}
