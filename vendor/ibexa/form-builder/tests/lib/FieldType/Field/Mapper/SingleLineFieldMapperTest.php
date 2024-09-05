<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\FieldType\Field\Mapper\SingleLineFieldMapper;
use Ibexa\FormBuilder\Form\Type\Field\SingleLineFieldType;
use Symfony\Component\Validator\Constraints as Assert;

class SingleLineFieldMapperTest extends AbstractFieldMapperTest
{
    private const FIELD_NAME = 'The single line field';
    private const ATTR_HELP_VALUE = 'The help';
    private const ATTR_PLACEHOLDER_VALUE = 'The placeholder';
    private const ATTR_DEFAULT_VALUE_VALUE = 'data';

    /**
     * {@inheritdoc}
     */
    public function dataProviderForMapForm(): array
    {
        $withDefaultAttributes = $this->createField(self::FIELD_NAME, [
            'help' => null,
            'placeholder' => null,
            'default_value' => null,
        ]);

        $withAllAttributes = $this->createField(self::FIELD_NAME, [
            'help' => self::ATTR_HELP_VALUE,
            'placeholder' => self::ATTR_PLACEHOLDER_VALUE,
            'default_value' => self::ATTR_DEFAULT_VALUE_VALUE,
        ]);

        return [
            [
                $withDefaultAttributes,
                [],
                SingleLineFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'help' => null,
                    'data' => null,
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
                SingleLineFieldType::class,
                [
                    'label' => self::FIELD_NAME,
                    'help' => self::ATTR_HELP_VALUE,
                    'attr' => [
                        'placeholder' => self::ATTR_PLACEHOLDER_VALUE,
                    ],
                    'data' => self::ATTR_DEFAULT_VALUE_VALUE,
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
        return new SingleLineFieldMapper('single_line', SingleLineFieldType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedSupportedField(): string
    {
        return 'single_line';
    }
}

class_alias(SingleLineFieldMapperTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\Mapper\SingleLineFieldMapperTest');
