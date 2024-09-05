<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\FormMapper;

use Ibexa\Bundle\ProductCatalog\Form\FormMapper\AttributeGroupMapper;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use PHPUnit\Framework\TestCase;

final class AttributeGroupMapperTest extends TestCase
{
    private const ATTRIBUTE_GROUP_IDENTIFIER = 'foo';

    /** @var \Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeGroupServiceInterface $attributeGroupService;

    private AttributeGroupMapper $attributeGroupMapper;

    private AttributeGroup $attributeGroup;

    protected function setUp(): void
    {
        $this->attributeGroup = new AttributeGroup(
            1,
            self::ATTRIBUTE_GROUP_IDENTIFIER,
            'name',
            0,
            ['pol-PL'],
            [],
        );
        $this->attributeGroupService = $this->createMock(AttributeGroupServiceInterface::class);
        $this->attributeGroupMapper = new AttributeGroupMapper($this->attributeGroupService);
    }

    public function testMapToFormData(): void
    {
        $baseLanguage = new Language(['languageCode' => 'pol-PL']);
        $this->attributeGroupService
            ->method('getAttributeGroup')
            ->with(self::ATTRIBUTE_GROUP_IDENTIFIER, [$baseLanguage])
            ->willReturn(new AttributeGroup(
                1,
                self::ATTRIBUTE_GROUP_IDENTIFIER,
                'name PL',
                0,
                ['pol-PL'],
                [],
            ));

        $attributeGroupUpdateData = $this->attributeGroupMapper->mapToFormData(
            $this->attributeGroup,
            [
                'language' => new Language(['languageCode' => 'eng-GB']),
                'baseLanguage' => new Language(['languageCode' => 'pol-PL']),
            ]
        );

        self::assertSame(self::ATTRIBUTE_GROUP_IDENTIFIER, $attributeGroupUpdateData->getOriginalIdentifier());
        self::assertSame(self::ATTRIBUTE_GROUP_IDENTIFIER, $attributeGroupUpdateData->getIdentifier());
        self::assertSame('name PL', $attributeGroupUpdateData->getName());
        self::assertSame(0, $attributeGroupUpdateData->getPosition());
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $attributeGroupUpdateData->getLanguage();
        self::assertSame('eng-GB', $language->languageCode);
    }

    public function testMapToFormDataWithoutBaseLanguage(): void
    {
        $this->attributeGroupService
            ->expects(self::never())
            ->method('getAttributeGroup');

        $attributeGroupUpdateData = $this->attributeGroupMapper->mapToFormData(
            $this->attributeGroup,
            [
                'language' => new Language(['languageCode' => 'eng-GB']),
                'baseLanguage' => null,
            ]
        );

        self::assertSame(self::ATTRIBUTE_GROUP_IDENTIFIER, $attributeGroupUpdateData->getOriginalIdentifier());
        self::assertSame(self::ATTRIBUTE_GROUP_IDENTIFIER, $attributeGroupUpdateData->getIdentifier());
        self::assertSame('name', $attributeGroupUpdateData->getName());
        self::assertSame(0, $attributeGroupUpdateData->getPosition());
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $attributeGroupUpdateData->getLanguage();
        self::assertSame('eng-GB', $language->languageCode);
    }
}
