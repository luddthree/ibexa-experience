<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Length;

class ExactLength extends Length
{
    /**
     * @param int $length
     * @param string|null $message
     */
    public function __construct(int $length, ?string $message = null)
    {
        $options = [
            'min' => $length,
            'max' => $length,
        ];

        if ($message !== null) {
            $options['exactMessage'] = $message;
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return Length::class;
    }
}

class_alias(ExactLength::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\ExactLength');
