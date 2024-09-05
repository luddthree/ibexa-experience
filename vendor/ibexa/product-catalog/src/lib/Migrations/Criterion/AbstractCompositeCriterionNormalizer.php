<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

/**
 * @template T of \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\AbstractCompositeCriterion
 *
 * @extends \Ibexa\ProductCatalog\Migrations\Criterion\AbstractCriterionNormalizer<T>
 */
abstract class AbstractCompositeCriterionNormalizer extends AbstractCriterionNormalizer implements NormalizerAwareInterface, DenormalizerAwareInterface
{
    use NormalizerAwareTrait;

    use DenormalizerAwareTrait;

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $context
     *
     * @return array<mixed>
     */
    final protected function convertCriteria($data, ?string $format, array $context): array
    {
        Assert::keyExists($data, 'criteria');
        Assert::isArray($data['criteria']);

        $criteria = [];
        foreach ($data['criteria'] as $criterionData) {
            $criteria[] = $this->denormalizer->denormalize(
                $criterionData,
                CriterionInterface::class,
                $format,
                $context,
            );
        }

        return $criteria;
    }

    protected function doNormalize($object, string $format = null, array $context = []): array
    {
        return [
            'criteria' => $this->normalizer->normalize($object->getCriteria(), $format, $context),
        ];
    }
}
