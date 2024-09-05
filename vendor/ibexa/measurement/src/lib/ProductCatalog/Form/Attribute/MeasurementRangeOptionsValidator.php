<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\Core\Options\OptionsBag;
use Symfony\Component\Validator\Constraints as Assert;

final class MeasurementRangeOptionsValidator extends AbstractMeasurementOptionsValidator
{
    protected function getConstraints(OptionsBag $options): array
    {
        return array_merge(parent::getConstraints($options), [
            '[measurementDefaultRange][defaultRangeMinimumValue]' => new Assert\Optional([
                new Assert\Type('numeric'),
                new Assert\LessThanOrEqual(null, '[measurementDefaultRange][defaultRangeMaximumValue]'),
                new Assert\LessThanOrEqual(null, '[measurementRange][maximum]'),
                new Assert\GreaterThanOrEqual(null, '[measurementRange][minimum]'),
            ]),
            '[measurementDefaultRange][defaultRangeMaximumValue]' => new Assert\Optional([
                new Assert\Type('numeric'),
                new Assert\GreaterThanOrEqual(null, '[measurementDefaultRange][defaultRangeMinimumValue]'),
                new Assert\LessThanOrEqual(null, '[measurementRange][maximum]'),
                new Assert\GreaterThanOrEqual(null, '[measurementRange][minimum]'),
            ]),
        ]);
    }
}
