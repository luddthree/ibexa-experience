<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\Core\Options\OptionsBag;
use Ibexa\Contracts\Measurement\Value\Definition\Sign;
use Ibexa\Measurement\FieldType\MeasurementType;
use Symfony\Component\Validator\Constraints as Assert;

final class MeasurementOptionsValidator extends AbstractMeasurementOptionsValidator
{
    protected function getConstraints(OptionsBag $options): array
    {
        $constraints = array_merge(parent::getConstraints($options), [
            '[inputType]' => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Choice([
                    MeasurementType::INPUT_TYPE_SIMPLE_INPUT,
                    MeasurementType::INPUT_TYPE_RANGE,
                ]),
            ]),
        ]);

        if ($options->get('inputType') === MeasurementType::INPUT_TYPE_SIMPLE_INPUT) {
            $constraints['[measurementDefaultRange][defaultRangeMinimumValue]'] = new Assert\Optional();
            $constraints['[measurementDefaultRange][defaultRangeMaximumValue]'] = new Assert\Optional();
            $constraints['[sign]'] = new Assert\Required(new Assert\Choice(null, Sign::getAllowedValues()));
            $constraints['[defaultValue]'] = new Assert\Optional([
                new Assert\Type('numeric'),
                new Assert\LessThanOrEqual(null, '[measurementRange][maximum]'),
                new Assert\GreaterThanOrEqual(null, '[measurementRange][minimum]'),
            ]);
        } elseif ($options->get('inputType') === MeasurementType::INPUT_TYPE_RANGE) {
            $constraints['[sign]'] = new Assert\Optional();
            $constraints['[defaultValue]'] = new Assert\Optional();
            $constraints['[measurementDefaultRange][defaultRangeMinimumValue]'] = new Assert\Optional([
                new Assert\Type('numeric'),
                new Assert\LessThanOrEqual(null, '[measurementDefaultRange][defaultRangeMaximumValue]'),
                new Assert\LessThanOrEqual(null, '[measurementRange][maximum]'),
                new Assert\GreaterThanOrEqual(null, '[measurementRange][minimum]'),
            ]);
            $constraints['[measurementDefaultRange][defaultRangeMaximumValue]'] = new Assert\Optional([
                new Assert\Type('numeric'),
                new Assert\GreaterThanOrEqual(null, '[measurementDefaultRange][defaultRangeMinimumValue]'),
                new Assert\LessThanOrEqual(null, '[measurementRange][maximum]'),
                new Assert\GreaterThanOrEqual(null, '[measurementRange][minimum]'),
            ]);
        }

        return $constraints;
    }
}
