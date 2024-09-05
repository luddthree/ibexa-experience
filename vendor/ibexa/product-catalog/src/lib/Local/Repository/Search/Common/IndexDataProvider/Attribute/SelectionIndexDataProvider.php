<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\Attribute;

use Ibexa\Contracts\Core\Search;
use Ibexa\Contracts\Core\Search\FieldType;

/**
 * @extends \Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\Attribute\AbstractScalarDataProvider<string>
 */
final class SelectionIndexDataProvider extends AbstractScalarDataProvider
{
    protected function getSearchFieldType(): FieldType
    {
        return new Search\FieldType\StringField();
    }
}
