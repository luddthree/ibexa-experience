<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\CustomerGroupPriceCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice\CustomerGroupPrice;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class UniqueProductPriceCustomerGroupValidator extends ConstraintValidator
{
    private HandlerInterface $productPriceHandler;

    public function __construct(HandlerInterface $productPriceHandler)
    {
        $this->productPriceHandler = $productPriceHandler;
    }

    /**
     * @param mixed $value
     * @param \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueProductPrice $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof CustomerGroupPriceCreateStruct) {
            throw new UnexpectedValueException($value, CustomerGroupPriceCreateStruct::class);
        }

        $product = $value->getProduct();
        $currency = $value->getCurrency();
        $price = $this->productPriceHandler->findOneByProductCode(
            $product->getCode(),
            $currency->getId(),
            $value::getDiscriminator(),
            [
                'customer_group_id' => $value->getCustomerGroupId(),
            ],
        );

        if ($price !== null) {
            assert($price instanceof CustomerGroupPrice);
            $this->context->buildViolation('ibexa.product_price.customer_group.price_exists')
                ->setParameter('%customer_group_identifier%', $price->getCustomerGroup()->identifier)
                ->setParameter('%product_code%', $price->getProductCode())
                ->setParameter('%currency_code%', $price->getCurrency()->code)
                ->setTranslationDomain('validators')
                ->addViolation();
        }
    }
}
