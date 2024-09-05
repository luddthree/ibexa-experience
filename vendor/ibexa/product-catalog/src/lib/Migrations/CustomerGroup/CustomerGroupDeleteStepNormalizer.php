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
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<\Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupDeleteStep>
 */
final class CustomerGroupDeleteStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        assert($object instanceof CustomerGroupDeleteStep);

        return [
            'criteria' => $this->normalizer->normalize(
                $object->getCriterion(),
                $format,
                $context,
            ),
        ];
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): CustomerGroupDeleteStep
    {
        Assert::isArray($data);
        Assert::keyExists($data, 'criteria');

        Assert::isArray($data['criteria']);
        $criteria = $this->denormalizer->denormalize(
            $data['criteria'],
            CriterionInterface::class,
            $format,
            $context,
        );

        return new CustomerGroupDeleteStep($criteria);
    }

    public function getHandledClassType(): string
    {
        return CustomerGroupDeleteStep::class;
    }

    public function getType(): string
    {
        return 'customer_group';
    }

    public function getMode(): string
    {
        return 'delete';
    }
}
