<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Variant;

use Exception;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorContext;
use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantGenerateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class VariantGenerator implements VariantGeneratorInterface
{
    private LocalProductServiceInterface $productService;

    private CodeGeneratorInterface $codeGenerator;

    private Repository $repository;

    public function __construct(
        LocalProductServiceInterface $productService,
        CodeGeneratorInterface $codeGenerator,
        Repository $repository
    ) {
        $this->productService = $productService;
        $this->codeGenerator = $codeGenerator;
        $this->repository = $repository;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function generateVariants(
        ProductInterface $product,
        ProductVariantGenerateStruct $variantGenerateStruct
    ): void {
        $createStructs = [];
        $combinations = $this->generateCombination($variantGenerateStruct->getAttributes());
        foreach ($combinations as $index => $attributes) {
            $code = $this->codeGenerator->generateCode(new CodeGeneratorContext(
                $product->getProductType(),
                $attributes,
                $product,
                $index + 1
            ));

            $createStructs[] = new ProductVariantCreateStruct(
                $attributes,
                $code
            );
        }

        $this->repository->beginTransaction();
        try {
            $this->productService->createProductVariants($product, $createStructs);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * @param array<string,array<mixed>> $groups
     *
     * @return iterable<int,array<string,mixed>>
     */
    private function generateCombination(array $groups): iterable
    {
        $group = key($groups);
        $values = $groups[$group];
        unset($groups[$group]);

        if (!empty($groups)) {
            $results = $this->generateCombination($groups);
            foreach ($results as $result) {
                foreach ($values as $value) {
                    $result[$group] = $value;
                    yield $result;
                }
            }
        } else {
            foreach ($values as $value) {
                yield [$group => $value];
            }
        }
    }
}
