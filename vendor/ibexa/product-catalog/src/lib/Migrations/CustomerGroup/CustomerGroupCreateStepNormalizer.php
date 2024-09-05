<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<\Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupCreateStep>
 */
final class CustomerGroupCreateStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        assert($object instanceof CustomerGroupCreateStep);

        return [
            'identifier' => $object->getIdentifier(),
            'names' => $object->getNames(),
            'descriptions' => $object->getDescriptions(),
            'global_price_rate' => $object->getGlobalPriceRate(),
        ];
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): CustomerGroupCreateStep
    {
        Assert::keyExists($data, 'identifier');
        Assert::keyExists($data, 'names');
        Assert::isArray($data['names']);

        if (isset($data['descriptions'])) {
            Assert::isArray($data['descriptions']);
        }

        $globalPriceRate = (string)($data['global_price_rate'] ?? '0');
        /** @phpstan-var numeric-string $globalPriceRate */
        Assert::numeric($globalPriceRate);

        return new CustomerGroupCreateStep(
            $data['identifier'],
            $data['names'],
            $data['descriptions'] ?? [],
            $globalPriceRate,
        );
    }

    public function getHandledClassType(): string
    {
        return CustomerGroupCreateStep::class;
    }

    public function getType(): string
    {
        return 'customer_group';
    }

    public function getMode(): string
    {
        return 'create';
    }
}
