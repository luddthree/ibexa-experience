<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\FormMapper;

use Ibexa\Bundle\ProductCatalog\Form\FormMapper\AttributeDefinitionMapper;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use PHPUnit\Framework\TestCase;

final class AttributeDefinitionMapperTest extends TestCase
{
    private const ATTRIBUTE_DEFINITION_IDENTIFIER = 'foo';

    /** @var \Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private AttributeDefinitionMapper $attributeDefinitionMapper;

    private AttributeDefinition $attributeDefinition;

    protected function setUp(): void
    {
        $this->attributeDefinition = new AttributeDefinition(
            1,
            self::ATTRIBUTE_DEFINITION_IDENTIFIER,
            $this->getAttributeTypeMock(),
            $this->getAttributeGroupMock(),
            'name',
            0,
            ['pol-PL'],
            null,
            ['pol-PL' => 'name'],
            [],
        );

        $this->attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);
        $this->attributeDefinitionMapper = new AttributeDefinitionMapper($this->attributeDefinitionService);
    }

    public function testMapToFormData(): void
    {
        $baseLanguageCode = 'pol-PL';
        $this->attributeDefinitionService
            ->method('getAttributeDefinition')
            ->with(self::ATTRIBUTE_DEFINITION_IDENTIFIER, [$baseLanguageCode])
            ->willReturn(new AttributeDefinition(
                1,
                self::ATTRIBUTE_DEFINITION_IDENTIFIER,
                $this->getAttributeTypeMock(),
                $this->getAttributeGroupMock(),
                'name PL',
                0,
                ['pol-PL'],
                null,
                ['pol-PL' => 'name PL'],
                [],
            ));

        $attributeDefinitionUpdateData = $this->attributeDefinitionMapper->mapToFormData(
            $this->attributeDefinition,
            [
                'language' => new Language(['languageCode' => 'eng-GB']),
                'baseLanguage' => new Language(['languageCode' => 'pol-PL']),
            ]
        );

        self::assertSame(self::ATTRIBUTE_DEFINITION_IDENTIFIER, $attributeDefinitionUpdateData->getOriginalIdentifier());
        self::assertSame(self::ATTRIBUTE_DEFINITION_IDENTIFIER, $attributeDefinitionUpdateData->getIdentifier());
        self::assertSame('name PL', $attributeDefinitionUpdateData->getName());
        self::assertSame(0, $attributeDefinitionUpdateData->getPosition());
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $attributeDefinitionUpdateData->getLanguage();
        self::assertSame('eng-GB', $language->languageCode);
    }

    public function testMapToFormDataWithoutBaseLanguage(): void
    {
        $this->attributeDefinitionService
            ->expects(self::never())
            ->method('getAttributeDefinition');

        $attributeDefinitionUpdateData = $this->attributeDefinitionMapper->mapToFormData(
            $this->attributeDefinition,
            [
                'language' => new Language(['languageCode' => 'eng-GB']),
                'baseLanguage' => null,
            ]
        );

        self::assertSame(self::ATTRIBUTE_DEFINITION_IDENTIFIER, $attributeDefinitionUpdateData->getOriginalIdentifier());
        self::assertSame(self::ATTRIBUTE_DEFINITION_IDENTIFIER, $attributeDefinitionUpdateData->getIdentifier());
        self::assertSame('name', $attributeDefinitionUpdateData->getName());
        self::assertSame(0, $attributeDefinitionUpdateData->getPosition());
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $attributeDefinitionUpdateData->getLanguage();
        self::assertSame('eng-GB', $language->languageCode);
    }

    private function getAttributeTypeMock(): AttributeTypeInterface
    {
        return $this->createMock(AttributeTypeInterface::class);
    }

    private function getAttributeGroupMock(): AttributeGroupInterface
    {
        return $this->createMock(AttributeGroupInterface::class);
    }
}
