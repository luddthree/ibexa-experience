<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CodeGenerator\Strategy;

use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorContext;
use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface;

final class RandomCodeGenerator implements CodeGeneratorInterface
{
    public function generateCode(CodeGeneratorContext $context): string
    {
        $prefix = '';
        if ($context->getBaseProduct() !== null) {
            $prefix = $context->getBaseProduct()->getCode() . '-';
        }

        return strtoupper(uniqid($prefix));
    }
}
