<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\Templating\Twig;

use Ibexa\FormBuilder\FieldType\Converter\FieldConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FieldExtension extends AbstractExtension
{
    /** @var \Ibexa\FormBuilder\FieldType\Converter\FieldConverter */
    protected $fieldConverter;

    /**
     * @param \Ibexa\FormBuilder\FieldType\Converter\FieldConverter $fieldConverter
     */
    public function __construct(FieldConverter $fieldConverter)
    {
        $this->fieldConverter = $fieldConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_field_encode',
                [$this->fieldConverter, 'encode'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_field_encode',
                ]
            ),
            new TwigFunction(
                'ibexa_field_encode',
                [$this->fieldConverter, 'encode']
            ),
        ];
    }
}

class_alias(FieldExtension::class, 'EzSystems\EzPlatformFormBuilderBundle\Templating\Twig\FieldExtension');
