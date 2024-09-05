<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Variant;

use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorContext;
use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Local\Repository\CodeGenerator\CodeGeneratorRegistryInterface;
use Ibexa\ProductCatalog\Local\Repository\Variant\VariantCodeGenerator;
use PHPUnit\Framework\TestCase;

final class VariantCodeGeneratorTest extends TestCase
{
    private const EXAMPLE_DEFAULT_STRATEGY = 'random';
    private const EXAMPLE_SELECTED_STRATEGY = 'incremental';
    private const EXAMPLE_PRODUCT_CODE = 'foo-v1';

    /** @var \Ibexa\ProductCatalog\Config\ConfigProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ConfigProviderInterface $configProvider;

    /** @var \Ibexa\ProductCatalog\Local\Repository\CodeGenerator\CodeGeneratorRegistryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CodeGeneratorRegistryInterface $registry;

    private VariantCodeGenerator $generator;

    protected function setUp(): void
    {
        $this->configProvider = $this->createMock(ConfigProviderInterface::class);
        $this->registry = $this->createMock(CodeGeneratorRegistryInterface::class);

        $this->generator = new VariantCodeGenerator(
            $this->configProvider,
            $this->registry,
            self::EXAMPLE_DEFAULT_STRATEGY
        );
    }

    public function testGenerateCode(): void
    {
        $context = $this->getExampleCodeGeneratorContext();

        $selectedCodeGenerator = $this->createMock(CodeGeneratorInterface::class);
        $selectedCodeGenerator
            ->method('generateCode')
            ->with($context)
            ->willReturn(self::EXAMPLE_PRODUCT_CODE);

        $this->configProvider
            ->method('getEngineOption')
            ->with('variant_code_generator_strategy', self::EXAMPLE_DEFAULT_STRATEGY)
            ->willReturn(self::EXAMPLE_SELECTED_STRATEGY);

        $this->registry
            ->method('getCodeGenerator')
            ->with(self::EXAMPLE_SELECTED_STRATEGY)
            ->willReturn($selectedCodeGenerator);

        self::assertEquals(self::EXAMPLE_PRODUCT_CODE, $this->generator->generateCode($context));
    }

    private function getExampleCodeGeneratorContext(): CodeGeneratorContext
    {
        return new CodeGeneratorContext(
            $this->createMock(ProductTypeInterface::class)
        );
    }
}
