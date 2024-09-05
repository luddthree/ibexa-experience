<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Extensions extends Constraint
{
    public const EXTENSION_NOT_ALLOWED_ERROR = '2f78a95d-579d-4e90-8fe3-0606c36dbc88';

    /** @var array */
    public $extensions;

    public $extensionsMessage = 'Extension not allowed.';

    /**
     * @param string $extensions
     * @param string|null $message
     */
    public function __construct(string $extensions, ?string $message = null)
    {
        $options = [
            'extensions' => array_map(
                'trim',
                explode(
                    ',',
                    strtolower($extensions)
                )
            ),
        ];

        if ($message !== null) {
            $options['extensionsMessage'] = $message;
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return ExtensionsValidator::class;
    }
}

class_alias(Extensions::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\Extensions');
