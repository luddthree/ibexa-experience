<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Regex as BaseRegex;
use Symfony\Component\Validator\Constraints\RegexValidator;

class Regex extends BaseRegex
{
    /** @var string */
    public $pattern;

    public $message = 'Value does not match the pattern.';

    /**
     * @param $pattern
     * @param string|null $message
     */
    public function __construct(string $pattern, ?string $message = null)
    {
        $options = ['pattern' => json_decode($pattern)->pattern];

        if ($message !== null) {
            $options['message'] = $message;
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return RegexValidator::class;
    }
}

class_alias(Regex::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\Regex');
