<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CodeGenerator;

use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface;

interface CodeGeneratorRegistryInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if given `$alias` is not valid code generator strategy
     */
    public function getCodeGenerator(string $alias): CodeGeneratorInterface;

    public function hasCodeGenerator(string $alias): bool;
}
