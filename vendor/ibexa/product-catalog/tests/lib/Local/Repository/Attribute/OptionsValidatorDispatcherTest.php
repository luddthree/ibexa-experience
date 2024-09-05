<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\OptionsValidatorDispatcher;
use Ibexa\ProductCatalog\Local\Repository\Attribute\OptionsValidatorRegistryInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;
use PHPUnit\Framework\TestCase;

final class OptionsValidatorDispatcherTest extends TestCase
{
    private const EXAMPLE_TYPE_IDENTIFIER = 'integer';
    private const EXAMPLE_OPTIONS = [
        'min' => 0,
        'max' => 100,
    ];

    /** @var \Ibexa\ProductCatalog\Local\Repository\Attribute\OptionsValidatorRegistryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private OptionsValidatorRegistryInterface $registry;

    private OptionsValidatorDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(OptionsValidatorRegistryInterface::class);
        $this->dispatcher = new OptionsValidatorDispatcher($this->registry);
    }

    public function testDispatch(): void
    {
        $expectedErrors = [
            new OptionsValidatorError(null, 'Invalid options'),
        ];

        $type = $this->createTypeMock(self::EXAMPLE_TYPE_IDENTIFIER);

        $validator = $this->createMock(OptionsValidatorInterface::class);
        $validator
            ->expects(self::once())
            ->method('validateOptions')
            ->with(new AttributeDefinitionOptions(self::EXAMPLE_OPTIONS))
            ->willReturn($expectedErrors);

        $this->registry
            ->method('hasValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn(true);

        $this->registry
            ->method('getValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn($validator);

        $actualErrors = $this->dispatcher->validateOptions($type, self::EXAMPLE_OPTIONS);

        self::assertSame($expectedErrors, $actualErrors);
    }

    public function testDispatchForMissingValidator(): void
    {
        $type = $this->createTypeMock(self::EXAMPLE_TYPE_IDENTIFIER);

        $this->registry
            ->method('hasValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn(false);

        $this->registry
            ->expects(self::never())
            ->method('getValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER);

        self::assertEmpty($this->dispatcher->validateOptions($type, self::EXAMPLE_OPTIONS));
    }

    private function createTypeMock(string $identifier): AttributeTypeInterface
    {
        $type = $this->createMock(AttributeTypeInterface::class);
        $type->method('getIdentifier')->willReturn($identifier);

        return $type;
    }
}
