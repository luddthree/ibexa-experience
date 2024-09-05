<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Webmozart\Assert\Assert;

/**
 * @phpstan-extends \Ibexa\ProductCatalog\Migrations\Criterion\AbstractCriterionNormalizer<
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion,
 * >
 */
class FieldValueCriterionNormalizer extends AbstractCriterionNormalizer
{
    protected function getHandledType(): string
    {
        return 'field_value';
    }

    protected function getHandledClass(): string
    {
        return FieldValueCriterion::class;
    }

    protected function doDenormalize(array $data, string $type, ?string $format, array $context): CriterionInterface
    {
        Assert::keyExists($data, 'value');

        Assert::keyExists($data, 'field');
        Assert::stringNotEmpty($data['field']);

        $operator = $data['operator'] ?? null;
        Assert::nullOrStringNotEmpty($operator);

        return new FieldValueCriterion($data['field'], $data['value'], $operator);
    }

    protected function doNormalize(CriterionInterface $object, string $format = null, array $context = []): array
    {
        return [
            'field' => $object->getField(),
            'value' => $object->getValue(),
            'operator' => $object->getOperator(),
        ];
    }
}
