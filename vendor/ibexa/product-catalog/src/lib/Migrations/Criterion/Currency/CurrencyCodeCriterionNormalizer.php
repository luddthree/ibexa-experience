<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Criterion\Currency;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\CurrencyCodeCriterion;
use Ibexa\ProductCatalog\Migrations\Criterion\FieldValueCriterionNormalizer;
use Ibexa\ProductCatalog\Migrations\Currency\CurrencyDeleteStep;
use Ibexa\ProductCatalog\Migrations\Currency\CurrencyUpdateStep;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

final class CurrencyCodeCriterionNormalizer extends FieldValueCriterionNormalizer implements ContextAwareDenormalizerInterface
{
    protected function getHandledClass(): string
    {
        return CurrencyCodeCriterion::class;
    }

    protected function doDenormalize(array $data, string $type, ?string $format, array $context): CriterionInterface
    {
        return new CurrencyCodeCriterion($data['value'], $data['operator'] ?? null);
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        if (!parent::supportsDenormalization($data, $type, $format)) {
            return false;
        }

        if (($data['field'] ?? null) !== 'code') {
            return false;
        }

        $allowedSteps = [CurrencyDeleteStep::class, CurrencyUpdateStep::class];

        return isset($context[AbstractStepNormalizer::CONTEXT_STEP_CLASS_KEY])
            && in_array($context[AbstractStepNormalizer::CONTEXT_STEP_CLASS_KEY], $allowedSteps, true);
    }
}
