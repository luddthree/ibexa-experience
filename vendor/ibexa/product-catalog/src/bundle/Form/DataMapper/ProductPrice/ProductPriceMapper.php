<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPriceDataInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface;
use Ibexa\ProductCatalog\Exception\MissingHandlingServiceException;

/**
 * Responsible for conversion of form data objects into ProductPrice structs.
 */
final class ProductPriceMapper
{
    /** @var iterable<\Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\DataToStructTransformerInterface> */
    private iterable $dataConverters;

    /**
     * @param iterable<\Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\DataToStructTransformerInterface> $dataConverters
     */
    public function __construct(
        iterable $dataConverters
    ) {
        $this->dataConverters = $dataConverters;
    }

    public function mapToStruct(ProductPriceDataInterface $data): ProductPriceStructInterface
    {
        foreach ($this->dataConverters as $dataConverter) {
            if ($dataConverter->supports($data)) {
                return $dataConverter->convertDataToStruct($data);
            }
        }

        throw new MissingHandlingServiceException(
            $this->dataConverters,
            $data,
            DataToStructTransformerInterface::class,
            'ibexa.product_catalog.price.data_to_struct_transformer',
        );
    }
}
