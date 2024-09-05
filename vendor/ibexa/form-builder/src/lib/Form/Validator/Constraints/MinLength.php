<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LengthValidator;

class MinLength extends Length
{
    /**
     * @param int $min
     * @param string|null $message
     */
    public function __construct(int $min, ?string $message = null)
    {
        $options = ['min' => $min];

        if ($message !== null) {
            $options['minMessage'] = $message;
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

class_alias(MinLength::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\MinLength');
