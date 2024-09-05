<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\FieldType\Field\Mapper\RadioFieldMapper;
use Ibexa\FormBuilder\Form\Type\Field\RadioFieldType;
use Symfony\Component\Validator\Constraints as Assert;

class RadioFieldMapperTest extends AbstractFieldMapperTest
{
    private const FIELD_NAME = 'The radio field';
    private const ATTR_HELP_VALUE = 'The help';
    private const ATTR_OPTIONS_VALUE = [
        'a' => 'a',
        'b' => 'b',
        'c' => 'c',
        'd' => 'd',
    ];

    /**
     * {@inheritdoc}
     */
    public function dataProviderForMapForm(): array
    {
        $withDefaultAttributes = $this->createField(self::FIELD_NAME, [
            'help' => null,
            'options' => [],
        ]);

        $withAllAttributes = $this->createField(self::FIELD_NAME, [
            'options' => self::ATTR_OPTIONS_VALUE,
            'help' => self::ATTR_HELP_VALUE,
        ]);

        return [
            [
                $withDefaultAttributes,
                [],
                RadioFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'help' => null,
                    'required' => true,
                    'constraints' => [],
                    'choices' => [],
                    'field' => $withDefaultAttributes,
                    'data' => null,
                    'translation_domain' => false,
                ],
            ],
            [
                $withAllAttributes,
                [
                    new Assert\NotBlank(),
                ],
                RadioFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'choices' => self::ATTR_OPTIONS_VALUE,
                    'help' => self::ATTR_HELP_VALUE,
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                    'field' => $withAllAttributes,
                    'data' => 'a',
                    'translation_domain' => false,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function createMapper(): FieldMapperInterface
    {
        return new RadioFieldMapper('radio', RadioFieldType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedSupportedField(): string
    {
        return 'radio';
    }
}

class_alias(RadioFieldMapperTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\Mapper\RadioFieldMapperTest');
