<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace lib\Local\Repository\AttributeType;

use Ibexa\Contracts\Core\FieldType\FieldType;
use Ibexa\ProductCatalog\Local\Repository\AttributeType\FieldTypeAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FieldTypeAdapterTest extends TestCase
{
    private const EXAMPLE_FIELD_TYPE_IDENTIFIER = 'ezstring';
    private const EXAMPLE_IDENTIFIER = 'string';
    private const EXAMPLE_NAME = 'String';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TranslatorInterface $translator;

    /** @var \Ibexa\Contracts\Core\FieldType\FieldType|\PHPUnit\Framework\MockObject\MockObject */
    private FieldType $fieldType;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->fieldType = $this->createMock(FieldType::class);
        $this->fieldType->method('getFieldTypeIdentifier')->willReturn(self::EXAMPLE_FIELD_TYPE_IDENTIFIER);
    }

    public function testGetIdentifierReturnsCustomIdentifier(): void
    {
        $adapter = new FieldTypeAdapter(
            $this->translator,
            $this->fieldType,
            self::EXAMPLE_IDENTIFIER
        );

        self::assertEquals(self::EXAMPLE_IDENTIFIER, $adapter->getIdentifier());
    }

    public function testGetIdentifierReturnsFallback(): void
    {
        $adapter = new FieldTypeAdapter(
            $this->translator,
            $this->fieldType
        );

        self::assertEquals(self::EXAMPLE_FIELD_TYPE_IDENTIFIER, $adapter->getIdentifier());
    }

    public function testGetName(): void
    {
        $this->translator
            ->method('trans')
            ->with('ezstring.name', [], 'ibexa_fieldtypes')
            ->willReturn(self::EXAMPLE_NAME);

        $adapter = new FieldTypeAdapter(
            $this->translator,
            $this->fieldType,
            self::EXAMPLE_IDENTIFIER
        );

        self::assertEquals(self::EXAMPLE_NAME, $adapter->getName());
    }
}
