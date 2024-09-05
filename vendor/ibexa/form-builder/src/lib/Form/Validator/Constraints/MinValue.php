<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\RangeValidator;

class MinValue extends Range
{
    /**
     * @param float|null $min
     * @param string|null $message
     */
    public function __construct(?float $min, ?string $message = null)
    {
        $options = [
            'min' => $min,
        ];

        if ($message !== null) {
            $options['minMessage'] = $message;
        }

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return RangeValidator::class;
    }
}

class_alias(MinValue::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\MinValue');
