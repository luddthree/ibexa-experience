<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Variant;

use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorContext;
use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Local\Repository\CodeGenerator\CodeGeneratorRegistryInterface;

final class VariantCodeGenerator implements CodeGeneratorInterface
{
    private ConfigProviderInterface $configProvider;

    private CodeGeneratorRegistryInterface $registry;

    private string $defaultStrategy;

    public function __construct(
        ConfigProviderInterface $configProvider,
        CodeGeneratorRegistryInterface $registry,
        string $default
    ) {
        $this->configProvider = $configProvider;
        $this->registry = $registry;
        $this->defaultStrategy = $default;
    }

    public function generateCode(CodeGeneratorContext $context): string
    {
        return $this->registry
            ->getCodeGenerator($this->getCodeGeneratorStrategy())
            ->generateCode($context);
    }

    private function getCodeGeneratorStrategy(): string
    {
        return $this->configProvider->getEngineOption('variant_code_generator_strategy', $this->defaultStrategy);
    }
}
