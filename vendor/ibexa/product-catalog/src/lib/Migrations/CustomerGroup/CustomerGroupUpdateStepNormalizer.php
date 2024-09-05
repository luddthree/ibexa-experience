<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<\Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupUpdateStep>
 */
final class CustomerGroupUpdateStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        assert($object instanceof CustomerGroupUpdateStep);

        $criteria = $this->normalizer->normalize(
            $object->getCriterion(),
            $format,
            $context,
        );

        return [
            'criteria' => $criteria,
            'identifier' => $object->getIdentifier(),
            'names' => $object->getNames(),
            'descriptions' => $object->getDescriptions(),
            'global_price_rate' => $object->getGlobalPriceRate(),
        ];
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): CustomerGroupUpdateStep
    {
        Assert::isArray($data);
        $identifier = $data['identifier'] ?? null;
        $names = $data['names'] ?? [];
        $descriptions = $data['descriptions'] ?? [];

        $globalPriceRate = null;
        if (isset($data['global_price_rate'])) {
            $globalPriceRate = (string)($data['global_price_rate']);
            Assert::numeric($globalPriceRate);
        }

        Assert::keyExists($data, 'criteria');
        Assert::isArray($data['criteria']);
        $criteria = $this->denormalizer->denormalize(
            $data['criteria'],
            CriterionInterface::class,
            $format,
            $context,
        );

        return new CustomerGroupUpdateStep(
            $criteria,
            $identifier,
            $names,
            $descriptions,
            $globalPriceRate,
        );
    }

    public function getHandledClassType(): string
    {
        return CustomerGroupUpdateStep::class;
    }

    public function getType(): string
    {
        return 'customer_group';
    }

    public function getMode(): string
    {
        return 'update';
    }
}
