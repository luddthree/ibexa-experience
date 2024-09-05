<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupCreateData;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;

final class CustomerGroupCreateMapper
{
    public function mapToStruct(CustomerGroupCreateData $data): CustomerGroupCreateStruct
    {
        $language = $data->getLanguage();
        assert($language instanceof Language);
        $languageId = $language->id;

        assert(is_int($languageId));
        assert($data->getName() !== null);

        return new CustomerGroupCreateStruct(
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
