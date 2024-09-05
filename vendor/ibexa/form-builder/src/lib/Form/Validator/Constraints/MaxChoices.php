<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class MaxChoices extends Constraint
{
    public const MAX_CHOICES_EXCEEDED_ERROR = '6efa4fcb-19ff-47fd-8874-286800a54b88';

    /** @var bool */
    public $maxChoices = false;

    /** @var string */
    public $maxChoicesMessage = 'Maximum number of choices exceeded.';

    /**
     * @param int $maxChoices
     * @param string|null $message
     */
    public function __construct(int $maxChoices, ?string $message = null)
    {
        $options = ['maxChoices' => $maxChoices];

        if ($message !== null) {
            $options['maxChoicesMessage'] = $message;
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return MaxChoicesValidator::class;
    }
}

class_alias(MaxChoices::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\MaxChoices');
