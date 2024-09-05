<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Required extends Constraint
{
    public const IS_REQUIRED_ERROR = '4e8669f4-9041-4961-b7fb-41c47af51747';

    /** @var bool */
    public $required = false;

    /** @var string */
    public $requiredMessage = 'This value is required.';

    /**
     * @param bool $required
     * @param string|null $message
     */
    public function __construct(bool $required, ?string $message = null)
    {
        $options = ['required' => $required];

        if ($message !== null) {
            $options['requiredMessage'] = $message;
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return RequiredValidator::class;
    }
}

class_alias(Required::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\Required');
