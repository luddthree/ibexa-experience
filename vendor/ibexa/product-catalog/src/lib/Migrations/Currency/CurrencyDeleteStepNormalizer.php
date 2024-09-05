<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Currency;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<\Ibexa\ProductCatalog\Migrations\Currency\CurrencyDeleteStep>
 */
final class CurrencyDeleteStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        assert($object instanceof CurrencyDeleteStep);

        return [
            'criteria' => $this->normalizer->normalize(
                $object->getCriterion(),
                $format,
                $context,
            ),
        ];
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): CurrencyDeleteStep
    {
        Assert::keyExists($data, 'criteria');
        $criteria = $this->denormalizer->denormalize(
            $data['criteria'],
            CriterionInterface::class,
            $format,
            $context,
        );

        return new CurrencyDeleteStep(
            $criteria,
        );
    }

    public function getHandledClassType(): string
    {
        return CurrencyDeleteStep::class;
    }

    public function getType(): string
    {
        return 'currency';
    }

    public function getMode(): string
    {
        return 'delete';
    }
}
