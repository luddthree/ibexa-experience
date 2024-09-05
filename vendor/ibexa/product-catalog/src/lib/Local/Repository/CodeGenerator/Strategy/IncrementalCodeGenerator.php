<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CodeGenerator\Strategy;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorContext;
use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface;

final class IncrementalCodeGenerator implements CodeGeneratorInterface
{
    public function generateCode(CodeGeneratorContext $context): string
    {
        if (!$context->hasBaseProduct()) {
            throw new InvalidArgumentException('$context', 'missing base product');
        }

        if (!$context->hasIndex()) {
            throw new InvalidArgumentException('$context', 'missing index');
        }

        return $context->getBaseProduct()->getCode() . '-' . $context->getIndex();
    }
}
