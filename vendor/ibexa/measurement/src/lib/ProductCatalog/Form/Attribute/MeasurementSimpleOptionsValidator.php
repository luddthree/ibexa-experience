<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\Core\Options\OptionsBag;
use Ibexa\Contracts\Measurement\Value\Definition\Sign;
use Symfony\Component\Validator\Constraints as Assert;

final class MeasurementSimpleOptionsValidator extends AbstractMeasurementOptionsValidator
{
    protected function getConstraints(OptionsBag $options): array
    {
        return array_merge(parent::getConstraints($options), [
            '[defaultValue]' => new Assert\Optional([
                new Assert\Type('numeric'),
                new Assert\LessThanOrEqual(null, '[measurementRange][maximum]'),
                new Assert\GreaterThanOrEqual(null, '[measurementRange][minimum]'),
            ]),
            '[sign]' => new Assert\Required(new Assert\Choice(null, Sign::getAllowedValues())),
        ]);
    }
}
