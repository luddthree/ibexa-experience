<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CodeGenerator;

use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface;
use Ibexa\ProductCatalog\Common\Pool;

final class CodeGeneratorRegistry implements CodeGeneratorRegistryInterface
{
    /** @var \Ibexa\ProductCatalog\Common\Pool<\Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface> */
    private Pool $pool;

    /**
     * @param iterable<string,\Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface> $generators
     */
    public function __construct(iterable $generators)
    {
        $this->pool = new Pool(CodeGeneratorInterface::class, $generators);
        $this->pool->setExceptionArgumentName('type');
    }

    public function getCodeGenerator(string $alias): CodeGeneratorInterface
    {
        return $this->pool->get($alias);
    }

    public function hasCodeGenerator(string $alias): bool
    {
        return $this->pool->has($alias);
    }
}
