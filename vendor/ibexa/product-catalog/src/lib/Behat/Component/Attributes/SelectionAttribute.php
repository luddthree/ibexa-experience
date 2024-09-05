<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component\Attributes;

use Ibexa\AdminUi\Behat\Component\Fields\Selection as FieldSelection;

final class SelectionAttribute extends FieldSelection
{
    public function getFieldTypeIdentifier(): string
    {
        return 'selection';
    }
}
