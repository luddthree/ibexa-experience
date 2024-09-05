<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class MinChoices extends Constraint
{
    public const MIN_CHOICES_NOT_REACHED_ERROR = '944d856f-191f-4d67-9483-0e95998b7c09';

    /** @var bool */
    public $minChoices = false;

    /** @var string */
    public $minChoicesMessage = 'Minimum number of choices not reached.';

    /**
     * @param $minChoices
     * @param string|null $message
     */
    public function __construct(int $minChoices, ?string $message = null)
    {
        $options = ['minChoices' => $minChoices];

        if ($message !== null) {
            $options['minChoicesMessage'] = $message;
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return MinChoicesValidator::class;
    }
}

class_alias(MinChoices::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\MinChoices');
