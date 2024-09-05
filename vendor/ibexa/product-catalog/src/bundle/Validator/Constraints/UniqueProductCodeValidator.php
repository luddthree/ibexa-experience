<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductCodeDataContainerInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueProductCodeValidator extends ConstraintValidator
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueProductCode $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof ProductCodeDataContainerInterface) {
            return;
        }

        if (empty($value->getCode())) {
            return;
        }

        try {
            $product = $this->productService->getProduct($value->getCode());
            if ($this->isSameProduct($value, $product)) {
                return;
            }

            $this->context
                ->buildViolation($constraint->message)
                ->atPath('code')
                ->setParameter('%code%', $value->getCode())
                ->addViolation();
        } catch (NotFoundException|UnauthorizedException $e) {
            return;
        }
    }

    private function isSameProduct(ProductCodeDataContainerInterface $value, ProductInterface $product): bool
    {
        return $value->getOriginalCode() === $product->getCode();
    }
}
