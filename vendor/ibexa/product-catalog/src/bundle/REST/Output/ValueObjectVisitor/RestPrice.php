<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\Visitor;

final class RestPrice extends Price
{
    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Price $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        parent::visit($visitor, $generator, $data->price);
    }
}
