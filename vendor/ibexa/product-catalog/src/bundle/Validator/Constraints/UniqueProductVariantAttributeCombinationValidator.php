<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\AbstractProductVariantData;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\ProductVariantFetchAdapter;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class UniqueProductVariantAttributeCombinationValidator extends ConstraintValidator
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueProductVariantAttributeCombination) {
            throw new UnexpectedTypeException($constraint, UniqueProductVariantAttributeCombination::class);
        }

        if (!$value instanceof AbstractProductVariantData) {
            throw new UnexpectedValueException($value, AbstractProductVariantData::class);
        }

        $product = $value->getProduct();

        /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> $otherProductVariants */
        $otherProductVariants = new BatchIterator(new ProductVariantFetchAdapter($this->productService, $product));

        $attributes = $value->getAttributes();
        $newCombination = [];
        foreach ($attributes as $identifier => $attribute) {
            $newCombination[$identifier] = $attribute->getValue();
        }

        $combinations = [];
        foreach ($otherProductVariants as $variant) {
            if ($variant->getCode() === $value->getOriginalCode()) {
                // Do not compare the same product variant with itself
                continue;
            }
            $combination = [];
            foreach ($variant->getAttributes() as $attribute) {
                $combination[$attribute->getIdentifier()] = $attribute->getValue();
            }

            $combinations[$variant->getCode()] = $combination;
        }

        foreach ($combinations as $productVariantCode => $combination) {
            if ($combination == $newCombination) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('%product_variant_code%', $productVariantCode)
                    ->atPath('attributes')
                    ->addViolation()
                ;
            }
        }
    }
}
