<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Currency;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<\Ibexa\ProductCatalog\Migrations\Currency\CurrencyCreateStep>
 */
final class CurrencyCreateStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        assert($object instanceof CurrencyCreateStep);

        return [
            'code' => $object->getCode(),
            'subunits' => $object->getSubunits(),
            'enabled' => $object->isEnabled(),
        ];
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): CurrencyCreateStep
    {
        Assert::keyExists($data, 'code');
        Assert::stringNotEmpty($data['code']);
        Assert::keyExists($data, 'subunits');

        return new CurrencyCreateStep($data['code'], $data['subunits'], $data['enabled'] ?? true);
    }

    public function getHandledClassType(): string
    {
        return CurrencyCreateStep::class;
    }

    public function getType(): string
    {
        return 'currency';
    }

    public function getMode(): string
    {
        return 'create';
    }
}
