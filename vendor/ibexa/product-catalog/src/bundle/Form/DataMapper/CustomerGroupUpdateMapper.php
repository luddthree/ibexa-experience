<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupUpdateData;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;

final class CustomerGroupUpdateMapper
{
    public function mapToStruct(CustomerGroupUpdateData $data): CustomerGroupUpdateStruct
    {
        $language = $data->getLanguage();
        $languageId = $language->id;

        assert(is_int($languageId));
        assert($data->getName() !== null);

        return new CustomerGroupUpdateStruct(
            $data->getId(),
            $data->getIdentifier(),
            [
                $languageId => $data->getName(),
            ],
            [
                $languageId => $data->getDescription() ?? '',
            ],
            $data->getGlobalPriceRate(),
        );
    }
}
