<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LengthValidator;

class MaxLength extends Length
{
    /**
     * @param int $max
     * @param string|null $message
     */
    public function __construct(int $max, ?string $message = null)
    {
        $options = ['max' => $max];

        if ($message !== null) {
            $options['maxMessage'] = $message;
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return LengthValidator::class;
    }
}

class_alias(MaxLength::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\MaxLength');
