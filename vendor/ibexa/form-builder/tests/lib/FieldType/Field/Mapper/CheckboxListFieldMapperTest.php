<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\FieldType\Field\Mapper\CheckboxListFieldMapper;
use Ibexa\FormBuilder\Form\Type\Field\CheckboxListFieldType;
use Symfony\Component\Validator\Constraints as Assert;

class CheckboxListFieldMapperTest extends AbstractFieldMapperTest
{
    private const FIELD_NAME = 'The checkbox list field';
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
            'options' => [],
            'help' => null,
        ]);

        $withAllAttributes = $this->createField(self::FIELD_NAME, [
            'options' => self::ATTR_OPTIONS_VALUE,
            'help' => self::ATTR_HELP_VALUE,
        ]);

        return [
            [
                $withDefaultAttributes,
                [],
                CheckboxListFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'choices' => [],
                    'help' => null,
                    'required' => false,
                    'constraints' => [],
                    'field' => $withDefaultAttributes,
                    'translation_domain' => false,
                ],
            ],
            [
                $withAllAttributes,
                [
                    new Assert\NotBlank(),
                ],
                CheckboxListFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'choices' => self::ATTR_OPTIONS_VALUE,
                    'help' => self::ATTR_HELP_VALUE,
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                    'field' => $withAllAttributes,
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
        return new CheckboxListFieldMapper('checkbox_list', CheckboxListFieldType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedSupportedField(): string
    {
        return 'checkbox_list';
    }
}

class_alias(CheckboxListFieldMapperTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\Mapper\CheckboxListFieldMapperTest');
